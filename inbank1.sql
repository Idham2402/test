-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2025 at 08:57 AM
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
-- Database: `inbank`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_name` varchar(50) NOT NULL,
  `account_number` varchar(12) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `account_name`, `account_number`, `balance`) VALUES
(1, 7, 'I-SPEND', '944789461293', 7990.00),
(2, 7, 'I-SAVINGS', '380749908571', 210.00),
(3, 8, 'I-SPEND', '987057814689', 10000.00),
(4, 8, 'I-SAVINGS', '039614174861', 0.00),
(5, 9, 'I-SPEND', '345615501221', 100.00),
(6, 9, 'I-SAVINGS', '195574491153', 900.00),
(7, 10, 'I-SPEND', '958059273592', 1000.00),
(8, 10, 'I-SAVINGS', '194137649374', 0.00),
(9, 11, 'I-SPEND', '926937698765', 1000.00),
(10, 11, 'I-SAVINGS', '582090614302', 0.00),
(11, 12, 'I-SPEND', '168219954480', 1000.00),
(12, 12, 'I-SAVINGS', '480966905184', 0.00),
(13, 13, 'I-SPEND', '414713740732', 1000.00),
(14, 13, 'I-SAVINGS', '476243458988', 0.00),
(15, 14, 'I-SPEND', '303421624363', 1000.00),
(16, 14, 'I-SAVINGS', '457884971094', 0.00),
(17, 15, 'I-SPEND', '820232400173', 1000.00),
(18, 15, 'I-SAVINGS', '421244574393', 0.00),
(19, 16, 'I-SPEND', '996210563797', 1000.00),
(20, 16, 'I-SAVINGS', '795452344557', 0.00),
(25, 53, 'I-SPEND', '065795206667', 1000.00),
(26, 53, 'I-SAVINGS', '319577809552', 0.00),
(27, 54, 'I-SPEND', '733487130601', 1000.00),
(28, 54, 'I-SAVINGS', '901238737498', 0.00),
(29, 55, 'I-SPEND', '106988734542', 1000.00),
(30, 55, 'I-SAVINGS', '910628358779', 0.00),
(31, 56, 'I-SPEND', '125602085558', 1000.00),
(32, 56, 'I-SAVINGS', '639940848978', 0.00),
(33, 57, 'I-SPEND', '756202530265', 1000.00),
(34, 57, 'I-SAVINGS', '096394169682', 0.00),
(35, 58, 'I-SPEND', '160869553424', 1000.00),
(36, 58, 'I-SAVINGS', '455911147765', 0.00),
(37, 59, 'I-SPEND', '405088764301', 1000.00),
(38, 59, 'I-SAVINGS', '568824851399', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `activity_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `username`, `activity_type`, `activity_time`) VALUES
(1, 9, '', 'Login', '2025-01-08 15:46:44'),
(2, 9, 'faisal', 'Login', '2025-01-08 15:49:57'),
(3, 9, 'faisal', 'Login', '2025-01-08 15:50:21'),
(4, 9, 'faisal', 'Update Profile', '2025-01-08 16:49:03'),
(5, 9, 'faisal', 'Login', '2025-01-08 16:51:54'),
(6, 9, 'faisal', 'Update Profile Picture', '2025-01-08 16:52:29'),
(10, 1, 'admin', 'Login', '2025-01-08 17:17:31'),
(11, 9, 'faisal', 'Login', '2025-01-08 18:39:15'),
(12, 1, 'admin', 'Login', '2025-01-08 23:33:18'),
(13, 58, 'abcdefg', 'Login', '2025-01-09 12:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `sender_account_number` varchar(12) NOT NULL,
  `receiver_account_number` varchar(12) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `card_number` varchar(16) DEFAULT NULL,
  `cvv` varchar(3) DEFAULT NULL,
  `ic_card` varchar(12) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `card_number`, `cvv`, `ic_card`, `phone_number`, `email`, `profile_picture`) VALUES
