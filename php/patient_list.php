<?php
session_start();
require_once 'db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: doctor_login.php");
    exit();
}

try {
    $stmt = $db->prepare("
        SELECT id_card, last_name, first_name 
        FROM person 
        WHERE person_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $doctor = $stmt->fetch();

    if ($doctor) {
        echo "<h2>歡迎醫師 {$doctor['last_name']}{$doctor['first_name']}</h2>";
        echo "<p>身分證字號：{$doctor['id_card']}</p>";
    } else {
        echo "無法取得醫師資料";
    }
} catch (PDOException $e) {
    echo "系統錯誤，請稍後再試";
}
?>

<form action="logout.php" method="post">
    <button type="submit">登出</button>
</form>