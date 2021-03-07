<?php

namespace App\Traits;

use App\User;
use App\Services;

trait FunctionalTraits{
    use AlertMessageTraits;
    use CustomValidationRulesTraits;
    use FileUploadTraits;
    use EmailTraits;
    use UtilityTraits;
    use ProductsTraits;

    public function checkRecordExist($models, $fieldNames, $fieldValues){
        if(!empty($models) && !empty($fieldNames) && !empty($fieldValues)){
            foreach($fieldNames as $index=> $fieldName){
                if($models::where($fieldName, $fieldValues[$index])->count()){
                    $response   =   [
                        'status'    =>  'error',
                        'message'   =>  $this->alreadyExist($fieldValues[$index]),
                        'data'      => []
                    ];
                    return $response;
                }
            }
        }

    }

    public function getDataByPrimaryKey($model, $value){
        $data   = $model::find($value);
        return $data;

    }

}
