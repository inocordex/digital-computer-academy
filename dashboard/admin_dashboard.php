<?php
session_start();
include '../includes/config.php'; // Ensure this file initializes $conn correctly

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Debugging function to log SQL errors (Optional)
function logError($stmt) {
    if (!$stmt) {
        die("SQL Error: " . $GLOBALS['conn']->error);
    }
}

// Update class link and start time
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_link'], $_POST['class_start_time'], $_POST['course_filter'])) {
    $new_class_link = trim($_POST['class_link']);
    $new_class_start_time = trim($_POST['class_start_time']);
    $course_filter = trim($_POST['course_filter']); // Selected course

    // Step 1: Delete the existing class link from `settings`
    $deleteQuery = "DELETE FROM settings WHERE id = 1";
    $deleteStmt = $conn->prepare($deleteQuery);
    if ($deleteStmt) {
        $deleteStmt->execute();
    } else {
        echo "Error deleting class link: " . $conn->error;
    }

    // Step 2: Insert the new class link and start time
    $insertQuery = "INSERT INTO settings (id, class_link, class_start_time) VALUES (1, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    if ($insertStmt) {
        $insertStmt->bind_param('ss', $new_class_link, $new_class_start_time);
        $insertStmt->execute();
    } else {
        echo "Error inserting class details: " . $conn->error;
    }

    // Step 3: Update the `users` table for the selected course
    $updateCourseQuery = "UPDATE users SET class_link = ? WHERE course = ?";
    $updateCourseStmt = $conn->prepare($updateCourseQuery);
    if ($updateCourseStmt) {
        $updateCourseStmt->bind_param('ss', $new_class_link, $course_filter);
        $updateCourseStmt->execute();
    } else {
        echo "Error updating students: " . $conn->error;
    }

    if ($insertStmt && $insertStmt->affected_rows > 0) {
        echo "<script>alert('Class details updated successfully for $course_filter students.');</script>";
    } else {
        echo "<script>alert('Failed to update class details.');</script>";
    }
}

// Step 4: Fetch students based on course filter
$course_filter = isset($_GET['course_filter']) ? $_GET['course_filter'] : '';
if ($course_filter) {
    $sql = "SELECT id, registration_number, child_name, phone, course FROM users WHERE course = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $course_filter);
} else {
    $sql = "SELECT id, registration_number, child_name, phone, course FROM users";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();
$students = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
// Update class link and start time
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_link'], $_POST['class_start_time'])) {
    $new_class_link = trim($_POST['class_link']);
    $new_class_start_time = trim($_POST['class_start_time']);

    // First delete the existing class link record from the settings table
    $deleteQuery = "DELETE FROM settings WHERE id = 1";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->execute();

    // Now insert the new class link and start time into the settings table
    $insertQuery = "INSERT INTO settings (id, class_link, class_start_time) VALUES (1, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param('ss', $new_class_link, $new_class_start_time);
    $insertStmt->execute();

    if ($insertStmt->affected_rows > 0) {
        echo "<script>alert('Class details updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update class details.');</script>";
    }
}

// Fetch current class link and start time after update
$classLinkQuery = "SELECT class_link, class_start_time FROM settings WHERE id = 1";
$linkResult = $conn->query($classLinkQuery);
$classLinkRow = $linkResult->fetch_assoc();
$classLink = $classLinkRow ? $classLinkRow['class_link'] : '';
$classStartTime = $classLinkRow ? $classLinkRow['class_start_time'] : '';

// Fetch available courses
$coursesQuery = "SELECT DISTINCT course FROM users";
$coursesResult = $conn->query($coursesQuery);
$courses = $coursesResult->fetch_all(MYSQLI_ASSOC);

