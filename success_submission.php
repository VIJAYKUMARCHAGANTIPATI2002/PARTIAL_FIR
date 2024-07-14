<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the FPDF and FPDI libraries
require('fpdf/fpdf.php');
require('FPDI/src/autoload.php'); // Ensure this path is correct based on your project structure

use setasign\Fpdi\Fpdi;

// Database connection
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "IITMPROJECTDB";
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from database
$sql = "SELECT * FROM Users ORDER BY Id DESC LIMIT 1"; // Fetch the latest entry
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("No data found");
}

mysqli_close($conn);

class PDF extends Fpdi
{
    protected $angle = 0; // Define the $angle property
    protected $extgstates = array(); // Define the $extgstates property

    // Import pages from an existing PDF
    function ImportFirstPage($file)
    {
        $this->setSourceFile($file);
        $tplIdx = $this->importPage(1);
        $this->useTemplate($tplIdx, 0, 0, 210, 297);
    }

    // Page header
    function Header()
    {
        // Only add header for pages after the first imported page
        if ($this->PageNo() > 1) {
            // Full page background color
            $this->SetFillColor(173, 216, 230); // Light blue background
            $this->Rect(0, 0, 210, 297, 'F'); // Apply background to the entire page

            // Header section
            $this->SetFillColor(173, 216, 230); // Light blue background
            $this->Rect(0, 0, 210, 50, 'F'); // Apply background to header

            // Logo
            $this->Image('logo.png', 10, 10, 30);
            
            // Title
            $this->SetFont('Arial', 'B', 15);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 20, 'Case Registration Details', 0, 1, 'C');
            $this->Ln(20);
        }
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-30);
        $this->SetFont('Arial', 'I', 8);

        // Customer care number
        $this->Cell(0, 10, 'For help, contact: 1800-123-4567', 0, 1, 'C');

        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');

        // Signature
        $this->Image('signature.png', 160, $this->GetY() - 20, 30);
    }

    // Simple table with formatted text and border
    function BasicTable($header, $data)
    {
        // Header background color
        $this->SetFillColor(173, 216, 230); // Light blue background
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(50, 50, 100);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');

        // Table width
        $tableWidth = 190;
        
        // Header
        foreach ($header as $col)
            $this->Cell($tableWidth / 2, 7, $col, 1, 0, 'C', true);
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($tableWidth / 2, 6, $row[0], 'LR', 0, 'L', $fill);
            $this->MultiCell($tableWidth / 2, 6, $row[1], 'LR', 'L', $fill);
            $fill = !$fill;
        }
        // Border at the bottom of the table
        $this->Cell($tableWidth, 0, '', 'T');
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Import the first page from Report.pdf
$pdf->ImportFirstPage('Report.pdf');

// Add a new page for the case details
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Define the header and data for the table
$header = array('Field', 'Value');
$table_data = array(
    array('Full Name', $data['Full_Name']),
    array('Date of Birth', $data['Date_of_Birth']),
    array('Mobile Number', $data['Mobile_Number']),
    array('Gender', $data['Gender']),
    array('Aadhar Number', $data['Aadhar_number']),
    array('Case Description', $data['Case_Description']),
    array('Area', $data['Area']),
    array('District', $data['District'])
);

// Generate the table
$pdf->BasicTable($header, $table_data);

$pdf->Output('F', 'Combined_Report.pdf'); // Save the combined PDF file on the server

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission</title>
    <link rel="stylesheet" href="submit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<canvas></canvas>

<?php
$uniqueId = uniqid();
?>
<div class="popup center">
    <div class="icon">
        <i class="fa fa-check"></i>
    </div>
    <div class="title">
        Case Registered!
    </div>
    <div class="description">
        Your case has been registered with the case ID: <?php echo "<strong>" . $uniqueId . "</strong>"; ?> We will reach out to you within a few hours.
    </div>
    <div class="dismiss-btn">
        <button id="dismiss-popup-btn">
            <a href="Combined_Report.pdf" download id="downloadLink">Download Details</a>
        </button>
    </div>
</div>

<div class="center">
    <button id="open-popup-btn">
        CLICK HERE FOR CASE ID
    </button>
</div>

<script src="submit.js"></script>

</body>
</html>
