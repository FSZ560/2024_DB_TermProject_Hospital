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
                <a href="../common/introduction.php">
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
                <div class="news-title">門診時間異動通知</div>
            </div>
            <div class="news-item">
                <div class="news-date">2024/11/10</div>
                <div class="news-title">新增特約醫療機構服務</div>
            </div>
            <div class="news-item">
                <div class="news-date">2024/10/25</div>
                <div class="news-title">公費流感疫苗接種作業施打開始</div>
            </div>
            <div class="news-item">
                <div class="news-date">2024/10/10</div>
                <div class="news-title">陪探病規範及時段調整公告</div>
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
</body>

</html>