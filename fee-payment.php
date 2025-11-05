<?php
session_start();
require_once "config/db_config.php";

// Redirect if not logged in or not a student
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$fullname = $_SESSION['fullname'];

// Fetch fee details
$query = "SELECT balance, amount_paid FROM user_fees WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $fee = $result->fetch_assoc();
    $balance = $fee['balance'];
    $amount_paid = $fee['amount_paid'];
} else {
    $balance = 0;
    $amount_paid = 0;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Payment | NovaLMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/dashboard-1.css">
    <style>
        .content h2 {
            color: #4a2c4f;
            font-size: 22px;
        }
        .payment-container {
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            width: 60%;
            max-width: 600px;
            margin: 3rem auto;
        }
        .payment-container h2 {
            color: #8e2dc2;
            margin-bottom: 35px;
        }
        .payment-container p {
            font-size: 1.1em;
        }
        .payment-form {
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .payment-form input {
            display: block;
            margin: 10px 0;
            padding: 12px;
            width: 100%;
            max-width: 300px;
            font-size: 16px;
        }
        .pay-btn {
            background: linear-gradient(135deg, #6a1b9a, #fbc02d);
            color: #fff;
            border: none;
            padding: 12px 25px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 10px;
        }
        .pay-btn:hover {
            background: linear-gradient(135deg, #6a1b9a, #fbc02d);
        }
        .top-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .balance-info {
            font-weight: bold;
        }
    </style>
</head>
<body>
<header class="top-nav">
    <div class="logo">
        <img src="assets/img/novalmslogo.png" alt="NovaLMS Logo" />
    </div>
    <nav class="top-links">
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</header>

<div class="main-container">
    <aside class="sidebar">
        <ul>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">🏠 Dashboard</li>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">📚 Courses</li>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">📝 Assignments</li>
            <li class="nav-item active" onclick="window.location.href='fee-payment.php'">💳 Fee Payment</li>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">🧪 Tech Labs</li>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">🎯 Career Tracks</li>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">📊 Progress</li>
            <li class="nav-item" onclick="window.location.href='dashboard.php'">⚙️ Settings</li>
        </ul>
    </aside>

    <main class="content">
        <h2>Payment Summary for <?php echo $_SESSION['fullname']; ?><span style="font-size: 1.2em;">🧾</span></h2>
        <div class="payment-container">
            <h2>Fee Payment Form</h2>
            <p><strong>Amount Paid:</strong> ₦<?php echo number_format($amount_paid, 2); ?></p>
            <p><strong>Outstanding Balance:</strong> ₦<?php echo number_format($balance, 2); ?></p>

            <form action="paystack-api.php" method="POST" class="payment-form">
                <label for="amount">Enter amount you wish to pay (₦):</label>
                <input type="number" name="amount" id="amount" min="100" max="<?php echo $balance; ?>" required>
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <button type="submit" class="pay-btn">Proceed to Paystack</button>
            </form>
        </div>
    </main>
</div>

<footer>
    <p>&copy; 2025 NovaLMS. All rights reserved.</p>
</footer>
</body>
</html>