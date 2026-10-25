<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
require_once "functions.php";   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Banking Transaction Receipt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container wide">
<?php
$fullName = "";
if (isset($_POST["fullName"])) {
    $fullName = $_POST["fullName"];
}
$accountNumber = "";
if (isset($_POST["accountNumber"])) {
    $accountNumber = $_POST["accountNumber"];
}
$gender = "";
if (isset($_POST["gender"])) {
    $gender = $_POST["gender"];
}
$accountType = "";
if (isset($_POST["accountType"])) {
    $accountType = $_POST["accountType"];
}
$openingBalance = "";
if (isset($_POST["openingBalance"])) {
    $openingBalance = $_POST["openingBalance"];
}
$transactionType = "";
if (isset($_POST["transactionType"])) {
    $transactionType = $_POST["transactionType"];
}
$amount = "";
if (isset($_POST["transactionAmount"])) {
    $amount = $_POST["transactionAmount"];
}
$duration = "";
if (isset($_POST["duration"])) {
    $duration = $_POST["duration"];
}

$valid  = true;   
$errors = "";     

if (empty($fullName)) {
    $valid = false;
    $errors = $errors . "<li>Customer Full Name must not be empty.</li>";
}

if (empty($accountNumber)) {
    $valid = false;
    $errors = $errors . "<li>Account Number must not be empty.</li>";
}

if (empty($gender)) {
    $valid = false;
    $errors = $errors . "<li>Gender must be selected.</li>";
}

if (empty($accountType)) {
    $valid = false;
    $errors = $errors . "<li>Account Type must be selected.</li>";
}

$balanceOk = true;
if ($openingBalance === "") {
    $balanceOk = false;
    $valid = false;
    $errors = $errors . "<li>Opening Balance must be entered.</li>";
} elseif ($openingBalance < 0) {
    $balanceOk = false;
    $valid = false;
    $errors = $errors . "<li>Opening Balance must be zero or greater.</li>";
}

if (empty($transactionType)) {
    $valid = false;
    $errors = $errors . "<li>Transaction Type must be selected.</li>";
}

$amountOk = true;
if ($amount === "" || $amount <= 0) {
    $amountOk = false;
    $valid = false;
    $errors = $errors . "<li>Transaction Amount must be greater than zero.</li>";
}

if ($duration === "") {
    $valid = false;
    $errors = $errors . "<li>Account Duration must be entered.</li>";
} elseif ($duration < 0 || strpos($duration, ".") !== false) {
    $valid = false;
    $errors = $errors . "<li>Account Duration must be a non-negative whole number.</li>";
}

if ($transactionType == "Withdrawal" && $balanceOk && $amountOk) {
    $checkFee = calculateTransactionFee($transactionType, $amount);
    if ($amount + $checkFee > $openingBalance) {
        $valid = false;
        $errors = $errors . "<li>Withdrawal Amount plus Transaction Fee must not exceed the Opening Balance.</li>";
    }
}

$fileOk = true;
if (empty($_FILES["document"]["name"])) {
    $fileOk = false;
    $valid = false;
    $errors = $errors . "<li>An identification document must be selected.</li>";
}

$fileName = "";
$fileType = "";
$fileSize = 0;
$uploadStatus = "";

if ($valid && $fileOk) {
    $fileName = $_FILES["document"]["name"];
    $fileType = $_FILES["document"]["type"];
    $fileSize = $_FILES["document"]["size"];
    $tmpName  = $_FILES["document"]["tmp_name"];
    $target   = "uploads/" . $fileName;

    if ($fileSize > 2097152) {                 
        $valid = false;
        $errors = $errors . "<li>File is larger than 2 MB and was rejected.</li>";
    } elseif (file_exists($target)) {          
        $valid = false;
        $errors = $errors . "<li>A file named '" . $fileName . "' already exists. Rename your file and try again.</li>";
    } elseif (move_uploaded_file($tmpName, $target)) {
        $uploadStatus = "Successful";
    } else {
        $valid = false;
        $errors = $errors . "<li>The file could not be saved to the uploads folder.</li>";
    }
}

if (!$valid) {
    echo "<h1>Transaction Failed</h1>";
    echo "<div class='error-box'><strong>Please correct the following:</strong><ul>" . $errors . "</ul></div>";
    echo "<div class='center'><a class='back-link' href='index.html'>Back to Banking Form</a></div>";
} else {
    $fee          = calculateTransactionFee($transactionType, $amount);
    $charges      = calculateTotalCharges($fee);
    $finalBalance = calculateFinalBalance($openingBalance, $amount, $transactionType, $charges);
    $status       = checkAccountStatus($finalBalance);
    $interest     = calculateInterest($finalBalance, $accountType, $duration);
    $future       = calculateFutureBalance($finalBalance, $accountType, $duration);

    if ($accountType == "Savings") {
        $rateText = "3%";
    } elseif ($accountType == "Current") {
        $rateText = "5%";
    } else {
        $rateText = "2%";
    }
?>
    <h1>Banking Transaction Receipt</h1>
    <div class="success-box">Transaction processed successfully. Document uploaded successfully.</div>

    <table>
        <tr><th>Banking Information</th><th>Value</th></tr>
        <tr><td class="label">Customer Full Name</td><td><?php echo $fullName; ?></td></tr>
        <tr><td class="label">Account Number</td><td><?php echo $accountNumber; ?></td></tr>
        <tr><td class="label">Gender</td><td><?php echo $gender; ?></td></tr>
        <tr><td class="label">Account Type</td><td><?php echo $accountType; ?></td></tr>
        <tr><td class="label">Opening Balance</td><td>$<?php echo number_format($openingBalance, 2); ?></td></tr>
        <tr><td class="label">Transaction Type</td><td><?php echo $transactionType; ?></td></tr>
        <tr><td class="label">Transaction Amount</td><td>$<?php echo number_format($amount, 2); ?></td></tr>
        <tr><td class="label">Transaction Fee</td><td>$<?php echo number_format($fee, 2); ?></td></tr>
        <tr><td class="label">Total Banking Charges</td><td>$<?php echo number_format($charges, 2); ?></td></tr>
        <tr><td class="label">Final Balance</td><td>$<?php echo number_format($finalBalance, 2); ?></td></tr>
        <tr><td class="label">Account Status</td><td><?php echo $status; ?></td></tr>
        <tr><td class="label">Account Duration</td><td><?php echo $duration; ?> year(s)</td></tr>
        <tr><td class="label">Interest Rate</td><td><?php echo $rateText; ?></td></tr>
        <tr><td class="label">Calculated Interest</td><td>$<?php echo number_format($interest, 2); ?></td></tr>
        <tr><td class="label">Projected Future Balance</td><td>$<?php echo number_format($future, 2); ?></td></tr>
        <tr><td class="label">Document Name</td><td><?php echo $fileName; ?></td></tr>
        <tr><td class="label">File Type</td><td><?php echo $fileType; ?></td></tr>
        <tr><td class="label">File Size</td><td><?php echo $fileSize; ?> bytes</td></tr>
        <tr><td class="label">Upload Status</td><td><?php echo $uploadStatus; ?></td></tr>
    </table>

    <div class="center"><a class="back-link" href="index.html">Back to Banking Form</a></div>
<?php } ?>
</div>
</body>
</html>

</body>
</html>