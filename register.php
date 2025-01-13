<?php
include 'includes/config.php';

// Function to generate the registration number
function generateRegistrationNumber($course, $conn) {
    // Define level mapping
    $levels = [
        'basic' => '-B-',  // Basic Computer and MS Office
        'programming' => '-P-',  // Programming
        'software_development' => '-S-'  // Software Development
    ];

    // Get current year
    $year = date("Y");

    // Get the level based on the course selected
    $level = isset($levels[$course]) ? $levels[$course] : '-B-';

    // Get the last registration number from the database
    $sql = "SELECT registration_number FROM users ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $last_reg_number = '';
    if ($result->num_rows > 0) {
        $last_reg_number = $result->fetch_assoc()['registration_number'];
    }

    // Extract the number part of the last registration number (e.g., 001)
    preg_match('/(\d+)\//', $last_reg_number, $matches);
    $last_number = isset($matches[1]) ? (int) $matches[1] : 001;

    // Increment the registration number
    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);

    // Generate the registration number in the format: DCA-p-001/2024
    return "DCA" . $level . $new_number . "/" . $year;
}

// Handle registration form submission
if (isset($_POST['register'])) {
    $parent_name = $_POST['parent_name'];
    $parent_id = $_POST['parent_id'];
    $phone = $_POST['phone'];
    $child_name = $_POST['child_name'];
    $course = $_POST['course'];

    // Generate the registration number
    $registration_number = generateRegistrationNumber($course, $conn);

    // Insert into database using prepared statements
    $stmt = $conn->prepare("INSERT INTO users (parent_name, parent_id, phone, child_name, course, registration_number) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $parent_name, $parent_id, $phone, $child_name, $course, $registration_number);

    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to confirmation page with the registration details
        header("Location: admission_confirmation.php?reg_no=$registration_number&name=$child_name&course=$course");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Digital Computer Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Limit input field width */
        .form-control,
        .form-select {
            max-width: 50%;
        }
        /* Center align the form */
        .form-container {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Register for Digital Computer Academy</h2>
        <center>
        <form action="register.php" method="POST">
            <!-- Existing Fields -->
            <div class="mb-3">
                <input type="text" class="form-control" id="child_name" name="child_name" required placeholder="Student Name">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" id="parent_name" name="parent_name" required placeholder="Parent's Name">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" id="parent_id" name="parent_id" required placeholder="National ID Number">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" id="phone" name="phone" required placeholder="Phone Number">
            </div>

            <!-- New Address Fields -->
            <div class="mb-3">
                <select class="form-select" id="county" name="county" required>
                    <option value="">County</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" id="subcounty" name="subcounty" required>
                    <option value="">Sub-County</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" id="location" name="location" required>
                    <option value="">Location</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <select class="form-select" id="course" name="course" required>
                    <option value="basic">Computer Basics</option>
                    <option value="programming">Programming</option>
                    <option value="software_development">Software Development</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="register">Register</button>
        </form>
        </center>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script to Load Kenyan Counties Data -->
    <script>
        const countyDropdown = document.getElementById("county");
        const subcountyDropdown = document.getElementById("subcounty");
        const locationDropdown = document.getElementById("location");

        // Fetch Kenyan regions from JSON file
        fetch("assets/json/kenya_regions.json")
            .then((response) => response.json())
            .then((data) => {
                const counties = data.counties;
                for (const county in counties) {
                    const option = document.createElement("option");
                    option.value = county;
                    option.textContent = county;
                    countyDropdown.appendChild(option);
                }

                // Update sub-counties based on selected county
                countyDropdown.addEventListener("change", () => {
                    const selectedCounty = countyDropdown.value;
                    const subcounties = counties[selectedCounty]?.subcounties || {};
                    subcountyDropdown.innerHTML = '<option value="">Select Sub-County</option>';
                    locationDropdown.innerHTML = '<option value="">Select Location</option>';
                    for (const subcounty in subcounties) {
                        const option = document.createElement("option");
                        option.value = subcounty;
                        option.textContent = subcounty;
                        subcountyDropdown.appendChild(option);
                    }
                });

                // Update locations based on selected sub-county
                subcountyDropdown.addEventListener("change", () => {
                    const selectedSubcounty = subcountyDropdown.value;
                    const locations = counties[countyDropdown.value]?.subcounties[selectedSubcounty] || [];
                    locationDropdown.innerHTML = '<option value="">Select Location</option>';
                    locations.forEach((location) => {
                        const option = document.createElement("option");
                        option.value = location;
                        option.textContent = location;
                        locationDropdown.appendChild(option);
                    });
                });
            });
    </script>
</body>
</html>
