<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient_login.php");
    exit();
}

try {
    // 查詢病患的所有看診紀錄
    $stmt = $db->prepare("
        SELECT 
            mr.record_id,
            mr.clinic_id,
            c.clinic_date,
            c.period,
            d.department_name,
            CONCAT(p.last_name, p.first_name) as doctor_name,
            COUNT(mc.certificate_id) as certificate_count
        FROM medical_record mr
        JOIN clinic c ON mr.clinic_id = c.clinic_id
        JOIN department d ON c.department_id = d.department_id
        JOIN staff s ON c.doctor_id = s.person_id
        JOIN person p ON s.person_id = p.person_id
        LEFT JOIN medical_certificate mc ON mr.record_id = mc.record_id
        WHERE mr.patient_id = ?
        GROUP BY mr.record_id
        ORDER BY c.clinic_date DESC, 
                 FIELD(c.period, 'morning', 'afternoon', 'evening')
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $records = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = "系統錯誤，請稍後再試";
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
    <title>看診紀錄</title>
    <link rel="stylesheet" href="asset/history.css">
</head>
<body>
    <div class="container">
        <h1>看診紀錄</h1>

        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (empty($records)): ?>
            <div class="empty-message">目前沒有看診紀錄</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>看診日期</th>
                        <th>時段</th>
                        <th>科別</th>
                        <th>醫師</th>
                        <th>診斷書數量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['clinic_date']); ?></td>
                            <td><?php echo htmlspecialchars(getPeriodText($record['period'])); ?></td>
                            <td><?php echo htmlspecialchars($record['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['certificate_count']); ?></td>
                            <td>
                                <a href="pt_certificate_list.php?record_id=<?php echo urlencode($record['record_id']); ?>" 
                                   class="view-btn">查看診斷書</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="patient_dashboard.php" class="back-btn">返回</a>
    </div>
</body>
</html>
