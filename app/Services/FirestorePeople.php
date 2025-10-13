<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Google\Cloud\Firestore\FirestoreClient;

class FirestorePeople
{
    public const COLLECTION = 'people';

    private FirestoreClient $db;

    public function __construct()
    {
        // Lee primero del config nuevo; si no, cae al .env
        $cred = config('firebase.projects.app.credentials')
            ?? env('FIREBASE_CREDENTIALS')
            ?? env('GOOGLE_APPLICATION_CREDENTIALS');

        if (!$cred || !is_string($cred)) {
            throw new \RuntimeException(
                'No se encontró la ruta del JSON de credenciales. ' .
                'Configura firebase.projects.app.credentials en config/firebase.php ' .
                'o la variable FIREBASE_CREDENTIALS/GOOGLE_APPLICATION_CREDENTIALS en .env.'
            );
        }

        if (!file_exists($cred)) {
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

    public function list(int $limit = 50)
    {
        return $this->col()->limit($limit)->documents();
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
