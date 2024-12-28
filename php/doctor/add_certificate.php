<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor' || !isset($_GET['record_id'])) {
    header("Location: clinic_list.php");
    exit();
}

$record_id = $_GET['record_id'];

try {
    // 取得病歷及病患資訊
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

    // 處理表單提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescription'])) {
        $db->beginTransaction();
        
        try {
            // 生成 certificate_id
            $stmt = $db->prepare("SELECT certificate_id FROM medical_certificate ORDER BY certificate_id DESC LIMIT 1");
            $stmt->execute();
            $last_id = $stmt->fetch();
            $new_id = $last_id ? 
                'CE' . str_pad(intval(substr($last_id['certificate_id'], 2)) + 1, 5, '0', STR_PAD_LEFT) : 
                'CE00001';

            // 新增診斷書
            $stmt = $db->prepare("
                INSERT INTO medical_certificate (certificate_id, record_id, prescription) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$new_id, $record_id, $_POST['prescription']]);

            $db->commit();
            echo "<script>
                alert('診斷書新增成功');
                window.location.href = 'dr_certificate_list.php?record_id=" . urlencode($record_id) . "';
            </script>";
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            $error_message = "新增診斷書失敗，請稍後再試";
        }
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
    <title>新增診斷書</title>
    <link rel="stylesheet" href="./asset/add_certificate.css">  <!-- 引入外部 CSS 檔案 -->
</head>
<body>
    <div class="container">
        <?php if (isset($record)): ?>
            <div class="record-info">
                <h1>新增診斷書</h1>
                <h2>病患資訊</h2>
                <p>姓名：<?php echo htmlspecialchars($record['last_name'] . $record['first_name']); ?></p>
                <p>性別：<?php echo $record['gender'] === NULL ? '-' : ($record['gender'] === 'M' ? '男' : '女'); ?></p>
                <p>出生日期：<?php echo htmlspecialchars($record['birthday']); ?></p>
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

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="prescription">診斷內容：</label>
                    <textarea id="prescription" name="prescription" required 
                              placeholder="請輸入診斷內容..."></textarea>
                </div>

                <div class="button-group">
                    <a href="patient_list.php?clinic_id=<?php echo $record['clinic_id']; ?>" 
                       class="button back-btn">返回已看診病人清單</a>
                    <button type="submit" class="button submit-btn">新增診斷書</button>
                </div>
            </form>
        <?php else: ?>
            <div class="error-message">無效的病歷記錄</div>
            <div class="button-group">
                <a href="clinic_list.php" class="button back-btn">返回診次列表</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
