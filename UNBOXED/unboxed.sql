-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2024 at 08:07 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin');

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
  `skill` varchar(1000) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_num` varchar(11) NOT NULL,
  `wallet` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `image_dp`, `name`, `field`, `skill`, `email`, `password`, `address`, `contact_num`, `wallet`) VALUES
(20, './images/alvin.jpg', 'Alvin Nario', 'Writer', 'Web Design,Web Development,Frameworks', 'alvinnario07@gmail.com', 'alvin', 'Purok Evergreen Macalaya,Castilla, Sorsogon', '09774246291', 45055),
(21, './images/rachelle.jpg', 'Rachelle Anne Manila', '', '', 'rachellemanila@gmail.com', 'nang', 'Tagas, Daraga, Albay', '09123456789', 0),
(33, './images/trina.jpg', 'Trina Hibo', 'writer', 'graphic-design', 'trinahibo@gmail.com', 'trina', 'Washington, Albay', '09123456789', 47610),
(35, './images/hazel.jpg', 'Hazel Marqueses', 'developer', 'graphic-design, ui-ux-design, web-development', 'hazelmarqueses@gmail.com', 'hazel1234', 'Pilar, Sorsogon', '09123456789', 0),
(37, './images/5c5afe3c0d7296e58b4441d087349e3a.jpg', 'Toothless dragon', 'Developer', 'Graphic Design, UI/UX Design, Copywriting, Creative Writing', 'toothless@gmail.com', 'haha', 'Bicol Univ', '', 0),
(41, './images/Screenshot 2023-03-07 175514.png', 'Trdaidasd auosbdj', 'Designer', '', 'hazelmaqueses@gmail.com', '', 'Macalaya, Castilla, Sorsogon', '09774246291', 0),
(43, './images/5c5afe3c0d7296e58b4441d087349e3a.jpg', 'dajsl da dabjls dm', 'Developer', '', 'alvinnario@gmail.com', 'haha', 'Macalaya, Castilla, Sorsogon', '09774246291', 0),
(49, NULL, '', '', 'Web Development', '', '', NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `driver_id` int(11) NOT NULL,
  `image_dp` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`driver_id`, `image_dp`, `name`, `email`, `password`) VALUES
(1, './images/alvin.jpg', 'driver', 'driver@gmail.com', 'driver');

-- --------------------------------------------------------

--
-- Table structure for table `field`
--

CREATE TABLE `field` (
  `field_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `field`
--

INSERT INTO `field` (`field_id`, `field_name`, `skills`) VALUES
(1, 'Designer', 'Graphic Design, UI/UX Design, Adobe Creative Suite, Typography, Color Theory, Web Design, Print Design, Branding, Illustration'),
(2, 'Developer', 'Programming Languages, Web Development, Frameworks, Database Management, Version Control, Problem Solving, Algorithms and Data Structures, API Integration, Mobile App Development'),
(3, 'Writer', 'Writing, Editing and Proofreading, Research, Journalism, SEO Writing, Blogging, Technical Writing, Communication Skills, Adaptability'),
(4, 'Entrepreneur', 'Business Development, Strategic Planning, Financial Management, Leadership, Marketing and Sales, Networking, Risk Management, Negotiation, Problem-Solving'),
(5, 'Freelancer', 'Time Management, Client Communication, Project Management, Self-Motivation, Marketing and Branding, Negotiation, Accounting and Invoicing, Adaptability, Multitasking'),
(6, 'Artist', 'Drawing and Painting, Sculpting, Mixed Media, Art History, Color Theory, Composition, Creativity, Visual Communication, Fine Arts Techniques'),
(7, 'Photographer', 'Photography Techniques, Lighting, Composition, Camera Equipment Knowledge, Post-Processing, Photo Editing, Image Retouching, Creativity, Client Management'),
(8, 'Influencer', 'Content Creation, Social Media Management, Audience Engagement, Personal Branding, Communication Skills, Negotiation, Collaboration, Trend Analysis, Platform-Specific Knowledge'),
(9, 'Athlete', 'Physical Fitness, Endurance, Strength Training, Sports-specific Skills, Agility, Speed, Mental Toughness, Discipline, Injury Prevention'),
(10, 'Chef', 'Culinary Techniques, Food Safety and Hygiene, Menu Planning, Recipe Development, Cooking Methods, Flavor Pairing, Presentation, Creativity, Time Management');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `name`, `email`, `contact`, `address`, `status`) VALUES
(14, 'Alvin Nario', 'alvinnario07@gmail.com', '09774246291', 'Purok Evergreen Macalaya, Castilla, Sorsogon', 'Completed'),
(24, 'Trina Hibo', 'trinahibo@gmail.com', '09123456789', 'Washington, Albay', 'Completed');

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
  `order_id` varchar(16) NOT NULL,
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

