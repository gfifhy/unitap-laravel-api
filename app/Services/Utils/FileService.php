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
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        // Decrypt the file contents
        $decryptedFileContents = Crypt::decrypt($encryptedFileContents, $encryptionKey);
        $result = "data:image/".$ext.";base64,".base64_encode($decryptedFileContents);
        return $result;
    }

    public function upload($folderName, $fileName, $file, $key)
    {
        $folderName = $this->basePath . $folderName;
        //getting file contents
        $file = file_get_contents($file);
        //encryption
        //Encrypt the file contents using the custom key
        $encryptedContents = Crypt::encrypt($file, $key);
        $result = Storage::disk('local')->put($folderName . '/' . $fileName, $encryptedContents);
        return $folderName . '/' . $fileName;
    }
}
