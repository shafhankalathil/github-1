<?php

namespace App\Traits;

use App\MediaGallery;
use App\MediaType;
use App\User;

trait FileUploadTraits{

    public function upload($field, $identifier, $request){
        $file  = $request->file($field);

        if(!$mediaSettings  = MediaType::where('identifier', $identifier)->first()){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->notExist("Media Type"),
            ];
            return $response;
        }

        $basePath           = $mediaSettings['base_path'];
        $allowedExtensions  = json_decode($mediaSettings['allowed_extensions']);
        $path               = public_path().$basePath."vendor/".$request['uuid']."/";

        if(!in_array($request->file($field)->extension(), $allowedExtensions)){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->invalidFileFormat(),
            ];
            return $response;
        }

        if(!$file->move($path, $file->getClientOriginalName())){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->errorFileUpload(),
            ];
            return $response;
        }

        if(!$mediaGallery = MediaGallery::create(
            ['media_type_id' => $mediaSettings['id'],
                'file_type' => $identifier,
                'filename' => $file->getClientOriginalName(),
                'path'     => $path,
                'uuid'     => $request['uuid']
            ])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->somethingWrong('when saving the media gallery.'),
            ];
            return $response;
        }

        $response   =   [
            'status'    =>  'success',
            'message'   =>  '',
        ];
        return $response;

    }



}
