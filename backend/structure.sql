-- Adminer 4.8.1 MySQL 8.0.31-0ubuntu0.22.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `engal` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `engal`;

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `gallery` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `upload_date` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `value` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `date` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `remote_addr` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `browser` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  `status` char(255) CHARACTER SET cp1250 COLLATE cp1250_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2022-12-31 15:22:03