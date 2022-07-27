-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2022 at 08:29 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onbakul`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(5) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id_admin`, `name`, `email`, `password`) VALUES
(17, 'test admin1', 'test1@test.com', '$2y$10$JJkCncNksdjc4C57f1jWye/zM3PfK5Ac.EAdUQcFCtoZZ7ikq3gBO'),
(18, 'Chakra', 'bagas@gmail.com', '$2y$10$ZDpRffhCI8QU2jx/qBQBdeyiHJaHlGfnBnjuZu0r.zPErSQCGs/rm');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id_owner` int(5) DEFAULT NULL,
  `id_outlet` int(5) DEFAULT NULL,
  `is_variant` tinyint(1) DEFAULT NULL,
  `id_product` int(5) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL,
  `selling_price` int(12) DEFAULT NULL,
  `capital_price` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_owner` int(5) DEFAULT NULL,
  `id_category` int(5) NOT NULL,
  `category_name` char(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_owner`, `id_category`, `category_name`) VALUES
(1, 52, 'Makanan'),
(1, 55, 'Makanan Ringan'),
(1, 56, 'Makanan Berat'),
(16, 62, 'Minuman'),
(12, 64, 'sembako'),
(12, 65, 'cafe'),
(12, 66, 'fashion'),
(12, 84, 'Minuman Kaleng'),
(12, 86, 'Minuman 1'),
(12, 122, 'Minuman 2'),
(12, 124, 'Minuman 3'),
(12, 125, 'Minuman 4'),
(12, 126, 'Minuman 5'),
(12, 127, 'Minuman 6'),
(12, 128, 'Minuman 7'),
(12, 129, 'Minuman 8'),
(12, 130, 'Minuman 9'),
(12, 131, 'Minuman 10'),
(12, 134, 'Minuman 11'),
(12, 135, 'Minuman 12'),
(12, 136, 'Minuman 13'),
(12, 137, 'Minuman 14'),
(12, 138, 'Minuman 15'),
(31, 139, 'Mie'),
(32, 140, 'Ikan Hias');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id_owner` int(5) DEFAULT NULL,
  `id_outlet` int(5) DEFAULT NULL,
  `id_customer` int(5) NOT NULL,
  `customer_name` char(50) DEFAULT NULL,
  `city` char(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `telp` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id_owner`, `id_outlet`, `id_customer`, `customer_name`, `city`, `address`, `telp`) VALUES
(1, 1, 24, 'mas bambang', 'jakarta', 'jln .......................', '1111'),
(12, 24, 35, 'mas alif', 'jakarta', 'jln. raya', '089662128483'),
(12, 24, 50, 'mas ridho', 'Jakarta', 'Jln. Raya No 001', '085212987212'),
(12, 24, 51, 'mas riko', '', '', ''),
(12, 24, 52, 'mas chakra', '', '', ''),
(12, 24, 53, 'mas adit', '', '', ''),
(12, 24, 54, 'mas dicky', '', '', ''),
(12, 24, 55, 'mas ibnu', '', '', ''),
(12, 24, 56, 'mas faris', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

CREATE TABLE `finance` (
  `id_finance` int(5) NOT NULL,
  `e_wallet_name` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `history_payment`
--

CREATE TABLE `history_payment` (
  `id_history_payment` int(5) NOT NULL,
  `id_owner` int(5) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  `month` char(2) DEFAULT NULL,
  `day` char(2) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `paid_off` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `history_payment`
--

INSERT INTO `history_payment` (`id_history_payment`, `id_owner`, `year`, `month`, `day`, `start`, `end`, `paid_off`) VALUES
(2, 12, '2022', '04', '10', '2022-04-10 12:24:23', '2022-05-10 12:24:23', 30000),
(3, 12, '2022', '04', '10', '2022-04-10 12:24:44', '2022-05-10 12:24:44', 30000),
(4, 12, '2022', '04', '11', '2022-04-11 12:46:14', '2022-05-11 12:46:14', 30000),
(5, 12, '2022', '04', '11', '2022-04-11 12:46:53', '2022-05-11 12:46:53', 30000),
(6, 12, '2022', '04', '11', '2022-04-11 12:47:08', '2022-05-11 12:47:08', 30000),
(7, 12, '2022', '05', '10', NULL, NULL, 30000),
(8, 12, '2022', '05', '11', NULL, NULL, 30000),
(9, 12, '2022', '05', '11', NULL, NULL, 30000),
(10, 12, '2022', '05', '12', NULL, NULL, 30000),
(11, 12, '2022', '04', '12', '2022-04-12 14:09:23', '2022-05-12 14:09:23', 30000),
(12, 12, '2022', '04', '12', '2022-04-12 16:30:07', '2022-05-12 16:30:07', 30000),
(13, 12, '2022', '04', '12', '2022-04-12 16:31:06', '2022-05-12 16:31:06', 30000),
(14, 12, '2022', '04', '12', '2022-04-12 16:32:18', '2022-05-12 16:32:18', 30000),
(15, 12, '2022', '04', '12', '2022-04-12 16:32:19', '2022-05-12 16:32:19', 30000),
(16, 12, '2022', '04', '12', '2022-04-12 16:34:35', '2022-05-12 16:34:35', 30000),
(17, 12, '2022', '04', '12', '2022-04-12 16:36:38', '2022-05-12 16:36:38', 30000),
(18, 12, '2022', '04', '12', '2022-04-12 17:21:52', '2022-05-12 17:21:52', 30000),
(19, 12, '2022', '04', '12', '2022-04-12 17:24:58', '2022-05-12 17:24:58', 30000),
(20, 12, '2022', '04', '12', '2022-04-12 20:08:36', '2022-05-12 20:08:36', 30000),
(21, 12, '2022', '04', '12', '2022-04-12 20:09:22', '2022-05-12 20:09:22', 30000),
(22, 12, '2022', '04', '12', '2022-04-12 20:11:18', '2022-05-12 20:11:18', 30000),
(23, 12, '2022', '04', '15', '2022-04-15 13:36:11', '2022-05-15 13:36:11', 30000),
(24, 12, '2022', '04', '15', '2022-04-15 14:00:19', '2022-05-15 14:00:19', 30000),
(25, 12, '2022', '04', '15', '2022-04-15 14:40:52', '2022-05-15 14:40:52', 30000),
(26, 12, '2022', '04', '16', '2022-04-16 19:39:17', '2022-05-16 19:39:17', 30000),
(27, 12, '2022', '05', '13', '2022-05-13 11:31:04', '2022-06-13 11:31:04', 30000),
(28, 12, '2022', '06', '16', '2022-06-16 10:14:46', '2022-07-16 10:14:46', 30000),
(29, 30, '2022', '06', '27', '2022-06-27 20:13:31', '2022-07-27 20:13:31', 30000),
(30, 31, '2022', '06', '28', '2022-06-28 11:07:36', '2022-07-28 11:07:36', 30000),
(31, 12, '2022', '07', '09', '2022-07-09 13:46:47', '2022-08-09 13:46:47', 30000),
(32, 12, '2022', '07', '11', '2022-07-11 11:37:36', '2022-08-11 11:37:36', 30000);

-- --------------------------------------------------------

--
-- Table structure for table `outlets`
--

CREATE TABLE `outlets` (
  `id_owner` int(5) DEFAULT NULL,
  `id_outlet` int(5) NOT NULL,
  `id_category` int(5) DEFAULT NULL,
  `loginable` tinyint(1) DEFAULT NULL,
  `owner_code` char(10) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `outlet_name` char(50) DEFAULT NULL,
  `city` char(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `telp` int(15) DEFAULT NULL,
  `products_ro` tinyint(1) DEFAULT NULL,
  `units_ro` tinyint(1) DEFAULT NULL,
  `categories_ro` tinyint(1) DEFAULT NULL,
  `customers_ro` tinyint(1) DEFAULT NULL,
  `suppliers_ro` tinyint(1) DEFAULT NULL,
  `outlets_ro` tinyint(1) DEFAULT NULL,
  `transactions_ro` tinyint(1) DEFAULT NULL,
  `purchases_ro` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `outlets`
--

INSERT INTO `outlets` (`id_owner`, `id_outlet`, `id_category`, `loginable`, `owner_code`, `pin`, `outlet_name`, `city`, `address`, `telp`, `products_ro`, `units_ro`, `categories_ro`, `customers_ro`, `suppliers_ro`, `outlets_ro`, `transactions_ro`, `purchases_ro`) VALUES
(1, 1, NULL, 0, 'OBWM-00001', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 24, NULL, 0, 'OBAA-00012', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(13, 27, NULL, 0, 'OBUA-00013', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(16, 28, NULL, 0, 'OBTK-00016', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 29, 0, 1, 'OBAA-00012', '1234', 'Cabang Umum', 'Semarang', '', 0, 0, 1, 1, 1, 1, 1, 1, 1),
(17, 30, NULL, 0, 'OBTE-00017', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 31, NULL, 0, 'OBBB-00018', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 32, 65, 1, 'OBAA-00012', '1111', 'Cabang Cafe', 'jakarta', '', 0, 1, 1, 1, 1, 1, 1, 1, 1),
(12, 33, 66, 1, 'OBAA-00012', '2222', 'Cabang Fashion', 'Semarang', '', 0, 0, 1, 1, 1, 1, 1, 1, 1),
(19, 65, NULL, 0, 'OBAN-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 66, NULL, 0, 'OB11-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 67, NULL, 0, 'OB11-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 68, NULL, 0, 'OBAU-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 69, NULL, 0, 'OBA1-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 70, NULL, 0, 'OBAU-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 71, NULL, 0, 'OBTA-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 72, NULL, 0, 'OBTA-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 73, NULL, 0, 'OBTA-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 74, NULL, 0, 'OBAN-00019', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 75, 0, 1, 'OBAA-00012', '123oa', 'Outlet Android', '', '', 0, 1, 1, 1, 1, 1, 1, 1, 1),
(12, 80, 0, 1, 'OBAA-00012', '223344', 'Cabang Sembako 2', 'Jakarta', 'Jln. Raya No 200', 2147483647, 1, 1, 1, 1, 1, 1, 1, 1),
(29, 81, NULL, 0, 'OBTB-00029', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(30, 82, NULL, 0, 'OBTB-00030', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(31, 83, NULL, 0, 'OBWM-00031', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(32, 84, NULL, 0, 'OBTI-00032', '0', 'Pusat', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id_owner` int(5) NOT NULL,
  `created_at` date DEFAULT NULL,
  `business_name` varchar(100) DEFAULT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `owner_code` char(10) DEFAULT NULL,
  `telp` char(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_pro` tinyint(1) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id_owner`, `created_at`, `business_name`, `owner_name`, `owner_code`, `telp`, `email`, `password`, `is_pro`, `start`, `end`) VALUES
(1, '2021-12-27', 'warung miee', 'mas bambange', 'OBWM-00001', '0', 'bambang@gmail.com', '$2y$10$OLTKaYnU5jQTdzo2ZhTCIebfNEZLdbqXyqXJpTzf0u1ySrjdslU.i', NULL, NULL, NULL),
(12, '2021-12-30', 'apa aja ada', 'chakra', 'OBAA-00012', '085678543122', 'chakra@gmail.com', '$2y$10$G4UxJ09fOoDYDXaMZGVvxOD9OHAV52wrGg4hgHlhRbKZnEab7Ub/i', 1, '2022-07-11 11:37:36', '2022-08-11 11:37:36'),
(15, '2022-01-10', 'usaha aa', 'mas aa', 'OBUA-00013', '0', 'nama@nama.com', '$2y$10$GHfVCxz1SK98Ro8cu72pK.sjF.mpQmvqEcR6yAxMukhoIfk3LvCa6', NULL, NULL, NULL),
(16, '2022-01-11', 'toko kelontong', 'bambang cahyadi', 'OBTK-00016', '0', 'cahyadi@gmail.com', '$2y$10$w0UFGa5lOLrCJkrR7AMM6u3pRHEW6fVAzlIdGRh926BQwf1LBDpbG', NULL, NULL, NULL),
(17, '2022-01-12', 'test aaa', 'test aa', 'OBTE-00017', '0', 'a@a.com', '$2y$10$azSB.cWW/dsNKsmTxQDnf.9Q8uuk4SAa5hBt.5IJ7jMEqJTLz1Xyq', NULL, NULL, NULL),
(18, '2022-01-13', 'test bbb', 'test bb', 'OBBB-00018', '0', 'b@b.bb', '$2y$10$AIwEP4Woq8kvgpkqLkUaqeKDkmce6eNpFZ9YcvfUo7CzhKnRqXb2m', NULL, NULL, NULL),
(28, '2022-04-04', 'android1', 'android2', 'OBAN-00019', '0', 'android3@gmail.com', '$2y$10$DtEVR.1xJHPBIaOQD283wOrzLriinHwD7Srup2..IaSYXQBObg/Cq', 0, NULL, NULL),
(29, '2022-06-26', 'Toko Bapak', 'Sarwanto', 'OBTB-00029', '0', 'sarwan@gmail.com', '$2y$10$6xtei9s9ytZmiRYth30Y8ugzNMLGt1FnUGgGGJsBsJ9JiFfIsapVW', NULL, NULL, NULL),
(30, '2022-06-27', 'Toko Besi', 'Ibnu', 'OBTB-00030', '0', 'ibnu@gmail.com', '$2y$10$P0cOjx7DTb4Km7yvBzJR.ebbX75rV6Gh4KhnzxFY/lLyONtjSOiPW', 1, '2022-06-27 20:13:31', '2022-07-27 20:13:31'),
(31, '2022-06-28', 'Warung Mie', 'Riko', 'OBWM-00031', '0', 'riko@gmail.com', '$2y$10$JY1sYNWuHKY/rmpx3jxRpugIzcjzMf0RYDByKpRZPAwkeer0g9Ary', 1, '2022-06-28 11:07:36', '2022-07-28 11:07:36'),
(32, '2022-07-14', 'Toko ikan', 'Wardoyo', 'OBTI-00032', '0', 'wardoyo@gmail.com', '$2y$10$dpO9dxsN48Ju.yzHhCc9LurA61eKGByEUGcLNWk9Hcq6gA1JR7A9e', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id_owner` int(5) DEFAULT NULL,
  `id_product` int(5) NOT NULL,
  `product_img` varchar(255) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `barcode` varchar(30) DEFAULT NULL,
  `id_category` int(5) DEFAULT NULL,
  `capital_price` int(12) DEFAULT NULL,
  `selling_price` int(12) DEFAULT NULL,
  `available_stock` tinyint(1) DEFAULT NULL,
  `id_unit` int(5) DEFAULT NULL,
  `stock_quantity` int(5) DEFAULT NULL,
  `stock_min` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id_owner`, `id_product`, `product_img`, `product_name`, `barcode`, `id_category`, `capital_price`, `selling_price`, `available_stock`, `id_unit`, `stock_quantity`, `stock_min`) VALUES
(1, 144, NULL, 'Roti Manis', '', 0, 1000, 2000, 1, 45, 2, 0),
(1, 145, '659d69005f92031cec835eee5220482a.png', 'Roti Coklat Keju', 'i1', 0, 2000, 3000, 1, 45, 1, 0),
(1, 146, NULL, 'Roti Keju', '', 0, 3000, 4000, 1, 45, 2, 2),
(1, 147, NULL, 'Kripik Singkong', '', 0, 4000, 5000, 1, 45, 1, 5),
(12, 148, NULL, 'roti melon', '', 64, 5000, 6000, 0, 0, 1, 0),
(12, 149, NULL, 'roti melon v2', '', 64, 1000, 1500, 1, 50, 25, 2),
(12, 150, NULL, 'roti melon v3', '', 64, 6000, 7000, 0, 0, 0, 0),
(1, 151, NULL, 'Beras 5KG', '8991609119892', 0, 7000, 7500, 1, 45, 3, 0),
(1, 152, NULL, 'Gula 5KG', '1', 0, 8000, 8500, 0, 0, 1, 0),
(12, 153, NULL, 'beras putih', '', 64, 8000, 10000, 1, 51, 10, 1),
(16, 154, NULL, 'Kopi', '', 62, 10000, 10500, 0, 0, 1, 0),
(16, 157, NULL, 'Beras', '', 0, 11000, 12000, 0, 0, 0, 0),
(12, 158, NULL, 'Sprite', '8992761002022', 64, 12000, 12500, 0, 0, 1, 0),
(12, 170, NULL, 'Aqua', '', 65, 13000, 14000, 1, 50, 21, 0),
(12, 171, NULL, 'Mizone', '', 65, 14000, 15000, 0, 0, 0, 0),
(12, 172, NULL, 'sepatu 1', '', 66, 15000, 16000, 0, 0, 0, 0),
(12, 173, NULL, 'sepatu 2', '', 66, 16000, 17000, 0, 0, 0, 0),
(12, 177, NULL, 'Green Tea', '', 65, 17000, 18000, 0, 0, 1, 0),
(12, 178, NULL, 'Teh Kotak', '', 65, 18000, 19000, 0, 0, 1, 0),
(12, 181, NULL, 'sepatu 3', '', 66, 19000, 20000, 0, 0, 0, 0),
(1, 182, NULL, 'White Coffee', '', 0, 20000, 20500, 0, 0, 1, 0),
(12, 183, NULL, 'White Coffee Luwak', '', 65, 21000, 21500, 0, 0, 1, 0),
(12, 186, NULL, 'Kopi Kapal Api', '', 0, 22000, 22500, 0, 0, 0, 0),
(12, 255, '0319ea25c4c869f2c459963692b577f5.jpg', 'Kerupuk Ikan', '9999', 65, 23000, 23500, 0, 50, 10, 3),
(12, 257, NULL, 'Kue Coklat', '', 0, 24000, 25000, 0, 0, 1, 0),
(12, 258, '148ace8f261818ee5db6d1994810de63.png', 'Minyak', '', 0, 25000, 26000, 0, 0, 1, 0),
(12, 259, NULL, 'Telur', '', 0, 26000, 26500, 1, 50, 1, 0),
(12, 260, NULL, 'Kue Bolu', '', 0, 27000, 27500, 1, 66, 3, 0),
(12, 309, NULL, 'Adem Sari', '', 0, 28000, 28500, 0, 0, 1, 0),
(12, 310, NULL, 'Ale Ale', '', 0, 29000, 30000, 1, 66, 75, 5),
(12, 311, NULL, 'Teh Rio', '', 0, 30000, 31000, 1, 50, 3, 2),
(12, 312, NULL, 'Antangin', '', 0, 31000, 32000, 1, 66, 2, 0),
(12, 313, NULL, 'Masuk Angin', '', 0, 32000, 32500, 1, 67, 3, 0),
(12, 314, NULL, 'Balsem', '', 0, 33000, 33500, 0, 68, 1, 0),
(12, 315, NULL, 'Baygon', '', 0, 34000, 34500, 1, 66, 0, 0),
(12, 317, NULL, 'Detol', '', 0, 35000, 35500, 0, 0, 1, 0),
(12, 325, 'c9edf31a99162285cc6a468e3f7879e3.jpg', 'Kecap Sedap 15ML', '', 0, 500, 1000, 0, 0, 1, 0),
(12, 326, 'bfc9c08b6143dac6058ad93458cf16bb.jpg', 'Kecap Sedap 175ML', '', 0, 500, 1000, 0, 0, 1, 0),
(12, 327, 'afae4a6c6019cd00c3bd4a2a0c03bf4d.jpg', 'Kecap Sedap 225ML', '', 0, 1000, 2000, 0, 0, 1, 0),
(12, 328, NULL, 'Kacang Garuda', '', 0, 4000, 5000, 0, 0, 1, 0),
(31, 330, NULL, 'Mie Ayam', '', 139, 8000, 10000, 1, 131, 93, 2),
(32, 331, NULL, 'Ikan Koi', '', 140, 15000, 45000, 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id_owner` int(5) DEFAULT NULL,
  `id_outlet` int(5) DEFAULT NULL,
  `id_purchase` int(5) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `id_product` int(5) DEFAULT NULL,
  `product_name` char(30) DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL,
  `price` int(8) DEFAULT NULL,
  `id_supplier` int(5) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id_owner`, `id_outlet`, `id_purchase`, `status`, `id_product`, `product_name`, `quantity`, `price`, `id_supplier`, `date`, `time`, `note`) VALUES
(31, 83, 79, 1, 330, 'Mie Ayam', 50, 5000, 54, '2022-06-28', '06:09:36', ''),
(12, 24, 82, 0, 153, 'beras putih', 23, 8000, 53, '2022-07-09', '06:32:44', '');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id_owner` int(5) DEFAULT NULL,
  `id_outlet` int(5) DEFAULT NULL,
  `id_supplier` int(5) NOT NULL,
  `supplier_name` char(50) DEFAULT NULL,
  `city` char(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `telp` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id_owner`, `id_outlet`, `id_supplier`, `supplier_name`, `city`, `address`, `telp`) VALUES
(12, 1, 24, 'PT. Kelapa Muda', 'Ngawi', '', ''),
(12, 24, 26, 'PT. Kelapa Tua', 'Jepara', 'Jl. Kenari no. 13', '08662732562157'),
(12, 24, 30, 'PT. Sejahtera', 'Bandung', 'Jln. Raya 12', '08921322129832'),
(12, 24, 31, 'PT. Raya Abadi', 'Madiun', 'Jln. Raya 92', ''),
(12, 24, 53, 'PT. Abadi Jaya', 'Jakarta', 'Jln. Raya No 100', '085678423761'),
(31, 83, 54, 'PT Mie Instan', 'Jakarta', 'Jln. No 100', '');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id_owner` int(5) DEFAULT NULL,
  `id_outlet` int(5) DEFAULT NULL,
  `id_transaction` int(5) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `invoice` varchar(30) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `method` int(1) DEFAULT NULL,
  `discount` int(3) DEFAULT NULL,
  `grand_total` int(12) DEFAULT NULL,
  `paid_off` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id_owner`, `id_outlet`, `id_transaction`, `customer_name`, `invoice`, `date`, `time`, `method`, `discount`, `grand_total`, `paid_off`) VALUES
