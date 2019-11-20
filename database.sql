SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_array` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `_config` (`id`, `type`, `name`, `is_array`, `value`) VALUES
(6, 'site', 'template', 0, 'default'),
(7, 'site', 'url', 0, 'http://127.0.0.1/mygit/ci_master');

CREATE TABLE `_menu` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `cat_id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `url` text NOT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `group_ids` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `_menu_cat` (
  `id` int(11) UNSIGNED NOT NULL,
  `par_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `_user` (
  `id` int(11) UNSIGNED NOT NULL,
  `group_ids` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `_user` (`id`, `group_ids`, `username`, `password`, `name`, `email`, `phone`, `image`, `params`, `active`, `created`, `updated`) VALUES
(1, ',1,2,3,', 'admin', 'HLPcG893gkR9mE5Zl+8fhMg74dK2kYSg9+KD8vZqGWH393PeOTFJvhadmZ6nHrWTpvLPsMj0/ZOGPfBLzcaHUg==', 'Administrator', 'admin@gmail.com', '000000000000', '', '', 1, '2019-11-16 16:18:18', '2019-11-20 20:47:45');

CREATE TABLE `_user_group` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `_user_group` (`id`, `name`, `created`, `updated`) VALUES
(1, 'Administrator', '2019-11-20 20:37:01', '2019-11-20 20:37:01'),
(2, 'Member', '2019-11-20 20:37:37', '2019-11-20 20:37:37'),
(3, 'Registered', '2019-11-20 20:37:52', '2019-11-20 20:37:52');


ALTER TABLE `_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `_menu`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `_menu_cat`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

ALTER TABLE `_user_group`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `_menu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `_menu_cat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `_user_group`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
