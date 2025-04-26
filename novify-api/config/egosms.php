<?php

return [
    'api_url' => env('EGO_SMS_API_URL', 'https://www.egosms.co/api/v1/json/'),
    'username' => env('EGO_SMS_USERNAME'),
    'password' => env('EGO_SMS_PASSWORD'),
    'sender_id' => env('EGO_SMS_SENDER_ID', 'PESIO'),
]; 