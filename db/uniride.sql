-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 24 nov. 2026 à 09:42
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `uniride`
--

-- --------------------------------------------------------

--
-- Structure de la table `appuser`
--

CREATE TABLE `appuser` (
  `idUser` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `dateInscription` date NOT NULL,
  `statut` varchar(50) NOT NULL,
  `emailVerifie` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `appuser`
--

INSERT INTO `appuser` (`idUser`, `nom`, `prenom`, `email`, `password`, `telephone`, `adresse`, `dateInscription`, `statut`, `emailVerifie`) VALUES
(1, 'madiouni', 'nour', 'madiouninour0@gmail.com', '$2y$10$GmF5hpWRRZvaS990y0U3teiZhbSwMieAiULmsKjn229gWam9Qq.ta', '28530676', '20 rue uranium la marsa', '2026-11-22', 'actif', 0),
(2, 'kchaw', 'nassim', 'kchawnassim3@gmail.com', '$2y$10$s53MClUKXqiSRyZy0d6jpukM4LW9tvNaE/jq.nhBqCGWzeZ/bUzhK', '27000432', 'la marsa', '2026-11-22', 'inactif', 0),
(4, 'kckkjhgv', 'nass', 'nourelhoudamadiouni@gmail.com', '$2y$10$A3zMefgZfZpTykzzMG9lDOyXPKdafkF3VF42j1bZU3MhHjxM122gS', '54869455', '20 rue zbat', '2026-11-23', 'actif', 0);

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

CREATE TABLE `paiement` (
  `idPaiement` int(11) NOT NULL,
  `idReservation` int(11) NOT NULL,
  `montant` double NOT NULL,
  `datePaiement` date NOT NULL,
  `modePaiement` varchar(250) NOT NULL,
  `statut` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `idReservation` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idTrip` int(11) NOT NULL,
  `dateReservation` date NOT NULL,
  `nbPlaces` int(11) NOT NULL,
  `statut` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `idRole` int(11) NOT NULL,
  `nomRole` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`idRole`, `nomRole`) VALUES
(1, 'Utilisateur1'),
(2, 'Administrateur'),
(4, 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `trip`
--

CREATE TABLE `trip` (
  `idTrip` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idVehicule` int(11) NOT NULL,
  `lieuDepart` varchar(200) NOT NULL,
  `lieuArrivee` varchar(200) NOT NULL,
  `dateDepart` date NOT NULL,
  `dateArrivee` date NOT NULL,
  `prix` double NOT NULL,
  `nbPlacesDisponibles` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `statut` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tripstop`
--

CREATE TABLE `tripstop` (
  `idStop` int(11) NOT NULL,
  `idTrip` int(11) NOT NULL,
  `lieuStop` varchar(200) NOT NULL,
  `heurePassage` time NOT NULL,
  `ordreStop` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `userrole`
--

CREATE TABLE `userrole` (
  `idUser` int(11) NOT NULL,
  `idRole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `userrole`
--

INSERT INTO `userrole` (`idUser`, `idRole`) VALUES
(1, 1),
(1, 2),
(2, 1),
(4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `idVehicule` int(11) NOT NULL,
  `marque` varchar(150) NOT NULL,
  `modele` varchar(150) NOT NULL,
  `immatriculation` varchar(150) NOT NULL,
  `couleur` varchar(150) NOT NULL,
  `annee` int(11) NOT NULL,
  `nbPlaces` int(11) NOT NULL,
  `typeVehicule` varchar(150) NOT NULL,
  `statut` varchar(150) NOT NULL,
  `idUser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vehiculephoto`
--

CREATE TABLE `vehiculephoto` (
  `idPhoto` int(11) NOT NULL,
  `idVehicule` int(11) NOT NULL,
  `urlPhoto` varchar(200) NOT NULL,
  `estPrincipale` tinyint(1) NOT NULL,
  `dateAjout` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `appuser`
--
ALTER TABLE `appuser`
  ADD PRIMARY KEY (`idUser`);

--
-- Index pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`idPaiement`),
  ADD KEY `fk_reservation_paiement` (`idReservation`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`idReservation`),
  ADD KEY `fk_reservation_user` (`idUser`),
  ADD KEY `fk_reservation_trip` (`idTrip`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`idRole`);

--
-- Index pour la table `trip`
--
ALTER TABLE `trip`
  ADD PRIMARY KEY (`idTrip`),
  ADD KEY `fk_user_trip` (`idUser`),
  ADD KEY `fk_vehicule_trip` (`idVehicule`);

--
-- Index pour la table `tripstop`
--
ALTER TABLE `tripstop`
  ADD PRIMARY KEY (`idStop`),
  ADD KEY `fk_tripstop_trip` (`idTrip`);

--
-- Index pour la table `userrole`
--
ALTER TABLE `userrole`
  ADD PRIMARY KEY (`idUser`,`idRole`),
  ADD KEY `fk_userrole_role` (`idRole`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`idVehicule`),
  ADD KEY `fk_vehicule_user` (`idUser`);

--
-- Index pour la table `vehiculephoto`
--
ALTER TABLE `vehiculephoto`
  ADD PRIMARY KEY (`idPhoto`),
  ADD KEY `idVehicule` (`idVehicule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `appuser`
--
ALTER TABLE `appuser`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `idPaiement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `idReservation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `idRole` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `trip`
--
ALTER TABLE `trip`
  MODIFY `idTrip` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `idVehicule` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vehiculephoto`
--
ALTER TABLE `vehiculephoto`
  MODIFY `idPhoto` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `fk_reservation_paiement` FOREIGN KEY (`idReservation`) REFERENCES `reservation` (`idReservation`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_reservation_trip` FOREIGN KEY (`idTrip`) REFERENCES `trip` (`idTrip`),
  ADD CONSTRAINT `fk_reservation_user` FOREIGN KEY (`idUser`) REFERENCES `appuser` (`idUser`);

--
-- Contraintes pour la table `trip`
--
ALTER TABLE `trip`
  ADD CONSTRAINT `fk_user_trip` FOREIGN KEY (`idUser`) REFERENCES `appuser` (`idUser`),
  ADD CONSTRAINT `fk_vehicule_trip` FOREIGN KEY (`idVehicule`) REFERENCES `vehicule` (`idVehicule`);

--
-- Contraintes pour la table `tripstop`
--
ALTER TABLE `tripstop`
  ADD CONSTRAINT `fk_tripstop_trip` FOREIGN KEY (`idTrip`) REFERENCES `trip` (`idTrip`);

--
-- Contraintes pour la table `userrole`
--
ALTER TABLE `userrole`
  ADD CONSTRAINT `fk_userrole_role` FOREIGN KEY (`idRole`) REFERENCES `role` (`idRole`),
  ADD CONSTRAINT `fk_userrole_user` FOREIGN KEY (`idUser`) REFERENCES `appuser` (`idUser`);

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `fk_vehicule_user` FOREIGN KEY (`idUser`) REFERENCES `appuser` (`idUser`);

--
-- Contraintes pour la table `vehiculephoto`
--
ALTER TABLE `vehiculephoto`
  ADD CONSTRAINT `fk_photovehicule_vehicule` FOREIGN KEY (`idVehicule`) REFERENCES `vehicule` (`idVehicule`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
