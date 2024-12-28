<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: doctor_login.php");
    exit();
}

try {
    $stmt = $db->prepare("
        SELECT c.clinic_id, c.clinic_date, c.period, c.location, 
               d.department_name, 
               COUNT(DISTINCT a.appointment_id) as appointment_count,
               COUNT(DISTINCT mr.record_id) as treated_count
        FROM clinic c
        JOIN department d ON c.department_id = d.department_id
        LEFT JOIN appointment a ON c.clinic_id = a.clinic_id
        LEFT JOIN medical_record mr ON c.clinic_id = mr.clinic_id
        WHERE c.doctor_id = ?
        GROUP BY c.clinic_id
        ORDER BY c.clinic_date DESC, 
                 FIELD(c.period, 'morning', 'afternoon', 'evening')
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $clinics = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "系統錯誤，請稍後再試";
}

function getPeriodText($period) {
    switch ($period) {
        case 'morning':
            return '上午';
        case 'afternoon':
            return '下午';
        case 'evening':
            return '晚上';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>門診清單</title>
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

        h1 {
            color: #ff9900;
            margin-bottom: 20px;
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

        th {
            background-color: #ff9900;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .button-group {
            margin-top: 20px;
        }

        .back-btn {
            padding: 10px 20px;
            background-color: #666;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .error {
            color: red;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            margin: 0 5px;
            display: inline-block;
            font-size: 14px;
        }

        .consult-btn {
            background-color: #4CAF50;
        }

        .consult-btn:hover {
            background-color: #45a049;
        }

        .list-btn {
            background-color: #2196F3;
        }

        .list-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>門診清單</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (empty($clinics)): ?>
            <div class="empty-message">目前沒有門診記錄</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>門診日期</th>
                        <th>時段</th>
                        <th>科別</th>
                        <th>看診地點</th>
                        <th>待看診人數</th>
                        <th>已看診人數</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinics as $clinic): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clinic['clinic_date']); ?></td>
                            <td><?php echo htmlspecialchars(getPeriodText($clinic['period'])); ?></td>
                            <td><?php echo htmlspecialchars($clinic['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['location']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['appointment_count']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['treated_count']); ?></td>
                            <td>
                                <a href="patient_consult.php?clinic_id=<?php echo urlencode($clinic['clinic_id']); ?>" 
                                   class="action-btn consult-btn">進行看診</a>
                                <a href="patient_list.php?clinic_id=<?php echo urlencode($clinic['clinic_id']); ?>" 
                                   class="action-btn list-btn">已看病人清單</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="button-group">
            <a href="doctor_dashboard.php" class="back-btn">返回</a>
        </div>
    </div>
</body>
</html>
