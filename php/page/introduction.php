<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>醫院介紹</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            flex-direction: column; /* 使項目垂直排列 */
            justify-content: flex-start;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hospital-info {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-top: 20px;
        }

        .back-btn {
            padding: 10px 20px;
            background-color: #666;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #45a049;
        }
    </style>

    <style>
        .img_container {
            text-align: center; /* 將圖片水平置中 */
        }
        img {
            max-width: 100%; /* 確保圖片不會超出容器範圍 */
            height: auto; /* 保持圖片的比例 */
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- 標題和返回按鈕放在 header 中 -->
        <div class="header">
            <h1>醫院介紹</h1>
            <a href="../main/mainpage.php" class="back-btn">返回主頁</a>
        </div>

        <!-- 下面是醫院介紹的文字內容 -->
        <p class="hospital-info">
            本醫院成立於 XXXX 年，致力於提供專業、優質的醫療服務。我們擁有先進的醫療設備和經驗豐富的醫療團隊，為病患提供全方位的醫療照護。我們的宗旨是關愛每一位病患，並以人為本，提供安全、有效的治療方案。
        </p>
        <p class="hospital-info">
            醫院設有多個專科診所，包括內科、外科、婦科、牙科等，並且設有綜合急診室，隨時為病患提供急救服務。無論是門診還是住院，我們都會全心全意為您提供最好的醫療服務。
        </p>
        <p class="hospital-info">
            我們的醫療團隊由專業醫師、護理人員及後勤支援團隊組成，所有人員都經過嚴格的專業訓練，並持續進行專業教育和提升，確保能夠提供最先進的醫療服務。
        </p>

        <div class="img_container">
            <img src="../../resource/prettty_picture.jpg" alt="推輪椅圖" />
        </div>
    </div>

    
</body>

</html>
