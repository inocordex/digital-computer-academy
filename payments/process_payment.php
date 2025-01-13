<?php
include '../includes/config.php';

// Sample M-Pesa Integration for STK Push
function initiateMpesaPayment($phone, $amount, $registration_number) {
    $consumerKey = 'YOUR_CONSUMER_KEY';
    $consumerSecret = 'YOUR_CONSUMER_SECRET';
    $paybillNumber = 'YOUR_PAYBILL_NUMBER';
    $passkey = 'YOUR_PASSKEY';

    $timestamp = date("YmdHis");
    $password = base64_encode($paybillNumber . $passkey . $timestamp);

    $accessTokenURL = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    $stkPushURL = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

    // Get access token
    $credentials = base64_encode("$consumerKey:$consumerSecret");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $accessTokenURL);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    $accessToken = json_decode($response)->access_token;

    // Initiate STK Push
    $data = [
        "BusinessShortCode" => $paybillNumber,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $paybillNumber,
        "PhoneNumber" => $phone,
        "CallBackURL" => "https://yourwebsite.com/payments/payment_callback.php",
        "AccountReference" => $registration_number,
        "TransactionDesc" => "Class Payment"
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $stkPushURL);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken", "Content-Type: application/json"]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}
?>
