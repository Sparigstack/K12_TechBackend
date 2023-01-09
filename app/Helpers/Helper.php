<?php
namespace App\Helpers;
use App\Models\ErrorLog;


class Helper{
   public static function Parseerror($errorfrom,$errormsg,$uid,$priority){
        $error = new ErrorLog();
        $error->user_id = $uid;
        $error->error_from = $errorfrom;
        $error->error_msg = $errormsg;
        $error->priority = $priority;
        $error->created_date = now()->format('Y-m-d');
        $error->save();
        
    }
}