<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Services;
use App\Traits\FunctionalTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    use FunctionalTraits;

    public $successStatus = 200;
    public $errorStatus   = 401;
    public $category      = [];



    //Services add, list, delete
    public function createService(Request $request){
        $input          = $request->all();
        $parentId       = isset($request['parentId'])?$request['parentId']:0;

        //Custom Validation Rules Traits
        $requestInputFields = ['service'];
        $alertValues        = ['Service'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        if(Services::create([
            'name'      =>  $input['service'],
            'parent_id' =>  $parentId
        ])){
            $response   =   [
                'status'    =>  'success',
                'message'   =>  $this->saveSuccess(),
                'data'      =>  Services::all()
            ];
            return response()->json($response, $this->successStatus);
        }
    }
    public function dashboard(Request $request){
        $response   =   [
            'status'    =>  'success',
            'message'   =>  'Dashboard',
            'data'      =>  Services::all()
        ];
        return response()->json($response, $this->successStatus);
    }
    public function deleteService(Request $request){
        $input      = $request->all();

        //Custom Validation Rules Traits
        $requestInputFields = ['serviceId'];
        $alertValues        = ['Service'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        if(!$services = Services::find($input['serviceId'])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->notExist('Service'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        if(!empty($input['serviceId'])){
            $categories = Categories::where('services_id', $input['serviceId'])->count();
            if($categories > 0){
                $categories = Categories::with('children')
                    ->where('services_id', $input['serviceId'])
                    ->delete();
            }


            $service = $services->delete();

            if($service){
                $response   =   [
                    'status'    =>  'success',
                    'message'   =>  $this->deleteSuccess('Service'),
                    'data'      => Services::all()
                ];
                return response()->json($response, $this->successStatus);
            }

        }

        $service = Services::all();
        if(empty($service->count())){
            $response   =   [
                'status'    =>  'sucess',
                'message'   => $this->notExist('Category'),
                'data'      => $service
            ];
            return response()->json($response, $this->successStatus);

        }

        $response   =   [
            'status'    =>  'error',
            'message'   =>  $this->somethingWrong('when deleting service'),
            'data'      => $service
        ];
        return response()->json($response, $this->errorStatus);
    }

    //Categories add, list, delete
    public function createCategory(Request $request){
        $input          = $request->all();
        $parentId       = isset($input['parentId'])?$input['parentId']:0;

        //Custom Validation Rules Traits
        //$requestInputFields = ['serviceId', 'name'];
        $requestInputFields = ['name'];
        //$alertValues        = ['Service', 'Category Name'];
        $alertValues        = ['Category Name'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        /*if(!$service    = Services::find($input['serviceId'])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->notExist("Service"),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }*/


        if(Categories::create([
            //'services_id'    =>  $input['serviceId'], 'category_name'=>$input['name'], 'parent_id'=>$parentId
             'category_name'=>$input['name'], 'parent_id'=>$parentId
        ]))
        {
            $categories = Categories::with('children')
                ->where('parent_id',0)
                //->where('services_id', $input['serviceId'])
                ->get();

            $response   =   [
                'status'    =>  'success',
                'message'   =>  $this->saveSuccess(),
                'data'      =>  $categories
            ];
            return response()->json($response, $this->successStatus);
        }
    }
    public function listCategory(Request $request){
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

        if(!$services   = Services::find($input['serviceId'])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->notExist('Service'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        $categories = Categories::with('children')
            ->where('parent_id',0)
            ->where('services_id', $input['serviceId'])
            ->get();

        if(empty($categories->count())){
            $response   =   [
                'status'    =>  'sucess',
                'message'   => $this->notExist('Category'),
                'data'      => $categories
            ];
            return response()->json($response, $this->errorStatus);
        }

        $response   =   [
            'status'    => 'success',
            'message'   => 'Category list',
            'data'      => $categories
        ];

        return response()->json($response);
    }
    public function deleteCategory(Request $request){
        $input      = $request->all();
        $categoryId = isset($input['categoryId'])?$input['categoryId']:0;



        //Custom Validation Rules Traits
        $requestInputFields = ['serviceId'];
        $alertValues        = ['Service'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        if(!$services = Services::find($input['serviceId'])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->notExist('Service'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        $mainCategories = Categories::with('children')
            ->where('parent_id',0)
            ->where('services_id',$input['serviceId'])
            ->get();

        if(!empty($input['categoryId']) && !empty($input['serviceId'])){

            //category existing check
            if(!$categories = Categories::find($input['categoryId'])){
                $response   =   [
                    'status'    =>  'success',
                    'message'   =>  $this->notExist('Category'),
                    'data'      => $mainCategories
                ];
                return response()->json($response, $this->errorStatus);
            }

            //Deleteing
            $categories = Categories::with('children')
                ->where('parent_id', $input['categoryId'])
                ->delete();
            $parentCategory = Categories::find($input['categoryId'])->delete();

            //Fetching main categories after delete
            $mainCategories = Categories::with('children')
                ->where('parent_id',0)
                ->where('services_id',$input['serviceId'])
                ->get();

            if($categories || $parentCategory){
                $response   =   [
                    'status'    =>  'success',
                    'message'   =>  $this->deleteSuccess('Category'),
                    'data'      => $mainCategories
                ];
                return response()->json($response, $this->successStatus);
            }

        }
        else{
            if(Categories::where('services_id', $input['serviceId'])->delete()){
                //fetching main categories after delete
                $mainCategories = Categories::with('children')
                    ->where('parent_id',0)
                    ->where('services_id',$input['serviceId'])
                    ->get();

                $response   =   [
                    'status'    =>  'success',
                    'message'   =>  $this->deleteSuccess('Category'),
                    'data'      => $mainCategories
                ];
                return response()->json($response, $this->successStatus);
            }
        }


        if(empty($categories->count())){
            $response   =   [
                'status'    =>  'sucess',
                'message'   => $this->notExist('Category'),
                'data'      => $mainCategories
            ];
            return response()->json($response, $this->errorStatus);

        }


        $response   =   [
            'status'    => 'error',
            'message'   => $this->somethingWrong('when deleting category'),
            'data'      => $categories
        ];
        return response()->json($response, $this->errorStatus);



    }
}
