<?php
// เริ่มต้น Session และตรวจสอบสิทธิ์
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../login.php");
    exit();
}
require_once 'db.php';

// ดึงรหัสอาจารย์จาก Session
$teacher_id = $_SESSION['username'];
$full_name  = $_SESSION['full_name'];

// --- จัดการการเพิ่ม / แก้ไข ข้อมูลแผนการสอนของอาจารย์ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action        = $_POST['action'] ?? '';
    $subject_id    = $_POST['subject_id'] ?? '';
    $stdgroup_id   = $_POST['stdgroup_id'] ?? '';
    $room_id       = !empty($_POST['room_id']) ? $_POST['room_id'] : null;
    $term          = (int)($_POST['term'] ?? 1);
    $academic_year = (int)($_POST['academic_year'] ?? 2569);

    if ($action === 'add') {
        // เพิ่มแผนการสอนใหม่ของตนเอง
        $stmt = $pdo->prepare("INSERT INTO schedule (subject_id, teacher_id, stdgroup_id, room_id, term, academic_year) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$subject_id, $teacher_id, $stdgroup_id, $room_id, $term, $academic_year]);
    } elseif ($action === 'edit') {
        $sched_id = (int)($_POST['schedule_id'] ?? 0);
        // แก้ไขเฉพาะรายการของตนเองเท่านั้น
        $stmt = $pdo->prepare("UPDATE schedule SET subject_id = ?, stdgroup_id = ?, room_id = ?, term = ?, academic_year = ? WHERE schedule_id = ? AND teacher_id = ?");
        $stmt->execute([$subject_id, $stdgroup_id, $room_id, $term, $academic_year, $sched_id, $teacher_id]);
    }
    header("Location: teacher_schedule.php");
    exit();
}

// ลบรายการของตนเอง
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM schedule WHERE schedule_id = ? AND teacher_id = ?");
    $stmt->execute([$del_id, $teacher_id]);
    header("Location: teacher_schedule.php");
    exit();
}

// ดึงตัวเลือกสำหรับ Dropdown
$subjects = $pdo->query("SELECT * FROM subject ORDER BY subject_name ASC")->fetchAll();
$rooms    = $pdo->query("SELECT * FROM room ORDER BY room_name ASC")->fetchAll();
$groups   = $pdo->query("SELECT * FROM stdgroup ORDER BY stdgroup_name ASC")->fetchAll();

