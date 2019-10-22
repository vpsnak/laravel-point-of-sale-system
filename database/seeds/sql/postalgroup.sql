-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2019 at 02:47 PM
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
-- Table structure for table `postalgroup`
--

CREATE TABLE `postalgroup` (
  `postalgroup_id` int(11) UNSIGNED NOT NULL,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL,
  `group_price` float(10,2) NOT NULL DEFAULT '0.00',
  `country` varchar(255) NOT NULL DEFAULT '0',
  `free_shipping` varchar(255) NOT NULL DEFAULT '0',
  `minimum_amount` varchar(255) NOT NULL DEFAULT '0',
  `cutoff_time` time NOT NULL,
  `free_next_days` int(11) NOT NULL DEFAULT '0',
  `close_next_day_time` time DEFAULT NULL,
  `location_ids` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `postalgroup`
--

INSERT INTO `postalgroup` (`postalgroup_id`, `group_name`, `status`, `group_price`, `country`, `free_shipping`, `minimum_amount`, `cutoff_time`, `free_next_days`, `close_next_day_time`, `location_ids`) VALUES
(1, 'USA - NY - Manhattan - H10', 1, 10.00, '', '1', '100', '18:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(2, 'USA - NY - Manhattan - H15', 1, 15.00, '', '1', '100', '17:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(3, 'USA - NY - Manhattan - H15', 1, 15.00, '', '1', '100', '17:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(4, 'USA - NY - Brooklyn - H19', 1, 19.00, '', '2', '0', '15:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(5, 'USA - NY - Bronx - H19', 1, 19.00, '', '2', '0', '15:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(6, 'USA - NY - Staten Island - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(7, 'USA - NY - Queens - Astoria - H19', 1, 19.00, '', '2', '0', '15:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(8, 'USA - NY - Queens - H19', 1, 19.00, '', '2', '0', '15:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(9, 'USA - NY - Queens - Jamaica - H30', 1, 30.00, '', '2', '0', '15:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(10, 'USA - NY - Qeens - Rockaway - H30', 1, 30.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(11, 'USA - NY - Yonkers - H30', 1, 30.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(12, 'USA - NY - New Rochelle - H30', 1, 30.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(19, 'USA - AL - Alabama - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(20, 'USA - AK - Alaska - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(21, 'USA - AZ - Arizona - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(22, 'USA - AR - Arkansas - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(23, 'USA - CA - California - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(24, 'USA - CO - Colorado - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(25, 'USA - CT - Connecticut - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(26, 'USA - DE - Delaware - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(27, 'USA - FL - Florida - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(28, 'USA - GA - Georgia - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(29, 'USA - HI - Hawaii - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(30, 'USA - ID - Idaho -W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(31, 'USA - IL - Illinois - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(32, 'USA - IN - Indiana - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(33, 'USA - IA - Iowa - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(34, 'USA - KS - Kansas - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(35, 'USA - KY - Kentucky - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(36, 'USA - LA - Louisiana - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(37, 'USA - ME - Maine - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(38, 'USA - MD - Maryland - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(39, 'USA - MA - Massachusetts - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(40, 'USA - MI - Michigan - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(41, 'USA - MN - Minnesota - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(42, 'USA - MS - Mississippi - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(43, 'USA - MO - Missouri - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(44, 'USA - MT - Montana - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(45, 'USA - NE - Nebraska - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(46, 'USA - NV - Nevada - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(47, 'USA - NH - New Hampshire - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(48, 'USA - NJ - New Jersey - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, '00:00:00', '1,2,3,4,5,6,7'),
(49, 'USA - NM - New Mexico - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(50, 'USA - NY - New York - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(51, 'USA - NC - North Carolina - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(52, 'USA - ND - North Dakota - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(53, 'USA - OH - Ohio - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(54, 'USA - OK - Oklahoma - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(55, 'USA - OR - Oregon - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(56, 'USA - PA - Pennsylvania - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(57, 'USA - RI - Rhode Island - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(58, 'USA - SC - South Carolina - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(59, 'USA - SD- South Dakota - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(60, 'USA - TN - Tennessee - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(61, 'USA - TX - Texas - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(62, 'USA - UT - Utah - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(63, 'USA - VT - Vermont - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(64, 'USA - VA - Virginia - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(65, 'USA - WA - Washington - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(66, 'USA - WV - West Virginia - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(67, 'USA - WI - Wisconsin - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(68, 'USA - WY - Wyoming - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(69, 'USA - DC - Washington D.C. - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(70, 'USA - Territories - GU - PR - VI - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(71, 'USA - CA - California (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(72, 'USA - FL - Florida (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(73, 'USA - IL - Illinois (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(74, 'USA - MO - Missouri (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(75, 'USA - NY - New York (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(76, 'USA - OH - Ohio (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(77, 'USA - PA - Pennsylvania (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(78, 'USA - TX - Texas (B) - W25', 1, 25.00, '', '2', '0', '13:00:00', 0, NULL, '1,2,3,4,5,6,7'),
(79, 'USA - NY - Brooklyn 2 - H25', 1, 25.00, '', '2', '0', '15:00:00', 0, '00:00:00', ''),
(80, 'USA - NY - Queens 2 - H25', 1, 25.00, '', '2', '0', '15:00:00', 0, '00:00:00', ''),
(81, 'USA - NY - Bronx 2 - H25', 1, 25.00, '', '2', '0', '15:00:00', 0, '00:00:00', ''),
(82, 'USA - NY - Manhattan Gardening', 1, 0.00, '', '1', '1', '17:00:00', 0, '00:00:00', ''),
(83, 'USA - NY - Manhattan - InStore Pick Up 1 - 96th str', 1, 0.00, '', '1', '1', '17:00:00', 0, '00:00:00', ''),
(84, 'USA - NY - Manhattan - InStore Pick Up 2 - 87th str', 1, 0.00, '', '1', '1', '00:01:00', 0, '00:00:00', ''),
(86, 'USA - NY - Manhattan - WSHOP', 1, 0.00, '', '2', '0', '00:01:00', 0, NULL, ''),
(87, 'USA - NY - Manhattan - InStore Pick Up 3 - 1 Prince St.', 1, 0.00, '', '1', '1', '00:01:00', 0, '00:00:00', ''),
(88, 'USA - NJ - 1 - H0', 1, 0.00, '', '1', '0', '17:00:00', 0, '00:00:00', ''),
(89, 'USA - NJ - 2 - H10', 1, 10.00, '', '2', '0', '17:00:00', 0, '00:00:00', ''),
(90, 'USA - NJ - 2 - H15', 1, 15.00, '', '2', '0', '17:00:00', 0, '00:00:00', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `postalgroup`
--
ALTER TABLE `postalgroup`
  ADD PRIMARY KEY (`postalgroup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `postalgroup`
--
ALTER TABLE `postalgroup`
  MODIFY `postalgroup_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
