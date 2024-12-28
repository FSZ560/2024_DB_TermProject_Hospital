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
        }

        .clinic-info {
            background-color: #fff3e0;
            padding: 15px;
            border-radius: 4px;
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

        .add-btn {
            background-color: #4CAF50;
        }

        .add-btn:hover {
            background-color: #45a049;
        }

        .view-btn {
            background-color: #2196F3;
        }

        .view-btn:hover {
            background-color: #1976D2;
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
    </style>
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
                <table>
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
                                <td><?php echo $patient['gender'] === 'M' ? '男' : '女'; ?></td>
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
</body>
</html>
