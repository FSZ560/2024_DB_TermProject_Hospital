<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor' || !isset($_GET['record_id'])) {
    header("Location: clinic_list.php");
    exit();
}

$record_id = $_GET['record_id'];

try {
    $stmt = $db->prepare("
        SELECT mr.record_id, mr.clinic_id, c.clinic_date, c.period,
               p.last_name, p.first_name, p.gender, p.birthday,
               d.department_name
        FROM medical_record mr
        JOIN clinic c ON mr.clinic_id = c.clinic_id
        JOIN patient pt ON mr.patient_id = pt.person_id
        JOIN person p ON pt.person_id = p.person_id
        JOIN department d ON c.department_id = d.department_id
        WHERE mr.record_id = ? AND c.doctor_id = ?
    ");
    $stmt->execute([$record_id, $_SESSION['user_id']]);
    $record = $stmt->fetch();

    if (!$record) {
        throw new Exception("無效的病歷記錄");
    }

    // 查詢該病歷的所有診斷書
    $stmt = $db->prepare("
        SELECT certificate_id, prescription
        FROM medical_certificate
        WHERE record_id = ?
        ORDER BY certificate_id
    ");
    $stmt->execute([$record_id]);
    $certificates = $stmt->fetchAll();

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
    <title>診斷書清單</title>
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
            color: #ff9900;
            margin-bottom: 20px;
        }

        .record-info {
            background-color: #fff3e0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .patient-info {
            margin-bottom: 10px;
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

        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
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

        .prescription-content {
            white-space: pre-wrap;
            font-family: 'Consolas', monospace;
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            color: #212529;
            font-size: 14px;
            line-height: 1.6;
            margin: 10px 0;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            max-height: 300px;
            overflow-y: auto;
        }
        
        .prescription-content:hover {
            background-color: #fff;
            border-color: #ff9900;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($record)): ?>
            <div class="record-info">
                <h1>診斷書清單</h1>
                <div class="patient-info">
                    <h2>病患資訊</h2>
                    <p>姓名：<?php echo htmlspecialchars($record['last_name'] . $record['first_name']); ?></p>
                    <p>性別：<?php echo $record['gender'] === NULL ? '-' : ($record['gender'] === 'M' ? '男' : '女'); ?></p>
                    <p>出生日期：<?php echo htmlspecialchars($record['birthday']); ?></p>
                </div>
                <div class="visit-info">
                    <h2>就診資訊</h2>
                    <p>病歷號：<?php echo htmlspecialchars($record['record_id']); ?></p>
                    <p>診別：<?php echo htmlspecialchars($record['department_name']); ?></p>
                    <p>
                        看診時間：
                        <?php 
                        echo htmlspecialchars($record['clinic_date']) . ' ' . 
                             htmlspecialchars(getPeriodText($record['period']));
                        ?>
                    </p>
                </div>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (empty($certificates)): ?>
                <div class="empty-message">目前沒有診斷書記錄</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>診斷書編號</th>
                            <th>診斷內容</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cert['certificate_id']); ?></td>
                                <td>
                                    <div class="prescription-content"><?php echo htmlspecialchars(trim($cert['prescription'])); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <a href="patient_list.php?clinic_id=<?php echo urlencode($record['clinic_id']); ?>" 
               class="back-btn">返回病人清單</a>
        <?php else: ?>
            <div class="error-message">無效的病歷記錄</div>
            <a href="clinic_list.php" class="back-btn">返回診次列表</a>
        <?php endif; ?>
    </div>
</body>
</html>