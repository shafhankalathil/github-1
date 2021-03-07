<?php

namespace App\Http\Controllers;

use App\Attributes;
use App\Traits\FunctionalTraits;
use Illuminate\Http\Request;

class FinalFormController extends Controller
{
    use FunctionalTraits;

    public $successStatus = 200;
    public $errorStatus   = 401;

    public function createFinalForm(Request $request){
        $input  = $request->all();

        //Custom Validation Rules Traits
        $requestInputFields = ['serviceId', 'attributes'];
        $alertValues        = ['Service', 'Attributes'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        //Get Data using Primarykey
        $model  = 'App\Services';
        $value  = $input['serviceId'];
        if(!$this->getDataByPrimaryKey($model, $value)){
            $response   =   [
                'status'    => 'error',
                'message'   => $this->invalid("Service"),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        if(!$attribute = Attributes::create(
            ['services_id'=>$input['serviceId'], 'attributes'=>json_encode($input['attributes'])])
        ){
            $response   =   [
                'status'    => 'error',
                'message'   => $this->somethingWrong("when saving attributes"),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        $response   =   [
            'status'    => 'success',
            'message'   => $this->saveSuccess(),
            'data'      => json_decode($attribute)
        ];
        return response()->json($response, $this->successStatus);
    }
    public function listFinalForm(Request $request){
        $input  = $request->all();

        //Custom Validation Rules Traits
        $requestInputFields = ['serviceId'];
        $alertValues        = ['Service'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        //Get Data using Primarykey
        $model  = 'App\Services';
        $value  = $input['serviceId'];
        if(!$this->getDataByPrimaryKey($model, $value)){
            $response   =   [
                'status'    => 'error',
                'message'   => $this->invalid("Service"),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        if(!$attribute = Attributes::where('services_id', $input['serviceId'])->get()){
            $response   =   [
                'status'    => 'success',
                'message'   => $this->notExist("Attribute"),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        $response   =   [
            'status'    => 'success',
            'message'   => 'Attribute List',
            'data'      => json_decode($attribute)
        ];
        return response()->json($response, $this->successStatus);
    }
}
