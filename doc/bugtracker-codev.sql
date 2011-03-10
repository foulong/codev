-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeu 10 Mars 2011 à 09:57
-- Version du serveur: 5.1.41
-- Version de PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `bugtracker`
--

-- --------------------------------------------------------

--
-- Structure de la table `codev_config_table`
--

CREATE TABLE IF NOT EXISTS `codev_config_table` (
  `config_id` varchar(15) NOT NULL,
  `value` longtext NOT NULL,
  `type` int(10) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `codev_config_table`
--


-- --------------------------------------------------------

--
-- Structure de la table `codev_holidays_table`
--

CREATE TABLE IF NOT EXISTS `codev_holidays_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` int(10) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#D8D8D8',
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Fixed Holidays (national, religious, etc.)' AUTO_INCREMENT=36 ;

--
-- Contenu de la table `codev_holidays_table`
--

INSERT INTO `codev_holidays_table` (`id`, `date`, `description`, `color`) VALUES
(6, 1303682400, 'lundi de paques', '#58CC77'),
(7, 1304200800, 'fete du travail', '#D8D8D8'),
(8, 1306965600, 'ascension', '#58CC77'),
(9, 1310594400, 'fete nationale', '#58CC77'),
(10, 1320102000, 'toussaints', '#58CC77'),
(11, 1320966000, 'armistice', '#58CC77'),
(12, 1324767600, 'noel', '#D8D8D8'),
(26, 1336428000, 'victoire 1945', '#58CC77'),
(14, 1293836400, 'Reveillon', '#D8D8D8'),
(25, 1335823200, 'fete du travail', '#58CC77'),
(24, 1333922400, 'lundi de paques', '#58CC77'),
(20, 1279058400, '	fete nationale', '#58CC77'),
(21, 1288566000, 'toussaints', '#58CC77'),
(22, 1289430000, 'armistice', '#58CC77'),
(23, 1293231600, 'noel', '#D8D8D8'),
(27, 1304805600, 'victoire 1945', '#D8D8D8'),
(28, 1337205600, 'ascension', '#58CC77'),
(29, 1307916000, 'pentecote', '#58CC77'),
(30, 1342216800, 'fete nationale', '#D8D8D8'),
(31, 1344981600, 'assomption', '#58CC77'),
(32, 1313359200, 'assomption', '#58CC77'),
(33, 1351724400, 'toussaint', '#58CC77'),
(34, 1352588400, 'armistice', '#D8D8D8'),
(35, 1356390000, 'noel', '#58CC77');

-- --------------------------------------------------------

--
-- Structure de la table `codev_job_table`
--

CREATE TABLE IF NOT EXISTS `codev_job_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` int(10) NOT NULL DEFAULT '0',
  `color` varchar(7) CHARACTER SET utf8 DEFAULT '#000000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Contenu de la table `codev_job_table`
--

INSERT INTO `codev_job_table` (`id`, `name`, `type`, `color`) VALUES
(1, 'Etude d impact', 0, '#FFCD85'),
(2, 'Analyse de l''existant', 0, '#FFF494'),
(3, 'Developpement', 0, '#C2DFFF'),
(4, 'Tests et Corrections', 0, '#92C5FC'),
(10, 'N/A', 1, '#A8FFBD'),
(18, 'Documentation', 0, '#E0F57A');

-- --------------------------------------------------------

--
-- Structure de la table `codev_project_job_table`
--

CREATE TABLE IF NOT EXISTS `codev_project_job_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  `job_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `codev_project_job_table`
--

INSERT INTO `codev_project_job_table` (`id`, `project_id`, `job_id`) VALUES
(1, 11, 10),
(6, 23, 10),
(7, 24, 10);

-- --------------------------------------------------------

--
-- Structure de la table `codev_sidetasks_category_table`
--

CREATE TABLE IF NOT EXISTS `codev_sidetasks_category_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  `cat_management` int(10) NOT NULL,
  `cat_incident` int(10) DEFAULT NULL,
  `cat_absence` int(10) DEFAULT NULL,
  `cat_tools` int(11) DEFAULT NULL,
  `cat_doc` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`),
  KEY `project_id_2` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `codev_sidetasks_category_table`
--

