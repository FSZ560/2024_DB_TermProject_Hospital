### 一、 系統功能介紹

### 二、 E-R Diagram

<img src="er-diagram.svg" width="800" height="600" alt="E-R Diagram">

ps. 假設 staff 只有醫生

### 三、 系統中的表格定義與正規型式分析

#### 1. Person table

```sql
CREATE TABLE `person` (
  `person_id` char(7) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `id_card` char(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `gender` enum('M','F') NOT NULL,
  `birthday` date NOT NULL,
  PRIMARY KEY (`person_id`)
) 
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ last_name, first_name
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ id_card 
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ password
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ phone
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ address
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ gender
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ birthday
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ last_name, first_name
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ person_id
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ password
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ phone
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ address
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ gender
  &nbsp;&nbsp;&nbsp;&nbsp;  id_card $\rightarrow$ birthday
}

因為`person_id`代表一個特定的人，可決定唯一的`last_name`、`first_name`、`id_card`、`password`、`phone`、`address`、`gender`、`birthday`，且`person_id`是一個candidate key

`id_card`也能推到所有其他的任何屬性，且`id_card`是一個candidate key

$→$ 此表格符合3NF和BCNF。

#### 2. patient table

```sql
CREATE TABLE `patient` (
  `person_id` char(7) NOT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ height
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ weight
}

因為`patient`是`person`的子實體集，因此其 PK 就是 `person` 的 PK `person_id`，且`person_id` 代表一個特定的人，可決定唯一的`height`、`weight`，且`person_id`是一個candidate key

$→$ 此表格符合3NF和BCNF。

#### 3.staff table

```sql
CREATE TABLE `staff` (
  `person_id` char(7) NOT NULL,
  `department_id` char(7) NOT NULL,
  `salary` int(11) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ department_id
  &nbsp;&nbsp;&nbsp;&nbsp;  person_id $\rightarrow$ salary
}

因為`staff`是`person`的子實體集，因此其 PK 就是 `person` 的 PK `person_id`，且`person_id` 代表一個特定的人，可決定唯一的`height`、`weight`，且`person_id`是一個candidate key

$→$ 此表格符合3NF和BCNF。

#### 4. department table

```sql
CREATE TABLE `department` (
  `department_id` char(7) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  PRIMARY KEY (`department_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  department_id $\rightarrow$ department_name
  &nbsp;&nbsp;&nbsp;&nbsp;  department_name $\rightarrow$ department_id
}

因為`department_id`代表一個特定的部門，可決定唯一的`department_name`，
又`department_name`也能代表一個特定的部門，可決定唯一的`department_id`，且`department_id`、`department_name`都是candidate key

$→$ 此表格符合3NF和BCNF。

#### 5. clinic table

```sql
CREATE TABLE `clinic` (
  `clinic_id` char(7) NOT NULL,
  `clinic_date` date NOT NULL,
  `period` enum('morning','afternoon','evening') NOT NULL,
  `department_id` char(7) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `doctor_id` char(7) NOT NULL,
  PRIMARY KEY (`clinic_id`),
  KEY `department_id` (`department_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `clinic_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`),
  CONSTRAINT `clinic_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `staff` (`person_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  clinic_id $\rightarrow$ clinic_date
  &nbsp;&nbsp;&nbsp;&nbsp;  clinic_id $\rightarrow$ period
  &nbsp;&nbsp;&nbsp;&nbsp;  clinic_id $\rightarrow$ department_id
  &nbsp;&nbsp;&nbsp;&nbsp;  clinic_id $\rightarrow$ location
  &nbsp;&nbsp;&nbsp;&nbsp;  clinic_id $\rightarrow$ doctor_id
}

因為`clinic_id`代表一個特定的門診，可決定唯一的`clinic_date`、`period`、`department_id`、`location`、`doctor_id`，且`clinic_id`是一個candidate key

$→$ 此表格符合3NF和BCNF。

#### 6. appointment table

```sql
CREATE TABLE `appointment` (
  `appointment_id` char(7) NOT NULL,
  `sequence_number` int(10) unsigned NOT NULL,
  `clinic_id` char(7) NOT NULL,
  `patient_id` char(7) NOT NULL,
  `register_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`appointment_id`),
  KEY `clinic_id` (`clinic_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`clinic_id`),
  CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`person_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  appointment_id $\rightarrow$ sequence_number 
  &nbsp;&nbsp;&nbsp;&nbsp;  appointment_id $\rightarrow$ clinic_id 
  &nbsp;&nbsp;&nbsp;&nbsp;  appointment_id $\rightarrow$ patient_id 
  &nbsp;&nbsp;&nbsp;&nbsp;  appointment_id $\rightarrow$ register_time 
}

因為`appointment_id`代表一個特定的掛號申請，可決定唯一的`sequence_number`、`clinic_id`、`patient_id`、`register_time`，且`appointment_id`是一個candidate key

$→$ 此表格符合3NF和BCNF。

#### 7. medical_record table

```sql
CREATE TABLE `medical_record` (
  `record_id` char(7) NOT NULL,
  `patient_id` char(7) NOT NULL,
  `clinic_id` char(7) NOT NULL,
  PRIMARY KEY (`record_id`),
  KEY `patient_id` (`patient_id`),
  KEY `clinic_id` (`clinic_id`),
  CONSTRAINT `medical_record_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`person_id`),
  CONSTRAINT `medical_record_ibfk_2` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`clinic_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  record_id $\rightarrow$ patient_id 
  &nbsp;&nbsp;&nbsp;&nbsp;  record_id $\rightarrow$ clinic_id 
}

因為`record_id`代表一個特定的掛號申請，可決定唯一的`patient_id`、`clinic_id`，且`record_id`是一個candidate key

$→$ 此表格符合3NF和BCNF。

#### 8. medical_certificate table

```sql
CREATE TABLE `medical_certificate` (
  `certificate_id` char(7) NOT NULL,
  `record_id` char(7) NOT NULL,
  `prescription` text DEFAULT NULL,
  PRIMARY KEY (`certificate_id`),
  KEY `record_id` (`record_id`),
  CONSTRAINT `medical_certificate_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `medical_record` (`record_id`)
)
```
F = {
  &nbsp;&nbsp;&nbsp;&nbsp;  certificate_id $\rightarrow$ record_id 
  &nbsp;&nbsp;&nbsp;&nbsp;  certificate_id $\rightarrow$ prescription 
}

因為`certificate_id`代表一個特定的掛號申請，可決定唯一的`patient_id`、`clinic_id`，且`certificate_id`是一個candidate key

$→$ 此表格符合3NF和BCNF。

### 四、 符合正規化和 ER 圖的表格定義