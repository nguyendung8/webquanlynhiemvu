-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2024 at 11:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webquanlybenhvien`
--

-- --------------------------------------------------------

--
-- Table structure for table `bac_si`
--

CREATE TABLE `bac_si` (
  `id` int(11) NOT NULL,
  `chuyen_khoa` varchar(100) DEFAULT NULL,
  `kinh_nghiem` int(11) DEFAULT NULL,
  `gioi_thieu_ngan_gon` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `benh_nhan`
--

CREATE TABLE `benh_nhan` (
  `id` int(11) NOT NULL,
  `nhom_mau` varchar(10) DEFAULT NULL,
  `tien_su_benh` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id` int(11) NOT NULL,
  `benh_nhan_id` int(11) DEFAULT NULL,
  `bac_si_id` int(11) DEFAULT NULL,
  `diem_danh_gia` int(11) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `ngay_gui` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dich_vu`
--

CREATE TABLE `dich_vu` (
  `id` int(11) NOT NULL,
  `ten_dich_vu` varchar(100) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `gia` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `don_thuoc`
--

CREATE TABLE `don_thuoc` (
  `id` int(11) NOT NULL,
  `benh_nhan_id` int(11) DEFAULT NULL,
  `bac_si_id` int(11) DEFAULT NULL,
  `ngay_ke` datetime DEFAULT NULL,
  `danh_sach_thuoc` text DEFAULT NULL,
  `don_gia` int(100) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ho_so_benh_nhan`
--

