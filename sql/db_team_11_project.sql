-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-12-17 08:00:23
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

-- --------------------------------------------------------

--
-- 資料表結構 `medical_record`
--

CREATE TABLE `medical_record` (
  `record_id` char(7) NOT NULL,
  `patient_id` char(7) NOT NULL,
  `clinic_id` char(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
('PE00010', 168.00, 65.00);

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
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `gender` enum('M','F') DEFAULT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `person`
--

INSERT INTO `person` (`person_id`, `last_name`, `first_name`, `id_card`, `password`, `phone`, `address`, `gender`, `birthday`) VALUES
('PE00001', '陳', '大文', 'A123456789', '$2y$10$lXEtoT4LMtJHBIm/sFK7B.MTZ5qK35QI0j2P/9dQComc1cA/SvuRa', '0912345678', '台北市信義區信義路五段7號', 'M', '1990-03-15'),
('PE00002', '林', '小美', 'B234567890', '$2y$10$Cn9DtEFLP2GLtC90.XKP.OtOpc8c/Rpt4.QVYdDwkOV51EsNQ/bJW', '0923456789', '台中市西屯區台灣大道三段99號', 'F', '1995-07-22'),
('PE00003', '王', '建國', 'C187654321', '$2y$10$nRa8iy9WL8G9HFdqtgSssOOqLNRL0rH.GPANGcS8REzJyeJPdw16W', '0934567890', NULL, 'M', '1988-12-01'),
('PE00004', '張', '淑華', 'D198765432', '$2y$10$nlO7iQupd2aok54CpzHBUOkUjv0HlrjXVfUPRdHntcq.i3wdoXv12', NULL, '高雄市前金區中正路100號', 'F', '1992-05-30'),
('PE00005', '李', '志明', 'E165432109', '$2y$10$0mciJZ/XmwOWtJTanGOdwuOn3t7H1OApJcKsYcDgfYKlLlI19cP06', '0956789012', NULL, 'M', NULL),
('PE00006', '黃', '雅琪', 'F143219876', '$2y$10$IvoGOLqx8VtSbcFUbvyKyO4wTptDRiw4j1plVNUr41jLKa5m55j.u', NULL, NULL, 'F', '1997-09-18'),
('PE00007', '劉', '俊傑', 'G154326789', '$2y$10$GlgdN9cTiwuymYc48ewjg.hOrU2c3c/tubBp15yOPpz3LrNEIqb5S', '0978901234', '新竹市東區光復路二段101號', NULL, '1993-11-25'),
('PE00008', '吳', '家豪', 'H176543210', '$2y$10$2xicrxjZO0Sg4hQPrbEYoO6kG3qN9QxTRJ06KUtELz6Zd8td/5a2G', '0989012345', '桃園市中壢區中大路300號', 'M', '1991-08-14'),
('PE00009', '周', '美玲', 'I187654321', '$2y$10$Y.CVeYIghgfQOpJ3xfpSKesrxYFZ2sBery53A/HxkB7MRkr1w69u.', NULL, NULL, 'F', '1994-04-27'),
('PE00010', '謝', '明宏', 'J198765432', '$2y$10$wNw6zPR2ypR3jACsnt0k5Oit/WCrTVK2fltxWVMbqD7hs87jOi4Ju', '0967890123', '台南市東區大學路1號', NULL, NULL);

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
