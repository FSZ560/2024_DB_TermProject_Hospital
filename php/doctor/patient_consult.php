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

    // 查詢該診次所有預約的病人
    $stmt = $db->prepare("
        SELECT a.appointment_id, a.sequence_number, 
               p.person_id, p.last_name, p.first_name, p.gender, p.birthday,
               pt.height, pt.weight
        FROM appointment a
        JOIN patient pt ON a.patient_id = pt.person_id
        JOIN person p ON pt.person_id = p.person_id
        WHERE a.clinic_id = ?
        ORDER BY a.sequence_number
    ");
    $stmt->execute([$clinic_id]);
    $patients = $stmt->fetchAll();

    // 處理病人診療狀態的更新
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db->beginTransaction();
        
        try {
            $appointment_id = $_POST['appointment_id'];
            $action = $_POST['action'];
            $patient_id = $_POST['patient_id'];

            if ($action === 'treat') {
                // 生成 record_id
                $stmt = $db->prepare("SELECT record_id FROM medical_record ORDER BY record_id DESC LIMIT 1");
                $stmt->execute();
                $last_id = $stmt->fetch();
                $new_record_id = $last_id ? 
                    'RE' . str_pad(intval(substr($last_id['record_id'], 2)) + 1, 5, '0', STR_PAD_LEFT) : 
                    'RE00001';

                // 新增醫療紀錄
                $stmt = $db->prepare("
                    INSERT INTO medical_record (record_id, patient_id, clinic_id) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$new_record_id, $patient_id, $clinic_id]);
            }

            // 刪除預約紀錄
            $stmt = $db->prepare("DELETE FROM appointment WHERE appointment_id = ?");
            $stmt->execute([$appointment_id]);

            $db->commit();
            header("Location: patient_consult.php?clinic_id=" . urlencode($clinic_id) . "&success=1");
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            $error_message = "處理失敗，請稍後再試";
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
    <title>看診名單</title>
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
            cursor: pointer;
            margin: 0 5px;
        }

        .treat-btn {
            background-color: #4CAF50;
        }

        .reject-btn {
            background-color: #f44336;
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

        .success-message {
            color: green;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
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
                <h1>待看診名單</h1>
                <h2>
                    <?php 
                    echo htmlspecialchars($clinic['department_name']) . ' - ' . 
                         htmlspecialchars($clinic['clinic_date']) . ' ' . 
                         htmlspecialchars(getPeriodText($clinic['period'])); 
                    ?>
                </h2>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">處理成功</div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (empty($patients)): ?>
                <p>目前沒有待看診病人</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>看診序號</th>
                            <th>姓名</th>
                            <th>性別</th>
                            <th>年齡</th>
                            <th>身高</th>
                            <th>體重</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['sequence_number']); ?></td>
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
                                <td>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($patient['appointment_id']); ?>">
                                        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient['person_id']); ?>">
                                        
                                        <button type="submit" name="action" value="treat" class="action-btn treat-btn">
                                            需進行醫療協助
                                        </button>
                                        
                                        <button type="submit" name="action" value="reject" class="action-btn reject-btn">
                                            退回，無須醫療協助
                                        </button>
                                    </form>
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
