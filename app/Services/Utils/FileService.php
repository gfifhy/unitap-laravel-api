<?php

namespace App\Services\Utils;

use App\Services\Utils\FileServiceInterface;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FileService implements FileServiceInterface
{
    private $basePath;
    public function  __construct() {
        $this->basePath = config('storage.base_path');
    }

    public function download($path, $encryptionKey)
    {
        $encryptedFileContents = Storage::disk('local')->get($path);

        // Decrypt the file contents
        $decryptedFileContents = Crypt::decrypt($encryptedFileContents, $encryptionKey);

        // Return the decrypted file to the user as an attachment with the appropriate content type and filename
        return response()->make($decryptedFileContents, 200, [
            'Content-Type' => Storage::disk('local')->mimeType($path),
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"'
        ]);
        //return Storage::disk('local')->download($path);
    }

    public function upload($folderName, $fileName, $file, $key)
    {


        $fileName = $fileName . "." . $file->getClientOriginalExtension();
        $folderName = $this->basePath . $folderName;


        //encryption
        $fileContents = file_get_contents($file->getRealPath());
        // Encrypt the file contents using the custom key
        $encryptedContents = Crypt::encrypt($fileContents, $key);


        $result = Storage::disk('local')->put($folderName . '/' . $fileName, $encryptedContents);
        return $folderName . '/' . $fileName;
    }
}
