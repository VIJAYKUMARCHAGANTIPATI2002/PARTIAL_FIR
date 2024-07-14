<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $full_name = $_POST["full_name"];
    $Date_of_Birth = $_POST["Date_of_Birth"];
    $Mobile_Number = $_POST["Mobile_Number"];
    $Gender = $_POST["Gender"];
    $Aadhar_number = $_POST["Aadhar_number"];
    $Case_Description = $_POST["Case_Description"];
    $Area = $_POST["Area"];
    $District = $_POST["District"];

    // Store form data in session
    $_SESSION['full_name'] = $full_name;
    $_SESSION['Date_of_Birth'] = $Date_of_Birth;
    $_SESSION['Mobile_Number'] = $Mobile_Number;
    $_SESSION['Gender'] = $Gender;
    $_SESSION['Aadhar_number'] = $Aadhar_number;
    $_SESSION['Case_Description'] = $Case_Description;
    $_SESSION['Area'] = $Area;
    $_SESSION['District'] = $District;

    // Database connection details
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "IITMPROJECTDB";

    // Establish connection to the database
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL query using prepared statements
    $stmt = $conn->prepare("INSERT INTO Users (Id, Full_Name, Date_of_Birth, Mobile_Number, Gender, Aadhar_number, Case_Description, Area, District, time_of_register) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())");
    $stmt->bind_param("ssssssss", $full_name, $Date_of_Birth, $Mobile_Number, $Gender, $Aadhar_number, $Case_Description, $Area, $District);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the success submission page
        header("Location: success_submission.php");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }

    // Close the statement and the database connection
    $stmt->close();
    mysqli_close($conn);
} else {
    echo "Invalid request method.";
}
