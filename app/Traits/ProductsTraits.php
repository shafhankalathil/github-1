<?php

namespace App\Traits;


use App\MediaGalleries;
use App\ProductCategories;
use App\ProductDetails;
use App\ProductPrices;
use App\Products;
use Illuminate\Support\Str;
use PHPUnit\Framework\Exception;

trait ProductsTraits{
    public function getProductBySku($sku){
        try{
            if ($sku){
               return Products::where('sku', $sku)->first();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function getProductById($productId){
        try{
            if ($productId){
                $data = Products::where('products.id', $productId)
                    ->join('product_details as details', 'details.product_id','=', 'products.id')
                    ->join('product_prices as prices', 'prices.product_id', '=', 'products.id')
                    ->join('product_categories as c', 'c.product_id', '=', 'products.id')
                    ->select('products.*', 'prices.price', 'prices.offer_price', 'prices.discount',
                        'details.attribute_sets_id', 'details.description')
                    ->first();

                return $data;
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function getProduct(){
        try{
            $data = Products::join('product_details as details', 'details.product_id','=', 'products.id')
                ->join('product_prices as prices', 'prices.product_id', '=', 'products.id')
                ->join('product_categories as c', 'c.product_id', '=', 'products.id')
                ->select('products.*', 'prices.price', 'prices.offer_price', 'prices.discount',
                    'details.attribute_sets_id', 'details.description')
                ->get();

            return $data;
        }
        catch (Exception $e){}
    }

    public function deleteProductById($productId){
        try{
            $this->deleteProductCategoryByProductId($productId);
            $this->deleteProductPriceByProductId($productId);
            $this->deleteProductDetailsByProductId($productId);
            Products::find($productId)->delete();
        }
        catch (Exception $e){}
    }

    public function getProductDetailsByProductId($productId){
        try{
            if ($productId){
                return ProductDetails::where('product_id', $productId)->first();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function getProductPriceByProductId($productId){
        try{
            if ($productId){
                return ProductPrices::where('product_id', $productId)->first();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function getProductCategoryByProductId($productId){
        try{
            if ($productId){
                return ProductCategories::where('product_id', $productId)->first();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function getProductImagesByProductId($productId){
        try{
            if ($productId){
                $productData = $this->getProductById($productId);
                $uuid        = $productData['uuid'];
                return MediaGalleries::where('uuid', $uuid)->get();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function deleteProductDetailsByProductId($productId){
        try{
            if ($productId){
               ProductDetails::where('product_id', $productId)->delete();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function deleteProductPriceByProductId($productId){
        try{
            if ($productId){
                ProductPrices::where('product_id', $productId)->delete();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function deleteProductCategoryByProductId($productId){
        try{
            if ($productId){
                ProductCategories::where('product_id', $productId)->delete();
            }

            return '';
        }
        catch (Exception $e){}
    }

    public function deleteProductImagesByProductId($productId){
        try{
            if ($productId){
                $productData = $this->getProductById($productId);
                $uuid        = $productData['uuid'];
                MediaGalleries::where('uuid', $uuid)->delete();
            }

            return '';
        }
        catch (Exception $e){}
    }


}
