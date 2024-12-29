<?php
session_start();
require_once '../common/db_conn.php';

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
} catch (PDOException $e) {
    echo "系統錯誤，請稍後再試";
}

function maskIdCard($idCard) {
    return substr_replace($idCard, '****', 3, 4);
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>醫師管理系統</title>
    <link rel="stylesheet" href="./asset/doctor_dashboard.css">  <!-- 引入外部 CSS 檔案 -->
</head>

<body>
    <div class="welcome-section">
        <h2>歡迎醫師 <?php echo htmlspecialchars($doctor['last_name'] . $doctor['first_name']); ?></h2>
        <p>身分證字號：<?php echo htmlspecialchars(maskIdCard($doctor['id_card'])); ?></p>
    </div>

    <div class="menu-grid">
        <a href="../doctor/clinic_create.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/schedule.png" alt="新增門診">
                </div>
                <div class="menu-title">新增門診</div>
                <div class="menu-description">開設一個新的門診</div>
            </div>
        </a>

        <a href="../doctor/clinic_list.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/document.png" alt="門診清單">
                </div>
                <div class="menu-title">門診清單</div>
                <div class="menu-description">查看您的門診清單</div>
            </div>
        </a>
    </div>

    <form action="../common/logout.php" method="post">
        <button type="submit" class="logout-btn">登出</button>
    </form>
</body>

</html>