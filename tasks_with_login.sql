-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 01 2018 г., 22:10
-- Версия сервера: 5.6.37
-- Версия PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `tasks_with_login`
--

-- --------------------------------------------------------

--
-- Структура таблицы `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `is_done` tinyint(4) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `task`
--

INSERT INTO `task` (`id`, `user_id`, `assigned_user_id`, `description`, `is_done`, `date_added`) VALUES
(18, 17, 20, 'task 1 by 11', 0, '2017-09-24 17:30:22'),
(19, 17, 19, 'task 2 by 11', 1, '2017-09-24 17:30:28'),
(21, 18, 19, 'task 1 by 2', 0, '2017-09-24 17:30:53'),
(22, 18, 18, 'task 2 by 2', 0, '2017-09-24 17:31:01'),
(23, 18, 19, 'task 3 by 2', 1, '2017-09-24 17:31:05'),
(24, 19, 18, 'task 1 by 3', 0, '2017-09-24 17:31:25'),
(25, 19, 19, 'task 2 by 3', 0, '2017-09-24 17:31:29'),
(26, 19, 18, 'task 3 by 3', 0, '2017-09-24 17:31:33'),
(31, 0, 0, 'dddddddddddddd', 0, '2017-10-02 16:25:19'),
(32, 0, 0, 'fffffffffffffff', 0, '2017-10-02 16:25:25'),
(33, 0, 0, 'f', 0, '2017-10-02 16:25:29'),
(34, 0, 0, 'ff', 0, '2017-10-02 16:25:39'),
(35, 0, 0, 'd', 0, '2017-10-02 16:26:14'),
(37, 0, 0, 'd', 0, '2017-10-02 16:29:42'),
(38, 18, 18, 'task 4 by 2', 0, '2017-10-03 15:38:12');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `login`, `password`) VALUES
(17, 'user1', '$2y$10$ba.du0BCJDxZRZofrqZRsOKWhbmYNCnHt21YM09dhkK/ZGp9kP.V.'),
(18, 'user2', '$2y$10$VfsPIS.paky5/o9YgQCwvudwb5yZeOF3YFX8fU07jhiIS/0cwHqJG'),
(19, 'user3', '$2y$10$R0dy3gc4SgtWCtHBVl0We.6uMsOn99ZMhehWt0cPAHfYAlpg.o1YS'),
(20, 'user4', '$2y$10$Oc7F8udhkj9R6OYx6r7j4Ope0P/aHWns.XD3tkzPG87uU7HhUhR7y');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
