-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 02:06 PM
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
-- Database: `furniture_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('readed','unread') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`, `status`) VALUES
(1, 'Ashish Prajapati', 'twest101@gmail.com', 'i want to customize the tv stand', '2025-03-27 08:22:52', ''),
(2, 'ashutosh prajapati', 'test23@gmail.com', 'Thanks for the Products.', '2025-03-27 08:26:41', ''),
(3, 'ashutosh prajapati', 'test24@gmail.com', 'i want a wooden chair', '2025-03-27 08:27:13', ''),
(4, 'Ashish Prajapati', 'test010@gmail.com', 'i Want to customize the table.', '2025-03-27 08:30:19', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `shipping_address`, `created_at`) VALUES
(1, 1, 108995.00, 'cancelled', 'grtjhr', '2025-03-27 12:45:04'),
(2, 1, 21799.00, 'shipped', 'hrtjt', '2025-03-27 12:46:34'),
(3, 4, 43297.00, 'delivered', 'hbrt', '2025-03-27 12:49:09'),
(4, 1, 7499.00, 'delivered', 'addr1', '2025-03-27 12:53:56'),
(5, 5, 65097.00, 'delivered', 'addr2', '2025-03-27 12:55:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 9, 5, 21799.00),
(2, 2, 9, 1, 21799.00),
(3, 3, 5, 1, 10799.00),
(4, 3, 3, 1, 7499.00),
(5, 3, 1, 1, 24999.00),
(6, 4, 3, 1, 7499.00),
(7, 5, 7, 1, 6699.00),
(8, 5, 8, 1, 33399.00),
(9, 5, 1, 1, 24999.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image_url`) VALUES
(1, 'Modern Sofa', 24999.00, 'A stylish and comfortable modern sofa. ₹24,999', 'https://m.media-amazon.com/images/I/615ukV7Bp8L._SL1100_.jpg'),
(2, 'Dining Table', 16699.00, 'A wooden dining table with six chairs. ₹16,699', 'https://m.media-amazon.com/images/I/61FRKwUHrLL._SY300_SX300_QL70_FMwebp_.jpg'),
(3, 'Office Chair', 7499.00, 'Ergonomic office chair with lumbar support. ₹7,499', 'https://www.nilkamalfurniture.com/cdn/shop/files/LMEGAHIBOFFCHRBRN.jpg?v=1739180093&width=720'),
(4, 'Bed Frame', 28999.00, 'Queen size bed frame with storage. ₹28,999', 'https://www.nilkamalsleep.com/cdn/shop/files/StrikerMetalBed_White_PlusMattress_King_cb5a54f1-b25a-4f86-a631-fc1ad7c53a6a_650x.jpg?v=1724666312'),
(5, 'Bookshelf', 10799.00, 'Spacious bookshelf for your home or office. ₹10,799', 'https://m.media-amazon.com/images/I/61ewEvPy2wL._SX300_SY300_QL70_FMwebp_.jpg'),
(6, 'TV Stand', 13299.00, 'Modern TV stand with multiple compartments. ₹13,299', 'https://m.media-amazon.com/images/I/61GQAp30vzL._SL1500_.jpg'),
(7, 'Coffee Table', 6699.00, 'Glass-top coffee table for your living room. ₹6,699', 'https://www.ikea.com/in/en/images/products/vittsjoe-coffee-table-black-brown-glass__0135348_pe292039_s5.jpg?f=u'),
(8, 'Wardrobe', 33399.00, 'Spacious wardrobe with sliding doors. ₹33,399', 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcTI48jLWIRhaE8nmJsjWCFg4P0q3tfyO_vN2eZEBztSiN4MTqQiZn9rT3804dIIAJ_eFWhK5rlzyINleUHZee1SS5v-zoaIwsOJD8E7bWX7'),
(9, 'Recliner Chair', 21799.00, 'Comfortable recliner chair with soft padding. ₹21,799', 'https://thesleepcompany.in/cdn/shop/files/1_2a6b9bfb-7b91-440e-aa6d-0e1484862f55.webp?v=1742755261&width=1445');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@luxfurn.com', '$2y$10$Wd8/uvq.p.Z3oNI5zqeDn.GAWBl7yGCpQHGkw9O4.UZtX3EzrQIHi', 'Admin User', 'admin', '2025-03-27 12:10:26'),
(4, 'Ashish', 'test222@gmail.com', '$2y$10$X2zkOZdHe7qj/DSZbrWoM.5xZSumUZKq5M5nOPDbMUAQY8tOY0o92', 'Ashish Prajapati', 'user', '2025-03-27 12:48:45'),
(5, 'Ashutosh', 'test111@gmail.com', '$2y$10$Og/J9OOCipeBW33STEvnuu304RQk7IV692D.ceuTchy14hKEimFV.', 'ashutosh prajapati', 'user', '2025-03-27 12:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