INSERT INTO `codev_sidetasks_category_table` (`id`, `project_id`, `cat_management`, `cat_incident`, `cat_absence`, `cat_tools`, `cat_doc`) VALUES
(1, 11, 15, 19, 17, 18, 16),
(2, 23, 35, 33, NULL, 31, 34),
(3, 24, 40, 38, NULL, 39, 36);

-- --------------------------------------------------------

--
-- Structure de la table `codev_team_project_table`
--

CREATE TABLE IF NOT EXISTS `codev_team_project_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  `team_id` int(10) NOT NULL,
  `type` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150 ;

--
-- Contenu de la table `codev_team_project_table`
--

INSERT INTO `codev_team_project_table` (`id`, `project_id`, `team_id`, `type`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 0),
(4, 4, 1, 0),
(5, 5, 1, 0),
(6, 6, 1, 0),
(7, 12, 1, 0),
(8, 15, 1, 0),
(9, 11, 1, 1),
(102, 6, 6, 0),
(101, 19, 6, 0),
(14, 16, 1, 0),
(103, 20, 6, 0),
(25, 11, 6, 1),
(24, 17, 6, 0),
(104, 5, 6, 0),
(51, 11, 21, 1),
(143, 20, 35, 0),
(34, 11, 3, 1),
(142, 3, 35, 0),
(141, 21, 35, 0),
(140, 6, 35, 0),
(139, 4, 35, 0),
(138, 15, 35, 0),
(137, 12, 35, 0),
(136, 2, 35, 0),
(135, 1, 35, 0),
(134, 19, 35, 0),
(78, 2, 6, 0),
(79, 1, 6, 0),
(80, 16, 6, 0),
(81, 11, 26, 1),
(82, 3, 6, 0),
(86, 2, 26, 0),
(85, 1, 26, 0),
(87, 12, 26, 0),
(88, 15, 26, 0),
(89, 4, 26, 0),
(90, 6, 26, 0),
(91, 3, 26, 0),
(92, 5, 26, 0),
(93, 16, 26, 0),
(94, 17, 26, 0),
(106, 21, 1, 0),
(105, 21, 26, 0),
(98, 20, 1, 0),
(99, 20, 26, 0),
(100, 19, 26, 0),
(149, 24, 6, 1),
(147, 17, 35, 0),
(145, 16, 35, 0),
(144, 5, 35, 0),
(123, 11, 34, 1),
(124, 23, 34, 1),
(125, 23, 26, 1),
(126, 19, 1, 0),
(128, 11, 35, 1),
(129, 24, 35, 1),
(132, 24, 1, 1),
(133, 24, 26, 1);

-- --------------------------------------------------------

--
-- Structure de la table `codev_team_table`
--

CREATE TABLE IF NOT EXISTS `codev_team_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `leader_id` int(10) DEFAULT NULL,
  `date` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Contenu de la table `codev_team_table`
--

INSERT INTO `codev_team_table` (`id`, `name`, `description`, `leader_id`, `date`) VALUES
(1, 'atos', 'Equipe Atos Aix', 2, 1275256800),
(3, 'admin', 'Administrateurs CoDev', 7, 1275256800),
(21, 'FDJ', 'FDJ', 12, 1275256800),
(6, 'atos_tests', 'Equipe Atos compementaire', 21, 1275256800),
(26, 'CoDev_Atos', 'Equipe CoDev ATOS', 22, 1275256800),
(34, 'Multimedia', 'Web team', 10, 1298934000),
(35, 'PROSYS', 'Travaux sur GEMS et OLPM', 22, 1275256800);

-- --------------------------------------------------------

--
-- Structure de la table `codev_team_user_table`
--

CREATE TABLE IF NOT EXISTS `codev_team_user_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `team_id` int(10) NOT NULL,
  `access_level` int(10) unsigned NOT NULL DEFAULT '10',
  `arrival_date` int(10) unsigned NOT NULL,
  `departure_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=146 ;

--
-- Contenu de la table `codev_team_user_table`
--

