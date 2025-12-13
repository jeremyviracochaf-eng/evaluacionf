<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseStorageService
{
    protected $bucket;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDefaultStorageBucket(env('FIREBASE_STORAGE_BUCKET'));

        $this->bucket = $factory->createStorage()->getBucket();
    }

    public function upload($file, string $path): string
    {
        $object = $this->bucket->upload(
            fopen($file->getRealPath(), 'r'),
            ['name' => $path]
        );

        $object->update(['acl' => []]); // público

        return sprintf(
            'https://storage.googleapis.com/%s/%s',
            env('FIREBASE_STORAGE_BUCKET'),
            $path
        );
    }
}
