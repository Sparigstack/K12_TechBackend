<?php

namespace App\Http\Controllers;
use Personal;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Contracts\Container\BindingResolutionException;
class PersonalController extends Controller
{

    public function showform(Request $request)
    {   
      
        return view('upload');
    }
    public function store(Request $request)
    {
         $result =$request->file('file');
        $file = fopen($result,'r');
        $header = fgetcsv($file);
        $escapedheader=[];       
        foreach($header as $key =>$value){
          
         $lheader = strtolower($value);           
         $escapedItem=preg_replace('/[^a-z]/','',$lheader);        
         array_push($escapedheader,$escapedItem);
        }  
        return $escapedheader;        
        

         
        }
    
    

}
