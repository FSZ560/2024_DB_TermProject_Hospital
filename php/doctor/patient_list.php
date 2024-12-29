<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor' || !isset($_GET['clinic_id'])) {
    header("Location: clinic_list.php");
    exit();
}

$clinic_id = $_GET['clinic_id'];

try {
    // 檢查這個診次是否屬於當前醫師
    $stmt = $db->prepare("
        SELECT clinic_date, period, department_name 
        FROM clinic c 
        JOIN department d ON c.department_id = d.department_id 
        WHERE clinic_id = ? AND doctor_id = ?
    ");
    $stmt->execute([$clinic_id, $_SESSION['user_id']]);
    $clinic = $stmt->fetch();

    if (!$clinic) {
        throw new Exception("無效的診次");
    }

    // 查詢該診次所有已看診的病人
    $stmt = $db->prepare("
        SELECT mr.record_id, p.person_id, p.last_name, p.first_name, 
               p.gender, p.birthday, pt.height, pt.weight,
               (SELECT COUNT(*) FROM medical_certificate mc WHERE mc.record_id = mr.record_id) as certificate_count
        FROM medical_record mr
        JOIN patient pt ON mr.patient_id = pt.person_id
        JOIN person p ON pt.person_id = p.person_id
        WHERE mr.clinic_id = ?
        ORDER BY mr.record_id
    ");
    $stmt->execute([$clinic_id]);
    $patients = $stmt->fetchAll();

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
    <title>已看診病人清單</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./asset/patient_list.css">  <!-- 引入外部 CSS 檔案 -->
</head>
<body>
    <div class="container">
        <?php if (isset($clinic)): ?>
            <div class="clinic-info">
                <h1>已看診病人清單</h1>
                <h2>
                    <?php 
                    echo htmlspecialchars($clinic['department_name']) . ' - ' . 
                         htmlspecialchars($clinic['clinic_date']) . ' ' . 
                         htmlspecialchars(getPeriodText($clinic['period'])); 
                    ?>
                </h2>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (empty($patients)): ?>
                <p>目前沒有已看診病人</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>病歷號</th>
                            <th>姓名</th>
                            <th>性別</th>
                            <th>年齡</th>
                            <th>身高</th>
                            <th>體重</th>
                            <th>診斷書數量</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['record_id']); ?></td>
                                <td><?php echo htmlspecialchars($patient['last_name'] . $patient['first_name']); ?></td>
                                <td>
                                    <?php
                                    if ($patient['gender'] === NULL) {
                                        echo '-';
                                    } else if ($patient['gender'] === 'M') {
                                        echo '男';
                                    } else {
                                        echo '女';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $birthDate = new DateTime($patient['birthday']);
                                    if ($patient['birthday']  === NULL) {
                                        echo '-';
                                    } else {
                                        $today = new DateTime();
                                        $age = $birthDate->diff($today)->y;
                                        echo $age . '歲';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $patient['height'] ? htmlspecialchars($patient['height']) . ' cm' : '-'; ?></td>
                                <td><?php echo $patient['weight'] ? htmlspecialchars($patient['weight']) . ' kg' : '-'; ?></td>
                                <td><?php echo htmlspecialchars($patient['certificate_count']); ?></td>
                                <td>
                                    <a href="add_certificate.php?record_id=<?php echo urlencode($patient['record_id']); ?>" 
                                       class="action-btn add-btn">新增診斷書</a>
                                    <a href="dr_certificate_list.php?record_id=<?php echo urlencode($patient['record_id']); ?>" 
                                       class="action-btn view-btn">查看診斷書清單</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php else: ?>
            <div class="error-message">無效的診次資訊</div>
        <?php endif; ?>

        <a href="clinic_list.php" class="back-btn">返回診次列表</a>
    </div>
    <script src="./script/patient_list.js"></script>  <!-- 引入外部 JavaScript 檔案 -->
</body>
</html>
