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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./asset/clinic_list.css">  <!-- 引入外部 CSS 檔案 -->
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th data-sort="clinic_date">門診日期</th>
                        <th data-sort="period">時段</th>
                        <th data-sort="department_name">科別</th>
                        <th data-sort="location">看診地點</th>
                        <th data-sort="appointment_count">待看診人數</th>
                        <th data-sort="treated_count">已看診人數</th>
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
                                <a href="patient_consult.php?clinic_id=<?php echo urlencode($clinic['clinic_id']); ?>" class="action-btn consult-btn">進行看診</a>
                                <a href="patient_list.php?clinic_id=<?php echo urlencode($clinic['clinic_id']); ?>" class="action-btn list-btn">已看病人清單</a>
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
    <script src="./script/clinic_list.js"></script>  <!-- 引入外部 JavaScript 檔案 -->
</body>
</html>
