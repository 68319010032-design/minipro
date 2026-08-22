<?php
// กำหนดพารามิเตอร์สำหรับการเชื่อมต่อฐานข้อมูล MariaDB
$host = '127.0.0.1';
$db   = 'school_db';
$user = 'root';
$pass = '1234';

try {
    // สร้าง Object การเชื่อมต่อด้วย PDO รองรับภาษาไทย UTF-8
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // แจ้งเตือนข้อผิดพลาดแบบ Exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // ดึงข้อมูลเป็น Associative Array
        PDO::ATTR_EMULATE_PREPARES   => false,                  // ป้องกัน SQL Injection อย่างปลอดภัย
    ]);
} catch (PDOException $e) {
    // จัดการและแสดงข้อความเมื่อการเชื่อมต่อล้มเหลว
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
}
?>