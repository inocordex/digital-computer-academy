<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Handle class link update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_link'], $_POST['class_start_time'], $_POST['course_filter'])) {
    $new_class_link = trim($_POST['class_link']);
    $new_class_start_time = trim($_POST['class_start_time']);
    $course_filter = trim($_POST['course_filter']);

    // Delete and insert class details
    $conn->query("DELETE FROM settings WHERE id = 1");
    $stmt = $conn->prepare("INSERT INTO settings (id, class_link, class_start_time) VALUES (1, ?, ?)");
    $stmt->bind_param('ss', $new_class_link, $new_class_start_time);
    $stmt->execute();

    // Update users table for selected course
    $stmt = $conn->prepare("UPDATE users SET class_link = ? WHERE course = ?");
    $stmt->bind_param('ss', $new_class_link, $course_filter);
    $stmt->execute();

    echo "<script>alert('Class details updated successfully for $course_filter students.');</script>";
}

// Fetch teacher's details
$user_id = $_SESSION['admin_id'];
$query = "SELECT unique_id, other_names, course, phone_number FROM teachers";
$result = mysqli_query($conn, $query);
$teachers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch students based on course filter
$course_filter = $_GET['course_filter'] ?? '';
$sql = $course_filter ? "SELECT * FROM users WHERE course = ?" : "SELECT * FROM users";
$stmt = $conn->prepare($sql);
if ($course_filter)
    $stmt->bind_param('s', $course_filter);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

// Fetch available courses
$coursesResult = $conn->query("SELECT DISTINCT course FROM users");
$courses = $coursesResult->fetch_all(MYSQLI_ASSOC);
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
            justify-content: center;
            align-items: center;
            padding: 2%;
            color: #ccc;
        }

        .container {
            margin-top: 2rem;
        }

        .card-body {
            color: #ccc;
            background-color: #066b55;
        }

        .table-bordered th,
        .table-bordered td {
            color: #fff;
        }

        @media (max-width: 768px) {
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
    </header>

    <div class="container">

        <!-- Other Roles -->
        <div class="card mb-4">
            <div class="card-header">Other Roles</div>
            <div class="card-body">
                <div class="d-flex justify-content-around flex-wrap">
                    <a href="../logout.php" class="btn btn-success">Logout</a>
                    <a href="enroll_teacher.php" class="btn btn-success">Enroll a Teacher</a>
                    <a href="../register.php" class="btn btn-success">Enroll a Student</a>
                    <a href="generate_reports.php" class="btn btn-success">Generate Reports</a>
                    <a href="send_notifications.php" class="btn btn-success">Send Notifications</a>
                    <a href="manage_classes.php" class="btn btn-success">Manage Classes</a>
                </div>
            </div>
        </div>

        <!-- View All Students -->
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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Registration No.</th>
                            <th>Child's Name</th>
                            <th>Phone Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['child_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No students found for the selected course.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- View All Teachers -->
        <div class="card mb-4">
            <div class="card-header">Teaching Staff</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Teaching Course</th>
                            <th>Phone Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($teacher['unique_id']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['other_names']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['course']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['phone_number']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No teacher information available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>

</html>
