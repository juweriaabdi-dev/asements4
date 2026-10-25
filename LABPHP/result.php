<?php
require_once "fuction.php";
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit("Please submit the student form first.");
}

$name = trim($_POST["student_name"] ?? "");
$id = trim($_POST["student_id"] ?? "");
$gender = $_POST["gender"] ?? "";
$faculty = $_POST["faculty"] ?? "";

$php = $_POST["php_mark"] ?? "";
$database = $_POST["database_mark"] ?? "";
$networking = $_POST["networking_mark"] ?? "";

// Validation
$errors = "";

if ($name == "") {
    $errors .= "Student name is required.<br>";
}

if ($id == "") {
    $errors .= "Student ID is required.<br>";
}

if (!isset($_POST["gender"]) || $gender == "") {
    $errors .= "Please select your gender.<br>";
}

if ($faculty == "") {
    $errors .= "Please select your faculty.<br>";
}

if ($php === "" || !is_numeric($php) || $php < 0 || $php > 100) {
    $errors .= "PHP mark must be between 0 and 100.<br>";
}

if ($database === "" || !is_numeric($database) || $database < 0 || $database > 100) {
    $errors .= "Database mark must be between 0 and 100.<br>";
}

if ($networking === "" || !is_numeric($networking) || $networking < 0 || $networking > 100) {
    $errors .= "Networking mark must be between 0 and 100.<br>";
}

if ($errors != "") {
    echo "<h2>Validation Errors</h2>";
    echo $errors;
    echo '<br><a href="index.html">Go Back</a>';
    exit;
}

$php = (float)$php;
$database = (float)$database;
$networking = (float)$networking;
$total = calculateTotal($php, $database, $networking);
$average = calculateAverage($total);
$grade = calculateGrade($average);
$result = checkResult($average);
$certificateName = "No file";
$fileType = "N/A";
$fileSize = 0;
$uploadStatus = "Failed";

if (
    !isset($_FILES["certificate"]) ||
    $_FILES["certificate"]["error"] == UPLOAD_ERR_NO_FILE
) {
    echo "<h2>Upload Error</h2>";
    echo "Please select a student certificate.";
    echo '<br><a href="index.html">Go Back</a>';
    exit;
}
$file = $_FILES["certificate"];

if ($file["error"] != UPLOAD_ERR_OK) {
    echo "<h2>Upload Error</h2>";
    echo "The certificate could not be uploaded.";
    echo '<br><a href="index.html">Go Back</a>';
    exit;
}
if ($file["size"] > 2 * 1024 * 1024) {
    echo "<h2>Upload Error</h2>";
    echo "Certificate size must not exceed 2 MB.";
    echo '<br><a href="index.html">Go Back</a>';
    exit;
}
$certificateName = basename($file["name"]);
$fileType = $file["type"];
$fileSize = round($file["size"] / 1024, 2);
$uploadFolder = "uploads/";
if (!is_dir($uploadFolder)) {
    echo "Please create the uploads folder first.";
    exit;
}
$destination = $uploadFolder . $certificateName;

if (file_exists($destination)) {
    echo "<h2>Upload Error</h2>";
    echo "A file with this name already exists. Rename your certificate and try again.";
    echo '<br><a href="index.html">Go Back</a>';
    exit;
}
if (move_uploaded_file($file["tmp_name"], $destination)) {
    $uploadStatus = "Successfully Uploaded";
} else {
    echo "<h2>Upload Error</h2>";
    echo "The certificate could not be saved.";
    echo '<br><a href="index.html">Go Back</a>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Results</title>
</head>
<body>

<h2>Student Examination Results</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Information</th>
        <th>Result</th>
    </tr>

    <tr>
        <td>Student Full Name</td>
        <td><?php echo htmlspecialchars($name); ?></td>
    </tr>
 
    <tr>
        <td>Student ID</td>
        <td><?php echo htmlspecialchars($id); ?></td>
    </tr>

    <tr>
        <td>Gender</td>
        <td><?php echo htmlspecialchars($gender); ?></td>
    </tr>

    <tr>
        <td>Faculty</td>
        <td><?php echo htmlspecialchars($faculty); ?></td>
    </tr>

    <tr>
        <td>PHP Mark</td>
        <td><?php echo $php; ?></td>
    </tr>

    <tr>
        <td>Database Mark</td>
        <td><?php echo $database; ?></td>
    </tr>

    <tr>
        <td>Networking Mark</td>
        <td><?php echo $networking; ?></td>
    </tr>

    <tr>
        <td>Total Marks</td>
        <td><?php echo $total; ?> / 300</td>
    </tr>

    <tr>
        <td>Average Marks</td>
        <td><?php echo number_format($average, 2); ?></td>
    </tr>

    <tr>
        <td>Grade</td>
        <td><?php echo $grade; ?></td>
    </tr>

    <tr>
        <td>Pass or Fail</td>
        <td><?php echo $result; ?></td>
    </tr>

    <tr>
        <td>Certificate Name</td>
        <td><?php echo htmlspecialchars($certificateName); ?></td>
    </tr>

    <tr>
        <td>File Type</td>
        <td><?php echo htmlspecialchars($fileType); ?></td>
    </tr>

    <tr>
        <td>File Size</td>
        <td><?php echo $fileSize; ?> KB</td>
    </tr>
    <tr>
        <td>upload Folder</td>
        <td> 

    <tr>
        <td>Upload Status</td>
        <td><?php echo htmlspecialchars($uploadStatus); ?></td>
    </tr>
</table>

<br>
<a href="index.html">Back to Student Form</a>
</body>
</html>

