/* MySQL setup for Wagon. This commands specify the three MySQL tables for wagon to store all of its data.*/

CREATE TABLE `wp_wagon_userlist` (`privilege` enum('Owner','Editor','Viewer','') NOT NULL, `user` int(11) NOT NULL, `list` int(11) NOT NULL, `list_user_num` int(11) NOT NULL) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

ALTER TABLE `wp_wagon_userlist` ADD PRIMARY KEY (`list_user_num`);

CREATE TABLE IF NOT EXISTS `wp_wagon_list` (`list_name` varchar(35) NOT NULL, `privacy` enum('Public','Friends of Friends','Friends','Private') NOT NULL, `list_num` int(11) NOT NULL) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

ALTER TABLE `wp_wagon_list` ADD PRIMARY KEY (`list_num`), ADD KEY `list_num` (`list_num`);

CREATE TABLE IF NOT EXISTS `wp_wagon_items` (`item_num` int(11) NOT NULL, `list` int(11) NOT NULL, `item_name` varchar(35) DEFAULT NULL, `price` decimal(10,2) DEFAULT NULL, `date_saved` timestamp NULL DEFAULT CURRENT_TIMESTAMP, `item_url` varchar(2000) CHARACTER SET ascii COLLATE ascii_bin NOT NULL) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

ALTER TABLE `wp_wagon_items` ADD PRIMARY KEY (`item_num`);

ALTER TABLE `wp_wagon_items` MODIFY `item_num` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=32;
