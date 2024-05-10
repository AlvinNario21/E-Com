-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2024 at 05:20 AM
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

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `customer_id`, `product_img`, `product_id`, `product_name`, `quantity`, `unit_price`, `total_price`) VALUES
(36, 33, './images/436834534_961184519119683_5012056967409942280_n.jpg', 164, 'Rasbeery Pi Segregator Robot', 1, 45000.00, NULL),
(39, 20, './images/358627001_1986906498327473_8021749840168404824_n.jpg', 170, 'Painted Abaca Bag', 1, 1500.00, NULL);

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
  `contact_num` varchar(11) NOT NULL,
  `premium` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `image_dp`, `name`, `field`, `skill`, `email`, `password`, `address`, `contact_num`, `premium`) VALUES
(20, './images/alvin.jpg', 'Alvin Nario', 'Designer', 'Graphic Design,UI/UX Design,Copywriting,Creative Writing,Web Development,Software Development', 'alvinnario07@gmail.com', 'alvinnario', 'Purok Evergreen Macalaya, Castilla, Sorsogon', '09774246291', 'Yes'),
(21, './images/rachelle.jpg', 'Rachelle Anne Manila', '', '', 'rachellemanila@gmail.com', 'nang', 'Tagas, Daraga, Albay', '09123456789', ''),
(33, './images/trina.jpg', 'Trina Hibo', 'writer', 'graphic-design', 'trinahibo@gmail.com', 'trina', 'Washington, Albay', '0', ''),
(35, './images/hazel.jpg', 'Hazel Marqueses', 'developer', 'graphic-design, ui-ux-design, web-development', 'hazelmarqueses@gmail.com', 'hazel1234', 'Pilar, Sorsogon', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `email`, `name`, `contact_number`, `message`, `created_at`) VALUES
(1, 'alvinnario07@gmail.com', 'Alvin Nario', '09774246291', 'hi', '2024-05-10 03:18:06'),
(2, 'alvinnario07@gmail.com', 'Alvin Nario', '09774246291', 'hi', '2024-05-10 03:18:52');

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
  `status` varchar(255) NOT NULL COMMENT 'To Ship, To Receive, Completed, Canceled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `customer_id`, `customer_name`, `address`, `contact`, `product_id`, `product_img`, `product_name`, `product_owner`, `quantity`, `price`, `total_payment`, `payment`, `order_date`, `status`) VALUES
(116, 35, 'Hazel Marqueses', 'Pilar, Sorsogon', '0', 168, './images/436730570_784827986915548_4918511893808253710_n.png', 'Poet.tri Mug', 'Trina Hibo', 1, 250.00, 250, 'Gcash', '2024-05-09', 'Pending'),
(117, 20, 'Alvin Nario', 'Purok Evergreen Macalaya, Castilla, Sorsogon', '09774246291', 11, './images/drawing.jpg', 'Commission Drawing', 'Trina Hibo', 5, 10000.00, 50000, 'Cash On Delivery', '2024-05-09', 'Canceled'),
(118, 33, 'Trina Hibo', 'Washington, Albay', '0', 169, './images/375205480_630184555845351_8839002758568565261_n.jpg', 'Abaca Bag', 'Rachelle Anne Manila', 1, 750.00, 750, 'Gcash', '2024-05-09', 'Pending'),
(119, 20, 'Alvin Nario', 'Purok Evergreen Macalaya, Castilla, Sorsogon', '09774246291', 170, './images/358627001_1986906498327473_8021749840168404824_n.jpg', 'Painted Abaca Bag', 'Rachelle Anne Manila', 1, 1500.00, 1500, 'Cash On Delivery', '2024-05-10', 'Pending');

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
(11, './images/drawing.jpg', 'Commission Drawing', 'Transform your ideas into stunning visuals with Commission Drawing! Our platform connects you with talented artists who bring your vision to life. Say goodbye to bland graphics and hello to custom-made masterpieces.', 10000.00, 33, 'Trina Hibo', 4, 'POET.TRI', 100, 50),
(164, './images/436834534_961184519119683_5012056967409942280_n.jpg', 'Rasbeery Pi Segregator Robot', 'Safeguard Sentry: Our Armed Controlled Robot offers advanced security solutions with its precision control and armed capabilities, ensuring unparalleled protection for your premises.', 45000.00, 20, 'Alvin Nario', 2, 'Robotics', 20, 0),
(165, './images/436858202_414274394740003_9212261695247401285_n.png', 'Arduino Following Robot', 'FollowMe Buddy: Say hello to your new robotic sidekick! Our Arduino Following Robot utilizes advanced sensors and programming to seamlessly track your movements, ensuring it stays by your side every step of the way.', 7500.00, 20, 'Alvin Nario', 2, 'Robotics', 20, 0),
(166, './images/441466438_1118696499235969_5194583542363177900_n.gif', 'Armed Controlled Robot', 'Safeguard Sentry: Our Armed Controlled Robot offers advanced security solutions with its precision control and armed capabilities, ensuring unparalleled protection for your premises.', 15000.00, 20, 'Alvin Nario', 2, 'Robotics', 20, 0),
(167, './images/435372857_978439143885703_7075667936230719400_n.jpg', 'Commission Painting', 'Personalized Artistry: Transform your inspiration into art with our commissioned painting service. From portraits to landscapes, our skilled painters craft one-of-a-kind pieces that capture the essence of your imagination.', 2500.00, 33, 'Trina Hibo', 4, 'POET.TRI', 10, 0),
(168, './images/436730570_784827986915548_4918511893808253710_n.png', 'Poet.tri Mug', 'Sip from the poetry of life with our Poet.tri Mug. Each cup is adorned with verses that stir the soul, making your morning coffee or evening tea an experience of inspiration and reflection.', 250.00, 33, 'Trina Hibo', 4, 'POET.TRI', 100, 0),
(169, './images/375205480_630184555845351_8839002758568565261_n.jpg', 'Abaca Bag', 'Tropical Tote: Embrace sustainable style with our Abaca Bag, handcrafted from natural abaca fibers. Perfect for beach days or everyday errands, its sturdy construction and timeless design make it a versatile and eco-friendly accessory.', 750.00, 21, 'Rachelle Anne Manila', 3, 'Abaca Finest', 50, 0),
(170, './images/358627001_1986906498327473_8021749840168404824_n.jpg', 'Painted Abaca Bag', 'Tropical Artistry Tote: Carry a piece of paradise with our Painted Abaca Bag, where island inspiration meets artistic expression. Each bag is hand-painted with vibrant tropical motifs, adding a pop of color and personality to your ensemble.', 1500.00, 21, 'Rachelle Anne Manila', 3, 'Abaca Finest', 50, 0);

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
(13, './images/alvin.jpg', 'Alvin Nario', 117, 11, 20, NULL, NULL, '2024-05-10');

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
(2, './images/5_66c80bf7-7bd6-4226-a578-0324cf8be684_1600x.png', 20, 'Alvin Nario', 'Robotics'),
(3, './images/abaca.jpg', 21, 'Rachelle Anne Manila', 'Abaca Finest'),
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
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shippings`
--
ALTER TABLE `shippings`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `showcase`
--
ALTER TABLE `showcase`
  MODIFY `showcase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

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
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE;

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
