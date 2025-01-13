<?php
// Include FPDF library
require('dashboard/fpdf.php');

// Check if necessary parameters are passed
if (isset($_GET['reg_no']) && isset($_GET['name']) && isset($_GET['course'])) {
    // Get student details from URL
    $registration_number = $_GET['reg_no'];
    $name = $_GET['name'];
    $course = $_GET['course'];
} else {
    // If parameters are missing, redirect back to the registration page
    header("Location: register.php");
    exit();
}

// Create PDF object
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Set margins (Top, Left, Right)
$pdf->SetMargins(10, 10, 10);

// Draw the ID card border (rectangle)
$pdf->Rect(10, 20, 85, 55);  // Draw a rectangle for the ID card

// Set font for the title inside the ID card (Increased by 1 size and made bold)
// Title font color: Red for Academy title
$pdf->SetFont('Arial', 'B', 10);  // Font size increased by 1
$pdf->SetTextColor(255, 0, 0);  // Red color for the title
$pdf->SetXY(12, 22);  // Position inside the card for the Academy title
$pdf->Cell(73, 6, 'NIMSO Digital Computer Academy', 0, 1, 'C');  // Title centered inside the card

// Set font and color for the "Student ID Card" title (Blue)
$pdf->SetFont('Arial', 'B', 9);  // Font size increased by 1
$pdf->SetTextColor(0, 0, 255);  // Blue color for "Student ID Card"
$pdf->SetXY(12, 27);  // Position inside the card for "Student ID Card"
$pdf->Cell(73, 6, 'Student ID Card', 0, 1, 'C'); // Title centered inside the card

// Set font for the table inside the ID card (Increased by 1 size and made bold)
// Use default black color for table
$pdf->SetFont('Arial', 'B', 8);  // Font size increased by 1
$pdf->SetTextColor(0, 0, 0);  // Black color for content text
$pdf->SetXY(12, 34);  // Position below the title inside the card

// Table for Registration Number, Name, and Course
$pdf->Cell(40, 6, 'Registration Number:', 0, 0); // Align text on left
$pdf->Cell(40, 6, $registration_number, 0, 1); // Align value next to the label

$pdf->Cell(40, 6, 'Name:', 0, 0); // Align text on left
$pdf->Cell(40, 6, $name, 0, 1); // Align value next to the label

$pdf->Cell(40, 6, 'Course:', 0, 0); // Align text on left
$pdf->Cell(40, 6, ucfirst(str_replace('_', ' ', $course)), 0, 1); // Align value next to the label

// Output the PDF as a downloadable ID Card
$pdf->Output('I', 'student_id_' . $registration_number . '.pdf');
exit();
?>
