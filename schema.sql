-- Database Schema for stefanzweig.eu

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ebooks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `original_title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `price` DECIMAL(10,2) NOT NULL,
  `synopsis` TEXT NOT NULL,
  `sample_text` TEXT DEFAULT NULL,
  `cover_image` VARCHAR(255) NOT NULL DEFAULT 'covers/default.jpg',
  `file_epub_path` VARCHAR(255) NOT NULL,
  `file_pdf_path` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_ref` VARCHAR(100) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_status` ENUM('pending', 'paid', 'expired', 'failed') DEFAULT 'pending',
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `sakurupiah_trx_id` VARCHAR(100) DEFAULT NULL,
  `payment_no` VARCHAR(100) DEFAULT NULL,
  `qr_string` TEXT DEFAULT NULL,
  `payment_url` VARCHAR(255) DEFAULT NULL,
  `expired_at` DATETIME DEFAULT NULL,
  `paid_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `ebook_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ebook_id`) REFERENCES `ebooks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_downloads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `ebook_id` INT NOT NULL,
  `download_count` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_ebook` (`user_id`, `ebook_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ebook_id`) REFERENCES `ebooks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Kategori
INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Novella & Fiksi', 'novella-fiksi'),
(2, 'Biografi Sejarah', 'biografi-sejarah'),
(3, 'Essai & Otobiografi', 'essai-otobiografi')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Data Awal Ebook Sample
INSERT INTO `ebooks` (`id`, `category_id`, `title`, `original_title`, `slug`, `price`, `synopsis`, `sample_text`, `cover_image`, `file_epub_path`, `file_pdf_path`) VALUES
(1, 1, 'Novel Catur', 'Schachnovelle', 'novel-catur', 45000.00, 'Karya mahakarya terakhir Stefan Zweig tentang pergulatan kejiwaan seorang tahanan Gestapo yang bertahan hidup lewat permainan catur mental.', 'Di atas kapal uap yang berlayar dari New York menuju Buenos Aires...', 'covers/default.jpg', 'storage/epub/sample.epub', 'storage/pdf/sample.pdf')
ON DUPLICATE KEY UPDATE `title`=`title`;

-- Default Admin Account (Password: admin123)
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`) VALUES
(1, 'Administrator Zweig', 'admin@stefanzweig.eu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.XLOaY21iC', 'admin')
ON DUPLICATE KEY UPDATE `email`=`email`;