-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 03:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `production_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page`
--

CREATE TABLE `tbl_page` (
  `page_id` int(6) NOT NULL,
  `page_type` varchar(100) NOT NULL,
  `page_title` text NOT NULL,
  `page_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_page`
--

INSERT INTO `tbl_page` (`page_id`, `page_type`, `page_title`, `page_description`) VALUES
(1, 'aboutus', 'About Us', '<div style=\"text-align: justify;\"><font color=\"#7b8898\"><span style=\"font-size: 15px; background-color: rgb(255, 255, 255);\">Welcome to our production system! We are a team of experienced software developers who have created a platform designed to simplify the process of conducting and production.\r\n\r\nOur goal is to provide a user-friendly interface that makes it easy for organizations of all sizes to gather valuable insights from their customers, employees, or any other target audience. Our system allows you to design custom surveys, distribute them via various channels, and collect responses in real-time.\r\n\r\nOur team is dedicated to ensuring that our platform meets the highest standards of security and data privacy. We take the protection of your data seriously and have implemented robust measures to ensure that your information is always safe.\r\n\r\nWhether you\'re conducting market research, customer satisfaction surveys, or employee engagement surveys, our platform has the tools you need to get the job done. We are committed to providing exceptional customer service and support to ensure that your survey experience is as smooth and hassle-free as possible.\r\n\r\nThank you for choosing our production system. We look forward to helping you achieve your production goals!</span></font></div>');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(6) NOT NULL,
  `user_fullName` varchar(200) NOT NULL,
  `user_contact` varchar(100) NOT NULL,
  `user_address` text NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `user_dateRegister` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `user_fullName`, `user_contact`, `user_address`, `user_email`, `user_password`, `user_dateRegister`) VALUES
(1, 'LIM JIA SHIN', '0186686142', 'LOT 4026-7, LORONG AIK HWA 6, BUKIT BAKRI, 84200 MUAR, JOHOR', 'jiashin0604@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-21 10:37:44'),
(2, 'aurora', '01234', 'Kedah', 'aurora@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-21 11:53:02'),
(3, 's', 's', 's', 'a@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-22 21:53:32'),
(4, 'LIM JIA SHIN', '0186686142', 'LOT 4026-7, LORONG AIK HWA 6, BUKIT BAKRI, 84200 MUAR, JOHOR', 'a@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-22 22:13:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_page`
--
ALTER TABLE `tbl_page`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_page`
--
ALTER TABLE `tbl_page`
  MODIFY `page_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
