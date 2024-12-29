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
        SELECT *
        FROM v_medical_record_detail
        WHERE record_id = ? AND doctor_id = ?
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./asset/dr_certificate_list.css">  <!-- 引入外部 CSS 檔案 -->
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
                <table class="table table-striped">
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
    <script src="./script/dr_certificate_list.js"></script>  <!-- 引入外部 JavaScript 檔案 -->
</body>
</html>
