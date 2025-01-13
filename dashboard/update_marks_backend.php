<?php
session_start();
include '../includes/config.php'; // Include database configuration

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the form inputs
    $registration_number = trim($_POST['registration_number']);
    $cat = trim($_POST['cat']);
    $marks = intval($_POST['marks']);

    // Validate inputs
    if (empty($registration_number) || empty($cat) || $marks < 0 || $marks > 100) {
        $_SESSION['error'] = "Invalid input. Please provide correct details.";
        header("Location: update_marks.php");
        exit();
    }

    // Validate which CAT marks to update
    $cat_column = '';
    if ($cat === "cat1") {
        $cat_column = "cat1_marks";
    } elseif ($cat === "cat2") {
        $cat_column = "cat2_marks";
    } elseif ($cat === "cat3") {
        $cat_column = "cat3_marks";
    } else {
        $_SESSION['error'] = "Invalid CAT selection. Use cat1, cat2, or cat3.";
        header("Location: update_marks.php");
        exit();
    }

    // Update the marks in the database
    $sql = "UPDATE users SET $cat_column = ? WHERE registration_number = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("is", $marks, $registration_number);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Marks for $cat updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update marks. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error. Please try again later.";
    }

    // Redirect back to the update_marks.php page
    header("Location: update_marks.php");
    exit();
} else {
    // If the request is not POST, redirect to the form
    header("Location: update_marks.php");
    exit();
}
?>
