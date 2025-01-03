-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 04:49 PM
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
(3, 9, 3, '2024-12-29 01:45:00', '2024-12-29 23:45:00', 'Pending', 'Abby is responsible to overview');

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
(5, 3, 15, 'Mixing', 'Pending'),
(6, 3, 16, 'Decorating', 'Pending');

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

-- Ingredients for Classic Baguette (ID: 1)
INSERT INTO `tbl_ingredients` VALUES
(62, 1, 'Bread Flour', 500, 'g'),
(63, 1, 'Active Dry Yeast', 7, 'g'),
(64, 1, 'Salt', 10, 'g'),
(65, 1, 'Warm Water', 350, 'ml');

-- Ingredients for Chocolate Croissant (ID: 2)
INSERT INTO `tbl_ingredients` VALUES
(66, 2, 'All-Purpose Flour', 500, 'g'),
(67, 2, 'Butter', 250, 'g'),
(68, 2, 'Sugar', 50, 'g'),
(69, 2, 'Salt', 10, 'g'),
(70, 2, 'Active Dry Yeast', 7, 'g'),
(71, 2, 'Milk', 200, 'ml'),
(72, 2, 'Dark Chocolate', 200, 'g');

-- Ingredients for Vanilla Chiffon Cake (ID: 3)
INSERT INTO `tbl_ingredients` VALUES
(73, 3, 'Cake Flour', 150, 'g'),
(74, 3, 'Eggs', 6, 'pcs'),
(75, 3, 'Sugar', 150, 'g'),
(76, 3, 'Vegetable Oil', 80, 'ml'),
(77, 3, 'Vanilla Extract', 10, 'ml');

-- Ingredients for Sourdough Bread (ID: 4)
INSERT INTO `tbl_ingredients` VALUES
(78, 4, 'Bread Flour', 500, 'g'),
(79, 4, 'Whole Wheat Flour', 100, 'g'),
(80, 4, 'Sourdough Starter', 150, 'g'),
(81, 4, 'Salt', 12, 'g'),
(82, 4, 'Water', 350, 'ml');

-- Ingredients for Chocolate Chip Cookies (ID: 5)
INSERT INTO `tbl_ingredients` VALUES
(83, 5, 'All-Purpose Flour', 280, 'g'),
(84, 5, 'Butter', 230, 'g'),
(85, 5, 'Brown Sugar', 200, 'g'),
(86, 5, 'White Sugar', 100, 'g'),
(87, 5, 'Eggs', 2, 'pcs'),
(88, 5, 'Vanilla Extract', 5, 'ml'),
(89, 5, 'Chocolate Chips', 300, 'g');

-- Ingredients for Fruit Danish (ID: 6)
INSERT INTO `tbl_ingredients` VALUES
(90, 6, 'All-Purpose Flour', 400, 'g'),
(91, 6, 'Butter', 250, 'g'),
(92, 6, 'Sugar', 50, 'g'),
(93, 6, 'Active Dry Yeast', 7, 'g'),
(94, 6, 'Milk', 180, 'ml'),
(95, 6, 'Eggs', 2, 'pcs'),
(96, 6, 'Mixed Fruits', 300, 'g');

-- Ingredients for Red Velvet Cake (ID: 7)
INSERT INTO `tbl_ingredients` VALUES
(97, 7, 'Cake Flour', 300, 'g'),
(98, 7, 'Cocoa Powder', 20, 'g'),
(99, 7, 'Butter', 120, 'g'),
(100, 7, 'Sugar', 300, 'g'),
(101, 7, 'Eggs', 3, 'pcs'),
(102, 7, 'Buttermilk', 240, 'ml'),
(103, 7, 'Red Food Coloring', 30, 'ml'),
(104, 7, 'Cream Cheese', 500, 'g');

-- Ingredients for Whole Wheat Bread (ID: 8)
INSERT INTO `tbl_ingredients` VALUES
(105, 8, 'Whole Wheat Flour', 300, 'g'),
(106, 8, 'Bread Flour', 200, 'g'),
(107, 8, 'Active Dry Yeast', 7, 'g'),
(108, 8, 'Honey', 30, 'ml'),
(109, 8, 'Salt', 10, 'g'),
(110, 8, 'Warm Water', 300, 'ml');

-- Ingredients for Macarons (ID: 9)
INSERT INTO `tbl_ingredients` VALUES
(111, 9, 'Almond Flour', 200, 'g'),
(112, 9, 'Powdered Sugar', 200, 'g'),
(113, 9, 'Egg Whites', 70, 'g'),
(114, 9, 'Granulated Sugar', 90, 'g'),
(115, 9, 'Food Coloring', 1, 'g');

-- Ingredients for Opera Cake (ID: 10)
INSERT INTO `tbl_ingredients` VALUES
(116, 10, 'Almond Flour', 150, 'g'),
(117, 10, 'Powdered Sugar', 150, 'g'),
(118, 10, 'Eggs', 5, 'pcs'),
(119, 10, 'Dark Chocolate', 200, 'g'),
(120, 10, 'Coffee Extract', 30, 'ml'),
(121, 10, 'Butter', 250, 'g'),
(122, 10, 'Heavy Cream', 200, 'ml');

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
  `recipe_instructions` text NOT NULL,
  `recipe_dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipe_dateUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_recipe`
--

