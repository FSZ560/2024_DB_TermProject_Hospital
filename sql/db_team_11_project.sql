-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-12-29 16:09:25
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `db_team_11_project`
--

DELIMITER $$
--
-- 函式
--
CREATE DEFINER=`root`@`localhost` FUNCTION `f_get_doctor_clinic_count` (`in_doctor_id` CHAR(7)) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;
    
    SELECT COUNT(*) INTO total
    FROM clinic 
    WHERE doctor_id = in_doctor_id;
    
    RETURN total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 資料表結構 `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` char(7) NOT NULL,
  `sequence_number` int(10) UNSIGNED NOT NULL,
  `clinic_id` char(7) NOT NULL,
  `patient_id` char(7) NOT NULL,
  `register_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `sequence_number`, `clinic_id`, `patient_id`, `register_time`) VALUES
('AP00002', 1, 'CL00002', 'PE00010', '2024-12-29 00:09:25'),
('AP00004', 3, 'CL00003', 'PE00011', '2024-12-29 00:14:02'),
('AP00005', 2, 'CL00002', 'PE00007', '2024-12-29 00:16:33'),
('AP00006', 4, 'CL00003', 'PE00006', '2024-12-29 01:01:35');

-- --------------------------------------------------------

--
-- 資料表結構 `clinic`
--

CREATE TABLE `clinic` (
  `clinic_id` char(7) NOT NULL,
  `clinic_date` date NOT NULL,
  `period` enum('morning','afternoon','evening') NOT NULL,
  `department_id` char(7) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `doctor_id` char(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `clinic`
--

INSERT INTO `clinic` (`clinic_id`, `clinic_date`, `period`, `department_id`, `location`, `doctor_id`) VALUES
('CL00001', '2024-12-20', 'morning', 'DE00001', '第一醫療大樓 101 室', 'PE00001'),
('CL00002', '2024-12-25', 'evening', 'DE00005', '第二醫療大樓 610 室', 'PE00001'),
('CL00003', '2024-12-26', 'afternoon', 'DE00006', '第三醫療大樓 315 室', 'PE00001'),
('CL00004', '2025-01-16', 'evening', 'DE00004', '第一醫療大樓 709 室', 'PE00003'),
('CL00005', '2025-10-10', 'afternoon', 'DE00004', '第三醫療大樓 1146 室', 'PE00003');

-- --------------------------------------------------------

--
-- 資料表結構 `department`
--

CREATE TABLE `department` (
  `department_id` char(7) NOT NULL,
  `department_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `department`
--

INSERT INTO `department` (`department_id`, `department_name`) VALUES
('DE00001', '眼科'),
('DE00002', '牙科'),
('DE00003', '內科'),
('DE00004', '外科'),
('DE00005', '兒科'),
('DE00006', '家醫科');

-- --------------------------------------------------------

--
-- 資料表結構 `medical_certificate`
--

