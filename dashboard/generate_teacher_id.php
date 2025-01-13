<?php
// Include the FPDF library
require('fpdf.php');

// Fetch teacher data from the database based on unique ID
$unique_id = $_GET['unique_id'];  // Get recognition ID from the URL
include('../includes/config.php');  // Include database connection

// Query to get teacher details
$query = "SELECT * FROM teachers WHERE unique_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $unique_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

// Check if teacher exists
if (!$teacher) {
    die("Teacher not found.");
}

// Create PDF object with A4 size (210mm x 297mm)
$pdf = new FPDF('P', 'mm', 'A4'); // A4 size (210mm x 297mm)
$pdf->AddPage();

// Set margins (Top, Left, Right)
$pdf->SetMargins(10, 10, 10);  // Set margins to avoid content getting cut off

// Draw the ID card border (rectangle) - Black border
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(0, 0, 0);  // Black color for borders
$pdf->Rect(10, 10, 80, 60);  // Rectangle at (x=10, y=10), width=80mm, height=60mm

// Set title font and color (Red)
$pdf->SetFont('Arial', 'B', 9);  // Font size increased by 1
$pdf->SetTextColor(255, 0, 0);  // Red color for text
$pdf->SetXY(12, 22);  // Position inside the card for the Academy title
$pdf->Cell(73, 6, 'NIMSO Digital Computer Academy', 0, 1, 'C');  // Title centered inside the card
// Set the ID card title font and color (Blue)
$pdf->SetFont('Arial', 'B', 8);  // Font size increased by 1
$pdf->SetTextColor(0, 0, 255);  // Blue color for text
$pdf->SetXY(12, 27);  // Position inside the card for "Student ID Card"
$pdf->Cell(73, 6, 'STAFF ID Card', 0, 1, 'C'); // Title centered inside the card

// Set font for the table content (Bold Black)
$pdf->SetFont('Arial', 'B', 7);  // Bold and increased size by 1
$pdf->SetTextColor(0, 0, 0);  // Black color for text
$pdf->SetXY(15, 35);

// Set green border color for the table
$pdf->SetDrawColor(0, 255, 0);  // Green color for table borders

// Adjusted table width to fit the ID card (we'll make it narrower)
$cell_width = 38;  // Width for each column (to fit inside 80mm)

// Draw table with content from recognition ID to phone number with green borders
$pdf->Cell($cell_width, 5, 'Recognition ID:', 0, 0, 'L');  // First column with green border
$pdf->Cell($cell_width, 5, $teacher['unique_id'], 0, 1, 'L');  // Second column with green border

$pdf->Cell($cell_width, 5, 'Name:', 0, 0, 'L');
$pdf->Cell($cell_width, 5, $teacher['surname'] . ' ' . $teacher['other_names'], 1, 1, 'L');

$pdf->Cell($cell_width, 5, 'Course Teaching:', 0, 0, 'L');
$pdf->Cell($cell_width, 5, $teacher['course'], 1, 1, 'L');

$pdf->Cell($cell_width, 5, 'ID Number:', 0, 0, 'L');
$pdf->Cell($cell_width, 5, $teacher['id_number'], 1, 1, 'L');

$pdf->Cell($cell_width, 5, 'Phone:', 0, 0, 'L');
$pdf->Cell($cell_width, 5, $teacher['phone_number'], 1, 1, 'L');

// Add the profile (passport) photo on the right with border
$photo_path = '../uploads/photos/' . $teacher['passport_photo'];  // Path to the photo
if (file_exists($photo_path)) {
    // Resize and place the photo (x=100, y=15, width=20mm, height=20mm)
    $pdf->SetDrawColor(0, 0, 0);  // Black border around the image
    $pdf->Rect(100, 15, 20, 20);  // Drawing a rectangle around the image
    $pdf->Image($photo_path, 100, 15, 20, 20);  // Image at X=100, Y=15, Width=20mm, Height=20mm
}

// Output the ID card as a PDF to be downloaded
$pdf->Output('I', 'teacher_id_' . $teacher['unique_id'] . '.pdf');
exit();
?>
