<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\School;



class LoginController extends Controller
{
    function Register(Request $request) {
        $user = new User;
        $userName = $request->input('name');// no use
        $userEmail = $request->input('email');
        $userGoogleId = $request->input('googleId');
        $userMicrosoftId = $request->input('microsoftId');
        $userAccessToken = $request->input('accessToken'); 
        $flag = $request->input('flag');
        $usersavedemail = User::where('email', $request->input('email'))->first();
        if (isset($usersavedemail)) {          
           if($usersavedemail->status == 'Approve'){
               
                if ($flag == 1) {
                    $updatedLoginDetail = User::where('email', $usersavedemail->email)->update(['remember_token' => $userAccessToken, 'google_id' => $userGoogleId]);
                } else {
                    $updatedLoginDetail = User::where('email',$usersavedemail->email)->update(['remember_token' => $userAccessToken, 'microsoft_id' => $userMicrosoftId]);
                }
                            return Response::json(array(
                            'status' => "success",                            
                ));
                }else{
              
                             return Response::json(array(
                            'status' => 'Users Domain name is not valid'                           
                ));
           }
        }else{
                             return Response::json(array(
                            'status' => 'SignUpFirst',                            
                ));
        
        }

    }

    function loginValidation(Request $request) {
        $useremail = $request->input('email');
        $user = User::where('email', $useremail)->first();

        if (isset($user)){
            $email = $user->email;
            if ($email == $useremail) {
                $userAccessToken = $request->input('accessToken');
                $updatedLoginDetail = User::where('email', $useremail)->update(['remember_token' => $userAccessToken]);
                return Response::json(array(
                            'status' => "success",
                            'msg' => $user
                ));
            }
        }else{
            return Response::json(array(
                        'status' => "Error",
            ));
        }
    }
    
    function addUsers(Request $request){
        $firstname = $request->input('FirstName');
        $lastname = $request->input('lastname');
        $email = $request->input('email');
        $requstedEmailDomain  = substr(strrchr($email, "@"), 1);
        $schoolalldata = School::all();
        $status = 'false';
        $schoolID = '';
       foreach($schoolalldata as $data){
          $dataEmailDomain =  substr(strrchr($data['Email'], "@"), 1); 
          if($requstedEmailDomain == $dataEmailDomain){
              $school = School::where('Email',$data['Email'])->first();
              $schoolID = $school->ID;
              $status = 'true';            
          }
       }
       if($status == 'true'){
        $user = new User;
        $user->name = $firstname.''.$lastname;
        $user->email = $email;      
        $user->school_id = $schoolID;
        $user->status = 'Approve';
        $user->save();
        return Response::json(array(
                            'status' => "success",  
                            'response'=>'Approve'
                     ));
       }else{
           $user = new User;
        $user->name = $firstname.''.$lastname;
        $user->email = $email;             
        $user->status = 'Reject';
        $user->save();
        return Response::json(array(
                            'status' => "success", 
                             'response'=>'Reject'
                ));
       }
    }

}

