<?php
session_start();
include '../includes/config.php'; // Include configuration file

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user details from the database using the session user_id
$user_id = $_SESSION['user_id'];

// Use prepared statement to prevent SQL injection
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user_id to the statement
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If no user found, redirect to login page
if (!$user) {
    header("Location: ../login.php");
    exit();
}

// Extract user information
$registration_number = $user['registration_number'];
$parent_name = $user['parent_name'];
$child_name = $user['child_name'];
$course = $user['course'];
$date_registered = $user['date_registered'];

// Check if CAT marks are set, otherwise initialize to 0
$cat1_marks = isset($user['cat1_marks']) ? $user['cat1_marks'] : 0;
$cat2_marks = isset($user['cat2_marks']) ? $user['cat2_marks'] : 0;
$cat3_marks = isset($user['cat3_marks']) ? $user['cat3_marks'] : 0;
$final_marks = isset($user['final_marks']) ? $user['final_marks'] : 0;
$grade = isset($user['grade']) ? $user['grade'] : 0;

// Fetch the current class link and start time from the settings table
$classQuery = "SELECT class_link, class_start_time FROM settings WHERE id = 1";
$classResult = $conn->query($classQuery);
$classData = $classResult->fetch_assoc();
$classLink = $classData['class_link'] ?? '';
$classStartTime = $classData['class_start_time'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header {
            background-color: #3e316b75;
            display: flex;
            position: relative;
            padding: 2%;
        }

        .mt-3 {
            display: flex;
            height: min-content;
        }

        .c-link {
            padding-left: 50%;
        }

        .card h1 {
            color: gold;
            padding-left: 25%;
        }

        .card-body {
            color: #ccc;
            display: flex;
            background-color: #3e316b75;
        }

        .card-header {
            font-weight: bolder;
            color: gold;
        }

        .table-bordered {
            color: #fff;
        }

        .mt-4 .table th {
            color: gold;
        }

        table td {
            font-size: 17px;
            color: #fff;
        }

        .mt-4 {
            color: #fff;
            background-color: #3e316b75;
        }
        .mtd{
            padding: 20px;
        }
        .mtd h1{
            min-width: max-content;
        }
        a{
            text-decoration: none;
            color: gold;
        }
    </style>
</head>

<body>
    <header>
    </header>
    <div class="container mt-5">

        <!-- Personal Details Section -->
        <div class="card">
            <div class="mt-3">
                <div class="mtd"><h1 class="btn btn-success w-100"><?php echo htmlspecialchars($child_name); ?></h1></div>
                <div class="mtd"><h1 class="btn btn-success w-100"><a href="logout.php">Logout</a></h1></div>
                <div class="mtd"><h1 class="btn btn-success w-100">Personal Details</h1></div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Registration Number</th>
                        <td><?php echo htmlspecialchars($registration_number); ?></td>
                    </tr>
                    <tr>
                        <th>Parent Name</th>
                        <td><?php echo htmlspecialchars($parent_name); ?></td>
                    </tr>
                    <tr>
                        <th>Child's Name</th>
                        <td><?php echo htmlspecialchars($child_name); ?></td>
                    </tr>
                    <tr>
                        <th>Course Enrolled</th>
                        <td><?php echo ucfirst(htmlspecialchars($course)); ?></td>
                    </tr>
                    <tr>
                        <th>Date of Registration</th>
                        <td><?php echo htmlspecialchars($date_registered); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        <?php
        // Fetch the current class link and start time from the settings table
        $classQuery = "SELECT class_link, class_start_time FROM settings WHERE id = 1";
        $classResult = $conn->query($classQuery);
        $classData = $classResult->fetch_assoc();
        $classLink = $classData['class_link'] ?? '';
        $classStartTime = $classData['class_start_time'] ?? '';

        // Only show the class section if the course is selected
        if (!empty($classLink) && !empty($classStartTime)):
            ?>

            <!-- Course Outline and Join Class Link -->
            <div class="card mt-4">
                <div class="card-header">
                    Course Outline
                </div>
                <div class="card-body">
                    <table>
                        <td> <?php
                        // Display the course outline based on the selected course
                        if ($course == 'basic') {
                            echo "<ul>";
                            echo "<li>1: Introduction to Computers</li>";
                            echo "<li>2: Basic Computer Hardware and Software</li>";
                            echo "<li>3: Operating System Basics</li>";
                            echo "<li>4: Introduction to Word Processing (MS Word)</li>";
                            echo "<li>5: Spreadsheet Basics (MS Excel)</li>";
                            echo "<li>6: Presentation Software (MS PowerPoint)</li>";
                            echo "<li>7: Introduction to the Internet and Browsing</li>";
                            echo "<li>8: Introduction to Email and Communication Tools</li>";
                            echo "<li>9: File Management and Storage</li>";
                            echo "<li>10: Introduction to Digital Security</li>";
                            echo "<li>11: Computer Troubleshooting Basics</li>";
                            echo "<li>12: Certification Exam</li>";
                            echo "</ul>";
                        } elseif ($course == 'programming') {
                            echo "<ul>";
                            echo "<li>1: Introduction to Programming</li>";
                            echo "<li>2: Object-Oriented Programming - Java</li>";
                            echo "<li>3: Understanding Low-Level Programming - C</li>";
                            echo "<li>4: HTML - Introduction and Basics</li>";
                            echo "<li>5: CSS - Styling Web Pages</li>";
                            echo "<li>6: JavaScript - Client-Side Scripting</li>";
                            echo "<li>7: Python - Programming Basics</li>";
                            echo "<li>8: Certification Exam</li>";
                            echo "</ul>";
                        } elseif ($course == 'software_development') {
                            echo "<ul>";
                            echo "<li>lec 1: Introduction to Software Development</li>";
                            echo "<li>lec 2: Software Development Process</li>";
                            echo "<li>lec 3: Web Application Development using HTML, CSS, JS</li>";
                            echo "<li>lec 4: Python, Django Web Development</li>";
                            echo "<li>lec 5: Mobile Application Development</li>";
                            echo "<li>lec 6: Software Project Proposal and Documentation</li>";
                            echo "<li>lec 7: Final Project (User's Choice of Language)</li>";
                            echo "<li>lec 8: Project Presentation for Certification</li>";
                            echo "</ul>";
                        }
                        ?></td>

                        <hr>

                        <td>
                            <div class="c-link">
                                <!-- Join Class Link -->
                                <h5>Join Your Class</h5>
                                <p>
                                    <strong>Scheduled Class Time:</strong>
                                    <?php echo htmlspecialchars($classStartTime); ?><br>
                                <p id="countdown-timer">Loading countdown...</p>
                                <script>
                                    // Countdown Timer
                                    function startCountdown(classTime) {
                                        const timer = document.getElementById("countdown-timer");
                                        const classStartTime = new Date(classTime).getTime();
                                        const interval = setInterval(() => {
                                            const now = new Date().getTime();
                                            const distance = classStartTime - now;

                                            if (distance <= 0) {
                                                timer.innerHTML = "The class has started!";
                                                clearInterval(interval);
                                            } else {
                                                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                timer.innerHTML = `Class starts in ${hours}h ${minutes}m ${seconds}s`;
                                            }
                                        }, 1000);
                                    }

                                    // Start countdown with the fetched class start time
                                    startCountdown("<?php echo $classStartTime; ?>");
                                </script>
                                <a href="<?php echo htmlspecialchars($classLink); ?>" target="_blank"
                                    class="btn btn-primary">Join Now</a>
                                </p>
                            </div>
                        </td>
                    </table>




                </div>

            </div>
            <!-- CAT 1 Section (AI-generated) -->
            <div class="card mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>CAT 1: (AI Graded)</th>
                            <th>CAT 2: Teacher Graded</th>
                            <th>CAT 3: Final Exam</th>
                            <th>Overall Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php if ($cat1_marks == 0): ?>
                                    <p>Not yet posted or graded.</p>
                                <?php else: ?>
                                    <p><strong>Score: </strong> <?php echo $cat1_marks; ?> / 20</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($cat2_marks == 0): ?>
                                    <p>Not yet posted or graded.</p>
                                <?php else: ?>
                                    <p><strong>Score: </strong> <?php echo $cat2_marks; ?> / 20</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($cat3_marks == 0): ?>
                                    <p>Not yet posted or graded.</p>
                                <?php else: ?>
                                    <p><strong>Score: </strong> <?php echo $cat3_marks; ?> / 60</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                // Ensure no final grading if any CAT marks are missing
                                if ($cat1_marks == 0 || $cat2_marks == 0 || $cat3_marks == 0) {
                                    $final_marks = 0; // Reset final marks
                                    $grade = "Not yet graded"; // Indicate grading is incomplete
                                    echo "<p><strong>Final Grading:</strong> Not yet all CATs graded.</p>";
                                } else {
                                    // Calculate final marks if all CATs are graded
                                    $final_marks = $cat1_marks + $cat2_marks + $cat3_marks;

                                    // Determine the grade
                                    if ($final_marks < 40) {
                                        $grade = 'E';
                                    } elseif ($final_marks < 50) {
                                        $grade = 'D';
                                    } elseif ($final_marks < 60) {
                                        $grade = 'C';
                                    } elseif ($final_marks < 70) {
                                        $grade = 'B';
                                    } else {
                                        $grade = 'A';
                                    }

                                    echo "<p><strong>Marks Scored:</strong> $final_marks%</p>";
                                    echo "<p><strong>Grade:</strong> $grade</p>";
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <!-- Admin has not selected a course, hide class schedule and join button -->
            <p>The class schedule and join button will be available once the course is selected by the admin.</p>
        <?php endif; ?>


    </div>
    <!-- Features Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Revision Materials</h2>
        <div class="row">
            <div class="col-md-4 text-center">
                <i class="bi bi-laptop display-4 text-primary"></i>
                <h4 class="mt-3"> Lesson Videos</h4>
                <p><a href="">click here for youtube videos of this class.</a></p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-book display-4 text-primary"></i>
                <h4 class="mt-3">Past papers</h4>
                <p><a href="">click here</a></p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-award display-4 text-primary"></i>
                <h4 class="mt-3">Books</h4>
                <p><a href="">click here</a></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2024 Digital Computer Academy. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>