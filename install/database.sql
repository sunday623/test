-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- โฮสต์: localhost
-- เวอร์ชั่นของเซิร์ฟเวอร์: 5.1.73-log
-- รุ่นของ PHP: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_category`
--

CREATE TABLE `{prefix}_category` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `topic` text COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `{prefix}_category`
--

INSERT INTO `{prefix}_category` (`id`, `type`, `category_id`, `topic`, `color`, `published`) VALUES
(1, 'position', 1, 'a:2:{s:2:"th";s:57:"ผู้อำนวยการโรงเรียน";s:2:"en";s:8:"Director";}', NULL, 1),
(2, 'position', 2, 'a:2:{s:2:"th";s:66:"รองผู้อำนวยการโรงเรียน";s:2:"en";s:13:"Vice-Director";}', NULL, 1),
(3, 'position', 3, 'a:2:{s:2:"th";s:9:"ครู";s:2:"en";s:7:"Teacher";}', NULL, 1),
(4, 'position', 4, 'a:2:{s:2:"th";s:30:"ครูผู้ช่วย";s:2:"en";s:17:"Assistant teacher";}', NULL, 1),
(5, 'class', 3, 'a:2:{s:2:"en";s:7:"Class 3";s:2:"th";s:47:"มัธยมศึกษาปีที่ 3";}', NULL, 1),
(6, 'class', 2, 'a:2:{s:2:"en";s:7:"Class 2";s:2:"th";s:47:"มัธยมศึกษาปีที่ 2";}', NULL, 1),
(7, 'class', 1, 'a:2:{s:2:"en";s:7:"Class 1";s:2:"th";s:47:"มัธยมศึกษาปีที่ 1";}', NULL, 1),
(8, 'repairstatus', 0, 'แจ้งซ่อม', '#660000', 1),
(9, 'repairstatus', 0, 'กำลังดำเนินการ', '#339900', 1),
(10, 'repairstatus', 0, 'รออะไหล่', '#FF3300', 1),
(11, 'room', 9, 'a:2:{s:2:"th";s:21:"ทวิภาคี";s:2:"en";s:21:"ทวิภาคี";}', NULL, 1),
(12, 'room', 4, 'a:2:{s:2:"th";s:1:"4";s:2:"en";s:1:"4";}', NULL, 1),
(13, 'room', 3, 'a:2:{s:2:"th";s:1:"3";s:2:"en";s:1:"3";}', NULL, 1),
(14, 'room', 2, 'a:2:{s:2:"th";s:1:"2";s:2:"en";s:1:"2";}', NULL, 1),
(15, 'room', 1, 'a:2:{s:2:"th";s:1:"1";s:2:"en";s:1:"1";}', NULL, 1),
(16, 'department', 2, 'a:2:{s:2:"en";s:36:"ช่างกลโรงงาน";s:2:"th";s:36:"ช่างกลโรงงาน";}', NULL, 1),
(17, 'department', 1, 'a:2:{s:2:"en";s:24:"ช่างยนต์";s:2:"th";s:24:"ช่างยนต์";}', NULL, 1),
(18, 'class', 4, 'a:2:{s:2:"en";s:7:"Class 4";s:2:"th";s:47:"มัธยมศึกษาปีที่ 4";}', NULL, 1),
(19, 'class', 5, 'a:2:{s:2:"en";s:7:"Class 5";s:2:"th";s:47:"มัธยมศึกษาปีที่ 5";}', NULL, 1),
(20, 'class', 6, 'a:2:{s:2:"en";s:7:"Class 6";s:2:"th";s:47:"มัธยมศึกษาปีที่ 6";}', NULL, 1),
(21, 'term', 1, 'a:2:{s:2:"en";s:1:"1";s:2:"th";s:14:"เทอม 1";}', NULL, 1),
(22, 'term', 2, 'a:2:{s:2:"en";s:1:"2";s:2:"th";s:14:"เทอม 2";}', NULL, 1),
(23, 'term', 3, 'a:2:{s:2:"en";s:6:"Summer";s:2:"th";s:21:"ฤดูร้อน";}', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_course`
--

