<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: patient_login.php");
    exit();
}

try {
    // 查詢個人資料
    $stmt = $db->prepare("SELECT last_name, first_name, phone, address, gender, birthday, height, weight FROM person LEFT JOIN patient ON person.person_id = patient.person_id WHERE person.person_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $patient = $stmt->fetch();

    if (!$patient) {
        throw new Exception("無法獲取個人資料");
    }

    // 處理表單提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 更新資料庫
        $stmt = $db->prepare("UPDATE person SET phone = ?, address = ?, gender = ?, birthday = ? WHERE person_id = ?");
        $stmt->execute([
            $_POST['phone'],
            $_POST['address'],
            $_POST['gender'],
            $_POST['birthday'],
            $_SESSION['user_id']
        ]);

        $stmt = $db->prepare("UPDATE patient SET height = ?, weight = ? WHERE person_id = ?");
        $stmt->execute([
            $_POST['height'],
            $_POST['weight'],
            $_SESSION['user_id']
        ]);

        echo "<script>
            alert('個人資料更新成功');
            window.location.href = 'patient_dashboard.php';
        </script>";
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>更新個人資料</title>
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
            color: #4CAF50;
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
            background-color: #4CAF50;
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
        <h1>更新個人資料</h1>

        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="last_name">姓氏：</label>
                <input type="text" id="last_name" value="<?php echo htmlspecialchars($patient['last_name']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="first_name">名字：</label>
                <input type="text" id="first_name" value="<?php echo htmlspecialchars($patient['first_name']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="phone">電話：</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">地址：</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($patient['address']); ?>">
            </div>

            <div class="form-group">
                <label for="gender">性別：</label>
                <select id="gender" name="gender">
                    <option value="M" <?php echo $patient['gender'] === 'M' ? 'selected' : ''; ?>>男性</option>
                    <option value="F" <?php echo $patient['gender'] === 'F' ? 'selected' : ''; ?>>女性</option>
                </select>
            </div>

            <div class="form-group">
                <label for="birthday">生日：</label>
                <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($patient['birthday']); ?>">
            </div>

            <div class="form-group">
                <label for="height">身高 (cm)：</label>
                <input type="number" step="0.01" id="height" name="height" value="<?php echo htmlspecialchars($patient['height']); ?>">
            </div>

            <div class="form-group">
                <label for="weight">體重 (kg)：</label>
                <input type="number" step="0.01" id="weight" name="weight" value="<?php echo htmlspecialchars($patient['weight']); ?>">
            </div>

            <div class="button-group">
                <button type="button" class="back-btn" onclick="window.location.href='patient_dashboard.php'">返回</button>
                <button type="submit" class="submit-btn">更新資料</button>
            </div>
        </form>
    </div>
</body>
</html>