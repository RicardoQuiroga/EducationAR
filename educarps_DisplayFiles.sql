-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 13, 2021 at 04:30 PM
-- Server version: 5.6.41-84.1
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `educarps_DisplayFiles`
--

-- --------------------------------------------------------

--
-- Table structure for table `ControlData`
--

CREATE TABLE `ControlData` (
  `MarkerArea` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `XPos` int(11) NOT NULL,
  `YPos` int(11) NOT NULL,
  `Source` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ControlData`
--

INSERT INTO `ControlData` (`MarkerArea`, `XPos`, `YPos`, `Source`) VALUES
('Task', 0, 0, 'materials/imgs/BaseBinary-HexProblem1.png'),
('Model', 0, 0, 'materials/imgs/BaseBinary-HexProblem1TransparentANS.png');

-- --------------------------------------------------------

--
-- Table structure for table `DisplayFiles`
--

CREATE TABLE `DisplayFiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'For verification',
  `filePath` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `projectionType` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'For Differentiating between marker and location-based.',
  `sequenceNum` int(11) DEFAULT NULL COMMENT 'Set for if part of a sequence, NULL otherwise',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `DisplayFiles`
--

INSERT INTO `DisplayFiles` (`id`, `fileName`, `extension`, `filePath`, `projectionType`, `sequenceNum`) VALUES
(1, 'BaseBinary-HexProblem1.png', 'png', '/materials/imgs/', 'marker', NULL),
(2, 'BaseBinary-HexProblem1Transparent.png', 'png', 'materials/imgs/', 'marker', NULL),
(3, 'BaseBinary-HexProblem1TransparentANS.png', 'png', '/materials/imgs/', 'marker', NULL);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