(12, 24, 176, 'Umum', 'OBAA-00012-001-0000001', '2022-01-13', '19:51:30', 1, NULL, 56000, 60000),
(12, 24, 178, 'Umum', 'OBAA-00012-001-0000003', '2022-01-13', '19:56:06', 0, NULL, 60000, 100000),
(12, 24, 182, 'Umum', 'OBAA-00012-001-0000001', '2022-01-14', '20:47:53', 0, NULL, 30000, 30000),
(12, 29, 188, 'Umum', 'OBAA-00012-002-0000002', '2022-01-18', '09:33:55', 0, NULL, 16000, 20000),
(12, 32, 190, 'Umum', 'OBAA-00012-003-0000001', '2022-01-18', '20:07:47', 0, NULL, 1, 1),
(12, 32, 191, 'Umum', 'OBAA-00012-003-0000002', '2022-01-18', '21:21:28', 0, NULL, 4, 4),
(12, 32, 192, 'Umum', 'OBAA-00012-003-0000001', '2022-01-19', '12:27:00', 0, NULL, 9, 10),
(12, 32, 196, 'Umum', 'OBAA-00012-003-0000002', '2022-01-19', '13:04:13', 0, NULL, 16, 20),
(12, 29, 198, 'Umum', 'OBAA-00012-002-0000001', '2022-01-19', '13:08:23', 0, NULL, 6000, 10000),
(12, 24, 201, 'Umum', 'OBAA-00012-001-0000001', '2022-01-25', '11:28:32', 0, NULL, 6000, 10000),
(12, 24, 202, 'Umum', 'OBAA-00012-001-0000002', '2022-01-25', '11:29:42', 0, NULL, 10000, 10000),
(12, 24, 204, 'Umum', 'OBAA-00012-001-0000004', '2022-01-25', '12:21:37', 0, NULL, 2, 2),
(1, 1, 205, 'Umum', 'OBWM-00001-001-0000001', '2022-01-25', '12:23:22', 0, NULL, 10000, 10000),
(1, 1, 206, 'Umum', 'OBWM-00001-001-0000002', '2022-01-25', '12:23:43', 0, NULL, 20000, 20000),
(12, 24, 235, 'Umum', 'OBAA-00012-001-0000001', '2022-03-30', '13:13:44', 0, NULL, 10000, 3000),
(12, 24, 237, 'Umum', 'OBAA-00012-001-0000002', '2022-03-30', '13:15:15', 0, NULL, 15555, 15555),
(12, 24, 325, 'Umum', 'OBAA-00012-001-0000001', '2022-04-03', '11:06:02', 0, NULL, 1500, 1500),
(12, 24, 326, 'Umum', 'OBAA-00012-001-0000002', '2022-04-03', '11:07:26', 0, NULL, 6000, 10000),
(12, 24, 327, 'Umum', 'OBAA-00012-001-0000003', '2022-04-03', '14:45:01', 0, NULL, 30000, 30000),
(12, 24, 386, 'Umum', 'OBAA-00012-001-0000001', '2022-06-24', '20:08:40', 0, 0, 60000, 100000),
(12, 24, 387, 'mas adit', 'OBAA-00012-001-0000002', '2022-06-24', '20:10:20', 0, 1000, 9000, 10000),
(12, 24, 388, 'mas dicky', 'OBAA-00012-001-0000003', '2022-06-24', '20:17:02', 0, 7500, 42500, 50000),
(12, 24, 390, 'mas riko', 'OBAA-00012-001-0000004', '2022-06-24', '20:22:14', 0, 3250, 29250, 50000),
(12, 32, 392, 'Umum', 'OBAA-00012-003-0000001', '2022-06-27', '13:02:54', 0, NULL, 47000, 50000),
(31, 83, 393, 'Umum', 'OBWM-00031-001-0000001', '2022-06-28', '11:04:33', 0, 0, 70000, 100000),
(12, 24, 398, 'Umum', 'OBAA-00012-001-0000001', '2022-07-09', '11:00:12', 0, NULL, 176000, 200000),
(12, 24, 399, 'mas adit', 'OBAA-00012-001-0000001', '2022-07-11', '11:34:46', 0, NULL, 87000, 100000),
(12, 24, 400, 'Umum', 'OBAA-00012-001-0000002', '2022-07-11', '11:36:31', 0, NULL, 10000, 10000),
(12, 24, 401, 'Umum', 'OBAA-00012-001-0000001', '2022-07-17', '09:51:28', 0, NULL, 265500, 300000),
(12, 24, 402, 'Umum', 'OBAA-00012-001-0000002', '2022-07-17', '10:44:37', 0, 0, 113010, 120000);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id_transaction` int(5) DEFAULT NULL,
  `is_variant` tinyint(1) DEFAULT NULL,
  `id_product` int(5) DEFAULT NULL,
  `invoice` varchar(30) DEFAULT NULL,
  `product_name` char(100) DEFAULT NULL,
  `capital_price` int(12) DEFAULT NULL,
  `selling_price` int(12) DEFAULT NULL,
  `quantity` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id_transaction`, `is_variant`, `id_product`, `invoice`, `product_name`, `capital_price`, `selling_price`, `quantity`) VALUES
