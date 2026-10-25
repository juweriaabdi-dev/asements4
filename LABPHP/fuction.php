<?php
function calculateTotal($php, $database, $networking)
{
    return $php + $database + $networking;
}
function calculateAverage($total)
{
    return $total / 3;
}
function calculateGrade($average)
{
    if ($average >= 90) {
        return "A";
    } elseif ($average >= 80) {
        return "B";
    } elseif ($average >= 70) {
        return "C";
    } elseif ($average >= 60) {
        
        return "D";
    } else {
        return "F";
    }
}
function checkResult($average)
{
    if ($average >= 60) {
        return "Pass";
    } else {
        return "Fail";
    }
}
?>