<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: doctor_login.php");
    exit();
}

try {
    // 獲取所有科別
    $stmt = $db->prepare("SELECT department_id, department_name FROM department");
    $stmt->execute();
    $departments = $stmt->fetchAll();
    
    if (!$departments) {
        throw new Exception("無法獲取科別資料");
    }

    // 處理表單提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 生成clinic_id
        $stmt = $db->prepare("SELECT clinic_id FROM clinic ORDER BY clinic_id DESC LIMIT 1");
        $stmt->execute();
        $last_id = $stmt->fetch();
        $new_id = $last_id ? 
            'CL' . str_pad(intval(substr($last_id['clinic_id'], 2)) + 1, 5, '0', STR_PAD_LEFT) : 
            'CL00001';

        // 新增門診記錄
        $stmt = $db->prepare("
            INSERT INTO clinic (clinic_id, clinic_date, period, department_id, location, doctor_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $new_id,
            $_POST['clinic_date'],
            $_POST['period'],
            $_POST['department_id'],
            $_POST['location'],
            $_SESSION['user_id']
        ]);

        if ($result) {
            echo "<script>
                alert('門診新增成功');
                window.location.href = 'doctor_dashboard.php';
            </script>";
        }
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>新增門診</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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

        h1 {
            color: #ff9900;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn {
            background-color: #ff9900;
            color: white;
        }

        .back-btn {
            background-color: #666;
            color: white;
        }

        .error {
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
        <h1>新增門診時段</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="department_id">科別：</label>
                <select id="department_id" name="department_id" required>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept['department_id']); ?>">
                            <?php echo htmlspecialchars($dept['department_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="clinic_date">門診日期：</label>
                <input type="date" id="clinic_date" name="clinic_date" required
                       min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="period">門診時段：</label>
                <select id="period" name="period" required>
                    <option value="morning">上午</option>
                    <option value="afternoon">下午</option>
                    <option value="evening">晚上</option>
                </select>
            </div>

            <div class="form-group">
                <label for="location">看診地點：</label>
                <input type="text" id="location" name="location" required>
            </div>

            <div class="button-group">
                <button type="button" class="back-btn" onclick="window.location.href='doctor_dashboard.php'">返回</button>
                <button type="submit" class="submit-btn">新增門診</button>
            </div>
        </form>
    </div>

    <script>
        // 防止選擇過去的日期
        document.getElementById('clinic_date').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
