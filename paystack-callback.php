<?php
session_start();
require_once "config/db_config.php"; // Database connection

if (!isset($_GET['reference'])) {
    die("Transaction reference missing.");
}

$reference = $_GET['reference'];

// Verify payment with Paystack
$verifyUrl = "https://api.paystack.co/transaction/verify/" . $reference;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer sk_test_ed202aceb0e9f7300bb35c407e0b77047f466fad",
    "Cache-Control: no-cache"
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// If payment was successful
if ($result && isset($result['data']['status']) && $result['data']['status'] === 'success') {

    $amountPaid = $result['data']['amount'] / 100; // Convert from kobo to naira
    $email = $result['data']['customer']['email'];
    $reference = $result['data']['reference'];
    $paymentMethod = "card";
    $remarks = "Payment verified successfully via Paystack.";
    $paymentDate = date("Y-m-d H:i:s");

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        die("User session expired. Please log in again.");
    }

    // Fetch user fee info
    $sql = "SELECT total_amount, amount_paid FROM user_fees WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($total_amount, $amount_paid);
    $stmt->fetch();
    $stmt->close();

    // Compute new totals
    $newAmountPaid = $amount_paid + $amountPaid;
    $newBalance = max(0, $total_amount - $newAmountPaid);

    // Update user_fees
    $update = $conn->prepare("UPDATE user_fees SET amount_paid = ?, balance = ?, last_payment_date = ? WHERE user_id = ?");
    $update->bind_param("ddsi", $newAmountPaid, $newBalance, $paymentDate, $user_id);
    $update->execute();
    $update->close();

    // Insert into payment_history
    $insert = $conn->prepare("
        INSERT INTO payment_history (user_id, payment_amount, payment_method, reference_number, remarks)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("idsss", $user_id, $amountPaid, $paymentMethod, $reference, $remarks);
    $insert->execute();
    $insert->close();

    // Redirect back with success message
    echo <<<HTML
    <style>
    body {
        background-color: #f3efe7;
    }
    .message-box {
        max-width: 500px;
        margin: 5rem auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 3rem 2.5rem;
        text-align: center;
    }
    h2  {
        font-size: 1.8rem;
        color: #222;
        margin-bottom: 0.8rem;
    }
    p {
        color: #666;
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    .btn-container {
        margin-top: 2rem;
    }
    .btn {
        display: inline-block;
        background: linear-gradient(90deg, #7b2ff7, #f107a3);
        color: #fff;
        padding: 0.8rem 1.5rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    </style>
    <div class="message-box">
        <h2>✅ Payment Successful</h2>
        <p>Your payment of ₦$amountPaid was successful.</p>
    
        <div class="btn-container">
            <a href="fee-payment.php" class="btn">Return to Fee Payment Page</a>
        </div>
    </div>
    HTML;
    exit;

} else {
    // Failed or invalid transaction
    echo <<<HTML
    <style>
    body {
        background-color: #f3efe7;
    }
    .message-box {
        max-width: 500px;
        margin: 5rem auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 3rem 2.5rem;
        text-align: center;
    }
    h2  {
        font-size: 1.8rem;
        color: #222;
        margin-bottom: 0.8rem;
    }
    .btn-container {
        margin-top: 2rem;
    }
    .btn {
        display: inline-block;
        background: linear-gradient(90deg, #7b2ff7, #f107a3);
        color: #fff;
        padding: 0.8rem 1.5rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    </style>
    <div class='message-box'>
        <h2>❌ Payment Failed</h2>

        <div class="btn-container">
            <a href="fee-payment.php" class="btn">Try Again</a>
        </div>
    </div>
    HTML;
    exit;
}
?>