<?php
session_start();
require_once 'config/db_config.php';

// Redirect to login page if not logged in or not admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Total Students
$studentsQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'student'");
$totalStudents = $studentsQuery->fetch_assoc()['total'];

// Active Courses
$coursesQuery = $conn->query("SELECT COUNT(*) AS total FROM courses WHERE status = 'active'");
$activeCourses = $coursesQuery->fetch_assoc()['total'];

// Pending Approvals
$pendingQuery = $conn->query("SELECT COUNT(*) AS total FROM enrollments WHERE status = 'pending'");
$pendingApprovals = $pendingQuery->fetch_assoc()['total'];

// Static Admin Activities
$recentActivities = [
    ['action' => 'Created new course: Cloud Computing', 'created_at' => '2025-10-26 14:32:00'],
    ['action' => 'Suspended student: user_id 5', 'created_at' => '2025-10-25 09:18:00'],
    ['action' => 'Approved enrollment for user_id 3', 'created_at' => '2025-10-24 16:45:00'],
    ['action' => 'Updated course description: AI & Machine Learning', 'created_at' => '2025-10-23 11:02:00'],
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NovaLMS Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/admin-db.css">
</head>
<body>
    <header class="top-nav">
        <div class="logo">
            <img src="assets/img/novalmslogo.png" alt="NovaLMS Logo" />
        </div>
        <nav class="top-links">
            <span class="nav-tab" onclick="toggleSection('overview')">Overview</span>
            <span class="nav-tab" onclick="toggleSection('approvals')">Approvals</span>
            <span class="nav-tab" onclick="toggleSection('activities')">Activities</span>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <ul>
                <li class="nav-item" onclick="toggleSection('overview')">📊 Dashboard</li>
                <li class="nav-item" onclick="window.location.href='admin-manage-students.php'">👥 Manage Students</li>
                <li class="nav-item" onclick="window.location.href='admin-manage-courses.php'">📚 Manage Courses</li>
                <li class="nav-item" onclick="toggleSection('approvals')">✅ Approvals</li>
                <li class="nav-item" onclick="toggleSection('activities')">📝 Admin Logs</li>
            </ul>
        </aside>

        <main class="content">
            <!-- Overview Section -->
            <section id="overview" class="dashboard-section">
                <h2>Welcome back, <?php echo $_SESSION['fullname']; ?> 👋</h2>
                <div class="cards">
                    <div class="card total-users">
                        <h3>Total Students</h3>
                        <p><?php echo $totalStudents; ?></p>
                    </div>
                    <div class="card active-courses">
                        <h3>Active Courses</h3>
                        <p><?php echo $activeCourses; ?></p>
                    </div>
                    <div class="card pending-approvals">
                        <h3>Pending Approvals</h3>
                        <p><?php echo $pendingApprovals; ?></p>
                    </div>
                    <div class="card system-uptime">
                        <h3>System Uptime</h3>
                        <p>99.9% This Month</p>
                    </div>
                    <div class="card recent-activities">
                        <h3>Recent Admin Activities</h3>
                        <ul>
                            <?php foreach ($recentActivities as $activity): ?>
                                <li><?php echo $activity['action'] . " (" . $activity['created_at'] . ")"; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Approvals -->
            <section id="approvals" class="dashboard-section hidden">
                <h2>Pending Approvals</h2>
                <p>Review and approve new course submissions.</p>
                <button onclick="alert('Approval workflow coming soon!')">Review Approvals</button>
            </section>

            <!-- Activities -->
            <section id="activities" class="dashboard-section hidden">
                <h2>Recent Admin Activities</h2>
                <ul>
                    <?php foreach ($recentActivities as $activity): ?>
                        <li><?php echo $activity['action'] . " (" . $activity['created_at'] . ")"; ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 NovaLMS. All rights reserved. | Designed by Zeliat</p>
    </footer>

    <script>
        function toggleSection(id) {
            const sections = document.querySelectorAll('.dashboard-section');
            sections.forEach(section => section.classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
        }
    </script>
</body>
</html>