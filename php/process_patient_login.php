<?php
require_once 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_card = $_POST['id'];
    $password = $_POST['password'];
    
    try {
        $stmt = $db->prepare("
            SELECT p.person_id, p.password 
            FROM person p 
            JOIN patient pt ON p.person_id = pt.person_id 
            WHERE p.id_card = ?
        ");
        $stmt->execute([$id_card]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['person_id'];
            $_SESSION['user_type'] = 'patient';
            header("Location: intersection.php");
            exit();
        } else {
            header("Location: patient_login.php?error=invalid");
            exit();
        }
    } catch(PDOException $e) {
        header("Location: patient_login.php?error=system");
        exit();
    }
}
?>
