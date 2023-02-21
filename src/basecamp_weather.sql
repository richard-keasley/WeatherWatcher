-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 21, 2023 at 01:20 PM
-- Server version: 10.6.11-MariaDB-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `basecamp_weather`
--
CREATE DATABASE IF NOT EXISTS `basecamp_weather` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `basecamp_weather`;

-- --------------------------------------------------------

--
-- Table structure for table `dailies`
--

DROP TABLE IF EXISTS `dailies`;
CREATE TABLE IF NOT EXISTS `dailies` (
  `date` date NOT NULL,
  `count` int(11) DEFAULT NULL,
  `temperature_min` float DEFAULT NULL,
  `temperature_avg` float DEFAULT NULL,
  `temperature_max` float DEFAULT NULL,
  `humidity_min` float DEFAULT NULL,
  `humidity_avg` float DEFAULT NULL,
  `humidity_max` float DEFAULT NULL,
  `rain_max` float DEFAULT NULL,
  `solar_avg` float DEFAULT NULL,
  `solar_max` float DEFAULT NULL,
  `uvi_avg` float DEFAULT NULL,
  `uvi_max` float DEFAULT NULL,
  `wind_avg` float DEFAULT NULL,
  `wind_max` float DEFAULT NULL,
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `readings`
--

DROP TABLE IF EXISTS `readings`;
CREATE TABLE IF NOT EXISTS `readings` (
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `server` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`server`)),
  `readings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`readings`)),
  `inputs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`inputs`)),
  PRIMARY KEY (`datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
