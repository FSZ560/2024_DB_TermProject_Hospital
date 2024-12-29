<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient' || !isset($_GET['record_id'])) {
    header("Location: patient_dashboard.php");
    exit();
}

$record_id = $_GET['record_id'];

try {
    // 確認這個病歷記錄屬於當前登入的病患
    $stmt = $db->prepare("
        SELECT mr.record_id, mr.clinic_id, c.clinic_date, c.period,
               p.last_name, p.first_name,
               d.department_name,
               CONCAT(dp.last_name, dp.first_name) as doctor_name
        FROM medical_record mr
        JOIN clinic c ON mr.clinic_id = c.clinic_id
        JOIN department d ON c.department_id = d.department_id
        JOIN staff s ON c.doctor_id = s.person_id
        JOIN person dp ON s.person_id = dp.person_id
        JOIN person p ON mr.patient_id = p.person_id
        WHERE mr.record_id = ? AND mr.patient_id = ?
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
    <link rel="stylesheet" href="asset/pt_certificate_list.css">
</head>
<body>
    <div class="container">
        <?php if (isset($record)): ?>
            <div class="record-info">
                <h1>診斷書清單</h1>
                <h2>就診資訊</h2>
                <p>
                    看診時間：
                    <?php 
                    echo htmlspecialchars($record['clinic_date']) . ' ' . 
                         htmlspecialchars(getPeriodText($record['period']));
                    ?>
                </p>
                <p>科別：<?php echo htmlspecialchars($record['department_name']); ?></p>
                <p>醫師：<?php echo htmlspecialchars($record['doctor_name']); ?></p>
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

            <a href="history.php" class="back-btn">返回看診紀錄</a>
        <?php else: ?>
            <div class="error-message">無效的病歷記錄</div>
            <a href="history.php" class="back-btn">返回看診紀錄</a>
        <?php endif; ?>
    </div>
</body>
</html>
