<?php
// ตรวจสอบสิทธิ์ผู้ดูแลระบบ
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}
require_once 'db.php';

// ดึงรายชื่ออาจารย์ทั้งหมดสำหรับตัวกรอง
$teachers = $pdo->query("SELECT teacher_id, teacher_name FROM teacher ORDER BY teacher_name")->fetchAll();

// ข้อมูลกำหนดวันและคาบเวลา
$days = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์'];
$periods = [
    1 => '08.30-09.30', 2 => '09.30-10.30', 3 => '10.30-11.30', 4 => '11.30-12.30',
    5 => '12.30-13.30', 6 => '13.30-14.30', 7 => '14.30-15.30', 8 => '15.30-16.30',
    9 => '16.30-17.30', 10 => '17.30-18.30'
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการตารางสอน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --primary: #2563eb; --primary-light: #eff6ff; --text-main: #0f172a; --text-muted: #64748b; --border: #e2e8f0; --transition: all 0.25s ease; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: var(--bg); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        
        .sidebar { width: 240px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 24px 12px; gap: 8px; }
        .nav-links { flex: 1; display: flex; flex-direction: column; gap: 6px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-muted); text-decoration: none; border-radius: 12px; font-size: 15px; transition: var(--transition); }
        .nav-item:hover { background: var(--bg); color: var(--text-main); transform: translateX(3px); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 500; }
        .logout-btn { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #ef4444; text-decoration: none; border-radius: 12px; font-size: 15px; }
        .logout-btn:hover { background: #fef2f2; }
        
        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; padding: 30px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .title-area h1 { font-size: 22px; font-weight: 600; }
        .title-area p { font-size: 14px; color: var(--text-muted); margin-top: 4px; }
        .filter-area { display: flex; gap: 10px; align-items: center; }
        .filter-area select { padding: 9px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; font-size: 14px; background: white; }
        .btn-generate { background: var(--primary); color: white; padding: 9px 16px; border-radius: 8px; border: none; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        
        /* สไตล์โครงตารางสอน */
        .timetable-container { background: var(--surface); border-radius: 12px; border: 1px solid var(--border); overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .timetable { width: 100%; border-collapse: collapse; min-width: 900px; }
        .timetable th, .timetable td { border: 1px solid var(--border); text-align: center; font-size: 13px; padding: 8px 4px; }
        .timetable thead th { background: #f1f5f9; font-weight: 500; }
        .timetable tbody th { background: #f8fafc; width: 80px; }
        .time-label { font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px; }
        .slot-empty { height: 75px; background: #ffffff; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="nav-links">
            <a href="admin_dashboard.php" class="nav-item"><i class='bx bx-grid-alt'></i> ภาพรวม</a>
            <a href="admin_schedule.php" class="nav-item active"><i class='bx bx-calendar-event'></i> ตารางสอน</a>
            <a href="admin_teacher.php" class="nav-item"><i class='bx bx-user-voice'></i> อาจารย์</a>
            <a href="admin_room.php" class="nav-item"><i class='bx bx-buildings'></i> ห้องเรียน</a>
        </div>
        <a href="../login.php" class="logout-btn"><i class='bx bx-log-out'></i> ออกจากระบบ</a>
    </aside>

    <main class="main-content">
        <div class="header-section">
            <div class="title-area">
                <h1>ตารางสอนรายบุคคล</h1>
                <p>ภาคเรียนที่ 1 ปีการศึกษา 2569</p>
            </div>
            <div class="filter-area">
                <select>
                    <option value="">-- เลือกอาจารย์ --</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['teacher_id'] ?>"><?= htmlspecialchars($t['teacher_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn-generate"><i class='bx bx-cog'></i> ประมวลผลตาราง</button>
            </div>
        </div>

        <div class="timetable-container">
            <table class="timetable">
                <thead>
                    <tr>
                        <th>วัน \ คาบ</th>
                        <?php foreach($periods as $num => $time): ?>
                            <th><?= $num ?> <span class="time-label"><?= $time ?></span></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($days as $day): ?>
                    <tr>
                        <th><?= $day ?></th>
                        <?php for($i = 1; $i <= 10; $i++): ?>
                            <td class="slot-empty"></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>