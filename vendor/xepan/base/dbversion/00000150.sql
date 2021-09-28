-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 27, 2017 at 11:50 AM
-- Server version: 10.1.19-MariaDB-1~xenial
-- PHP Version: 7.0.15-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `printonclick`
--

-- --------------------------------------------------------

--
-- Table structure for table `item_serial`
--

CREATE TABLE `item_serial` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `is_return` tinyint(4) DEFAULT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `purchase_invoice_id` int(11) DEFAULT NULL,
  `sale_order_id` int(11) DEFAULT NULL,
  `sale_invoice_id` int(11) DEFAULT NULL,
  `dispatch_id` int(11) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `is_available` tinyint(4) DEFAULT NULL,
  `narration` text,
  `qsp_detail_id` int(11) DEFAULT NULL,
  `purchase_order_detail_id` int(11) DEFAULT NULL,
  `purchase_invoice_detail_id` int(11) DEFAULT NULL,
  `sale_order_detail_id` int(11) DEFAULT NULL,
  `sale_invoice_detail_id` int(11) DEFAULT NULL,
  `transaction_row_id` int(11) DEFAULT NULL,
  `dispatch_row_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `item_serial`
--
ALTER TABLE `item_serial`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `item_serial`
--
ALTER TABLE `item_serial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;