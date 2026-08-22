-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.3.2-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for school_db
CREATE DATABASE IF NOT EXISTS `school_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `school_db`;

-- Dumping structure for table school_db.result
CREATE TABLE IF NOT EXISTS `result` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `slot_id` varchar(20) NOT NULL,
  `room_id` varchar(20) NOT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`result_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `slot_id` (`slot_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `1` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`slot_id`) REFERENCES `time_slot` (`slot_id`) ON UPDATE CASCADE,
  CONSTRAINT `3` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.result: ~0 rows (approximately)

-- Dumping structure for table school_db.room
CREATE TABLE IF NOT EXISTS `room` (
  `room_id` varchar(20) NOT NULL,
  `room_name` varchar(50) NOT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.room: ~23 rows (approximately)
INSERT INTO `room` (`room_id`, `room_name`) VALUES
	('FLD01', 'FLD-01'),
	('OJT01', 'OJT-01'),
	('R10205', 'R10205'),
	('R6303', 'IT-6303'),
	('R6305', 'IT-6305'),
	('R6306', 'IT-6306'),
	('R6308', 'IT-6308'),
	('R6309', 'IT-6309'),
	('R6310', 'IT-6310'),
	('R6312', 'IT-6312'),
	('R6314', 'IT-6314'),
	('R6316', 'IT-6316'),
	('R6318', 'IT-6318'),
	('R6320', 'IT-6320'),
	('R6322', 'IT-6322'),
	('R6403', 'IT-6403'),
	('R6404', 'IT-6404'),
	('R6405', 'IT-6405'),
	('R6407', 'IT-6407'),
	('R6408', 'IT-6408'),
	('R6414', 'IT-6414'),
	('R6416', 'IT-6416'),
	('R6418', 'IT-6418');

-- Dumping structure for table school_db.schedule
CREATE TABLE IF NOT EXISTS `schedule` (
  `schedule_id` int(11) NOT NULL,
  `subject_id` varchar(20) NOT NULL,
  `teacher_id` varchar(20) NOT NULL,
  `stdgroup_id` varchar(20) NOT NULL,
  `room_id` varchar(20) DEFAULT NULL,
  `term` int(11) NOT NULL DEFAULT 1,
  `academic_year` int(11) NOT NULL DEFAULT 2569,
  PRIMARY KEY (`schedule_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `stdgroup_id` (`stdgroup_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON UPDATE CASCADE,
  CONSTRAINT `3` FOREIGN KEY (`stdgroup_id`) REFERENCES `stdgroup` (`stdgroup_id`) ON UPDATE CASCADE,
  CONSTRAINT `4` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.schedule: ~128 rows (approximately)
INSERT INTO `schedule` (`schedule_id`, `subject_id`, `teacher_id`, `stdgroup_id`, `room_id`, `term`, `academic_year`) VALUES
	(1, '21903-2014', 'TIT017', 'ITCSP11', 'R6322', 1, 2569),
	(2, '21901-2002', 'TIT016', 'ITCSP11', 'R6414', 1, 2569),
	(3, '20000-2001', 'TIT013', 'ITCSP11', 'FLD01', 1, 2569),
	(4, '21901-2010', 'TIT005', 'ITCSP11', 'R6404', 1, 2569),
	(5, '21900-1001', 'TIT014', 'ITCSP11', 'R6416', 1, 2569),
	(6, '21900-1001', 'TIT014', 'ITCSP12', 'R6416', 1, 2569),
	(7, '21901-2002', 'TIT015', 'ITCSP12', 'R6312', 1, 2569),
	(8, '21901-2010', 'TIT005', 'ITCSP12', 'R6404', 1, 2569),
	(9, '20000-2001', 'TIT013', 'ITCSP12', 'FLD01', 1, 2569),
	(10, '21903-2014', 'TIT017', 'ITCSP12', 'R6322', 1, 2569),
	(11, '21901-2002', 'TIT015', 'ITCSP13', 'R6316', 1, 2569),
	(12, '21901-2010', 'TIT005', 'ITCSP13', 'R6404', 1, 2569),
	(13, '21903-2014', 'TIT017', 'ITCSP13', 'R6305', 1, 2569),
	(14, '20000-2001', 'TIT013', 'ITCSP13', 'FLD01', 1, 2569),
	(15, '21900-1001', 'TIT018', 'ITCSP13', 'R6303', 1, 2569),
	(16, '21901-2002', 'TIT015', 'ITCSP14', 'R6312', 1, 2569),
	(17, '21903-2014', 'TIT017', 'ITCSP14', 'R6403', 1, 2569),
	(18, '21900-1001', 'TIT018', 'ITCSP14', 'R6303', 1, 2569),
	(19, '21901-2010', 'TIT005', 'ITCSP14', 'R6404', 1, 2569),
	(20, '20000-2001', 'TIT013', 'ITCSP14', 'FLD01', 1, 2569),
	(21, '21901-2001', 'TIT002', 'ITCSP21', 'R6414', 1, 2569),
	(22, '21901-2004', 'TIT008', 'ITCSP21', 'R6414', 1, 2569),
	(23, '20000-2003', 'TIT008', 'ITCSP21', 'FLD01', 1, 2569),
	(24, '21901-2009', 'TIT002', 'ITCSP21', 'R6322', 1, 2569),
	(25, '21901-2003', 'TIT016', 'ITCSP21', 'R6316', 1, 2569),
	(26, '21901-2008', 'TIT019', 'ITCSP21', 'R6405', 1, 2569),
	(27, '20000-2003', 'TIT013', 'ITCSP22', 'FLD01', 1, 2569),
	(28, '21901-2009', 'TIT002', 'ITCSP22', 'R6322', 1, 2569),
	(29, '21901-2008', 'TIT019', 'ITCSP22', 'R6405', 1, 2569),
	(30, '21901-2003', 'TIT016', 'ITCSP22', 'R6316', 1, 2569),
	(31, '21901-2001', 'TIT002', 'ITCSP22', 'R6414', 1, 2569),
	(32, '21901-2004', 'TIT008', 'ITCSP22', 'R6414', 1, 2569),
	(33, '21901-2009', 'TIT002', 'ITCSP23', 'R6305', 1, 2569),
	(34, '20000-2003', 'TIT013', 'ITCSP23', 'FLD01', 1, 2569),
	(35, '21901-2003', 'TIT004', 'ITCSP23', 'R6316', 1, 2569),
	(36, '21901-2008', 'TIT019', 'ITCSP23', 'R6405', 1, 2569),
	(37, '21901-2001', 'TIT002', 'ITCSP23', 'R6414', 1, 2569),
	(38, '21901-2004', 'TIT008', 'ITCSP23', 'R6312', 1, 2569),
	(39, '21901-2004', 'TIT008', 'ITCSP24', 'R6414', 1, 2569),
	(40, '21901-2003', 'TIT004', 'ITCSP24', 'R6316', 1, 2569),
	(41, '21901-2009', 'TIT002', 'ITCSP24', 'R6322', 1, 2569),
	(42, '21901-2008', 'TIT019', 'ITCSP24', 'R6405', 1, 2569),
	(43, '21901-2001', 'TIT002', 'ITCSP24', 'R6303', 1, 2569),
	(44, '20000-2003', 'TIT008', 'ITCSP24', 'FLD01', 1, 2569),
	(45, '21901-2007', 'TIT018', 'ITCSP31', 'R6405', 1, 2569),
	(46, '21901-2005', 'TIT014', 'ITCSP31', 'R6416', 1, 2569),
	(47, '20000-2005', 'TIT013', 'ITCSP31', 'R6408', 1, 2569),
	(48, '21901-2006', 'TIT016', 'ITCSP31', 'R6418', 1, 2569),
	(49, '21901-2005', 'TIT014', 'ITCSP32', 'R6416', 1, 2569),
	(50, '21901-2006', 'TIT016', 'ITCSP32', 'R6418', 1, 2569),
	(51, '21901-2007', 'TIT018', 'ITCSP32', 'R6405', 1, 2569),
	(52, '20000-2005', 'TIT013', 'ITCSP32', 'R6408', 1, 2569),
	(53, '20000-2007', 'TIT006', 'ITCSP33', 'OJT01', 1, 2569),
	(54, '21900-1002', 'TIT006', 'ITCSP33', 'OJT01', 1, 2569),
	(55, '21900-1003', 'TIT006', 'ITCSP33', 'OJT01', 1, 2569),
	(56, '21900-1002', 'TIT006', 'ITCSP34', 'OJT01', 1, 2569),
	(57, '21900-1003', 'TIT006', 'ITCSP34', 'OJT01', 1, 2569),
	(58, '20000-2007', 'TIT006', 'ITCSP34', 'OJT01', 1, 2569),
	(59, '21903-2015', 'TIT017', 'ITCSH11', 'R6305', 1, 2569),
	(60, '21901-2002', 'TIT015', 'ITCSH11', 'R6306', 1, 2569),
	(61, '21903-2016', 'TIT003', 'ITCSH11', 'R6310', 1, 2569),
	(62, '21901-2010', 'TIT005', 'ITCSH11', 'R6404', 1, 2569),
	(63, '21900-1001', 'TIT014', 'ITCSH11', 'R6416', 1, 2569),
	(64, '20000-2001', 'TIT018', 'ITCSH11', 'FLD01', 1, 2569),
	(65, '21900-1001', 'TIT014', 'ITCSH12', 'R6416', 1, 2569),
	(66, '21903-2015', 'TIT017', 'ITCSH12', 'R6305', 1, 2569),
	(67, '21901-2010', 'TIT005', 'ITCSH12', 'R6404', 1, 2569),
	(68, '21901-2002', 'TIT015', 'ITCSH12', 'R6306', 1, 2569),
	(69, '21903-2016', 'TIT003', 'ITCSH12', 'R6310', 1, 2569),
	(70, '20000-2001', 'TIT018', 'ITCSH12', 'FLD01', 1, 2569),
	(71, '21901-2009', 'TIT002', 'ITCSH21', 'R6305', 1, 2569),
	(72, '21901-2008', 'TIT019', 'ITCSH21', 'R6405', 1, 2569),
	(73, '21901-2004', 'TIT008', 'ITCSH21', 'R6414', 1, 2569),
	(74, '20000-2003', 'TIT018', 'ITCSH21', 'FLD01', 1, 2569),
	(75, '21901-2003', 'TIT004', 'ITCSH21', 'R6318', 1, 2569),
	(76, '21901-2001', 'TIT002', 'ITCSH21', 'R6308', 1, 2569),
	(77, '21901-2003', 'TIT004', 'ITCSH22', 'R6318', 1, 2569),
	(78, '21901-2004', 'TIT008', 'ITCSH22', 'R6414', 1, 2569),
	(79, '21901-2001', 'TIT002', 'ITCSH22', 'R6308', 1, 2569),
	(80, '20000-2003', 'TIT018', 'ITCSH22', 'FLD01', 1, 2569),
	(81, '21901-2009', 'TIT002', 'ITCSH22', 'R6305', 1, 2569),
	(82, '21901-2008', 'TIT019', 'ITCSH22', 'R6405', 1, 2569),
	(83, '30000-2001', 'TIT013', 'ITSEH11', 'R6408', 1, 2569),
	(84, '31901-2001', 'TIT009', 'ITSEH11', 'R6418', 1, 2569),
	(85, '31901-2002', 'TIT016', 'ITSEH11', 'R6418', 1, 2569),
	(86, '31901-2004', 'TIT004', 'ITSEH11', 'R6407', 1, 2569),
	(87, '31901-2005', 'TIT009', 'ITSEH11', 'R6407', 1, 2569),
	(88, '31902-2001', 'TIT017', 'ITSEH11', 'R6418', 1, 2569),
	(89, '30000-2001', 'TIT013', 'ITSEH12', 'R6408', 1, 2569),
	(90, '31901-2001', 'TIT009', 'ITSEH12', 'R6418', 1, 2569),
	(91, '31901-2002', 'TIT016', 'ITSEH12', 'R6418', 1, 2569),
	(92, '31901-2004', 'TIT004', 'ITSEH12', 'R6407', 1, 2569),
	(93, '31901-2005', 'TIT009', 'ITSEH12', 'R6407', 1, 2569),
	(94, '31902-2001', 'TIT017', 'ITSEH12', 'R6418', 1, 2569),
	(95, '31902-2004', 'TIT009', 'ITSEH21', 'R6308', 1, 2569),
	(96, '31900-1001', 'TIT004', 'ITSEH21', 'R6418', 1, 2569),
	(97, '30000-2003', 'TIT015', 'ITSEH21', 'R6408', 1, 2569),
	(98, '31901-2006', 'TIT009', 'ITSEH21', 'R6308', 1, 2569),
	(99, '31901-2007', 'TIT004', 'ITSEH21', 'R6308', 1, 2569),
	(100, '31901-2003', 'TIT004', 'ITSEH21', 'R6308', 1, 2569),
	(101, '30000-2001', 'TIT013', 'ITCNH11', 'R6408', 1, 2569),
	(102, '31901-2001', 'TIT009', 'ITCNH11', 'R6418', 1, 2569),
	(103, '31901-2002', 'TIT016', 'ITCNH11', 'R6418', 1, 2569),
	(104, '31901-2004', 'TIT004', 'ITCNH11', 'R6407', 1, 2569),
	(105, '31901-2005', 'TIT009', 'ITCNH11', 'R6407', 1, 2569),
	(106, '31903-2001', 'TIT010', 'ITCNH11', 'R10205', 1, 2569),
	(107, '30000-2003', 'TIT015', 'ITCNH21', 'R6408', 1, 2569),
	(108, '31900-1001', 'TIT010', 'ITCNH21', 'R10205', 1, 2569),
	(109, '31903-2003', 'TIT010', 'ITCNH21', 'R10205', 1, 2569),
	(110, '31903-2004', 'TIT010', 'ITCNH21', 'R10205', 1, 2569),
	(111, '31903-2005', 'TIT010', 'ITCNH21', 'R10205', 1, 2569),
	(112, '31903-2002', 'TIT010', 'ITCNH21', 'R10205', 1, 2569),
	(113, '31900-0001', 'TIT014', 'ITSEH11', 'R6416', 1, 2569),
	(114, '31900-0002', 'TIT001', 'ITSEH11', 'R6309', 1, 2569),
	(115, '31900-0003', 'TIT001', 'ITSEH11', 'R6309', 1, 2569),
	(116, '31900-0004', 'TIT011', 'ITSEH11', 'R6314', 1, 2569),
	(117, '31900-0005', 'TIT012', 'ITSEH11', 'R6320', 1, 2569),
	(118, '31900-0006', 'TIT012', 'ITSEH11', 'R6320', 1, 2569),
	(119, '31900-0007', 'TIT007', 'ITSEH11', 'R6408', 1, 2569),
	(120, '31900-0008', 'TIT015', 'ITSEH11', 'R6404', 1, 2569),
	(121, '31900-0001', 'TIT014', 'ITCNH11', 'R6416', 1, 2569),
	(122, '31900-0002', 'TIT001', 'ITCNH11', 'R6309', 1, 2569),
	(123, '31900-0003', 'TIT001', 'ITCNH11', 'R6309', 1, 2569),
	(124, '31900-0004', 'TIT011', 'ITCNH11', 'R6314', 1, 2569),
	(125, '31900-0005', 'TIT012', 'ITCNH11', 'R6320', 1, 2569),
	(126, '31900-0006', 'TIT012', 'ITCNH11', 'R6320', 1, 2569),
	(127, '31900-0007', 'TIT007', 'ITCNH11', 'R6408', 1, 2569),
	(128, '31900-0008', 'TIT015', 'ITCNH11', 'R6404', 1, 2569);

-- Dumping structure for table school_db.slot_block
CREATE TABLE IF NOT EXISTS `slot_block` (
  `block_id` int(11) NOT NULL AUTO_INCREMENT,
  `stdgroup_id` varchar(20) NOT NULL,
  `slot_id` varchar(20) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`block_id`),
  KEY `stdgroup_id` (`stdgroup_id`),
  KEY `slot_id` (`slot_id`),
  CONSTRAINT `1` FOREIGN KEY (`stdgroup_id`) REFERENCES `stdgroup` (`stdgroup_id`) ON UPDATE CASCADE,
  CONSTRAINT `2` FOREIGN KEY (`slot_id`) REFERENCES `time_slot` (`slot_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.slot_block: ~0 rows (approximately)

-- Dumping structure for table school_db.stdgroup
CREATE TABLE IF NOT EXISTS `stdgroup` (
  `stdgroup_id` varchar(20) NOT NULL,
  `stdgroup_name` varchar(50) NOT NULL,
  PRIMARY KEY (`stdgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.stdgroup: ~21 rows (approximately)
INSERT INTO `stdgroup` (`stdgroup_id`, `stdgroup_name`) VALUES
	('ITCNH11', 'สช.1/1(CN)'),
	('ITCNH21', 'สช.2/1(CN)'),
	('ITCSH11', 'สช.1/1'),
	('ITCSH12', 'สช.1/2'),
	('ITCSH21', 'สช.2/1'),
	('ITCSH22', 'สช.2/2'),
	('ITCSP11', 'สป.1/1'),
	('ITCSP12', 'สป.1/2'),
	('ITCSP13', 'สป.1/3'),
	('ITCSP14', 'สป.1/4'),
	('ITCSP21', 'สป.2/1'),
	('ITCSP22', 'สป.2/2'),
	('ITCSP23', 'สป.2/3'),
	('ITCSP24', 'สป.2/4'),
	('ITCSP31', 'สป.3/1'),
	('ITCSP32', 'สป.3/2'),
	('ITCSP33', 'สป.3/3'),
	('ITCSP34', 'สป.3/4'),
	('ITSEH11', 'สช.1/1(SE)'),
	('ITSEH12', 'สช.1/2(SE)'),
	('ITSEH21', 'สช.2/1(SE)');

-- Dumping structure for table school_db.subject
CREATE TABLE IF NOT EXISTS `subject` (
  `subject_id` varchar(20) NOT NULL,
  `subject_name` varchar(150) NOT NULL,
  `hours` int(11) NOT NULL DEFAULT 1,
  `subject_type` varchar(30) NOT NULL DEFAULT 'NORMAL',
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.subject: ~50 rows (approximately)
INSERT INTO `subject` (`subject_id`, `subject_name`, `hours`, `subject_type`) VALUES
	('20000-2001', 'กิจกรรมลูกเสือวิสามัญ 1', 2, 'FIXED_ACTIVITY'),
	('20000-2003', 'กิจกรรมเสริมสร้างสุจริต จิตอาสา', 2, 'FIXED_ACTIVITY'),
	('20000-2005', 'กิจกรรมองค์การวิชาชีพ 3', 2, 'FIXED_ACTIVITY'),
	('20000-2007', 'กิจกรรมในสถานประกอบการ1', 2, 'FIXED_ACTIVITY'),
	('21900-1001', 'การเขียนโปรแกรมคอมพิวเตอร์', 6, 'NORMAL'),
	('21900-1002', 'โครงงานและสัมมนาวิชาชีพ', 4, 'NORMAL'),
	('21900-1003', 'การฝึกงานในสถานประกอบการ', 20, 'NORMAL'),
	('21901-2001', 'ระบบจัดการฐานข้อมูล', 5, 'NORMAL'),
	('21901-2002', 'การสร้างเว็บไซต์', 5, 'NORMAL'),
	('21901-2003', 'การพัฒนาเว็บแอปพลิเคชัน', 5, 'NORMAL'),
	('21901-2004', 'การประยุกต์ใช้งานคลาวด์คอมพิวติ้ง', 4, 'NORMAL'),
	('21901-2005', 'การเขียนโปรแกรมเชิงวัตถุเบื้องต้น', 5, 'NORMAL'),
	('21901-2006', 'การเขียนโปรแกรมบนอุปกรณ์พกพา', 5, 'NORMAL'),
	('21901-2007', 'การประยุกต์ใช้อุปกรณ์ IoT', 5, 'NORMAL'),
	('21901-2008', 'การบริการและบำรุงรักษาคอมพิวเตอร์', 5, 'NORMAL'),
	('21901-2009', 'การเขียนโปรแกรมจัดการฐานข้อมูล', 5, 'NORMAL'),
	('21901-2010', 'การสร้างภาพเคลื่อนไหวเบื้องต้น', 4, 'NORMAL'),
	('21903-2014', 'การพัฒนาซอฟต์แวร์ด้วยโอเพนซอร์ส', 5, 'NORMAL'),
	('21903-2015', 'ระบบสารสนเทศทางธุรกิจ', 4, 'NORMAL'),
	('21903-2016', 'การรักษาความปลอดภัยของระบบคอมพิวเตอร์', 4, 'NORMAL'),
	('30000-2001', 'กิจกรรมองค์การวิชาชีพ 1', 2, 'FIXED_ACTIVITY'),
	('30000-2003', 'กิจกรรมองค์การวิชาชีพ 3', 2, 'FIXED_ACTIVITY'),
	('31900-0001', 'การเขียนโปรแกรมเชิงโครงสร้าง', 4, 'NORMAL'),
	('31900-0002', 'คณิตศาสตร์สำหรับคอมพิวเตอร์', 3, 'NORMAL'),
	('31900-0003', 'สถิติเพื่องานวิจัยทางเทคโนโลยีสารสนเทศ', 3, 'NORMAL'),
	('31900-0004', 'ภาษาอังกฤษสำหรับเทคโนโลยีสารสนเทศ', 3, 'NORMAL'),
	('31900-0005', 'การเป็นผู้ประกอบการด้านดิจิทัล', 3, 'NORMAL'),
	('31900-0006', 'จริยธรรมและกฎหมายคอมพิวเตอร์', 3, 'NORMAL'),
	('31900-0007', 'เทคโนโลยีสารสนเทศเพื่อการจัดการ', 3, 'NORMAL'),
	('31900-0008', 'การออกแบบประสบการณ์และส่วนติดต่อผู้ใช้ (UI/UX)', 4, 'NORMAL'),
	('31900-1001', 'โครงงานวิชาชีพ 1', 4, 'NORMAL'),
	('31900-1002', 'โครงงานวิชาชีพ 2', 4, 'NORMAL'),
	('31900-1003', 'การฝึกงานในสถานประกอบการ', 20, 'NORMAL'),
	('31901-2001', 'การวิเคราะห์และออกแบบระบบเชิงวัตถุ', 4, 'NORMAL'),
	('31901-2002', 'การพัฒนาเว็บด้วยเทคโนโลยีสมัยใหม่', 5, 'NORMAL'),
	('31901-2003', 'การพัฒนาโมบายแอปพลิเคชันขั้นสูง', 5, 'NORMAL'),
	('31901-2004', 'การพัฒนาแอปพลิเคชันบนคลาวด์', 4, 'NORMAL'),
	('31901-2005', 'การบริหารจัดการฐานข้อมูลขั้นสูง', 4, 'NORMAL'),
	('31901-2006', 'การจัดการโครงการซอฟต์แวร์', 4, 'NORMAL'),
	('31901-2007', 'การประกันคุณภาพและการทดสอบซอฟต์แวร์', 4, 'NORMAL'),
	('31902-2001', 'วิศวกรรมความต้องการซอฟต์แวร์', 4, 'NORMAL'),
	('31902-2002', 'สถาปัตยกรรมและการออกแบบซอฟต์แวร์', 4, 'NORMAL'),
	('31902-2003', 'กระบวนการและการพัฒนาซอฟต์แวร์แบบเอจิล', 4, 'NORMAL'),
	('31902-2004', 'การทดสอบซอฟต์แวร์และการรับรองคุณภาพ', 4, 'NORMAL'),
	('31902-2005', 'การจัดการโครงแบบซอฟต์แวร์และการบำรุงรักษา', 4, 'NORMAL'),
	('31903-2001', 'การสื่อสารข้อมูลและเครือข่าย', 4, 'NORMAL'),
	('31903-2002', 'การบริหารระบบเครือข่ายคอมพิวเตอร์', 4, 'NORMAL'),
	('31903-2003', 'ความมั่นคงปลอดภัยของระบบเครือข่าย', 4, 'NORMAL'),
	('31903-2004', 'เทคโนโลยีเครือข่ายไร้สายและการสื่อสารเคลื่อนที่', 4, 'NORMAL'),
	('31903-2005', 'การประยุกต์ใช้งานเทคโนโลยีคลาวด์เน็ตเวิร์ก', 4, 'NORMAL');

-- Dumping structure for table school_db.teacher
CREATE TABLE IF NOT EXISTS `teacher` (
  `teacher_id` varchar(20) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  PRIMARY KEY (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.teacher: ~19 rows (approximately)
INSERT INTO `teacher` (`teacher_id`, `teacher_name`) VALUES
	('TIT001', 'ครูกัมพล'),
	('TIT002', 'ครูจงศิริ'),
	('TIT003', 'ครูพัลลภ'),
	('TIT004', 'ครูสมยศ'),
	('TIT005', 'ครูนงลักษณ์'),
	('TIT006', 'ครูเยาวเรศ'),
	('TIT007', 'ครูประยุทธ์'),
	('TIT008', 'ครูอรุณ'),
	('TIT009', 'ครูสมพร'),
	('TIT010', 'ครูสราภรณ์'),
	('TIT011', 'ครูสมนึก'),
	('TIT012', 'ครูปกรณ์'),
	('TIT013', 'ครูธนภันทร์'),
	('TIT014', 'ครูสุกัญญา'),
	('TIT015', 'ครูวิภาดา'),
	('TIT016', 'ครูปริญญา'),
	('TIT017', 'ครูมัลธิกา'),
	('TIT018', 'ครูนุชนาถ'),
	('TIT019', 'ครูสิทธิชัย');

-- Dumping structure for table school_db.time_slot
CREATE TABLE IF NOT EXISTS `time_slot` (
  `slot_id` varchar(20) NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `period_no` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`slot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.time_slot: ~50 rows (approximately)
INSERT INTO `time_slot` (`slot_id`, `day_name`, `period_no`, `start_time`, `end_time`) VALUES
	('FRI01', 'ศุกร์', 1, '08:30:00', '09:30:00'),
	('FRI02', 'ศุกร์', 2, '09:30:00', '10:30:00'),
	('FRI03', 'ศุกร์', 3, '10:30:00', '11:30:00'),
	('FRI04', 'ศุกร์', 4, '11:30:00', '12:30:00'),
	('FRI05', 'ศุกร์', 5, '12:30:00', '13:30:00'),
	('FRI06', 'ศุกร์', 6, '13:30:00', '14:30:00'),
	('FRI07', 'ศุกร์', 7, '14:30:00', '15:30:00'),
	('FRI08', 'ศุกร์', 8, '15:30:00', '16:30:00'),
	('FRI09', 'ศุกร์', 9, '16:30:00', '17:30:00'),
	('FRI10', 'ศุกร์', 10, '17:30:00', '18:30:00'),
	('MON01', 'จันทร์', 1, '08:30:00', '09:30:00'),
	('MON02', 'จันทร์', 2, '09:30:00', '10:30:00'),
	('MON03', 'จันทร์', 3, '10:30:00', '11:30:00'),
	('MON04', 'จันทร์', 4, '11:30:00', '12:30:00'),
	('MON05', 'จันทร์', 5, '12:30:00', '13:30:00'),
	('MON06', 'จันทร์', 6, '13:30:00', '14:30:00'),
	('MON07', 'จันทร์', 7, '14:30:00', '15:30:00'),
	('MON08', 'จันทร์', 8, '15:30:00', '16:30:00'),
	('MON09', 'จันทร์', 9, '16:30:00', '17:30:00'),
	('MON10', 'จันทร์', 10, '17:30:00', '18:30:00'),
	('THU01', 'พฤหัสบดี', 1, '08:30:00', '09:30:00'),
	('THU02', 'พฤหัสบดี', 2, '09:30:00', '10:30:00'),
	('THU03', 'พฤหัสบดี', 3, '10:30:00', '11:30:00'),
	('THU04', 'พฤหัสบดี', 4, '11:30:00', '12:30:00'),
	('THU05', 'พฤหัสบดี', 5, '12:30:00', '13:30:00'),
	('THU06', 'พฤหัสบดี', 6, '13:30:00', '14:30:00'),
	('THU07', 'พฤหัสบดี', 7, '14:30:00', '15:30:00'),
	('THU08', 'พฤหัสบดี', 8, '15:30:00', '16:30:00'),
	('THU09', 'พฤหัสบดี', 9, '16:30:00', '17:30:00'),
	('THU10', 'พฤหัสบดี', 10, '17:30:00', '18:30:00'),
	('TUE01', 'อังคาร', 1, '08:30:00', '09:30:00'),
	('TUE02', 'อังคาร', 2, '09:30:00', '10:30:00'),
	('TUE03', 'อังคาร', 3, '10:30:00', '11:30:00'),
	('TUE04', 'อังคาร', 4, '11:30:00', '12:30:00'),
	('TUE05', 'อังคาร', 5, '12:30:00', '13:30:00'),
	('TUE06', 'อังคาร', 6, '13:30:00', '14:30:00'),
	('TUE07', 'อังคาร', 7, '14:30:00', '15:30:00'),
	('TUE08', 'อังคาร', 8, '15:30:00', '16:30:00'),
	('TUE09', 'อังคาร', 9, '16:30:00', '17:30:00'),
	('TUE10', 'อังคาร', 10, '17:30:00', '18:30:00'),
	('WED01', 'พุธ', 1, '08:30:00', '09:30:00'),
	('WED02', 'พุธ', 2, '09:30:00', '10:30:00'),
	('WED03', 'พุธ', 3, '10:30:00', '11:30:00'),
	('WED04', 'พุธ', 4, '11:30:00', '12:30:00'),
	('WED05', 'พุธ', 5, '12:30:00', '13:30:00'),
	('WED06', 'พุธ', 6, '13:30:00', '14:30:00'),
	('WED07', 'พุธ', 7, '14:30:00', '15:30:00'),
	('WED08', 'พุธ', 8, '15:30:00', '16:30:00'),
	('WED09', 'พุธ', 9, '16:30:00', '17:30:00'),
	('WED10', 'พุธ', 10, '17:30:00', '18:30:00');

-- Dumping structure for table school_db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table school_db.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `role`, `full_name`) VALUES
	(1, 'admin01', '1234', 'admin', 'admin'),
	(2, 'teacher01', '1234', 'teacher', 'teacher'),
	(3, 'student01', '1234', 'student', 'student');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
