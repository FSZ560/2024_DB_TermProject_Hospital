<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['user_id'];

try {
    // 查詢病患的掛號資訊
    $stmt = $db->prepare("
        SELECT a.appointment_id, a.sequence_number, a.register_time, 
               c.clinic_date, c.period, d.department_name, c.location
        FROM appointment a
        JOIN clinic c ON a.clinic_id = c.clinic_id
        JOIN department d ON c.department_id = d.department_id
        WHERE a.patient_id = ?
        ORDER BY c.clinic_date, c.period
    ");
    $stmt->execute([$patient_id]);
    $appointments = $stmt->fetchAll();

    // 處理刪除請求
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
        $delete_stmt = $db->prepare("DELETE FROM appointment WHERE appointment_id = ? AND patient_id = ?");
        $delete_stmt->execute([$_POST['appointment_id'], $patient_id]);
        header("Location: appointment_manage.php");
        exit();
    }

} catch (Exception $e) {
    $error_message = $e->getMessage();
}

function getPeriodText($period) {
    switch ($period) {
        case 'morning': return '上午';
        case 'afternoon': return '下午';
        case 'evening': return '晚上';
        default: return '';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>掛號管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        thead {
            background: linear-gradient(to right, #4CAF50, #45a049);
        }

        th {
            color: white;
        }

        .delete-btn {
            padding: 6px 12px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }

        .error-message {
            color: red;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>掛號管理</h1>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (empty($appointments)): ?>
            <p>目前沒有掛號記錄。</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>掛號編號</th>
                        <th>科別</th>
                        <th>日期</th>
                        <th>時段</th>
                        <th>地點</th>
                        <th>掛號時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['clinic_date']); ?></td>
                            <td><?php echo htmlspecialchars(getPeriodText($appointment['period'])); ?></td>
                            <td><?php echo htmlspecialchars($appointment['location'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($appointment['register_time']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['appointment_id']); ?>">
                                    <button type="submit" class="delete-btn">取消掛號</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="dashboard.php" class="back-btn">返回主頁</a>
    </div>
</body>
</html>
