<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XX醫院</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .header {
            background: linear-gradient(to right, #1a4f7a, #2c3e50);
            color: white;
            width: 100%;
            padding: 15px 0;
            margin-bottom: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hospital-info {
            text-align: left;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .hospital-contact {
            font-size: 14px;
            margin-top: 5px;
            color: #e0e0e0;
        }

        .nav-bar {
            background-color: #fff;
            width: 100%;
            padding: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .button-group {
            display: flex;
            gap: 15px;
        }

        .nav-button {
            padding: 8px 20px;
            font-size: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .intro-btn {
            background-color: transparent;
            color: #3498db;
            border: 2px solid #3498db;
        }

        .doctor-btn {
            background-color: transparent;
            color: #ff9900;
            border: 2px solid #ff9900;
        }

        .patient-btn {
            background-color: transparent;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }

        .intro-btn:hover {
            background-color: #3498db;
            color: white;
            transform: translateY(-2px);
        }

        .doctor-btn:hover {
            background-color: #ff9900;
            color: white;
            transform: translateY(-2px);
        }

        .patient-btn:hover {
            background-color: #4CAF50;
            color: white;
            transform: translateY(-2px);
        }

        .container {
            width: 80%;
            max-width: 800px;
            text-align: center;
        }

        .content-area {
            width: 80%;
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .main-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .news-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-date {
            color: #666;
            font-size: 0.9em;
        }

        .announcement {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        /* Popup modal */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 600px;
            animation: expand 0.1s ease-out forwards;
        }

        .news-title a {
            color: black; /* 這是藍色，可以改成你想要的顏色 */
            text-decoration: none; /* 移除下劃線 */
        }

        @keyframes expand {
            0% {
                transform: translate(-50%, -50%) scale(0);
            }
            100% {
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .popup h3 {
            margin-top: 0;
        }

        .popup p {
            margin-bottom: 0;
        }

        .popup .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            background-color: transparent;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="hospital-info">
                <h1 class="hospital-name">XX醫院</h1>
                <div class="hospital-contact">
                    總機：(02)XXXX-XXXX | 地址：XX市XX區XX路XX號
                </div>
            </div>
        </div>
    </div>

    <div class="nav-bar">
        <div class="nav-container">
            <div class="button-group">
                <a href="../page/introduction.php">
                    <button class="nav-button intro-btn">醫院簡介</button>
                </a>
                <a href="../patient/patient_login.php">
                    <button class="nav-button patient-btn">病患登入</button>
                </a>
                <a href="../doctor/doctor_login.php">
                    <button class="nav-button doctor-btn">醫師登入</button>
                </a>
            </div>
        </div>
    </div>

    <div class="content-area">
        <div class="main-content">
            <h2>最新消息</h2>
            <div class="news-item">
                <div class="news-date">2024/12/15</div>
                <div class="news-title">
                    <a href="#"  onclick="showPopup('門診時間異動通知', '2024/12/15', '由於節假日的安排，我們將於 2025 年 1 月 1 日（元旦）及 2025 年 2 月 28 日（春節）進行門診時間異動。請各位病患提前安排就診時間，避免造成不便。所有專科門診將於上述假期的前一日及後一日照常開診，且會提前結束，請病患務必提前預約。<br>如有任何問題，請致電我們的客服專線：(02)XXXX-XXXX。')">門診時間異動通知</a>
                </div>
            </div>
            <div class="news-item">
                <div class="news-date">2024/11/10</div>
                <div class="news-title">
                    <a href="#" onclick="showPopup('新增特約醫療機構服務', '2024/11/10', 'XX醫院很高興地宣布，已與多家優質醫療機構達成合作協議，病患可以在以下新增的特約醫療機構享受優惠服務：<br>1. ABC醫療機構 – 提供專科診療及健康檢查服務<br>2. XYZ診所 – 提供物理治療及復健服務<br>3. 123牙科診所 – 提供牙齒保健及治療服務<br>請病患於就診時出示醫院就診證明，即可享有優惠。<br>詳情請洽詢門診櫃台或撥打客服專線。')">新增特約醫療機構服務</a>
                </div>
            </div>
            <div class="news-item">
                <div class="news-date">2024/10/25</div>
                <div class="news-title">
                    <a href="#"  onclick="showPopup('公費流感疫苗接種作業施打開始', '2024/10/25', '2024年冬季流感疫苗接種已正式開始！為保障民眾健康，公費流感疫苗將提供給65歲以上長者、孕婦以及5歲以下幼兒接種。接種時間為每週一至週五，上午9點至下午4點，地點為醫院接種中心。<br>若有需要接種流感疫苗的民眾，請先致電預約並攜帶身份證明文件。')">公費流感疫苗接種作業施打開始</a>
                </div>
            </div>
            <div class="news-item">
                <div class="news-date">2024/10/10</div>
                <div class="news-title">
                    <a href="#" class="news-title" onclick="showPopup('陪探病規範及時段調整公告', '2024/10/10', '近期因為防疫需求，我院將調整陪探病規範及時段，具體變動如下：<br>1. 陪同病人探病的親屬需在指定時段內進行，並且需出示健康碼及體溫檢測結果。<br>2. 每日陪探病時段為上午10:00至12:00，下午2:00至4:00。<br>3. 每次探病僅限1名親屬陪同。<br>我們將於2025年1月1日起正式實施此規範，敬請大家理解與配合。')">陪探病規範及時段調整公告</a>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <h2>重要公告</h2>
            <div class="announcement">
                <strong>春節門診公告</strong>
                <p>農曆春節期間（2025/01/25-2025/02/02）門診時間異動...</p>
            </div>
            <div class="announcement">
                <strong>防疫提醒</strong>
                <p>請配合量測體溫及配戴口罩，共同維護醫療環境安全。</p>
            </div>
        </div>
    </div>

    <!-- Popup Modal -->
    <div id="popupModal" class="popup">
        <button class="close-btn" onclick="closePopup()">×</button>
        <h3 id="popupTitle"></h3>
        <p id="popupDate"></p>
        <p id="popupContent"></p>
    </div>

    <script>
        function showPopup(title, date, content) {
            document.getElementById('popupTitle').innerText = title;
            document.getElementById('popupDate').innerText = date;
            document.getElementById('popupContent').innerText = content;
            document.getElementById('popupModal').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popupModal').style.display = 'none';
        }
    </script>
</body>

</html>
