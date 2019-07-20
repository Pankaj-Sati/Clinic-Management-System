-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2018 at 12:43 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- Patient Details table to store personal details of a patient

CREATE TABLE `patient_details` (
  `patient_id` int(9) PRIMARY KEY AUTO_INCREMENT,
  `first_name` char(250) NOT NULL,
  `last_name` char(250) NOT NULL,
  `year_of_birth` smallint(4) NOT NULL,
  `gender` set('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
  `weight` smallint(3) DEFAULT NULL,
  `height` smallint(3) DEFAULT NULL,
  `city` char(250) DEFAULT NULL,
  `locality` char(250) DEFAULT NULL,
  `street_address` varchar(500) DEFAULT NULL,
  `occupation` char(250) DEFAULT NULL,
  `mobile_number` bigint(10) NOT NULL,
  `email_id` char(250) DEFAULT NULL,
  `blood_group` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


