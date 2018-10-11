<?php

namespace App\Utils\Email;

use App\Entities\ApiSetting;

class Email
{


    public static function sendEmail($to,$subject,$content){

        $apiSettings = ApiSetting::where('parent_id',1)->first();
        if(!empty($apiSettings) && !$apiSettings->is_email_send){
            return false;
        }

        try{

            $apiSettings = ApiSetting::where('parent_id',1)->first();

            $mail = new PHPMailer(); // create a new object


            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);

            $mail->Username = $apiSettings->reg_email;//"vel@nexdecade.com";
            $mail->Password = $apiSettings->reg_email_pass;//"2tbBjbsUVC0";
            $mail->SetFrom($apiSettings->reg_email,$apiSettings->email_from_template);
            $mail->Subject = $subject;
            $mail->AddAddress($to);  // Add a recipient
            $mail->Body = $content;

            if(!$mail->Send()){
                return false;
            }else{
                return true;
            }
            
        }catch(\Exception $ex){
            echo '<pre>';
            echo $ex->getMessage().',Line'.$ex->getLine().'<br/>';
            print_r($ex->getTrace());
        }

    }
}