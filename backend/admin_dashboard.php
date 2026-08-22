<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}
require_once 'db.php';

// จัดการลบข้อมูลตาม Table และ Primary Key
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $tbl = $_GET['table'] ?? '';
    $id  = $_GET['id'] ?? '';
    $key = $_GET['key'] ?? '';
    
    $allowed = [
        'users'      => 'id',
        'teacher'    => 'teacher_id',
        'room'       => 'room_id',
        'subject'    => 'subject_id',
        'stdgroup'   => 'stdgroup_id',
        'time_slot'  => 'slot_id',
        'schedule'   => 'schedule_id',
        'slot_block' => 'block_id'
    ];
    
    if (array_key_exists($tbl, $allowed) && $allowed[$tbl] === $key) {
        $stmtDel = $pdo->prepare("DELETE FROM `$tbl` WHERE `$key` = ?");
        $stmtDel->execute([$id]);
        header("Location: admin_dashboard.php");
        exit();
    }
}

// 1. ดึงสถิติจำนวนทั้งหมด
$stats = [
    'users'      => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'teacher'    => $pdo->query("SELECT COUNT(*) FROM teacher")->fetchColumn(),
    'room'       => $pdo->query("SELECT COUNT(*) FROM room")->fetchColumn(),
    'subject'    => $pdo->query("SELECT COUNT(*) FROM subject")->fetchColumn(),
    'stdgroup'   => $pdo->query("SELECT COUNT(*) FROM stdgroup")->fetchColumn(),
    'time_slot'  => $pdo->query("SELECT COUNT(*) FROM time_slot")->fetchColumn(),
    'slot_block' => $pdo->query("SELECT COUNT(*) FROM slot_block")->fetchColumn(),
    'schedule'   => $pdo->query("SELECT COUNT(*) FROM schedule")->fetchColumn(),
];

