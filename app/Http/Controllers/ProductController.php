<?php

namespace App\Http\Controllers;

use App\ProductCategories;
use App\ProductDetails;
use App\ProductPrices;
use App\Products;
use App\Traits\FunctionalTraits;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class ProductController extends Controller
{

    use FunctionalTraits;

    public $successStatus = 200;
    public $errorStatus   = 401;

    public function createProduct(Request $request){
        $input          = $request->all();
        $returnData     = [];

        //Custom Validation Rules Traits
        $requestInputFields = ['name', 'sku', 'attribute_sets_id', 'quantity', 'price', 'offer_price', 'category'];
        $alertValues        = ['Product Name', 'Sku', 'Attribute Set', 'Quantity', 'Price', 'Offer Price', 'Category'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }


        $productData = $this->addToProducts($input);

        if ($productData['status'] == 'error'){
            if($this->alreadyExistRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
                return response()->json($this->alreadyExistRules($input, $requestInputFields, $alertValues), $this->errorStatus);
            }
        }

        if ($productData && isset($productData['data'])){
            $input['product_id']    = $productData['data']['id'];
            $input['sku']           = $productData['data']['sku'];
            $input['uuid']          = $productData['data']['uuid'];

            $productDetails     = $this->addToProductDetails($input);
            $productPrices      = $this->addToProductPrice($input);
            $productCategories  = $this->addToProductCategories($input);
            //$productMedia       = $this->addToMediaGalleries($input);

            $this->addToProductDetails($input);
            $this->addToProductPrice($input);
            $this->addToProductCategories($input);
        }

        $products   = $this->getProductById($input['product_id']);

        $response   =   [
            'status'    =>  'success',
            'message'   =>  $this->saveSuccess(),
            'data'      =>  $products
        ];
        return response()->json($response, $this->successStatus);
    }

    public function addToProducts($input){

        if (isset($input['name']) && isset($input['sku'])){
            $sku    = $input['sku'];
            $name   = $input['name'];
            if ($this->getProductBySku($sku)){
                $response   =   [
                    'status'    => 'error',
                    'message'   => $this->alreadyExist('Product'),
                ];
                return $response;
            }
            else{
                if (Products::create(['name'  => $name,
                    'sku'=>$sku,
                    'uuid'=>Uuid::generate()->string])
                ){
                    $response   =   [
                        'status'    => 'success',
                        'message'   => $this->saveSuccess(),
                        'data'      => $this->getProductBySku($sku)
                    ];
                    return $response;
                }
            }
        }
    }

    public function addToProductDetails($input){
        if ($input && isset($input['product_id'])){

            if ($this->getProductDetailsByProductId($input['product_id'])){
                $this->deleteProductDetailsByProductId($input['product_id']);
            }

            if (ProductDetails::create(
                [
                    'product_id'        => $input['product_id'],
                    'attribute_sets_id' => $input['attribute_sets_id'],
                    'description'       => $input['description']
                ]
            )){
                $response   =   [
                    'status'    => 'success',
                    'message'   => $this->saveSuccess(),
                    'data'      => $this->getProductDetailsByProductId($input['product_id'])
                ];
                return $response;
            }
        }
    }

    public function addToProductPrice($input){
        if ($input && isset($input['product_id'])){
            if ($this->getProductPriceByProductId($input['product_id'])){
                $this->deleteProductPriceByProductId($input['product_id']);
            }

            if (ProductPrices::create(
                [
                    'product_id'        => $input['product_id'],
                    'price'             => $input['price'],
                    'offer_price'       => $input['offer_price'],
                    'discount'          => $input['discount']
                ]
            )){
                $response   =   [
                    'status'    => 'success',
                    'message'   => $this->saveSuccess(),
                    'data'      => $this->getProductPriceByProductId($input['product_id'])
                ];
                return $response;
            }

        }
    }

    public function addToProductCategories($input){
        if ($input && isset($input['product_id'])){
            if ($this -> getProductCategoryByProductId($input['product_id'])){
                $this -> deleteProductCategoryByProductId($input['product_id']);
            }

            if (isset($input['category'])){
                if (ProductCategories::create(
                    [
                        'product_id'        => $input['product_id'],
                        'categories'        => implode(',', $input['category']),
                    ]
                )){
                    $response   =   [
                        'status'    => 'success',
                        'message'   => $this->saveSuccess(),
                        'data'      => $this->getProductCategoryByProductId($input['product_id'])
                    ];
                    return $response;
                }
            }
        }
    }

    public function addToMediaGalleries($input){
        if ($input && isset($input['product_id'])){
            /*if ($this -> getProductImagesByProductId($input['product_id'])){
                $this -> deleteProductImagesByProductId($input['product_id']);
            }*/

        }
    }

    public function listProduct(Request $request){

        $response   =   [
            'status'    => 'success',
            'message'   => 'Product List',
        ];

        if ($request ){
            if (isset($request['product_id']) && !empty($request['product_id'])){
                $response['data']   = $this->getProductById($request['product_id']);
            }
            else{
                $response['data']   = $this->getProduct();
            }
        }

        return response()->json($response);
    }

    public function deleteProduct(Request $request){
        if ($request ){
            if (isset($request['product_id']) && !empty($request['product_id'])){
                $productId  = $request['product_id'];
                $response   =   [
                    'status'    => 'success',
                    'message'   => $this->deleteSuccess('Product'),
                ];

                $this->deleteProductById($productId);

            }
            else{
                $response   =   [
                    'status'    => 'error',
                    'message'   => $this->invalid('Product'),
                ];

            }
        }

        return response()->json($response);

    }

}
