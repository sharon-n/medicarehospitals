<?php

ini_set('memory_limit', '1G');
function getAccessToken($consumer_key, $consumer_secret) {
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        // Handle the case where curl_exec failed
        error_log("Failed to fetch access token from Safaricom API");
        return null;
    }

    $result = json_decode($response);
    
    if (isset($result->access_token)) {
        return $result->access_token;
    } else {
        // Log the entire response for debugging
        error_log("Unexpected response from Safaricom API: " . $response);
        return null;
    }
}

function lipaNaMpesaOnline($token, $shortcode, $passkey, $callback_url, $phone_number, $amount, $account_reference, $transaction_desc) {
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $data = [
        "BusinessShortCode" => $shortcode,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone_number,
        "PartyB" => $shortcode,
        "PhoneNumber" => $phone_number,
        "CallBackURL" => $callback_url,
        "AccountReference" => $account_reference,
        "TransactionDesc" => $transaction_desc
    ];
    

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true); // Return decoded response for better handling
}

function sendMpesaSTKPush($phone_number, $amount, $account_reference) {
    $consumer_key = getenv('wuBTFwNMLkjY0gbzOTZ3X4YKe68RihgyfQdA9YoETCHj7oBp');
    $consumer_secret = getenv('YsluAIbON4SDeChr5TIOXRybfVHm7MU0ovOOPfpouLyTeH6Gw6GiznxGlWCpVyPX');
    $shortcode = getenv('174379');
    $passkey = getenv('bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
    $callback_url = getenv('https://mydomain.com/path');

    
    if (!$consumer_key || !$consumer_secret || !$shortcode || !$passkey || !$callback_url) {
        error_log("Missing M-Pesa credentials. Please check your environment variables.");
        return null;
    }

    $token = getAccessToken($consumer_key, $consumer_secret);
    if ($token) {
        error_log("Access Token: " . $token);
    } else {
        error_log("Failed to retrieve Access Token");
    die("Failed to retrieve Access Token");

    $response = lipaNaMpesaOnline($token, $shortcode, $passkey, $callback_url, $phone_number, $amount, $account_reference, "Appointment booking");

}


    //return lipaNaMpesaOnline($token, $shortcode, $passkey, $callback_url, $phone_number, $amount, $account_reference, "Appointment booking");
}
?>
