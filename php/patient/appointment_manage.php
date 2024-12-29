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
    <link rel="stylesheet" href="asset/appointment_manage.css">
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

        <a href="patient_dashboard.php" class="back-btn">返回</a>
    </div>
</body>
</html>
