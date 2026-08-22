<?php
// ตรวจสอบสิทธิ์ผู้ดูแลระบบ
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php"); 
    exit(); 
}
require_once 'db.php';

// จัดการการแบ่งหน้า
$limit = 10; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$totalRecords = $pdo->query("SELECT COUNT(*) FROM room")->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// ดึงข้อมูลห้องเรียนตามหน้าปัจจุบัน
$stmt = $pdo->prepare("SELECT room_id, room_name FROM room ORDER BY room_id ASC LIMIT :start, :limit");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$rooms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการห้องเรียน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --primary: #2563eb; --primary-light: #eff6ff; --text-main: #0f172a; --text-muted: #64748b; --border: #f1f5f9; --transition: all 0.25s ease; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: var(--bg); display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        .sidebar { width: 240px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 24px 12px; gap: 8px; }
        .nav-links { flex: 1; display: flex; flex-direction: column; gap: 6px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-muted); text-decoration: none; border-radius: 12px; font-size: 15px; transition: var(--transition); }
        .nav-item:hover { background: var(--bg); color: var(--text-main); transform: translateX(3px); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 500; }
        .logout-btn { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #ef4444; text-decoration: none; border-radius: 12px; font-size: 15px; }
        .logout-btn:hover { background: #fef2f2; }
        .main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; padding: 30px 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-title { font-size: 22px; font-weight: 500; }
        .card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 16px 24px; font-size: 13px; color: var(--text-muted); font-weight: 500; border-bottom: 1px solid var(--border); }
        td { padding: 18px 24px; font-size: 14px; border-bottom: 1px solid var(--border); }
        tr:hover td { background-color: #fafbfc; }
        .pagination { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
        .page-buttons { display: flex; gap: 4px; }
        .page-btn { padding: 6px 12px; border: 1px solid transparent; background: transparent; color: var(--text-muted); border-radius: 8px; text-decoration: none; font-size: 13px; }
        .page-btn.active { background: var(--surface); border-color: #e2e8f0; color: var(--primary); font-weight: 600; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="nav-links">
            <a href="admin_dashboard.php" class="nav-item"><i class='bx bx-grid-alt'></i> ภาพรวม</a>
            <a href="admin_schedule.php" class="nav-item"><i class='bx bx-calendar-event'></i> ตารางสอน</a>
            <a href="admin_teacher.php" class="nav-item"><i class='bx bx-user-voice'></i> อาจารย์</a>
            <a href="admin_room.php" class="nav-item active"><i class='bx bx-buildings'></i> ห้องเรียน</a>
        </div>
        <a href="../login.php" class="logout-btn"><i class='bx bx-log-out'></i> ออกจากระบบ</a>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">ห้องเรียนและสถานที่</h1>
        </div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th style="width: 140px;">รหัสห้อง</th>
                        <th>ชื่อห้องเรียน / สถานที่</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $row): ?>
                    <tr>
                        <td style="color: var(--text-muted);"><?= htmlspecialchars($row['room_id']) ?></td>
                        <td style="font-weight: 500;"><?= htmlspecialchars($row['room_name']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <span style="font-size:13px; color:var(--text-muted);">แสดง <?= $start + 1 ?>-<?= min($start + $limit, $totalRecords) ?> จาก <?= $totalRecords ?> รายการ</span>
                <div class="page-buttons">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-btn <?= ($i === $page) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </main>

</body>
</html>