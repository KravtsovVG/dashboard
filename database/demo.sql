/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.6.17 : Database - dashboard
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`dashboard` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dashboard`;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`migration`,`batch`) values ('2016_01_04_101933_users',1),('2016_01_04_102430_projects',1),('2016_01_04_102701_project_users',1);

/*Table structure for table `project_user` */

DROP TABLE IF EXISTS `project_user`;

CREATE TABLE `project_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invitation` tinyint(4) NOT NULL DEFAULT '0',
  `is_owner` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `project_user` */

insert  into `project_user`(`id`,`project_id`,`user_id`,`invitation`,`is_owner`,`created_at`,`updated_at`) values (1,1,1,1,1,'2016-01-07 06:16:03','2016-01-07 07:01:35'),(2,1,4,1,0,'2016-01-07 06:16:04','2016-01-07 06:16:04'),(3,2,4,1,0,'2016-01-07 07:02:50','2016-01-07 07:02:50'),(4,2,3,1,0,'2016-01-07 07:02:52','2016-01-07 07:02:52'),(5,3,2,1,0,'2016-01-07 09:37:34','2016-01-07 09:38:21'),(6,3,3,1,0,'2016-01-07 09:37:37','2016-01-07 09:37:37'),(7,1,3,1,1,'2016-01-07 09:37:37','2016-01-07 13:01:33'),(9,1,5,1,0,'2016-01-07 13:14:53','2016-01-07 13:14:53');

/*Table structure for table `projects` */

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `projects` */

insert  into `projects`(`id`,`name`,`user_id`,`created_at`,`updated_at`) values (1,'Dashboard',2,'2016-01-07 06:16:02','2016-01-07 13:14:58'),(2,'Gaurang Patel Test-01',2,'2016-01-07 07:02:50','2016-01-07 07:02:50'),(3,'Gaurang Ghinaiya Test-01',1,'2016-01-07 09:37:33','2016-01-07 09:37:33');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `google` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`password`,`google`,`remember_token`,`created_at`,`updated_at`) values (1,'Gaurang Ghinaiya','gaurangghinaiya@yahoo.in','$2y$10$tmYIwCG5A1QwJ1/IX8dMNeqVjEwJjNeVBYDoJKq5tisqjem8wyQVa',NULL,'y9bqz4jdDQGBuu51rIWXGRtCKCkIp8dkOomz53VfOiZdWcBPpy4PiynKdEX9','2016-01-04 11:05:42','2016-01-07 09:38:03'),(2,'Gaurang Patel','gaurangghinaiya@gmail.com','$2y$10$C7Bdv22d74oFIOL2OdggDOYhlZOP/g2hcLbSSPZewJqzwfoWQQVjC','116204010032486392529','wfUt6dBwyFvkXKOoPM2nLKVYA4g0MGukqy0va75ExiW1DxpPtcnzlI7wKKJa','2016-01-04 13:14:37','2016-01-07 09:36:57'),(3,'Amit Patel','amit@gmail.com','$2y$10$fwbvdtpK0usUH0jmghLvmOamtjY2nz8SXtmSiw75iu1psFEBOo91q',NULL,'7DN2my4hXtuQZo83M0cJPVQBUh0S0rtzgcHPV5tjVejdquciYMD35SrAeTEN','2016-01-05 05:51:55','2016-01-05 05:51:59'),(4,'John','john@ymail.com','$2y$10$WpFM9AscdnSXElUm4.vamuHiPEt.NVaGTzdIIxr65d/5Jh9uU3./C',NULL,'oDWE3QvPrPM32cgr3i1xcyp3P9LZcoFNaZawzKskIa2ycHJLL1XTM14vyegr','2016-01-05 05:52:22','2016-01-07 07:07:48'),(5,'Ajay Baldha','ajay@gmail.com','$2y$10$sZ6FtED3zj5PtsVK8g4yBOsbaaajnAAiLlZWSm6Jx8grL.w5d6DWq',NULL,'XmkioRwaHIhP5hr5wMpXnFeD7jvNYbCptaEwxs1uVH3fba7SRqIMp1rGm7jN','2016-01-05 05:52:46','2016-01-07 07:09:44');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
