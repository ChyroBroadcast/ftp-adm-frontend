-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 25 Août 2015 à 18:25
-- Version du serveur :  5.5.44-0+deb8u1
-- Version de PHP :  5.6.9-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `qlowd`
--

-- --------------------------------------------------------

--
-- Structure de la table `Address`
--

CREATE TABLE IF NOT EXISTS `Address` (
`id` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `street` text NOT NULL,
  `zip_code` text NOT NULL,
  `city` text NOT NULL,
  `country` text NOT NULL,
  `phone` text NOT NULL,
  `iban` text NOT NULL,
  `vat_number` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `AddressCustomerRelation`
--

CREATE TABLE IF NOT EXISTS `AddressCustomerRelation` (
  `customer` int(11) NOT NULL,
  `address` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Customer`
--

CREATE TABLE IF NOT EXISTS `Customer` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_space` bigint(20) unsigned NOT NULL,
  `used_space` bigint(20) unsigned NOT NULL,
  `price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `FtpUser`
--

CREATE TABLE IF NOT EXISTS `FtpUser` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL,
  `shell` varchar(255) NOT NULL,
  `access` enum('none','read','write','read_write') NOT NULL DEFAULT 'none',
  `chroot` tinyint(1) NOT NULL,
  `homedirectory` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `FtpXferLog`
--

CREATE TABLE IF NOT EXISTS `FtpXferLog` (
  `id` int(10) unsigned NOT NULL,
  `username` text NOT NULL,
  `filename` text NOT NULL,
  `filesize` bigint(20) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(1) NOT NULL,
  `sync_status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
`id` int(10) unsigned NOT NULL,
  `login` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `fullname` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `phone` text NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `customer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `Address`
--
ALTER TABLE `Address`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `AddressCustomerRelation`
--
ALTER TABLE `AddressCustomerRelation`
 ADD PRIMARY KEY (`customer`,`address`), ADD KEY `customer` (`customer`), ADD KEY `address` (`address`);

--
-- Index pour la table `Customer`
--
ALTER TABLE `Customer`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `FtpUser`
--
ALTER TABLE `FtpUser`
 ADD KEY `id` (`id`);

--
-- Index pour la table `User`
--
ALTER TABLE `User`
 ADD PRIMARY KEY (`id`), ADD KEY `customer` (`customer`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `Address`
--
ALTER TABLE `Address`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `Customer`
--
ALTER TABLE `Customer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `User`
--
ALTER TABLE `User`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `AddressCustomerRelation`
--
ALTER TABLE `AddressCustomerRelation`
ADD CONSTRAINT `AddressCustomerRelation_ibfk_2` FOREIGN KEY (`address`) REFERENCES `Address` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `AddressCustomerRelation_ibfk_1` FOREIGN KEY (`customer`) REFERENCES `Customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `FtpUser`
--
ALTER TABLE `FtpUser`
ADD CONSTRAINT `FtpUser_ibfk_1` FOREIGN KEY (`id`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `User`
--
ALTER TABLE `User`
ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`customer`) REFERENCES `Customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
