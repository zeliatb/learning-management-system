<?php
session_start();
require_once 'config/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Handle course actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = $_POST['course_id'] ?? null;

    if (isset($_POST['edit_course'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE courses SET title=?, description=? WHERE course_id=?");
        $stmt->bind_param("ssi", $title, $description, $courseId);
        $stmt->execute();
    }

    if (isset($_POST['deactivate_course'])) {
        $conn->query("UPDATE courses SET status='inactive' WHERE course_id=$courseId");
    }

    if (isset($_POST['activate_course'])) {
        $conn->query("UPDATE courses SET status='active' WHERE course_id=$courseId");
    }

    if (isset($_POST['delete_course'])) {
        $conn->query("DELETE FROM courses WHERE course_id=$courseId");
    }
}

// Fetch courses
$courses = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <style>
        /* Base Reset */
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        }

        body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f4f2f7, #eae6f2);
        color: #2f2a35;
        line-height: 1.6;
        }

        /* Top Navigation */
        header.top-nav {
        background: #4b3a5a;
        color: white;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        }

        .logo img {
        height: 40px;
        width: auto;
        max-width: 180px;
        object-fit: contain;
        }

        .top-links .nav-tab,
        .top-links .logout {
        margin-left: 16px;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        color: white;
        text-decoration: none;
        transition: background 0.3s ease;
        }

        .top-links .nav-tab:hover,
        .top-links .logout:hover {
        background: rgba(255, 255, 255, 0.3);
        }

        /* Sidebar */
        .sidebar {
        background: #e6e0ec;
        padding: 24px;
        width: 220px;
        min-height: 100vh;
        }

        .sidebar ul {
        list-style: none;
        padding-left: 0;
        }

        .sidebar .nav-item {
        padding: 15px 8px;
        margin-bottom: 15px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        color: #4b3a5a;
        transition: background 0.3s ease;
        }

        .sidebar .nav-item:hover {
        background-color: #d4cbe2;
        }

        .sidebar .nav-item a {
        text-decoration: none;
        color: inherit;
        }

        /* Main Layout */
        .main-container {
        display: flex;
        }

        .content {
        flex-grow: 1;
        padding: 40px;
        background: #fdfbff;
        }

        .content h2 {
        font-size: 26px;
        margin-bottom: 24px;
        color: #4b3a5a;
        }

        .search-container {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        }

        .search-container input[type="text"] {
        padding: 10px 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 100%;
        max-width: 300px;
        font-size: 14px;
        margin-right: 12px;
        background-color: #f4f1fa;
        color: #4b3a5a;
        }

        .search-container button {
        padding: 10px 16px;
        background-color: #7c6bb0;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.3s ease;
        }

        .search-container button:hover {
        background-color: #6a5a9e;
        }

        /* Table Styling */
        .table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        .table th,
        .table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid #eee;
        }

        .table th {
        background-color: #e3dff0;
        color: #4b3a5a;
        font-weight: 600;
        }

        .table td {
        color: #2f2a35;
        }

        .status-active {
        color: #2e7d32;
        font-weight: bold;
        }

        .status-suspended {
        color: #c62828;
        font-weight: bold;
        }

        /* Buttons */
        button {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        margin-right: 6px;
        transition: background 0.3s ease;
        }

        .action-btn {
        background-color: #7c6bb0;
        color: white;
        }

        .suspend-btn {
        background-color: #c62828;
        color: white;
        }

        .activate-btn {
        background-color: #2e7d32;
        color: white;
        }

        .delete-btn {
        background-color: #4b3a5a;
        color: white;
        }

        /* Modal */
        .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(45, 35, 65, 0.6);
        justify-content: center;
        align-items: center;
        z-index: 1000;
        }

        .modal.show {
        display: flex;
        }

        .modal-content {
        background: white;
        padding: 32px;
        border-radius: 12px;
        width: 400px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }

        .modal-content h3 {
        margin-bottom: 16px;
        color: #4b3a5a;
        }

        .modal-content label {
        display: block;
        margin-top: 12px;
        font-weight: 500;
        }

        .modal-content input,
        .modal-content textarea {
        width: 100%;
        padding: 8px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
        }

        .modal-content button {
        margin-top: 16px;
        margin-right: 8px;
        }

        /* Footer */
        footer {
        text-align: center;
        padding: 20px;
        background-color: #4b3a5a;
        color: white;
        font-size: 14px;
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo"><img src="assets/img/novalmslogo.png" alt="NovaLMS Logo" /></div>
        <nav class="top-links">
            <span class="nav-tab" onclick="window.location.href='admin-dashboard.php'">Dashboard</span>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <ul>
                <li class="nav-item"><a href="admin-dashboard.php">📊 Dashboard</a></li>
                <li class="nav-item"><a href="admin-manage-courses.php">👥 Manage Courses</a></li>
            </ul>
        </aside>

        <main class="content">
            <h2>Manage Courses</h2>
            <div class="search-container">
                <input type="text" id="courseSearch" placeholder="Search courses..." oninput="searchCourses()" />
                <button onclick="searchCourses()">Search</button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $course['title']; ?></td>
                            <td><?php echo $course['description']; ?></td>
                            <td>
                                <?php if ($course['status'] === 'active'): ?>
                                    <span class="status-active">Active</span>
                                <?php else: ?>
                                    <span class="status-suspended">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="openCourseModal(<?php echo $course['course_id']; ?>, '<?php echo $course['title']; ?>', '<?php echo $course['description']; ?>')">Edit</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Change course status?');">
                                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                    <?php if ($course['status'] === 'active'): ?>
                                        <button name="deactivate_course">Deactivate</button>
                                    <?php else: ?>
                                        <button name="activate_course">Activate</button>
                                    <?php endif; ?>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this course?');">
                                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                    <button name="delete_course">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Edit Course Modal -->
    <div id="courseModal" class="modal">
        <div class="modal-content">
            <h3>Edit Course</h3>
            <form method="POST">
                <input type="hidden" name="course_id" id="editCourseId">
                <label>Title:</label>
                <input type="text" name="title" id="editCourseTitle" required>
                <label>Description:</label>
                <textarea name="description" id="editCourseDescription" required></textarea>
                <button type="submit" name="edit_course">Save</button>
                <button type="button" onclick="closeCourseModal()">Cancel</button>
            </form>
        </div>
    </div>

     <footer>
        <p>&copy; 2025 NovaLMS. All rights reserved. | Designed by Zeliat</p>
    </footer>

    <script>
        function openCourseModal(id, title, description) {
            document.getElementById('editCourseId').value = id;
            document.getElementById('editCourseTitle').value = title;
            document.getElementById('editCourseDescription').value = description;
            document.getElementById('courseModal').classList.add('show');
        }

        function closeCourseModal() {
            document.getElementById('courseModal').classList.remove('show');
        }

        function searchCourses() {
            const query = document.getElementById('courseSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');

            rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
            });
        }
    </script>
</body>
</html>