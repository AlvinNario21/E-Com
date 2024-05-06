-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2024 at 10:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unboxed`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `municipality` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `province` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`address_id`, `customer_id`, `barangay`, `municipality`, `postal_code`, `province`, `country`) VALUES
(1, 20, 'Macalaya', 'Castilla', '4713', 'Sorsogon City', 'Philippines'),
(2, 21, 'Tagas', 'Daraga', '4730', 'Albay', 'Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_img` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `image_dp` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `field` varchar(255) NOT NULL,
  `skill` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_num` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `image_dp`, `name`, `field`, `skill`, `email`, `password`, `address`, `contact_num`) VALUES
(20, './images/alvin.jpg', 'Alvin Nario', '', '', 'alvinnario07@gmail.com', '1234', 'Macalaya, Castilla, Sorsogon', '09774246291'),
(21, './images/rachelle.jpg', 'Rachelle Anne Manila', '', '', 'rachellemanila@gmail.com', 'nang', 'Tagas, Daraga, Albay', '09123456789'),
(33, './images/trina.jpg', 'Trina Hibo', 'writer', 'graphic-design', 'trinahibo@gmail.com', 'trina', 'Washington, Albay', '0'),
(35, './images/hazel.jpg', 'Hazel Marqueses', 'developer', 'graphic-design, ui-ux-design, web-development', 'hazelmarqueses@gmail.com', 'hazel1234', 'Pilar, Sorsogon', '0');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_img` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_owner` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_payment` int(11) NOT NULL,
  `payment` varchar(100) NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(255) NOT NULL COMMENT 'To Ship, To Receive, Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `customer_id`, `customer_name`, `address`, `contact`, `product_id`, `product_img`, `product_name`, `product_owner`, `quantity`, `price`, `total_payment`, `payment`, `order_date`, `status`) VALUES
(108, 20, 'Alvin Nario', 'Macalaya, Castilla, Sorsogon', '09774246291', 11, './images/drawing.jpg', 'Commission Drawing', 'Trina Hibo', 2, 10000.00, 20000, 'Cash On Delivery', '2024-05-02', 'Rated'),
(109, 33, 'Trina Hibo', 'Washington, Albay', '0', 4, './images/cine.png', 'Bado', 'Alvin Nario', 1, 1000.00, 1000, 'Gcash', '2024-05-02', 'Rated'),
(110, 33, 'Trina Hibo', 'Washington, Albay', '0', 8, './images/Screenshot 2023-10-15 111957.png', 'Laptop', 'Rachelle Anne Manila', 1, 20000.00, 20000, 'Cash On Delivery', '2024-05-02', 'To Receive'),
(111, 21, 'Rachelle Anne Manila', 'Tagas, Daraga, Albay', '09123456789', 4, './images/cine.png', 'Bado', 'Alvin Nario', 1, 1000.00, 1000, 'Cash On Delivery', '2024-05-02', 'Rated'),
(112, 35, 'Hazel Marqueses', 'Pilar, Sorsogon', '0', 4, './images/cine.png', 'Bado', 'Alvin Nario', 4, 1000.00, 4000, 'Cash On Delivery', '2024-05-03', 'Rated'),
(113, 35, 'Hazel Marqueses', 'Pilar, Sorsogon', '0', 8, './images/Screenshot 2023-10-15 111957.png', 'Laptop', 'Rachelle Anne Manila', 1, 20000.00, 20000, 'Gcash', '2024-05-03', 'Rated'),
(114, 35, 'Hazel Marqueses', 'Pilar, Sorsogon', '0', 11, './images/drawing.jpg', 'Commission Drawing', 'Trina Hibo', 1, 10000.00, 10000, 'Gcash', '2024-05-03', 'Rated');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_img` varchar(1000) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `showcase_id` int(11) NOT NULL,
  `showcase_name` varchar(50) NOT NULL,
  `stocks` int(11) NOT NULL,
  `sold` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_img`, `name`, `description`, `price`, `customer_id`, `owner`, `showcase_id`, `showcase_name`, `stocks`, `sold`) VALUES