(176, 0, 153, 'OBAA-00012-001-0000001', 'beras putih v1', 0, 10000, 5),
(176, 0, 158, 'OBAA-00012-001-0000001', 'Sprite', 0, 6000, 1),
(178, 0, 158, 'OBAA-00012-001-0000003', 'Sprite', 0, 6000, 10),
(182, 0, 158, 'OBAA-00012-001-0000001', 'Sprite', 0, 6000, 5),
(188, 0, 158, 'OBAA-00012-002-0000002', 'Sprite', 0, 6000, 2),
(188, 0, 148, 'OBAA-00012-002-0000002', 'roti melon', 0, 1000, 2),
(188, 1, 164, 'OBAA-00012-002-0000002', 'roti melon v3, v1', 0, 1000, 2),
(190, 0, 170, 'OBAA-00012-003-0000001', 'cafe 1', 0, 1, 1),
(191, 0, 171, 'OBAA-00012-003-0000002', 'cafe 2', 0, 2, 2),
(192, 0, 177, 'OBAA-00012-003-0000001', 'cafe 3', 0, 3, 3),
(196, 0, 178, 'OBAA-00012-003-0000002', 'cafe 4', 0, 4, 4),
(198, 0, 158, 'OBAA-00012-002-0000001', 'Sprite', 0, 6000, 1),
(201, 0, 158, 'OBAA-00012-001-0000001', 'Sprite', 0, 6000, 1),
(202, 0, 153, 'OBAA-00012-001-0000002', 'beras putih v1', 0, 10000, 1),
(204, 0, 171, 'OBAA-00012-001-0000004', 'cafe 2', 0, 2, 1),
(205, 0, 182, 'OBWM-00001-001-0000001', '1', 0, 10000, 1),
(206, 0, 182, 'OBWM-00001-001-0000002', '1', 0, 10000, 2),
(235, 0, 153, 'OBAA-00012-001-0000001', 'beras putih v1', 0, 10000, 1),
(237, 0, 153, 'OBAA-00012-001-0000002', 'beras putih v1', 0, 10000, 1),
(237, 0, 259, 'OBAA-00012-001-0000002', '4 test unit web', 4444, 5555, 1),
(325, 0, 310, 'OBAA-00012-001-0000001', '1 c1', 1000, 1500, 1),
(326, 0, 310, 'OBAA-00012-001-0000002', '1 c1', 1000, 1500, 4),
(327, 0, 153, 'OBAA-00012-001-0000003', 'beras putih v1', 0, 10000, 3),
(386, 0, 310, 'OBAA-00012-001-0000001', 'Ale Ale', 29000, 30000, 2),
(387, 0, 153, 'OBAA-00012-001-0000002', 'beras putih v1', 9000, 10000, 1),
(388, 0, 153, 'OBAA-00012-001-0000003', 'beras putih v1', 9000, 10000, 1),
(388, 0, 310, 'OBAA-00012-001-0000003', 'Ale Ale', 29000, 30000, 1),
(388, 0, 328, 'OBAA-00012-001-0000003', 'Kacang Garuda', 4000, 5000, 2),
(390, 0, 313, 'OBAA-00012-001-0000004', 'Masuk Angin', 32000, 32500, 1),
(392, 0, 255, 'OBAA-00012-003-0000001', 'Kerupuk Ikan', 23000, 23500, 2),
(393, 0, 330, 'OBWM-00031-001-0000001', 'Mie Ayam', 8000, 10000, 7),
(398, 0, 153, 'OBAA-00012-001-0000001', 'beras putih', 8000, 10000, 5),
(398, 0, 312, 'OBAA-00012-001-0000001', 'Antangin', 31000, 32000, 3),
(398, 0, 310, 'OBAA-00012-001-0000001', 'Ale Ale', 29000, 30000, 1),
(399, 0, 325, 'OBAA-00012-001-0000001', 'Kecap Sedap 15ML', 500, 1000, 18),
(399, 0, 315, 'OBAA-00012-001-0000001', 'Baygon', 34000, 34500, 2),
(400, 0, 153, 'OBAA-00012-001-0000002', 'beras putih', 8000, 10000, 1),
(401, 0, 312, 'OBAA-00012-001-0000001', 'Antangin', 31000, 32000, 5),
(401, 0, 315, 'OBAA-00012-001-0000001', 'Baygon', 34000, 34500, 3),
(401, 1, 235, 'OBAA-00012-001-0000001', 'Adem Sari, Adem Sari Bijian', 1500, 2000, 1),
(402, 0, 312, 'OBAA-00012-001-0000002', 'Antangin', 31000, 32000, 2),
(402, 0, 170, 'OBAA-00012-001-0000002', 'Aqua', 13000, 14000, 1),
(402, 1, 196, 'OBAA-00012-001-0000002', 'Minyak, test 12', 6002, 7002, 5);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id_owner` int(5) DEFAULT NULL,
  `id_unit` int(5) NOT NULL,
  `unit_name` char(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id_owner`, `id_unit`, `unit_name`) VALUES
