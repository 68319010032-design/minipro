<?php
// เริ่มต้น Session เพื่อจัดเก็บข้อมูลประจำตัวผู้ใช้งาน
session_start();

// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>กำลังตรวจสอบข้อมูล...</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Kanit', sans-serif; background-color: #f8fafc; }</style>
</head>
<body>

<?php
// ตรวจสอบว่ามีการส่งข้อมูลด้วยวิธี POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าและตัดช่องว่างส่วนเกิน
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    // ตรวจสอบบัญชีผู้ใช้ในระบบ
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->execute([$username, $role]);
    $userData = $stmt->fetch();

    // ตรวจสอบความถูกต้องของรหัสผ่าน
    if ($userData && $password === $userData['password']) {
        // บันทึกค่าลง Session
        $_SESSION['user_id']   = $userData['id'];
        $_SESSION['username']  = $userData['username'];
        $_SESSION['full_name'] = $userData['full_name'];
        $_SESSION['role']      = $userData['role'];

        // กำหนดหน้าปลายทางตามประเภทผู้ใช้งาน
        $next_page = ($role === 'admin') ? "admin_dashboard.php" : (($role === 'teacher') ? "teacher_schedule.php" : "student_schedule.php");

        // แสดงแจ้งเตือนเมื่อเข้าสู่ระบบสำเร็จ
        echo "<script>
            Swal.fire({
                title: 'เข้าสู่ระบบสำเร็จ',
                text: 'ยินดีต้อนรับคุณ {$userData['full_name']}',
                icon: 'success',
                confirmButtonText: 'เข้าสู่ระบบ',
                confirmButtonColor: '#2563eb'
            }).then(() => {
                window.location.href = '$next_page';
            });
        </script>";
    } else {
        // แจ้งเตือนเมื่อข้อมูลไม่ถูกต้องและย้อนกลับหน้า login.php
        echo "<script>
            Swal.fire({
                title: 'เข้าสู่ระบบไม่สำเร็จ',
                text: 'ชื่อผู้ใช้ รหัสผ่าน หรือสิทธิ์ไม่ถูกต้อง',
                icon: 'error',
                confirmButtonText: 'ลองใหม่อีกครั้ง',
                confirmButtonColor: '#ef4444'
            }).then(() => {
                window.location.href = '../login.php';
            });
        </script>";
    }
}
?>
</body>
</html>