(4, './images/cine.png', 'Bado', 'tigasulog', 1000.00, 20, 'Alvin Nario', 2, 'POGING-POGI CLOTHING', 100, 0),
(8, './images/Screenshot 2023-10-15 111957.png', 'Laptop', 'walang issue pero ako meron', 20000.00, 21, 'Rachelle Anne Manila', 3, 'Sorry Mali Apparel', 100, 0),
(11, './images/drawing.jpg', 'Commission Drawing', 'Drawing', 10000.00, 33, 'Trina Hibo', 4, 'POET.TRI', 5, 5),
(29, './images/Screenshot 2023-05-05 185045.png', 'Cake kamo dyan', 'cake kamo dyan HAHA', 150.00, 20, 'Alvin Nario', 2, 'POGING-POGI CLOTHING', 10, 0),
(30, './images/Screenshot 2023-10-15 111957.png', 'dasdask', 'dalskdandas', 239.00, 20, 'Alvin Nario', 2, 'POGING-POGI CLOTHING', 12, 0);

-- --------------------------------------------------------

--
-- Table structure for table `return_refund_requests`
--

CREATE TABLE `return_refund_requests` (
  `request_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `request_type` varchar(10) DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `reason` varchar(1000) DEFAULT NULL,
  `resolution_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `customer_img` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `customer_img`, `customer_name`, `order_item_id`, `product_id`, `customer_id`, `rating`, `review_text`, `review_date`) VALUES
