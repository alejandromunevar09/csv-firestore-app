<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\FieldPath;

class FirestorePeople
{
    public const COLLECTION = 'people';

    private FirestoreClient $db;

    public function __construct()
    {
        $cred = config('firebase.projects.app.credentials')
            ?? env('FIREBASE_CREDENTIALS')
            ?? env('GOOGLE_APPLICATION_CREDENTIALS');

        if (!$cred || !file_exists($cred)) {
            throw new \RuntimeException("El archivo de credenciales no existe: {$cred}");
        }

        $this->db = (new Factory)
            ->withServiceAccount($cred)
            ->createFirestore()
            ->database();
    }

    private function col()
    {
        return $this->db->collection(self::COLLECTION);
    }

    /** Lista “a pelo” (sin paginación) */
    public function list(int $limit = 50)
    {
        return $this->col()->limit($limit)->documents();
    }

    /**
     * Página con cursor (orden por ID de documento).
     * @return array{docs:\Google\Cloud\Firestore\DocumentSnapshot[], nextCursor:?string}
     */
    public function listPage(int $limit = 10, ?string $afterId = null): array
    {
        // Ordenamos por ID de documento (seguro aunque falten campos)
        $query = $this->col()->orderBy(FieldPath::documentId())->limit($limit);

        if ($afterId) {
            // Obtenemos el snapshot del cursor y arrancamos después
            $snap = $this->col()->document($afterId)->snapshot();
            if ($snap->exists()) {
                $query = $query->startAfter($snap);
            }
        }

        $docs = iterator_to_array($query->documents());
        $next = null;

        if (count($docs) === $limit) {
            $last = end($docs);
            $next = $last->id(); // usamos el último ID como cursor
        }

        return ['docs' => $docs, 'nextCursor' => $next];
    }
    

    public function get(string $id)
    {
        return $this->col()->document($id)->snapshot();
    }

    public function create(array $data): string
    {
        $doc = $this->col()->newDocument();
        $doc->set($data);
        return $doc->id();
    }

    public function upsert(string $id, array $data): void
    {
        $this->col()->document($id)->set($data, ['merge' => true]);
    }

    public function delete(string $id): void
    {
        $this->col()->document($id)->delete();
    }

    public function whereEquals(string $field, $value, int $limit = 50)
    {
        return $this->col()->where($field, '=', $value)->limit($limit)->documents();
    }

    public function whereArrayContains(string $field, string $token, int $limit = 50)
    {
        return $this->col()->where($field, 'array-contains', $token)->limit($limit)->documents();
    }
}
