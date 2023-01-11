<?php
namespace App\Helpers;


class Utility {
 static function sendEmail_New($toEmail, $sub, $body) {

        require_once 'lib/sendgrid-php/sendgrid-php.php';

//        $Fromsenderid = "hello@gocoworq.com";
        $Fromsenderid = "team.sprigstack@gmail.com";

        $receiverid = $toEmail;

        $subject = $sub;

        $message = $body;



        $email = new \SendGrid\Mail\Mail();

        $email->setFrom($Fromsenderid); // from email ID

        $email->setSubject($subject);

        $email->addTo($receiverid); // to email ID

        $email->addBcc('team.gocoworq@gmail.com');

        $email->addContent(
                "text/html", $message
        );
    
        
      
        $sendgrid = new \SendGrid(env('SENDGRID_KEY'));
        try {

            $response = $sendgrid->send($email);
          
            return 1;
        } catch (Exception $e) {

            echo 'Unable to send mail: ' . $e->getMessage() . "\n";

            return 0;
        }

        return 1;
    }


}