CREATE TABLE `medical_certificate` (
  `certificate_id` char(7) NOT NULL,
  `record_id` char(7) NOT NULL,
  `prescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `medical_certificate`
--

INSERT INTO `medical_certificate` (`certificate_id`, `record_id`, `prescription`) VALUES
('CE00001', 'RE00001', '近視\r\n使用散瞳劑治療'),
('CE00002', 'RE00001', '老花眼\r\n需使用眼鏡'),
('CE00003', 'RE00001', '乾眼症\r\n開立人工淚液'),
('CE00005', 'RE00002', '針眼\r\n開立 Chloramphenicol Eye Drops'),
('CE00006', 'RE00003', '急性支氣管炎\r\n開立 IBU TABLET \"ROOT\"');

-- --------------------------------------------------------

--
-- 資料表結構 `medical_record`
--

CREATE TABLE `medical_record` (
  `record_id` char(7) NOT NULL,
  `patient_id` char(7) NOT NULL,
  `clinic_id` char(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `medical_record`
--

INSERT INTO `medical_record` (`record_id`, `patient_id`, `clinic_id`) VALUES
('RE00001', 'PE00005', 'CL00001'),
('RE00002', 'PE00007', 'CL00001'),
('RE00003', 'PE00005', 'CL00003');

-- --------------------------------------------------------

--
-- 資料表結構 `patient`
--

CREATE TABLE `patient` (
  `person_id` char(7) NOT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `patient`
--

INSERT INTO `patient` (`person_id`, `height`, `weight`) VALUES
('PE00003', 162.50, 55.00),
('PE00005', 170.00, 62.00),
('PE00006', 158.50, 48.50),
('PE00007', 175.00, 70.00),
('PE00008', 170.00, NULL),
('PE00009', NULL, 55.00),
('PE00010', 168.00, 65.00),
('PE00011', 160.00, 50.00);

-- --------------------------------------------------------

--
-- 資料表結構 `person`
--

CREATE TABLE `person` (
  `person_id` char(7) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `id_card` char(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `gender` enum('M','F') NOT NULL,
  `birthday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `person`
--

INSERT INTO `person` (`person_id`, `last_name`, `first_name`, `id_card`, `password`, `phone`, `address`, `gender`, `birthday`) VALUES
('PE00001', '陳', '大文', 'A123456789', '$2y$10$lXEtoT4LMtJHBIm/sFK7B.MTZ5qK35QI0j2P/9dQComc1cA/SvuRa', '0912-345-678', '台北市信義區信義路五段7號', 'M', '1990-03-15'),
('PE00002', '林', '小美', 'B234567890', '$2y$10$Cn9DtEFLP2GLtC90.XKP.OtOpc8c/Rpt4.QVYdDwkOV51EsNQ/bJW', '0923-456-789', '台中市西屯區台灣大道三段99號', 'F', '1995-07-22'),
('PE00003', '王', '建國', 'C187654321', '$2y$10$nRa8iy9WL8G9HFdqtgSssOOqLNRL0rH.GPANGcS8REzJyeJPdw16W', '0934-567-890', NULL, 'M', '1988-12-01'),
('PE00004', '張', '淑華', 'D198765432', '$2y$10$nlO7iQupd2aok54CpzHBUOkUjv0HlrjXVfUPRdHntcq.i3wdoXv12', '0958-125-498', '高雄市前金區中正路100號', 'F', '1992-05-30'),
('PE00005', '李', '志明', 'E165432109', '$2y$10$0mciJZ/XmwOWtJTanGOdwuOn3t7H1OApJcKsYcDgfYKlLlI19cP06', '0912-987-654', '東京都世田谷区北沢2-6-10仙田ビルB1', 'M', '2000-01-01'),
('PE00006', '黃', '雅琪', 'F143219876', '$2y$10$IvoGOLqx8VtSbcFUbvyKyO4wTptDRiw4j1plVNUr41jLKa5m55j.u', '0965-478-123', NULL, 'F', '1997-09-18'),
('PE00007', '劉', '俊傑', 'G154326789', '$2y$10$GlgdN9cTiwuymYc48ewjg.hOrU2c3c/tubBp15yOPpz3LrNEIqb5S', '0978-901-234', '新竹市東區光復路二段101號', 'M', '1993-11-25'),
('PE00008', '吳', '家豪', 'H176543210', '$2y$10$2xicrxjZO0Sg4hQPrbEYoO6kG3qN9QxTRJ06KUtELz6Zd8td/5a2G', '0989-012-345', '桃園市中壢區中大路300號', 'M', '1991-08-14'),
('PE00009', '周', '美玲', 'I187654321', '$2y$10$Y.CVeYIghgfQOpJ3xfpSKesrxYFZ2sBery53A/HxkB7MRkr1w69u.', '0912-345-987', NULL, 'F', '1994-04-27'),
('PE00010', '謝', '明宏', 'J198765432', '$2y$10$wNw6zPR2ypR3jACsnt0k5Oit/WCrTVK2fltxWVMbqD7hs87jOi4Ju', '0967-890-123', '台南市東區大學路1號', 'M', '2015-10-24'),
('PE00011', '王', '大明', 'L123456789', '$2y$10$hG/E/HGIVVPFjJRQbotYDeeRR0cAkpPhXwGrRmbK/hfX5ESzOqn3i', '0912-345-678', '', 'M', '1900-01-01');

--
-- 觸發器 `person`
--
DELIMITER $$
CREATE TRIGGER `phone_insert_tri` BEFORE INSERT ON `person` FOR EACH ROW BEGIN
    IF NEW.phone REGEXP '^09[0-9]{8}$' THEN
        SET NEW.phone = CONCAT(
            SUBSTRING(NEW.phone, 1, 4),
            '-',
            SUBSTRING(NEW.phone, 5, 3),
            '-',
            SUBSTRING(NEW.phone, 8)
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `phone_update_tri` BEFORE UPDATE ON `person` FOR EACH ROW BEGIN
    IF NEW.phone REGEXP '^09[0-9]{8}$' THEN
        SET NEW.phone = CONCAT(
            SUBSTRING(NEW.phone, 1, 4),
            '-',
            SUBSTRING(NEW.phone, 5, 3),
            '-',
            SUBSTRING(NEW.phone, 8)
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 資料表結構 `staff`
--

CREATE TABLE `staff` (
  `person_id` char(7) NOT NULL,
  `department_id` char(7) NOT NULL,
  `salary` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `staff`
--

INSERT INTO `staff` (`person_id`, `department_id`, `salary`) VALUES
('PE00001', 'DE00001', 150000),
('PE00002', 'DE00006', NULL),
('PE00003', 'DE00002', 250000);

-- --------------------------------------------------------

--
-- 替換檢視表以便查看 `v_medical_record_detail`
-- (請參考以下實際畫面)
--
CREATE TABLE `v_medical_record_detail` (
`record_id` char(7)
,`clinic_id` char(7)
,`clinic_date` date
,`period` enum('morning','afternoon','evening')
,`last_name` varchar(20)
,`first_name` varchar(20)
,`gender` enum('M','F')
,`birthday` date
,`department_name` varchar(50)
,`doctor_id` char(7)
);

-- --------------------------------------------------------

--
-- 檢視表結構 `v_medical_record_detail`
--
DROP TABLE IF EXISTS `v_medical_record_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_medical_record_detail`  AS SELECT `mr`.`record_id` AS `record_id`, `mr`.`clinic_id` AS `clinic_id`, `c`.`clinic_date` AS `clinic_date`, `c`.`period` AS `period`, `p`.`last_name` AS `last_name`, `p`.`first_name` AS `first_name`, `p`.`gender` AS `gender`, `p`.`birthday` AS `birthday`, `d`.`department_name` AS `department_name`, `c`.`doctor_id` AS `doctor_id` FROM ((((`medical_record` `mr` join `clinic` `c` on(`mr`.`clinic_id` = `c`.`clinic_id`)) join `patient` `pt` on(`mr`.`patient_id` = `pt`.`person_id`)) join `person` `p` on(`pt`.`person_id` = `p`.`person_id`)) join `department` `d` on(`c`.`department_id` = `d`.`department_id`)) ;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `clinic_id` (`clinic_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- 資料表索引 `clinic`
--
ALTER TABLE `clinic`
  ADD PRIMARY KEY (`clinic_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- 資料表索引 `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- 資料表索引 `medical_certificate`
--
ALTER TABLE `medical_certificate`
  ADD PRIMARY KEY (`certificate_id`),
  ADD KEY `record_id` (`record_id`);

--
-- 資料表索引 `medical_record`
--
ALTER TABLE `medical_record`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- 資料表索引 `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`person_id`);

--
-- 資料表索引 `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`person_id`);

--
-- 資料表索引 `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `department_id` (`department_id`);

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`clinic_id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`person_id`);

--
-- 資料表的限制式 `clinic`
--
ALTER TABLE `clinic`
  ADD CONSTRAINT `clinic_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`),
  ADD CONSTRAINT `clinic_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `staff` (`person_id`);

--
-- 資料表的限制式 `medical_certificate`
--
ALTER TABLE `medical_certificate`
  ADD CONSTRAINT `medical_certificate_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `medical_record` (`record_id`);

--
-- 資料表的限制式 `medical_record`
--
ALTER TABLE `medical_record`
  ADD CONSTRAINT `medical_record_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`person_id`),
  ADD CONSTRAINT `medical_record_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`clinic_id`);

--
-- 資料表的限制式 `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);

--
-- 資料表的限制式 `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
