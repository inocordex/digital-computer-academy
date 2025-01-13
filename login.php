<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Digital Computer Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Digital Computer Academy</a>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Login</h2>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
            <a href="register.php" class="btn btn-primary">register</a>
        </form>

        <?php
        if (isset($_POST['login'])) {
            $registration_number = $_POST['registration_number'];
            $phone = $_POST['phone'];

            // Check if registration number and phone match in the database
            $sql = "SELECT * FROM users WHERE registration_number = '$registration_number' AND phone = '$phone'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Successful login
                $user = $result->fetch_assoc();
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['child_name'] = $user['child_name'];
                $_SESSION['course'] = $user['course'];
                echo "<div class='alert alert-success mt-3'>Login successful! Welcome, " . $user['child_name'] . ".</div>";
                header('Location: dashboard/student_dashboard.php');
                exit;
            } else {
                echo "<div class='alert alert-danger mt-3'>Invalid Registration Number or Phone Number.</div>";
            }
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
