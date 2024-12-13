<?php
require_once 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 檢查身分證字號是否已被註冊
        $stmt = $db->prepare("SELECT COUNT(*) FROM person WHERE id_card = ?");
        $stmt->execute([$_POST['id_card']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("此身分證字號已被註冊");
        }

        // 接收表單資料
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $id_card = $_POST['id_card'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $birthday = $_POST['birthday'];
        $height = $_POST['height'];
        $weight = $_POST['weight'];
        $person_id = generatePersonId();

        // insert into person table
        $stmt_person = $db->prepare("INSERT INTO person (person_id, last_name, first_name, id_card, password, phone, address, gender, birthday) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_person->execute([$person_id, $last_name, $first_name, $id_card, $password, $phone, $address, $gender, $birthday]);

        // insert into patient table
        $stmt_patient = $db->prepare("INSERT INTO patient (person_id, height, weight) VALUES (?, ?, ?)");
        $stmt_patient->execute([$person_id, $height, $weight]);

        // success
        echo "<script>
            alert('註冊成功');
            window.location.href = 'patient_login.php';
        </script>";

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

function generatePersonId() {
    global $db;
    $stmt = $db->prepare("SELECT person_id FROM person ORDER BY person_id DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row) {
        $lastIdNum = intval(substr($row['person_id'], 2)) + 1;
        $newId = 'PE' . str_pad($lastIdNum, 5, '0', STR_PAD_LEFT);
    } else {
        $newId = 'PE00001';
    }
    return $newId;
}

?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>病患註冊</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .back-btn {
            background-color: #666;
            color: white;
            margin-right: 10px;
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
    <h1>病患註冊</h1>
    
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="last_name">姓氏：</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="first_name">名字：</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="id_card">身分證字號：</label>
            <input type="text" id="id_card" name="id_card" required>
        </div>

        <div class="form-group">
            <label for="password">密碼：</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="phone">電話：</label>
            <input type="text" id="phone" name="phone">
        </div>

        <div class="form-group">
            <label for="address">地址：</label>
            <input type="text" id="address" name="address">
        </div>

        <div class="form-group">
            <label for="gender">性別：</label>
            <select id="gender" name="gender">
                <option value="M">男</option>
                <option value="F">女</option>
            </select>
        </div>

        <div class="form-group">
            <label for="birthday">生日：</label>
            <input type="date" id="birthday" name="birthday">
        </div>

        <div class="form-group">
            <label for="height">身高（cm）：</label>
            <input type="number" id="height" name="height" step="0.01">
        </div>

        <div class="form-group">
            <label for="weight">體重（kg）：</label>
            <input type="number" id="weight" name="weight" step="0.01">
        </div>

        <div class="button-group">
            <button type="button" class="back-btn" onclick="window.location.href='patient_login.php'">返回登入</button>
            <button type="submit" class="submit-btn">註冊</button>
        </div>
    </form>
</body>
</html>
