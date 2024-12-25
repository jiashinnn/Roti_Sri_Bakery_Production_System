-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2024 at 06:00 PM
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
-- Table structure for table `tbl_batches`
--

CREATE TABLE `tbl_batches` (
  `batch_id` int(6) NOT NULL,
  `recipe_id` int(6) NOT NULL,
  `schedule_id` int(6) NOT NULL,
  `batch_startTime` datetime NOT NULL DEFAULT current_timestamp(),
  `batch_endTime` datetime NOT NULL,
  `batch_status` enum('Pending','In Progress','Completed','') NOT NULL DEFAULT 'Pending',
  `batch_remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_batches`
--

INSERT INTO `tbl_batches` (`batch_id`, `recipe_id`, `schedule_id`, `batch_startTime`, `batch_endTime`, `batch_status`, `batch_remarks`) VALUES
(1, 9, 3, '2024-12-26 12:20:00', '2024-12-26 14:20:00', 'In Progress', 'Abby is responsible for baking');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_batch_assignments`
--

CREATE TABLE `tbl_batch_assignments` (
  `ba_id` int(6) NOT NULL,
  `batch_id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `ba_task` varchar(255) NOT NULL,
  `ba_status` enum('Pending','In Progress','Completed','') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_batch_assignments`
--

INSERT INTO `tbl_batch_assignments` (`ba_id`, `batch_id`, `user_id`, `ba_task`, `ba_status`) VALUES
(2, 1, 6, 'Baking', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ingredients`
--

CREATE TABLE `tbl_ingredients` (
  `ingredient_id` int(6) NOT NULL,
  `recipe_id` int(6) NOT NULL,
  `ingredient_name` varchar(200) NOT NULL,
  `ingredient_quantity` int(6) NOT NULL,
  `ingredient_unitOfMeasure` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ingredients`
--

INSERT INTO `tbl_ingredients` (`ingredient_id`, `recipe_id`, `ingredient_name`, `ingredient_quantity`, `ingredient_unitOfMeasure`) VALUES
(17, 9, 'Sugar', 200, 'g'),
(18, 9, 'Cocoa Powder', 100, 'g'),
(19, 9, 'Eggs', 4, 'pcs'),
(20, 9, 'Butter', 250, 'g');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_recipe`
--

CREATE TABLE `tbl_recipe` (
  `recipe_id` int(6) NOT NULL,
  `recipe_name` varchar(100) NOT NULL,
  `recipe_category` varchar(100) NOT NULL,
  `recipe_batchSize` int(6) NOT NULL,
  `recipe_unitOfMeasure` varchar(50) NOT NULL,
  `recipe_dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipe_dateUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_recipe`
--

INSERT INTO `tbl_recipe` (`recipe_id`, `recipe_name`, `recipe_category`, `recipe_batchSize`, `recipe_unitOfMeasure`, `recipe_dateCreated`, `recipe_dateUpdated`) VALUES
(9, 'Chocolate Cake', 'Cake', 8, 'pcs', '2024-12-25 15:00:26', '2024-12-25 15:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_schedule`
--

CREATE TABLE `tbl_schedule` (
  `schedule_id` int(6) NOT NULL,
  `recipe_id` int(6) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_quantityToProduce` int(6) NOT NULL,
  `schedule_status` enum('Pending','In Progress','Completed','') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_schedule`
--

INSERT INTO `tbl_schedule` (`schedule_id`, `recipe_id`, `schedule_date`, `schedule_quantityToProduce`, `schedule_status`) VALUES
(3, 9, '2024-12-28', 2, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_schedule_assignments`
--

CREATE TABLE `tbl_schedule_assignments` (
  `sa_id` int(6) NOT NULL,
  `schedule_id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `sa_dateAssigned` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_schedule_assignments`
--

INSERT INTO `tbl_schedule_assignments` (`sa_id`, `schedule_id`, `user_id`, `sa_dateAssigned`) VALUES
(4, 3, 5, '2024-12-25 15:51:00');

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
  `user_dateRegister` datetime NOT NULL DEFAULT current_timestamp(),
  `user_role` varchar(10) NOT NULL DEFAULT 'Baker'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `user_fullName`, `user_contact`, `user_address`, `user_email`, `user_password`, `user_dateRegister`, `user_role`) VALUES
(1, 'Admin', '0123456789', 'Roti Sri Bakery', 'admin@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-21 10:37:44', 'Admin'),
(2, 'aurora', '01234', 'Kedah', 'aurora@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-21 11:53:02', 'Baker'),
(3, 's', 's', 's', 'a@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-22 21:53:32', 'Baker'),
(4, 'LIM JIA SHIN', '0186686142', 'LOT 4026-7, LORONG AIK HWA 6, BUKIT BAKRI, 84200 MUAR, JOHOR', 'a@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-22 22:13:34', 'Baker'),
(5, 'bbb', '123456789', 'bbb', 'bbb@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-12-25 16:31:43', 'Baker'),
(6, 'Abby', '0123456789', 'Kedah', 'abby@gmail.com', '25d55ad283aa400af464c76d713c07ad', '2024-12-25 12:01:35', 'Baker');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_batches`
--
ALTER TABLE `tbl_batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `tbl_batch_assignments`
--
ALTER TABLE `tbl_batch_assignments`
  ADD PRIMARY KEY (`ba_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `tbl_ingredients`
--
ALTER TABLE `tbl_ingredients`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD KEY `receipt_id` (`recipe_id`);

--
-- Indexes for table `tbl_recipe`
--
ALTER TABLE `tbl_recipe`
  ADD PRIMARY KEY (`recipe_id`);

--
-- Indexes for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `tbl_schedule_assignments`
--
ALTER TABLE `tbl_schedule_assignments`
  ADD PRIMARY KEY (`sa_id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_batches`
--
ALTER TABLE `tbl_batches`
  MODIFY `batch_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_batch_assignments`
--
ALTER TABLE `tbl_batch_assignments`
  MODIFY `ba_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_ingredients`
--
ALTER TABLE `tbl_ingredients`
  MODIFY `ingredient_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_recipe`
--
ALTER TABLE `tbl_recipe`
  MODIFY `recipe_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  MODIFY `schedule_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_schedule_assignments`
--
ALTER TABLE `tbl_schedule_assignments`
  MODIFY `sa_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_batches`
--
ALTER TABLE `tbl_batches`
  ADD CONSTRAINT `tbl_batches_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `tbl_recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_batches_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `tbl_schedule` (`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_batch_assignments`
--
ALTER TABLE `tbl_batch_assignments`
  ADD CONSTRAINT `tbl_batch_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_batch_assignments_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `tbl_batches` (`batch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_ingredients`
--
ALTER TABLE `tbl_ingredients`
  ADD CONSTRAINT `tbl_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `tbl_recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  ADD CONSTRAINT `tbl_schedule_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `tbl_recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_schedule_assignments`
--
ALTER TABLE `tbl_schedule_assignments`
  ADD CONSTRAINT `tbl_schedule_assignments_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `tbl_schedule` (`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_schedule_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
