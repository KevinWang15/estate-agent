/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50709
Source Host           : localhost:3306
Source Database       : estate_agent

Target Server Type    : MYSQL
Target Server Version : 50709
File Encoding         : 65001

Date: 2016-04-14 23:04:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for agents
-- ----------------------------
DROP TABLE IF EXISTS `agents`;
CREATE TABLE `agents` (
  `user_id` int(11) NOT NULL,
  `fee` decimal(20,2) DEFAULT NULL,
  `title` text,
  `description` text,
  PRIMARY KEY (`user_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `agent_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for agent_estate
-- ----------------------------
DROP TABLE IF EXISTS `agent_estate`;
CREATE TABLE `agent_estate` (
  `agent_id` int(11) NOT NULL,
  `estate_id` int(11) NOT NULL,
  PRIMARY KEY (`agent_id`,`estate_id`),
  KEY `estate_idx` (`estate_id`),
  CONSTRAINT `agent_estate_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `agent_estate_estate` FOREIGN KEY (`estate_id`) REFERENCES `estates` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for buyers
-- ----------------------------
DROP TABLE IF EXISTS `buyers`;
CREATE TABLE `buyers` (
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `buyer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for estates
-- ----------------------------
DROP TABLE IF EXISTS `estates`;
CREATE TABLE `estates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `city` text,
  `district` text,
  `zone` text,
  `neighborhood` text,
  `room` text,
  `condition` text,
  `description` text,
  `verified` tinyint(1) DEFAULT NULL,
  `verified_by_agent_id` int(11) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `is_for_rent` tinyint(1) DEFAULT NULL,
  `is_hidden` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `verified_by_agent_idx` (`verified_by_agent_id`),
  CONSTRAINT `estate_verified_by_agent` FOREIGN KEY (`verified_by_agent_id`) REFERENCES `agents` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=504 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` int(11) DEFAULT NULL,
  `proposal_id` int(11) DEFAULT NULL,
  `estate_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proposal_idx` (`proposal_id`),
  KEY `estate_idx` (`estate_id`),
  KEY `seller_idx` (`seller_id`),
  KEY `buyer_idx` (`buyer_id`),
  CONSTRAINT `order_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `order_estate` FOREIGN KEY (`estate_id`) REFERENCES `estates` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `order_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `proposals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `order_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for proposals
-- ----------------------------
DROP TABLE IF EXISTS `proposals`;
CREATE TABLE `proposals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estate_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estate_idx` (`estate_id`),
  KEY `order_idx` (`order_id`),
  KEY `agent_idx` (`agent_id`),
  KEY `proposal_buyer_idx` (`buyer_id`),
  CONSTRAINT `proposal_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `proposal_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `proposal_estate` FOREIGN KEY (`estate_id`) REFERENCES `estates` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `proposal_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=409 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sellers
-- ----------------------------
DROP TABLE IF EXISTS `sellers`;
CREATE TABLE `sellers` (
  `user_id` int(11) NOT NULL,
  `verified` tinyint(1) DEFAULT NULL,
  `verified_by_agent_id` int(11) DEFAULT NULL,
  `id_card_num` text,
  PRIMARY KEY (`user_id`),
  KEY `verified_by_agent_idx` (`verified_by_agent_id`),
  CONSTRAINT `seller_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `seller_verified_by_agent` FOREIGN KEY (`verified_by_agent_id`) REFERENCES `agents` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` decimal(11,0) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `mobile_UNIQUE` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8;
