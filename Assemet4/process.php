
<?php
require_once "functions.php";


if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo '<p>Please submit the form first.
          <a href="index.html">Back to Banking Form</a></p>';
    exit;
}


$fullName = isset($_POST["full_name"])
    ? $_POST["full_name"] : "";

$accountNumber = isset($_POST["account_number"])
    ? $_POST["account_number"] : "";

$gender = isset($_POST["gender"])
    ? $_POST["gender"] : "";

$accountType = isset($_POST["account_type"])
    ? $_POST["account_type"] : "";

$openingBalance = isset($_POST["opening_balance"])
    ? $_POST["opening_balance"] : "";

$transactionType = isset($_POST["transaction_type"])
    ? $_POST["transaction_type"] : "";

$transactionAmount = isset($_POST["transaction_amount"])
    ? $_POST["transaction_amount"] : "";

$duration = isset($_POST["duration"])
    ? $_POST["duration"] : "";

$errors = "";


if (empty($fullName)) {
    $errors .= "<li>Customer name is required.</li>";
}

if (empty($accountNumber)) {
    $errors .= "<li>Account number is required.</li>";
}

if (empty($gender)) {
    $errors .= "<li>Please select gender.</li>";
} elseif ($gender != "Male" && $gender != "Female") {
    $errors .= "<li>Invalid gender selection.</li>";
}

if (empty($accountType)) {
    $errors .= "<li>Please select account type.</li>";
} elseif ($accountType != "Savings"
       && $accountType != "Current"
       && $accountType != "Business") {
    $errors .= "<li>Invalid account type.</li>";
}


if (!isset($_POST["opening_balance"])
    || $openingBalance === ""
    || $openingBalance < 0) {
    $errors .= "<li>Opening balance must be zero or greater.</li>";
}


if (empty($transactionType)) {
    $errors .= "<li>Please select transaction type.</li>";
} elseif ($transactionType != "Deposit"
       && $transactionType != "Withdrawal") {
    $errors .= "<li>Invalid transaction type.</li>";
}


if (!isset($_POST["transaction_amount"])
    || $transactionAmount === ""
    || $transactionAmount <= 0) {
    $errors .= "<li>Transaction amount must be greater than zero.</li>";
}


if (!isset($_POST["duration"])
    || $duration === ""
    || $duration < 0
    || $duration != floor($duration)) {
    $errors .= "<li>Duration must be a non-negative whole number.</li>";
}

$fileName = "";
$fileType = "";
$fileSize = 0;
$tempName = "";
$uploadError = UPLOAD_ERR_NO_FILE;

if (isset($_FILES["document"])) {
    $fileName = $_FILES["document"]["name"];
    $fileType = $_FILES["document"]["type"];
    $fileSize = $_FILES["document"]["size"];
    $tempName = $_FILES["document"]["tmp_name"];
    $uploadError = $_FILES["document"]["error"];
}


if ($uploadError == UPLOAD_ERR_NO_FILE) {
    $errors .= "<li>Please select an identification document.</li>";
} elseif ($uploadError != UPLOAD_ERR_OK) {
    $errors .= "<li>Document upload failed.</li>";
} elseif ($fileSize > 2097152) {
    $errors .= "<li>Document must not exceed 2 MB.</li>";
}


$transactionFee = 0;

if ($transactionType == "Deposit"
    || $transactionType == "Withdrawal") {
    $transactionFee = calculateTransactionFee(
        $transactionType,
        $transactionAmount
    );
}


if ($transactionType == "Withdrawal"
    && $transactionAmount !== ""
    && $openingBalance !== ""
    && ($transactionAmount + $transactionFee) > $openingBalance) {
    $errors .= "<li>Withdrawal plus fee exceeds available balance.</li>";
}

$uploadPath = "uploads/" . $fileName;

if ($uploadError == UPLOAD_ERR_OK
    && $fileSize <= 2097152
    && file_exists($uploadPath)) {
    $errors .= "<li>A file with this name already exists. Rename it.</li>";
}