// Record attendance manually
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    $presentStudents = $_POST['attendance'];
    $currentDate = date('Y-m-d'); // Current date for attendance

    foreach ($presentStudents as $studentId) {
        // Insert attendance for each selected student
        $sql = "INSERT INTO attendance (student_id, attendance_date, status) 
                VALUES (?, ?, 'Present')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $studentId, $currentDate);
            $stmt->execute();
        } else {
            echo "Error preparing query: " . $conn->error;
        }
    }

    echo "<script>alert('Attendance recorded successfully!'); window.location.href='admin_dashboard.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header {
            background-color: #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2%;
            color: #ccc;
        }

        .d-main {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .c-link {
            max-width: 50%;
            width: 100%;
            padding: 20px;
        }

        .card-body {
            color: #ccc;
            background-color: #066b55;
        }

        .table-bordered {
            color: #ccc;
        }

        @media (max-width: 768px) {
            .d-main {
                flex-direction: column;
            }

            .c-link {
                max-width: 100%;
            }

            .btn-primary {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h2 class="text-center mb-0">Admin Dashboard</h2>
        <div>
            <a href="../logout.php" class="btn btn-success">Logout</a>
        </div>
    </header>

    <div class="container mt-5">

        <!-- Post Class Link -->
        <div class="card mb-4">
            <div class="card-header">Post Class Link and Start Time</div>
            <div class="card-body">
                <div class="d-main">
                    <div class="c-link">
                        <form method="POST">
                            <label for="class_link">Class Link</label>
                            <input type="text" name="class_link" id="class_link" class="form-control"
                                value="<?php echo htmlspecialchars($classLink ?? ''); ?>" required>

                            <label for="class_start_time" class="mt-3">Class Starting Time</label>
                            <input type="datetime-local" name="class_start_time" id="class_start_time" class="form-control"
                                value="<?php echo htmlspecialchars($classStartTime ? date('Y-m-d\TH:i', strtotime($classStartTime)) : ''); ?>" required>

                            <button type="submit" class="btn btn-primary mt-3">Update Class Details</button>
                        </form>
                    </div>
                    <div class="c-link">
                        <p><strong>Scheduled Class Time:</strong>
                            <?php echo $classStartTime ? date('d M Y, h:i A', strtotime($classStartTime)) : 'Not set'; ?>
                        </p>
                        <p id="countdown-timer">Loading countdown...</p>
                        <script>
                            function startCountdown(classTime) {
                                const timer = document.getElementById("countdown-timer");
                                if (!classTime) {
                                    timer.innerHTML = "Class time is not set.";
                                    return;
                                }

                                const classStartTime = new Date(classTime).getTime();
                                const interval = setInterval(() => {
                                    const now = new Date().getTime();
                                    const distance = classStartTime - now;

                                    if (distance <= 0) {
                                        timer.innerHTML = "Class time has arrived!";
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

                        <form action="<?php echo htmlspecialchars($classLink); ?>" method="GET" target="_blank">
                            <button type="submit" class="btn btn-success" 
                                <?php echo empty($classLink) ? 'disabled aria-disabled="true"' : ''; ?>>
                                Start Class
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View and Mark Attendance -->
        <div class="card mb-4">
            <div class="card-header">Filter Students by Course</div>
            <div class="card-body">
                <div class="d-flex justify-content-around flex-wrap">
                    <?php foreach ($courses as $course): ?>
                        <a href="admin_dashboard.php?course_filter=<?php echo urlencode($course['course']); ?>"
                            class="btn btn-primary <?php echo $course_filter == $course['course'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($course['course']); ?> Students
                        </a>
                    <?php endforeach; ?>
                </div>
                <h1 class="mt-3"><?php echo $course_filter ? "$course_filter Students" : "All Students"; ?></h1>
                <form method="POST">
                    <input type="hidden" name="course_filter" value="<?php echo htmlspecialchars($course_filter); ?>">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Registration No.</th>
                                <th>Child's Name</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><input type="checkbox" name="attendance[]" value="<?php echo $student['id']; ?>"></td>
                                    <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['child_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Mark Attendance</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
