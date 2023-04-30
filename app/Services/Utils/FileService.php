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
        return $decryptedFileContents;
        // Return the decrypted file to the user as an attachment with the appropriate content type and filename
        /*return response()->make($decryptedFileContents, 200, [
            'Content-Type' => Storage::disk('local')->mimeType($path),
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"'
        ]);*/
        //return Storage::disk('local')->download($path);
    }

    public function upload($folderName, $fileName, $file, $key)
    {


        //convert base64 to image
        //split filedata from its metadata
        //$fileData = substr($file, strpos($file, ',') + 1);
        //extension name from metadata
        //$extension = explode(';',explode('/',explode(',', $file)[0])[1])[0];
        //image
        //$image= base64_decode($fileData);


        //generating filename
        //$fileName = $fileName . "." . $extension;
        //folder name
        $folderName = $this->basePath . $folderName;


        //encryption
        // Encrypt the file contents using the custom key
        $encryptedContents = Crypt::encrypt($file, $key);


        $result = Storage::disk('local')->put($folderName . '/' . $fileName, $encryptedContents);
        return $folderName . '/' . $fileName;
    }
}
