# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 8.0.20)
# Database: kmh_new_model
# Generation Time: 2021-05-05 02:33:13 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table kmh_activity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_activity`;

CREATE TABLE `kmh_activity` (
  `ac_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `ac_type` varchar(255) DEFAULT NULL COMMENT '구분',
  `ac_detail` varchar(255) DEFAULT NULL COMMENT '구분 - 디테일',
  `ac_rel` varchar(255) DEFAULT NULL COMMENT '연관아이디',
  `ac_mb_id` int unsigned DEFAULT NULL COMMENT '회원아이디',
  `ac_msg` varchar(255) DEFAULT NULL COMMENT '메세지',
  `ac_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `ac_ip` varchar(45) DEFAULT NULL COMMENT '아이피',
  PRIMARY KEY (`ac_id`),
  KEY `ac_type` (`ac_type`),
  KEY `ac_rel` (`ac_rel`),
  KEY `ac_detail` (`ac_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='액티비티';



# Dump of table kmh_board
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_board`;

CREATE TABLE `kmh_board` (
  `board_id` varchar(45) NOT NULL DEFAULT '' COMMENT '아이디',
  `board_name` varchar(255) DEFAULT NULL COMMENT '이름',
  `board_auth_list` int DEFAULT NULL COMMENT '목록 권한',
  `board_auth_view` int DEFAULT NULL COMMENT '조회 권한',
  `board_auth_write` int DEFAULT NULL COMMENT '작성 권한',
  `board_auth_reply` int DEFAULT NULL COMMENT '답글 권한',
  `board_auth_comment` int DEFAULT NULL COMMENT '코멘트 권한',
  `board_auth_file` int DEFAULT NULL COMMENT '파일첨부 권한',
  `board_use_editor` tinyint(1) DEFAULT NULL COMMENT '에디터사용',
  `board_use_secret` tinyint(1) DEFAULT NULL COMMENT '비밀글사용 (2 : 필수)',
  `board_category` varchar(255) DEFAULT NULL COMMENT '카테고리',
  `board_admin` varchar(255) DEFAULT NULL COMMENT '관리자(여러명가능)',
  `board_per_page` int DEFAULT NULL COMMENT '페이지 글수',
  `board_skin` varchar(45) DEFAULT 'basic' COMMENT '스킨',
  `board_extra_info` varchar(255) DEFAULT NULL COMMENT '여분정보',
  `board_base_url` varchar(255) DEFAULT NULL COMMENT '대표 기본 url',
  PRIMARY KEY (`board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='게시판 설정';

LOCK TABLES `kmh_board` WRITE;
/*!40000 ALTER TABLE `kmh_board` DISABLE KEYS */;

INSERT INTO `kmh_board` (`board_id`, `board_name`, `board_auth_list`, `board_auth_view`, `board_auth_write`, `board_auth_reply`, `board_auth_comment`, `board_auth_file`, `board_use_editor`, `board_use_secret`, `board_category`, `board_admin`, `board_per_page`, `board_skin`, `board_extra_info`, `board_base_url`)
VALUES
	('news','뉴스',0,0,0,0,0,0,1,1,'basic=기본|test=시험','42',12,'gallery',NULL,NULL),
	('notice','공지사항',0,0,100,100,1,0,1,NULL,NULL,NULL,10,'basic',NULL,NULL);

/*!40000 ALTER TABLE `kmh_board` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_cart
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_cart`;

CREATE TABLE `kmh_cart` (
  `cart_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `cart_mb_id` int DEFAULT NULL COMMENT '회원아이디',
  `cart_session_id` varchar(255) DEFAULT NULL COMMENT '세션아이디',
  `cart_item` text COMMENT '장바구니 내용',
  `cart_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `cart_updated_at` timestamp NULL DEFAULT NULL COMMENT '업데이트일',
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_category`;

CREATE TABLE `kmh_category` (
  `cate_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '코드',
  `cate_depth` tinyint unsigned DEFAULT '0' COMMENT '뎁스',
  `cate_name` varchar(255) DEFAULT NULL COMMENT '카테고리명',
  `cate_fullname` varchar(255) DEFAULT NULL COMMENT '카테고리 조합명',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_chat
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_chat`;

CREATE TABLE `kmh_chat` (
  `chat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `chat_room_id` int unsigned NOT NULL,
  `chat_mb_id` int unsigned NOT NULL,
  `chat_word` varchar(255) NOT NULL DEFAULT '',
  `chat_read_count` int NOT NULL DEFAULT '1',
  `chat_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `chat_deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`chat_id`),
  KEY `chat_mb_id` (`chat_mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_chat_room
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_chat_room`;

CREATE TABLE `kmh_chat_room` (
  `room_id` int unsigned NOT NULL AUTO_INCREMENT,
  `room_last_word` varchar(255) DEFAULT NULL,
  `romm_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `room_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_chat_room_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_chat_room_member`;

CREATE TABLE `kmh_chat_room_member` (
  `rm_room_id` int unsigned NOT NULL,
  `rm_mb_id` int unsigned NOT NULL,
  `rm_name` varchar(255) DEFAULT NULL,
  `rm_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rm_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`rm_room_id`,`rm_mb_id`),
  KEY `mb_id` (`rm_mb_id`),
  CONSTRAINT `mb_id` FOREIGN KEY (`rm_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `room_id` FOREIGN KEY (`rm_room_id`) REFERENCES `kmh_chat_room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_comment`;

CREATE TABLE `kmh_comment` (
  `cm_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `cm_type` varchar(45) DEFAULT NULL COMMENT '구분',
  `cm_post_id` int unsigned DEFAULT NULL COMMENT '게시글 아이디',
  `cm_mb_id` int unsigned NOT NULL COMMENT '회원 아이디',
  `cm_writer` varchar(45) DEFAULT NULL COMMENT 'x 작성자(비회원)',
  `cm_password` varchar(255) DEFAULT NULL COMMENT 'x 비밀번호(비회원)',
  `cm_content` varchar(420) DEFAULT '' COMMENT '내용',
  `cm_is_secret` tinyint(1) DEFAULT NULL COMMENT 'x 비밀글',
  `cm_ip` varchar(45) DEFAULT NULL COMMENT '아이피',
  `cm_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `cm_updated_at` timestamp NULL DEFAULT NULL COMMENT '수정일',
  `cm_deleted_at` timestamp NULL DEFAULT NULL COMMENT '삭제일',
  `cm_family` int DEFAULT NULL COMMENT '패밀리',
  `cm_family_seq` int NOT NULL DEFAULT '0' COMMENT '패밀리 순',
  `cm_parent` int DEFAULT NULL COMMENT '연관',
  `cm_depth` int NOT NULL DEFAULT '0' COMMENT '댓글뎁스',
  PRIMARY KEY (`cm_id`),
  KEY `fk_kmh_comment_kmh_board1_idx` (`cm_post_id`),
  KEY `fk_kmh_comment_kmh_member1_idx` (`cm_mb_id`),
  KEY `type` (`cm_type`),
  KEY `post_id` (`cm_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='댓글';

LOCK TABLES `kmh_comment` WRITE;
/*!40000 ALTER TABLE `kmh_comment` DISABLE KEYS */;

INSERT INTO `kmh_comment` (`cm_id`, `cm_type`, `cm_post_id`, `cm_mb_id`, `cm_writer`, `cm_password`, `cm_content`, `cm_is_secret`, `cm_ip`, `cm_created_at`, `cm_updated_at`, `cm_deleted_at`, `cm_family`, `cm_family_seq`, `cm_parent`, `cm_depth`)
VALUES
	(1,NULL,167,2,NULL,NULL,'test11',NULL,'::1','2018-08-14 09:25:47','2018-08-14 13:31:05',NULL,1,0,NULL,0),
	(2,NULL,167,3,NULL,NULL,'원글 이지롱',NULL,'::1','2018-08-14 09:26:49','2018-08-14 12:22:56','2018-08-14 13:39:32',2,0,NULL,0),
	(3,NULL,167,3,NULL,NULL,'댓글',NULL,'::1','2018-08-14 09:26:52','2019-01-11 14:26:35','2018-08-14 13:39:29',2,25,2,1),
	(4,NULL,167,3,NULL,NULL,'댓글테스트',NULL,'::1','2018-08-14 10:35:29','2018-08-14 13:39:56',NULL,4,0,NULL,0),
	(5,NULL,167,3,NULL,NULL,'밥은 먹을수 있나요? 111',NULL,'::1','2018-08-14 10:47:44','2018-08-14 12:22:45','2018-08-14 13:39:47',5,0,NULL,0),
	(6,NULL,167,3,NULL,NULL,'123',NULL,'::1','2018-08-14 11:17:03','2018-08-14 11:17:03','2018-08-14 13:39:45',6,0,0,0),
	(7,NULL,167,3,NULL,NULL,'asdf',NULL,'::1','2018-08-14 11:17:55','2018-08-14 11:17:55','2018-08-14 13:39:44',7,0,0,0),
	(8,NULL,167,3,NULL,NULL,'ㅁㄴㅇㄹ123',NULL,'::1','2018-08-14 11:21:30','2018-08-14 12:22:40','2018-08-14 13:39:43',8,0,0,0),
	(12,NULL,167,3,NULL,NULL,'ㄴㅇㄹㅎ',NULL,'::1','2018-08-14 12:25:32','2018-08-14 12:25:32','2018-08-14 13:39:42',12,0,0,0),
	(13,NULL,167,3,NULL,NULL,'댓글2',NULL,'::1','2018-08-14 13:10:43','2019-01-11 14:26:35','2018-08-14 13:17:21',2,22,2,1),
	(14,NULL,167,3,NULL,NULL,'새글',NULL,'::1','2018-08-14 13:10:52','2018-08-14 13:10:52','2018-08-14 13:39:42',14,0,0,0),
	(15,NULL,167,3,NULL,NULL,'댓글의 댓글',NULL,'::1','2018-08-14 13:11:28','2019-01-11 14:26:35','2018-08-14 13:18:43',2,28,3,2),
	(16,NULL,167,3,NULL,NULL,'댓글의 댓글2',NULL,'::1','2018-08-14 13:11:37','2019-01-11 14:26:35','2018-08-14 13:18:45',2,27,3,2),
	(17,NULL,167,3,NULL,NULL,'댓글2 댓글',NULL,'::1','2018-08-14 13:11:46','2019-01-11 14:26:35','2018-08-14 13:18:15',2,23,13,2),
	(18,NULL,167,3,NULL,NULL,'대댓글',NULL,'::1','2018-08-14 13:18:34','2019-01-11 14:26:35','2018-08-14 13:18:41',2,29,15,3),
	(19,NULL,167,3,NULL,NULL,'관리자에게 댓글 12',NULL,'::1','2018-08-14 13:27:56','2019-01-11 14:26:35','2018-08-14 13:39:21',1,21,1,1),
	(20,NULL,167,3,NULL,NULL,'하하호호',NULL,'::1','2018-08-14 13:28:03','2018-08-14 13:28:03','2018-08-14 13:39:41',20,0,0,0),
	(21,NULL,167,3,NULL,NULL,'댓글',NULL,'::1','2018-08-14 13:34:27','2019-01-11 14:26:35','2018-08-14 13:39:27',2,26,3,2),
	(22,NULL,167,3,NULL,NULL,'sadf',NULL,'::1','2018-08-14 13:36:42','2018-08-14 13:36:42','2018-08-14 13:39:40',22,0,0,0),
	(23,NULL,167,3,NULL,NULL,'zzz',NULL,'::1','2018-08-14 13:36:55','2018-08-14 13:36:55','2018-08-14 13:39:39',23,0,0,0),
	(24,NULL,167,3,NULL,NULL,'asdf',NULL,'::1','2018-08-14 13:37:13','2018-08-14 13:37:13','2018-08-14 13:39:38',24,0,0,0),
	(25,NULL,167,3,NULL,NULL,'asdf',NULL,'::1','2018-08-14 13:37:30','2018-08-14 13:37:30','2018-08-14 13:39:37',25,0,0,0),
	(26,NULL,167,3,NULL,NULL,'asdf',NULL,'::1','2018-08-14 13:37:50','2018-08-14 13:37:50','2018-08-14 13:39:36',26,0,0,0),
	(27,NULL,167,3,NULL,NULL,'ㅁㄴㅇㄹ',NULL,'::1','2018-08-14 13:39:14','2018-08-14 13:39:14','2018-08-14 13:39:34',27,0,0,0),
	(28,NULL,167,3,NULL,NULL,'답글 작성',NULL,'::1','2018-08-14 13:40:02','2019-01-11 14:26:35',NULL,1,20,1,1),
	(29,NULL,167,3,NULL,NULL,'내글에 답글',NULL,'::1','2018-08-14 13:40:14','2019-01-11 14:26:35',NULL,4,18,4,1),
	(30,NULL,167,3,NULL,NULL,'답답~',NULL,'::1','2018-08-14 13:40:20','2019-01-11 14:26:35',NULL,4,19,29,2),
	(31,NULL,167,3,NULL,NULL,'ㄸ',NULL,'::1','2018-08-14 13:54:53','2018-08-14 13:54:53','2018-08-14 13:54:56',31,0,0,0),
	(32,NULL,167,3,NULL,NULL,'test',NULL,'::1','2018-08-16 11:32:39','2018-08-16 11:32:39',NULL,32,0,0,0),
	(33,NULL,167,3,NULL,NULL,'sdfgsdfg',NULL,'::1','2018-08-16 11:33:51','2018-08-16 11:33:51',NULL,33,0,0,0),
	(34,NULL,167,3,NULL,NULL,'sdfgsdfgsg',NULL,'::1','2018-08-16 11:33:54','2018-08-16 11:33:54',NULL,34,0,0,0),
	(35,NULL,167,3,NULL,NULL,'sdfgsdfg',NULL,'::1','2018-08-16 11:33:57','2018-08-16 11:33:57',NULL,35,0,0,0),
	(36,NULL,167,3,NULL,NULL,'xcvbcxvbxbxcvb',NULL,'::1','2018-08-16 11:34:01','2018-08-16 11:34:01',NULL,36,0,0,0),
	(37,NULL,9,2,NULL,NULL,'',NULL,'::1','2018-08-29 17:47:56','2018-08-29 17:47:56','2018-08-29 17:48:05',37,0,0,0),
	(38,NULL,9,2,NULL,NULL,'ghfgh',NULL,'::1','2018-08-29 17:47:59','2018-08-29 17:47:59','2018-08-29 17:48:09',38,0,0,0),
	(39,NULL,9,2,NULL,NULL,'123123',NULL,'::1','2018-08-29 17:51:28','2018-08-29 17:51:28',NULL,39,0,0,0),
	(40,NULL,18,2,NULL,NULL,'ㅁㄴㅇㄹㅁㄴㅇㄹ',NULL,'::1','2018-09-06 14:48:52','2018-09-06 14:48:52',NULL,40,0,0,0),
	(41,NULL,18,2,NULL,NULL,'fgh',NULL,'::1','2018-09-06 16:05:35','2018-09-06 16:05:35',NULL,41,0,0,0),
	(42,NULL,9,2,NULL,NULL,'두번째 댓글',NULL,'1.217.88.66','2018-11-12 17:50:49','2018-11-12 17:50:49','2018-11-12 17:51:48',42,0,0,0),
	(43,NULL,9,2,NULL,NULL,'관리자 모드에서',NULL,'1.217.88.66','2018-11-28 15:33:37','2018-11-28 15:33:37',NULL,43,0,0,0),
	(44,NULL,7,2,NULL,NULL,'관리자모드에서 등록하는 댓글 - 수정 수정2',NULL,'1.217.88.66','2018-11-28 16:08:43','2019-01-10 16:11:30',NULL,44,0,0,0),
	(53,NULL,7,2,NULL,NULL,'ㅅㄷㄴㅅ',NULL,'1.217.88.66','2018-12-05 16:53:16','2019-01-11 14:26:35',NULL,44,9,44,1),
	(54,NULL,7,2,NULL,NULL,'ㅅㄷㄴㅅ',NULL,'1.217.88.66','2018-12-05 16:53:20','2019-01-11 14:26:35',NULL,44,13,53,2),
	(59,NULL,7,2,NULL,NULL,'asdf',NULL,'1.217.88.66','2018-12-12 13:24:33','2018-12-12 13:24:33','2018-12-12 13:24:36',59,0,0,0),
	(66,NULL,7,2,NULL,NULL,'asdfasdf',NULL,'1.217.88.66','2018-12-12 13:33:20','2018-12-12 13:33:20','2018-12-12 13:34:36',66,0,0,0),
	(68,NULL,7,2,NULL,NULL,'adsf',NULL,'1.217.88.66','2018-12-12 13:34:57','2018-12-12 13:34:57','2018-12-12 13:35:00',68,0,0,0),
	(69,NULL,7,2,NULL,NULL,'test',NULL,'1.217.88.66','2018-12-12 13:35:18','2019-01-11 14:26:35','2018-12-12 13:35:22',68,8,68,1),
	(70,NULL,9,2,NULL,NULL,'123',NULL,'1.217.88.66','2018-12-12 14:25:18','2018-12-12 14:25:18',NULL,70,0,0,0),
	(73,NULL,169,42,NULL,NULL,'ssdgsgd',NULL,'1.217.88.66','2019-01-09 19:56:40','2019-01-09 19:56:40',NULL,73,0,0,0),
	(74,NULL,7,2,NULL,NULL,'답변입니다.',NULL,'1.217.88.66','2019-01-10 16:11:04','2019-01-11 14:26:35',NULL,44,10,53,2),
	(75,NULL,7,42,NULL,NULL,'댓글입력입니다.',NULL,'1.217.88.66','2019-01-10 16:15:22','2019-01-10 16:15:22',NULL,75,0,0,0),
	(76,NULL,7,42,NULL,NULL,'test',NULL,'1.217.88.66','2019-01-10 16:25:13','2019-01-11 14:26:35',NULL,75,6,75,1),
	(77,NULL,7,42,NULL,NULL,'답변의 답변',NULL,'1.217.88.66','2019-01-10 16:31:17','2019-01-11 14:26:35',NULL,44,11,74,3),
	(78,NULL,7,42,NULL,NULL,'3',NULL,'1.217.88.66','2019-01-10 16:31:35','2019-01-11 14:26:35',NULL,44,12,77,4),
	(79,NULL,7,42,NULL,NULL,'ㅅㄷㅅㄷㅅㄷㅅㄷㅅ',NULL,'1.217.88.66','2019-01-10 16:32:17','2019-01-11 14:26:35',NULL,75,5,75,1),
	(80,NULL,7,42,NULL,NULL,'새댓',NULL,'1.217.88.66','2019-01-10 16:32:22','2019-01-10 16:32:22','2019-01-10 16:32:27',80,0,0,0),
	(81,NULL,7,42,NULL,NULL,'답변',NULL,'1.217.88.66','2019-01-10 18:19:58','2019-01-11 14:26:35',NULL,44,14,54,3),
	(82,NULL,7,41,NULL,NULL,'이메일 가입자',NULL,'1.217.88.66','2019-01-10 18:30:56','2019-01-10 18:30:56',NULL,82,0,0,0),
	(83,NULL,9,42,NULL,NULL,'ㅎㅎ',NULL,'110.70.54.235','2019-01-10 19:00:30','2019-01-11 14:26:35',NULL,37,4,37,1),
	(84,NULL,7,2,NULL,NULL,'대댓글',NULL,'110.70.54.235','2019-01-11 12:37:42','2019-01-11 14:26:35',NULL,82,3,82,1),
	(85,NULL,169,2,NULL,NULL,'머래',NULL,'1.217.88.66','2019-01-11 14:11:38','2019-01-11 14:26:35',NULL,73,2,73,1),
	(86,NULL,169,2,NULL,NULL,'새댓글입니다 관리자 글도 수정 - ㅎㅎ',NULL,'1.217.88.66','2019-01-11 14:11:53','2019-01-11 15:22:04','2019-01-11 15:23:01',86,0,0,0),
	(87,NULL,177,2,NULL,NULL,'답변이 있어도 수정하면 그만?',NULL,'1.217.88.66','2019-01-11 14:14:01','2019-01-11 14:26:50',NULL,87,0,0,0),
	(88,NULL,177,2,NULL,NULL,'댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? 댓글은 몇자까지 입력이 가능한가요? ',NULL,'1.217.88.66','2019-01-11 14:14:51','2019-01-11 14:15:06','2019-01-11 14:17:37',88,0,0,0),
	(89,NULL,177,2,NULL,NULL,'답변 수정불가!',NULL,'1.217.88.66','2019-01-11 14:26:35','2019-01-11 14:29:14',NULL,87,1,87,1);

/*!40000 ALTER TABLE `kmh_comment` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_config`;

CREATE TABLE `kmh_config` (
  `config_id` varchar(255) NOT NULL DEFAULT '' COMMENT '항목',
  `config_desc` varchar(255) DEFAULT NULL COMMENT '설명',
  `config_value` varchar(255) DEFAULT NULL COMMENT '값',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_device
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_device`;

CREATE TABLE `kmh_device` (
  `dv_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'FCM 토큰',
  `dv_mb_id` int unsigned NOT NULL COMMENT '회원',
  `dv_os` varchar(45) DEFAULT NULL COMMENT 'OS',
  `dv_uuid` varchar(255) DEFAULT NULL COMMENT 'UUID',
  `dv_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '최초등록',
  `dv_push` tinyint unsigned DEFAULT '1' COMMENT '푸시허용 여부',
  PRIMARY KEY (`dv_id`),
  KEY `fk_kmh_device_kmh_member1_idx` (`dv_mb_id`),
  KEY `push` (`dv_push`),
  KEY `dv_uuid` (`dv_uuid`),
  CONSTRAINT `dv_mb_id` FOREIGN KEY (`dv_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='디바이스 정보\\\\n1년 이상 기록은 삭제';



# Dump of table kmh_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_file`;

CREATE TABLE `kmh_file` (
  `file_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `file_folder` varchar(255) DEFAULT NULL COMMENT '폴더',
  `file_name` varchar(255) DEFAULT NULL COMMENT '파일명',
  `file_save` varchar(255) DEFAULT NULL COMMENT '저장파일명',
  `file_type` varchar(45) DEFAULT NULL COMMENT '타입',
  `file_size` int DEFAULT NULL COMMENT '사이즈',
  `file_is_image` tinyint DEFAULT NULL COMMENT '이미지여부',
  `file_hit` int DEFAULT '0' COMMENT '다운로드수',
  `file_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `file_rel_type` varchar(255) DEFAULT NULL COMMENT '연관 타입',
  `file_rel_id` varchar(255) DEFAULT NULL COMMENT '연관 대상',
  `file_rel_desc` varchar(255) DEFAULT NULL COMMENT '연관 설명',
  PRIMARY KEY (`file_id`),
  KEY `rel_type` (`file_rel_type`),
  KEY `rel_id` (`file_rel_id`),
  KEY `red_desc` (`file_rel_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='파일첨부';

LOCK TABLES `kmh_file` WRITE;
/*!40000 ALTER TABLE `kmh_file` DISABLE KEYS */;

INSERT INTO `kmh_file` (`file_id`, `file_folder`, `file_name`, `file_save`, `file_type`, `file_size`, `file_is_image`, `file_hit`, `file_created_at`, `file_rel_type`, `file_rel_id`, `file_rel_desc`)
VALUES
	(47,'news/201808/','Magnific-Popup-master.zip','14d12d56981b1d24fb5597456ba7ccef.zip','application/zip',346502,0,0,'2018-08-14 19:16:43','board','168','attach'),
	(48,'news/201808/','Magnific-Popup-master.zip','7b973f10a6b56b85dea6a3b25b7b34f7.zip','application/zip',346502,0,0,'2018-08-16 09:23:42','board','170','attach'),
	(50,'news/201808/','cb_1001_2018-08-09.sql','8f6b41303b659e6fa4be5c6dac1713b6.sql','text/plain',1240542,0,0,'2018-08-16 11:02:28','board','172','attach'),
	(51,'news/201808/','rv_reservation_log_view.csv','5408574c44bc8de15636a0945b5b950f.csv','text/plain',92539,0,0,'2018-08-16 11:02:28','board','172','attach'),
	(52,'news/201808/','codeigniter-base-model_MY_Model.php at master · jamierumbelow_codeigniter-base-model.pdf','e75a1513248eb8017214407d28d955c6.pdf','application/pdf',212185,0,0,'2018-08-16 11:02:28','board','172','attach'),
	(53,'news/201808/','a.php','9e3e37ef8eb738f1187317b69df4ce5e.php','text/x-php',2379,0,0,'2018-08-16 11:02:28','board','172','attach'),
	(54,'news/201808/','bootstrap-pettaxi-theme.css','9764b1cd1deba7b858dc9d49dff21c3a.css','text/plain',116854,0,0,'2018-08-16 11:02:28','board','172','attach'),
	(55,'news/201808/','select2-bootstrap-theme-master.zip','aa8fac0ebf354f7a40d1ca519b6e8377.zip','application/zip',309137,0,0,'2018-08-16 11:08:03','board','172','attach'),
	(56,'news/201808/','기존포털-최근 예약건있는 선주.xls','2d4f646e2393de338d8139811ef5df14.xls','application/vnd.ms-office',15872,0,0,'2018-08-16 11:08:03','board','172','attach'),
	(57,'news/201808/','KakaoTalk_Photo_2018-08-14-13-43-08.gif','75a9dd6073725c4d89d32d47564e1454.gif','image/gif',61701,1,1,'2018-08-16 11:22:45','board','172','attach'),
	(58,'news/201808/','정리할것.todo','5a0b3cc465729970035ed4a76f8e626e.todo','text/plain',1354,0,0,'2018-08-16 11:22:45','board','172','attach'),
	(61,'editor/201808/','abc.png','500ec7095f4097f8b48ae6dcec3e514a.png','image/png',574285,1,0,'2018-08-22 11:38:08','board','175','editor'),
	(62,'news/201808/','0402-바다사랑메인-확정.png','25c7538a42392bc131c7851161778bc1.png','image/png',2745910,1,2,'2018-08-22 11:38:50','board','175','attach'),
	(63,'news/201808/','abc.png','26510924fa4bae434495dcaca17545d0.png','image/png',574285,1,0,'2018-08-22 11:41:53','board','175','attach'),
	(64,'news/201808/','20180821-조황정보-추가.png','44359dd83f2b8eda76eabef29a7cb10d.png','image/png',1747831,1,0,'2018-08-22 11:42:31','board','175','attach'),
	(68,'notice/201811/','breakpoints-js-master.zip','5fb52170a7838bec54f11ed1a5ff715f.zip','application/zip',163377,0,2,'2018-11-12 18:17:14','board','7','attach'),
	(69,'editor/201811/','slide1.jpg','b830fb72205ccca6146e888d5a77b611.jpg','image/jpeg',232384,1,0,'2018-11-12 18:18:49','board','7','editor'),
	(70,'notice/201811/','바다사랑아이콘.zip','8c720e09d11feeea19fcc3b2649d8c97.zip','application/zip',11640,0,0,'2018-11-28 16:08:21','board','7','attach'),
	(81,'notice/201901/','KakaoTalk_Photo_2019-01-07-16-06-08.png','179d8fb316c1ed3030b78b5616ddc0d0.png','image/png',16863,1,0,'2019-01-11 10:59:39','board','177','attach'),
	(84,'editor/201901/','DaumMap_20190103_161452.png','df7aa6ab07a5a30af37193f7ef8b5eb7.png','image/png',625509,1,0,'2019-01-11 15:38:53','board','180','editor');

/*!40000 ALTER TABLE `kmh_file` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_level`;

CREATE TABLE `kmh_level` (
  `level_id` tinyint unsigned NOT NULL COMMENT '아이디',
  `level_name` varchar(45) DEFAULT NULL COMMENT '이름',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='레벨구분 (필요시구현)';

LOCK TABLES `kmh_level` WRITE;
/*!40000 ALTER TABLE `kmh_level` DISABLE KEYS */;

INSERT INTO `kmh_level` (`level_id`, `level_name`)
VALUES
	(0,'비회원'),
	(1,'회원'),
	(100,'최고관리자');

/*!40000 ALTER TABLE `kmh_level` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_like
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_like`;

CREATE TABLE `kmh_like` (
  `like_id` int unsigned NOT NULL AUTO_INCREMENT,
  `like_rel` varchar(45) NOT NULL,
  `like_rel_id` int unsigned NOT NULL,
  `like_type` enum('like','dislike') NOT NULL,
  `like_mb_id` int unsigned NOT NULL,
  PRIMARY KEY (`like_id`),
  KEY `fk_kmh_like_kmh_member1_idx` (`like_mb_id`),
  CONSTRAINT `fk_kmh_like_kmh_member1` FOREIGN KEY (`like_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_log`;

CREATE TABLE `kmh_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `controller` varchar(45) DEFAULT NULL COMMENT '컨트롤러',
  `method` varchar(45) DEFAULT NULL COMMENT '메소드',
  `file` varchar(255) DEFAULT NULL COMMENT '파일',
  `line` varchar(45) DEFAULT NULL COMMENT '라인',
  `msg` varchar(2000) DEFAULT NULL COMMENT '메세지',
  `title` varchar(255) DEFAULT NULL COMMENT '타이틀',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='개발로그';



# Dump of table kmh_log_bot
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_log_bot`;

CREATE TABLE `kmh_log_bot` (
  `bot_id` int unsigned NOT NULL AUTO_INCREMENT,
  `bot_info` varchar(255) DEFAULT NULL,
  `bot_name` varchar(45) DEFAULT NULL,
  `bot_url` varchar(255) DEFAULT NULL,
  `bot_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bot_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`bot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_member`;

CREATE TABLE `kmh_member` (
  `mb_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `mb_tid` varchar(255) DEFAULT NULL COMMENT '계정아이디',
  `mb_email` varchar(45) DEFAULT NULL COMMENT '이메일',
  `mb_password` varchar(255) DEFAULT NULL COMMENT '비밀번호',
  `mb_display` varchar(255) DEFAULT NULL COMMENT '표시명',
  `mb_level` tinyint DEFAULT '1' COMMENT '권한',
  `mb_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `mb_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
  `mb_deleted_at` timestamp NULL DEFAULT NULL COMMENT '삭제일',
  `mb_name` varchar(45) DEFAULT NULL COMMENT '이름',
  `mb_contact` varchar(20) DEFAULT NULL COMMENT '연락처',
  `mb_mobile` varchar(20) DEFAULT NULL COMMENT '휴대폰',
  `mb_post` varchar(10) DEFAULT NULL COMMENT '우편번호',
  `mb_addr1` varchar(255) DEFAULT NULL COMMENT '주소',
  `mb_addr2` varchar(255) DEFAULT NULL COMMENT '상세주소',
  `mb_use_spam` tinyint DEFAULT NULL COMMENT '광고수신여부',
  `mb_point` int DEFAULT '0' COMMENT '잔여포인트',
  `mb_sex` enum('male','female') DEFAULT NULL COMMENT '성별',
  `mb_status` enum('ask','ok','fail','out') DEFAULT 'ok' COMMENT '상태',
  `mb_holdind_end_at` timestamp NULL DEFAULT NULL COMMENT '기능제한 종료일',
  `mb_social_type` varchar(45) DEFAULT 'web' COMMENT '소셜로그인 구분',
  `mb_social_id` varchar(255) DEFAULT NULL COMMENT '소셜로그인 ID',
  `mb_social_image` varchar(255) DEFAULT NULL COMMENT '소셜로그인 이미지',
  PRIMARY KEY (`mb_id`),
  UNIQUE KEY `v_id` (`mb_tid`),
  UNIQUE KEY `mb_email` (`mb_email`),
  UNIQUE KEY `mb_display_UNIQUE` (`mb_display`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원';

LOCK TABLES `kmh_member` WRITE;
/*!40000 ALTER TABLE `kmh_member` DISABLE KEYS */;

INSERT INTO `kmh_member` (`mb_id`, `mb_tid`, `mb_email`, `mb_password`, `mb_display`, `mb_level`, `mb_created_at`, `mb_updated_at`, `mb_deleted_at`, `mb_name`, `mb_contact`, `mb_mobile`, `mb_post`, `mb_addr1`, `mb_addr2`, `mb_use_spam`, `mb_point`, `mb_sex`, `mb_status`, `mb_holdind_end_at`, `mb_social_type`, `mb_social_id`, `mb_social_image`)
VALUES
	(1,'admin','admin@admin.co.kr','cc2ec993f855b10607accb617b8cae54c227230749f8b3663b2a4e91235828f972eed8fa8869fc328adab84f32f64a355905fd724584648aae9f92d7e98053e1Ot1uK42a4wt42QdNspRGpZWYrI9FVq6qsga9dPLmyw4=','최고관리자',100,'2018-06-08 15:31:58','2021-05-05 11:30:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'ok',NULL,'web',NULL,NULL);

/*!40000 ALTER TABLE `kmh_member` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_nation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_nation`;

CREATE TABLE `kmh_nation` (
  `nation_id` varchar(40) NOT NULL,
  `nation_title` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`nation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_option`;

CREATE TABLE `kmh_option` (
  `ot_pd_id` int unsigned NOT NULL COMMENT '제품아이디',
  `ot_type` varchar(45) NOT NULL DEFAULT '' COMMENT '옵션종류',
  `ot_name` varchar(45) NOT NULL DEFAULT '' COMMENT '옵션명',
  `ot_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `ot_price` int NOT NULL DEFAULT '0' COMMENT '가격',
  `ot_use` tinyint(1) DEFAULT NULL COMMENT '사용여부',
  PRIMARY KEY (`ot_pd_id`,`ot_type`,`ot_name`),
  KEY `fk_kmh_option_kmh_product1_idx` (`ot_pd_id`),
  CONSTRAINT `fk_kmh_option_kmh_product1` FOREIGN KEY (`ot_pd_id`) REFERENCES `kmh_product` (`pd_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_order`;

CREATE TABLE `kmh_order` (
  `order_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '주문아이디',
  `order_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `order_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
  `order_deleted_at` timestamp NULL DEFAULT NULL COMMENT '삭제일',
  `order_mb_id` int unsigned DEFAULT NULL COMMENT '회원아이디',
  `order_name` varchar(45) DEFAULT NULL COMMENT '주문자 이름',
  `order_tel` varchar(45) DEFAULT NULL COMMENT '주문자 연락처',
  `order_post` varchar(45) DEFAULT NULL COMMENT '우편번호',
  `order_addr1` varchar(255) DEFAULT NULL COMMENT '기본주소',
  `order_addr2` varchar(255) DEFAULT NULL COMMENT '상세주소',
  `order_status` enum('100_ask','200_waitpaid','300_prepare','500_complete','900_cancel') DEFAULT '100_ask' COMMENT '주문서 상태',
  `order_memo` varchar(1000) DEFAULT NULL COMMENT '고객메모',
  `order_deli_price` int DEFAULT '0' COMMENT '배송비',
  `order_pd_price` int DEFAULT '0' COMMENT '제품가',
  `order_point_use` int DEFAULT '0' COMMENT '포인트 사용량',
  `order_admin_price` int DEFAULT '0' COMMENT '관리자 조정가',
  `order_total_price` int DEFAULT '0' COMMENT '최종금액',
  `order_admin_memo` varchar(1000) DEFAULT NULL COMMENT '관리자전용 메모',
  `order_paid_at` timestamp NULL DEFAULT NULL COMMENT '결제일시',
  `order_detail` varchar(255) DEFAULT NULL COMMENT '주문서 간략설명',
  PRIMARY KEY (`order_id`),
  KEY `fk_kmh_order_kmh_member1_idx` (`order_mb_id`),
  CONSTRAINT `fk_kmh_order_kmh_member1` FOREIGN KEY (`order_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_order_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_order_log`;

CREATE TABLE `kmh_order_log` (
  `ol_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `ol_order_id` int unsigned NOT NULL COMMENT '주문서번호',
  `ol_order_status` varchar(100) NOT NULL DEFAULT '' COMMENT '주문서상태',
  `ol_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `ol_mb_id` int unsigned DEFAULT NULL COMMENT '회원아이디',
  PRIMARY KEY (`ol_id`),
  KEY `fk_kmh_order_log_kmh_order1_idx` (`ol_order_id`),
  KEY `fk_kmh_order_log_kmh_member1_idx` (`ol_mb_id`),
  CONSTRAINT `fk_kmh_order_log_kmh_member1` FOREIGN KEY (`ol_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_kmh_order_log_kmh_order1` FOREIGN KEY (`ol_order_id`) REFERENCES `kmh_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_order_product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_order_product`;

CREATE TABLE `kmh_order_product` (
  `op_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `op_order_id` int unsigned NOT NULL COMMENT '주문서번호',
  `op_pd_id` int unsigned NOT NULL COMMENT '제품아이디',
  `op_count` smallint unsigned DEFAULT '1' COMMENT '수량',
  `op_price_one` int unsigned DEFAULT '0' COMMENT '제품 개당가격',
  `op_price` int unsigned DEFAULT '0' COMMENT '합계가격',
  `op_options` text COMMENT '옵션들',
  `op_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  PRIMARY KEY (`op_id`),
  KEY `fk_kmh_order_product_kmh_order1_idx` (`op_order_id`),
  KEY `fk_kmh_order_product_kmh_product1_idx` (`op_pd_id`),
  CONSTRAINT `fk_kmh_order_product_kmh_order1` FOREIGN KEY (`op_order_id`) REFERENCES `kmh_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_kmh_order_product_kmh_product1` FOREIGN KEY (`op_pd_id`) REFERENCES `kmh_product` (`pd_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table kmh_point
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_point`;

CREATE TABLE `kmh_point` (
  `pt_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `pt_mb_id` int unsigned DEFAULT NULL COMMENT '회원아이디',
  `pt_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  `pt_amount` int DEFAULT NULL COMMENT '포인트',
  `pt_left_point` int DEFAULT NULL COMMENT '잔여포인트',
  `pt_desc` varchar(1000) DEFAULT NULL COMMENT '비고',
  `pt_rel_id` varchar(255) DEFAULT NULL COMMENT '포인트 확인',
  PRIMARY KEY (`pt_id`),
  KEY `fk_kmh_point_kmh_member1_idx` (`pt_mb_id`),
  KEY `pt_rel_id` (`pt_rel_id`),
  CONSTRAINT `fk_kmh_point_kmh_member1` FOREIGN KEY (`pt_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='포인트';



# Dump of table kmh_popup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_popup`;

CREATE TABLE `kmh_popup` (
  `pu_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `pu_type` enum('일반','레이어') NOT NULL DEFAULT '레이어' COMMENT '팝업타입',
  `pu_title` varchar(45) DEFAULT NULL COMMENT '제목',
  `pu_desc` text COMMENT '내용',
  `pu_start` date DEFAULT NULL COMMENT '시작일',
  `pu_end` date DEFAULT NULL COMMENT '종료일',
  `pu_x` int DEFAULT NULL COMMENT '가로위치',
  `pu_y` int DEFAULT NULL COMMENT '세로위치',
  `pu_width` int DEFAULT NULL COMMENT '너비',
  `pu_height` int DEFAULT NULL COMMENT '높이',
  `pu_align` enum('left','center','right') NOT NULL DEFAULT 'center' COMMENT '정렬',
  `pu_background` varchar(45) DEFAULT NULL COMMENT '배경',
  `pu_link` varchar(255) DEFAULT NULL COMMENT '링크',
  `pu_file` varchar(255) DEFAULT NULL COMMENT '첨부파일',
  PRIMARY KEY (`pu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='팝업';



# Dump of table kmh_post
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_post`;

CREATE TABLE `kmh_post` (
  `post_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `post_board_id` varchar(45) NOT NULL DEFAULT '' COMMENT '설정 아이디',
  `post_mb_id` int unsigned DEFAULT NULL COMMENT '회원 아이디',
  `post_category` varchar(45) DEFAULT NULL COMMENT '카테고리',
  `post_writer` varchar(100) DEFAULT NULL COMMENT '작성자(비회원)',
  `post_password` varchar(255) DEFAULT NULL COMMENT '비밀번호(비회원)',
  `post_family` int DEFAULT NULL COMMENT '패미리',
  `post_family_seq` int DEFAULT '0' COMMENT '패미리순서',
  `post_parent` int DEFAULT NULL COMMENT '연결부모글 (답답글에 필요)',
  `post_depth` smallint DEFAULT '0' COMMENT '답글뎁스',
  `post_title` varchar(255) DEFAULT NULL COMMENT '제목',
  `post_content` longtext COMMENT '내용',
  `post_hit` int NOT NULL DEFAULT '0' COMMENT '조회수',
  `post_is_secret` tinyint DEFAULT NULL COMMENT '비밀글',
  `post_is_notice` tinyint DEFAULT NULL COMMENT '공지글',
  `post_use_editor` tinyint(1) DEFAULT '0' COMMENT '에디터 사용여부',
  `post_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `post_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '수정일',
  `post_deleted_at` timestamp NULL DEFAULT NULL COMMENT '삭제일',
  `post_ip` varchar(45) DEFAULT NULL COMMENT '아이피',
  `post_ex1` varchar(255) DEFAULT NULL COMMENT '여분1',
  `post_ex2` varchar(255) DEFAULT NULL COMMENT '여분2',
  `post_ex3` varchar(255) DEFAULT NULL COMMENT '여분3',
  `post_ex4` varchar(255) DEFAULT NULL COMMENT '여분4',
  `post_ex5` varchar(255) DEFAULT NULL COMMENT '여분5',
  `post_ex6` varchar(255) DEFAULT NULL COMMENT '여분6',
  `post_ex7` varchar(255) DEFAULT NULL COMMENT '여분7',
  `post_ex8` varchar(255) DEFAULT NULL COMMENT '여분8',
  `post_ex9` varchar(255) DEFAULT NULL COMMENT '여분9',
  `post_ex10` varchar(255) DEFAULT NULL COMMENT '여분10',
  `post_ex_date1` timestamp NULL DEFAULT NULL COMMENT '날짜1',
  `post_ex_date2` timestamp NULL DEFAULT NULL COMMENT '날짜2',
  PRIMARY KEY (`post_id`),
  KEY `fk_kmh_board_kmh_board_config_idx` (`post_board_id`),
  KEY `fk_kmh_board_kmh_member1_idx` (`post_mb_id`),
  CONSTRAINT `fk_kmh_board_kmh_member1` FOREIGN KEY (`post_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='게시글';

LOCK TABLES `kmh_post` WRITE;
/*!40000 ALTER TABLE `kmh_post` DISABLE KEYS */;

INSERT INTO `kmh_post` (`post_id`, `post_board_id`, `post_mb_id`, `post_category`, `post_writer`, `post_password`, `post_family`, `post_family_seq`, `post_parent`, `post_depth`, `post_title`, `post_content`, `post_hit`, `post_is_secret`, `post_is_notice`, `post_use_editor`, `post_created_at`, `post_updated_at`, `post_deleted_at`, `post_ip`, `post_ex1`, `post_ex2`, `post_ex3`, `post_ex4`, `post_ex5`, `post_ex6`, `post_ex7`, `post_ex8`, `post_ex9`, `post_ex10`, `post_ex_date1`, `post_ex_date2`)
VALUES
	(5,'notice_',1,NULL,NULL,NULL,46,0,NULL,0,'title','con',0,NULL,NULL,0,NULL,'2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(7,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'게시글 제목입니다. 폰트를 알아볼까요?','<p><img src=\"/uploads/editor/201811/b830fb72205ccca6146e888d5a77b611.jpg\"><br></p>',28,0,1,1,'2018-07-03 10:30:23','2019-01-11 09:04:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(8,'notice',NULL,NULL,'손님용','1234',46,0,NULL,1,'Francisca Torphy','Esse voluptas in earum ut sunt officiis. Velit alias delectus quod qui nihil rerum. Laudantium totam commodi sit.',5,1,NULL,0,'2018-07-02 10:30:23','2018-08-03 17:40:29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(9,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM Prof. Giuseppe McGlynn DVM','Delectus magni eum aut. Dolorem velit ut consectetur sit. Corporis incidunt quia neque officiis quasi. Eius asperiores ducimus dolore corrupti.',24,NULL,NULL,0,'2006-03-19 11:41:04','2019-01-11 09:00:58',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(10,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Mrs. Tessie Weimann PhD','Quam omnis nobis fugit ut doloribus veritatis vero. Recusandae ut aut modi ducimus ad. Sit cumque quod voluptatum consequatur nesciunt.',10,NULL,NULL,0,'2011-07-16 05:53:14','2019-01-11 12:30:15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(11,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Remington Hoeger','Soluta accusamus in praesentium ut. Saepe quo id aliquam error tenetur quo. Nam qui illo veniam rerum veritatis. Corporis optio quos recusandae labore exercitationem voluptate.',5,NULL,NULL,0,'1994-05-22 04:09:55','2019-01-09 13:59:21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(12,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Viviane Lakin','Ut sed aliquid ut in quod architecto. Hic cupiditate voluptatum quaerat dignissimos aut ipsum. Nulla odit reiciendis culpa eveniet. Voluptatem culpa laborum dignissimos.',5,NULL,NULL,0,'1976-05-03 16:09:05','2019-01-09 17:43:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(13,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Judah Collins','Aut reiciendis voluptatem non tempore aut ut ad corrupti. Omnis occaecati quas adipisci sunt nulla. Pariatur mollitia harum perspiciatis dolor consequatur possimus provident asperiores.',3,NULL,NULL,0,'1985-11-18 12:50:33','2018-11-28 15:09:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(14,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Nash Lebsack','Sint doloremque aut dolorum aut amet officia vitae. Vitae minus autem omnis officia quis voluptatem. Voluptates sint enim et totam. Voluptatem enim quia mollitia qui reiciendis non ea.',2,NULL,NULL,0,'2007-10-14 10:17:40','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(15,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Dr. Donny Weber','Similique doloremque aliquam atque animi atque. Dicta adipisci consequatur excepturi rem eum est. Ut minus eligendi dolorum pariatur. Et velit quo et nesciunt est aliquid itaque.',2,NULL,NULL,0,'1992-08-27 00:28:31','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(16,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Daphne Fadel','Impedit molestiae qui similique nesciunt consequatur. Quo est laboriosam quis sunt ducimus eaque odio. Dolores aspernatur hic voluptatum molestiae.',6,NULL,NULL,0,'2003-02-03 16:18:37','2018-12-10 13:09:08',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(17,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Kyle Watsica','Ex impedit ipsam odio molestiae nemo. Reprehenderit aperiam est ipsa. Dolor quia alias aut inventore qui. Expedita est eos qui blanditiis earum.',2,NULL,NULL,0,'1995-06-07 18:43:34','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(18,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Patricia Batz','Et tempora sint quia nulla fugit. Cumque magnam sed est amet quasi est consequuntur. Suscipit dolores omnis eveniet laudantium. Quia voluptatem et aut.',6,NULL,NULL,0,'2011-06-11 20:40:59','2019-01-08 17:27:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(19,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Florida Spencer DVM','Autem tempore voluptatibus qui aperiam quam esse cumque. Exercitationem voluptatem iusto voluptatem maiores vel. Non sed beatae itaque omnis minus et voluptatem.',1,NULL,NULL,0,'1982-05-13 07:26:45','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(20,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Brennon Medhurst MD','Soluta similique sunt nisi quo. Rerum dolores accusantium consequatur natus.',1,NULL,NULL,0,'1973-09-20 14:18:13','2018-12-10 13:09:17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(21,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Crawford Torphy','Voluptas exercitationem commodi quam unde aspernatur quis. Corrupti eaque qui excepturi. At necessitatibus iste praesentium sunt ut aut. Vero et dolorem maxime officiis molestiae veniam mollitia.',1,NULL,NULL,0,'1986-09-04 02:11:13','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(22,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Raoul Ernser','Ut sequi cupiditate dolore odio laborum et. Et consequuntur velit aut quia iure recusandae vel. Debitis dignissimos dicta quidem in praesentium. Recusandae sed molestias corrupti.',1,NULL,NULL,0,'1992-09-29 17:45:03','2018-11-07 16:58:11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(23,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Dr. Chaim Murray','Mollitia dolore ea delectus maiores cum voluptates. Harum ratione dolorem ex maiores magnam. Eum consequuntur quis esse iste illum et. In voluptas corrupti vero odit nihil sit.',2,NULL,NULL,0,'1994-02-27 02:17:55','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(24,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Tyrese Lubowitz','Qui minus nulla ut. In eum aliquid eos eligendi. Ut consequatur in itaque ullam molestiae nostrum.',1,NULL,NULL,0,'2000-07-17 23:29:59','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(25,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Micah Turner','Dolor unde in qui exercitationem quisquam sint fugiat. Eveniet eveniet aut repellendus a ad. Magni et repellendus hic a aut ab molestiae.',0,NULL,NULL,0,'1972-08-30 10:04:52','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(26,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Mr. Jimmy Fay I','Tenetur et omnis repellat aut blanditiis. Rerum id est facere autem iure. Laborum aut voluptate minima repellendus nihil perferendis.',1,NULL,NULL,0,'2004-06-25 12:29:09','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(27,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Prof. Kenya Rau','Nesciunt delectus dolores ullam dolorem. Qui deserunt ipsum labore. Enim corporis aut minima ea molestias consequatur ut.',1,NULL,NULL,0,'1995-04-24 05:17:38','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(28,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Ismael Gorczany','Et rem a aliquid. Accusamus ut ut voluptatem est veniam.',2,NULL,NULL,0,'1971-02-20 00:50:50','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(29,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Mia Bernhard IV','Et sit quisquam molestiae itaque perferendis. Velit rem est facilis. Aut omnis ab facere ut ipsam adipisci voluptatum et.',0,NULL,NULL,0,'1987-12-19 14:19:00','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(30,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Ms. Tomasa Powlowski','Incidunt est et tenetur aut officiis sed aut. Est voluptas eos unde nisi molestiae corporis. Totam sit sit et aspernatur eligendi.',0,NULL,NULL,0,'1987-07-26 11:17:22','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(31,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Herminio Kulas DDS','Ipsa voluptatibus excepturi consequatur accusamus. Ut asperiores aliquid quidem eos. Voluptatem quod et quidem exercitationem.',2,NULL,NULL,0,'1972-08-05 00:03:43','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(32,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Mr. Rogers Denesik I','Similique aperiam blanditiis asperiores veritatis eius soluta. Cumque iusto labore aliquid quis dolorem neque ut. Ipsam reprehenderit vitae neque voluptatem. Quod est voluptatibus omnis.',0,NULL,NULL,0,'1993-02-04 22:33:00','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(33,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Dr. Tracey Fisher DVM','Totam et aut placeat id. Et eligendi eos quibusdam molestiae quibusdam natus delectus. Impedit delectus est qui nisi.',0,NULL,NULL,0,'2000-05-17 02:47:01','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(34,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Garfield Konopelski DDS','Rem voluptas vero est soluta ipsum. Harum adipisci non rerum explicabo rerum quis aut. Numquam sint aliquid est ipsam id saepe. Dolor voluptatem voluptas minus dolore nesciunt id.',2,NULL,NULL,0,'1998-09-22 09:42:48','2018-11-12 18:14:57',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(35,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Mrs. Ona Farrell','Qui vitae pariatur tempora quasi culpa blanditiis. Expedita velit recusandae et est sed. Quis quod voluptatibus voluptatem quaerat.',2,NULL,NULL,0,'1997-05-01 07:09:59','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(36,'notice',1,NULL,NULL,NULL,46,0,NULL,0,'Nikita McDermott','Quidem porro perferendis reiciendis blanditiis assumenda non natus. Corporis doloribus fugiat molestias a natus veniam possimus sunt.',0,NULL,NULL,0,'2005-01-27 20:08:55','2018-08-01 18:07:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(114,'',NULL,NULL,'ks1995@gmail.com','1234',NULL,0,NULL,0,'8/6 테스트 답글의 답글','',0,0,0,1,'2018-08-08 14:56:14','2018-08-08 14:56:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(115,'',NULL,NULL,'ks1995@gmail.com','1234',NULL,0,NULL,0,'8/6 테스트 답글의 답글','',0,0,0,1,'2018-08-08 15:02:44','2018-08-08 15:02:44',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(116,'',NULL,NULL,'ks1995@gmail.com','1234',NULL,0,NULL,0,'8/6 테스트 답글의 답글','',0,0,0,1,'2018-08-08 15:04:01','2018-08-08 15:04:01',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(125,'news',NULL,'basic','ks1995@gmail.com','1234',125,0,0,0,'1','',2,0,0,1,'2018-08-08 15:45:32','2019-01-11 13:54:35',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(126,'news',NULL,NULL,'ks1995@gmail.com','1234',126,0,0,0,'2','',2,0,0,1,'2018-08-08 15:45:48','2019-01-11 13:59:57',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(127,'news',NULL,NULL,'ks1995@gmail.com','1234',127,0,0,0,'3','',3,0,0,1,'2018-08-08 15:45:58','2019-01-11 13:57:43',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(128,'news',NULL,NULL,'ks1995@gmail.com','1234',126,10,126,1,'21','',1,0,0,1,'2018-08-08 15:46:12','2018-08-09 18:55:32',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(129,'news',NULL,NULL,'ks1995@gmail.com','1234',126,7,126,1,'22','',2,0,0,1,'2018-08-08 15:46:27','2019-01-11 14:00:47',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(130,'news',NULL,NULL,'ks1995@gmail.com','1234',126,11,128,2,'211','',2,0,0,1,'2018-08-08 15:46:40','2018-08-22 12:46:30',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(131,'news',NULL,NULL,'ks1995@gmail.com','1234',127,10,127,1,'31','',2,0,0,1,'2018-08-08 15:55:57','2019-01-11 13:59:48',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(132,'news',NULL,NULL,'ks1995@gmail.com','1234',127,7,127,1,'32','',2,0,0,1,'2018-08-08 15:56:20','2019-01-11 14:00:59',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(133,'news',NULL,NULL,'ks1995@gmail.com','1234',127,6,127,1,'33','',3,0,0,1,'2018-08-08 15:57:23','2019-01-11 13:59:42',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(134,'news',NULL,NULL,'ks1995@gmail.com','test',127,9,132,2,'321','',1,0,0,1,'2018-08-08 15:57:49','2018-08-09 18:55:32',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(135,'news',NULL,NULL,'ks1995@gmail.com','1234',127,8,132,2,'322','',1,0,0,1,'2018-08-08 15:58:00','2018-08-09 18:55:32',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(136,'news',NULL,NULL,'ks1995@gmail.com','1234',136,0,0,0,'4','<p>test</p>',2,0,0,1,'2018-08-08 16:00:40','2018-08-14 13:54:45',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(137,'news',NULL,NULL,'ks1995@gmail.com','1234',136,5,136,1,'41','',1,0,0,1,'2018-08-08 16:02:11','2018-08-09 18:55:32',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(138,'news',NULL,NULL,'ks1995@gmail.com','1234',138,0,0,0,'5','<p><img src=\"/uploads/editor/201808/c029c2dd803ad17478995452e970f804.png\"><br></p>',4,0,0,1,'2018-08-08 16:03:25','2018-08-22 11:13:24',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(148,'',NULL,NULL,'test','1234',NULL,0,NULL,0,'파일첨부','<p><img src=\"/uploads/editor/201808/32756e1036b7a072e36ed00e23f76c61.png\"><br></p>',0,0,0,1,'2018-08-08 18:03:40','2018-08-08 18:03:40',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(167,'news',NULL,'basic',NULL,NULL,167,0,0,0,'새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 새글 시험 ','<p xss=\"removed\">dfgsdfgsdfg</p><p xss=\"removed\"></p><p xss=\"removed\"><br></p><p xss=\"removed\"><br></p><table class=\"table table-bordered\"><tbody><tr><td><br></td><td xss=\"removed\">가운데 정렬</td><td><br></td><td><br></td></tr><tr><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p xss=\"removed\"><br></p>',9,0,0,1,'2018-08-13 12:41:37','2019-01-11 13:54:25',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(168,'news',NULL,NULL,NULL,NULL,168,0,0,0,'test','<p>test</p>',3,0,0,1,'2018-08-14 19:16:43','2018-10-18 17:41:00',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(169,'news',NULL,NULL,NULL,NULL,169,0,0,0,'Test Test Test Test Test Test Test Test Test Test Test Test Test Test Test Test Test Test ','<p>test</p>',11,0,1,1,'2018-08-14 19:18:40','2019-01-11 15:19:33',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(170,'news',NULL,'basic',NULL,NULL,170,0,0,0,'test','',2,1,0,1,'2018-08-16 09:23:42','2018-08-17 11:54:11',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(171,'news',NULL,'test',NULL,NULL,171,0,0,0,'test','<p><img src=\"/uploads/editor/201808/05d1700a7e1f55ccaf49958b8275e811.gif\"><br></p>',3,0,0,1,'2018-08-16 09:56:30','2018-08-22 11:14:40',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(172,'news',NULL,'test','ks1995@gmail.com','ks1995',172,0,0,0,'비회원','<p>123123</p>',4,0,0,1,'2018-08-16 10:05:48','2018-12-05 14:15:16',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(173,'news',NULL,NULL,'admin@admin.co.kr','123123',173,0,0,0,'tetst','<div class=\"row\">\r\n  <div class=\"col-md-6\">\r\n      <img src=\"/uploads/editor/201808/7991778a283bfa71e66b1968aca6059a.png\" xss=\"removed\">\r\n  </div>\r\n  <div class=\"col-md-6\">\r\n      <div class=\"list-group\">\r\n          <a href=\"#\" class=\"list-group-item active\">\r\n            Cras justo odio\r\n          </a>\r\n          <a href=\"#\" class=\"list-group-item\">Dapibus ac facilisis in</a>\r\n          <a href=\"#\" class=\"list-group-item\">Morbi leo risus</a>\r\n          <a href=\"#\" class=\"list-group-item\">Porta ac consectetur ac</a>\r\n          <a href=\"#\" class=\"list-group-item\">Vestibulum at eros</a></div>\r\n      <div class=\"list-group\">\r\n      </div>\r\n  </div>\r\n</div>\r\n<p></p>\r\n<table class=\"table table-bordered\"><tbody><tr><th>th</th><th>th</th><th>th</th><th>th</th><th>th</th></tr><tr><td>th</td><td><br></td><td><br></td><td><br></td><td><br></td></tr><tr><td>th</td><td><br></td><td><br></td><td><br></td><td><br></td></tr></tbody></table><p><br></p>',1,0,0,1,'2018-08-21 16:40:26','2018-08-22 10:09:45',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(174,'news',1,NULL,NULL,NULL,174,0,0,0,'test captcha','<p><br></p>',5,0,0,1,'2018-08-22 10:34:00','2018-11-28 10:12:53',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(175,'news',1,NULL,NULL,NULL,175,0,0,0,'test','<p>test</p><p><img src=\"/uploads/editor/201808/500ec7095f4097f8b48ae6dcec3e514a.png\"></p>',11,0,0,1,'2018-08-22 11:38:17','2019-01-10 13:31:27',NULL,'::1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(176,'notice',1,NULL,NULL,NULL,176,0,0,0,'test','<p>test</p>',5,0,0,1,'2018-11-28 11:57:44','2019-01-09 19:44:21',NULL,'1.217.88.66',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(177,'notice',1,NULL,NULL,NULL,177,0,0,0,'게시글 새로올림','<p>이 책을 저희를 GNU/Linux, 그리고 오픈 소스의 세계로 이끌어주신 Kalyan Varma46, 그리고\r\nPESIT47의 다른 많은 분들께 바칩니다.\r\n</p><p>또한 좋은 친구이자 스승이 되어주셨던, <br>그리운 고 Atul Chitnis48를 기억하며 이 책을 바칩니다.</p><p>\r\n마지막으로 지금의 인터넷을 탄생시킨 주역들49에게 이 책을 바칩니다. <br>이 책은 2003년도에 처\r\n음으로 작성되었습니다만, 여전히 많이 읽히고 있습니다. </p><p>이것은 바로 이들이 개척해 왔던 지식\r\n의 공유 정신 덕분입니다.</p>',6,0,1,1,'2019-01-11 10:59:39','2019-01-12 10:33:17',NULL,'1.217.88.66',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(179,'notice',1,NULL,NULL,NULL,179,0,0,0,'모바일에서 작성','',1,0,0,1,'2019-01-11 14:20:42','2019-01-11 14:20:42',NULL,'110.70.54.235',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(180,'notice',1,NULL,NULL,NULL,180,0,0,0,'입력글','<p><img src=\"/uploads/editor/201901/df7aa6ab07a5a30af37193f7ef8b5eb7.png\" xss=removed><br></p>',1,0,0,1,'2019-01-11 15:39:22','2019-01-11 15:39:22',NULL,'1.217.88.66',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `kmh_post` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_product`;

CREATE TABLE `kmh_product` (
  `pd_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `pd_name` varchar(255) DEFAULT NULL COMMENT '제품명',
  `pd_cate_id` int unsigned DEFAULT NULL COMMENT '카테고리',
  `pd_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
  `pd_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
  `pd_deleted_at` timestamp NULL DEFAULT NULL COMMENT '삭제일',
  `pd_use` tinyint(1) DEFAULT '1' COMMENT '사용여부',
  `pd_price` int unsigned DEFAULT '0' COMMENT '가격',
  `pd_min` smallint DEFAULT '1' COMMENT '최소주문수량',
  `pd_detail` text COMMENT '설명',
  `pd_order` int unsigned NOT NULL DEFAULT '0' COMMENT '주문된 수량',
  PRIMARY KEY (`pd_id`),
  KEY `fk_kmh_product_kmh_category1_idx` (`pd_cate_id`),
  KEY `pd_use` (`pd_use`),
  CONSTRAINT `fk_kmh_product_kmh_category1` FOREIGN KEY (`pd_cate_id`) REFERENCES `kmh_category` (`cate_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `kmh_product` WRITE;
/*!40000 ALTER TABLE `kmh_product` DISABLE KEYS */;

INSERT INTO `kmh_product` (`pd_id`, `pd_name`, `pd_cate_id`, `pd_created_at`, `pd_updated_at`, `pd_deleted_at`, `pd_use`, `pd_price`, `pd_min`, `pd_detail`, `pd_order`)
VALUES
	(3,'test제품',NULL,'2019-09-18 17:38:05','2019-09-18 17:39:03',NULL,1,1000,1,NULL,0);

/*!40000 ALTER TABLE `kmh_product` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table kmh_rating
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_rating`;

CREATE TABLE `kmh_rating` (
  `rt_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '아이디',
  `rt_mb_id` int unsigned DEFAULT NULL COMMENT '평가자(필요시)',
  `rt_type` varchar(45) DEFAULT NULL COMMENT '종류',
  `rt_rel` int DEFAULT NULL COMMENT '대상아이디',
  `rt_rating` decimal(3,1) DEFAULT NULL COMMENT '별점',
  `rt_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
  PRIMARY KEY (`rt_id`),
  KEY `fk_kmh_rating_kmh_member1_idx` (`rt_mb_id`),
  CONSTRAINT `fk_kmh_rating_kmh_member1` FOREIGN KEY (`rt_mb_id`) REFERENCES `kmh_member` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='평점';



# Dump of table kmh_test
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kmh_test`;

CREATE TABLE `kmh_test` (
  `test_id` int unsigned NOT NULL AUTO_INCREMENT,
  `test_id_copy` int DEFAULT NULL,
  `test_varchar` varchar(255) DEFAULT NULL,
  `test_varchar2` varchar(255) DEFAULT NULL,
  `test_created_at` timestamp NULL DEFAULT NULL,
  `test_updated_at` timestamp NULL DEFAULT NULL,
  `test_deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
