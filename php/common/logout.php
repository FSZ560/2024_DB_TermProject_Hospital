<?php
session_start();
$userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';
session_unset();
session_destroy();

if ($userType === 'doctor') {
    header("Location: doctor_login.php");
} else if ($userType === 'patient') {
    header("Location: patient_login.php");
} else {
    header("Location: patient_login.php");
}
exit();
?>
