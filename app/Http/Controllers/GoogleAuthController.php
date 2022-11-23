<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
   public function addUser() {
        $google_user = Socialite::driver('google')->user();
        $user = User::where('google_id',$google_user->getId())->first();
 return  response()->json(["Result"=>"Data hase been saved"]);
 if(!$user){
      $new_user = User::create([
          'name'=> $google_user->getName(),
             'email'=> $google_user->getEmail(),
          'google_id'=> $google_user->getId()]);
            Auth::login($new_user);
      return true;
  }else{
      Auth::login($user);
       return true;
  }
}
    
//    public function redirect() {
// return Socialite::driver('google')->redirect();
//}
//  public function callbackGoogle() {
//try{
//     $google_user = Socialite::driver('google')->user();
//     $user = User::where('google_id',$google_user->getId())->first();
////     dd($user);
//  if(!$user){
//      $new_user = User::create([
//          'name'=> $google_user->getName(),
//             'email'=> $google_user->getEmail(),
//          'google_id'=> $google_user->getId()]);
//      
//      Auth::login($new_user);
//      return redirect()->intended('dashboard');
//  }else{
//      Auth::login($user);
//       return redirect()->intended('dashboard');
//  }
//} catch (\Throwable $th){
//    dd('Something went wrong!'.$th->getMessage());    
//}
//   
//  }
}
