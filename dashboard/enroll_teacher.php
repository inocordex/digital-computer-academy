<?php
// Include database connection
include '../includes/config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $surname = $_POST['surname'];
    $other_names = $_POST['other_names'];
    $phone_number = $_POST['phone_number'];
    $id_number = $_POST['id_number'];
    $course = $_POST['course'];
    $payment_account = $_POST['payment_account'];
    
    // Ensure the uploads/photos directory exists
    $photo_upload_dir = 'uploads/photos/';
    if (!is_dir($photo_upload_dir)) {
        mkdir($photo_upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Handle passport photo upload
    $photo_name = $_FILES['passport_photo']['name'];
    $photo_tmp = $_FILES['passport_photo']['tmp_name'];
    $photo_extension = pathinfo($photo_name, PATHINFO_EXTENSION);
    $new_photo_name = "teacher_" . time() . "." . $photo_extension;

    // Move the uploaded file to the server
    if (!move_uploaded_file($photo_tmp, $photo_upload_dir . $new_photo_name)) {
        die("Error uploading the photo.");
    }

    // Generate unique ID (e.g., STAFF001DCA/2024)
    $year = date("Y");
    $query = "SELECT COUNT(*) AS count FROM teachers WHERE YEAR(enrollment_date) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher_count = $result->fetch_assoc()['count'];
    $unique_id = "STAFF" . str_pad($teacher_count + 1, 3, '0', STR_PAD_LEFT) . "DCA/" . $year;

    // Insert teacher data into the database
    $insert_query = "INSERT INTO teachers (unique_id, surname, other_names, phone_number, id_number, course, payment_account, passport_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssssss", $unique_id, $surname, $other_names, $phone_number, $id_number, $course, $payment_account, $new_photo_name);
    $stmt->execute();

    // Redirect to ID card generation page
    header("Location: generate_teacher_id.php?unique_id=$unique_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Enroll New Teacher</h2>
        <form method="POST" enctype="multipart/form-data">
            <!-- Form fields here -->
            <div class="mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname" required>
            </div>
            <div class="mb-3">
                <label for="other_names" class="form-label">Other Names</label>
                <input type="text" class="form-control" id="other_names" name="other_names" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <div class="mb-3">
                <label for="id_number" class="form-label">ID Number</label>
                <input type="text" class="form-control" id="id_number" name="id_number" required>
            </div>
            <div class="mb-3">
                <label for="course" class="form-label">Course Enrolled For</label>
                <select class="form-control" id="course" name="course" required>
                    <option value="basic">Basic</option>
                    <option value="programming">Programming</option>
                    <option value="software_development">Software Development</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="payment_account" class="form-label">Payment Account</label>
                <input type="text" class="form-control" id="payment_account" name="payment_account" required>
            </div>
            <div class="mb-3">
                <label for="passport_photo" class="form-label">Passport Photo</label>
                <input type="file" class="form-control" id="passport_photo" name="passport_photo" required>
            </div>
            <button type="submit" class="btn btn-primary">Enroll Teacher</button>
        </form>
    </div>
</body>
</html>
