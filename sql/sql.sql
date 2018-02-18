-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.30-MariaDB-0ubuntu0.17.10.1 - Ubuntu 17.10
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for sgd
CREATE DATABASE IF NOT EXISTS `sgd` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `sgd`;

-- Dumping structure for table sgd.grups
CREATE TABLE IF NOT EXISTS `grups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Grup_Telegram` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Llista_Mail` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Data_Reunions` date DEFAULT NULL,
  `Lloc_Reunions` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tipus` int(11) DEFAULT '0' COMMENT '0 = Privat, 1 = Semipublic, 2 = Public',
  `Rep_suport_de` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.grups_persones
CREATE TABLE IF NOT EXISTS `grups_persones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idpersona` int(11) NOT NULL,
  `idgrup` int(11) NOT NULL,
  `rol` int(11) DEFAULT '1' COMMENT '0 = Pot veure, 1 = Usuari/a, 2 = Administrador(a) grup',
  PRIMARY KEY (`idpersona`,`idgrup`),
  KEY `grup_fk` (`idgrup`),
  KEY `gp_id` (`id`),
  CONSTRAINT `grup_fk` FOREIGN KEY (`idgrup`) REFERENCES `grups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `persona_fk` FOREIGN KEY (`idpersona`) REFERENCES `persones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.infoentitat
CREATE TABLE IF NOT EXISTS `infoentitat` (
  `id` int(11) NOT NULL DEFAULT '1',
  `Nom` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IBAN` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BIC` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PrvtId` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.login_failed
CREATE TABLE IF NOT EXISTS `login_failed` (
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.persones
CREATE TABLE IF NOT EXISTS `persones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomusuari` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipus` int(11) NOT NULL COMMENT '0 = Usuari no validat, 1 = admin, 2 = usuari validat, 3 = usuari validat completament, 4 = desactivada',
  `contrasenya` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nom_i_Cognoms` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NIF` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Districte` tinyint(4) NOT NULL,
  `Barri` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefon_Casa` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefon_Mobil` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Genere` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = Desconegut/NC, 1 = Dona, 2 = Home, 3 = Altre/Cap',
  `Data_Naixement` date NOT NULL,
  `Comentaris` text COLLATE utf8mb4_unicode_ci,
  `Tipus_Membre_Partit` int(11) NOT NULL COMMENT '0 = No, 1 = Simpatitzant, 2 = Membre Ple Dret',
  `Data_Alta_Simpatitzant` date NOT NULL,
  `Data_Baixa_Simpatitzant` date NOT NULL,
  `Data_Alta_Membre_Ple_Dret` date NOT NULL,
  `Data_Baixa_Membre_Ple_Dret` date NOT NULL,
  `IBAN` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Sense_Ingressos` tinyint(1) NOT NULL,
  `Paga_Transferencia` tinyint(1) NOT NULL,
  `Periodicitat_Quota` tinyint(4) NOT NULL DEFAULT '0' COMMENT '12 = Anual, 3 = Trimestral, 4 = Quatrimestral, 6 = Semestral',
  `BIC` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Alies_Telegram` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canviatself` tinyint(1) NOT NULL DEFAULT '0',
  `css` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Tipus interficie web, 0 = simple, 1 = moderne',
  `Xarxes` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Adresa` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Codi_Postal` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Ciutat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Provincia` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email2` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SGD_Origen` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `No_vol_emails` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = No vol emails, 0 = si vol',
  `lastlogin` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomusuari` (`nomusuari`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.quotes
CREATE TABLE IF NOT EXISTS `quotes` (
  `uid` int(11) NOT NULL,
  `quota` decimal(10,2) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`),
  CONSTRAINT `fk_uid` FOREIGN KEY (`uid`) REFERENCES `persones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.reunions
CREATE TABLE IF NOT EXISTS `reunions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL,
  `Nom` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Data` datetime DEFAULT NULL,
  `Lloc` text COLLATE utf8mb4_unicode_ci,
  `Ordre` text COLLATE utf8mb4_unicode_ci,
  `Acta` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `FK_reunio_gid` (`gid`),
  CONSTRAINT `FK_reunio_gid` FOREIGN KEY (`gid`) REFERENCES `grups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.reunions_persones
CREATE TABLE IF NOT EXISTS `reunions_persones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `tipus` int(11) DEFAULT '1' COMMENT '1 = Convocat, 2 = Assistira, 3 = Assistit, 4 = Excusa',
  PRIMARY KEY (`rid`,`uid`),
  KEY `id` (`id`),
  KEY `FK_rp_uid` (`uid`),
  CONSTRAINT `FK_rp_rid` FOREIGN KEY (`rid`) REFERENCES `reunions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_rp_uid` FOREIGN KEY (`uid`) REFERENCES `persones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.votacions
CREATE TABLE IF NOT EXISTS `votacions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL,
  `tipus` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = Tancada, 1 = Oberta',
  `tipusvot` tinyint(4) DEFAULT '0' COMMENT '0 = Selecció única, 1 = selecció múltiple, 2 = Cumulatiu',
  `startdate` date DEFAULT NULL,
  `closedate` date DEFAULT NULL,
  `autotanca` tinyint(1) DEFAULT '0' COMMENT '0 = No es tanca automaticament, 1 = es auto tanca a la data closedate',
  `resultatsquan` tinyint(1) DEFAULT '0' COMMENT '0 = Sempre visibles, 1 = al tancarse',
  `pregunta` text COLLATE utf8mb4_unicode_ci,
  `secreta` tinyint(1) DEFAULT '1' COMMENT '1 = Si, 0 = No',
  `quipotvotar` tinyint(1) DEFAULT '1' COMMENT '1 = Tothom, 0 = Nomes MPD',
  `cumulatiu_max_vots` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_gid` (`gid`),
  CONSTRAINT `fk_gid` FOREIGN KEY (`gid`) REFERENCES `grups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.votacions_respostes
CREATE TABLE IF NOT EXISTS `votacions_respostes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_local` int(11) DEFAULT NULL,
  `vid` int(11) DEFAULT NULL,
  `resposta` text COLLATE utf8mb4_unicode_ci,
  `vots` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_resp_vid` (`vid`),
  CONSTRAINT `fk_resp_vid` FOREIGN KEY (`vid`) REFERENCES `votacions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
-- Dumping structure for table sgd.vots
CREATE TABLE IF NOT EXISTS `vots` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `vid` int(11) DEFAULT NULL,
  `rid_local` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vots_uid` (`uid`),
  KEY `fk_vots_vid` (`vid`),
  CONSTRAINT `fk_vots_uid` FOREIGN KEY (`uid`) REFERENCES `persones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_vots_vid` FOREIGN KEY (`vid`) REFERENCES `votacions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