(5, './images/trina.jpg', 'Trina Hibo', 109, 4, 33, 5, 'hi love nagkaon kana? HAHAHA', '2024-05-02'),
(7, './images/rachelle.jpg', 'Rachelle Anne Manila', 111, 4, 21, 4, 'lapa HAHAHA', '2024-05-02'),
(8, './images/hazel.jpg', 'Hazel Marqueses', 112, 4, 35, 5, 'HAHAHAHAHAHa kainis', '2024-05-03'),
(9, './images/hazel.jpg', 'Hazel Marqueses', 113, 8, 35, 5, 'lapa HAHHHAHAH ano man daw ini', '2024-05-03'),
(10, './images/hazel.jpg', 'Hazel Marqueses', 113, 8, 35, 5, 'LAPA BA', '2024-05-03'),
(11, './images/alvin.jpg', 'Alvin Nario', 108, 11, 20, 5, 'dora dora dora AHAHAHHA', '2024-05-04'),
(12, './images/hazel.jpg', 'Hazel Marqueses', 114, 11, 35, 5, 'yedey HAHAHA', '2024-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `shippings`
--

CREATE TABLE `shippings` (
  `shipping_id` int(11) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `pickup_address` varchar(255) DEFAULT NULL,
  `shipping_company` varchar(50) DEFAULT NULL,
  `shipping_date` date DEFAULT NULL,
  `pick_up_time` time NOT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `shipping_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shippings`
--

INSERT INTO `shippings` (`shipping_id`, `recipient_name`, `product_id`, `pickup_address`, `shipping_company`, `shipping_date`, `pick_up_time`, `shipping_cost`, `shipping_status`) VALUES
(49, 'Alvin Nario', 11, 'Washington, Albay', 'Flash Express', '2024-05-02', '15:18:00', 55.00, 'To Ship'),
(59, 'Trina Hibo', 4, 'Macalaya, Castilla, Sorsogon', 'Ninja Van', '2024-05-02', '16:05:00', 55.00, 'To Ship'),
(60, '', 0, 'Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(61, 'Trina Hibo', 4, 'Macalaya, Castilla, Sorsogon', 'Ninja Van', '2024-05-02', '19:58:00', 55.00, 'To Ship'),
(62, '', 0, 'Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(63, '', 0, 'Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(64, 'Hazel Marqueses', 4, 'Macalaya, Castilla, Sorsogon', 'Ninja Van', '2024-05-03', '19:30:00', 55.00, 'To Ship'),
(65, '', 0, 'Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(66, 'Hazel Marqueses', 4, 'Macalaya, Castilla, Sorsogon', 'Flash Express', '2024-05-03', '19:33:00', 55.00, 'To Ship'),
(67, '', 0, 'Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(68, '', 0, 'Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(69, 'Hazel Marqueses', 8, 'Tagas, Daraga, Albay', 'Flash Express', '2024-05-09', '19:51:00', 55.00, 'To Ship'),
(70, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(71, 'Trina Hibo', 8, 'Tagas, Daraga, Albay', 'Ninja Van', '2024-04-30', '19:52:00', 55.00, 'To Ship'),
(72, 'Hazel Marqueses', 8, 'Tagas, Daraga, Albay', 'Ninja Van', '2024-05-03', '19:00:00', 55.00, 'To Ship'),
(73, 'Hazel Marqueses', 8, 'Tagas, Daraga, Albay', 'Flash Express', '2024-05-03', '20:01:00', 55.00, 'To Ship'),
(74, 'Hazel Marqueses', 8, 'Tagas, Daraga, Albay', '', '2024-05-03', '20:01:00', 55.00, 'To Ship'),
(75, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(76, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(77, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(78, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(79, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(80, 'Hazel Marqueses', 8, 'Tagas, Daraga, Albay', 'Flash Express', '2024-05-03', '20:07:00', 55.00, 'To Ship'),
(81, 'Hazel Marqueses', 8, 'Tagas, Daraga, Albay', 'Flash Express', '2024-05-03', '20:08:00', 55.00, 'To Ship'),
(82, '', 0, 'Tagas, Daraga, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(83, 'Hazel Marqueses', 11, 'Washington, Albay', 'Flash Express', '2024-05-04', '08:07:00', 55.00, 'To Ship'),
(84, '', 0, 'Washington, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship');

-- --------------------------------------------------------

--
-- Table structure for table `showcase`
--

CREATE TABLE `showcase` (
  `showcase_id` int(11) NOT NULL,
  `showcase_dp` varchar(100) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `owner` varchar(50) NOT NULL,
  `showcase_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `showcase`
--

INSERT INTO `showcase` (`showcase_id`, `showcase_dp`, `customer_id`, `owner`, `showcase_name`) VALUES
(2, './images/pogi.jpg', 20, 'Alvin Nario', 'POGING-POGI CLOTHING'),
(3, './images/sorrymali.jpg', 21, 'Rachelle Anne Manila', 'Sorry Mali Apparel '),
(4, './images/435434703_1085262015881044_4187634531093442417_n.png', 33, 'Trina Hibo', 'POET.TRI'),
(42, './images/Screenshot 2023-04-21 212436.png', 35, 'Hazel Marqueses', 'Primes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_customer_id` (`customer_id`),
  ADD KEY `order_item_ibfk_2` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`customer_id`);

--
-- Indexes for table `return_refund_requests`
--
ALTER TABLE `return_refund_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indexes for table `shippings`
--
ALTER TABLE `shippings`
  ADD PRIMARY KEY (`shipping_id`);

--
-- Indexes for table `showcase`
--
ALTER TABLE `showcase`
  ADD PRIMARY KEY (`showcase_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `shippings`
--
ALTER TABLE `shippings`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `showcase`
--
ALTER TABLE `showcase`
  MODIFY `showcase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `return_refund_requests`
--
ALTER TABLE `return_refund_requests`
  ADD CONSTRAINT `return_refund_requests_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `return_refund_requests_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_item_id`) REFERENCES `order_item` (`order_item_id`);

--
-- Constraints for table `showcase`
--
ALTER TABLE `showcase`
  ADD CONSTRAINT `showcase_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
