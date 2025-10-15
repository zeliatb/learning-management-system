<?php
session_start();

//Redirect to login page if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Extract first name and email
$firstname = htmlspecialchars($_SESSION['firstname']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NovaLMS Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/dashboard0.css">
</head>
<body>
    <header class="top-nav">
        <div class="logo">
            <img src="assets/img/novalmslogo.png" alt="NovaLMS Logo" />
        </div>
        <nav class="top-links">
            <span class="nav-tab" onclick="toggleSection('labs')">Tech Labs</span>
            <span class="nav-tab" onclick="toggleSection('announcements')">Announcements</span>
            <span class="nav-tab" onclick="toggleSection('profile')">Profile</span>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <ul>
                <li class="nav-item" onclick="toggleSection('dashboard')">🏠 Dashboard</li>
                <li class="nav-item" onclick="toggleSection('courses')">📚 Courses</li>
                <li class="nav-item" onclick="toggleSection('assignments')">📝 Assignments</li>
                <li class="nav-item" onclick="toggleSection('labs')">🧪 Tech Labs</li>
                <li class="nav-item" onclick="toggleSection('career')">🎯 Career Tracks</li>
                <li class="nav-item" onclick="toggleSection('progress')">📊 Progress</li>
                <li class="nav-item" onclick="toggleSection('settings')">⚙️ Settings</li>
            </ul>
        </aside>

        <main class="content">
            <section id="dashboard" class="dashboard-section">
                <h2>Welcome back, <?php echo $firstname; ?> 👋</h2>
                <div class="cards">
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Explore New Tracks</h3>
                        <p>Discover trending tech careers and curated learning paths.</p>
                    </div>
                    <div class="card" onclick="toggleSection('progress')">
                        <h3>Check Your Progress</h3>
                        <p>View your completion stats and certification milestones.</p>
                    </div>
                    <div class="card" onclick="toggleSection('assignments')">
                        <h3>Upcoming Deadlines</h3>
                        <p>Stay ahead with assignment and lab due dates.</p>
                    </div>
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Learning Tip</h3>
                        <p>"Study in short bursts and take breaks — your brain loves it!"</p>
                    </div>
                    <div class="card resources">
                        <h3>Resources</h3>
                        <ul>
                            <li><a href="#">Study Guide PDF</a></li>
                            <li><a href="#">Lecture Video</a></li>
                            <li><a href="#">External LMS Tools</a></li>
                        </ul>
                    </div>
                    <div class="card">
                        <h3>My Achievements</h3>
                        <ul>
                            <li>✔️ Certificate in Python Programming</li>
                            <li>✔️ Badge: Top Performer in UX Design</li>
                        </ul>
                    </div>
                    <div class="card downloads">
                        <h3>Downloads</h3>
                        <ul>
                            <li><a href="#">Assignment Template</a></li>
                            <li><a href="#">Course Syllabus</a></li>
                        </ul>
                    </div>
                    <div class="card">
                        <h3>Need Help?</h3>
                        <button>Chat with Support</button>
                        </div>
                </div>
            </section>

            <section id="courses" class="dashboard-section hidden">
                <h2>Tech Courses</h2>
                <div class="cards">
                    <div class="card">
                        <h3>Web Development</h3>
                        <p>HTML, CSS, JavaScript, React</p>
                    </div>
                    <div class="card">
                        <h3>Data Science</h3>
                        <p>Python, Pandas, SQL, Machine Learning</p>
                    </div>
                    <div class="card">
                        <h3>AI & Machine Learning</h3>
                        <p>Neural Networks, NLP, Deep Learning</p>
                    </div>
                    <div class="card">
                        <h3>Cybersecurity</h3>
                        <p>Ethical Hacking, Network Security</p>
                    </div>
                    <div class="card">
                        <h3>Cloud Computing</h3>
                        <p>AWS, Azure, Google</p>
                    </div>
                    <div class="card">
                        <h3>DevOps Engineering</h3>
                        <p>Docker, Kubernetes, Terraform</p>
                    </div>
                    <div class="card">
                        <h3>Product Management</h3>
                        <p>Jira, Trello, Notion, Figma, Productboard</p>
                    </div>
                </div>
            </section>

            <section id="assignments" class="dashboard-section hidden">
                <h2>Assignments</h2>
                <p>Track your submissions, due dates, and feedback.</p>
                <table class="assignment-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Web Development</td>
                            <td>Responsive Layout Project</td>
                            <td>Oct 18</td>
                            <td><span class="status pending">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Data Science</td>
                            <td>Data Cleaning Exercise</td>
                            <td>Oct 12</td>
                            <td><span class="status submitted">Submitted</span></td>
                        </tr>
                        <tr>
                            <td>Cloud Computing</td>
                            <td>AWS CLI Lab</td>
                            <td>Oct 21</td>
                            <td><span class="status pending">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section id="labs" class="dashboard-section hidden">
                <h2>Tech Labs</h2>
                <p>Practice coding, security, and data analysis in live environments.</p>

                <div class="cards">
                    <div class="card">
                        <h3>Code Sandbox</h3>
                        <p>Write and test HTML, CSS, JavaScript, and Python directly in your browser.</p>
                        <button onclick="alert('Launching Code Sandbox...')">Launch Lab</button>
                    </div>
                    <div class="card">
                        <h3>Cybersecurity Lab</h3>
                        <p>Simulate penetration testing and learn ethical hacking techniques safely.</p>
                        <button onclick="alert('Opening Cybersecurity Lab...')">Start Lab</button>
                    </div>
                    <div class="card">
                        <h3>Data Analysis Lab</h3>
                        <p>Work with real datasets using Pandas, SQL, and visualization tools.</p>
                        <button onclick="alert('Loading Data Analysis Lab...')">Enter Lab</button>
                    </div>
                    <div class="card">
                        <h3>Cloud Deployment Lab</h3>
                        <p>Deploy sample apps to AWS and Azure using CI/CD pipelines.</p>
                        <button onclick="alert('Connecting to Cloud Lab...')">Access Lab</button>
                    </div>
                </div>
            </section>

            <section id="announcements" class="dashboard-section hidden">
                <h2>Announcements</h2>
                <ul class="announcement-list">
                    <li><strong>Oct 10:</strong> Midterm exams start next week.</li>
                    <li><strong>Oct 5:</strong> New Cloud Computing lab materials uploaded.</li>
                    <li><strong>Oct 1:</strong> Welcome to the new semester! 🎉</li>
                </ul>
            </section>

            <section id="career" class="dashboard-section hidden">
                <h2>Career Tracks</h2>
                <div class="cards">
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Frontend Developer</h3>
                        <p>Master HTML, CSS, JavaScript, and React to build stunning web interfaces.</p>
                    </div>
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Backend Developer</h3>
                        <p>Learn server-side development with Node.js, Python, and databases.</p>
                    </div>
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Data Analyst</h3>
                        <p>Analyze data using SQL, Python, and visualization tools like Tableau.</p>
                    </div>
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Cybersecurity Specialist</h3>
                        <p>Explore Ethical Hacking, Network Security, and Penetration Testing.</p>
                    </div>
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>AI Engineer</h3>
                        <p>Build intelligent systems with Machine Learning and Neural Networks.</p>
                    </div>
                    <div class="card" onclick="toggleSection('courses')">
                        <h3>Cloud Architect</h3>
                        <p>Design scalable cloud solutions using AWS, Azure, and DevOps tools.</p>
                    </div>
                </div>
            </section>

            <section id="progress" class="dashboard-section hidden">
                <h2>Your Progress</h2>
                <p>Web Development: <strong>75%</strong></p>
                <div class="progress-bar"><div class="progress-fill" style="width: 75%;"></div></div>

                <p>Data Science: <strong>20%</strong></p>
                <div class="progress-bar"><div class="progress-fill" style="width: 20%;"></div></div>

                <p>Cybersecurity: <strong>80%</strong></p>
                <div class="progress-bar"><div class="progress-fill" style="width: 80%;"></div></div>

                <p>Cloud Computing: <strong>30%</strong></p>
                <div class="progress-bar"><div class="progress-fill" style="width: 30%;"></div></div>
            </section>

            <section id="settings" class="dashboard-section hidden">
                <h2>Settings</h2>
                <p>Update your preferences and notification settings.</p>

                <div class="cards">
                    <div class="card">
                        <h3>Theme Mode</h3>
                        <p>Switch between Light and Dark mode for better visibility.</p>
                        <button id="themeToggle" onclick="toggleTheme()">Dark Mode</button>
                    </div>
                    <div class="card">
                        <h3>Notification Preferences</h3>
                        <p>Choose how you want to receive updates and reminders.</p>
                        <select>
                            <option>Email only</option>
                            <option>SMS only</option>
                            <option>Both Email & SMS</option>
                            <option>None</option>
                        </select>
                    </div>
                    <div class="card">
                        <h3>Language</h3>
                        <p>Select your preferred language for the dashboard.</p>
                        <select>
                            <option>English</option>
                            <option>French</option>
                            <option>Spanish</option>
                            <option>Arabic</option>
                        </select>
                    </div>
                </div>
            </section>

            <section id="profile" class="dashboard-section hidden">
                <h2>Your Profile</h2>
                <p><strong>Name:</strong> <?php echo $firstname; ?></p>
                <img src="assets/img/default-profile.png" alt="Profile Picture" width="200">
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
        function showOptions(course) {
            alert(`You clicked on ${course}. More options coming soon!`);
        }
        function toggleTheme() {
            const body = document.body;
            const button = document.getElementById('themeToggle');

            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                button.textContent = 'Light Mode';
            } else {
                button.textContent = 'Dark Mode';
            }
        }
    </script>
</body>
</html>