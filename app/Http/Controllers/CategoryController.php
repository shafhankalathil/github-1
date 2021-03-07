<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Traits\FunctionalTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use FunctionalTraits;

    public $successStatus = 200;
    public $errorStatus   = 401;
    public $category      = [];

    //Categories add, list, delete
    public function createCategory(Request $request){
        $input          = $request->all();
        $parentId       = isset($input['parentId'])?$input['parentId']:0;

        //Custom Validation Rules Traits
        $requestInputFields = ['name'];
        $alertValues        = ['Category Name'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        if(Categories::create([
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
        $requestInputFields = ['categoryId'];
        $alertValues        = ['Category'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        $categories = Categories::with('children')
            //->where('parent_id',0)
            ->where('id', $input['categoryId'])
            ->get();

        if(empty($categories->count())){
            $response   =   [
                'status'    =>  'success',
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

        //dd(Auth::user());

        //Custom Validation Rules Traits
        $requestInputFields = ['categoryId'];
        $alertValues        = ['Category'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        $mainCategories = Categories::with('children')
            //where('parent_id',0)
            ->where('id',$categoryId)
            ->get();


        if(!empty($input['categoryId'])){

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
                ->where('parent_id', $categoryId)
                ->delete();
            $parentCategory = Categories::find($categoryId)->delete();

            //Fetching main categories after delete
            $mainCategories = Categories::with('children')
                ->where('id',$categoryId)
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
            if(Categories::where('id', $categoryId)->delete()){
                //fetching main categories after delete
                $mainCategories = Categories::with('children')
                    ->where('parent_id',0)
                    ->where('id',$categoryId)
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