if ($errors != "") {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Validation Errors</title>
    </head>
    <body>
        <h1>Please Correct These Errors</h1>
        <ul>' . $errors . '</ul>
        <a href="index.html">Back to Banking Form</a>
    </body>
    </html>';
    exit;
}


if (!move_uploaded_file($tempName, $uploadPath)) {
    echo '<p>Unable to save the document.
          Check the uploads folder permissions.</p>';
    echo '<a href="index.html">Back to Banking Form</a>';
    exit;
}


$transactionFee = calculateTransactionFee(
    $transactionType,
    $transactionAmount
);

$totalCharges = calculateTotalCharges($transactionFee);

$finalBalance = calculateFinalBalance(
    $openingBalance,
    $transactionType,
    $transactionAmount,
    $totalCharges
);

if ($accountType == "Savings") {
    $interestRate = "5%";
} elseif ($accountType == "Current") {
    $interestRate = "2%";
} else {
    $interestRate = "3%";
}


$interest = calculateInterest(
    $accountType,
    $finalBalance,
    $duration
);

$accountStatus = checkAccountStatus($finalBalance);

$futureBalance = calculateFutureBalance(
    $finalBalance,
    $interest,
    $duration
);


function safeOutput($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Banking Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 25px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #0f766e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: left;
            overflow-wrap: anywhere;
        }

        th {
            background: #e2e8f0;
        }

        a {
            color: #0f766e;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Banking Transaction Receipt</h1>

    <table>
        <tr>
            <th>Banking Information</th>
            <th>Displayed Value</th>
        </tr>

        <tr><td>Customer Full Name</td>
            <td><?php echo safeOutput($fullName); ?></td></tr>

        <tr><td>Account Number</td>
            <td><?php echo safeOutput($accountNumber); ?></td></tr>

        <tr><td>Gender</td>
            <td><?php echo safeOutput($gender); ?></td></tr>

        <tr><td>Account Type</td>
            <td><?php echo safeOutput($accountType); ?></td></tr>

        <tr><td>Opening Balance</td>
            <td>$<?php echo number_format($openingBalance, 2); ?></td></tr>

        <tr><td>Transaction Type</td>
            <td><?php echo safeOutput($transactionType); ?></td></tr>

        <tr><td>Transaction Amount</td>
            <td>$<?php echo number_format($transactionAmount, 2); ?></td></tr>

        <tr><td>Transaction Fee</td>
            <td>$<?php echo number_format($transactionFee, 2); ?></td></tr>

        <tr><td>Total Banking Charges</td>
            <td>$<?php echo number_format($totalCharges, 2); ?></td></tr>

        <tr><td>Final Balance</td>
            <td>$<?php echo number_format($finalBalance, 2); ?></td></tr>

        <tr><td>Account Status</td>
            <td><?php echo $accountStatus; ?></td></tr>

        <tr><td>Account Duration</td>
            <td><?php echo safeOutput($duration); ?> years</td></tr>

        <tr><td>Interest Rate</td>
            <td><?php echo $interestRate; ?></td></tr>

        <tr><td>Calculated Interest</td>
            <td>$<?php echo number_format($interest, 2); ?></td></tr>

        <tr><td>Projected Future Balance</td>
            <td>$<?php echo number_format($futureBalance, 2); ?></td></tr>

        <tr><td>Document Name</td>
            <td><?php echo safeOutput($fileName); ?></td></tr>

        <tr><td>File Type</td>
            <td><?php echo safeOutput($fileType); ?></td></tr>

        <tr><td>File Size</td>
            <td><?php echo $fileSize; ?> bytes</td></tr>

        <tr><td>Upload Status</td>
            <td>Successful</td></tr>
    </table>

    <p><a href="index.html">Back to Banking Form</a></p>
    

    <p>This is a fictional banking simulation only.</p>
</div>

</body>
</html>