// 2. ดึงข้อมูลจากทุกตาราง (SELECT ตรงจากตารางเพื่อป้องกันข้อผิดพลาดเรื่องชื่อคอลัมน์)
$dataUsers     = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
$dataTeacher   = $pdo->query("SELECT * FROM teacher ORDER BY teacher_id ASC")->fetchAll();
$dataRoom      = $pdo->query("SELECT * FROM room ORDER BY room_id ASC")->fetchAll();
$dataSubject   = $pdo->query("SELECT * FROM subject ORDER BY subject_id ASC")->fetchAll();
$dataStdgroup  = $pdo->query("SELECT * FROM stdgroup ORDER BY stdgroup_id ASC")->fetchAll();
$dataTimeSlot  = $pdo->query("SELECT * FROM time_slot ORDER BY FIELD(day_name, 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์'), period_no ASC")->fetchAll();
$dataSlotBlock = $pdo->query("SELECT * FROM slot_block ORDER BY block_id ASC")->fetchAll();
$dataSchedule  = $pdo->query("SELECT s.*, sub.subject_name, sub.hours, t.teacher_name, g.stdgroup_name, r.room_name 
                              FROM schedule s 
                              JOIN subject sub ON s.subject_id = sub.subject_id 
                              JOIN teacher t ON s.teacher_id = t.teacher_id 
                              JOIN stdgroup g ON s.stdgroup_id = g.stdgroup_id 
                              LEFT JOIN room r ON s.room_id = r.room_id 
                              ORDER BY s.schedule_id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการฐานข้อมูล | Admin Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --transition: all 0.2s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: var(--bg); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }

        .sidebar {
            width: 250px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 24px 12px;
            gap: 6px;
        }
        .sidebar-title { font-size: 11px; color: var(--text-muted); padding: 8px 16px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .nav-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; color: var(--text-muted); text-decoration: none; border-radius: 10px; font-size: 14px; transition: var(--transition); }
        .nav-item div { display: flex; align-items: center; gap: 10px; }
        .nav-item:hover { background: #f1f5f9; color: var(--text-main); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 500; }
        .badge { background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; }
        .nav-item.active .badge { background: var(--primary); color: white; }
        .logout-btn { display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--danger); text-decoration: none; border-radius: 10px; font-size: 14px; margin-top: auto; transition: var(--transition); }
        .logout-btn:hover { background: #fef2f2; }

        .main-content { flex: 1; overflow-y: auto; padding: 32px 48px; scroll-behavior: smooth; }
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .page-title { font-size: 24px; font-weight: 600; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 14px; margin-bottom: 36px; }
        .stat-card { background: var(--surface); padding: 16px 20px; border-radius: 12px; border: 1px solid var(--border); text-decoration: none; color: inherit; transition: var(--transition); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.04); border-color: var(--primary); }
        .stat-card span { font-size: 12px; color: var(--text-muted); }
        .stat-card h3 { font-size: 22px; font-weight: 600; margin-top: 4px; color: var(--primary); }

        .section-block { margin-bottom: 36px; }
        .section-title { font-size: 18px; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        
        .table-card { background: var(--surface); border-radius: 14px; border: 1px solid var(--border); overflow: hidden; max-height: 420px; overflow-y: auto; position: relative; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { position: sticky; top: 0; background: #f8fafc; padding: 14px 20px; font-size: 13px; color: var(--text-muted); border-bottom: 1px solid var(--border); font-weight: 500; z-index: 1; }
        td { padding: 12px 20px; font-size: 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:hover td { background-color: #fafbfc; }
        .btn-del { color: var(--danger); text-decoration: none; padding: 4px 8px; border-radius: 6px; font-size: 13px; font-weight: 500; }
        .btn-del:hover { background: #fef2f2; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-title">เมนูควบคุมหลัก</div>
        <a href="#overview" class="nav-item active"><div><i class='bx bx-grid-alt'></i> ภาพรวมทั้งหมด</div></a>
        <a href="admin_schedule.php" class="nav-item"><div><i class='bx bx-calendar-week'></i> ตารางสอนรวม</div></a>
        
        <div class="sidebar-title" style="margin-top: 15px;">ตารางในระบบ</div>
        <a href="#tbl-schedule" class="nav-item"><div><i class='bx bx-time'></i> แผนการสอน</div><span class="badge"><?= $stats['schedule'] ?></span></a>
        <a href="#tbl-users" class="nav-item"><div><i class='bx bx-shield-quarter'></i> ผู้ใช้งาน</div><span class="badge"><?= $stats['users'] ?></span></a>
        <a href="#tbl-teacher" class="nav-item"><div><i class='bx bx-user-voice'></i> อาจารย์</div><span class="badge"><?= $stats['teacher'] ?></span></a>
        <a href="#tbl-room" class="nav-item"><div><i class='bx bx-buildings'></i> ห้องเรียน</div><span class="badge"><?= $stats['room'] ?></span></a>
        <a href="#tbl-subject" class="nav-item"><div><i class='bx bx-book'></i> รายวิชา</div><span class="badge"><?= $stats['subject'] ?></span></a>
        <a href="#tbl-stdgroup" class="nav-item"><div><i class='bx bx-group'></i> กลุ่มเรียน</div><span class="badge"><?= $stats['stdgroup'] ?></span></a>
        <a href="#tbl-timeslot" class="nav-item"><div><i class='bx bx-time-five'></i> คาบเวลา</div><span class="badge"><?= $stats['time_slot'] ?></span></a>
        <a href="#tbl-slotblock" class="nav-item"><div><i class='bx bx-block'></i> งดใช้คาบ</div><span class="badge"><?= $stats['slot_block'] ?></span></a>

        <a href="../login.php" class="logout-btn"><i class='bx bx-log-out'></i> ออกจากระบบ</a>
    </aside>

    <main class="main-content" id="overview">
        <div class="top-header">
            <h1 class="page-title">ระบบจัดการฐานข้อมูล (Admin Center)</h1>
            <span style="font-size: 14px; color: var(--text-muted);">ผู้ดูแลระบบ: <b><?= htmlspecialchars($_SESSION['full_name']); ?></b></span>
        </div>

        <!-- 1. กล่องสรุปสถิติด่วน -->
        <div class="stats-grid">
            <a href="#tbl-schedule" class="stat-card"><span>แผนการสอน</span><h3><?= $stats['schedule'] ?></h3></a>
            <a href="#tbl-teacher" class="stat-card"><span>อาจารย์</span><h3><?= $stats['teacher'] ?></h3></a>
            <a href="#tbl-room" class="stat-card"><span>ห้องเรียน</span><h3><?= $stats['room'] ?></h3></a>
            <a href="#tbl-subject" class="stat-card"><span>รายวิชา</span><h3><?= $stats['subject'] ?></h3></a>
            <a href="#tbl-stdgroup" class="stat-card"><span>กลุ่มเรียน</span><h3><?= $stats['stdgroup'] ?></h3></a>
            <a href="#tbl-users" class="stat-card"><span>ผู้ใช้งาน</span><h3><?= $stats['users'] ?></h3></a>
            <a href="#tbl-timeslot" class="stat-card"><span>คาบเวลา</span><h3><?= $stats['time_slot'] ?></h3></a>
            <a href="#tbl-slotblock" class="stat-card"><span>งดใช้คาบ</span><h3><?= $stats['slot_block'] ?></h3></a>
        </div>

        <!-- 2. ตาราง Schedule (แผนการสอน) -->
        <div class="section-block" id="tbl-schedule">
            <div class="section-title"><i class='bx bx-time'></i> 1. แผนการจัดตารางสอน (Schedule - <?= count($dataSchedule) ?> รายการ)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th style="width: 70px;">รหัส</th><th>วิชา</th><th>ผู้สอน</th><th>กลุ่มเรียน</th><th>ห้องเรียน</th><th>ภาค/ปี</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataSchedule as $r): ?>
                        <tr>
                            <td style="color: var(--text-muted);">#<?= $r['schedule_id'] ?></td>
                            <td><b><?= htmlspecialchars($r['subject_name']) ?></b> (<?= htmlspecialchars($r['subject_id']) ?>)</td>
                            <td><?= htmlspecialchars($r['teacher_name']) ?></td>
                            <td><?= htmlspecialchars($r['stdgroup_name']) ?></td>
                            <td><?= htmlspecialchars($r['room_name'] ?? 'ยังไม่ระบุ') ?></td>
                            <td><?= $r['term'] ?>/<?= $r['academic_year'] ?></td>
                            <td style="text-align: center;"><a href="?action=delete&table=schedule&key=schedule_id&id=<?= $r['schedule_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. ตาราง Users -->
        <div class="section-block" id="tbl-users">
            <div class="section-title"><i class='bx bx-shield-quarter'></i> 2. บัญชีผู้ใช้งาน (Users - <?= count($dataUsers) ?> บัญชี)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th>ID</th><th>Username</th><th>ชื่อ-นามสกุล</th><th>สิทธิ์</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataUsers as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><b><?= htmlspecialchars($r['username']) ?></b></td>
                            <td><?= htmlspecialchars($r['full_name']) ?></td>
                            <td><span class="badge"><?= $r['role'] ?></span></td>
                            <td style="text-align: center;"><a href="?action=delete&table=users&key=id&id=<?= $r['id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. ตาราง Teacher -->
        <div class="section-block" id="tbl-teacher">
            <div class="section-title"><i class='bx bx-user-voice'></i> 3. อาจารย์ผู้สอน (Teacher - <?= count($dataTeacher) ?> ท่าน)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th style="width: 150px;">รหัสอาจารย์</th><th>ชื่อ-นามสกุล</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataTeacher as $r): ?>
                        <tr>
                            <td style="color: var(--primary); font-weight: 500;"><?= htmlspecialchars($r['teacher_id']) ?></td>
                            <td><?= htmlspecialchars($r['teacher_name']) ?></td>
                            <td style="text-align: center;"><a href="?action=delete&table=teacher&key=teacher_id&id=<?= $r['teacher_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. ตาราง Room -->
        <div class="section-block" id="tbl-room">
            <div class="section-title"><i class='bx bx-buildings'></i> 4. ห้องเรียนและสถานที่ (Room - <?= count($dataRoom) ?> ห้อง)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th style="width: 150px;">รหัสห้อง</th><th>ชื่อห้อง / อาคาร</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataRoom as $r): ?>
                        <tr>
                            <td style="color: var(--text-muted);"><?= htmlspecialchars($r['room_id']) ?></td>
                            <td><b><?= htmlspecialchars($r['room_name']) ?></b></td>
                            <td style="text-align: center;"><a href="?action=delete&table=room&key=room_id&id=<?= $r['room_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. ตาราง Subject -->
        <div class="section-block" id="tbl-subject">
            <div class="section-title"><i class='bx bx-book'></i> 5. รายวิชา (Subject - <?= count($dataSubject) ?> วิชา)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th style="width: 140px;">รหัสวิชา</th><th>ชื่อรายวิชา</th><th>ชั่วโมง/สัปดาห์</th><th>ประเภท</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataSubject as $r): ?>
                        <tr>
                            <td style="color: var(--primary);"><?= htmlspecialchars($r['subject_id']) ?></td>
                            <td><b><?= htmlspecialchars($r['subject_name']) ?></b></td>
                            <td><?= $r['hours'] ?> ชั่วโมง</td>
                            <td><span class="badge"><?= $r['subject_type'] ?></span></td>
                            <td style="text-align: center;"><a href="?action=delete&table=subject&key=subject_id&id=<?= $r['subject_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 7. ตาราง Stdgroup -->
        <div class="section-block" id="tbl-stdgroup">
            <div class="section-title"><i class='bx bx-group'></i> 6. กลุ่มเรียน (StdGroup - <?= count($dataStdgroup) ?> กลุ่ม)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th style="width: 150px;">รหัสกลุ่ม</th><th>ชื่อกลุ่มเรียน</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataStdgroup as $r): ?>
                        <tr>
                            <td style="color: var(--text-muted);"><?= htmlspecialchars($r['stdgroup_id']) ?></td>
                            <td><b><?= htmlspecialchars($r['stdgroup_name']) ?></b></td>
                            <td style="text-align: center;"><a href="?action=delete&table=stdgroup&key=stdgroup_id&id=<?= $r['stdgroup_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 8. ตาราง Time Slot -->
        <div class="section-block" id="tbl-timeslot">
            <div class="section-title"><i class='bx bx-time-five'></i> 7. คาบเวลาเรียน (Time Slot - <?= count($dataTimeSlot) ?> คาบ)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th>รหัสคาบ</th><th>วัน</th><th>คาบที่</th><th>เวลาเริ่ม</th><th>เวลาสิ้นสุด</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($dataTimeSlot as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['slot_id']) ?></td>
                            <td><b><?= htmlspecialchars($r['day_name']) ?></b></td>
                            <td>คาบที่ <?= $r['period_no'] ?></td>
                            <td><?= $r['start_time'] ?></td>
                            <td><?= $r['end_time'] ?></td>
                            <td style="text-align: center;"><a href="?action=delete&table=time_slot&key=slot_id&id=<?= $r['slot_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 9. ตาราง Slot Block -->
        <div class="section-block" id="tbl-slotblock">
            <div class="section-title"><i class='bx bx-block'></i> 8. บล็อกช่วงเวลางดจัดสอน (Slot Block - <?= count($dataSlotBlock) ?> รายการ)</div>
            <div class="table-card">
                <table>
                    <thead><tr><th>ID</th><th>Schedule ID</th><th>Slot ID</th><th>Room ID</th><th style="text-align: center;">จัดการ</th></tr></thead>
                    <tbody>
                        <?php if (empty($dataSlotBlock)): ?>
                            <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">ไม่มีรายการบล็อกช่วงเวลา</td></tr>
                        <?php endif; ?>
                        <?php foreach($dataSlotBlock as $r): ?>
                        <tr>
                            <td><?= $r['block_id'] ?></td>
                            <td>#<?= htmlspecialchars($r['schedule_id']) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($r['slot_id']) ?></span></td>
                            <td><?= htmlspecialchars($r['room_id'] ?? '-') ?></td>
                            <td style="text-align: center;"><a href="?action=delete&table=slot_block&key=block_id&id=<?= $r['block_id'] ?>" class="btn-del" onclick="return confirm('ยืนยันการลบ?')">ลบ</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>