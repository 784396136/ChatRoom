/*
Navicat MySQL Data Transfer

Source Server         : blog
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : workerman

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2019-02-19 09:50:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'cunye', '4297f44b13955235245b2497399d7a93');
INSERT INTO `user` VALUES ('2', 'yinrui', '4297f44b13955235245b2497399d7a93');
INSERT INTO `user` VALUES ('4', 'admin', '4297f44b13955235245b2497399d7a93');
