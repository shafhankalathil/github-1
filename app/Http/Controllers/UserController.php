<?php

namespace App\Http\Controllers;

use App\MediaGallery;
use App\Traits\FunctionalTraits;
use App\User;
use App\UserEntities;

use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Webpatser\Uuid\Uuid;


class UserController extends Controller
{
    use FunctionalTraits;
    use HasApiTokens;

    public $successStatus = 200;
    public $errorStatus   = 401;


    public function login(Request $request) {
        //dd($request);
        //dd(Hash::make('~if(v_pro)'));
        //echo Uuid::generate()->string;

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $token  =   Auth::user()->createToken('DealQ');

            $response   =   [
                'status'    =>  'success',
                'message'   =>  $this->successLogin(),
                'data'      =>  [
                    'userType'  =>  Auth::user()->role,
                    //'authToken' =>  $request->bearerToken()
                    'authToken' =>  $token
                ],
            ];

            return response()->json($response, $this->successStatus);

        }
        else {
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->failedLogin(),
            ];
            return response()->json($response, $this->errorStatus);
        }
    }

    //User
    public function listUser(){
        if(!$user = User::where('role','<>','admin')->get()){
            $response   =   [
                'status'    =>  'success',
                'message'   =>  $this->noRecordAvailable(),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        foreach($user as $item){
            $media  = [];
            $media = MediaGallery::where('uuid', $item['uuid'])
                ->whereIn('file_type', ['images', 'documents'])
                ->get();

            if($media){
                $item['media'] = $media;
            }
        }

        $response   =   [
            'status'    => 'success',
            'message'   => 'User List',
            'data'      => $user
        ];
        return response()->json($response, $this->successStatus);
    }
    public function createUser(Request $request){
        $input      = $request->all();
        //$password   = $this->randomStringGenerator(8);

        //Custom Validation Rules Traits
        $requestInputFields = ['name', 'email', 'phone', 'password'];
        $alertValues        = ['Name', 'Email', 'Phone', 'Password'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        //Checking Unique Columns
        $fieldNames     = ['email', 'phone'];
        $fieldValues    = [$input['email'], $input['phone']];
        $models         = 'App\User';
        if($this->checkRecordExist('App\User', $fieldNames, $fieldValues)['status'] == 'error'){
            return response()->json($this->checkRecordExist('App\User', $fieldNames, $fieldValues), $this->errorStatus);
        }

        if(!$data = User::create([
            'name'      => $input['name'],
            'email'     => $input['email'],
            'password'  => Hash::make($request['password']),
            'phone'     => $input['phone'],
            'role'      => 'admin',
            'status'    => 1,
            'uuid'      => Uuid::generate()->string,
        ])){

            $response   =   [
                'status'    => 'error',
                'message'   => $this->somethingWrong('when creating User'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }


        //File upload
        $request['uuid'] = $data->uuid;

        if($request->hasFile('profileImage')){
            $result = $this->upload('profileImage', 'images', $request);
            if($result['status'] == 'error'){
                User::where('id', $data->id)->delete();
                MediaGallery::where('uuid', $data->uuid)->where('file_type', 'images')->delete();
                $response   =   [
                    'status'    =>  'error',
                    'message'   =>  $result['message'],
                    'data'      => []
                ];
                return $response;
            }
        }
        if($request->hasFile('documents')){
            $result = $this->upload('documents', 'documents', $request);
            if($result['status'] == 'error'){
                User::where('id', $data->id)->delete();
                MediaGallery::where('uuid', $data->uuid)->where('file_type', 'documents')->delete();
                $response   =   [
                    'status'    =>  'error',
                    'message'   =>  $result['message'],
                    'data'      => []
                ];
                return $response;
            }
        }

        //Email Sending
        //$data['password']   = $password;
        //$this->sendUserAccountMail($data);

        $userData   = User::where('uuid', $data->uuid)->first();
        $userData['media']  = [];
        $media      = MediaGallery::where('uuid', $data->uuid)
            ->whereIn('file_type', ['images', 'documents'])
            ->get();

        if($media){
            $userData['media'] = $media;
        }

        $response   =   [
            'status'    =>  'success',
            'message'   =>  $this->saveSuccess(),
            'data'      =>  $userData
        ];


        return response()->json($response, $this->successStatus);


    }
    public function updateUser(Request  $request){
        $input  = $request->all();


        //Custom Validation Rules Traits
        $requestInputFields = ['id','name', 'email', 'phone'];
        $alertValues        = ['id','Name', 'Email', 'Phone'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        if(!$user = User::find($input['id'])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->invalid('User'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        if($input['email']!=$user['email']){
            //Checking Unique Columns
            $fieldNames     = ['email'];
            $fieldValues    = [$input['email']];
            $models         = 'App\User';
            if($this->checkRecordExist('App\User', $fieldNames, $fieldValues)['status'] == 'error'){
                return response()->json($this->checkRecordExist('App\User', $fieldNames, $fieldValues), $this->errorStatus);
            }
        }

        //File upload
        $request['uuid'] = $user->uuid;
        if($request->hasFile('profileImage')){

            if(!$mediaGallery = MediaGallery::where('file_type', 'images')->where('uuid', $user['uuid'])->first()){
                $result = $this->upload('profileImage', 'images', $request);
            }
            else{
                unlink($mediaGallery['path'].$mediaGallery['filename']);
                if(MediaGallery::where('uuid', $user->uuid)->where('file_type', 'images')->delete()){
                    $result = $this->upload('profileImage', 'images', $request);
                }
            }

            if($result['status'] == 'error'){
                $response   =   [
                    'status'    =>  'error',
                    'message'   =>  $result['message'],
                    'data'      => []
                ];
                return $response;
            }
        }
        if($request->hasFile('documents')){
            if(!$mediaGallery = MediaGallery::where('file_type', 'documents')->where('uuid', $user['uuid'])->first()){
                $result = $this->upload('documents', 'documents', $request);
            }
            else{
                unlink($mediaGallery['path'].$mediaGallery['filename']);
                if(MediaGallery::where('uuid', $user->uuid)->where('file_type', 'documents')->delete()){
                    $result = $this->upload('documents', 'documents', $request);
                }
            }


            if($result['status'] == 'error'){
                $response   =   [
                    'status'    =>  'error',
                    'message'   =>  $result['message'],
                    'data'      => []
                ];
                return $response;
            }
        }

        $dataArray  =   [
            'name' => $input['name'],
            'email'=>$input['email'],
            'phone'=>$input['phone'],
            'address'=>$input['address']
        ];

        if(!$user->update($dataArray)){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->updateFailed(),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }


        $userData   = User::where('id', $input['id'])->first();
        $userData['media']  = [];
        $media      = MediaGallery::where('uuid', $userData->uuid)
            ->whereIn('file_type', ['images', 'documents'])
            ->get();

        if($media){
            $userData['media'] = $media;
        }

        $response   =   [
            'status'    =>  'success',
            'message'   =>  $this->saveSuccess(),
            'data'      => $userData
        ];

        return response()->json($response, $this->successStatus);

    }
    public function deleteUser(Request $request){
        $input  = $request->all();

        //Custom Validation Rules Traits
        $requestInputFields = ['id'];
        $alertValues        = ['id'];

        if($this->notSetRule($input, $requestInputFields, $alertValues )['status'] == 'error'){
            return response()->json($this->notSetRule($input, $requestInputFields, $alertValues ), $this->errorStatus);
        }
        if($this->emptyRules($input, $requestInputFields, $alertValues)['status'] == 'error'){
            return response()->json($this->emptyRules($input, $requestInputFields, $alertValues), $this->errorStatus);
        }

        if(!$user = User::find($input['id'])){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->invalid('User'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        //Delete Media
        $mediaGallery   = MediaGallery::where('uuid', $user->uuid)->whereIn('file_type',['images','documents'])->delete();



        //User Delete
        if(!User::where('id', $input['id'])->delete()){
            $response   =   [
                'status'    =>  'error',
                'message'   =>  $this->deleteFail('User'),
                'data'      => []
            ];
            return response()->json($response, $this->errorStatus);
        }

        $response   =   [
            'status'    =>  'success',
            'message'   =>  $this->deleteSuccess('User'),
            'data'      => []
        ];
        return response()->json($response, $this->successStatus);
    }

}
