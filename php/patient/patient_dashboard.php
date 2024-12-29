<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient_login.php");
    exit();
}

try {
    $stmt = $db->prepare("
        SELECT id_card, last_name, first_name 
        FROM person 
        WHERE person_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $patient = $stmt->fetch();
} catch (PDOException $e) {
    echo "系統錯誤，請稍後再試";
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>病患管理系統</title>
    <link rel="stylesheet" href="asset/patient_dashboard.css">
</head>

<body>
    <div class="welcome-section">
        <h2>歡迎 <?php echo htmlspecialchars($patient['last_name'] . $patient['first_name']); ?></h2>
        <p>身分證字號：<?php echo htmlspecialchars($patient['id_card']); ?></p>
    </div>

    <div class="menu-grid">
        <a href="../patient/personal_data.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/user_list.png" alt="個人資料">
                </div>
                <div class="menu-title">個人資料</div>
                <div class="menu-description">查看及修改您的個人資料</div>
            </div>
        </a>

        <a href="../patient/appointment_manage.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/document.png" alt="掛號歷史">
                </div>
                <div class="menu-title">掛號紀錄管理</div>
                <div class="menu-description">查看或取消您的目前的掛號記錄，</div>
            </div>
        </a>

        <a href="../patient/appointment_book.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/schedule.png" alt="門診掛號">
                </div>
                <div class="menu-title">門診掛號</div>
                <div class="menu-description">查看門診時間並進行掛號</div>
            </div>
        </a>

        <a href="../patient/history.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/medical_document.png" alt="過去就診紀錄">
                </div>
                <div class="menu-title">過去就診紀錄</div>
                <div class="menu-description">查看過去的就醫紀錄</div>
            </div>
        </a>
    </div>

    <form action="../common/logout.php" method="post">
        <button type="submit" class="logout-btn">登出</button>
    </form>
</body>

</html>