INSERT INTO `codev_team_user_table` (`id`, `user_id`, `team_id`, `access_level`, `arrival_date`, `departure_date`) VALUES
(1, 2, 1, 10, 1275256800, 0),
(2, 5, 1, 10, 1275256800, 0),
(3, 6, 1, 10, 1275256800, 1286488800),
(4, 7, 1, 10, 1275256800, 0),
(34, 8, 1, 10, 1275256800, 1285279200),
(6, 9, 1, 10, 1275256800, 0),
(12, 7, 3, 10, 1275256800, 0),
(8, 11, 1, 10, 1275256800, 0),
(41, 3, 21, 10, 1275256800, 0),
(40, 4, 21, 10, 1275256800, 0),
(39, 14, 21, 10, 1275256800, 0),
(38, 17, 21, 10, 1275256800, 0),
(37, 15, 21, 10, 1275256800, 0),
(42, 12, 21, 10, 1275256800, 0),
(21, 16, 6, 10, 1278540000, 0),
(22, 1, 3, 10, 1278540000, 0),
(31, 19, 1, 10, 1284328800, 0),
(33, 18, 1, 10, 1284328800, 1295564400),
(44, 12, 1, 20, 1275256800, 0),
(47, 20, 1, 10, 1288652400, 0),
(137, 10, 35, 10, 1291590000, 0),
(50, 21, 6, 10, 1290034800, 0),
(56, 22, 1, 30, 1289862000, 0),
(52, 22, 6, 30, 1289862000, 0),
(53, 10, 1, 10, 1291590000, 0),
(136, 22, 35, 30, 1289862000, 0),
(135, 7, 35, 10, 1275256800, 0),
(134, 20, 35, 10, 1288652400, 0),
(120, 19, 35, 10, 1284328800, 0),
(133, 18, 35, 10, 1284328800, 1295564400),
(144, 12, 35, 20, 1275256800, 0),
(145, 8, 35, 10, 1275256800, 1285279200),
(141, 21, 35, 10, 1290034800, 0),
(140, 5, 35, 10, 1275256800, 0),
(139, 11, 35, 10, 1275256800, 0),
(138, 2, 35, 10, 1275256800, 0),
(116, 9, 26, 10, 1275256800, 0),
(142, 6, 35, 10, 1275256800, 1286488800),
(117, 9, 35, 10, 1275256800, 0),
(95, 16, 26, 10, 1278540000, 0),
(85, 19, 26, 10, 1284328800, 0),
(86, 18, 26, 10, 1284328800, 1295564400),
(87, 20, 26, 10, 1288652400, 0),
(88, 7, 26, 10, 1275256800, 0),
(96, 22, 26, 30, 1289862000, 0),
(90, 10, 26, 10, 1291590000, 0),
(91, 2, 26, 10, 1275256800, 0),
(92, 11, 26, 10, 1275256800, 0),
(93, 5, 26, 10, 1275256800, 0),
(94, 21, 26, 10, 1290034800, 0),
(106, 2, 3, 10, 1297119600, 0),
(99, 8, 26, 10, 1275256800, 1285279200),
(100, 6, 26, 10, 1275256800, 1286488800),
(101, 12, 26, 20, 1275256800, 0),
(111, 10, 34, 10, 1298934000, 0),
(112, 9, 34, 10, 1298934000, 0),
(132, 16, 35, 10, 1278540000, 0);

-- --------------------------------------------------------

--
-- Structure de la table `codev_timetracking_table`
--

