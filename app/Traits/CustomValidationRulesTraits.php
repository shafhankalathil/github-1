<?php

namespace App\Traits;

use App\User;
use App\Services;

trait CustomValidationRulesTraits{

    public function notSetRule($request, $requestInputFields, $alertValues){
        if(!empty($requestInputFields)){
            foreach($requestInputFields as $index=> $requestInputs){
                if(!isset($request[$requestInputs])){
                    $response   =   [
                        'status'    =>  'error',
                        'message'   =>  $this->invalid($alertValues[$index]),
                    ];
                    return $response;
                }
            }

        }
    }

    public function emptyRules($request, $requestInputFields, $alertValues){
        if(!empty($requestInputFields)){
            foreach($requestInputFields as $index=> $requestInputs){
                if(empty($request[$requestInputs])){
                    $response   =   [
                        'status'    =>  'error',
                        'message'   =>  $this->emptyFieldsAlert(),
                    ];
                    return $response;
                }
            }

        }
    }

    public function alreadyExistRules($request, $requestInputFields, $alertValues){
        if(!empty($requestInputFields)){
            foreach($requestInputFields as $index=> $requestInputs){
                if(empty($request[$requestInputs])){
                    $response   =   [
                        'status'    =>  'error',
                        'message'   =>  $this->alreadyExist($requestInputs),
                    ];
                    return $response;
                }
            }

        }
    }
}
