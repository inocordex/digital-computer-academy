<?php
session_start();
include '../includes/config.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Fetch all courses
$coursesResult = $conn->query("SELECT DISTINCT course FROM users");
$courses = $coursesResult->fetch_all(MYSQLI_ASSOC);

// Fetch performance data for graph
$performanceData = [];
$graphQuery = "SELECT course, AVG(marks) as avg_marks FROM results GROUP BY course";
$graphResult = $conn->query($graphQuery);
while ($row = $graphResult->fetch_assoc()) {
    $performanceData[] = $row;
}

// Generate CSV for Student Performance
if (isset($_POST['generate_performance_report'])) {
    $course_filter = trim($_POST['course_filter']);
    $sql = $course_filter ? 
        "SELECT u.registration_number, u.child_name, r.subject, r.marks 
        FROM results r 
        JOIN users u ON r.student_id = u.id 
        WHERE u.course = ?" : 
        "SELECT u.registration_number, u.child_name, r.subject, r.marks 
        FROM results r 
        JOIN users u ON r.student_id = u.id";
    $stmt = $conn->prepare($sql);
    if ($course_filter) $stmt->bind_param('s', $course_filter);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_performance_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Registration Number', 'Child Name', 'Subject', 'Marks']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Performance Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 2rem;
        }

        .card {
            margin-bottom: 1rem;
        }

        .card-header {
            background-color: #066b55;
            color: #fff;
        }

        .btn-primary {
            width: 100%;
        }

        .chart-container {
            margin-top: 2rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h2 class="text-center">Performance Reports</h2>
        </header>

        <!-- Generate Student Performance Reports -->
        <div class="card">
            <div class="card-header">Generate Student Performance Reports</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="course_filter" class="form-label">Filter by Course (optional):</label>
                        <select name="course_filter" id="course_filter" class="form-select">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course['course']); ?>">
                                    <?php echo htmlspecialchars($course['course']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="generate_performance_report" class="btn btn-primary">Download Performance Report</button>
                </form>
            </div>
        </div>

        <!-- General Performance Graph -->
        <div class="card chart-container">
            <div class="card-header">General Performance by Course</div>
            <div class="card-body">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Prepare data for the chart
        const performanceData = <?php echo json_encode($performanceData); ?>;
        const labels = performanceData.map(data => data.course);
        const avgMarks = performanceData.map(data => parseFloat(data.avg_marks));

        // Render chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Average Marks',
                    data: avgMarks,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>
