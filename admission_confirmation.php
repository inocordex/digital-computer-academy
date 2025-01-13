<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Confirmation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styling -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .confirmation-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px auto;
            max-width: 600px;
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .confirmation-header h2 {
            color: #0056b3;
        }

        .confirmation-content {
            margin-bottom: 20px;
        }

        .confirmation-content p {
            font-size: 16px;
            color: #333;
        }

        .confirmation-details {
            margin-top: 20px;
        }

        .confirmation-details .row {
            margin-bottom: 10px;
        }

        .confirmation-details .col-md-4 {
            font-weight: bold;
        }

        .btn-download {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="confirmation-container">
            <div class="confirmation-header">
                <h2>Admission Confirmation</h2>
                <p>Thank you for registering at NIMSO Digital Computer Academy!</p>
            </div>

            <div class="confirmation-content">
                <p>Your admission is successful. Below are your registration details:</p>
            </div>

            <div class="confirmation-details">
                <div class="row">
                    <div class="col-md-4">Registration Number:</div>
                    <div class="col-md-8"><?php echo $_GET['reg_no']; ?></div>
                </div>
                <div class="row">
                    <div class="col-md-4">Name:</div>
                    <div class="col-md-8"><?php echo $_GET['name']; ?></div>
                </div>
                <div class="row">
                    <div class="col-md-4">Course:</div>
                    <div class="col-md-8"><?php echo ucfirst(str_replace('_', ' ', $_GET['course'])); ?></div>
                </div>
            </div>

            <!-- Download ID Card Button -->
            <div class="text-center">
                <a href="generate_student_id.php?reg_no=<?php echo $_GET['reg_no']; ?>&name=<?php echo $_GET['name']; ?>&course=<?php echo $_GET['course']; ?>" class="btn-download">Download ID Card</a>
            </div>

            <!-- Back Link -->
            <div class="back-link">
                <a href="register.php">Back to Registration</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
