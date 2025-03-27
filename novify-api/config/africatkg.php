<?php

return
[
    "sms_url" => env('AFRICATKG_SMS_URL', 'https://api.africastalking.com/version1/messaging/bulk'),
    "sms_username" => env('AFRICATKG_SMS_USERNAME', 'sandbox'),
    "sms_password" => env('AFRICATKG_SMS_PASSWORD', 'sandbox'),
    "sms_sender" => env('AFRICATKG_SMS_SENDER', 'Novify'),
    "api_key" => env('AFRICATKG_API_KEY',"")

];