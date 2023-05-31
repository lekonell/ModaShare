-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- 생성 시간: 23-05-31 19:05
-- 서버 버전: 8.0.33-0ubuntu0.22.04.2
-- PHP 버전: 8.1.2-1ubuntu2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `app_modashare`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `files`
--

CREATE TABLE `files` (
  `file_id` int NOT NULL,
  `file_name_source` varchar(300) NOT NULL,
  `file_name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_ext` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_size` bigint NOT NULL,
  `file_path` varchar(300) NOT NULL,
  `file_timeout` int NOT NULL,
  `file_expired` int NOT NULL DEFAULT '0',
  `file_downloaded` int NOT NULL DEFAULT '0',
  `file_password` varchar(100) NOT NULL,
  `uploader_id` int NOT NULL DEFAULT '0',
  `uploader_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `uploader_detail` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `history`
--

CREATE TABLE `history` (
  `history_id` int NOT NULL,
  `file_id` int NOT NULL,
  `state` varchar(50) NOT NULL,
  `request_url` varchar(250) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `user_ip` varchar(40) NOT NULL,
  `user_detail` varchar(400) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `users`
--

CREATE TABLE `users` (
  `uid` int NOT NULL,
  `id` varchar(20) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `authority` int NOT NULL DEFAULT '1',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `approver` int NOT NULL,
  `date_approved` datetime NOT NULL,
  `date_locked` datetime NOT NULL,
  `date_last_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- 테이블의 인덱스 `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`);

--
-- 테이블의 인덱스 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `uid` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
