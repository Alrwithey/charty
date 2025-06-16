-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2023 at 10:00 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `charity_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$f9M.B66b5b5sI.3tWv7FHe/WqG2TzX6G5.Jg8C/v.v9nZ2UeB5n7S');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `created_at`) VALUES
(1, 'تدشين الحملة السنوية لصحة العيون', 'برعاية كريمة من أمير منطقة المدينة المنورة، دشنت الجمعية حملتها السنوية للتوعية بأهمية الكشف المبكر عن أمراض العيون. تهدف الحملة إلى الوصول لأكبر شريحة من المجتمع وتقديم الفحوصات المجانية.', 'uploads/news1.jpg', '2023-07-24 07:55:54'),
(2, 'الجمعية توقع اتفاقية شراكة مع مستشفى الملك فهد', 'في خطوة لتعزيز خدماتها، وقعت الجمعية اتفاقية شراكة استراتيجية مع مستشفى الملك فهد التخصصي لإجراء العمليات الجراحية المعقدة للحالات التي ترعاها الجمعية.', 'uploads/news2.jpg', '2023-07-24 07:55:54');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `partner_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `icon_class` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `button_text` varchar(100) NOT NULL,
  `service_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `icon_class`, `title`, `description`, `button_text`, `service_order`) VALUES
(1, 'fa-solid fa-hand-holding-dollar', 'إجراء العمليات الجراحية', 'الكشف المبكر وإجراء العمليات الجراحية للمرضى المحتاجين بمنطقة المدينة المنورة.', 'تبرع لهم', 1),
(2, 'fa-solid fa-glasses', 'توفير النظارات الطبية', 'دعم وتوفير نظارات وعدسات طبية للمصابين بضعف البصر من المحتاجين.', 'ساهم بتوفير نظارات', 2),
(3, 'fa-solid fa-bullhorn', 'التثقيف الصحي للمجتمع', 'من خلال الفحص المبكر والمنشورات ومقاطع الفيديو التوعوية والفعاليات والأنشطة.', 'تبرع الآن', 3);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('accent_color', '#f7b538'),
('contact_address', 'المدينة المنورة، المملكة العربية السعودية'),
('contact_email', 'info@eyeschairty.com'),
('contact_phone', '920012345'),
('facebook_url', 'https://facebook.com'),
('google_map_embed', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d465132.8122288086!2d39.34081734891398!3d24.47135327265887!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x15bdbf016f90824b%3A0x79471542848c0490!2sMedina!5e0!3m2!1sen!2ssa!4v1689771122334!5m2!1sen!2ssa\" width=\"100%\" height=\"200\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
('instagram_url', 'https://instagram.com'),
('main_color', '#1a535c'),
('site_logo', 'uploads/logo.png'),
('site_name', 'جمعية عيون طيبة الخيرية'),
('twitter_url', 'https://twitter.com');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `stat_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `title`, `value`, `stat_order`) VALUES
(1, 'المحتاجين الذين تم مساعدتهم', 1500, 4),
(2, 'قيمة المساعدات (بالريال)', 2500000, 3),
(3, 'عدد المشاريع المنفذة', 85, 2),
(4, 'عدد الحملات الخيرية', 30, 1);

--
-- Indexes for dumped tables
--

ALTER TABLE `admin_users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);
ALTER TABLE `news` ADD PRIMARY KEY (`id`);
ALTER TABLE `partners` ADD PRIMARY KEY (`id`);
ALTER TABLE `services` ADD PRIMARY KEY (`id`);
ALTER TABLE `settings` ADD PRIMARY KEY (`setting_key`);
ALTER TABLE `stats` ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `admin_users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `news` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `partners` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `stats` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;