INSERT INTO `order_item` (`order_item_id`, `order_id`, `customer_id`, `customer_name`, `address`, `contact`, `product_id`, `product_img`, `product_name`, `product_owner`, `quantity`, `price`, `total_payment`, `payment`, `order_date`, `status`) VALUES
(127, '66421711438ba', 20, 'Alvin Nario', 'Purok Evergreen Macalaya, Castilla, Sorsogon', '09774246291', 167, './images/435372857_978439143885703_7075667936230719400_n.jpg', 'Commission Painting', 'Alvin Nario', 1, 2500.00, 2555, 'Gcash', '2024-05-13', 'Rated'),
(128, '66421ddea8453', 33, 'Trina Hibo', 'Washington, Albay', '09123456789', 164, './images/436834534_961184519119683_5012056967409942280_n.jpg', 'Rasbeery Pi Segregator Robot', 'Alvin Nario', 1, 45000.00, 45055, 'Gcash', '2024-05-13', 'Returned'),
(129, '6642afd736991', 20, 'Alvin Nario', 'Purok Evergreen Macalaya, Castilla, Sorsogon', '09774246291', 168, './images/436730570_784827986915548_4918511893808253710_n.png', 'Poet.tri Mug', 'Alvin Nario', 1, 250.00, 305, 'Cash On Delivery', '2024-05-14', 'To Receive');

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
(11, './images/drawing.jpg', 'Commission Drawing', 'Transform your ideas into stunning visuals with Commission Drawing! Our platform connects you with talented artists who bring your vision to life. Say goodbye to bland graphics and hello to custom-made masterpieces.', 10000.00, 33, 'Trina Hibo', 4, 'POET.TRI', 100, 5),
(164, './images/436834534_961184519119683_5012056967409942280_n.jpg', 'Rasbeery Pi Segregator Robot', 'Safeguard Sentry: Our Armed Controlled Robot offers advanced security solutions with its precision control and armed capabilities, ensuring unparalleled protection for your premises.', 45000.00, 20, 'Alvin Nario', 2, 'Robotics', 20, 0),
(165, './images/436858202_414274394740003_9212261695247401285_n.png', 'Arduino Following Robot', 'FollowMe Buddy: Say hello to your new robotic sidekick! Our Arduino Following Robot utilizes advanced sensors and programming to seamlessly track your movements, ensuring it stays by your side every step of the way.', 7500.00, 20, 'Alvin Nario', 2, 'Robotics', 20, 0),
(166, './images/441466438_1118696499235969_5194583542363177900_n.gif', 'Armed Controlled Robot', 'Safeguard Sentry: Our Armed Controlled Robot offers advanced security solutions with its precision control and armed capabilities, ensuring unparalleled protection for your premises.', 15000.00, 20, 'Alvin Nario', 2, 'Robotics', 20, 0),
(167, './images/435372857_978439143885703_7075667936230719400_n.jpg', 'Commission Painting', 'Personalized Artistry: Transform your inspiration into art with our commissioned painting service. From portraits to landscapes, our skilled painters craft one-of-a-kind pieces that capture the essence of your imagination.', 2500.00, 33, 'Trina Hibo', 4, 'POET.TRI', 9, 1),
(168, './images/436730570_784827986915548_4918511893808253710_n.png', 'Poet.tri Mug', 'Sip from the poetry of life with our Poet.tri Mug. Each cup is adorned with verses that stir the soul, making your morning coffee or evening tea an experience of inspiration and reflection.', 250.00, 33, 'Trina Hibo', 4, 'POET.TRI', 98, 2),
(169, './images/375205480_630184555845351_8839002758568565261_n.jpg', 'Abaca Bag', 'Tropical Tote: Embrace sustainable style with our Abaca Bag, handcrafted from natural abaca fibers. Perfect for beach days or everyday errands, its sturdy construction and timeless design make it a versatile and eco-friendly accessory.', 750.00, 21, 'Rachelle Anne Manila', 3, 'Abaca Finest', 50, 0),
(170, './images/358627001_1986906498327473_8021749840168404824_n.jpg', 'Painted Abaca Bag', 'Tropical Artistry Tote: Carry a piece of paradise with our Painted Abaca Bag, where island inspiration meets artistic expression. Each bag is hand-painted with vibrant tropical motifs, adding a pop of color and personality to your ensemble.', 1500.00, 21, 'Rachelle Anne Manila', 3, 'Abaca Finest', 50, 0),
(176, './images/wp5973673.jpg', 'Claw', 'Pink', 120.00, 37, 'Toothless dragon', 47, 'Dragons', 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `return_refund_requests`
--

CREATE TABLE `return_refund_requests` (
  `request_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `request_type` varchar(10) DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `reason` varchar(1000) DEFAULT NULL,
  `resolution_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_refund_requests`
--

INSERT INTO `return_refund_requests` (`request_id`, `order_id`, `customer_id`, `customer_name`, `request_type`, `request_date`, `status`, `reason`, `resolution_date`) VALUES
(5, 66421, 33, 'Trina Hibo', NULL, '2024-05-16', NULL, 'Damaged Product', NULL),
(6, 0, 33, 'Trina Hibo', NULL, '2024-05-16', NULL, '', NULL),
(7, 0, 33, 'Trina Hibo', NULL, '2024-05-16', NULL, '', NULL),
(8, 0, 33, 'Trina Hibo', NULL, '2024-05-16', NULL, '', NULL),
(9, 0, 33, 'Trina Hibo', NULL, '2024-05-16', NULL, '', NULL);

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
(15, './images/alvin.jpg', 'Alvin Nario', 127, 167, 20, 5, 'Ganda sulit ', '2024-05-14');

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
(84, '', 0, 'Washington, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(85, 'Hazel Marqueses', 168, 'Washington, Albay', 'Ninja Van', '2024-05-10', '14:13:00', 55.00, 'To Ship'),
(86, 'Toothless dragon', 168, 'Washington, Albay', 'J&T Express', '2024-05-10', '15:09:00', 55.00, 'To Ship'),
(87, '', 0, 'Washington, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(88, 'Toothless dragon', 168, 'Washington, Albay', 'Ninja Van', '2024-05-10', '15:13:00', 55.00, 'To Ship'),
(89, '', 0, 'Washington, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(90, 'Alvin Nario', 167, 'Washington, Albay', 'J&T Express', '2024-05-14', '07:57:00', 55.00, 'To Ship'),
(91, '', 0, 'Washington, Albay', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(92, 'Trina Hibo', 164, 'Purok Evergreen Macalaya, Castilla, Sorsogon', 'Flash Express', '2024-05-15', '18:55:00', 55.00, 'To Ship'),
(93, '', 0, 'Purok Evergreen Macalaya, Castilla, Sorsogon', '', '0000-00-00', '00:00:00', 55.00, 'To Ship'),
(94, '', 0, '', 'J&T Express', '0000-00-00', '00:00:00', NULL, ''),
(95, '', 0, 'Washington, Albay', 'J&T Express', '2024-05-16', '20:01:00', NULL, ''),
(96, '', 0, 'Washington, Albay', 'J&T Express', '2024-05-16', '17:59:00', NULL, ''),
(97, '', 0, 'Washington, Albay', 'Flash Express', '2024-05-16', '20:19:00', NULL, '');

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
(42, './images/Screenshot 2023-04-21 212436.png', 35, 'Hazel Marqueses', '7 Evelen'),
(47, './images/night-fury-light-fury-how-to-train-your-dragon-2880x1800-190.jpg', 37, 'Toothless Dragon', 'Dragons');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `skill_name`) VALUES
(1, 'Graphic Design'),
(2, 'UI/UX Design'),
(3, 'Adobe Creative Suite'),
(4, 'Typography'),
(5, 'Color Theory'),
(6, 'Web Design'),
(7, 'Print Design'),
(8, 'Branding'),
(9, 'Illustration'),
(10, 'Programming Languages'),
(11, 'Web Development'),
(12, 'Frameworks'),
(13, 'Database Management'),
(14, 'Version Control'),
(15, 'Problem Solving'),
(16, 'Algorithms and Data Structures'),
(17, 'API Integration'),
(18, 'Mobile App Development'),
(19, 'Writing'),
(20, 'Editing and Proofreading'),
(21, 'Research'),
(22, 'Journalism'),
(23, 'SEO Writing'),
(24, 'Blogging'),
(25, 'Technical Writing'),
(26, 'Communication Skills'),
(27, 'Adaptability'),
(28, 'Business Development'),
(29, 'Strategic Planning'),
(30, 'Financial Management'),
(31, 'Leadership'),
(32, 'Marketing and Sales'),
(33, 'Networking'),
(34, 'Risk Management'),
(35, 'Negotiation'),
(36, 'Problem-Solving'),
(37, 'Time Management'),
(38, 'Client Communication'),
(39, 'Project Management'),
(40, 'Self-Motivation'),
(41, 'Marketing and Branding'),
(42, 'Negotiation'),
(43, 'Accounting and Invoicing'),
(44, 'Adaptability'),
(45, 'Multitasking'),
(46, 'Drawing and Painting'),
(47, 'Sculpting'),
(48, 'Mixed Media'),
(49, 'Art History'),
(50, 'Color Theory'),
(51, 'Composition'),
(52, 'Creativity'),
(53, 'Visual Communication'),
(54, 'Fine Arts Techniques'),
(55, 'Photography Techniques'),
(56, 'Lighting'),
(57, 'Composition'),
(58, 'Camera Equipment Knowledge'),
(59, 'Post-Processing'),
(60, 'Photo Editing'),
(61, 'Image Retouching'),
(62, 'Creativity'),
(63, 'Client Management'),
(64, 'Content Creation'),
(65, 'Social Media Management'),
(66, 'Audience Engagement'),
(67, 'Personal Branding'),
(68, 'Communication Skills'),
(69, 'Negotiation'),
(70, 'Collaboration'),
(71, 'Trend Analysis'),
(72, 'Platform-Specific Knowledge'),
(73, 'Physical Fitness'),
(74, 'Endurance'),
(75, 'Strength Training'),
(76, 'Sports-specific Skills'),
(77, 'Agility'),
(78, 'Speed'),
(79, 'Mental Toughness'),
(80, 'Discipline'),
(81, 'Injury Prevention'),
(82, 'Culinary Techniques'),
(83, 'Food Safety and Hygiene'),
(84, 'Menu Planning'),
(85, 'Recipe Development'),
(86, 'Cooking Methods'),
(87, 'Flavor Pairing'),
(88, 'Presentation'),
(89, 'Creativity'),
(90, 'Time Management');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(16) NOT NULL,
  `order_id` varchar(16) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `product_owner` varchar(100) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `transaction_id`, `order_id`, `name`, `contact`, `total_amount`, `payment_method`, `reference_id`, `product_owner`, `product_name`, `order_date`, `status`) VALUES
(664215, '66421711438bc', '66421711438ba', 'Alvin Nario', '09774246291', 2555.00, 'Gcash', '66421711438bd', 'Trina Hibo', 'Commission Painting', '2024-05-13', 'Completed'),
(664216, '66421ddea8454', '66421ddea8453', 'Trina Hibo', '09123456789', 45055.00, 'Gcash', '66421ddea8455', 'Alvin Nario', 'Rasbeery Pi Segregator Robot', '2024-05-13', 'Completed');

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
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `field`
--
ALTER TABLE `field`
  ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `field`
--
ALTER TABLE `field`
  MODIFY `field_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `return_refund_requests`
--
ALTER TABLE `return_refund_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `shippings`
--
ALTER TABLE `shippings`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `showcase`
--
ALTER TABLE `showcase`
  MODIFY `showcase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=664217;

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
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_item_id`) REFERENCES `order_item` (`order_item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `showcase`
--
ALTER TABLE `showcase`
  ADD CONSTRAINT `showcase_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
