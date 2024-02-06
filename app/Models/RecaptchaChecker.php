<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecaptchaChecker extends Model
{
    use HasFactory;

    public static function checkRecaptchaVaidity($clientResponse){

        $curl = curl_init();

        $siteSecret = env("CAPTCHA_SECRET");

        $requestUrl = 'https://www.google.com/recaptcha/api/siteverify?secret='.$siteSecret.'&response='.$clientResponse;

        $postData = array(
            'secret' => $siteSecret,
            'response' => $clientResponse
        );

        $data_string = json_encode($postData);

        curl_setopt_array($curl, array(
        CURLOPT_URL => $requestUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: 0',                                                                    
        
        )));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response,true);

        return $response;
    }
}