INSERT INTO `tbl_recipe` (`recipe_id`, `recipe_name`, `recipe_category`, `recipe_batchSize`, `recipe_unitOfMeasure`, `recipe_instructions`, `recipe_dateCreated`, `recipe_dateUpdated`) VALUES
(1, 'Classic Baguette', 'Bread', 4, 'pcs', '1. Mix flour, yeast, and salt\n2. Add water gradually and knead for 10 minutes\n3. First rise: 1 hour\n4. Shape into baguettes\n5. Second rise: 30 minutes\n6. Score and bake at 230°C for 25 minutes', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(2, 'Chocolate Croissant', 'Pastry', 12, 'pcs', '1. Prepare laminated dough\n2. Roll and cut into triangles\n3. Add chocolate batons\n4. Shape and proof for 2 hours\n5. Egg wash and bake at 190°C for 18 minutes', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(3, 'Vanilla Chiffon Cake', 'Cake', 1, 'pcs', '1. Separate eggs\n2. Whip egg whites with sugar\n3. Mix wet and dry ingredients\n4. Fold in meringue\n5. Bake in tube pan at 170°C for 45 minutes', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(4, 'Sourdough Bread', 'Bread', 2, 'pcs', '1. Feed starter 12 hours before\n2. Mix dough and autolyse\n3. Stretch and fold every 30 minutes\n4. Bulk ferment 4-6 hours\n5. Shape and cold proof overnight\n6. Bake in Dutch oven', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(5, 'Chocolate Chip Cookies', 'Cookie', 24, 'pcs', '1. Cream butter and sugars\n2. Add eggs and vanilla\n3. Mix in dry ingredients\n4. Fold in chocolate chips\n5. Scoop and bake at 180°C for 12 minutes', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(6, 'Fruit Danish', 'Pastry', 8, 'pcs', '1. Prepare Danish dough\n2. Roll and cut squares\n3. Add pastry cream and fruits\n4. Proof for 1 hour\n5. Bake at 200°C for 15 minutes\n6. Glaze while warm', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(7, 'Red Velvet Cake', 'Cake', 1, 'pcs', '1. Mix wet ingredients\n2. Combine dry ingredients\n3. Add red food coloring\n4. Bake layers at 175°C\n5. Prepare cream cheese frosting\n6. Assemble and frost', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(8, 'Whole Wheat Bread', 'Bread', 2, 'pcs', '1. Mix flours and yeast\n2. Knead for 15 minutes\n3. First rise: 90 minutes\n4. Shape loaves\n5. Second rise: 45 minutes\n6. Bake at 200°C', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(9, 'Macarons', 'Cookie', 24, 'pcs', '1. Age egg whites\n2. Make almond flour mixture\n3. Prepare meringue\n4. Macaronage\n5. Pipe and rest\n6. Bake at 150°C\n7. Fill and mature', '2024-12-25 15:00:26', '2024-12-25 15:06:00'),
(10, 'Opera Cake', 'Cake', 1, 'pcs', '1. Bake Joconde layers\n2. Prepare coffee syrup\n3. Make chocolate ganache\n4. Prepare coffee buttercream\n5. Layer and refrigerate\n6. Glaze with chocolate', '2024-12-25 15:00:26', '2024-12-25 15:06:00');

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
(5, 3, 15, '2024-12-29 15:44:23'),
(6, 3, 14, '2024-12-29 15:44:23');

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
(13, 'Admin', '0123456789', 'Roti Sri Bakery', 'Admin@gmail.com', '$2y$10$ydcLcf5duwhxgjXK.k/mBu0ikQTz14zXVDzmcx25BOhUsNifs5QB.', '2024-12-29 16:36:59', 'Admin'),
(14, 'Alicia', '0123456789', 'Kedah', 'alicia@gmail.com', '$2y$10$AIa/Or4OCSbPpO6Ii2/FTOuElG6gNMrMurzMcaBBxHl0nZGz6pA.2', '2024-12-29 16:38:47', 'Baker'),
(15, 'Abby', '0123456789', 'Kedah', 'abby@gmail.com', '$2y$10$wHP8eBIK2iFIPznx4cop5ue5wlEBw4HYxU0is3ogQw5DeSLrvA8Re', '2024-12-29 16:39:21', 'Baker'),
(16, 'Aurora', '0123456789', 'Kedah', 'aurora@gmail.com', '$2y$10$EfeC4D4e6C2XFTUMbJjxXO.MJ2OvHtSwpYGztV.Hh5WxSxwcXdbA.', '2024-12-29 16:40:12', 'Baker'),
(17, 'Irdina', '0123456789', 'Kedah', 'irdina@gmail.com', '$2y$10$NpmQZqhTTp4YOGvN4DGNkeoKdZ2oiB6UJGrI4f/cCStUpNt2CWbVK', '2024-12-29 16:41:00', 'Baker'),
(18, 'Alia', '0123456789', 'Kedah', 'alia@gmail.com', '$2y$10$px1H5SauACBIugoCce/aPehQol8A7tS9w6qza4W/LB7OUQv4apSBO', '2024-12-29 16:42:41', 'Baker');

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
  MODIFY `batch_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_batch_assignments`
--
ALTER TABLE `tbl_batch_assignments`
  MODIFY `ba_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_ingredients`
--
ALTER TABLE `tbl_ingredients`
  MODIFY `ingredient_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `tbl_recipe`
--
ALTER TABLE `tbl_recipe`
  MODIFY `recipe_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_schedule`
--
ALTER TABLE `tbl_schedule`
  MODIFY `schedule_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_schedule_assignments`
--
ALTER TABLE `tbl_schedule_assignments`
  MODIFY `sa_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
