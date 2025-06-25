-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0
-- Время создания: Июн 18 2025 г., 10:38
-- Версия сервера: 8.0.41
-- Версия PHP: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sportgo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `admins`
--

INSERT INTO `admins` (`admin_id`, `login`, `password`) VALUES
(1, 'admin', '$2a$10$oaL2Pfz0V2SfQzd1YlvCFeFdj1L/tJabHEqZajWJ9CSBx/KzMlRJS');

-- --------------------------------------------------------

--
-- Структура таблицы `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price_per_hour` decimal(10,2) NOT NULL,
  `available_quantity` int NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `name`, `type`, `price_per_hour`, `available_quantity`, `description`) VALUES
(1, 'Горный велосипед', 'Велосипед', 350.00, 4, 'Качественный горный велосипед для сложных трасс'),
(2, 'Роликовые коньки', 'Ролики', 200.00, 8, 'Размеры: 38-45, регулируемые'),
(3, 'Туристическая палатка', 'Туризм', 500.00, 3, '4-местная, водонепроницаемая');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `equipment_id` int NOT NULL,
  `point_id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card') NOT NULL,
  `status` enum('new','confirmed','completed','cancelled') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `equipment_id`, `point_id`, `start_time`, `end_time`, `total_price`, `payment_method`, `status`, `created_at`) VALUES
(1, 6, 1, 1, '2025-06-16 13:12:00', '2025-06-16 13:14:00', 11.67, 'cash', 'confirmed', '2025-06-16 11:11:56'),
(2, 7, 1, 1, '2025-06-18 12:06:00', '2025-06-19 10:06:00', 7700.00, 'cash', 'completed', '2025-06-18 05:06:26'),
(3, 8, 1, 1, '2025-06-18 11:17:00', '2025-06-19 10:17:00', 8050.00, 'card', 'confirmed', '2025-06-18 05:17:45'),
(4, 8, 1, 1, '2025-06-20 10:35:00', '2025-06-22 10:35:00', 16800.00, 'card', 'new', '2025-06-18 05:35:56');

-- --------------------------------------------------------

--
-- Структура таблицы `pickup_points`
--

CREATE TABLE `pickup_points` (
  `point_id` int NOT NULL,
  `address` varchar(200) NOT NULL,
  `working_hours` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `pickup_points`
--

INSERT INTO `pickup_points` (`point_id`, `address`, `working_hours`) VALUES
(1, 'Ростовская 18', '');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `phone`, `email`, `login`, `password`, `registration_date`) VALUES
(6, 'Миша Миша Миша', '+78888888888', 'df@yandex.ru', 'qaz', '$2y$10$BJ6IJ9SV3wS0So1IhEIJFeWmaomepmmIKNQYxfZxxByyQDcsADswG', '2025-06-16 10:49:50'),
(7, 'Кулагин Лев Дмитриевич', '+79272888885', 'tjkh@iilj.com', 'lev', '$2y$10$ymGPe0ClYgFXVUYPkO4lce3VKQ5x87ho7aLDyEw24bpCt25ekxMaS', '2025-06-18 05:06:02'),
(8, 'Кулагин Лев Дмитриевич', '+79241234949', 'gfbc@mail.com', 'lev12', '$2y$10$2Z0FXX5ryn./qWL0AI5OxuxMhyPG2pSJ/7A.D96pjdEM1rkBJNLJC', '2025-06-18 05:17:05');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Индексы таблицы `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `point_id` (`point_id`);

--
-- Индексы таблицы `pickup_points`
--
ALTER TABLE `pickup_points`
  ADD PRIMARY KEY (`point_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `pickup_points`
--
ALTER TABLE `pickup_points`
  MODIFY `point_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`point_id`) REFERENCES `pickup_points` (`point_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
