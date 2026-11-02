-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 28, 2010 at 01:57 PM
-- Server version: 5.0.77
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sugartiny1`
--

-- --------------------------------------------------------

--
-- Table structure for table `crm_render_history`
--

CREATE TABLE IF NOT EXISTS `crm_render_history` (
  `id` varchar(255) NOT NULL,
  `render_start_time` varchar(255) NOT NULL,
  `render_end_time` varchar(255) NOT NULL,
  `total_render_time` varchar(255) NOT NULL,
  `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `crm_render_history`
--


-- --------------------------------------------------------

--
-- Table structure for table `crm_serve_history`
--

CREATE TABLE IF NOT EXISTS `crm_serve_history` (
  `id` varchar(50) NOT NULL,
  `requested_url` varchar(1024) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `campaign` varchar(50) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `serve_start_time` varchar(25) NOT NULL,
  `serve_end_time` varchar(25) NOT NULL,
  `total_serve_time` varchar(15) NOT NULL,
  `push_sent_time` varchar(25) NOT NULL,
  `push_received_time` varchar(25) NOT NULL,
  `client_server_time_diff` varchar(15) NOT NULL,
  `network_latency` varchar(15) NOT NULL,
  `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `crm_serve_history`
--

