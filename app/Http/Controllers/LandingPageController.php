<?php

namespace App\Http\Controllers;

use App\Models\CMSLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    public function _all()
    {
        return CMSLanding::all();
    }

    public function index()
    {
        return CMSLanding::where('is_disabled', 0)->get();
    }

    public function store(Request $req)
    {
        $formData = $req->all();

        $parsedFormData = [];

        foreach ($formData as $key => $value) {

            if (is_string($key)
                && preg_match('/^(\d+)_(.*)/', $key, $matches)) {

                $index = $matches[1];
                $field = $matches[2];
                $parsedFormData[$index][$field] = $value;

            }

        }

        foreach ($parsedFormData as $index => $data) {

            $this->validateArr($data, [
                "type" => 'required|string',
                "value" => 'array',
                "option" => 'string',
                "is_disabled" => 'string',
            ]);

            $existingRecord = CMSLanding::where('type', $data['type'])
                ->where('option', $data['option'] ?? null)
                ->first();

            $img = isset($data['value']['img']) && $data['value']['img'] !== '{}' ? 
                $data['value']['img'] :
                (object)[];

            $img_pth = null;

            if (get_class($img) === 'Illuminate\Http\UploadedFile') {

                $this->validateArr($data, [
                    "value.img" => 'image|mimes:png,jpeg,jpg|max:2048'
                ]);
                $img_pth = $img->storeAs('public', 
                    uniqid() . '_' . $img->getClientOriginalName()
                );

            } elseif ($existingRecord) {
                $record = json_decode($existingRecord['value'], true);
                $img_pth = isset($record['imgurl']) ?
                    $this->trimStoragePrefix($record['imgurl']) :
                    '';
            }

            $data['value']['imgurl'] = Storage::url($img_pth);

            if (!array_key_exists('is_disabled', $data)) {
                $data['is_disabled'] = 0;
            } else {
                $data['is_disabled'] = json_decode($data['is_disabled'], true);
            }
            
            $data['value'] = json_encode($data['value']);

            if ($existingRecord) {
                $existingRecord->update($data);
            } else {
                CMSLanding::create($data);
            }
        }

        return response()->noContent();
    }

    private function validateArr($data, $rule)
    {
        $validator = Validator::make($data, $rule);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: '
                . $validator->errors()->first());
        }
    }

    private function trimStoragePrefix(string $path): string
    {
        if (strpos($path, '/storage/') === 0) {
            return substr_replace($path, '', 0, strlen('/storage/'));
        }

        return $path;
    }


}
