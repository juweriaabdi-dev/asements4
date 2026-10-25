
<?php


function calculateDeposit($openingBalance, $amount) {
    return $openingBalance + $amount;
}


function calculateWithdrawal($openingBalance, $amount) {
    return $openingBalance - $amount;
}


function calculateTransactionFee($transactionType, $amount) {
    if ($transactionType == "Deposit") {
        return 0;
    } elseif ($transactionType == "Withdrawal"
              && $amount < 500) {
        return 2;
    } elseif ($transactionType == "Withdrawal"
              && $amount >= 500) {
        return 5;
    } else {
        return 0;
    }
}


function calculateInterest($accountType, $finalBalance, $duration) {
    if ($accountType == "Savings") {
        $rate = 0.05;
    } elseif ($accountType == "Current") {
        $rate = 0.02;
    } elseif ($accountType == "Business") {
        $rate = 0.03;
    } else {
        $rate = 0;
    }

    return $finalBalance * $rate * $duration;
}


function calculateTotalCharges($transactionFee) {
    return $transactionFee;
}


function calculateFinalBalance(
    $openingBalance,
    $transactionType,
    $amount,
    $totalCharges
) {
    if ($transactionType == "Deposit") {
        return calculateDeposit($openingBalance, $amount)
               - $totalCharges;
    } else {
        return calculateWithdrawal($openingBalance, $amount)
               - $totalCharges;
    }
}


function checkAccountStatus($finalBalance) {
    if ($finalBalance >= 5000) {
        return "High Balance";
    } elseif ($finalBalance >= 1000
              && $finalBalance < 5000) {
        return "Medium Balance";
    } else {
        return "Low Balance";
    }
}


function calculateFutureBalance(
    $finalBalance,
    $interest,
    $duration = 1
) {
    return $finalBalance + $interest;
}

?>
