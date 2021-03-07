<?php

namespace App\Traits;

trait AlertMessageTraits{
    public function successLogin(){
        return "Login Success";
    }
    public function failedLogin(){
        return "Invalid Credentials/Login";
    }

    public function emptyFieldsAlert(){
       return "Please fill the required fields.";
    }
    public function saveSuccess(){
        return "Saved Successfully";
    }
    public function invalid($param){
        if(empty($param)){
            return "Invalid Data";
        }
        else{
            return "Invalid ".$param;
        }
    }
    public function notExist($param){
        if(empty($param)){
            return "Data does not exist.";
        }
        else{
            return $param. " does not exist.";
        }
    }
    public function alreadyExist($param){
        if(!empty($param)){
            return $param." already exists.";
        }
        else{
            return "Data already exists.";
        }
    }
    public function deleteSuccess($param){
        if($param){
            return $param." removed successfully.";
        }
        else{
            return "Data removed successfully.";
        }
    }
    public function deleteFail($param){
        if($param){
            return 'Error in removing '.$param;
        }
        else{
            return "Error in removing data";
        }
    }
    public function somethingWrong($param){
        if($param){
            return "Something wrong ".$param;
        }
        else{
            return "Something wrong when deleting the data.";
        }
    }
    public function invalidFileFormat(){
        return "File format not supported.";
    }
    public function errorFileUpload(){
        return "Error in uploading file.";
    }
    public function updateFailed(){
        return "Failed to update the data";
    }
    public function updateSuccess(){
        return "Successfully updated the data";
    }
    public function sentSuccess($param){
        if(empty($param)){
            return 'Data sent successfully.';
        }
        else{
            return $param.' sent successfully.';
        }
    }
    public function sentFail($param){
        if(empty($param)){
            return 'Error in sending data.';
        }
        else{
            return 'Error in sending '.$param;
        }
    }
    public function noRecordAvailable(){
        return 'No Record Available';
    }
}
