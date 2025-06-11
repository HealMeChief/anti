-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 11 2025 г., 11:25
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `magaz`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Дамп данных таблицы `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `user_id`, `session_id`, `product_id`, `quantity`, `added_at`) VALUES
(42, NULL, 'a3ff662f032d754e2ccee699c1397badbea8c9ebf39d63e84a09ae1cb052015f', 3, 2, '2025-06-11 08:33:18'),
(45, NULL, '7f4a2db2d76251a1ccae5706ba3b384aa57859e09cf2ec0d9e7bdf89449bd367', 6, 2, '2025-06-11 09:21:31'),
(46, NULL, '7f4a2db2d76251a1ccae5706ba3b384aa57859e09cf2ec0d9e7bdf89449bd367', 5, 2, '2025-06-11 09:21:31');

-- --------------------------------------------------------

--
-- Структура таблицы `guest_sessions`
--

CREATE TABLE `guest_sessions` (
  `session_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `guest_sessions`
--

INSERT INTO `guest_sessions` (`session_id`, `created_at`, `last_activity`) VALUES
('7f4a2db2d76251a1ccae5706ba3b384aa57859e09cf2ec0d9e7bdf89449bd367', '2025-06-10 17:12:13', '2025-06-10 17:12:13'),
('a3ff662f032d754e2ccee699c1397badbea8c9ebf39d63e84a09ae1cb052015f', '2025-06-11 08:32:35', '2025-06-11 08:32:35');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_session_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(512) NOT NULL,
  `hover_image_url` varchar(512) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`product_id`, `name`, `price`, `description`, `image_url`, `hover_image_url`, `stock_quantity`, `is_active`, `created_at`) VALUES
(1, 'Футболка \"Хабаровск-Бишкек\"', 1990.00, NULL, 'https://thumb.tildacdn.com/tild3561-6362-4434-b836-356466653432/-/format/webp/3424936489.jpg', 'https://thumb.tildacdn.com/tild6239-6361-4534-b162-373735616131/-/format/webp/2147688686.jpg', 100, 1, '2025-06-10 13:34:01'),
(2, 'Худи \"Двуглавый Орел\"', 3990.00, NULL, 'https://thumb.tildacdn.com/tild6333-3266-4433-b633-396161366631/-/format/webp/3424843760.jpg', 'https://thumb.tildacdn.com/tild6561-3864-4537-a637-316465306531/-/format/webp/3146824184.jpg', 100, 1, '2025-06-10 13:35:25'),
(3, 'Худи \"Хабаровск-Бишкек\"', 3990.00, NULL, 'https://thumb.tildacdn.com/tild6539-3537-4631-a231-316362386433/-/format/webp/3424939947_1.jpg', 'https://thumb.tildacdn.com/tild3765-3165-4432-a564-393331643465/-/format/webp/3146865139.jpg', 100, 1, '2025-06-10 13:37:30'),
(4, 'Футболка \"Двуглавый Орел\"\r\n\r\n', 1990.00, NULL, 'https://thumb.tildacdn.com/tild3266-6462-4066-a264-306166316430/-/format/webp/3424920545.jpg', 'https://thumb.tildacdn.com/tild6139-6663-4162-b732-383135663531/-/format/webp/2745011012.jpg', 100, 1, '2025-06-10 13:38:04'),
(5, 'Футболка \"Anti Toxic Anti Hype\"', 1990.00, NULL, 'https://thumb.tildacdn.com/tild3864-3234-4365-b464-376137656335/-/format/webp/3424939907.jpg', 'https://thumb.tildacdn.com/tild6138-3031-4363-a166-666163326138/-/format/webp/2147732117.jpg', 100, 1, '2025-06-10 13:38:54'),
(6, 'Худи \"Anti Toxic Anti Hype\"', 3990.00, NULL, 'https://thumb.tildacdn.com/tild3531-3035-4631-a362-663265646534/-/format/webp/3424951625.jpg', 'https://thumb.tildacdn.com/tild3632-3466-4632-b234-353239623934/-/format/webp/3079355354.jpg', 100, 1, '2025-06-10 13:39:20'),
(7, 'Футболка \"I BALL WAS RAWT\"', 1990.00, NULL, 'https://thumb.tildacdn.com/tild6539-6630-4464-b436-393563366465/-/format/webp/3424952597.jpg', 'https://thumb.tildacdn.com/tild3731-6561-4838-b466-323933633038/-/format/webp/2147749006.jpg', 100, 1, '2025-06-10 13:40:04'),
(8, 'Худи \"Антихайпик\"', 3990.00, NULL, 'https://thumb.tildacdn.com/tild3365-6135-4635-a333-313130656334/-/format/webp/3424939942.jpg', 'https://thumb.tildacdn.com/tild3934-3432-4262-a564-346361663964/-/format/webp/3079356469.jpg', 100, 1, '2025-06-10 13:40:40');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `address`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'zxczxc@mail.ru', 'zxczxc', 'Жмышенко', 'Валерий', '', NULL, '2025-06-10 18:49:02', '2025-06-10 18:49:02', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Индексы таблицы `guest_sessions`
--
ALTER TABLE `guest_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `guest_session_id` (`guest_session_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `guest_sessions` (`session_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`guest_session_id`) REFERENCES `guest_sessions` (`session_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
