-- Quick Backup before Restore
-- Date: 2026-05-06 05:00:59

DROP TABLE IF EXISTS `approvals`;
CREATE TABLE `approvals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `room_amount` decimal(12,2) DEFAULT NULL,
  `insurance_amount` decimal(12,2) DEFAULT NULL,
  `furniture_amount` decimal(12,2) DEFAULT NULL,
  `contract_date` datetime DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `approvals` (`id`, `case_id`, `total_amount`, `room_amount`, `insurance_amount`, `furniture_amount`, `contract_date`, `transfer_date`, `note`, `created_at`) VALUES ('1', '1', '3200000.00', '2800000.00', '150000.00', '250000.00', '2026-05-04 10:00:00', '2026-05-20', 'อนุมัติวงเงินรวม 3.2 ล้านบาท', '2026-05-04 10:35:00');
INSERT INTO `approvals` (`id`, `case_id`, `total_amount`, `room_amount`, `insurance_amount`, `furniture_amount`, `contract_date`, `transfer_date`, `note`, `created_at`) VALUES ('2', '2', '5500000.00', '5000000.00', '200000.00', '300000.00', '2026-05-03 14:00:00', '2026-05-25', 'ลูกค้าขอสินเชื่อเพิ่มเฟอร์นิเจอร์', '2026-05-03 14:35:00');
INSERT INTO `approvals` (`id`, `case_id`, `total_amount`, `room_amount`, `insurance_amount`, `furniture_amount`, `contract_date`, `transfer_date`, `note`, `created_at`) VALUES ('3', '9', '0.00', '0.00', '0.00', '0.00', NULL, NULL, '', '2026-05-05 14:39:25');

DROP TABLE IF EXISTS `bank_submissions`;
CREATE TABLE `bank_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `bank_submissions_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `bank_submissions` (`id`, `case_id`, `bank_name`, `submit_date`, `note`, `created_at`) VALUES ('1', '1', 'ธนาคารกรุงไทย', '2026-05-01', 'ส่งเอกสารครบ ใช้สิทธิ์โครงการ', '2026-05-01 10:35:00');
INSERT INTO `bank_submissions` (`id`, `case_id`, `bank_name`, `submit_date`, `note`, `created_at`) VALUES ('2', '2', 'ธนาคารออมสิน', '2026-05-02', 'ลูกค้าเก่า ใช้สวัสดิการข้าราชการ', '2026-05-02 10:35:00');
INSERT INTO `bank_submissions` (`id`, `case_id`, `bank_name`, `submit_date`, `note`, `created_at`) VALUES ('3', '3', 'ธนาคารอาคารสงเคราะห์', '2026-05-03', 'ยื่นกู้ร่วมกับภรรยา', '2026-05-03 10:35:00');
INSERT INTO `bank_submissions` (`id`, `case_id`, `bank_name`, `submit_date`, `note`, `created_at`) VALUES ('4', '5', 'ธนาคารกรุงเทพ', '2026-05-04', 'พนักงานประจำ เงินเดือนสูง', '2026-05-04 10:35:00');
INSERT INTO `bank_submissions` (`id`, `case_id`, `bank_name`, `submit_date`, `note`, `created_at`) VALUES ('5', '10', 'ธนาคารกรุงเทพ', '2026-05-30', 'Test', '2026-05-05 14:37:25');

DROP TABLE IF EXISTS `case_activities`;
CREATE TABLE `case_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `case_activities_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `case_activities_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('1', '1', 'Case Created', '1', '2026-04-25 12:00:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('2', '1', 'Follow #1: ติดต่อครั้งแรก', '1', '2026-04-26 12:00:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('3', '1', 'KPI Check: Pass', '3', '2026-04-27 12:00:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('4', '2', 'Case Created', '1', '2026-04-28 15:30:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('5', '2', 'ส่งเอกสารให้ Support', '1', '2026-04-29 15:30:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('6', '3', 'Case Created', '1', '2026-04-30 10:15:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('7', '3', 'KPI Check: Fail - รายได้ไม่ถึง', '3', '2026-05-01 10:15:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('8', '4', 'Case Created', '2', '2026-05-01 13:45:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('9', '5', 'Case Created', '2', '2026-05-02 16:20:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('10', '5', 'Follow #1: สนใจมาก', '2', '2026-05-02 16:20:00');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('11', NULL, 'User Login: admin@company.com', '6', '2026-05-04 16:14:22');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('12', NULL, 'User Logout: admin@company.com', '6', '2026-05-04 16:48:26');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('13', NULL, 'User Login: somsri@company.com', '1', '2026-05-04 16:48:50');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('14', NULL, 'User Logout: somsri@company.com', '1', '2026-05-04 16:54:26');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('15', NULL, 'User Login: wichan@company.com', '3', '2026-05-04 17:00:02');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('16', NULL, 'User Logout: wichan@company.com', '3', '2026-05-04 17:00:22');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('17', NULL, 'User Login: admin@company.com', '6', '2026-05-04 17:06:04');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('18', NULL, 'User Logout: admin@company.com (2026-05-04 17:37:43)', '6', '2026-05-04 17:37:43');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('19', NULL, 'User Login: somsri@company.com', '1', '2026-05-04 18:27:30');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('20', NULL, 'User Logout: somsri@company.com (2026-05-04 18:37:02)', '1', '2026-05-04 18:37:02');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('21', NULL, 'User Login: admin@company.com', '6', '2026-05-04 18:37:13');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('22', NULL, 'User Logout: admin@company.com (2026-05-04 18:49:05)', '6', '2026-05-04 18:49:05');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('23', NULL, 'User Login: mana@company.com', '4', '2026-05-04 18:49:16');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('24', NULL, 'User Logout: mana@company.com (2026-05-04 18:52:08)', '4', '2026-05-04 18:52:08');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('25', NULL, 'User Login: admin@company.com', '6', '2026-05-04 18:52:18');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('26', NULL, 'User Logout: admin@company.com (2026-05-04 19:31:12)', '6', '2026-05-04 19:31:12');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('27', NULL, 'User Login: admin@company.com', '6', '2026-05-04 19:31:55');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('28', '8', 'KPI Check: ผ่าน', '6', '2026-05-04 19:58:14');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('29', NULL, 'User Logout:  (2026-05-05 13:38:57)', '1', '2026-05-05 13:38:57');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('30', NULL, 'User Login: admin@company.com', '6', '2026-05-05 13:39:56');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('31', NULL, 'User Logout: admin@company.com (2026-05-05 13:44:55)', '6', '2026-05-05 13:44:55');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('32', NULL, 'User Login: supoj@company.com', '5', '2026-05-05 13:45:04');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('33', NULL, 'User Logout: supoj@company.com (2026-05-05 13:52:50)', '5', '2026-05-05 13:52:50');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('34', NULL, 'User Login: admin@company.com', '6', '2026-05-05 13:53:05');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('35', '9', 'Case Created', '6', '2026-05-05 14:11:20');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('36', '10', 'Case Created', '6', '2026-05-05 14:16:03');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('37', '10', 'KPI Check: ไม่ผ่าน - ไม่ตาม', '6', '2026-05-05 14:16:13');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('38', '10', 'Bank submitted: ธนาคารกรุงเทพ (2026-05-30)', '6', '2026-05-05 14:37:25');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('39', '10', 'Pre-Approve ปฏิเสธ วงเงิน 3,500,000 บาท', '6', '2026-05-05 14:37:44');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('40', '9', 'KPI Check: ไม่ผ่าน - ลูกค้าไม่ตอบ', '6', '2026-05-05 14:39:04');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('41', '9', 'Pre-Approve กำลังดำเนินการ', '6', '2026-05-05 14:39:16');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('42', '9', 'Inspection #1: ไม่ผ่าน', '6', '2026-05-05 14:43:42');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('43', NULL, 'User Logout: admin@company.com (2026-05-05 14:52:54)', '6', '2026-05-05 14:52:54');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('44', NULL, 'User Login: admin@company.com', '6', '2026-05-05 14:53:20');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('45', '5', 'KPI Check: ไม่ผ่าน - ลูกค้าไม่ตอบ', '6', '2026-05-05 14:57:05');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('46', NULL, 'User Logout: admin@company.com (2026-05-05 15:05:51)', '6', '2026-05-05 15:05:51');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('47', NULL, 'User Login: supoj@company.com', '5', '2026-05-05 15:06:03');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('48', NULL, 'User Logout: supoj@company.com (2026-05-05 15:23:59)', '5', '2026-05-05 15:23:59');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('49', NULL, 'User Login: admin@company.com', '6', '2026-05-05 15:24:09');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('50', '10', 'Inspection #1: ผ่าน', '6', '2026-05-05 15:25:04');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('51', NULL, 'User Login: admin@company.com', '6', '2026-05-06 08:23:28');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('52', '6', 'Follow #1: negotiating', '6', '2026-05-06 08:24:35');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('53', NULL, 'User Login: admin@company.com', '6', '2026-05-06 09:32:35');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('54', NULL, 'User Logout: admin@company.com (2026-05-06 09:32:42)', '6', '2026-05-06 09:32:42');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('55', NULL, 'User Login: supoj@company.com', '5', '2026-05-06 09:33:32');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('56', NULL, 'User Logout: supoj@company.com (2026-05-06 09:38:07)', '5', '2026-05-06 09:38:07');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('57', NULL, 'User Login: wichan@company.com', '3', '2026-05-06 09:38:13');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('58', NULL, 'User Logout: wichan@company.com (2026-05-06 09:38:45)', '3', '2026-05-06 09:38:45');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('59', NULL, 'User Login: sommai@company.com', '2', '2026-05-06 09:38:49');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('60', NULL, 'User Logout: sommai@company.com (2026-05-06 09:55:53)', '2', '2026-05-06 09:55:53');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('61', NULL, 'User Login: admin@company.com', '6', '2026-05-06 09:56:03');
INSERT INTO `case_activities` (`id`, `case_id`, `action`, `user_id`, `created_at`) VALUES ('62', NULL, 'Database backup: backup_test_ssg_db_2026-05-06_04-58-01.sql', '6', '2026-05-06 09:58:01');

DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `case_date` date DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `follow_status` varchar(100) DEFAULT NULL,
  `follow_count` int(11) DEFAULT 0,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('1', '1', '2026-04-25', '2026-04-25', 'กำลังติดตาม', 'interested', '3', NULL, '1', '2026-04-25 12:00:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('2', '2', '2026-04-28', '2026-04-28', 'ส่งเคส', 'pending', '1', NULL, '1', '2026-04-28 15:30:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('3', '3', '2026-04-30', '2026-04-30', 'วงเงินไม่ถึง', 'not_qualified', '2', 'รายได้ไม่ถึงเกณฑ์ขั้นต่ำ', '1', '2026-04-30 10:15:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('4', '4', '2026-05-01', '2026-05-01', 'ยกเลิก', 'cancelled', '1', 'ลูกค้าเปลี่ยนใจไปซื้อโครงการอื่น', '2', '2026-05-01 13:45:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('5', '5', '2026-05-02', '2026-05-02', 'กำลังติดตาม', 'high_interest', '4', NULL, '2', '2026-05-02 16:20:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('6', '6', '2026-05-03', '2026-05-03', 'ส่งเคส', 'negotiating', '1', NULL, '1', '2026-05-03 18:30:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('7', '7', '2026-05-03', '2026-05-03', 'ไม่สนใจ', 'interested', '4', 'ลูกค้าต้องการห้องใหญ่กว่านี้', '2', '2026-05-03 21:00:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('8', '8', '2026-05-04', '2026-05-04', 'กำลังติดตาม', 'negotiating', '2', NULL, '1', '2026-05-04 14:10:00');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('9', '11', '2026-05-05', NULL, 'ส่งเคส', 'pending', '0', NULL, '6', '2026-05-05 14:11:20');
INSERT INTO `cases` (`id`, `customer_id`, `case_date`, `submit_date`, `status`, `follow_status`, `follow_count`, `cancel_reason`, `owner_id`, `created_at`) VALUES ('10', '9', '2026-05-05', NULL, 'อนุมัติ', 'document_submitted', '1', NULL, '6', '2026-05-05 14:16:03');

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `facebook` varchar(150) DEFAULT NULL,
  `line_id` varchar(150) DEFAULT NULL,
  `page_name` varchar(150) DEFAULT NULL,
  `channel` varchar(100) DEFAULT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `cashback` decimal(12,2) DEFAULT NULL,
  `living_type` enum('rent','live') DEFAULT NULL,
  `zone` varchar(150) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `work_age_month` int(11) DEFAULT NULL,
  `welfare` varchar(100) DEFAULT NULL,
  `debt_status` enum('have','none') DEFAULT NULL,
  `admin_status` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  KEY `project_id` (`project_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('1', 'CUS202604250001', 'สมชาย ใจดี', '0812345678', 'Somchai Jaidee', 'somchai_j', 'คอนโดดีดี', 'FB', 'A+', '1', '3200000.00', '50000.00', 'live', 'กรุงเทพ-ตะวันออก', 'บริษัท ไทยเทค จำกัด', '48', 'ประกันสังคม', 'none', NULL, '1', '2026-04-25 12:00:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('2', 'CUS202604280001', 'สมหญิง รักดี', '0823456789', 'Somying Rakdee', 'somying_r', 'บ้านสวย', 'TikTok', 'A', '2', '5500000.00', '100000.00', 'rent', 'กรุงเทพ-เหนือ', 'ธนาคารกรุงเทพ', '120', 'กองทุนสำรองเลี้ยงชีพ', 'have', NULL, '1', '2026-04-28 15:30:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('3', 'CUS202604300001', 'วิชัย มั่งมี', '0834567890', 'Wichai Mangmee', 'wichai_m', 'ลงทุนคอนโด', 'Line', 'B', '3', '1800000.00', '30000.00', 'rent', 'ชลบุรี', 'ฟรีแลนซ์', '24', 'ไม่มี', 'have', NULL, '1', '2026-04-30 10:15:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('4', 'CUS202605010001', 'นภา สายลม', '0845678901', 'Napa Sailom', 'napa_s', 'คอนโดสวย', 'FB', 'A', '1', '4200000.00', '80000.00', 'live', 'กรุงเทพ-ใต้', 'บริษัท สยามเทค จำกัด', '36', 'ประกันสังคม', 'none', NULL, '2', '2026-05-01 13:45:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('5', 'CUS202605020001', 'ประสิทธิ์ มั่นคง', '0856789012', 'Prasit Mankong', 'prasit_m', 'บ้านใหม่', 'TikTok', 'A+', '5', '6800000.00', '150000.00', 'live', 'กรุงเทพ-กลาง', 'บริษัท พลังงานไทย จำกัด มหาชน', '60', 'กองทุนสำรองเลี้ยงชีพ', 'none', NULL, '2', '2026-05-02 16:20:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('6', 'CUS202605030001', 'มาลี ดอกไม้', '0867890123', 'Malee Dokmai', 'malee_d', 'สวนคอนโด', 'FB', 'B', '2', '2500000.00', '40000.00', 'live', 'นนทบุรี', 'โรงพยาบาลกรุงเทพ', '24', 'สวัสดิการโรงพยาบาล', 'have', NULL, '1', '2026-05-03 18:30:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('7', 'CUS202605030002', 'ธนา พาณิชย์', '0878901234', 'Thana Panich', 'thana_p', 'ลงทุนคอนโด', 'Line', 'A', '3', '3500000.00', '70000.00', 'rent', 'สมุทรปราการ', 'เจ้าของธุรกิจ', '120', 'ไม่มี', 'have', NULL, '2', '2026-05-03 21:00:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('8', 'CUS202605040001', 'รัตนา แก้วใส', '0889012345', 'Ratana Kaewsai', 'ratana_k', 'คอนโดดีดี', 'FB', 'A+', '4', '4900000.00', '100000.00', 'live', 'กรุงเทพ-ตะวันตก', 'บริษัท โกลบอลเทค จำกัด', '48', 'ประกันสังคม', 'none', NULL, '1', '2026-05-04 14:10:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('9', 'CUS202605040002', 'ภาณุ วิเศษ', '0890123456', 'Panu Wiset', 'panu_w', 'บ้านสวย', 'TikTok', 'B', '1', '2100000.00', '30000.00', 'live', 'ปทุมธานี', 'รับจ้างทั่วไป', '12', 'ไม่มี', 'have', NULL, '2', '2026-05-04 15:00:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('10', 'CUS202605040003', 'กนกวรรณ สดใส', '0901234567', 'Kanokwan Sodsai', 'kanokwan_s', 'ลงทุนคอนโด', 'Line', 'A', '5', '7500000.00', '200000.00', 'rent', 'กรุงเทพ-กลาง', 'บริษัท ปตท. จำกัด มหาชน', '96', 'กองทุนสำรองเลี้ยงชีพ', 'none', NULL, '1', '2026-05-04 15:30:00');
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `facebook`, `line_id`, `page_name`, `channel`, `grade`, `project_id`, `price`, `cashback`, `living_type`, `zone`, `company_name`, `work_age_month`, `welfare`, `debt_status`, `admin_status`, `created_by`, `created_at`) VALUES ('11', 'CUS202605051566', 'อนุชน วทานิยโรจน์', '0821598923', 'FB', 'line', 'condo', 'FB', 'A+', '6', '3500000.00', '100000.00', 'live', 'เชียงใหม่', 'SSG', '120', 'ประกันสังคม', 'have', NULL, NULL, '2026-05-05 14:10:54');

DROP TABLE IF EXISTS `debt_clearings`;
CREATE TABLE `debt_clearings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `clear_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `staff_name` varchar(150) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `debt_clearings_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `debt_clearings` (`id`, `case_id`, `clear_date`, `location`, `staff_name`, `note`, `created_at`) VALUES ('1', '2', '2026-05-03 10:00:00', 'ธนาคารกรุงเทพ สาขาสีลม', 'คุณสมชาย ผู้ช่วย', 'ปิดหนี้บัตรเครดิตและสินเชื่อส่วนบุคคล', '2026-05-03 10:35:00');
INSERT INTO `debt_clearings` (`id`, `case_id`, `clear_date`, `location`, `staff_name`, `note`, `created_at`) VALUES ('5', '9', '2026-05-06 14:39:00', 'สถานที่', 'นาย A', 'ทดสอบ', '2026-05-05 15:23:17');

DROP TABLE IF EXISTS `debt_items`;
CREATE TABLE `debt_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `debt_id` int(11) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `debt_id` (`debt_id`),
  CONSTRAINT `debt_items_ibfk_1` FOREIGN KEY (`debt_id`) REFERENCES `debt_clearings` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `debt_items` (`id`, `debt_id`, `detail`, `amount`) VALUES ('1', '1', 'บัตรเครดิต ธนาคารกรุงเทพ', '150000.00');
INSERT INTO `debt_items` (`id`, `debt_id`, `detail`, `amount`) VALUES ('2', '1', 'สินเชื่อส่วนบุคคล', '200000.00');
INSERT INTO `debt_items` (`id`, `debt_id`, `detail`, `amount`) VALUES ('3', '1', 'ผ่อนรถยนต์', '350000.00');
INSERT INTO `debt_items` (`id`, `debt_id`, `detail`, `amount`) VALUES ('7', '5', 'บัตรเครดิต', '20000.00');
INSERT INTO `debt_items` (`id`, `debt_id`, `detail`, `amount`) VALUES ('8', '5', 'สินเชื่อ', '100.00');

DROP TABLE IF EXISTS `document_steps`;
CREATE TABLE `document_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `doc_status_1` varchar(50) DEFAULT NULL,
  `doc_status_2` varchar(50) DEFAULT NULL,
  `doc_status_3` varchar(50) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `precheck_status` varchar(50) DEFAULT NULL,
  `debt_close_status` enum('done','not_done') DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `document_steps_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `document_steps` (`id`, `case_id`, `doc_status_1`, `doc_status_2`, `doc_status_3`, `bank_name`, `bank_account`, `precheck_status`, `debt_close_status`, `note`, `created_at`) VALUES ('1', '1', 'ผ่าน', 'ผ่าน', 'ผ่าน', 'ธนาคารกรุงไทย', '123-456-7890', 'ผ่าน', 'done', 'เอกสารครบถ้วน ส่งธนาคารได้', '2026-04-30 10:35:00');
INSERT INTO `document_steps` (`id`, `case_id`, `doc_status_1`, `doc_status_2`, `doc_status_3`, `bank_name`, `bank_account`, `precheck_status`, `debt_close_status`, `note`, `created_at`) VALUES ('2', '2', 'ผ่าน', 'ผ่าน', 'ไม่ผ่าน', 'ธนาคารออมสิน', '234-567-8901', 'รอดำเนินการ', 'not_done', 'ขาดเอกสารสลิปเงินเดือน', '2026-05-02 10:35:00');
INSERT INTO `document_steps` (`id`, `case_id`, `doc_status_1`, `doc_status_2`, `doc_status_3`, `bank_name`, `bank_account`, `precheck_status`, `debt_close_status`, `note`, `created_at`) VALUES ('3', '5', 'ผ่าน', 'ผ่าน', 'ผ่าน', 'ธนาคารกรุงเทพ', '345-678-9012', 'ผ่าน', 'done', 'เอกสารพร้อม ส่งตรวจสอบ', '2026-05-04 10:35:00');
INSERT INTO `document_steps` (`id`, `case_id`, `doc_status_1`, `doc_status_2`, `doc_status_3`, `bank_name`, `bank_account`, `precheck_status`, `debt_close_status`, `note`, `created_at`) VALUES ('4', '10', 'ผ่าน', 'ผ่าน', 'เรียบร้อย', '', '1150', 'ผ่าน', 'not_done', '', '2026-05-05 14:32:17');

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('1', '1', '/uploads/cases/1/slip_salary_01.pdf', 'สลิปเงินเดือน', '2026-04-28 10:35:00');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('2', '1', '/uploads/cases/1/id_card_01.pdf', 'บัตรประชาชน', '2026-04-28 10:35:00');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('3', '1', '/uploads/cases/1/house_registration.pdf', 'ทะเบียนบ้าน', '2026-04-29 10:35:00');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('4', '2', '/uploads/cases/2/id_card.pdf', 'บัตรประชาชน', '2026-05-01 10:35:00');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('5', '1', '/uploads/cases/1/bank_statement.pdf', 'Statement ย้อนหลัง 6 เดือน', '2026-05-02 10:35:00');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('6', '5', '/uploads/cases/5/salary_cert.pdf', 'หนังสือรับรองเงินเดือน', '2026-05-04 10:35:00');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('10', '10', 'uploads/cases/10/1777966619_69f99e1b9412b.jpg', 'บัตรประชาชน', '2026-05-05 14:36:59');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('11', '10', 'uploads/cases/10/1777966634_69f99e2a7b929.jpg', 'ทะเบียนบ้าน', '2026-05-05 14:37:14');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('12', '9', 'uploads/inspections/9/inspect_1_1777967022_0.jpg', 'inspection_photo', '2026-05-05 14:43:42');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('13', '9', 'uploads/inspections/9/inspect_1_1777967022_1.jpg', 'inspection_photo', '2026-05-05 14:43:42');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('14', '9', 'uploads/inspections/9/inspect_1_1777967022_2.png', 'inspection_photo', '2026-05-05 14:43:42');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('15', '9', 'uploads/inspections/9/inspect_1_1777967022_3.png', 'inspection_photo', '2026-05-05 14:43:42');
INSERT INTO `files` (`id`, `case_id`, `file_path`, `file_type`, `created_at`) VALUES ('16', '9', 'uploads/inspections/9/inspect_1_1777967022_4.png', 'inspection_photo', '2026-05-05 14:43:42');

DROP TABLE IF EXISTS `follow_logs`;
CREATE TABLE `follow_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `step` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `follow_logs_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('1', '1', '1', 'interested', 'ติดต่อครั้งแรก สนใจห้องแบบ 1 bedroom', '2026-04-26 12:00:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('2', '1', '2', 'document_submitted', 'ส่งเอกสารเรียบร้อย รอตรวจสอบ', '2026-04-27 12:00:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('3', '1', '3', 'negotiating', 'ต่อรองราคา ขอส่วนลดเพิ่ม', '2026-04-28 12:00:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('4', '2', '1', 'pending', 'รอลูกค้าส่งเอกสารเพิ่มเติม', '2026-04-29 12:00:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('5', '3', '1', 'not_qualified', 'แจ้งลูกค้าว่าวงเงินไม่ถึง', '2026-05-01 10:15:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('6', '3', '2', 'cancelled', 'ลูกค้ายืนยันไม่สนใจต่อ', '2026-05-02 10:15:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('7', '5', '1', 'high_interest', 'ลูกค้าสนใจมาก ต้องการจองทันที', '2026-05-02 16:20:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('8', '5', '2', 'document_submitted', 'ส่งเอกสารครบ รออนุมัติ', '2026-05-03 16:20:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('9', '5', '3', 'bank_submitted', 'ส่งธนาคารแล้ว รอผล', '2026-05-04 16:20:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('10', '5', '4', 'waiting_approval', 'รอผลอนุมัติจากธนาคาร', '2026-05-04 16:30:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('11', '7', '1', 'not_interested', 'ลูกค้าต้องการห้อง 2 bedroom แต่โครงการไม่มี', '2026-05-03 21:00:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('12', '8', '1', 'interested', 'สนใจห้องริมสระว่ายน้ำ', '2026-05-04 14:10:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('13', '8', '2', 'site_visit', 'นัดดูห้องจริงวันที่ 6 พ.ค.', '2026-05-04 14:15:00');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('22', '8', '3', 'interested', 'Test', '2026-05-04 19:54:50');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('24', '7', '2', 'negotiating', 'Test', '2026-05-05 13:45:34');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('25', '7', '3', 'site_visit', 'Test', '2026-05-05 13:46:31');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('26', '7', '4', 'interested', '', '2026-05-05 13:48:34');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('27', '10', '1', 'document_submitted', '', '2026-05-05 14:38:10');
INSERT INTO `follow_logs` (`id`, `case_id`, `step`, `status`, `note`, `created_at`) VALUES ('28', '6', '1', 'negotiating', '', '2026-05-06 08:24:35');

DROP TABLE IF EXISTS `inspections`;
CREATE TABLE `inspections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `round` int(11) DEFAULT NULL,
  `inspect_date` datetime DEFAULT NULL,
  `status` enum('pass','fail') DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `inspections_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `inspections` (`id`, `case_id`, `round`, `inspect_date`, `status`, `note`, `created_at`) VALUES ('1', '1', '1', '2026-05-02 09:00:00', 'pass', 'ตรวจห้องครั้งที่ 1: สภาพห้องดี ไม่มี defect', '2026-05-02 09:00:00');
INSERT INTO `inspections` (`id`, `case_id`, `round`, `inspect_date`, `status`, `note`, `created_at`) VALUES ('2', '1', '2', '2026-05-03 14:00:00', 'pass', 'ตรวจห้องครั้งที่ 2: เก็บงานเรียบร้อย พร้อมโอน', '2026-05-03 14:00:00');
INSERT INTO `inspections` (`id`, `case_id`, `round`, `inspect_date`, `status`, `note`, `created_at`) VALUES ('3', '2', '1', '2026-05-04 10:00:00', 'fail', 'ตรวจห้องครั้งที่ 1: พบรอยร้าวที่ผนัง ต้องแก้ไข', '2026-05-04 10:35:00');
INSERT INTO `inspections` (`id`, `case_id`, `round`, `inspect_date`, `status`, `note`, `created_at`) VALUES ('5', '9', '1', '2026-05-05 07:42:00', 'fail', 'Test', '2026-05-05 14:43:42');
INSERT INTO `inspections` (`id`, `case_id`, `round`, `inspect_date`, `status`, `note`, `created_at`) VALUES ('6', '10', '1', '2026-05-05 08:24:00', 'pass', '', '2026-05-05 15:25:04');

DROP TABLE IF EXISTS `kpi_checks`;
CREATE TABLE `kpi_checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `checker_id` int(11) DEFAULT NULL,
  `result` enum('pass','fail') DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `checker_id` (`checker_id`),
  CONSTRAINT `kpi_checks_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  CONSTRAINT `kpi_checks_ibfk_2` FOREIGN KEY (`checker_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('1', '1', '3', 'pass', 'คุณภาพการสนทนาดี ให้ข้อมูลครบถ้วน', '2026-04-27 12:00:00');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('2', '2', '3', 'pass', 'การให้บริการเป็นมืออาชีพ', '2026-04-30 12:00:00');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('3', '3', '3', 'fail', 'ไม่ตรวจสอบคุณสมบัติลูกค้าก่อนส่งเคส', '2026-05-01 10:15:00');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('4', '4', '3', 'fail', 'ไม่ตามลูกค้าภายใน 24 ชม.', '2026-05-02 13:45:00');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('5', '8', '6', 'pass', '', '2026-05-04 19:58:13');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('6', '10', '6', 'fail', 'ไม่ตาม', '2026-05-05 14:16:13');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('7', '9', '6', 'fail', 'ลูกค้าไม่ตอบ', '2026-05-05 14:39:04');
INSERT INTO `kpi_checks` (`id`, `case_id`, `checker_id`, `result`, `reason`, `created_at`) VALUES ('8', '5', '6', 'fail', 'ลูกค้าไม่ตอบ', '2026-05-05 14:57:05');

DROP TABLE IF EXISTS `master_dropdowns`;
CREATE TABLE `master_dropdowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('1', 'channel', 'FB', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('2', 'channel', 'TikTok', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('3', 'channel', 'Line', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('4', 'channel', 'Website', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('5', 'channel', 'Walk-in', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('6', 'channel', 'Referral', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('7', 'zone', 'กรุงเทพ-กลาง', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('8', 'zone', 'กรุงเทพ-เหนือ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('9', 'zone', 'กรุงเทพ-ใต้', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('10', 'zone', 'กรุงเทพ-ตะวันออก', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('11', 'zone', 'กรุงเทพ-ตะวันตก', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('12', 'zone', 'นนทบุรี', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('13', 'zone', 'ปทุมธานี', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('14', 'zone', 'สมุทรปราการ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('15', 'zone', 'ชลบุรี', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('16', 'zone', 'เชียงใหม่', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('17', 'zone', 'ภูเก็ต', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('18', 'welfare', 'ประกันสังคม', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('19', 'welfare', 'กองทุนสำรองเลี้ยงชีพ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('20', 'welfare', 'สวัสดิการโรงพยาบาล', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('21', 'welfare', 'ไม่มี', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('22', 'case_status', 'ส่งเคส', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('23', 'case_status', 'กำลังติดตาม', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('24', 'case_status', 'ยกเลิก', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('25', 'case_status', 'ไม่สนใจ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('26', 'case_status', 'วงเงินไม่ถึง', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('27', 'case_status', 'อนุมัติ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('28', 'case_status', 'ปฏิเสธ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('29', 'follow_status', 'interested', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('30', 'follow_status', 'pending', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('31', 'follow_status', 'document_submitted', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('32', 'follow_status', 'negotiating', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('33', 'follow_status', 'cancelled', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('34', 'follow_status', 'not_qualified', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('35', 'follow_status', 'not_interested', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('36', 'follow_status', 'high_interest', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('37', 'follow_status', 'bank_submitted', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('38', 'follow_status', 'waiting_approval', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('39', 'follow_status', 'approved', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('40', 'follow_status', 'transferred', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('41', 'kpi_reason', 'ไม่ตาม', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('42', 'kpi_reason', 'ลูกค้าไม่ตอบ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('43', 'kpi_reason', 'ติดบูโร', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('44', 'kpi_reason', 'อายุงานไม่ถึง', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('45', 'kpi_reason', 'รายได้ไม่ถึง', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('46', 'kpi_reason', 'เปลี่ยนใจ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('47', 'kpi_reason', 'เอกสารไม่ครบ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('48', 'kpi_reason', 'หนี้เยอะ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('49', 'bank', 'ธนาคารกรุงเทพ', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('50', 'bank', 'ธนาคารกสิกรไทย', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('51', 'bank', 'ธนาคารกรุงไทย', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('52', 'bank', 'ธนาคารไทยพาณิชย์', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('53', 'bank', 'ธนาคารกรุงศรีอยุธยา', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('54', 'bank', 'ธนาคารออมสิน', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('55', 'bank', 'ธนาคารอาคารสงเคราะห์', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('56', 'bank', 'ธนาคารทหารไทยธนชาต', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('57', 'bank', 'ธนาคารยูโอบี', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('58', 'channel', 'Test', '1', '2026-05-04 19:03:47');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('59', 'document_type', 'บัตรประชาชน', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('60', 'document_type', 'ทะเบียนบ้าน', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('61', 'document_type', 'สลิปเงินเดือน', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('62', 'document_type', 'Statement ย้อนหลัง 6 เดือน', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('63', 'document_type', 'หนังสือรับรองเงินเดือน', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('64', 'document_type', 'สัญญาซื้อขาย', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('65', 'document_type', 'สำเนาโฉนด', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('66', 'document_type', 'ใบเปลี่ยนชื่อ-สกุล', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('67', 'document_type', 'ใบทะเบียนสมรส', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('68', 'document_type', 'สำเนาบัญชีธนาคาร', '1', '2026-05-05 14:35:46');
INSERT INTO `master_dropdowns` (`id`, `type`, `value`, `is_active`, `created_at`) VALUES ('69', 'document_type', 'อื่นๆ', '1', '2026-05-05 14:35:46');

DROP TABLE IF EXISTS `mortgages`;
CREATE TABLE `mortgages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `mortgage_date` date DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `account_name` varchar(150) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `approved_amount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `mortgages_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `mortgages` (`id`, `case_id`, `mortgage_date`, `bank_name`, `account_name`, `account_number`, `approved_amount`, `created_at`) VALUES ('1', '1', '2026-05-15', 'ธนาคารกรุงไทย', 'สมชาย ใจดี', '123-456-7890', '2800000.00', '2026-05-04 10:35:00');
INSERT INTO `mortgages` (`id`, `case_id`, `mortgage_date`, `bank_name`, `account_name`, `account_number`, `approved_amount`, `created_at`) VALUES ('2', '2', '2026-05-20', 'ธนาคารออมสิน', 'สมหญิง รักดี', '234-567-8901', '5000000.00', '2026-05-03 14:35:00');
INSERT INTO `mortgages` (`id`, `case_id`, `mortgage_date`, `bank_name`, `account_name`, `account_number`, `approved_amount`, `created_at`) VALUES ('3', '9', '2026-05-30', 'ธนาคารกรุงเทพ', 'BBL', '1123456789', '5000000.00', '2026-05-05 14:40:12');

DROP TABLE IF EXISTS `pre_approvals`;
CREATE TABLE `pre_approvals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) DEFAULT NULL,
  `status` enum('processing','approved','rejected') DEFAULT NULL,
  `approved_amount` decimal(12,2) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  CONSTRAINT `pre_approvals_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `pre_approvals` (`id`, `case_id`, `status`, `approved_amount`, `note`, `created_at`) VALUES ('1', '1', 'approved', '3200000.00', 'Pre-approve ผ่าน วงเงิน 3.2 ล้าน', '2026-04-29 10:35:00');
INSERT INTO `pre_approvals` (`id`, `case_id`, `status`, `approved_amount`, `note`, `created_at`) VALUES ('2', '2', 'approved', '5500000.00', 'อนุมัติวงเงินกู้ซื้อ', '2026-05-02 10:35:00');
INSERT INTO `pre_approvals` (`id`, `case_id`, `status`, `approved_amount`, `note`, `created_at`) VALUES ('3', '3', 'rejected', '0.00', 'รายได้ไม่เข้าเกณฑ์', '2026-05-01 10:35:00');
INSERT INTO `pre_approvals` (`id`, `case_id`, `status`, `approved_amount`, `note`, `created_at`) VALUES ('4', '5', 'processing', NULL, 'กำลังตรวจสอบเอกสาร', '2026-05-04 10:35:00');
INSERT INTO `pre_approvals` (`id`, `case_id`, `status`, `approved_amount`, `note`, `created_at`) VALUES ('5', '10', 'rejected', '3500000.00', 'test', '2026-05-05 14:26:36');
INSERT INTO `pre_approvals` (`id`, `case_id`, `status`, `approved_amount`, `note`, `created_at`) VALUES ('6', '9', 'processing', '0.00', '', '2026-05-05 14:39:16');

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `zone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('1', 'The Metro สุขุมวิท', '3500000.00', 'กรุงเทพ-ตะวันออก', '2026-01-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('2', 'Supalai พหลโยธิน', '5200000.00', 'กรุงเทพ-เหนือ', '2026-01-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('3', 'Lumpini พระราม 2', '1900000.00', 'สมุทรปราการ', '2026-01-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('4', 'Ideo รัชโยธิน', '4500000.00', 'กรุงเทพ-กลาง', '2026-02-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('5', 'Ashton จุฬา', '7000000.00', 'กรุงเทพ-กลาง', '2026-02-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('6', 'Condo U รังสิต', '1500000.00', 'ปทุมธานี', '2026-03-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('7', 'Niche Mono บางนา', '2200000.00', 'สมุทรปราการ', '2026-03-01 07:00:00');
INSERT INTO `projects` (`id`, `name`, `price`, `zone`, `created_at`) VALUES ('8', 'The Tree เตาปูน', '3800000.00', 'กรุงเทพ-เหนือ', '2026-04-01 07:00:00');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin_page','kpi','support','admin') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES ('1', 'คุณสมศรี Admin Page', 'somsri@company.com', '123456', 'admin_page', '2026-01-01 07:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES ('2', 'คุณสมหมาย Admin Page 2', 'sommai@company.com', '123456', 'admin_page', '2026-01-01 07:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES ('3', 'คุณวิชาญ KPI', 'wichan@company.com', '123456', 'kpi', '2026-01-01 07:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES ('4', 'คุณมานะ Support', 'mana@company.com', '123456', 'support', '2026-01-01 07:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES ('5', 'คุณสุพจน์ Support 2', 'supoj@company.com', '123456', 'support', '2026-01-01 07:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES ('6', 'Admin ใหญ่', 'admin@company.com', 'admin123', 'admin', '2026-01-01 07:00:00');

