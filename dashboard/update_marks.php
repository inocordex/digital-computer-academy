<?php
session_start();
include '../includes/config.php'; // Include config file

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Pagination settings
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Course filter
$course_filter = isset($_GET['course']) ? $_GET['course'] : '';

// SQL query for fetching students
$sql = "SELECT * FROM users";
if (!empty($course_filter)) {
    $sql .= " WHERE course = ?";
}
$sql .= " ORDER BY date_registered DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!empty($course_filter)) {
    $stmt->bind_param("sii", $course_filter, $records_per_page, $offset);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header {
            background-color: cornflowerblue;
            display: flex;
            position: relative;
            padding: 2%;
        }

        .mt-2 {
            padding-right: 10%;
        }

        .mt-2 h4 {
            width: max-content;
        }

        .mt-2 input {
            text-align: center;
            border-radius: 20px;
        }

        .mt-3 {
            padding-left: 25%;
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
            background-color: #066b55;
        }

        .card-header {
            font-weight: bolder;
            color: orange;
        }

        .table-bordered {
            color: #ccc;
        }

        .mt-4 .table th {
            color: orange;
        }

        table td {
            font-size: 17px;
            color: #ccc;
        }

        .mt-4 {
            color: #ccc;
            background-color: #066b55;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
            min-width: 80%;
        }

        .connt {
            max-width: 200px;
            padding-right: 30px;
            margin-bottom: 10px;
        }

        /* Ensure responsiveness */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 1%;
            }

            .mt-2 {
                padding-right: 0;
                width: 100%;
            }

            .mt-2 input,
            .mt-2 select,
            .mt-2 button {
                width: 100%;
                margin-bottom: 10px;
            }

            .connt {
                max-width: 100%;
                padding-right: 0;
            }

            .card-body {
                flex-direction: column;
                align-items: center;
                background-color: #066b55;
            }

            .card-body table {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="mt-3 w-100">
            <a href="admin_dashboard.php" class="btn btn-success w-100">Back to Dashboard</a>
        </div>
    </header>
    <div class="container mt-5">

        <hr>
        <!-- Form for updating marks -->
        <div class="card mt-4">
            <form action="update_marks_backend.php" method="POST">
                <div class="mt-2">
                    <h4>Registration number</h4>
                    <input type="text" name="registration_number" placeholder="eg. DRC..../2024" required>
                </div>
                <div class="mt-2">
                    <h4>Select CAT</h4>
                    <input type="text" name="cat" placeholder="eg. cat1, cat2, cat3" required>
                </div>
                <div class="mt-2">
                    <h4>Input marks</h4>
                    <input type="number" name="marks" placeholder="eg. 12" required>
                </div>
                <div class="mt-2">
                    <h4>Submit details</h4>
                    <button class="btn btn-success w-100">Submit</button>
                </div>
            </form>

            <?php
            if (isset($_SESSION['success'])) {
                echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
                unset($_SESSION['success']);
            }

            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                unset($_SESSION['error']);
            }
            ?>

        </div><br>
        <hr>

        <!-- Filter students by course -->
        <form method="GET" class="mb-4">
            <div class="connt"><label for="course_filter">Filter by Course:</label></div>
            <div class="connt">
                <select name="course" id="course_filter" class="form-select">
                    <option value="">All</option>
                    <option value="basic" <?php if ($course_filter == 'basic') echo 'selected'; ?>>basic</option>
                    <option value="programming" <?php if ($course_filter == 'programming') echo 'selected'; ?>>programming</option>
                    <option value="software_development" <?php if ($course_filter == 'software_development') echo 'selected'; ?>>software_development</option>
                </select>
            </div>
            <div class="connt">
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
            </div>
        </form>

        <!-- Student list table -->
        <div class="card mb-4">
            <div class="card-header">Filtered Students</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Registration Number</th>
                            <th>Student Name</th>
                            <th>CAT 1</th>
                            <th>CAT 2</th>
                            <th>Final Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['child_name']); ?></td>
                                <td><?php echo $row['cat1_marks']; ?></td>
                                <td><?php echo $row['cat2_marks']; ?></td>
                                <td><?php echo $row['cat3_marks']; ?></td>
                                <td><?php echo $row['cat1_marks'] + $row['cat2_marks'] + $row['cat3_marks']; ?></td>
                                <td><?php
                                $total = $row['cat1_marks'] + $row['cat2_marks'] + $row['cat3_marks'];
                                echo $total >= 70 ? 'A' : ($total >= 60 ? 'B' : ($total >= 50 ? 'C' : ($total >= 40 ? 'D' : 'E'))); 
                                ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination links -->
        <nav>
            <ul class="pagination">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&course=<?php echo urlencode($course_filter); ?>">Previous</a>
                </li>
                <li class="page-item <?php if ($result->num_rows < $records_per_page) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&course=<?php echo urlencode($course_filter); ?>">Next</a>
                </li>
            </ul>
        </nav>

    </div>

    <hr>
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2024 Digital Computer Academy. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