(1, 43, 'gram'),
(1, 45, 'paket'),
(12, 50, 'pcs'),
(12, 51, 'kg'),
(12, 52, 'ton'),
(16, 57, 'potong'),
(16, 58, 'ons'),
(12, 66, 'bijian'),
(12, 67, 'meter'),
(12, 68, 'roll'),
(12, 117, 'karton'),
(12, 118, 'slop'),
(12, 119, 'sak'),
(12, 120, 'lusin'),
(12, 121, 'porsi'),
(12, 122, 'botol'),
(12, 123, 'gelas'),
(12, 124, 'ekor'),
(12, 125, 'unit 1'),
(12, 127, 'unit 2'),
(12, 130, 'unit 3'),
(31, 131, 'Porsi'),
(32, 132, 'ekor');

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

CREATE TABLE `variants` (
  `id_product` int(5) DEFAULT NULL,
  `id_variant` int(5) NOT NULL,
  `variant_name` varchar(100) DEFAULT NULL,
  `capital_price` int(12) DEFAULT NULL,
  `selling_price` int(12) DEFAULT NULL,
  `available_stock` tinyint(1) DEFAULT NULL,
  `id_unit` int(5) DEFAULT NULL,
  `stock_quantity` int(5) DEFAULT NULL,
  `stock_min` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `variants`
--

INSERT INTO `variants` (`id_product`, `id_variant`, `variant_name`, `capital_price`, `selling_price`, `available_stock`, `id_unit`, `stock_quantity`, `stock_min`) VALUES
(150, 164, 'test 1', 0, 1000, 0, 0, 1, 0),
(150, 165, 'test 2', 0, 2000, 1, 50, 30, 2),
(152, 166, 'test 3', 0, 1, 0, 0, 1, 0),
(152, 167, 'test 4', 0, 2, 0, 0, 1, 0),
(151, 168, 'test 5', 0, 3, 0, 0, 1, 0),
(151, 169, 'test 6', 0, 3, 0, 0, 1, 0),
(157, 170, 'Unggulan', 5000, 10000, 0, 0, 1, 0),
(157, 171, 'Reguler', 0, 10000, 1, 58, 1000, 2),
(257, 191, 'test 7', 4001, 5001, 0, 0, 1, 0),
(257, 192, 'test 8', 5001, 6001, 1, 67, 1, 0),
(257, 193, 'test 9', 6001, 7001, 0, 0, 1, 0),
(258, 194, 'test 10', 4002, 5002, 1, 52, 1, 0),
(258, 195, 'test 11', 5002, 6002, 0, 0, 1, 0),
(258, 196, 'test 12', 6002, 7002, 1, 67, 94, 9),
(309, 234, 'Adem Sari Pcs', 10000, 12000, 0, 0, 1, 0),
(309, 235, 'Adem Sari Bijian', 1500, 2000, 1, 50, 4, 0),
(183, 238, 'test 13', 1, 2, 0, 0, 1, 0),
(183, 239, 'test 14', 1, 2, 0, 0, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indexes for table `finance`
--
ALTER TABLE `finance`
  ADD PRIMARY KEY (`id_finance`);

--
-- Indexes for table `history_payment`
--
ALTER TABLE `history_payment`
  ADD PRIMARY KEY (`id_history_payment`);

--
-- Indexes for table `outlets`
--
ALTER TABLE `outlets`
  ADD PRIMARY KEY (`id_outlet`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id_owner`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id_purchase`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id_transaction`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id_unit`);

--
-- Indexes for table `variants`
--
ALTER TABLE `variants`
  ADD PRIMARY KEY (`id_variant`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customer` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `history_payment`
--
ALTER TABLE `history_payment`
  MODIFY `id_history_payment` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `outlets`
--
ALTER TABLE `outlets`
  MODIFY `id_outlet` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id_owner` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=332;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id_purchase` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id_supplier` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id_transaction` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=403;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id_unit` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `variants`
--
ALTER TABLE `variants`
  MODIFY `id_variant` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
