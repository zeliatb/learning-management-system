<?php
session_start();
require_once 'config/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'] ?? null;

    if (isset($_POST['edit_student'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE user_id=?");
        $stmt->bind_param("ssi", $fullname, $email, $id);
        $stmt->execute();
    }

    if (isset($_POST['suspend_student'])) {
        $conn->query("UPDATE users SET active=0 WHERE user_id=$id");
    }

    if (isset($_POST['activate_student'])) {
        $conn->query("UPDATE users SET active=1 WHERE user_id=$id");
    }

    if (isset($_POST['delete_student'])) {
        $conn->query("DELETE FROM users WHERE user_id=$id");
    }
}

$students = $conn->query("SELECT * FROM users WHERE role='student'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <style>
        /* Base Reset */
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        }

        body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #fef6e4, #e8dbc5);
        color: #4a4633;
        line-height: 1.6;
        }

        /* Top Navigation */
        header.top-nav {
        background: #a8bfa1;
        color: #2f3e2f;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(90, 110, 88, 0.2);
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
        background: rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        color: #2f3e2f;
        text-decoration: none;
        transition: background 0.3s ease;
        }

        .top-links .nav-tab:hover,
        .top-links .logout:hover {
        background: rgba(255, 255, 255, 0.35);
        }

        /* Sidebar */
        .sidebar {
        background: #e3e8e1;
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
        color: #5a6e58;
        transition: background 0.3s ease;
        }

        .sidebar .nav-item:hover {
        background-color: #d6dacf;
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
        background: #fef6e4;
        }

        .content h2 {
        font-size: 26px;
        margin-bottom: 24px;
        color: #5a6e58;
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
        background-color: #fef6e4;
        color: #4a4633;
        }

        .search-container button {
        padding: 10px 16px;
        background-color: #a8bfa1;
        color: #2f3e2f;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.3s ease;
        }

        .search-container button:hover {
        background-color: #94ab8e;
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
        background-color: #e8dbc5;
        color: #5a6e58;
        font-weight: 600;
        }

        .table td {
        color: #4a4633;
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
        background-color: #a8bfa1;
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
        background-color: #5a6e58;
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
        background: rgba(70, 60, 40, 0.6);
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
        color: #5a6e58;
        }

        .modal-content label {
        display: block;
        margin-top: 12px;
        font-weight: 500;
        }

        .modal-content input {
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
        background-color: #a8bfa1;
        color: #2f3e2f;
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
                <li class="nav-item"><a href="admin-manage-students.php">👥 Manage Students</a></li>
            </ul>
        </aside>

        <main class="content">
            <h2>Manage Students</h2>
            <div class="search-container">
                <input type="text" id="studentSearch" placeholder="Search students..." oninput="searchStudents()" />
                <button onclick="searchStudents()">Search</button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $student['fullname']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td>
                                <?php if ($student['active']): ?>
                                    <span class="status-active">Active</span>
                                <?php else: ?>
                                    <span class="status-suspended">Suspended</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="action-btn" onclick="openEditModal(<?php echo $student['user_id']; ?>, '<?php echo $student['fullname']; ?>', '<?php echo $student['email']; ?>')">Edit</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">
                                    <?php if ($student['active']): ?>
                                        <button class="suspend-btn" name="suspend_student" onclick="return confirm('Are you sure you want to suspend this student?');">Suspend</button>
                                    <?php else: ?>
                                        <button class="activate-btn" name="activate_student" onclick="return confirm('Activate this student?');">Activate</button>
                                    <?php endif; ?>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this student?');">
                                    <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">
                                    <button class="delete-btn" name="delete_student">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Student</h3>
            <form method="POST">
                <input type="hidden" name="user_id" id="editUserId">
                <label>Full Name:</label>
                <input type="text" name="fullname" id="editFullname" required>
                <label>Email:</label>
                <input type="email" name="email" id="editEmail" required>
                <button type="submit" name="edit_student">Save</button>
                <button type="button" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 NovaLMS. All rights reserved. | Designed by Zeliat</p>
    </footer>

    <script>
        function openEditModal(id, name, email) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editFullname').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editModal').classList.add('show');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        function searchStudents() {
            const query = document.getElementById('studentSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');

            rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
            });
        }
    </script>
</body>
</html>