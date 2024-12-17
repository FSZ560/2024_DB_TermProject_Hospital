<?php
session_start();
require_once 'db_conn.php';

// 驗證使用者是否登入且為病人
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient_login.php");
    exit();
}

// 獲取病人資訊
try {
    $stmt = $db->prepare("
        SELECT id_card, last_name, first_name 
        FROM person 
        WHERE person_id = ? 
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$patient) {
        echo "找不到使用者資訊。";
        exit();
    }
} catch (PDOException $e) {
    die("系統錯誤，請稍後再試：" . $e->getMessage());
}

// 分頁參數
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// 查詢掛號記錄
$appointments = [];
try {
    $stmt = $db->prepare("
        SELECT * 
        FROM appointment 
        WHERE patient_id = ? 
        ORDER BY register_time DESC 
        LIMIT ?, ?
    ");
    $stmt->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $start_from, PDO::PARAM_INT);
    $stmt->bindValue(3, $records_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("無法獲取掛號記錄：" . $e->getMessage());
}

// 計算總記錄數
$total_records = 0;
$total_pages = 0;
try {
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM appointment WHERE patient_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total_records = $stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);
} catch (PDOException $e) {
    die("無法計算總記錄數：" . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掛號記錄查詢</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
            cursor: pointer;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #007bff;
        }
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }
        .pagination span.current {
            background-color: #007bff;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <h1>掛號記錄查詢</h1>
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Sequence No</th>
                <th>Clinic</th>
                <th>Patient ID</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($appointments) {
                foreach ($appointments as $appointment) {
                    echo "<tr>
                            <td>#{$appointment['appointment_id']}</td>
                            <td>{$appointment['sequence_number']}</td>
                            <td>{$appointment['clinic_id']}</td>
                            <td>{$appointment['patient_id']}</td>
                            <td>{$appointment['register_time']}</td>
                            <td>
                                <a href='detail.php?appointment_id={$appointment['appointment_id']}'>🔍</a>
                                <a href='?delete_id={$appointment['appointment_id']}' onclick='return confirm(\"確定要刪除這筆記錄嗎？\");'>🗑️</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>無掛號記錄</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- 分頁 -->
    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                echo "<span class='current'>$i</span>";
            } else {
                echo "<a href='?page=$i'>$i</a>";
            }
        }
        ?>
    </div>
</body>
</html>
