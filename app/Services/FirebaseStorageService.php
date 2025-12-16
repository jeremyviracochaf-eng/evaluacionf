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
    
    $uploadStream = $file->get(); 
    
    // Si usas el método get() de Laravel:
    $object = $this->bucket->upload(
        $uploadStream, 
        [
            'name' => $path,
            'predefinedAcl' => 'publicRead', 
        ]
    );

    return sprintf(
        'https://storage.googleapis.com/%s/%s',
        env('FIREBASE_STORAGE_BUCKET'),
        $path
    );
   }
}