CREATE TABLE `{prefix}_course` (
  `id` int(11) NOT NULL,
  `course_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `course_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `period` int(11) NOT NULL,
  `credit` decimal(2,1) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `year` int(4) NOT NULL,
  `term` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `{prefix}_course`
--

INSERT INTO `{prefix}_course` (`id`, `course_name`, `course_code`, `teacher_id`, `class`, `period`, `credit`, `type`, `year`, `term`) VALUES
(1, 'วิทยาศาสตร์', 'ว21101', 0, 1, 0, '1.5', 1, 0, 0),
(2, 'สังคมศึกษา', 'ส21101', 0, 1, 0, '1.5', 1, 0, 0),
(3, 'ประวัติศาสตร์ 1', 'ส21103', 0, 1, 0, '0.5', 1, 0, 0),
(4, 'สุขศึกษา 1', 'พ21101', 0, 1, 0, '0.5', 1, 0, 0),
(5, 'ศิลปะ', 'ศ21101', 0, 1, 0, '1.0', 1, 0, 0),
(6, 'การงานอาชีพและเทคโนโลยี', 'ง21101', 0, 1, 0, '0.5', 1, 0, 0),
(7, 'การงานอาชีพและเทคโนโลยี', 'ง21102', 0, 1, 0, '0.5', 1, 0, 0),
(8, 'ภาษาอังกฤษ', 'อ21101', 0, 1, 0, '1.5', 1, 0, 0),
(9, 'ภาษาไทย', 'ท21101', 0, 1, 0, '1.5', 1, 0, 0),
(10, 'สุขศึกษา 1', 'พ21103', 0, 1, 0, '0.5', 1, 0, 0),
(11, 'คอมพิวเตอร์ 1', 'ง20247', 0, 1, 0, '1.0', 1, 0, 0),
(12, 'พิมพืดีดไทย', 'ง20201', 0, 1, 0, '0.5', 1, 0, 0),
(13, 'ฟัง - พูด', 'อ20201', 0, 1, 0, '0.5', 1, 0, 0),
(14, 'ภาษาจีน1', 'จ20201', 0, 1, 0, '0.5', 1, 0, 0),
(15, 'ภาษาไทย', 'ท21101', 0, 1, 0, '1.5', 1, 2556, 1),
(16, 'คณิตศาสตร์', 'ค21101', 0, 1, 0, '1.5', 1, 2556, 1),
(17, 'วิทยาศาสตร์', 'ว21101', 0, 1, 0, '1.5', 1, 2556, 1),
(18, 'สังคมศึกษา', 'ส21101', 0, 1, 0, '1.5', 1, 2556, 1),
(19, 'ประวัติศาสตร์ 1', 'ส21103', 0, 1, 0, '0.5', 1, 2556, 1),
(20, 'สุขศึกษา 1', 'พ21101', 0, 1, 0, '0.5', 1, 2556, 1),
(21, 'สุขศึกษา 1', 'พ21103', 0, 1, 0, '0.5', 1, 2556, 1),
(22, 'ศิลปะ', 'ศ21101', 0, 1, 0, '1.0', 1, 2556, 1),
(23, 'การงานอาชีพและเทคโนโลยี', 'ง21101', 0, 1, 0, '0.5', 1, 2556, 1),
(24, 'คอมพิวเตอร์ 1', 'ง20247', 0, 1, 0, '1.0', 1, 2556, 1),
(25, 'การงานอาชีพและเทคโนโลยี', 'ง21102', 0, 1, 0, '0.5', 1, 2556, 1),
(26, 'พิมพืดีดไทย', 'ง20201', 0, 1, 0, '0.5', 1, 2556, 1),
(27, 'ฟัง - พูด', 'อ20201', 0, 1, 0, '0.5', 1, 2556, 1),
(28, 'ภาษาจีน1', 'จ20201', 0, 1, 0, '0.5', 1, 2556, 1),
(29, 'คณิตศาสตร์', 'ค21101', 0, 1, 0, '1.5', 1, 0, 1),
(30, 'ภาษาอังกฤษ', 'อ21101', 0, 1, 0, '1.5', 1, 2556, 1);

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_edocument`
--

CREATE TABLE `{prefix}_edocument` (
  `id` int(11) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `reciever` text COLLATE utf8_unicode_ci NOT NULL,
  `last_update` int(11) UNSIGNED NOT NULL,
  `document_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `detail` text COLLATE utf8_unicode_ci NOT NULL,
  `topic` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ext` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `size` double UNSIGNED NOT NULL,
  `file` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_edocument_download`
--

CREATE TABLE `{prefix}_edocument_download` (
  `id` int(10) UNSIGNED NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `downloads` int(10) UNSIGNED NOT NULL,
  `last_update` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_grade`
--

CREATE TABLE `{prefix}_grade` (
  `id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `course_id` int(11) NOT NULL,
  `number` tinyint(3) NOT NULL,
  `room` int(11) NOT NULL,
  `grade` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_personnel`
--

CREATE TABLE `{prefix}_personnel` (
  `id` int(11) UNSIGNED NOT NULL,
  `position` int(11) UNSIGNED NOT NULL,
  `department` int(11) NOT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL,
  `custom` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `id_card` varchar(13) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_student`
--

CREATE TABLE `{prefix}_student` (
  `id` int(11) NOT NULL,
  `student_id` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `number` tinyint(3) NOT NULL,
  `id_card` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_user`
--

CREATE TABLE `{prefix}_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `permission` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sex` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_card` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expire_date` date NOT NULL,
  `address` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinceID` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visited` int(11) UNSIGNED DEFAULT '0',
  `lastvisited` int(11) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `session_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `picture` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `fb` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{prefix}_user` (`id`, `username`, `salt`, `password`, `status`, `permission`, `name`) VALUES
(1, 'admin@localhost', 'admin@localhost', 'b620e8b83d7fcf7278148d21b088511917762014', 1, 'can_config,can_handle_all_edocument,can_upload_edocument', 'แอดมิน');
--
-- Indexes for dumped tables
--

--
-- Indexes for table `{prefix}_category`
--
ALTER TABLE `{prefix}_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_course`
--
ALTER TABLE `{prefix}_course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_edocument`
--
ALTER TABLE `{prefix}_edocument`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_edocument_download`
--
ALTER TABLE `{prefix}_edocument_download`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_grade`
--
ALTER TABLE `{prefix}_grade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_personnel`
--
ALTER TABLE `{prefix}_personnel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_card` (`id_card`);

--
-- Indexes for table `{prefix}_student`
--
ALTER TABLE `{prefix}_student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_card` (`id_card`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `{prefix}_user`
--
ALTER TABLE `{prefix}_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `{prefix}_category`
--
ALTER TABLE `{prefix}_category`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `{prefix}_course`
--
ALTER TABLE `{prefix}_course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `{prefix}_edocument`
--
ALTER TABLE `{prefix}_edocument`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `{prefix}_edocument_download`
--
ALTER TABLE `{prefix}_edocument_download`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `{prefix}_grade`
--
ALTER TABLE `{prefix}_grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `{prefix}_user`
--
ALTER TABLE `{prefix}_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
