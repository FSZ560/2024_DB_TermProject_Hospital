<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>病患登入系統</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
        }

        .login-container {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .login-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }

        .login-container input {
            width: 90%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        .login-container button.register-btn {
            margin-top: 10px;
            background-color: #2196F3;
        }

        .login-container button.register-btn:hover {
            background-color: #1e87dc;
        }

        .login-container button.home-btn {
            background-color: #666;
        }

        .login-container button.home-btn:hover {
            background-color: #555;
        }

        .error-message {
            color: #ff0000;
            background-color: #ffe6e6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>病患登入</h1>

        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error-message">';
            switch ($_GET['error']) {
                case 'invalid':
                    echo '身分證字號或密碼錯誤';
                    break;
                case 'system':
                    echo '系統錯誤，請稍後再試';
                    break;
            }
            echo '</div>';
        }
        ?>

        <form action="process_patient_login.php" method="post">
            <label for="id">身分證字號:</label>
            <input type="text" id="id" name="id" required>

            <label for="password">密碼:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">登入</button>
        </form>

        <form action="firstcome.php">
            <button type="submit" class="register-btn" style="margin-bottom: 10px;">註冊個人資料</button>
        </form>
        
        <form action="mainpage.php">
            <button type="submit" class="home-btn">返回首頁</button>
        </form>
    </div>
</body>

</html>
