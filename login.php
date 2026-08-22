<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | ระบบตารางสอน</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* 2. รีเซ็ตค่าพื้นฐานของเบราว์เซอร์ เพื่อไม่ให้เกิดขอบขาวส่วนเกิน */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        /* 3. ตั้งค่าให้เนื้อหาอยู่กึ่งกลางหน้าจอทั้งแนวตั้งและแนวนอน */
        body {
            background-color: #f3f4f6; /* พื้นหลังสีเทาอ่อน */
            color: #374151;
            display: flex;
            justify-content: center; /* จัดกึ่งกลางแนวนอน */
            align-items: center;     /* จัดกึ่งกลางแนวตั้ง */
            min-height: 100vh;       /* ยืดความสูงเต็มหน้าจอ */
            padding: 20px;
        }

        /* 4. กล่องหลักของหน้า Login */
        .login-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 1000px;
            min-height: 550px;
            border-radius: 20px;     /* มุมโค้งมน */
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1); /* มิติเงา */
            display: flex;           /* แบ่งพื้นที่ซ้าย-ขวา */
            overflow: hidden;        /* ป้องกันพื้นหลังทะลุมุมมน */
        }

        /* --- 5. พื้นที่ฝั่งซ้าย (ข้อความต้อนรับและแบรนด์) --- */
        .login-sidebar {
            flex: 1;                 /* สัดส่วน 50% */
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); /* การไล่โทนสีน้ำเงิน */
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* จัดกระจายบน-ล่าง */
        }

        .login-sidebar .welcome-text h1 {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .login-sidebar .welcome-text p {
            font-size: 16px;
            color: #dbeafe;
            line-height: 1.6;
            font-weight: 300;
        }

        /* --- 6. พื้นที่ฝั่งขวา (ฟอร์มเข้าสู่ระบบ) --- */
        .login-form-area {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffffff;
        }

        .login-form-area h2 { font-size: 28px; color: #111827; margin-bottom: 8px; font-weight: 600; }
        .login-form-area p.subtitle { color: #6b7280; font-size: 15px; margin-bottom: 35px; }
        .form-group { margin-bottom: 22px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #374151; }

        /* การจัดรูปแบบ Input และ Dropdown */
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            background-color: #f9fafb;
            color: #1f2937;
            transition: all 0.2s ease;
            outline: none;
        }

        /* สถานะเมื่อคลิกเลือกช่องกรอก */
        input:focus, select:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* การตกแต่งปุ่ม Submit */
        button {
            width: 100%;
            padding: 15px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }
        button:hover { background-color: #1d4ed8; }
        button:active { transform: scale(0.98); }

        /* --- 7. รองรับการแสดงผลหน้าจอมือถือ (Responsive) --- */
        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; min-height: auto; }
            .login-sidebar { padding: 40px 30px; }
            .login-form-area { padding: 40px 30px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        
        <div class="login-sidebar">
            <div class="logo"></div>
            <div class="welcome-text">
                <h1>ระบบจัดการ<br>ตารางสอนออนไลน์</h1>
                <p>เข้าสู่ระบบเพื่อจัดการตารางเรียน ตารางสอน และตรวจสอบข้อมูลส่วนตัวของคุณได้อย่างสะดวกรวดเร็ว</p>
            </div>
            <div style="font-size: 12px; opacity: 0.7;">
                © 2026 Education System. All rights reserved.
            </div>
        </div>

        <div class="login-form-area">
            <h2>ยินดีต้อนรับกลับมา</h2>
            <p class="subtitle">กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบ</p>
            
            <form action="backend/check_login.php" method="POST">
                
                <div class="form-group">
                    <label for="role">เข้าใช้งานในฐานะ</label>
                    <select name="role" id="role" required>
                        <option value="" disabled selected>-- เลือกสิทธิ์การใช้งาน --</option>
                        <option value="student">👨‍🎓 นักเรียน / นักศึกษา</option>
                        <option value="teacher">👨‍🏫 อาจารย์ผู้สอน</option>
                        <option value="admin">⚙️ ผู้ดูแลระบบ (Admin)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">ชื่อผู้ใช้งาน (Username)</label>
                    <input type="text" name="username" id="username" placeholder="กรอกชื่อผู้ใช้งานของคุณ" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">รหัสผ่าน (Password)</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit">เข้าสู่ระบบ</button>
            </form>
        </div>

    </div>

</body>
</html>