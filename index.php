<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIO DCA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">NIO Digital Computer Academy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="register.php">Student Registration</a></li><!-- works only for a set period set by admin. if past, notify user that intake is closed -->
                    <li class="nav-item"><a class="nav-link" href="login.php"> Student Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"> Staff Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero text-center text-white d-flex justify-content-center align-items-center">
        <div class="hero-content">
        <h1>NIO Digital Computer Academy</h1>
            <h3>we empower your children through:</h3>
            <p>Computer education at the comfort of your Home.</p>
            <a href="register.php" class="btn btn-primary btn-lg mt-3">Get Started</a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="row">
            <div class="col-md-4 text-center">
                <i class="bi bi-laptop display-4 text-primary"></i>
                <h4 class="mt-3">Interactive Learning</h4>
                <p>Engage in live Zoom classes with experienced instructors.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-book display-4 text-primary"></i>
                <h4 class="mt-3">Comprehensive Courses</h4>
                <p>From basic computer skills to advanced programming.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-award display-4 text-primary"></i>
                <h4 class="mt-3">Certified Excellence</h4>
                <p>Earn certificates upon completing each course.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2024 Digital Computer Academy. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
