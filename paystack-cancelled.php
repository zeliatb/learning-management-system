<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | NovaLMS</title>
    <link rel="stylesheet" href="assets/css/dashboard-1.css">
    <style>
        body {
        background-color: #f3efe7;
        }
        .cancel-container {
            max-width: 650px;
            margin: 5rem auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 3rem 2.5rem;
            text-align: center;
            height: fit-content;
        }

        .cancel-icon {
            font-size: 3.5rem;
            color: #ff4d4f;
            margin-bottom: 1rem;
        }

        h1 {
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

        .btn:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background: #eee;
            color: #555;
            margin-left: 0.5rem;
        }

        .btn-secondary:hover {
            background: #ddd;
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
    <div class="cancel-container">
        <div class="cancel-icon">❌</div>
        <h1>Payment Cancelled</h1>
        <p>
            Hi <strong><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Student'); ?></strong>,<br>
            It looks like you cancelled your Paystack payment before completing it.
        </p>
        <p>No payment has been processed yet. You can try again whenever you’re ready to complete your fee payment.</p>

        <div class="btn-container">
            <a href="fee-payment.php" class="btn">Try Again</a>
            <a href="dashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>