// ดึงรายการสอนเฉพาะของอาจารย์ท่านนี้
$stmt = $pdo->prepare("SELECT s.schedule_id, s.subject_id, s.teacher_id, s.stdgroup_id, s.room_id, s.term, s.academic_year,
                              sub.subject_name, sub.hours, g.stdgroup_name, r.room_name 
                       FROM schedule s
                       JOIN subject sub ON s.subject_id = sub.subject_id
                       JOIN stdgroup g ON s.stdgroup_id = g.stdgroup_id
                       LEFT JOIN room r ON s.room_id = r.room_id
                       WHERE s.teacher_id = ?
                       ORDER BY s.schedule_id ASC");
$stmt->execute([$teacher_id]);
$my_schedules = $stmt->fetchAll();

// คำนวณสถิติของอาจารย์
$total_classes = count($my_schedules);
$total_hours   = array_sum(array_column($my_schedules, 'hours'));

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
    <title>ระบบอาจารย์ผู้สอน | Teacher Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --primary: #2563eb; --primary-light: #eff6ff; --text-main: #0f172a; --text-muted: #64748b; --border: #e2e8f0; --danger: #ef4444; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: var(--bg); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        
        .sidebar { width: 250px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 24px 12px; gap: 6px; }
        .sidebar-title { font-size: 12px; color: var(--text-muted); padding: 8px 16px; text-transform: uppercase; font-weight: 600; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--text-muted); text-decoration: none; border-radius: 10px; font-size: 14px; transition: all 0.2s; }
        .nav-item:hover { background: #f1f5f9; color: var(--text-main); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 500; }
        .logout-btn { display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--danger); text-decoration: none; border-radius: 10px; font-size: 14px; margin-top: auto; }
        .logout-btn:hover { background: #fef2f2; }

        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; padding: 30px 40px; }
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: 600; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--surface); padding: 20px; border-radius: 14px; border: 1px solid var(--border); }
        .stat-card span { font-size: 13px; color: var(--text-muted); }
        .stat-card h3 { font-size: 24px; font-weight: 600; margin-top: 4px; color: var(--primary); }

        .timetable-container { background: var(--surface); border-radius: 14px; border: 1px solid var(--border); overflow-x: auto; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .timetable { width: 100%; border-collapse: collapse; min-width: 950px; text-align: center; }
        .timetable th, .timetable td { border: 1px solid var(--border); padding: 8px 4px; font-size: 13px; }
        .timetable thead th { background: #f8fafc; font-weight: 500; height: 50px; }
        .timetable tbody th { background: #f8fafc; width: 90px; font-weight: 500; }
        .time-label { display: block; font-size: 11px; color: var(--text-muted); }
        .slot-empty { height: 60px; background: #fff; }

        .card { background: var(--surface); border-radius: 14px; border: 1px solid var(--border); padding: 24px; margin-bottom: 24px; }
        .card-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
        .form-group input, .form-group select { padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; background: #fff; }
        .btn-primary { background: var(--primary); color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500; align-self: flex-end; }
        .btn-primary:hover { background: #1d4ed8; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { background: #f8fafc; padding: 12px; font-size: 13px; color: var(--text-muted); border-bottom: 1px solid var(--border); text-align: left; }
        table.data-table td { padding: 12px; font-size: 14px; border-bottom: 1px solid var(--border); }
        .action-links { display: flex; gap: 10px; }
        .btn-edit { color: var(--primary); cursor: pointer; font-size: 13px; background: none; border: none; font-weight: 500; }
        .btn-del { color: var(--danger); font-size: 13px; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-title">เมนูอาจารย์ผู้สอน</div>
        <a href="teacher_schedule.php" class="nav-item active"><i class='bx bx-calendar-event'></i> จัดการตารางสอนของฉัน</a>
        <a href="../login.php" class="logout-btn"><i class='bx bx-log-out'></i> ออกจากระบบ</a>
    </aside>

    <main class="main-content">
        <div class="top-header">
            <div>
                <h1 class="page-title">ตารางสอนของฉัน</h1>
                <p style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">อาจารย์ผู้สอน: <b><?= htmlspecialchars($full_name); ?></b> (รหัส: <?= htmlspecialchars($teacher_id) ?>)</p>
            </div>
            <button onclick="window.print()" style="padding: 9px 16px; border: 1px solid var(--border); background: white; border-radius: 8px; cursor: pointer;"><i class='bx bx-printer'></i> พิมพ์ตารางสอน</button>
        </div>

        <!-- สรุปสถิติชั่วโมงสอน -->
        <div class="stats-grid">
            <div class="stat-card"><span>จำนวนวิชา/กลุ่มที่สอน</span><h3><?= $total_classes ?> กลุ่ม</h3></div>
            <div class="stat-card"><span>รวมชั่วโมงสอนสัปดาห์นี้</span><h3><?= $total_hours ?> ชั่วโมง</h3></div>
        </div>

        <!-- ตาราง Matrix แสดงเวลา (รอระบบ Algorithm วางคาบ) -->
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

        <!-- ฟอร์มเพิ่ม / แก้ไข แผนการสอนของอาจารย์ -->
        <div class="card">
            <div class="card-title"><i class='bx bx-edit'></i> <span id="formTitle">เพิ่มแผนการสอนในตารางของฉัน</span></div>
            <form method="POST" action="teacher_schedule.php">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="schedule_id" id="formScheduleId" value="">

                <div class="form-grid">
                    <div class="form-group">
                        <label>รายวิชา</label>
                        <select name="subject_id" id="formSubject" required>
                            <option value="">-- เลือกรายวิชา --</option>
                            <?php foreach($subjects as $sb): ?>
                                <option value="<?= $sb['subject_id'] ?>"><?= htmlspecialchars($sb['subject_name']) ?> (<?= $sb['hours'] ?> ชม.)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>กลุ่มเรียน</label>
                        <select name="stdgroup_id" id="formGroup" required>
                            <option value="">-- เลือกกลุ่มเรียน --</option>
                            <?php foreach($groups as $g): ?>
                                <option value="<?= $g['stdgroup_id'] ?>"><?= htmlspecialchars($g['stdgroup_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ห้องเรียน</label>
                        <select name="room_id" id="formRoom">
                            <option value="">-- เลือกห้องเรียน --</option>
                            <?php foreach($rooms as $r): ?>
                                <option value="<?= $r['room_id'] ?>"><?= htmlspecialchars($r['room_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ภาคเรียน / ปีการศึกษา</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="number" name="term" id="formTerm" value="1" min="1" max="3" style="width: 70px;" required>
                            <input type="number" name="academic_year" id="formYear" value="2569" required>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 18px; display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary" id="btnSubmit">บันทึกข้อมูล</button>
                    <button type="button" onclick="resetScheduleForm()" style="padding: 10px 16px; border: 1px solid var(--border); background: white; border-radius: 8px; cursor: pointer; display: none;" id="btnCancel">ยกเลิกแก้ไข</button>
                </div>
            </form>
        </div>

        <!-- รายการวิชาในตารางของอาจารย์ พร้อมปุ่มแก้ไข/ลบ -->
        <div class="card">
            <div class="card-title"><i class='bx bx-list-ul'></i> รายการวิชาที่รับผิดชอบการสอน</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">รหัสแผน</th>
                        <th>รายวิชา</th>
                        <th>จำนวนชั่วโมง</th>
                        <th>กลุ่มเรียน</th>
                        <th>ห้องเรียน</th>
                        <th>ภาคเรียน</th>
                        <th style="text-align: center; width: 140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($my_schedules)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">ยังไม่มีรายการแผนการสอน</td></tr>
                    <?php endif; ?>
                    <?php foreach($my_schedules as $s): ?>
                    <tr>
                        <td style="color: var(--text-muted);">#<?= $s['schedule_id'] ?></td>
                        <td><b><?= htmlspecialchars($s['subject_name']) ?></b> (<?= htmlspecialchars($s['subject_id']) ?>)</td>
                        <td><?= $s['hours'] ?> ชั่วโมง</td>
                        <td><?= htmlspecialchars($s['stdgroup_name']) ?></td>
                        <td><?= htmlspecialchars($s['room_name'] ?? 'ยังไม่ระบุ') ?></td>
                        <td><?= $s['term'] ?>/<?= $s['academic_year'] ?></td>
                        <td style="text-align: center;">
                            <div class="action-links" style="justify-content: center;">
                                <button class="btn-edit" onclick='editSchedule(<?= json_encode($s) ?>)'><i class='bx bx-edit'></i> แก้ไข</button>
                                <a href="?delete_id=<?= $s['schedule_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันที่จะลบแผนการสอนนี้?')"><i class='bx bx-trash'></i> ลบ</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- JavaScript สำหรับนำข้อมูลเก่าขึ้นฟอร์มแก้ไข -->
    <script>
        function editSchedule(data) {
            document.getElementById('formTitle').innerText = 'แก้ไขข้อมูลแผนการสอน (รหัส #' + data.schedule_id + ')';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('formScheduleId').value = data.schedule_id;
            document.getElementById('formSubject').value = data.subject_id;
            document.getElementById('formGroup').value = data.stdgroup_id;
            document.getElementById('formRoom').value = data.room_id || '';
            document.getElementById('formTerm').value = data.term;
            document.getElementById('formYear').value = data.academic_year;
            document.getElementById('btnSubmit').innerText = 'บันทึกการแก้ไข';
            document.getElementById('btnCancel').style.display = 'inline-block';
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        function resetScheduleForm() {
            document.getElementById('formTitle').innerText = 'เพิ่มแผนการสอนในตารางของฉัน';
            document.getElementById('formAction').value = 'add';
            document.getElementById('formScheduleId').value = '';
            document.getElementById('formSubject').value = '';
            document.getElementById('formGroup').value = '';
            document.getElementById('formRoom').value = '';
            document.getElementById('formTerm').value = '1';
            document.getElementById('formYear').value = '2569';
            document.getElementById('btnSubmit').innerText = 'บันทึกข้อมูล';
            document.getElementById('btnCancel').style.display = 'none';
        }
    </script>

</body>
</html>