CREATE TABLE `ho_so_benh_nhan` (
  `id` int(11) NOT NULL,
  `benh_nhan_id` int(11) DEFAULT NULL,
  `bac_si_id` int(11) DEFAULT NULL,
  `ngay_kham` datetime DEFAULT NULL,
  `chan_doan` text DEFAULT NULL,
  `ket_qua_xet_nghiem` text DEFAULT NULL,
  `ghi_chu_bac_si` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lich_hen`
--

CREATE TABLE `lich_hen` (
  `id` int(11) NOT NULL,
  `benh_nhan_id` int(11) DEFAULT NULL,
  `bac_si_id` int(11) DEFAULT NULL,
  `thoi_gian` datetime DEFAULT NULL,
  `trang_thai` enum('cho_xac_nhan','da_xac_nhan','huy','hoan_thanh') DEFAULT 'cho_xac_nhan',
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lich_lam_viec`
--

CREATE TABLE `lich_lam_viec` (
  `id` int(11) NOT NULL,
  `bac_si_id` int(11) DEFAULT NULL,
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `trang_thai` enum('co_san','dang_ban') DEFAULT 'co_san'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int(11) NOT NULL,
  `ten` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `vai_tro` enum('benh_nhan','bac_si') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quan_tri_vien`
--

CREATE TABLE `quan_tri_vien` (
  `id` int(11) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tin_tuc`
--

CREATE TABLE `tin_tuc` (
  `id` int(11) NOT NULL,
  `tieu_de` varchar(255) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `ngay_dang` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xet_nghiem`
--

CREATE TABLE `xet_nghiem` (
  `id` int(11) NOT NULL,
  `benh_nhan_id` int(11) DEFAULT NULL,
  `bac_si_id` int(11) DEFAULT NULL,
  `loai_xet_nghiem` varchar(100) DEFAULT NULL,
  `ngay_thuc_hien` datetime DEFAULT NULL,
  `ket_qua` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bac_si`
--
ALTER TABLE `bac_si`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `benh_nhan`
--
ALTER TABLE `benh_nhan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benh_nhan_id` (`benh_nhan_id`),
  ADD KEY `bac_si_id` (`bac_si_id`);

--
-- Indexes for table `dich_vu`
--
ALTER TABLE `dich_vu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `don_thuoc`
--
ALTER TABLE `don_thuoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benh_nhan_id` (`benh_nhan_id`),
  ADD KEY `bac_si_id` (`bac_si_id`);

--
-- Indexes for table `ho_so_benh_nhan`
--
ALTER TABLE `ho_so_benh_nhan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benh_nhan_id` (`benh_nhan_id`),
  ADD KEY `bac_si_id` (`bac_si_id`);

--
-- Indexes for table `lich_hen`
--
ALTER TABLE `lich_hen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benh_nhan_id` (`benh_nhan_id`),
  ADD KEY `bac_si_id` (`bac_si_id`);

--
-- Indexes for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bac_si_id` (`bac_si_id`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quan_tri_vien`
--
ALTER TABLE `quan_tri_vien`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benh_nhan_id` (`benh_nhan_id`);

--
-- Indexes for table `tin_tuc`
--
ALTER TABLE `tin_tuc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xet_nghiem`
--
ALTER TABLE `xet_nghiem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benh_nhan_id` (`benh_nhan_id`),
  ADD KEY `bac_si_id` (`bac_si_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dich_vu`
--
ALTER TABLE `dich_vu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `don_thuoc`
--
ALTER TABLE `don_thuoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ho_so_benh_nhan`
--
ALTER TABLE `ho_so_benh_nhan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lich_hen`
--
ALTER TABLE `lich_hen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quan_tri_vien`
--
ALTER TABLE `quan_tri_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tin_tuc`
--
ALTER TABLE `tin_tuc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `xet_nghiem`
--
ALTER TABLE `xet_nghiem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bac_si`
--
ALTER TABLE `bac_si`
  ADD CONSTRAINT `bac_si_ibfk_1` FOREIGN KEY (`id`) REFERENCES `nguoi_dung` (`id`);

--
-- Constraints for table `benh_nhan`
--
ALTER TABLE `benh_nhan`
  ADD CONSTRAINT `benh_nhan_ibfk_1` FOREIGN KEY (`id`) REFERENCES `nguoi_dung` (`id`);

--
-- Constraints for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`benh_nhan_id`) REFERENCES `benh_nhan` (`id`),
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`bac_si_id`) REFERENCES `bac_si` (`id`);

--
-- Constraints for table `don_thuoc`
--
ALTER TABLE `don_thuoc`
  ADD CONSTRAINT `don_thuoc_ibfk_1` FOREIGN KEY (`benh_nhan_id`) REFERENCES `benh_nhan` (`id`),
  ADD CONSTRAINT `don_thuoc_ibfk_2` FOREIGN KEY (`bac_si_id`) REFERENCES `bac_si` (`id`);

--
-- Constraints for table `ho_so_benh_nhan`
--
ALTER TABLE `ho_so_benh_nhan`
  ADD CONSTRAINT `ho_so_benh_nhan_ibfk_1` FOREIGN KEY (`benh_nhan_id`) REFERENCES `benh_nhan` (`id`),
  ADD CONSTRAINT `ho_so_benh_nhan_ibfk_2` FOREIGN KEY (`bac_si_id`) REFERENCES `bac_si` (`id`);

--
-- Constraints for table `lich_hen`
--
ALTER TABLE `lich_hen`
  ADD CONSTRAINT `lich_hen_ibfk_1` FOREIGN KEY (`benh_nhan_id`) REFERENCES `benh_nhan` (`id`),
  ADD CONSTRAINT `lich_hen_ibfk_2` FOREIGN KEY (`bac_si_id`) REFERENCES `bac_si` (`id`);

--
-- Constraints for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  ADD CONSTRAINT `lich_lam_viec_ibfk_1` FOREIGN KEY (`bac_si_id`) REFERENCES `bac_si` (`id`);

--
-- Constraints for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`benh_nhan_id`) REFERENCES `benh_nhan` (`id`);

--
-- Constraints for table `xet_nghiem`
--
ALTER TABLE `xet_nghiem`
  ADD CONSTRAINT `xet_nghiem_ibfk_1` FOREIGN KEY (`benh_nhan_id`) REFERENCES `benh_nhan` (`id`),
  ADD CONSTRAINT `xet_nghiem_ibfk_2` FOREIGN KEY (`bac_si_id`) REFERENCES `bac_si` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
