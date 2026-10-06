-- Tool Rental Co. database (webtoolrental)
-- Course project: CST8285 Web Programming, Assignment 2
-- Sample data only. Import with phpMyAdmin or: mysql -u root < webtoolrental.sql

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `webtoolrental` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `webtoolrental`;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`) VALUES
(1, 'Heavy Equipment'),
(2, 'Power Tools'),
(3, 'Hand Tools'),
(4, 'Safety Equipment'),
(5, 'Cleaning Equipment');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `address`, `phone`, `email`) VALUES
(1, 'John', 'Smith', '123 Maple St, Ottawa, ON K1A 0B1', '(613) 555-0101', 'john.smith@example.com'),
(2, 'Emily', 'Johnson', '45 Queen St, Toronto, ON M5H 2N2', '(416) 555-2233', 'emily.johnson@example.com'),
(3, 'Michael', 'Brown', '789 King Rd, Kingston, ON K7K 3J4', '(613) 555-9876', 'michael.brown@example.com'),
(4, 'Sarah', 'Lee', '22 Elgin Ave, Montreal, QC H3Z 2Y7', '(514) 555-4433', 'sarah.lee@example.com'),
(5, 'David', 'Wilson', '99 Laurier Ave, Ottawa, ON K1P 6L5', '(613) 555-7766', 'david.wilson@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `location` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `location`) VALUES
(1, 'Warehouse'),
(2, 'Shop Floor'),
(3, 'Maintenance Dock');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `tool_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `tool_id`, `qty`, `startdate`, `enddate`, `total`) VALUES
(6, 5, 6, 1, '2025-08-12', '2025-08-18', 4200),
(7, 2, 9, 2, '2025-08-10', '2025-08-11', 50),
(8, 1, 10, 2, '2025-08-14', '2025-08-17', 540);

-- --------------------------------------------------------

--
-- Table structure for table `tool`
--

CREATE TABLE `tool` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `id_category` int(11) NOT NULL,
  `id_location` int(11) NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tool`
--

INSERT INTO `tool` (`id`, `name`, `id_category`, `id_location`, `price`) VALUES
(1, 'Hydraulic Jack', 1, 1, 45),
(2, 'Electric Drill', 2, 2, 15),
(3, 'Pneumatic Wrench', 2, 3, 600),
(4, 'Hammer', 4, 2, 50),
(5, 'Shop vac', 5, 3, 200),
(6, 'Compactor', 1, 1, 700),
(7, 'Electric Saw', 2, 1, 150),
(8, 'Pneumatic Hammer', 3, 2, 75),
(9, 'Screwdriver', 4, 3, 25),
(10, 'Garden Vac', 5, 2, 90);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_tool`
-- (See below for the actual view)
--
CREATE TABLE `view_tool` (
`id` int(11)
,`name` varchar(50)
,`price` float
,`category_id` int(11)
,`category` varchar(50)
,`location_id` int(11)
,`location` varchar(50)
);

-- --------------------------------------------------------

--
-- Structure for view `view_tool`
--
DROP TABLE IF EXISTS `view_tool`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_tool`  AS SELECT `t`.`id` AS `id`, `t`.`name` AS `name`, `t`.`price` AS `price`, `c`.`id` AS `category_id`, `c`.`category` AS `category`, `l`.`id` AS `location_id`, `l`.`location` AS `location` FROM ((`tool` `t` join `category` `c` on(`t`.`id_category` = `c`.`id`)) join `location` `l` on(`t`.`id_location` = `l`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tool`
--
ALTER TABLE `tool`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;
