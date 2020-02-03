-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2019 at 04:59 PM
-- Server version: 5.7.28
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plantshe_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `webo2_deliverytimeslot_deliverytimeslot`
--

CREATE TABLE `webo2_deliverytimeslot_deliverytimeslot` (
  `entity_id` int(11) NOT NULL COMMENT 'Deliverytimeslot ID',
  `title` varchar(255) NOT NULL COMMENT 'Title',
  `maximum_booking` varchar(255) NOT NULL COMMENT 'Max Deliveries Per Slot',
  `special_day` varchar(255) NOT NULL COMMENT 'Special Day',
  `interval` varchar(255) NOT NULL COMMENT 'Active Time',
  `cut_off_time` varchar(255) NOT NULL COMMENT 'Cut Off Time',
  `additional_cost` decimal(12,4) NOT NULL COMMENT 'Additional Cost',
  `mon` smallint(6) NOT NULL COMMENT 'Monday',
  `tue` smallint(6) NOT NULL COMMENT 'Tuesday',
  `wed` smallint(6) NOT NULL COMMENT 'Wednesday',
  `thu` smallint(6) NOT NULL COMMENT 'Thursday',
  `fri` smallint(6) NOT NULL COMMENT 'Friday',
  `sat` smallint(6) NOT NULL COMMENT 'Saturday',
  `sun` smallint(6) NOT NULL COMMENT 'Sunday',
  `specialday` smallint(6) NOT NULL COMMENT 'Special Day',
  `mon_extra_fee` decimal(12,4) NOT NULL COMMENT 'Monday Extra Fee',
  `tue_extra_fee` decimal(12,4) NOT NULL COMMENT 'Tuesday Extra Fee',
  `wed_extra_fee` decimal(12,4) NOT NULL COMMENT 'Wednesday Extra Fee',
  `thu_extra_fee` decimal(12,4) NOT NULL COMMENT 'Thursday Extra Fee',
  `fri_extra_fee` decimal(12,4) NOT NULL COMMENT 'Friday Extra Fee',
  `sat_extra_fee` decimal(12,4) NOT NULL COMMENT 'Saturday Extra Fee',
  `sun_extra_fee` decimal(12,4) NOT NULL COMMENT 'Sunday Extra Fee',
  `status` smallint(6) DEFAULT NULL COMMENT 'Enabled',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Deliverytimeslot Modification Time',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Deliverytimeslot Creation Time',
  `mon_spacial_date` text,
  `tue_spacial_date` text,
  `wed_spacial_date` text,
  `thu_spacial_date` text,
  `fri_spacial_date` text,
  `sat_spacial_date` text,
  `sun_spacial_date` text,
  `location_ids` text,
  `priority` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Deliverytimeslot Table';

--
-- Dumping data for table `webo2_deliverytimeslot_deliverytimeslot`
--

INSERT INTO `webo2_deliverytimeslot_deliverytimeslot` (`entity_id`, `title`, `maximum_booking`, `special_day`, `interval`, `cut_off_time`, `additional_cost`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `sun`, `specialday`, `mon_extra_fee`, `tue_extra_fee`, `wed_extra_fee`, `thu_extra_fee`, `fri_extra_fee`, `sat_extra_fee`, `sun_extra_fee`, `status`, `updated_at`, `created_at`, `mon_spacial_date`, `tue_spacial_date`, `wed_spacial_date`, `thu_spacial_date`, `fri_spacial_date`, `sat_spacial_date`, `sun_spacial_date`, `location_ids`, `priority`, `description`) VALUES
(1, 'Between 9am - 2.30pm', '300', '0', '09:00-14:30', '07:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-02-05 22:38:56', '2015-11-06 14:04:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 10, NULL),
(2, '12pm - 5pm', '300', '0', '12:00-17:00', '13:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 10.00, 0.00, 0.00, 0.00, 1, '2019-07-06 01:19:35', '2015-11-06 14:05:47', NULL, NULL, NULL, '2019-02-14', NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 20, 'Manhattan'),
(3, 'Between 5pm - 8pm', '300', '0', '17:00-20:00', '17:00', 0.00, 1, 1, 1, 1, 1, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2016-12-20 21:32:30', '2015-11-06 14:08:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,3,4,5,6,7', 30, NULL),
(4, 'Between 9am - 2.30pm', '300', '0', '09:00-14:30', '07:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2016-05-10 19:22:46', '2015-11-06 14:12:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 40, NULL),
(5, '12pm - 5pm', '300', '0', '12:00-17:00', '10:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 10.00, 0.00, 0.00, 0.00, 1, '2019-07-06 01:19:22', '2015-11-06 14:15:17', NULL, NULL, NULL, '2019-02-14', NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 50, 'Rest of NYC'),
(6, 'Between 5pm - 8pm', '300', '0', '17:00-20:00', '13:00', 0.00, 1, 1, 1, 1, 1, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2016-12-20 21:32:45', '2015-11-06 14:17:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,3,4,5,6,7', 60, NULL),
(7, '9am - 5pm', '200', '0', '09:00-17:00', '07:30', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5.0000, 1, '2019-05-06 18:03:48', '2015-11-06 14:19:40', NULL, NULL, NULL, NULL, NULL, NULL, '2019-05-12', '1,2,3,4,5,6,7,8', 18, 'Manhattan & rest of NYC'),
(8, '5pm - 8pm', '100', '0', '17:00-20:00', '10:00', 0.00, 1, 1, 1, 1, 1, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2017-03-14 04:11:14', '2015-11-06 14:21:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,3,4,6,7', 80, NULL),
(9, 'Between 12pm - 8pm', '200', '0', '12:00-20:00', '17:00', 0.00, 0, 0, 0, 0, 0, 0, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '2016-03-02 10:34:26', '2016-02-13 17:17:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 90, NULL),
(10, 'Between 12pm - 8pm', '200', '0', '12:00-20:00', '13:00', 0.00, 0, 0, 0, 0, 0, 0, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '2016-03-02 10:34:39', '2016-02-13 17:22:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 100, NULL),
(11, '9am - 3pm', '300', '0', '09:00-15:00', '07:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2017-11-17 00:18:48', '2017-01-30 20:39:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 11, NULL),
(12, '9am - 3pm', '300', '0', '09:00-15:00', '07:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2017-11-17 00:19:01', '2017-01-30 20:45:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 41, NULL),
(13, '5pm - 9pm', '300', '0', '17:00-21:00', '17:00', 0.00, 1, 1, 1, 1, 1, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-10-02 17:19:51', '2017-01-30 20:50:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,3,4,6', 31, 'Manhattan'),
(14, '5pm - 9pm', '300', '0', '17:00-21:00', '14:00', 0.00, 1, 1, 1, 1, 1, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-10-02 17:20:16', '2017-01-30 20:53:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,3,4,6,7', 61, 'Rest of NYC'),
(15, '9am - 5pm', '300', '0', '09:00-17:00', '00:30', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-03-07 21:04:19', '2017-01-30 21:05:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 12, NULL),
(16, '5pm - 9pm', '300', '0', '17:00-21:00', '12:00', 0.00, 1, 1, 1, 1, 1, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-05-06 18:38:28', '2017-01-30 21:07:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,3,4,6', 62, NULL),
(17, '9am - 12pm', '2', '0', '09:00-12:00', '00:01', 0.00, 1, 1, 1, 1, 1, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-07-12 22:19:08', '2017-03-03 15:32:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 1001, NULL),
(18, '12pm - 3pm', '2', '0', '12:00-15:00', '10:00', 0.00, 1, 1, 1, 1, 1, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-07-12 22:19:22', '2017-03-03 15:34:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 1002, NULL),
(19, '3pm - 6pm', '2', '0', '15:00-18:00', '13:00', 0.00, 1, 1, 1, 1, 1, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-07-12 22:19:37', '2017-03-03 15:37:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8', 1003, NULL),
(20, '9am - 1pm', '300', '0', '09:00-13:00', '11:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-10-25 22:25:06', '2018-02-09 22:37:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 17, 'Pick Up - 96'),
(21, '1pm - 5pm', '300', '0', '13:00-17:00', '15:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-10-25 22:17:01', '2018-02-09 22:38:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 27, 'Pick Up - 96'),
(22, '5pm - 7pm', '300', '0', '17:00-19:00', '18:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 1, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-08-08 04:07:53', '2018-02-09 22:39:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 37, 'Pick Up - 96'),
(23, '10am - 12pm', '200', '0', '10:00-12:00', '00:01', 0.00, 0, 0, 0, 0, 0, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-05-11 22:02:06', '2018-05-11 22:02:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8,9', 1000, NULL),
(24, '2pm - 6pm', '100', '0', '14:00-18:00', '12:30', 0.00, 0, 0, 0, 0, 0, 0, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-05-13 16:33:42', '2018-05-13 16:31:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5', 27, NULL),
(25, '7pm - 9pm', '100', '0', '19:00-21:00', '16:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-05-20 08:00:06', '2018-05-20 07:59:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2,3,4,5,6,7,8,9', 1005, NULL),
(26, '9am - 1pm', '300', '0', '09:00-13:00', '00:01', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-10-25 22:17:34', '2018-10-25 22:15:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 17, 'Pick Up - 87 / Prince'),
(27, '1pm - 5pm', '300', '0', '13:00-17:00', '10:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2018-10-25 22:20:41', '2018-10-25 22:20:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 27, 'Pick Up - 87 / Prince'),
(28, '5pm - 7pm', '300', '0', '17:00-19:00', '14:00', 0.00, 1, 1, 1, 1, 1, 1, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2019-08-08 04:08:20', '2018-10-25 22:22:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9', 37, 'Pick Up - 87 / Prince'),
(29, '12pm - 9pm', '300', '0', '12:00-21:00', '15:50', 0.00, 0, 0, 0, 0, 0, 0, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5.00, 1, '2019-05-12 23:56:02', '2019-05-06 18:09:34', NULL, NULL, NULL, NULL, NULL, NULL, '2019-05-12', '1,2,3,4,5,6,7,8', 19, 'Manhattan'),
(30, '12pm - 9pm', '300', '0', '12:00-21:00', '14:00', 0.00, 0, 0, 0, 0, 0, 0, 1, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6.00, 1, '2019-05-12 22:04:18', '2019-05-06 18:10:43', NULL, NULL, NULL, NULL, NULL, NULL, '2019-05-12', '1,2,3,4,5,6,7,8', 19, 'Rest of NYC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `webo2_deliverytimeslot_deliverytimeslot`
--
ALTER TABLE `webo2_deliverytimeslot_deliverytimeslot`
  ADD PRIMARY KEY (`entity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `webo2_deliverytimeslot_deliverytimeslot`
--
ALTER TABLE `webo2_deliverytimeslot_deliverytimeslot`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Deliverytimeslot ID', AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
