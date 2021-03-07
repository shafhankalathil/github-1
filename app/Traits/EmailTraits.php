<?php

namespace App\Traits;

use App\Mail\UserAccountCreationMail;
use App\User;
use Illuminate\Support\Facades\Mail;


trait EmailTraits{

    public function sendUserAccountMail($param){
        if(!Mail::to($param['email'])->send(new UserAccountCreationMail($param))){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->somethingWrong('when saving the media gallery.'),
            ];
            return $response;
        }


      //  \Mail::to($param['email'])->send(new \App\Mail\UserAccountCreationMail($param));
       // dd("Mail Sent");
    }
}