CREATE TABLE IF NOT EXISTS `codev_timetracking_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` int(10) NOT NULL,
  `bugid` int(10) NOT NULL,
  `jobid` int(10) NOT NULL,
  `date` int(10) DEFAULT NULL,
  `duration` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bugid` (`bugid`),
  KEY `userid` (`userid`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4039 ;


-- --------------------------------------------------------

--
-- Contenu de la table `mantis_custom_field_project_table`
--

INSERT INTO `mantis_custom_field_project_table` (`field_id`, `project_id`, `sequence`) VALUES
(1, 1, 0),
(1, 2, 0),
(1, 4, 0),
(1, 3, 0),
(1, 5, 0),
(1, 6, 0),
(3, 1, 2),
(4, 1, 4),
(3, 2, 2),
(4, 2, 4),
(3, 4, 2),
(4, 4, 4),
(3, 6, 2),
(4, 6, 4),
(3, 3, 2),
(4, 3, 4),
(3, 5, 2),
(4, 5, 4),
(1, 12, 0),
(3, 12, 2),
(4, 12, 4),
(8, 1, 0),
(1, 15, 0),
(3, 15, 2),
(4, 15, 4),
(4, 11, 0),
(3, 11, 0),
(8, 2, 0),
(8, 12, 0),
(8, 15, 0),
(8, 4, 0),
(8, 6, 0),
(8, 3, 0),
(8, 5, 0),
(8, 16, 0),
(1, 16, 0),
(3, 16, 2),
(4, 16, 4),
(1, 18, 0),
(9, 1, 5),
(9, 2, 5),
(9, 12, 5),
(9, 15, 5),
(9, 4, 5),
(9, 6, 5),
(9, 3, 5),
(9, 5, 5),
(9, 16, 5),
(10, 1, 3),
(10, 2, 3),
(10, 12, 3),
(10, 15, 3),
(10, 4, 3),
(10, 6, 3),
(10, 3, 3),
(10, 5, 3),
(10, 16, 3),
(10, 11, 3),
(11, 1, 5),
(11, 2, 5),
(11, 12, 5),
(11, 15, 5),
(11, 4, 5),
(11, 6, 5),
(11, 3, 5),
(11, 5, 5),
(11, 16, 5),
(11, 11, 5),
(10, 17, 0),
(3, 17, 0),
(4, 17, 0),
(8, 17, 0),
(8, 19, 0),
(1, 19, 0),
(3, 19, 2),
(10, 19, 3),
(4, 19, 4),
(9, 19, 5),
(11, 19, 5),
(8, 20, 0),
(1, 20, 0),
(3, 20, 2),
(10, 20, 3),
(4, 20, 4),
(9, 20, 5),
(11, 20, 5),
(8, 21, 0),
(1, 21, 0),
(3, 21, 2),
(10, 21, 3),
(4, 21, 4),
(9, 21, 5),
(11, 21, 5);

-- --------------------------------------------------------

--
-- Contenu de la table `mantis_custom_field_table`
--

INSERT INTO `mantis_custom_field_table` (`id`, `name`, `type`, `possible_values`, `default_value`, `valid_regexp`, `access_level_r`, `access_level_rw`, `length_min`, `length_max`, `require_report`, `require_update`, `display_report`, `display_update`, `require_resolved`, `display_resolved`, `display_closed`, `require_closed`, `filter_by`) VALUES
(1, 'TC', 0, '', '', '(tcp1b[12]_)?[0-9]{1,5}', 10, 25, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 1),
(3, 'Est. Effort (BI)', 1, '', '', '', 10, 40, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 1),
(4, 'Remaining (RAE)', 1, '', '', '', 10, 40, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 1),
(8, 'Dead Line', 8, '', '', '', 10, 25, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 1),
(9, 'FDL', 1, '', '', '', 10, 55, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 1),
(10, 'Budget supp. (BS)', 1, '', '', '', 10, 55, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 1),
(11, 'Liv. Date', 8, '', '', '', 10, 55, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Contenu de la table `mantis_project_table`
--

INSERT INTO `mantis_project_table` (`id`, `name`, `status`, `enabled`, `view_state`, `access_min`, `file_path`, `description`, `category_id`, `inherit_global`) VALUES
(1, 'ARIANE', 10, 1, 10, 10, '', 'Carte joueur.', 1, 1),
(2, 'Cappuccino', 10, 1, 10, 10, '', 'Nouveau Rapido+ (Amigo).', 1, 1),
(3, 'PeterPan', 10, 1, 10, 10, '', 'Nouveau Joker+.', 1, 1),
(4, 'LotoHum', 10, 0, 10, 10, '', 'Loto de la solidarité.', 1, 1),
(5, 'Promulgation', 10, 1, 10, 10, '', 'Standardisation Diffusion Promulgation.', 1, 1),
(6, 'Maintenance', 10, 1, 10, 10, '', 'Evolutions, Maintenance SJP.', 1, 1),
(17, 'Test', 30, 1, 10, 10, '', 'Ensemble des activitées liées aux tests.', 1, 1),
(18, 'FDL', 30, 0, 10, 10, '', 'Fiches de livraisons.', 1, 1),
(11, 'SuiviOp', 50, 1, 10, 10, '', 'Ensemble des activitées liées au suivi opérationel.', 1, 0),
(12, 'CAYMAN', 10, 0, 10, 10, '', 'Afficheur Point de Vente.', 1, 1),
(16, 'ROMA', 10, 1, 10, 10, '', 'Evolutions Euromillions.', 1, 1),
(15, 'LotoFoot', 10, 1, 10, 10, '', 'Suppression N Mise LF7&15.', 1, 1),
(19, 'Amigo', 10, 1, 10, 10, '', 'Cappuccino multiple.', 1, 1),
(20, 'Promo', 10, 1, 10, 10, '', 'Mécanismes promotionnels temps réels.', 1, 1),
(21, 'MIXN', 10, 1, 10, 10, '', 'Extension offre sport réseau.', 1, 1),
(22, 'FDJ', 50, 0, 10, 10, '', 'MetaProject for all FDJ Projects\r\n', 1, 0),
(23, 'SuiviOp Multimedia', 50, 1, 50, 10, '', 'SuiviOp spécifique à l''équipe Atos-WEB', 1, 0),
(24, 'SuiviOp PROSYS', 10, 1, 10, 10, '', 'SuiviOp specifique a l''equipe PROSYS (OLTP, GEMS, ...)', 1, 0);

-- --------------------------------------------------------

--
-- Contenu de la table `mantis_project_user_list_table`
--

INSERT INTO `mantis_project_user_list_table` (`project_id`, `user_id`, `access_level`) VALUES
(1, 16, 55),
(2, 16, 55),
(12, 16, 55),
(15, 16, 55),
(4, 16, 55),
(6, 16, 55),
(3, 16, 55),
(5, 16, 55),
(11, 16, 55),
(1, 18, 55),
(2, 18, 55),
(12, 18, 55),
(18, 18, 55),
(15, 18, 55),
(4, 18, 55),
(6, 18, 55),
(3, 18, 55),
(5, 18, 55),
(16, 18, 55),
(11, 18, 55),
(1, 19, 55),
(2, 19, 55),
(12, 19, 55),
(18, 19, 55),
(15, 19, 55),
(4, 19, 55),
(6, 19, 55),
(3, 19, 55),
(5, 19, 55),
(16, 19, 55),
(11, 19, 55),
(1, 20, 55),
(2, 20, 55),
(12, 20, 55),
(18, 20, 55),
(15, 20, 55),
(4, 20, 55),
(6, 20, 55),
(3, 20, 55),
(5, 20, 55),
(16, 20, 55),
(11, 20, 55),
(1, 21, 55),
(2, 21, 55),
(12, 21, 55),
(15, 21, 55),
(4, 21, 55),
(6, 21, 55),
(3, 21, 55),
(5, 21, 55),
(16, 21, 55),
(11, 21, 55),
(17, 16, 55),
(17, 21, 55),
(23, 9, 55),
(23, 10, 70),
(23, 22, 70);

-- --------------------------------------------------------

--
-- Contenu de la table `mantis_user_table`
--

INSERT INTO `mantis_user_table` (`id`, `username`, `realname`, `email`, `password`, `enabled`, `protected`, `access_level`, `login_count`, `lost_password_request_count`, `failed_login_count`, `cookie_string`, `last_visit`, `date_created`) VALUES
(1, 'administrator', 'administrator', 'root@localhost', '63a9f0ea7bb98050796b649e85481845', 1, 0, 90, 71, 0, 0, '2eaf0f02c475b359318a87988342c3c32fc634ac03eb8dfaa24ec99ef8046214', 1299584907, 1272555040),
(2, 'mnavarro', 'Mikaël NAVARRO', 'mikael.navarro@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 70, 268, 0, 0, '11cabc1f68e9f33c5cc44be68ad7f31897bfe84d77dbfdd0d07d06007cee886f', 1299747325, 1272609634),
(3, 'qualif', 'Qualification', 'mosi@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 25, 158, 0, 0, 'e2d3d4a08d60c35c322ff71130a5e43743173f3de6979e2256878e7ae7aab689', 1299682519, 1272610523),
(4, 'preq', 'PreQual', 'preq@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 25, 30, 0, 0, 'c3de616cddf08ced072f09a0eab293408854d073c75eaa1f2dcadfc6e0a991f1', 1299230365, 1272610558),
(5, 'sberal', 'Sébastien BERAL', 'sebastien.beral@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 10, 0, 0, '76aa39c680026f4c401e052699286bc5538f25fb5a752502f7ee016de25dad64', 1299081978, 1272610576),
(6, 'vcastelin', 'Vincent CASTELIN', 'vincent.castelin@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 44, 0, 0, 'a51edcc9a9846fdea46fa06f5e8bef4033e4f4fa1f3162904a44afb2f2fab084', 1285597326, 1272610608),
(7, 'lbayle', 'Louis BAYLE', 'lbayle.work@gmail.com', 'caf671ca0104ebfca4cd4b6f4b345e8e', 1, 0, 55, 57, 0, 0, 'f10234a5a2a8eee74e4a44f82fec7a9c8461efea4c2f77030b4a9d08051b88d7', 1299689626, 1272610636),
(8, 'cmaruejols', 'Christophe MARUEJOLS', 'christophe.maruejols@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 28, 0, 0, '26d616184948cf6d3530de8ba53fec739213325b38ed738d1f1e5fbf0c9bc8b1', 1284542987, 1272610681),
(9, 'afebvre', 'Anne FEBVRE', 'anne.febvre@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 70, 79, 0, 0, '464319d0c4e9a12a95836ac3335d94da0cb9a3d32b96175643983f83519325e3', 1299745983, 1272610824),
(10, 'mdoan', 'Marie DOAN', 'marie.doan@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 70, 15, 0, 0, '71a5b5f6087385d8c2053927e7c52ec61501f5540b4037358834c133717a5433', 1299746629, 1272610890),
(11, 'nladraa', 'Nadia LADRAA', 'nadia.ladraa@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 64, 0, 0, '08db035be4a8389fabfd3cb0d2cd6ef693f7a3fd55c7e7563d0364cb4798fd08', 1299745434, 1272610921),
(12, 'golivier', 'Gisèle OLIVIER', 'golivier@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 25, 34, 0, 0, '5403f2e529dd027e20097d7940426851a3486f593c903952db77a94a2b61dbfa', 1299585289, 1272610952),
(13, 'codev', 'CoDev', 'codev-atos@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 90, 158, 0, 0, '1df9e6d3be53458cb27290265f204d93a1a6b233624860466a3975c11a292b22', 1299513877, 1272611005),
(14, 'ogueneau', 'Olivier GUENEAU', 'ogueneau@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 40, 11, 0, 0, 'e741de824bed998a1c47e4f12d71a151cb729f0256553ebc39af26631c729bbc', 1294648654, 1272613873),
(15, 'fdj', 'FdJ', 'fdj@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 40, 0, 0, 0, 'd209db61f82e9f412d606f823c81bb13b3679e465b81e6c32aad076fa893a07e', 1272619824, 1272619824),
(16, 'cpatin', 'Carole PATIN', 'carole.patin@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 24, 0, 0, '9e6f44c13a312b84fc53ddca867ddfe1b0ba47405b73bee6173766ef04443bda', 1299592845, 1278570124),
(17, 'ktan', 'Karter TAN', 'ktan@lfdj.fr', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 25, 11, 0, 0, 'd7ac61539229d09f5d80aa7886c5de4d3e0933bfc7ceb37104fc9e9c43a9301d', 1294839655, 1280836945),
(18, 'jjulien', 'Jerôme JULIEN', 'jerome.julien@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 54, 0, 0, 'bc0e96ed1f1cebf863f284ac53e59b147168aaed411d880d71e4551855ade549', 1295620236, 1284363725),
(19, 'jbaldaccini', 'Jessica BALDACCINI', 'jessica.baldaccini@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 44, 0, 0, 'bba760e39d75ed9ea5484e94dbc537d92a7ece826bc810d861fbbaf4485cfef5', 1299745427, 1284363879),
(20, 'lachaibou', 'Lyna ACHAIBOU', 'lyna.achaibou@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 66, 0, 0, '1f28b703895404d6eb00ff6d512ee13a91102671069215c21d61126803ca2dbb', 1299747112, 1284364052),
(21, 'tuzieblo', 'Tomasz UZIEBLO', 'tomasz.uzieblo@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 55, 36, 0, 0, 'cc2162a91a787c1998e720fdad3d051148b18227ae253ccc22e39ffadfebfdc6', 1299746986, 1290073340),
(22, 'mbastide', 'Marie BASTIDE', 'marie.bastide@atosorigin.com', 'd41d8cd98f00b204e9800998ecf8427e', 1, 0, 70, 72, 0, 0, '5b9d06a658c4dc21fac7b494b79b505155dd0bc8f87de8d0e3f0817d90df1bfe', 1299747406, 1290517904);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
