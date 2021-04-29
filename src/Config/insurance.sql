-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2021 at 10:14 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `abc_insurance`
--

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `vehicle_type` varchar(45) NOT NULL,
  `ncb` decimal(10,0) NOT NULL,
  `engine_size` varchar(45) NOT NULL,
  `year_of_manufacture` varchar(45) NOT NULL,
  `driving_experince` int(11) NOT NULL,
  `involvement_in_car_accident` tinyint(4) NOT NULL,
  `conviction_of_any_driving_offence` tinyint(4) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `vehicle_type`, `ncb`, `engine_size`, `year_of_manufacture`, `driving_experince`, `involvement_in_car_accident`, `conviction_of_any_driving_offence`, `created_date`, `price`) VALUES
(14, 'name7', 'vehicle_type', '20', 'engine_size', '2015', 3, 0, 1, '2021-04-25 21:48:19', 20000),
(19, 'name4', 'vehicle_type', '20', 'engine_size', '2015', 3, 0, 1, '2021-04-25 21:48:45', 20000),
(20, 'name6', 'vehicle_type', '20', 'engine_size', '2015', 3, 0, 1, '2021-04-25 21:48:48', 20000),
(21, 'name8p', 'vehicle_type', '20', 'engine_size', '2015', 3, 0, 1, '2021-04-27 05:14:47', 2000);

-- --------------------------------------------------------

--
-- Table structure for table `quotation_request`
--

CREATE TABLE `quotation_request` (
  `id` int(11) NOT NULL,
  `vehicle_type` varchar(45) NOT NULL,
  `engine_size` varchar(45) NOT NULL,
  `ncb` decimal(10,0) NOT NULL,
  `year_of_manufacture` varchar(45) NOT NULL,
  `years_of_driving_experince` int(11) NOT NULL,
  `involement_in_car_accident` tinyint(4) NOT NULL,
  `conviction_of_any_driving_offence` tinyint(4) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  `reference_id` varchar(45) NOT NULL,
  `price` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quotation_request`
--

INSERT INTO `quotation_request` (`id`, `vehicle_type`, `engine_size`, `ncb`, `year_of_manufacture`, `years_of_driving_experince`, `involement_in_car_accident`, `conviction_of_any_driving_offence`, `created_date`, `name`, `email`, `phone`, `approved`, `reference_id`, `price`) VALUES
(3, 'vehicle_type', 'engine_size', '10', '2014', 3, 1, 1, '2021-04-25 22:16:09', 'full name', 'email@mail.com', '08042424242', 1, '202697521765952088b', 230000),
(4, 'vehicle_type', 'engine_size', '10', '2014', 3, 1, 1, '2021-04-25 22:18:14', 'full name', 'email@mail.com', '08042424242', 0, '946637056139ed697a9', 0),
(5, 'vehicle_type', 'engine_size', '10', '2014', 3, 1, 1, '2021-04-25 22:18:29', 'full name', 'email@mail.com', '08042424242', 0, '73680067611010', 0),
(6, 'vehicle_type', 'engine_size', '10', '2014', 3, 1, 1, '2021-04-27 05:20:05', 'full name', 'email@mail.com', '08042424242', 0, '6422250650cbb8', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(45) NOT NULL,
  `created_date` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_id`, `created_date`, `user_id`) VALUES
(1, 'iddddd', '2021-04-05', 40);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) NOT NULL,
  `address` varchar(45) NOT NULL,
  `dob` date NOT NULL,
  `password` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(4) DEFAULT '0',
  `temp_email` varchar(45) NOT NULL,
  `user_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `dob`, `password`, `date_created`, `active`, `temp_email`, `user_token`) VALUES
(39, 'Val_first_name', 'Val_last_name', 'tasiukwaplong@gmail.com', 'Val_phone', 'Val_address', '1996-12-01', '$2y$10$NIekTXAxJZzArRz4c6YG5uGCD0k/1hM5Q9KKtd0q7GMPJ.w7/ACCa', '2021-04-21 10:59:42', 1, '', '61865008587d7014d8c88e93f379611'),
(40, 'Tasiu', 'Kwaplong', 'tasiu4ll@gmail.com', '090315143464', 'Tudun kauri', '1996-12-12', '$2y$10$uhCJ7h8kLEZ1g.7LlTxKjOKRzLp0QNp7022qOKXCLSQmRr64Q8vAO', '2021-04-21 14:21:30', 1, '', '126149774886224ce053417a5aeed854c7ebb5add5');

-- --------------------------------------------------------

--
-- Table structure for table `user_insurance_policies`
--

CREATE TABLE `user_insurance_policies` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_created` date NOT NULL,
  `amount_paid` int(11) NOT NULL,
  `engine_number` varchar(45) NOT NULL,
  `chassis_number` varchar(45) NOT NULL,
  `vehicle_license_number` varchar(45) NOT NULL,
  `expired` tinyint(4) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `plans_id` int(11) DEFAULT NULL,
  `quotation_request_id` int(11) DEFAULT NULL,
  `transactions_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`,`name`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `quotation_request`
--
ALTER TABLE `quotation_request`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `reference_id_UNIQUE` (`reference_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `transaction_id_UNIQUE` (`transaction_id`),
  ADD KEY `fk_transactions_user1_idx` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `user_token` (`user_token`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Indexes for table `user_insurance_policies`
--
ALTER TABLE `user_insurance_policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `transactions_id_UNIQUE` (`transactions_id`),
  ADD UNIQUE KEY `quotation_request_id_UNIQUE` (`quotation_request_id`),
  ADD KEY `fk_user_insurance_plans_user_idx` (`user_id`),
  ADD KEY `fk_user_insurance_policies_plans1_idx` (`plans_id`),
  ADD KEY `fk_user_insurance_policies_quotation_request1_idx` (`quotation_request_id`),
  ADD KEY `fk_user_insurance_policies_transactions1_idx` (`transactions_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `quotation_request`
--
ALTER TABLE `quotation_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_insurance_policies`
--
ALTER TABLE `user_insurance_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_user1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_insurance_policies`
--
ALTER TABLE `user_insurance_policies`
  ADD CONSTRAINT `fk_user_insurance_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_user_insurance_policies_plans1` FOREIGN KEY (`plans_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `fk_user_insurance_policies_quotation_request1` FOREIGN KEY (`quotation_request_id`) REFERENCES `quotation_request` (`id`),
  ADD CONSTRAINT `fk_user_insurance_policies_transactions1` FOREIGN KEY (`transactions_id`) REFERENCES `transactions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
