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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .welcome-section {
            background: linear-gradient(to right, #4CAF50, #45a049);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .menu-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-link {
            text-decoration: none;
            color: inherit;
        }

        .menu-icon {
            margin-bottom: 15px;
        }

        .menu-icon img {
            width: 192px;
            height: 192px;
            object-fit: contain;
        }

        .menu-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .menu-description {
            color: #666;
            font-size: 14px;
        }

        .logout-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #ff5252;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #ff1744;
        }
    </style>
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

        <a href="../patient/history.php" class="menu-link">
            <div class="menu-item">
                <div class="menu-icon">
                    <img src="../../resource/document.png" alt="掛號歷史">
                </div>
                <div class="menu-title">掛號紀錄</div>
                <div class="menu-description">查看或取消您的目前的掛號記錄，</div>
            </div>
        </a>

        <a href="../patient/open_time.php" class="menu-link">
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
                    <img src="../../resource/TBC.png" alt="過去就診紀錄">
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