(1, 'admin', '$2y$10$xA7hKKRaqfpbCYVkDmqv0OX4EeD/gJURF/viKs9xh1nXgIpgCon.2', '12345789012', '123', '12391864', '13232312', 'admin@gmail.com\r\n', ''),
(2, 'ahmad', '$2y$10$PDOaQL8bHSY7S8qlyhbNKe34ir3YpxI3RKbIEp36EUBeaeVfDtuwW', '123456789', '123', '123456789', '123456789', 'ahmad@gmail.com', ''),
(3, 'test', 'test', '112321', '122', '123213212', '13232312', 'test@gmail.com', ''),
(4, '', '$2y$10$QA8d.52KYNw1LJGttOASROyfWXvMp8lxyqUZfS8tPyYZxkDD.mkzC', '123123123', '123', '', '', '', ''),
(5, '', '$2y$10$idfPi4P8F/1DAlhLXRwlneRi.m.PS3ep1ejYOx8AiuBsc.gvENSIO', '123456789', '123', '', '', '', ''),
(6, 'ulala', '$2y$10$rfPhOe/IRxwGrWSwtbm9VOr87nTSpQ2BKodZy.Ka7zcKjfV/Kjd3y', '123123123', '123', '12312312', '123123123123', 'ahmad@gmail.com', ''),
(7, 'amin', '$2y$10$D8xV.k93k532Np8kpO0theXfmRo0E7PQ67X4SBi0DkamkK45BoSSe', '123456789', '123', '123456789', '0198463871', 'amin@gmail.com', 'nbs.php'),
(8, 'dollah', '$2y$10$RV.4OXsC5KVeH.GmnrQmpOwhJps/V0ciXUQ0SS3WFHZxuhWQgvH.u', '123123123123', '123', '12312312', '0198463871', 'dollah@gmail.com', ''),
(9, 'faisal', '$2y$10$SdJ5CBGMjN1wV.TF9Bs1DOxcT.uTsXoUoOjQywFluz8CBuHZm3YNG', '123123123123', '123', '123456789', '1231123', 'faisal@gmail.com', 'cat.jpg'),
(10, 'abe', '$2y$10$1/Gos4bhE7LMzWZQhCtd2ONGtv1J8yrI/hw/VmV26zY7KRjMH.bGu', '123123123123', '123', '123123123123', '0198463871', 'abe@gmail.com', ''),
(11, 'aminah', '$2y$10$wVaGsB5L/V44zrvvEhRdFOVZ7sslBx5s1QtqMtsKvC7tQAXMPxZQu', '123123123123', '123', '12312312', '0198463871', 'aminah@gmail.com', ''),
(12, 'aminah', '$2y$10$xojmKXnX6yVOnc8Pfs3P8eq/HoNtLWrd1WJOAuHQTDkbJA.hvwL5i', '123123123123', '123', '12312312', '0198463871', 'aminah@gmail.com', ''),
(13, 'siti', '$2y$10$NMDYjZzqtP3x47yTbFfum.A4F3D7x65jN1Q7numt9i78Gf731vsQ6', '123456789', '123', '12312312', '0198463871', 'siti@gmail.com', ''),
(14, 'shidi', '$2y$10$GlqF5AIgosUk1ieMVqsaoOHBsujpWTiQk9zFVpKLq2SIPJoebfeTm', '12321312312', '123', '123123123123', '123123123123123', 'shidi@gmail.com', ''),
(15, 'ali', '$2y$10$r1q/aUyGdcPCC89gZHGaWeYZlsHtBC5AFmJW7tJebyui81yJmuBya', '12321312312', '123', '010714110439', '123456789', 'ali@gmail.com', 'cat.jpg'),
(16, '', '$2y$10$9vO23Q39HQSq1KMUwoFpJeLqpZvUs.AnCOZ6W4pUX5NmTezrtQK76', '12321312312', '123', '', '', '', ''),
(53, 'haris', '$2y$10$FK8DIuc4FbP9YL66FpDw2emE.wire5kz2MlAwi15jv8tNKdP/ErD.', '', '', '', '123', 'haris@gmail.com', ''),
(54, 'test1', '$2y$10$osRE76m81WG4GwiATvs67eo3MqW.WG8hYpkeqoapyYli9bdcXhzQu', '', '', '', '123', 'test1@gmail.com', ''),
(55, 'test3', '$2y$10$8qwq9sX.mrSukqCh9ZL1s.hkQSioIjcTy6RvinaVdHugYRi2XwPPi', '', '', '', '123', 'test3@gmail.com', ''),
(56, '123456', '$2y$10$VJZDD01HYNGSY.Hj8YFVT.QmOla3PIWLN82B3e2fkjCtxaDGJvmH.', '', '', '', '123', '123456@gmail.com', ''),
(57, 'abc', '$2y$10$QfIITjw1kox7gyeyd1c8puDGbWuuePDDE7Rcy2YfslBqfbaUy68EO', '', '', '', '123', 'abc@gmail.com', ''),
(58, 'abcdefg', '$2y$10$VnE3R9ZoDK6mp/dIGfBUue1iDj909sXQ6r6UiJLbeK3k996r.ZQyW', '', '', '', '123', 'abcdefg@gmail.com', ''),
(59, 'ab', '$2y$10$M9Fs4puicOiUMume.EgL4uJJ2cVqgYqIFSPu6HAXM8m3ufGn0vB9u', '', '', '', '12', 'ab@gmai.com', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_account_number` (`sender_account_number`),
  ADD KEY `receiver_account_number` (`receiver_account_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`sender_account_number`) REFERENCES `accounts` (`account_number`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`receiver_account_number`) REFERENCES `accounts` (`account_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
