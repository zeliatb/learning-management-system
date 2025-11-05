<?php
session_start();
require_once "config/db_config.php";

if (!isset($_POST['amount']) || !isset($_POST['email']) || !isset($_POST['user_id'])) {
    die("Invalid payment request.");
}

$amount = $_POST['amount'];
$email = $_POST['email'];
$user_id = $_POST['user_id'];

// Convert amount to kobo
$amount_kobo = $amount * 100;

// Paystack API Initialization
$url = "https://api.paystack.co/transaction/initialize";
$callback_url = "https://zeliatbraimah.eagletechafrica.com/paystack-callback.php";

$fields = [
  'email' => $email,
  'amount' => $amount_kobo,
  'callback_url' => $callback_url,
  'metadata' => [
      'cancel_action' => "https://zeliatbraimah.eagletechafrica.com/paystack-cancelled.php",
      'user_id' => $user_id
  ]
];

$fields_string = http_build_query($fields);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer sk_test_ed202aceb0e9f7300bb35c407e0b77047f466fad",
  "Cache-Control: no-cache"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$response = json_decode($result, true);

if ($response && $response['status'] === true) {
    header("Location: " . $response['data']['authorization_url']);
    exit;
} else {
    echo "Error initializing payment: " . ($response['message'] ?? "Unknown error");
}
?>