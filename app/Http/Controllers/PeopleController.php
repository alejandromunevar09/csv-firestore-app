<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeopleRequest;
use App\Services\FirestorePeople;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\Storage;

class PeopleController extends Controller
{
    public function __construct(private FirestorePeople $repo) {}

    // ===== CRUD =====
    public function index(Request $request)
    {
        $limit   = (int) $request->input('limit', 10);
        $afterId = $request->input('after'); // cursor

        $page = $this->repo->listPage($limit, $afterId);

        $people = [];
        foreach ($page['docs'] as $d) {
            if ($d->exists()) {
                $people[$d->id()] = $d->data();
            }
        }

        $next = $page['nextCursor']; // string|null

        return view('people.index', compact('people', 'next', 'limit'));
    }

    public function create()
    {
        return view('people.create', ['data' => null]);
    }

    public function store(PeopleRequest $request)
    {
        $data = $this->withSearchIndex($request->validated());
        $id = (string)($data['identification'] ?? '');
        if ($id) $this->repo->upsert($id, $data);
        else     $id = $this->repo->create($data);

        return redirect()->route('people.edit', $id)->with('status', 'Registro creado.');
    }

    public function destroy(string $id)
    {
        $this->repo->delete($id);
        return redirect()->route('people.index')->with('status', 'Registro eliminado.');
    }

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

        $people = [];
        foreach ($docs as $d) {
            if ($d->exists()) {
                $people[$d->id()] = $d->data();
            }
        }

        return view('people.index', compact('people'))->with('search_applied', true);
    }


    // ===== IMPORTACIÓN CSV =====
    public function importForm()
    {
        return view('people.import');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimetypes:text/plain,text/csv,text/tsv', 'max:20480'],
            'delimiter' => ['nullable', 'in:comma,semicolon,tab'],
        ]);

        // 1) Asegurar carpeta y guardar archivo
        Storage::makeDirectory('imports');
        $storedPath = $request->file('file')->store('imports');
        $fullPath   = Storage::path($storedPath);

        // 2) Configurar CSV
        try {
            $csv = Reader::createFromPath($fullPath, 'r');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo abrir el archivo: '.$e->getMessage());
        }

        $csv->setHeaderOffset(0);
        if ($request->string('delimiter') === 'semicolon') $csv->setDelimiter(';');
        if ($request->string('delimiter') === 'tab')       $csv->setDelimiter("\t");

        // 3) Encabezados esperados
        $expected = [
            'identification','firstname','lastname','address','cellphone',
            'email','gender','birthday','sex','status'
        ];

        try {
            $headers = $csv->getHeader();
        } catch (CsvException $e) {
            return back()->with('error', 'Archivo CSV inválido: '.$e->getMessage());
        }

        // Validar encabezados mínimos
        $required = ['identification','firstname','lastname'];
        foreach ($required as $must) {
            if (!in_array($must, $headers, true)) {
                return back()->with('error', "Falta la columna obligatoria: {$must}");
            }
        }

        // 4) Procesar
        $records = (new Statement())->process($csv);

        $ok = 0;           // filas insertadas/actualizadas
        $skipped = 0;      // filas saltadas (vacías o sin ID)
        $errors = [];      // errores por línea

        $line = 1; // línea de datos (no cuenta encabezados). League\Csv no siempre da índice, lo llevamos nosotros.
        foreach ($records as $row) {
            try {
                // Mapear únicamente columnas esperadas
                $data = [];
                foreach ($expected as $col) {
                    $data[$col] = isset($row[$col]) && is_string($row[$col]) ? trim($row[$col]) : ($row[$col] ?? null);
                }

                // Validaciones mínimas por fila
                if (empty($data['identification']) || empty($data['firstname']) || empty($data['lastname'])) {
                    $skipped++;
                    $errors[] = "Línea {$line}: faltan campos obligatorios (identification/firstname/lastname).";
                    $line++;
                    continue;
                }

                // Normalizar fecha DD/MM/YYYY → YYYY-MM-DD
                if (!empty($data['birthday']) && preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $data['birthday'], $m)) {
                    $data['birthday'] = "{$m[3]}-{$m[2]}-{$m[1]}";
                }

                // Índice de búsqueda
                $data = $this->withSearchIndex($data);

                // Upsert por identificación como ID del documento
                $docId = (string)$data['identification'];
                $this->repo->upsert($docId, $data);

                $ok++;
            } catch (\Throwable $e) {
                $errors[] = "Línea {$line}: ".$e->getMessage();
            } finally {
                $line++;
            }
        }

        // 5) Resumen para la vista
        $summary = [
            'archivo'  => basename($fullPath),
            'totales'  => ['exitos' => $ok, 'omitidos' => $skipped, 'errores' => count($errors)],
            'errores'  => $errors, // lista (máx. muestra en la vista)
        ];

        return back()->with('import_summary', $summary)
                     ->with('success', "Importación completa: {$ok} ok, {$skipped} omitidos, ".count($errors)." con error.");
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

    public function edit(string $id)
    {
        $snapshot = $this->repo->get($id);
        if (!$snapshot->exists()) {
            return redirect()->route('people.index')->with('error', 'Registro no encontrado.');
        }

        $person = $snapshot->data();
        return view('people.edit', compact('id', 'person'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->only([
            'identification', 'firstname', 'lastname', 'address', 'cellphone', 'email',
            'gender', 'birthday', 'sex', 'status'
        ]);

        $this->repo->upsert($id, $data);

        return redirect()->route('people.index')->with('success', 'Registro actualizado correctamente.');
    }


}
