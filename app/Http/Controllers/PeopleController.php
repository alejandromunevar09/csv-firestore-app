<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeopleRequest;
use App\Services\FirestorePeople;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Statement;

class PeopleController extends Controller
{
    public function __construct(private FirestorePeople $repo) {}

    // ===== CRUD =====
    public function index()
    {
        $docs = $this->repo->list(100);
        $items = [];
        foreach ($docs as $d) $items[] = array_merge(['_id' => $d->id()], $d->data());
        return view('people.index', compact('items'));
    }

    public function create()
    {
        return view('people.form', ['data' => null]);
    }

    public function store(PeopleRequest $request)
    {
        $data = $this->withSearchIndex($request->validated());
        $id = (string)($data['identification'] ?? '');
        if ($id) $this->repo->upsert($id, $data);
        else     $id = $this->repo->create($data);

        return redirect()->route('people.edit', $id)->with('status', 'Registro creado.');
    }

    public function edit(string $id)
    {
        $snap = $this->repo->get($id);
        abort_unless($snap->exists(), 404);
        $data = array_merge(['_id' => $id], $snap->data());
        return view('people.form', compact('data'));
    }

    public function update(PeopleRequest $request, string $id)
    {
        $data = $this->withSearchIndex($request->validated());
        $this->repo->upsert($id, $data);
        return back()->with('status', 'Registro actualizado.');
    }

    public function destroy(string $id)
    {
        $this->repo->delete($id);
        return redirect()->route('people.index')->with('status', 'Registro eliminado.');
    }

    // ===== BÚSQUEDA =====
    public function search(Request $request)
    {
        $request->validate([
            'field' => 'required|string|in:identification,email,search_index',
            'q'     => 'required|string'
        ]);

        $field = $request->string('field');
        $q     = trim($request->string('q'));

        if ($field !== 'search_index') {
            $docs = $this->repo->whereEquals($field, $q, 100);
        } else {
            $token = mb_strtolower(preg_replace('/\s+/', '', $q), 'UTF-8');
            $docs  = $this->repo->whereArrayContains('search_index', $token, 100);
        }

        $items = [];
        foreach ($docs as $d) $items[] = array_merge(['_id' => $d->id()], $d->data());
        return view('people.index', compact('items'))->with('search_applied', true);
    }

    // ===== IMPORTACIÓN CSV =====
    public function importForm()
    {
        return view('people.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimetypes:text/plain,text/csv,text/tsv','max:20480'],
            'delimiter' => ['nullable','in:comma,semicolon,tab'],
        ]);

        $path = $request->file('file')->store('imports');
        $csv  = Reader::createFromPath(storage_path('app/'.$path), 'r');
        $csv->setHeaderOffset(0);
        if ($request->string('delimiter') === 'semicolon') $csv->setDelimiter(';');
        if ($request->string('delimiter') === 'tab')       $csv->setDelimiter("\t");

        $headers = $csv->getHeader();
        $records = (new Statement())->process($csv);

        $expected = [
            'identification','firstname','lastname','address','cellphone',
            'email','gender','birthday','sex','status'
        ];

        foreach (['identification','firstname','lastname'] as $must) {
            if (!in_array($must, $headers, true)) {
                return back()->withErrors(['file' => "Falta la columna obligatoria: {$must}"]);
            }
        }

        $count = 0;
        foreach ($records as $row) {
            $data = [];
            foreach ($expected as $col) {
                $data[$col] = isset($row[$col]) && is_string($row[$col]) ? trim($row[$col]) : ($row[$col] ?? null);
            }

            if (!empty($data['birthday']) && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $data['birthday'], $m)) {
                $data['birthday'] = "{$m[3]}-{$m[2]}-{$m[1]}";
            }

            $data  = $this->withSearchIndex($data);
            $docId = (string)($data['identification'] ?? '');
            if ($docId) $this->repo->upsert($docId, $data);
            else        $this->repo->create($data);

            $count++;
        }

        return back()->with('status', "Importación completa: {$count} registros cargados.");
    }

    private function withSearchIndex(array $data): array
    {
        $parts = [];
        foreach (['firstname','lastname','email','identification'] as $k) {
            if (!empty($data[$k]) && is_string($data[$k])) {
                $t = mb_strtolower($data[$k], 'UTF-8');
                $t = preg_replace('/[^a-z0-9áéíóúüñ\.@]+/u', '', $t);
                if ($t) $parts[] = $t;
            }
        }
        $data['search_index'] = array_values(array_unique($parts));
        return $data;
    }
}
