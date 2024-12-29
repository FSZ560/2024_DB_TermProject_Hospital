<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient_login.php");
    exit();
}

// 處理掛號請求
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clinic_id'])) {
    try {
        // 檢查是否已經掛過這個診
        $check_stmt = $db->prepare("SELECT COUNT(*) FROM appointment WHERE clinic_id = ? AND patient_id = ?");
        $check_stmt->execute([$_POST['clinic_id'], $_SESSION['user_id']]);
        if ($check_stmt->fetchColumn() > 0) {
            $error_message = "您已經掛過這個門診了";
        } else {
            // 取得這個診的目前掛號序號
            $seq_stmt = $db->prepare("
                SELECT COALESCE(MAX(sequence_number), 0) + 1 
                FROM appointment 
                WHERE clinic_id = ?
            ");
            $seq_stmt->execute([$_POST['clinic_id']]);
            $next_sequence = $seq_stmt->fetchColumn();

            // 產生新的 appointment_id
            $id_stmt = $db->prepare("
                SELECT COALESCE(MAX(appointment_id), 'AP00000') 
                FROM appointment
            ");
            $id_stmt->execute();
            $last_id = $id_stmt->fetchColumn();
            $next_id = 'AP' . str_pad(intval(substr($last_id, 2)) + 1, 5, '0', STR_PAD_LEFT);

            // 新增掛號記錄
            $insert_stmt = $db->prepare("
                INSERT INTO appointment (appointment_id, sequence_number, clinic_id, patient_id) 
                VALUES (?, ?, ?, ?)
            ");
            $insert_stmt->execute([$next_id, $next_sequence, $_POST['clinic_id'], $_SESSION['user_id']]);

            $success_message = "掛號成功！您的看診序號為：" . $next_sequence;
        }
    } catch (PDOException $e) {
        $error_message = "掛號失敗，請稍後再試";
    }
}

try {
    $stmt = $db->prepare("
        SELECT 
            c.clinic_id,
            c.clinic_date,
            c.period,
            c.location,
            d.department_name,
            CONCAT(p.last_name, p.first_name) as doctor_name,
            COUNT(a.appointment_id) as current_appointments
        FROM clinic c
        JOIN department d ON c.department_id = d.department_id
        JOIN staff s ON c.doctor_id = s.person_id
        JOIN person p ON s.person_id = p.person_id
        LEFT JOIN appointment a ON c.clinic_id = a.clinic_id
        GROUP BY c.clinic_id
        ORDER BY c.clinic_date ASC, 
                 FIELD(c.period, 'morning', 'afternoon', 'evening')
    ");
    $stmt->execute();
    $clinics = $stmt->fetchAll();

    // 檢查病患是否已經掛號
    $stmt_check = $db->prepare("
        SELECT clinic_id 
        FROM appointment 
        WHERE patient_id = ?
    ");
    $stmt_check->execute([$_SESSION['user_id']]);
    $existing_appointments = $stmt_check->fetchAll(PDO::FETCH_COLUMN);

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
    <title>門診掛號</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/appointment_book.css">
</head>
<body>
    <div class="container">
        <h1>門診掛號</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (empty($clinics)): ?>
            <p>目前沒有可掛號的門診</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>門診日期</th>
                        <th>時段</th>
                        <th>科別</th>
                        <th>醫師</th>
                        <th>地點</th>
                        <th>已掛號人數</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinics as $clinic): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clinic['clinic_date']); ?></td>
                            <td><?php echo htmlspecialchars(getPeriodText($clinic['period'])); ?></td>
                            <td><?php echo htmlspecialchars($clinic['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['location']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['current_appointments']); ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="clinic_id" value="<?php echo htmlspecialchars($clinic['clinic_id']); ?>">
                                    <button type="submit" class="book-btn" 
                                        <?php echo in_array($clinic['clinic_id'], $existing_appointments) ? 'disabled' : ''; ?>>
                                        <?php 
                                        if (in_array($clinic['clinic_id'], $existing_appointments)) {
                                            echo '已掛號';
                                        } else {
                                            echo '掛號';
                                        }
                                        ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="patient_dashboard.php" class="back-btn">返回</a>
    </div>

    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('確定要進行掛號嗎？')) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <script src="./script/appointment_book.js"></script>  <!-- 引入外部 JavaScript 檔案 -->
</body>
</html>
