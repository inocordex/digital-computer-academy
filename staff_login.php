<?php
// Start the session
session_start();

// Include database configuration
include 'includes/config.php';

$error_message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = $_POST['id_number'];
    $phone_number = $_POST['phone_number'];

    // Check if both fields are filled
    if (empty($id_number) || empty($phone_number)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Query to check if the teacher exists in the database
        $query = "SELECT * FROM teachers WHERE id_number = ? AND phone_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $id_number, $phone_number);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a matching record is found
        if ($result->num_rows === 1) {
            $teacher = $result->fetch_assoc();

            // Set session variables
            $_SESSION['teacher_id'] = $teacher['id'];
            $_SESSION['teacher_name'] = $teacher['surname'] . " " . $teacher['other_names'];
            $_SESSION['unique_id'] = $teacher['unique_id'];

            // Redirect to teacher's dashboard
            header("Location: dashboard/teacher_dashboard.php");
            exit();
        } else {
            $error_message = "Invalid ID number or phone number. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Teacher Login</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="id_number" class="form-label">ID Number</label>
                <input type="text" class="form-control" id="id_number" name="id_number" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="password" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
