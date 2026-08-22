<?php
// ตรวจสอบสถานะการเข้าสู่ระบบ
session_start();
if (!isset($_SESSION['role'])) { 
    header("Location: ../login.php"); 
    exit(); 
}
require_once 'db.php';

$full_name = $_SESSION['full_name'];

// ดึงรายชื่อกลุ่มเรียนเพื่อนำมาทำตัวเลือก Dropdown
$groups = $pdo->query("SELECT stdgroup_id, stdgroup_name FROM stdgroup ORDER BY stdgroup_name ASC")->fetchAll();
$selected_group = $_GET['group'] ?? ($groups[0]['stdgroup_id'] ?? 'ITCSP11');

// ดึงวิชาเรียนของกลุ่มที่เลือก
$sql = "SELECT s.schedule_id, sub.subject_id, sub.subject_name, sub.hours, t.teacher_name, r.room_name 
        FROM schedule s
        JOIN subject sub ON s.subject_id = sub.subject_id
        JOIN teacher t ON s.teacher_id = t.teacher_id
        LEFT JOIN room r ON s.room_id = r.room_id
        WHERE s.stdgroup_id = :group_id
        ORDER BY s.schedule_id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':group_id' => $selected_group]);
$group_courses = $stmt->fetchAll();

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
    <title>ตารางเรียน | นักศึกษา</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --primary: #2563eb; --primary-light: #eff6ff; --text-main: #0f172a; --text-muted: #64748b; --border: #e2e8f0; --transition: all 0.25s ease; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: var(--bg); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        .sidebar { width: 240px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 24px 12px; gap: 8px; }
        .nav-links { flex: 1; display: flex; flex-direction: column; gap: 6px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-muted); text-decoration: none; border-radius: 12px; font-size: 15px; transition: var(--transition); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 500; }
        .logout-btn { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #ef4444; text-decoration: none; border-radius: 12px; font-size: 15px; }
        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; padding: 30px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .timetable-container { background: var(--surface); border-radius: 12px; border: 1px solid var(--border); overflow-x: auto; margin-bottom: 24px; }
        .timetable { width: 100%; border-collapse: collapse; min-width: 900px; }
        .timetable th, .timetable td { border: 1px solid var(--border); text-align: center; font-size: 13px; padding: 10px 6px; }
        .timetable thead th { background: #f8fafc; font-weight: 500; }
        .timetable tbody th { background: #f8fafc; width: 90px; }
        .slot-empty { height: 75px; background: #fff; }
        .card { background: var(--surface); border-radius: 16px; border: 1px solid #f1f5f9; padding: 24px; }
        .course-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-top: 14px; }
        .course-item { padding: 16px; border-radius: 12px; border: 1px solid var(--border); background: #fafbfc; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="nav-links">
            <a href="student_schedule.php" class="nav-item active"><i class='bx bx-book-reader'></i> ตารางเรียนของฉัน</a>
        </div>
        <a href="../login.php" class="logout-btn"><i class='bx bx-log-out'></i> ออกจากระบบ</a>
    </aside>

    <main class="main-content">
        <div class="header-section">
            <div>
                <h1 style="font-size: 22px; font-weight: 600;">ตารางเรียนประจำสัปดาห์</h1>
                <p style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">ผู้เข้าชม: <?= htmlspecialchars($full_name); ?></p>
            </div>
            <div>
                <select style="padding: 9px 14px; border: 1px solid var(--border); border-radius: 8px; background: white;" onchange="location.href='?group=' + this.value;">
                    <?php foreach($groups as $g): ?>
                        <option value="<?= $g['stdgroup_id'] ?>" <?= $g['stdgroup_id'] === $selected_group ? 'selected' : '' ?>>
                            กลุ่มเรียน <?= htmlspecialchars($g['stdgroup_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="timetable-container">
            <table class="timetable">
                <thead>
                    <tr>
                        <th>วัน \ คาบ</th>
                        <?php foreach($periods as $num => $time): ?>
                            <th><?= $num ?> <span style="display:block; font-size:11px; color:var(--text-muted);"><?= $time ?></span></th>
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

        <div class="card">
            <h2 style="font-size: 16px; font-weight: 600;">รายวิชาทั้งหมดของกลุ่มเรียนนี้</h2>
            <div class="course-list">
                <?php foreach($group_courses as $course): ?>
                <div class="course-item">
                    <div style="font-size: 12px; color: var(--primary); font-weight: 600;"><?= htmlspecialchars($course['subject_id']) ?> • <?= $course['hours'] ?> คาบ/สัปดาห์</div>
                    <div style="font-size: 14px; font-weight: 500; margin: 4px 0;"><?= htmlspecialchars($course['subject_name']) ?></div>
                    <div style="font-size: 12px; color: var(--text-muted);">ผู้สอน: <?= htmlspecialchars($course['teacher_name']) ?> | ห้อง: <?= htmlspecialchars($course['room_name'] ?? 'ยังไม่ระบุ') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

</body>
</html>