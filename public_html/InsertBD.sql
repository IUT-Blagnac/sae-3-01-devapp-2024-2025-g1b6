-- Suppression des données des tables sans supprimer les structures de table
DELETE FROM AVIS;
DELETE FROM PANIER;
DELETE FROM SOUHAITER;
DELETE FROM ASSOPACK;
DELETE FROM APPARTENIRCATEG;
DELETE FROM POSSEDERIP;
DELETE FROM POSSEDERADR;
DELETE FROM COMMANDE;
DELETE FROM CATPERE;  -- Suppression des données de CATPERE avant CATEGORIE
DELETE FROM PRODUIT;
DELETE FROM CLIENT;
DELETE FROM INFORMATIONPAIEMENT;
DELETE FROM ADRESSE;
DELETE FROM PACK;
DELETE FROM CATEGORIE;  -- Maintenant possible après CATPERE
DELETE FROM MARQUE;
DELETE FROM TRANSPORTEUR;
DELETE FROM ADMINISTRATEUR;


-- Insertion des transporteurs
INSERT INTO TRANSPORTEUR (TYPEEXP, FRAISEXP, FRAISKG, DELAILIVRAISON) VALUES
('France', 5.99, 1.00, 5),
('Europe', 9.99, 1.50, 2),
('Afrique', 3.99, 0.80, 7),
('Premium', 12.99, 2.00, 1),
('International', 15.99, 2.50, 10);

-- Insertion des marques
INSERT INTO MARQUE (NOMMARQUE, DESCMARQUE) VALUES
('Lego', 'Marque de jouets de construction en briques.'),
('Playmobil', 'Marque de jouets de figurines et de jeux de rôle.'),
('Mattel', 'Marque de jouets et de jeux pour enfants.'),
('Hasbro', 'Marque de jouets et de jeux de société.'),
('Fisher-Price', 'Marque de jouets éducatifs pour enfants.'),
('VTech', 'Marque de jouets électroniques et éducatifs.'),
('Hot Wheels', 'Marque de voitures miniatures et de circuits.'),
('Barbie', 'Marque de poupées et d''accessoires.'),
('Nerf', 'Marque de jouets de tir et de jeux d''action.'),
( 'Mega Bloks', 'Marque de jouets de construction en blocs.'),
( 'Brio', 'Marque de jouets en bois et de trains miniatures.'),
( 'Schleich', 'Marque de figurines réalistes d''animaux et de personnages.'),
( 'Melissa & Doug', 'Marque de jouets éducatifs et créatifs.'),
( 'Ravensburger', 'Marque de puzzles et de jeux de société.'),
( 'Spin Master', 'Marque de jouets innovants et de jeux de société.'),
( 'LeapFrog', 'Marque de jouets éducatifs électroniques.'),
( 'Little Tikes', 'Marque de jouets d''extérieur et de jeux de rôle.'),
( 'Step2', 'Marque de jouets d''extérieur et de jeux de rôle.'),
( 'Hape', 'Marque de jouets en bois et de jeux éducatifs.'),
( 'Janod', 'Marque de jouets en bois et de jeux éducatifs.'),
( 'Ludorama', 'Marque de jouets ludorama qui vend de tout.'),
('Sony', 'Marque populaire de jeux vidéos.'),
('Dimoitout', 'Marque de Peluches en tout genre.');

-- Insertion des catégories principales
INSERT INTO CATEGORIE (NOMCATEG, DESCCATEG) VALUES
('Jouets en Bois', null),
('Jeux de Société', null),
('Figurines', null),
('Puzzles', null),
('Peluches', null),
('Électronique', null),
('Véhicules', null),
('Extérieur', null),
('Créativité', null),
('Construction', null),
('Enfants', 'Jeux pour enfants'),
('Ado', 'Jeux pour ados'),
('Jeune-adulte', 'Jeux pour jeunes adultes'),
('Adulte', 'Jeux pour adultes'),
('Puzzles en 3D', null),('Puzzles classiques', null),
('Blocs de construction', null),
('Modèles complexes', null),
('Jeux éducatifs', null),
('Jouets d''éveil', null),
('Jeux de cartes', null),
('Plateaux de jeux', null),
('Collection ToyStory', 'La nouvelle collection de Toy Story'),
('Collection StarWars', 'La nouvelle collection de Star Wars'),
('Collection Disney', 'La nouvelle collection de Disney'),
('Collection Game of throne', 'La nouvelle collection Game of throne'),
('Collection DC', 'La nouvelle collection de DC'),
('Jeux Vidéo', null),
('Jeu Vidéo aventure', null),
('Jeu Vidéo RPG', null),
('Jeu Vidéo Action', null),
('Jeu Vidéo Horreur', null),
('Figurines en Bois', null),
('Robots', null),
('Tablettes', null),
('Jeux Interactifs', null),
('Voitures', null),
('Camions', null),
('Avions', null),
('Jeux de Jardin', null),
('Piscines', null),
('Toboggans', null),
('Peinture', null),
('Bricolage', null),
('Pâte à Modeler', null),
('Peluches Classique', null),
('Figurine Classique', null),
('Console de jeux', null);

-- Insertion des packs
INSERT INTO PACK (NOMPACK, DESCPACK) VALUES
('Pack éducatif', 'Ensemble de jeux éducatifs pour enfants'),
('Pack créatif', 'Stimule la créativité des enfants'),
('Pack voitures', '3 voitures de différentes couleurs'),
('Pack animaux', 'Ensemble de jouets sur les animaux'),
('Pack bois', 'Jouets en bois'),
('Pack poupées', 'Poupées et accessoires'),
('Pack aventure', 'Jeux pour petits explorateurs'),
('Pack jeu vidéo', 'Jeux vidéo pour les gamers');

-- Insertion de 50 clients répartis en France
INSERT INTO CLIENT (NOMCLIENT, PRENOMCLIENT, NUMTEL, EMAIL, PASSWORD, DATEN, DATEINSCRIPTION) VALUES
('Dupont', 'Jean', '0612345678', 'jean.dupont@gmail.com', 'hashed_password1', '1985-03-15', '2024-01-01'),
('Martin', 'Sophie', '0623456789', 'sophie.martin@gmail.com', 'hashed_password2', '1990-07-20', '2024-01-02'),
('Bernard', 'Luc', '0634567890', 'luc.bernard@gmail.com', 'hashed_password3', '1982-05-10', '2024-01-03'),
('Durand', 'Marie', '0645678901', 'marie.durand@gmail.com', 'hashed_password4', '1995-08-25', '2024-01-04'),
('Lefevre', 'Paul', '0656789012', 'paul.lefevre@gmail.com', 'hashed_password5', '1988-11-30', '2024-01-05'),
('Moreau', 'Julie', '0667890123', 'julie.moreau@gmail.com', 'hashed_password6', '1992-02-14', '2024-01-06'),
('Simon', 'Pierre', '0678901234', 'pierre.simon@gmail.com', 'hashed_password7', '1980-07-22', '2024-01-07'),
('Laurent', 'Emma', '0689012345', 'emma.laurent@gmail.com', 'hashed_password8', '1993-09-18', '2024-01-08'),
('Michel', 'Lucas', '0690123456', 'lucas.michel@gmail.com', 'hashed_password9', '1987-12-05', '2024-01-09'),
('Garcia', 'Chloe', '0612345679', 'chloe.garcia@gmail.com', 'hashed_password10', '1991-03-21', '2024-01-10'),
('Martinez', 'Hugo', '0623456780', 'hugo.martinez@gmail.com', 'hashed_password11', '1984-06-15', '2024-01-11'),
('Rodriguez', 'Alice', '0634567891', 'alice.rodriguez@gmail.com', 'hashed_password12', '1996-10-10', '2024-01-12'),
('Hernandez', 'Louis', '0645678902', 'louis.hernandez@gmail.com', 'hashed_password13', '1989-01-25', '2024-01-13'),
('Lopez', 'Sarah', '0656789013', 'sarah.lopez@gmail.com', 'hashed_password14', '1994-04-30', '2024-01-14'),
('Gonzalez', 'Thomas', '0667890124', 'thomas.gonzalez@gmail.com', 'hashed_password15', '1983-08-05', '2024-01-15'),
('Wilson', 'Laura', '0678901235', 'laura.wilson@gmail.com', 'hashed_password16', '1990-12-20', '2024-01-16'),
('Anderson', 'Maxime', '0689012346', 'maxime.anderson@gmail.com', 'hashed_password17', '1986-03-10', '2024-01-17'),
('Thomas', 'Camille', '0690123457', 'camille.thomas@gmail.com', 'hashed_password18', '1992-06-25', '2024-01-18'),
('Taylor', 'Nathan', '0612345680', 'nathan.taylor@gmail.com', 'hashed_password19', '1985-09-15', '2024-01-19'),
('Moore', 'Lea', '0623456781', 'lea.moore@gmail.com', 'hashed_password20', '1991-11-30', '2024-01-20'),
('Jackson', 'Ethan', '0634567892', 'ethan.jackson@gmail.com', 'hashed_password21', '1988-02-14', '2024-01-21'),
('Martin', 'Olivia', '0645678903', 'olivia.martin@gmail.com', 'hashed_password22', '1993-05-10', '2024-01-22'),
('Lee', 'Gabriel', '0656789014', 'gabriel.lee@gmail.com', 'hashed_password23', '1987-08-25', '2024-01-23'),
('Perez', 'Manon', '0667890125', 'manon.perez@gmail.com', 'hashed_password24', '1990-11-30', '2024-01-24'),
('Thompson', 'Arthur', '0678901236', 'arthur.thompson@gmail.com', 'hashed_password25', '1984-02-14', '2024-01-25'),
('White', 'Emma', '0689012347', 'emma.white@gmail.com', 'hashed_password26', '1995-05-10', '2024-01-26'),
('Harris', 'Leo', '0690123458', 'leo.harris@gmail.com', 'hashed_password27', '1989-08-25', '2024-01-27'),
('Clark', 'Sophie', '0612345681', 'sophie.clark@gmail.com', 'hashed_password28', '1992-11-30', '2024-01-28'),
('Lewis', 'Lucas', '0623456782', 'lucas.lewis@gmail.com', 'hashed_password29', '1986-02-14', '2024-01-29'),
('Robinson', 'Julie', '0634567893', 'julie.robinson@gmail.com', 'hashed_password30', '1991-05-10', '2024-01-30'),
('Walker', 'Nathan', '0645678904', 'nathan.walker@gmail.com', 'hashed_password31', '1985-08-25', '2024-01-31'),
('Young', 'Alice', '0656789015', 'alice.young@gmail.com', 'hashed_password32', '1993-11-30', '2024-02-01'),
('Allen', 'Thomas', '0667890126', 'thomas.allen@gmail.com', 'hashed_password33', '1988-02-14', '2024-02-02'),
('King', 'Emma', '0678901237', 'emma.king@gmail.com', 'hashed_password34', '1990-05-10', '2024-02-03'),
('Scott', 'Leo', '0689012348', 'leo.scott@gmail.com', 'hashed_password35', '1984-08-25', '2024-02-04'),
('Green', 'Sophie', '0690123459', 'sophie.green@gmail.com', 'hashed_password36', '1992-11-30', '2024-02-05'),
('Baker', 'Lucas', '0612345682', 'lucas.baker@gmail.com', 'hashed_password37', '1987-02-14', '2024-02-06'),
('Adams', 'Julie', '0623456783', 'julie.adams@gmail.com', 'hashed_password38', '1991-05-10', '2024-02-07'),
('Nelson', 'Nathan', '0634567894', 'nathan.nelson@gmail.com', 'hashed_password39', '1985-08-25', '2024-02-08'),
('Carter', 'Alice', '0645678905', 'alice.carter@gmail.com', 'hashed_password40', '1993-11-30', '2024-02-09'),
('Mitchell', 'Thomas', '0656789016', 'thomas.mitchell@gmail.com', 'hashed_password41', '1988-02-14', '2024-02-10'),
('Perez', 'Emma', '0667890127', 'emma.perez@gmail.com', 'hashed_password42', '1990-05-10', '2024-02-11'),
('Roberts', 'Leo', '0678901238', 'leo.roberts@gmail.com', 'hashed_password43', '1984-08-25', '2024-02-12'),
('Turner', 'Sophie', '0689012349', 'sophie.turner@gmail.com', 'hashed_password44', '1992-11-30', '2024-02-13'),
('Phillips', 'Lucas', '0690123460', 'lucas.phillips@gmail.com', 'hashed_password45', '1987-02-14', '2024-02-14'),
('Campbell', 'Julie', '0612345683', 'julie.campbell@gmail.com', 'hashed_password46', '1991-05-10', '2024-02-15'),
('Parker', 'Nathan', '0623456784', 'nathan.parker@gmail.com', 'hashed_password47', '1985-08-25', '2024-02-16'),
('Evans', 'Alice', '0634567895', 'alice.evans@gmail.com', 'hashed_password48', '1993-11-30', '2024-02-17'),
('Edwards', 'Thomas', '0645678906', 'thomas.edwards@gmail.com', 'hashed_password49', '1988-02-14', '2024-02-18'),
('Collins', 'Emma', '0656789017', 'emma.collins@gmail.com', 'hashed_password50', '1990-05-10', '2024-02-19');

-- Insertion des adresses
INSERT INTO ADRESSE (NUMRUE, NOMRUE, COMPLEMENTADR, NOMVILLE, CODEPOSTAL, PAYS) VALUES
('12', 'Rue de la Paix', NULL, 'Paris', 75002, 'France'),
('34', 'Avenue des Champs-Élysées', 'Appartement 5B', 'Paris', 75008, 'France'),
('56', 'Boulevard Saint-Germain', NULL, 'Paris', 75006, 'France'),
('78', 'Rue de Rivoli', 'Bâtiment A', 'Paris', 75001, 'France'),
('90', 'Avenue Montaigne', NULL, 'Paris', 75008, 'France'),
('123', 'Rue de la République', NULL, 'Lyon', 69002, 'France'),
('456', 'Cours Lafayette', 'Étage 3', 'Lyon', 69003, 'France'),
('789', 'Rue Victor Hugo', NULL, 'Lyon', 69002, 'France'),
('101', 'Avenue Jean Jaurès', NULL, 'Lyon', 69007, 'France'),
('202', 'Rue Garibaldi', NULL, 'Lyon', 69003, 'France'),
('15', 'Rue de la Liberté', NULL, 'Marseille', 13001, 'France'),
('27', 'Avenue du Prado', 'Appartement 2A', 'Marseille', 13008, 'France'),
('39', 'Boulevard Michelet', NULL, 'Marseille', 13009, 'France'),
('51', 'Rue Saint-Ferréol', 'Bâtiment B', 'Marseille', 13001, 'France'),
('63', 'Avenue de la Canebière', NULL, 'Marseille', 13001, 'France'),
('75', 'Rue Paradis', NULL, 'Marseille', 13006, 'France'),
('87', 'Avenue des Chartreux', 'Étage 4', 'Marseille', 13004, 'France'),
('99', 'Boulevard Baille', NULL, 'Marseille', 13005, 'France'),
('111', 'Rue de Rome', NULL, 'Marseille', 13006, 'France'),
('123', 'Avenue du Prado', NULL, 'Marseille', 13008, 'France'),
('135', 'Rue de la République', NULL, 'Nice', 06000, 'France'),
('147', 'Avenue Jean Médecin', 'Appartement 3C', 'Nice', 06000, 'France'),
('159', 'Boulevard Victor Hugo', NULL, 'Nice', 06000, 'France'),
('171', 'Rue de France', 'Bâtiment C', 'Nice', 06000, 'France'),
('183', 'Avenue des Fleurs', NULL, 'Nice', 06000, 'France'),
('195', 'Rue de la Buffa', NULL, 'Nice', 06000, 'France'),
('207', 'Avenue de la Californie', 'Étage 5', 'Nice', 06000, 'France'),
('219', 'Boulevard Gambetta', NULL, 'Nice', 06000, 'France'),
('231', 'Rue de l''Hôtel des Postes', NULL, 'Nice', 06000, 'France'),
('243', 'Avenue Thiers', NULL, 'Nice', 06000, 'France'),
('255', 'Rue de la Liberté', NULL, 'Bordeaux', 33000, 'France'),
('267', 'Avenue des Quinconces', 'Appartement 4D', 'Bordeaux', 33000, 'France'),
('279', 'Boulevard de la République', NULL, 'Bordeaux', 33000, 'France'),
('291', 'Rue Sainte-Catherine', 'Bâtiment D', 'Bordeaux', 33000, 'France'),
('303', 'Avenue de la Victoire', NULL, 'Bordeaux', 33000, 'France'),
('315', 'Rue du Palais Gallien', NULL, 'Bordeaux', 33000, 'France'),
('327', 'Avenue de la Libération', 'Étage 6', 'Bordeaux', 33000, 'France'),
('339', 'Boulevard Georges V', NULL, 'Bordeaux', 33000, 'France'),
('351', 'Rue du Château d''Eau', NULL, 'Bordeaux', 33000, 'France'),
('363', 'Avenue de la Marne', NULL, 'Bordeaux', 33000, 'France'),
('375', 'Rue de la République', NULL, 'Toulouse', 31000, 'France'),
('387', 'Avenue Jean Jaurès', 'Appartement 5E', 'Toulouse', 31000, 'France'),
('399', 'Boulevard de Strasbourg', NULL, 'Toulouse', 31000, 'France'),
('411', 'Rue de Metz', 'Bâtiment E', 'Toulouse', 31000, 'France'),
('423', 'Avenue des Minimes', NULL, 'Toulouse', 31000, 'France'),
('435', 'Rue de la Colombette', NULL, 'Toulouse', 31000, 'France'),
('447', 'Avenue de l''URSS', 'Étage 7', 'Toulouse', 31000, 'France'),
('459', 'Boulevard de la Gare', NULL, 'Toulouse', 31000, 'France'),
('471', 'Rue de la Pomme', NULL, 'Toulouse', 31000, 'France'),
('483', 'Avenue de la Gloire', NULL, 'Toulouse', 31000, 'France');


-- Insertion des informations de paiement
INSERT INTO INFORMATIONPAIEMENT (NUMCB, NOMCOMPLETCB, DATEEXP, CRYPTOGRAMME) VALUES
('1234567890123456', 'Jean Dupont', '2025-12-31', 123),
('2345678901234567', 'Sophie Martin', '2024-11-30', 456),
('3456789012345678', 'Luc Bernard', '2026-10-31', 789),
('4567890123456789', 'Marie Durand', '2023-09-30', 012),
('5678901234567890', 'Paul Lefevre', '2025-08-31', 345),
('6789012345678901', 'Julie Moreau', '2024-07-31', 678),
('7890123456789012', 'Pierre Simon', '2026-06-30', 901),
('8901234567890123', 'Emma Laurent', '2023-05-31', 234),
('9012345678901234', 'Lucas Michel', '2025-04-30', 567),
('0123456789012345', 'Chloe Garcia', '2024-03-31', 890);

-- Insertion des produits
INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
(1, 'Train en bois', 'Train avec rails en bois', 29.99, 'Multicolore', 'Bois', 1.2, 50, 1),
(2, 'Puzzle animaux', 'Puzzle éducatif en bois', 15.99, 'Multicolore', 'Bois', 0.8, 30, 1),
(3, 'Voiture télécommandée', 'Voiture électrique rapide', 49.99, 'Rouge', 'Plastique', 1.5, 25, 1),
(8, 'Maison de poupée', 'Maison en bois avec meubles', 69.99, 'Rose', 'Bois', 4.0, 20, 1),
(4, 'Peluche éléphant', 'Peluche douce pour câliner', 19.99, 'Gris', 'Tissu', 0.5, 100, 1),
(2, 'Jeu de mémoire', 'Jeu de société pour mémoire', 14.99, 'Multicolore', 'Carton', 0.7, 45, 1),
(2, 'Camion de pompier', 'Camion miniature avec échelle', 34.99, 'Rouge', 'Métal et plastique', 2.0, 30, 1),
(6, 'Cuisinière jouet', 'Cuisine miniature avec ustensiles', 59.99, 'Rose', 'Bois et plastique', 4.5, 15, 1),
(1, 'Cheval à bascule', 'Cheval en bois avec poignée', 49.99, 'Marron', 'Bois', 3.8, 10, 1),
(3, 'Jeu de société', 'Jeu éducatif sur les animaux', 24.99, 'Multicolore', 'Carton et plastique', 1.2, 40, 1),
(1, 'Puzzle en bois', 'Puzzle éducatif en bois', 12.99, 'Multicolore', 'Bois', 0.5, 50, 1),
(2, 'Jeu de cartes', 'Jeu de cartes pour enfants', 9.99, 'Multicolore', 'Carton', 0.2, 100, 1),
(3, 'Figurine dinosaure', 'Figurine de dinosaure en plastique', 14.99, 'Vert', 'Plastique', 0.3, 75, 1),
(4, 'Peluche ours', 'Peluche douce en forme d''ours', 19.99, 'Brun', 'Tissu', 0.6, 80, 1),
(5, 'Voiture de course', 'Voiture de course télécommandée', 39.99, 'Rouge', 'Plastique', 1.5, 40, 1),
(6, 'Robot interactif', 'Robot éducatif interactif', 49.99, 'Argent', 'Plastique et métal', 2.0, 30, 1),
(7, 'Tricycle', 'Tricycle pour enfants', 59.99, 'Bleu', 'Métal et plastique', 4.0, 20, 1),
(8, 'Toboggan', 'Toboggan pour jardin', 89.99, 'Jaune', 'Plastique', 10.0, 15, 1),
(9, 'Kit de peinture', 'Kit de peinture pour enfants', 19.99, 'Multicolore', 'Plastique et peinture', 1.0, 50, 1),
(10, 'Jeu de construction', 'Blocs de construction en bois', 29.99, 'Multicolore', 'Bois', 2.0, 60, 1),
(1, 'Casse-tête en bois', 'Casse-tête éducatif en bois', 14.99, 'Multicolore', 'Bois', 0.7, 40, 1),
(2, 'Jeu de société classique', 'Jeu de société pour toute la famille', 24.99, 'Multicolore', 'Carton', 1.2, 50, 1),
(3, 'Figurine super-héros', 'Figurine de super-héros en plastique', 19.99, 'Bleu', 'Plastique', 0.4, 70, 1),
(4, 'Peluche licorne', 'Peluche douce en forme de licorne', 24.99, 'Blanc', 'Tissu', 0.8, 60, 1),
(5, 'Camion benne', 'Camion benne miniature', 34.99, 'Jaune', 'Métal et plastique', 2.5, 25, 1),
(6, 'Tablette éducative', 'Tablette éducative pour enfants', 59.99, 'Noir', 'Plastique et électronique', 1.5, 20, 1),
(7, 'Balançoire', 'Balançoire pour jardin', 79.99, 'Vert', 'Métal et plastique', 8.0, 10, 1),
(8, 'Piscine gonflable', 'Piscine gonflable pour enfants', 49.99, 'Bleu', 'Plastique', 5.0, 20, 1),
(9, 'Kit de bricolage', 'Kit de bricolage pour enfants', 29.99, 'Multicolore', 'Plastique et métal', 1.5, 30, 1),
(10, 'Jeu de dominos', 'Jeu de dominos en bois', 19.99, 'Multicolore', 'Bois', 1.0, 50, 1),
(1, 'Labyrinthe en bois', 'Labyrinthe éducatif en bois', 16.99, 'Multicolore', 'Bois', 0.8, 35, 1),
(2, 'Jeu de mémoire', 'Jeu de mémoire pour enfants', 14.99, 'Multicolore', 'Carton', 0.5, 60, 1),
(3, 'Figurine animal', 'Figurine d''animal en plastique', 12.99, 'Marron', 'Plastique', 0.3, 80, 1),
(4, 'Peluche lapin', 'Peluche douce en forme de lapin', 19.99, 'Blanc', 'Tissu', 0.6, 70, 1),
(5, 'Train électrique', 'Train électrique miniature', 49.99, 'Noir', 'Plastique et métal', 3.0, 20, 1),
(6, 'Drone pour enfants', 'Drone télécommandé pour enfants', 69.99, 'Blanc', 'Plastique et électronique', 1.2, 15, 1),
(7, 'Trottinette', 'Trottinette pour enfants', 39.99, 'Rouge', 'Métal et plastique', 3.5, 25, 1),
(8, 'Ballon sauteur', 'Ballon sauteur pour enfants', 19.99, 'Rouge', 'Plastique', 1.0, 50, 1),
(9, 'Kit de couture', 'Kit de couture pour enfants', 24.99, 'Multicolore', 'Tissu et plastique', 0.8, 40, 1),
(10, 'Jeu de quilles', 'Jeu de quilles en bois', 29.99, 'Multicolore', 'Bois', 2.0, 30, 1),
(1, 'Boîte à formes', 'Boîte à formes en bois', 14.99, 'Multicolore', 'Bois', 0.7, 50, 1),
(2, 'Jeu de l''oie', 'Jeu de société classique', 19.99, 'Multicolore', 'Carton', 1.0, 40, 1),
(3, 'Figurine chevalier', 'Figurine de chevalier en plastique', 14.99, 'Gris', 'Plastique', 0.4, 60, 1),
(4, 'Peluche chien', 'Peluche douce en forme de chien', 19.99, 'Marron', 'Tissu', 0.6, 70, 1),
(5, 'Hélicoptère télécommandé', 'Hélicoptère miniature télécommandé', 39.99, 'Bleu', 'Plastique et métal', 1.5, 30, 1),
(6, 'Console de jeux', 'Console de jeux pour enfants', 59.99, 'Noir', 'Plastique et électronique', 1.2, 20, 1),
(7, 'Bateau gonflable', 'Bateau gonflable pour enfants', 49.99, 'Jaune', 'Plastique', 3.0, 15, 1),
(8, 'Cerf-volant', 'Cerf-volant pour enfants', 14.99, 'Multicolore', 'Tissu et plastique', 0.5, 50, 1),
(9, 'Kit de jardinage', 'Kit de jardinage pour enfants', 19.99, 'Vert', 'Plastique et métal', 1.0, 30, 1),
(2, 'Marionnettes en tissu', 'Ensemble de 4 marionnettes', 29.99, 'Multicolore', 'Tissu', 1.1, 25, 1);

INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
(1, 'Lego starWars', 'Jeu de construction Lego StarWars', 29.99, 'Multicolore', 'Plastique', 1.2, 50, 1),
(2, 'Playmobil ToyStory', 'Figurines Playmobil ToyStory', 15.99, 'Multicolore', 'Plastique', 0.8, 30, 1),
(3, 'Peluche Reine des neige', 'Reine des neige Disney', 49.99, 'Multicolore', 'Plastique', 1.5, 25, 1),
(4, 'Hasbro Game of throne', 'Jeux de société Hasbro Game of throne', 69.99, 'Multicolore', 'Carton', 4.0, 20, 1),
(5, 'Peluche Superman', 'Peluches Superman', 19.99, 'Multicolore', 'Plastique', 0.5, 100, 1),
(6, 'VTech', 'Jouets électroniques VTech', 14.99, 'Multicolore', 'Plastique', 0.7, 45, 1),
(7, 'Hot Wheels', 'Voitures miniatures Hot Wheels', 34.99, 'Multicolore', 'Plastique', 2.0, 30, 1),
(8, 'Barbie', 'Poupées Barbie', 59.99, 'Multicolore', 'Plastique', 4.5, 15, 1),
(9, 'Nerf', 'Jouets Nerf', 49.99, 'Multicolore', 'Plastique', 3.8, 10, 1),
(10, 'Mega Bloks', 'Jouets de construction Mega Bloks', 24.99, 'Multicolore', 'Plastique', 1.2, 40, 1),
(11, 'Brio', 'Trains miniatures Brio', 12.99, 'Multicolore', 'Bois', 0.5, 50, 1),
(12, 'Schleich', 'Figurines Schleich', 9.99, 'Multicolore', 'Plastique', 0.2, 100, 1),
(13, 'Melissa & Doug', 'Jouets Melissa & Doug', 14.99, 'Multicolore', 'Plastique', 0.3, 75, 1),
(14, 'Ravensburger', 'Puzzles Ravensburger', 19.99, 'Multicolore', 'Carton', 0.6, 80, 1),
(15, 'Spin Master', 'Jouets Spin Master', 39.99, 'Multicolore', 'Plastique', 1.5, 40, 1),
(16, 'LeapFrog', 'Jouets LeapFrog', 49.99, 'Multicolore', 'Plastique', 2.0, 30, 1),
(17, 'Little Tikes', 'Jouets Little Tikes', 59.99, 'Multicolore', 'Plastique', 4.0, 20, 1),
(18, 'Step2', 'Jouets Step2', 89.99, 'Multicolore', 'Plastique', 10.0, 15, 1),
(19, 'Hape', 'Jouets Hape', 19.99, 'Multicolore', 'Bois', 1.0, 50, 1),
(20, 'Janod', 'Jouets Janod', 29.99, 'Multicolore', 'Bois', 2.0, 60, 1),
(21, 'Ludorama Game', 'Jeu de société Ludorama', 29.99, 'Multicolore', 'Carton', 1.2, 50, 1),
(21, 'Karting Ludorama', 'Karting ludorama', 300, 'Multicolore', 'Plastique', 0.8, 30, 1),
(21, 'Ludorama figurine', 'Figurine Ludorama', 49.99, 'Multicolore', 'Carton', 1.5, 25, 1),
(21, 'Ludorama puzzle', 'Puzzle Ludorama', 69.99, 'Multicolore', 'Carton', 4.0, 20, 1),
(21, 'Ludorama peluche', 'Peluche Ludorama', 19.99, 'blanc', 'Plastique', 0.5, 100, 1),
(21, 'Ludorama strangeBox', 'Jeu de société Ludorama', 14.99, 'Multicolore', 'Carton', 0.7, 45, 1),
(21, 'Ludorama voiture', 'Voiture Ludorama', 34.99, 'Bleu', 'Plastique', 2.0, 30, 1),
(21, 'Console Ludorama', 'La console spéciale Ludorama', 250, 'Noir', 'Plastique', 12, 31, 1),
(21, 'Jeux Video Ludorama', 'Le jeux vidéo spécial ludorama', 35, 'bleu', 'DVD', 2, 38, 1),
(22, 'Elden Ring', 'Jeu vidéo Elden Ring', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Horizon Forbidden West', 'Jeu vidéo Horizon Forbidden West', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'God of War Ragnarok', 'Jeu vidéo God of War Ragnarok', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'The Legend of Zelda', 'Jeu vidéo The Legend of Zelda Breath of the Wild 2', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Final Fantasy XVI', 'Jeu vidéo Final Fantasy XVI', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Starfield', 'Jeu vidéo Starfield', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Fable', 'Jeu vidéo Fable', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'The Elder Scrolls VI', 'Jeu vidéo The Elder Scrolls VI', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Hogwarts Legacy', 'Jeu vidéo Hogwarts Legacy', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Gotham Knights', 'Jeu vidéo Gotham Knights', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Hellblade II Senua''s Saga', 'Jeu vidéo Hellblade II Senua''s Saga', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'The Lord of the Rings Gollum', 'Jeu vidéo The Lord of the Rings Gollum', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'The Witcher 4', 'Jeu vidéo The Witcher 4', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Silent Hill', 'Jeu vidéo Silent Hill', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Resident Evil 9', 'Jeu vidéo Resident Evil 9', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(22, 'Dead Space 4', 'Jeu vidéo Dead Space 4', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
(23, 'Peluche Batman', 'Peluche Batman', 19.99, 'Noir', 'Tissu', 0.5, 100, 1),
(23, 'Peluche Superman', 'Peluche Superman', 19.99, 'Bleu', 'Tissu', 0.5, 100, 1),
(23, 'Peluche Wonder Woman', 'Peluche Wonder Woman', 19.99, 'Rouge', 'Tissu', 0.5, 100, 1),
(2, 'Figurine Joker', 'Figurine Joker', 14.99, 'Vert', 'Plastique', 0.3, 100, 1),
(2, 'Figurine Harley Quinn', 'Figurine Harley Quinn', 14.99, 'Rouge', 'Plastique', 0.3, 100, 1),
(4, 'Jeu de société Mickey Mouse', 'Jeu de société Mickey Mouse', 24.99, 'Multicolore', 'Carton', 1.2, 100, 1),
(4, 'Jeu de société Disney', 'Jeu de société Disney', 24.99, 'Multicolore', 'Carton', 1.2, 100, 1),
(23, 'Peluche la reine des Neige', 'Peluche la reine des neige', 19.99, 'Multicolore', 'Tissu', 0.5, 100, 1),
(23, 'Peluche Elsa', 'Peluche Elsa', 19.99, 'Bleu', 'Tissu', 0.5, 100, 1),
(23, 'Peluche Anna', 'Peluche Anna', 19.99, 'Rouge', 'Tissu', 0.5, 100, 1),
(23,'Peluche Anakin', 'Peluche anakin prime', 12, 'Noir', 'Tissu', 1, 45, 1),
(23, 'Peluche dark Vador', 'Peluche Dark Vador', 12, 'Noir', 'Tissu', 1, 45, 1),
(23, 'Peluche Yoda', 'Peluche Yoda', 12, 'Vert', 'Tissu', 1, 45, 1),
(23, 'Peluche R2D2', 'Peluche R2D2', 12, 'Blanc','Tissu', 1, 45, 1),
(23, 'Peluche Daenerys Targarien', 'Peluche Daenerys Targarien', 12, 'Blanc','Tissu', 1, 45, 1),
(23, 'Peluche Jon Snow', 'Peluche Jon Snow', 12, 'Noir','Tissu', 1, 45, 1),
(23, 'Peluche Tyrion Lannister', 'Peluche Tyrion Lannister', 12, 'Noir','Tissu', 1, 45, 1),
(23, 'Peluche Drogon', 'Peluche Drogon, le dragon de daenerys', 12, 'Noir','Tissu', 1, 45, 1),
(1, 'Lego Game of Throne', 'Set lego game of throne 800 pièces', 150, 'Multicolore','Plastique', 1, 45, 1),
(1, 'Lego Toy Story', 'Set lego Toy Story 800 pièces', 150, 'Multicolore','Plastique', 1, 45, 1),
(23, 'Peluche Buz l''éclair', 'Peluche de Buz l''éclair', 13, 'blanc','Tissu', 1, 56, 1),
(2, 'Playmobil StarWars', 'Figurines Playmobil StarWars', 15.99, 'Multicolore','Plastique', 0.8, 30, 1),
(2, 'Playmobil Game of throne', 'Figurines Playmobil game of throne', 23.55, 'Multicolore', 'Plastique',2, 36, 1),
(3, 'Monopoly StarWars', 'Monopoly starWars', 29.99, 'Multicolore', 'Plastique',1.2, 50, 1),
(3, 'Monopoly Game of throne', 'Monopoly game of throne', 29.99, 'Multicolore','Plastique', 1.2, 50, 1),
(3, 'Monopoly La reine des neige', 'Monopoly la reine des neige', 29.99, 'Multicolore','Plastique', 1.2, 50, 1),
(3, 'Monopoly Disney', 'Monopoly Disney', 29.99, 'Multicolore','Plastique', 1.2, 50, 1);

INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
( 22, 'Call of Duty: Modern Warfare', 'Jeu intense et réaliste', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'Assassin''s Creed Valhalla', 'Jeu aventure dans univers des Vikings', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'Cyberpunk 2077', 'Jeu d''action et de rôle dans un monde futuriste', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'Red Dead Redemption 2', 'Jeu d''action et d''aventure dans le Far West', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'Resident Evil Village', 'Jeu d''horreur et de survie', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'The Last of Us Part II', 'Jeu d''horreur et d''aventure post-apocalyptique', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'Outlast', 'Jeu d''horreur et de survie en vue à la première personne', 59.99, 'Multicolore', 'DVD', 0.1, 100, 1),
( 22, 'Amnesia: Rebirth', 'Jeu d''horreur psychologique', 69.99, 'Multicolore', 'DVD', 0.1, 100, 1);

INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
( 19, 'Abacus en bois', 'Abacus éducatif en bois', 19.99, 'Multicolore', 'Bois', 0.8, 40, 1),
( 19, 'Alphabet en bois', 'Alphabet éducatif en bois', 14.99, 'Multicolore', 'Bois', 0.5, 50, 1),
( 5, 'Jeu de formes en bois', 'Jeu éducatif de formes en bois', 24.99, 'Multicolore', 'Bois', 1.0, 30, 1),
( 5, 'Puzzle en bois animaux', 'Puzzle classique en bois avec des animaux', 14.99, 'Multicolore', 'Bois', 0.5, 50, 1),
( 5, 'Puzzle en bois chiffres', 'Puzzle classique en bois avec des chiffres', 12.99, 'Multicolore', 'Bois', 0.5, 50, 1),
( 5, 'Puzzle en bois lettres', 'Puzzle classique en bois avec des lettres', 13.99, 'Multicolore', 'Bois', 0.5, 50, 1),
( 5, 'Puzzle en bois formes', 'Puzzle classique en bois avec des formes', 11.99, 'Multicolore', 'Bois', 0.5, 50, 1),
( 11, 'Figurine en bois cheval', 'Figurine en bois en forme de cheval', 9.99, 'Marron', 'Bois', 0.3, 50, 1),
( 20, 'Figurine en bois chien', 'Figurine en bois en forme de chien', 8.99, 'Blanc', 'Bois', 0.3, 50, 1),
( 11, 'Figurine en bois chat', 'Figurine en bois en forme de chat', 7.99, 'Noir', 'Bois', 0.3, 50, 1),
( 20, 'Figurine en bois éléphant', 'Figurine en bois en forme d''éléphant', 10.99, 'Gris', 'Bois', 0.3, 50, 1),
( 11, 'Figurine en bois lion', 'Figurine en bois en forme de lion', 11.99, 'Jaune', 'Bois', 0.3, 50, 1);

INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
(16, 'Robot éducatif Alpha', 'Robot interactif pour apprendre les bases de la programmation', 79.99, 'Blanc', 'Plastique et électronique', 1.5, 20, 1),
(16, 'Tablette KidPad', 'Tablette éducative pour enfants avec applications ludiques', 99.99, 'Bleu', 'Plastique et électronique', 0.8, 30, 1),
(16, 'Jeu interactif MathMaster', 'Jeu interactif pour apprendre les mathématiques', 49.99, 'Multicolore', 'Plastique et électronique', 0.5, 40, 1),
(16, 'Robot DinoBot', 'Robot dinosaure interactif avec télécommande', 89.99, 'Vert', 'Plastique et électronique', 2.0, 15, 1),
(16, 'Tablette JuniorTab', 'Tablette robuste pour enfants avec contrôle parental', 109.99, 'Rose', 'Plastique et électronique', 0.9, 25, 1),
(16, 'Voiture de police', 'Voiture de police miniature', 29.99, 'Bleu', 'Plastique', 1.0, 40, 1),
(16, 'Poupée princesse', 'Poupée en robe de princesse', 24.99, 'Rose', 'Plastique et tissu', 0.5, 50, 1),
(16, 'Nerf Blaster', 'Pistolet Nerf avec fléchettes', 34.99, 'Orange', 'Plastique', 1.2, 30, 1);

-- Insertion des produits pour la catégorie 'Voitures'
INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
(7, 'Voiture de sport', 'Voiture de sport rapide', 29.99, 'Rouge', 'Plastique', 0.5, 50, 1),
(7, 'Voiture de police', 'Voiture de police avec sirène', 24.99, 'Bleu', 'Plastique', 0.6, 40, 1),
(7, 'Voiture F1', 'Voiture de course télécommandée', 34.99, 'Jaune', 'Plastique', 0.7, 30, 1),
(7, 'Camion de Police', 'Camion de pompier avec échelle', 39.99, 'Rouge', 'Plastique', 1.0, 25, 1),
(7, 'Camion Tortue Ninja', 'Camion benne pour chantier', 29.99, 'Jaune', 'Plastique', 1.2, 20, 1),
(7, 'Camion de livraison', 'Camion de livraison avec portes ouvrantes', 34.99, 'Blanc', 'Plastique', 1.1, 30, 1),
(7, 'Avion de chasse', 'Avion de chasse rapide', 49.99, 'Gris', 'Plastique', 0.8, 20, 1),
(7, 'Avion de ligne', 'Avion de ligne avec passagers', 59.99, 'Blanc', 'Plastique', 1.5, 15, 1),
(7, 'Hélicoptère de secours', 'Hélicoptère de secours avec treuil', 44.99, 'Rouge', 'Plastique', 1.0, 25, 1);

-- Insertion des produits pour la catégorie 'Jeux de Jardin'
INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
(17, 'Balançoire en bois', 'Balançoire en bois pour jardin', 79.99, 'Marron', 'Bois', 10.0, 20, 1),
(17, 'Jeu de quilles géant', 'Jeu de quilles géant pour extérieur', 49.99, 'Multicolore', 'Plastique', 5.0, 30, 1),
(17, 'Tente de jeu', 'Tente de jeu pour enfants', 39.99, 'Bleu', 'Tissu', 3.0, 25, 1),
(17, 'Piscine gonflable', 'Piscine gonflable pour enfants', 59.99, 'Bleu', 'Plastique', 8.0, 15, 1),
(17, 'Piscine avec toboggan', 'Piscine avec toboggan intégré', 99.99, 'Multicolore', 'Plastique', 12.0, 10, 1),
(17, 'Piscine à balles', 'Piscine à balles pour enfants', 49.99, 'Multicolore', 'Plastique', 6.0, 20, 1),
(21, 'Toboggan en plastique', 'Toboggan en plastique pour jardin', 89.99, 'Rouge', 'Plastique', 15.0, 10, 1),
(21, 'Toboggan avec échelle', 'Toboggan avec échelle pour enfants', 119.99, 'Vert', 'Plastique', 18.0, 8, 1),
(21, 'Toboggan aquatique', 'Toboggan aquatique pour jardin', 129.99, 'Bleu', 'Plastique', 20.0, 5, 1);

-- Insertion des produits pour la catégorie 'Peinture'
INSERT INTO PRODUIT (IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK, DISPONIBLE) VALUES
(13, 'Kit de peinture acrylique', 'Kit complet de peinture acrylique', 29.99, 'Multicolore', 'Acrylique', 1.0, 50, 1),
(13, 'Chevalet de peinture', 'Chevalet en bois pour peinture', 49.99, 'Bois', 'Bois', 2.5, 30, 1),
(13, 'Palette de peinture', 'Palette de peinture avec 24 couleurs', 19.99, 'Multicolore', 'Acrylique', 0.8, 40, 1),
(13, 'Kit de bricolage en bois', 'Kit complet de bricolage en bois', 39.99, 'Bois', 'Bois', 1.5, 50, 1),
(13, 'Boîte à outils pour enfants', 'Boîte à outils en plastique pour enfants', 24.99, 'Multicolore', 'Plastique', 1.0, 40, 1),
(13, 'Kit de construction en bois', 'Kit de construction en bois pour enfants', 34.99, 'Bois', 'Bois', 2.0, 30, 1),
(13, 'Kit de pâte à modeler', 'Kit complet de pâte à modeler avec accessoires', 19.99, 'Multicolore', 'Pâte à modeler', 1.2, 50, 1),
(13, 'Pâte à modeler fluorescente', 'Pâte à modeler fluorescente pour enfants', 14.99, 'Multicolore', 'Pâte à modeler', 0.8, 40, 1),
(13, 'Pâte à modeler parfumée', 'Pâte à modeler parfumée avec différents parfums', 24.99, 'Multicolore', 'Pâte à modeler', 1.0, 30, 1),
(23, 'Peluches Woodie', 'Peluches Woodie de ToyStory', 24.99, 'Multicolore', 'Tissu', 1.0, 30, 1),
(23, 'Peluches Aliens', 'Peluches Aliens de ToyStory', 24.99, 'Multicolore', 'Tissu', 1.0, 30, 1);

-- Insertion des relations entre clients et adresses
INSERT INTO POSSEDERADR (IDADRESSE, IDCLIENT) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 14),
(15, 15),
(16, 16),
(17, 17),
(18, 18),
(19, 19),
(20, 20),
(21, 21),
(22, 22),
(23, 23),
(24, 24),
(25, 25),
(26, 26),
(27, 27),
(28, 28),
(29, 29),
(30, 30),
(31, 31),
(32, 32),
(33, 33),
(34, 34),
(35, 35),
(36, 36),
(37, 37),
(38, 38),
(39, 39),
(40, 40),
(41, 41),
(42, 42),
(43, 43),
(44, 44),
(45, 45),
(46, 46),
(47, 47),
(48, 48),
(49, 49),
(50, 50);

INSERT INTO CATPERE (IDCATEG_PERE, IDCATEG) VALUES
(11, 1), -- Enfants --> Jouets en Bois
(11, 2), -- Enfants --> Jeux de Société
(11, 3), -- Enfants --> Figurines
(11, 4), -- Enfants --> Puzzles
(11, 5), -- Enfants --> Peluches
(11, 6), -- Enfants --> Électronique
(11, 7), -- Enfants --> Véhicules
(11, 8), -- Enfants --> Extérieur
(11, 9), -- Enfants --> Créativité
(11, 10), -- Enfants --> Construction
(12, 1), -- Ado --> Jouets en Bois
(12, 2), -- Ado --> Jeux de Société
(12, 3), -- Ado --> Figurines
(12, 4), -- Ado --> Puzzles
(12, 6), -- Ado --> Électronique
(12, 7), -- Ado --> Véhicules
(12, 8), -- Ado --> Extérieur
(12, 9), -- Ado --> Créativité
(12, 10), -- Ado --> Construction
(13, 2), -- Jeune-adulte --> Jeux de Société
(13, 3), -- Jeune-adulte --> Figurines
(13, 4), -- Jeune-adulte --> Puzzles
(13, 6), -- Jeune-adulte --> Électronique
(13, 7), -- Jeune-adulte --> Véhicules
(13, 8), -- Jeune-adulte --> Extérieur
(13, 9), -- Jeune-adulte --> Créativité
(13, 10), -- Jeune-adulte --> Construction
(14, 2), -- Adulte --> Jeux de Société
(14, 4), -- Adulte --> Puzzles
(14, 6), -- Adulte --> Électronique
(14, 7), -- Adulte --> Véhicules
(14, 8), -- Adulte --> Extérieur
(14, 9), -- Adulte --> Créativité
(14, 10), -- Adulte --> Construction
(4, 15), -- Puzzles --> Puzzles en 3D
(4, 16), -- Puzzles --> Puzzles classiques
(10, 17), -- Construction --> Blocs de construction
(4, 18), -- Puzzles --> Modèles complexes
(10, 18), -- Construction --> Modèles complexes
(2, 19), -- Jeux de Société --> Jeux éducatifs
(9, 20), -- Créativité --> Jouets d'éveil
(2, 21), -- Jeux de Société --> Jeux de cartes
(2, 22), -- Jeux de Société --> Plateaux de jeux
(3, 23), -- Figurines --> Collection ToyStory
(3, 24), -- Figurines --> Collection StarWars
(3, 25), -- Figurines --> Collection Disney
(3, 26), -- Figurines --> Collection Game of throne
(3, 27), -- Figurines --> Collection DC
(11, 28), -- Enfants --> Jeux Vidéo
(12, 28), -- Ado --> Jeux Vidéo
(13, 28), -- Jeune-adulte --> Jeux Vidéo
(14, 28), -- Adulte --> Jeux Vidéo
(2, 23), -- Jeux de Société --> Collection ToyStory
(2, 24), -- Jeux de Société --> Collection StarWars
(2, 25), -- Jeux de Société --> Collection Disney
(2, 26), -- Jeux de Société --> Collection Game of throne
(2, 27), -- Jeux de Société --> Collection DC
(5, 23), -- Peluches --> Collection ToyStory
(5, 24), -- Peluches --> Collection StarWars
(5, 25), -- Peluches --> Collection Disney
(5, 26), -- Peluches --> Collection Game of throne
(5, 27), -- Peluches --> Collection DC
(10, 23), -- Construction --> Collection ToyStory
(10, 24), -- Construction --> Collection StarWars
(10, 25), -- Construction --> Collection Disney
(10, 26), -- Construction --> Collection Game of throne
(10, 27); -- Construction --> Collection DC
(28, 29), -- Jeux Vidéo --> Jeu Vidéo aventure
(28, 30), -- Jeux Vidéo --> Jeu Vidéo RPG
(28, 31), -- Jeux Vidéo --> Jeu Vidéo Action
(28, 34), -- Jeux Vidéo --> Jeu Vidéo Horreur

(1, 19), --  Jouets en Bois --> Jeux éducatifs
(1, 16), -- Jouets en Bois --> Puzzles classiques
(1, 35); -- Figurines en Bois --> Jouets en Bois
(6, 38), -- Électronique --> Robots
(6, 39), -- Électronique --> Tablettes
(6, 40), -- Électronique --> Jeux Interactifs
(7, 36), -- Véhicules --> Voitures
(7, 37), -- Véhicules --> Camions
(7, 41); -- Véhicules --> Avions

(8, 44), -- Extérieur --> Jeux de Jardin
(8, 45), -- Extérieur --> Piscines
(8, 46); -- Extérieur --> Toboggans

(9, 47), -- Créativité --> Peinture
(9, 48), -- Créativité --> Bricolage
(9, 49); -- Créativité --> Pâte à Modeler
(5, 50); -- Peluches --> Peluches Classique
(28, 48); -- Jeux Vidéo --> Console de jeux







-- Appartenance des produits aux catégories et sous-catégories
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(1, 19), -- Train en bois --> Jeux éducatifs
(2, 16), -- Puzzle animaux --> Puzzles classiques
(2, 4), -- Puzzle animaux --> Puzzles
(3, 7),  -- Voiture télécommandée --> Véhicules
(4, 20), -- Maison de poupée --> Jouets d'éveil
(5, 5),  -- Peluche éléphant --> Peluches
(6, 22), -- Jeu de mémoire --> Plateaux de jeux
(6, 2), -- Jeu de mémoire --> Jeux de Société
(7, 7),  -- Camion de pompier --> Véhicules
(8, 17), -- Cuisinière jouet --> Blocs de construction
(8, 10), -- Cuisinière jouet --> Construction
(9, 1),  -- Cheval à bascule --> Jouets en Bois
(9, 19), -- Cheval à bascule --> Jeux éducatifs
(10, 2), -- Jeu de société --> Jeux de Société
(11, 16), -- Puzzle en bois --> Puzzles classiques
(11, 4), -- Puzzle en bois --> Puzzles
(12, 21), -- Jeu de cartes --> Jeux de cartes
(13, 3),  -- Figurine dinosaure --> Figurines
(14, 5),  -- Peluche ours --> Peluches
(15, 7),  -- Voiture de course --> Véhicules
(16, 6),  -- Robot interactif --> Électronique
(17, 8),  -- Tricycle --> Extérieur
(18, 8),  -- Toboggan --> Extérieur
(19, 9),  -- Kit de peinture --> Créativité
(20, 10), -- Jeu de construction --> Construction
(21, 16), -- Casse-tête en bois --> Puzzles classiques
(21, 4), -- Casse-tête en bois --> Puzzles
(22, 2),  -- Jeu de société classique --> Jeux de Société
(23, 3), -- Figurine super-héros --> Figurines
(23, 47), -- Figurine super-héros --> Figurines classiques
(24, 2), -- Peluche licorne --> Peluches
(25, 3), -- Camion benne --> Véhicules
(26, 6),  -- Tablette éducative --> Électronique
(27, 8),  -- Balançoire --> Extérieur
(28, 8),  -- Piscine gonflable --> Extérieur
(29, 9),  -- Kit de bricolage --> Créativité
(30, 10), -- Jeu de dominos --> Construction
(31, 16), -- Labyrinthe en bois --> Puzzles classiques
(31, 4), -- Labyrinthe en bois --> Puzzles
(32, 22), -- Jeu de mémoire --> Plateaux de jeux
(32, 2), -- Jeu de mémoire --> Jeux de Société
(33, 3);  -- Figurine animal --> Figurines

(34, 5),  -- Peluche lapin --> Peluches
(34, 46), -- Peluche lapin --> Peluches classique
(35, 7),  -- Train électrique --> Véhicules
(36, 6),  -- Drone pour enfants --> Électronique
(37, 8),  -- Trottinette --> Extérieur
(38, 8),  -- Ballon sauteur --> Extérieur
(39, 9),  -- Kit de couture --> Créativité
(40, 10), -- Jeu de quilles --> Construction
(41, 19), -- Boîte à formes --> Jeux éducatifs
(42, 2),  -- Jeu de l'oie --> Jeux de Société
(54, 2), -- Hasbro Game of throne --> Jeux de Société
(43, 3),  -- Figurine chevalier --> Figurines
(44, 5),  -- Peluche chien --> Peluches
(45, 6),  -- Hélicoptère télécommandé --> Électronique
(46, 28),  -- Console de jeux --> Jeux vidéo
(47, 8),  -- Bateau gonflable --> Extérieur
(48, 8),  -- Cerf-volant --> Extérieur
(49, 8),  -- Kit de jardinage --> Extérieur
(50, 2),  -- Marionnettes en tissu --> Jeux de Société
(46, 48), -- Console de jeux --> Console de Jeux
(50, 19), -- Marionnettes en tissu --> Jeux éducatifs
(58, 3), -- Barbie --> Figurines
(58, 47), -- Barbie --> Figurines classiques
(60, 17), -- Mega Bloks --> Blocs de construction
(61, 7), -- Brio --> Véhicules
(61, 37), -- Brio --> Voitures
(62, 3), -- Schleich --> Figurines
(62, 47), -- Schleich --> Figurines classiques
(63, 20), -- Melissa & Doug --> Jouets d'éveil
(63, 9), -- Melissa & Doug --> Créativité
(64, 16), -- Ravensburger --> Puzzles classiques
(64, 4), -- Ravensburger --> Puzzles
(67, 7), -- Little Tikes --> Véhicules
(67, 37), -- Little Tikes --> Voitures
(69, 1), -- Hape --> Jouets en Bois
(78, 48), -- Console Ludorama --> Console de Jeux
(79, 29), -- Jeux Video Ludorama --> Jeu Vidéo aventure
(81, 29), -- Horizon Forbidden West --> Jeu Vidéo aventure
(123, 28), -- Call of Duty: Modern Warfare --> Jeu Vidéo
(124, 28), -- Assassin's Creed Valhalla --> Jeu Vidéo
(125, 28), -- Cyberpunk 2077 --> Jeu Vidéo
(126, 28), -- Red Dead Redemption 2 --> Jeu Vidéo
(127, 28), -- Resident Evil Village --> Jeu Vidéo
(128, 28), -- The Last of Us Part II --> Jeu Vidéo
(129, 28), -- Outlast --> Jeu Vidéo
(130, 28); -- Amnesia: Rebirth --> Jeu Vidéo
(59, 8), -- Nerf --> Extérieur
(59, 36); -- Nerf --> Jeux Interactifs





INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(10, 19), -- Jeu de société --> Jeux éducatifs
(22, 22), -- Jeu de société classique --> Plateaux de jeux
(42, 22), -- Jeu de l'oie --> Plateaux de jeux
(76, 22), -- Ludorama strangeBox --> Plateaux de jeux
(101, 22), -- Jeu de société Mickey Mouse --> Plateaux de jeux
(102, 22), -- Jeu de société Disney --> Plateaux de jeux
(119, 2), -- Monopoly StarWars --> Jeux de société
(120, 2), -- Monopoly Game of throne --> Jeux de société
(121, 2), -- Monopoly La reine des neige --> Jeux de société
(122, 2), -- Monopoly Disney --> Jeux de société
(101, 2), -- Jeu de société Mickey Mouse --> Jeux de société
(102, 2); -- Jeu de société Disney --> Jeux de société


-- Ajout des figurines à la catégorie Figurines Classique
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(13, 51), -- Figurine dinosaure --> Figurines Classique
(23, 51), -- Figurine super-héros --> Figurines Classique
(33, 51), -- Figurine animal --> Figurines Classique
(43, 51), -- Figurine chevalier --> Figurines Classique
(73, 51), -- Ludorama figurine --> Figurines Classique


-- Ajout des produits de la catégorie Construction dans les sous-catégories
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(20, 17), -- Jeu de construction --> Blocs de construction
(30, 17), -- Jeu de dominos --> Blocs de construction
(40, 17), -- Jeu de quilles --> Blocs de construction
(114, 18), -- Lego Game of Throne --> Modèles complexes
(115, 18); -- Lego Toy Story --> Modèles complexes

-- Ajout des peluches à la catégorie Peluches Classique
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(5, 50),  -- Peluche éléphant --> Peluches Classique
(14, 50), -- Peluche ours --> Peluches Classique
(24, 50), -- Peluche licorne --> Peluches Classique
(44, 50), -- Peluche chien --> Peluches Classique
(75, 50), -- Ludorama peluche --> Peluches Classique
(24, 5), -- Peluche licorne --> Peluches

(51, 24), -- Lego starWars --> Collection StarWars
(51, 10), -- Lego starWars --> Construction 
(51, 18), -- Lego starWars --> Modèles complexes
(52, 23), -- Playmobil ToyStory --> Collection ToyStory 
(52, 3), -- Playmobil ToyStory -->  Figurines 
(53, 25), -- Peluche Reine des neige --> Collection Disney
(53, 5), -- Peluche Reine des neige --> Peluches
(54, 26), -- Hasbro Game of throne --> Collection Game of throne
(55, 27), -- Peluche Superman --> Collection DC
(55, 5), -- Peluche Superman --> Peluches
(56, 6),  -- VTech --> Électronique
(57, 7),  -- Hot Wheels --> Véhicules
(58, 8),  -- Barbie --> Extérieur
(59, 9),  -- Nerf --> Créativité
(60, 10), -- Mega Bloks --> Construction
(61, 11), -- Brio --> Enfants
(62, 12), -- Schleich --> Ado
(63, 13), -- Melissa & Doug --> Jeune-adulte
(64, 14), -- Ravensburger --> Adulte
(65, 15), -- Spin Master --> Puzzles en 3D
(65, 4), -- Spin Master --> Puzzles
(66, 16), -- LeapFrog --> Puzzles classiques
(66, 4), -- LeapFrog --> Puzzles
(67, 17), -- Little Tikes --> Blocs de construction
(68, 18), -- Step2 --> Modèles complexes
(68, 4), -- Step2 --> Puzzles
(69, 19), -- Hape --> Jeux éducatifs
(70, 20), -- Janod --> Jouets d'éveil
(71, 21), -- Ludorama Game --> Jeux de cartes
(72, 7),  -- Karting Ludorama --> Véhicules
(73, 3),  -- Ludorama figurine --> Figurines
(74, 4),  -- Ludorama puzzle --> Puzzles
(74, 18),  -- Ludorama puzzle --> Modèles complexes
(75, 5),  -- Ludorama peluche --> Peluches
(76, 2),  -- Ludorama strangeBox --> Jeux de Société
(77, 7),  -- Ludorama voiture --> Véhicules
(78, 28), -- Console Ludorama --> Jeux Vidéo
(79, 28), -- Jeux Video Ludorama --> Jeux Vidéo
(80, 28), -- Elden Ring --> Jeux Vidéo
(81, 28), -- Horizon Forbidden West --> Jeux Vidéo
(82, 28), -- God of War Ragnarok --> Jeux Vidéo
(83, 28), -- The Legend of Zelda Breath of the Wild 2 --> Jeux Vidéo
(84, 28), -- Final Fantasy XVI --> Jeux Vidéo
(85, 28), -- Starfield --> Jeux Vidéo
(86, 28), -- Fable --> Jeux Vidéo
(87, 28), -- The Elder Scrolls VI --> Jeux Vidéo
(88, 28), -- Hogwarts Legacy --> Jeux Vidéo
(89, 28), -- Gotham Knights --> Jeux Vidéo
(90, 28), -- Hellblade II Senua's Saga --> Jeux Vidéo
(91, 28), -- The Lord of the Rings Gollum --> Jeux Vidéo
(92, 28), -- The Witcher 4 --> Jeux Vidéo
(93, 28), -- Silent Hill --> Jeux Vidéo
(94, 28), -- Resident Evil 9 --> Jeux Vidéo
(95, 28), -- Dead Space 4 --> Jeux Vidéo
(96, 27), -- Peluche Batman --> Collection DC
(97, 27), -- Peluche Superman --> Collection DC
(98, 27), -- Peluche Wonder Woman --> Collection DC
(96, 5), -- Peluche Batman --> Peluches
(97, 5), -- Peluche Superman --> Peluches
(98, 5), -- Peluche Wonder --> Peluches
(99, 27), -- Figurine Joker --> Collection DC
(100, 27), -- Figurine Harley Quinn --> Collection DC
(99, 3), -- Figurine Joker --> Figurines
(100, 3), -- Figurine Harley Quinn --> Figurines

(101, 25), -- Jeu de société Mickey Mouse --> Collection Disney
(102, 25), -- Jeu de société Disney --> Collection Disney
(103, 25), -- Peluche la reine des Neige --> Collection Disney
(104, 25), -- Peluche Elsa --> Collection Disney
(105, 25), -- Peluche Anna --> Collection Disney
(103, 5), -- Peluche la reine des Neige --> Peluches
(104, 5), -- Peluche Elsa --> Peluches
(105, 5), -- Peluche Anna --> Peluches
(106, 24), -- Peluche Anakin --> Collection StarWars
(107, 24), -- Peluche Dark Vador --> Collection StarWars
(108, 24), -- Peluche Yoda --> Collection StarWars
(109, 24), -- Peluche R2D2 --> Collection StarWars
(110, 26), -- Peluche Daenerys Targarien --> Collection Game of throne
(111, 26), -- Peluche Jon Snow --> Collection Game of throne
(112, 26), -- Peluche Tyrion Lannister --> Collection Game of throne
(113, 26), -- Peluche Drogon --> Collection Game of throne
(114, 26), -- Lego Game of Throne --> Collection Game of throne
(115, 23), -- Lego Toy Story --> Collection ToyStory
(116, 23), -- Peluche Buz l'éclair --> Collection ToyStory
(117, 24), -- Playmobil StarWars --> Collection StarWars
(118, 26), -- Playmobil Game of throne --> Collection Game of throne
(119, 24), -- Monopoly StarWars --> Collection StarWars
(120, 26), -- Monopoly Game of throne --> Collection Game of throne
(121, 25), -- Monopoly La reine des neige --> Collection Disney
(122, 25); -- Monopoly Disney --> Collection Disney






(106, 5),  -- Peluche Anakin --> Peluches
(107, 5),  -- Peluche Dark Vador --> Peluches
(108, 5),  -- Peluche Yoda --> Peluches
(109, 5),  -- Peluche R2D2 --> Peluches
(110, 5),  -- Peluche Daenerys Targarien --> Peluches
(111, 5),  -- Peluche Jon Snow --> Peluches
(112, 5),  -- Peluche Tyrion Lannister --> Peluches
(113, 5),  -- Peluche Drogon --> Peluches
(114, 10), -- Lego Game of Throne --> Construction
(115, 10), -- Lego Toy Story --> Construction
(116, 5),  -- Peluche Buz l'éclair --> Peluches
(117, 3),  -- Playmobil StarWars --> Figurines
(118, 3),  -- Playmobil Game of throne --> Figurines
(119, 22),  -- Monopoly StarWars --> Jeux de plateau
(120, 22),  -- Monopoly Game of throne --> Jeux de plateau
(121, 22),  -- Monopoly La reine des neige --> Jeux de plateau
(122, 22),  -- Monopoly Disney --> Jeux de plateau
(80, 29), -- Elden Ring --> Jeu Vidéo aventure
(21, 29), -- Horizon Forbidden West --> Jeu Vidéo aventure
(82, 30), -- God of War Ragnarok --> Jeu Vidéo RPG
(83, 29), -- The Legend of Zelda Breath of the Wild 2 --> Jeu Vidéo aventure
(84, 30), -- Final Fantasy XVI --> Jeu Vidéo RPG
(85, 30), -- Starfield --> Jeu Vidéo RPG
(86, 30), -- Fable --> Jeu Vidéo RPG
(87, 30), -- The Elder Scrolls VI --> Jeu Vidéo RPG
(88, 29), -- Hogwarts Legacy --> Jeu Vidéo aventure
(89, 31), -- Gotham Knights --> Jeu Vidéo Action
(90, 31), -- Hellblade II Senua's Saga --> Jeu Vidéo Action
(91, 29), -- The Lord of the Rings Gollum --> Jeu Vidéo aventure
(92, 30), -- The Witcher 4 --> Jeu Vidéo RPG
(45, 7), -- Hélicoptère télécommandé --> Véhicules
(45, 39), -- Hélicoptère télécommandé --> Avions






(93, 32), -- Silent Hill --> Jeu Vidéo Horreur
(94, 32), -- Resident Evil 9 --> Jeu Vidéo Horreur
(95, 32), -- Dead Space 4 --> Jeu Vidéo Horreur
(123, 31), -- Call of Duty: Modern Warfare --> Jeu Vidéo Action
(124, 31), -- Assassin's Creed Valhalla --> Jeu Vidéo Action
(125, 31), -- Cyberpunk 2077 --> Jeu Vidéo Action
(126, 31), -- Red Dead Redemption 2 --> Jeu Vidéo Action
(127, 32), -- Resident Evil Village --> Jeu Vidéo Horreur
(128, 32), -- The Last of Us Part II --> Jeu Vidéo Horreur
(129, 32), -- Outlast --> Jeu Vidéo Horreur
(130, 32); -- Amnesia: Rebirth --> Jeu Vidéo Horreur


(1, 1),  -- Train en bois --> Jouets en Bois
(2, 1),  -- Puzzle animaux --> Jouets en Bois
(4, 1),  -- Maison de poupée --> Jouets en Bois
(11, 1), -- Puzzle en bois --> Jouets en Bois
(21, 1), -- Casse-tête en bois --> Jouets en Bois
(31, 1), -- Labyrinthe en bois --> Jouets en Bois
(41, 1), -- Boîte à formes --> Jouets en Bois
(131, 1), -- Abacus en bois --> Jouets en Bois
(131, 19), -- Abacus en bois --> Jeux éducatifs
(132, 1), -- Alphabet en bois --> Jouets en Bois
(132, 19), -- Alphabet en bois --> Jeux éducatifs
(133, 1), -- Jeu de formes en bois --> Jouets en Bois
(133, 19), -- Jeu de formes en bois --> Jeux éducatifs
(134, 1), -- Puzzle en bois animaux --> Jouets en Bois
(134, 16), -- Puzzle en bois animaux --> Puzzles classiques
(135, 1), -- Puzzle en bois chiffres --> Jouets en Bois
(135, 16), -- Puzzle en bois chiffres --> Puzzles classiques
(136, 1), -- Puzzle en bois lettres --> Jouets en Bois
(136, 16), -- Puzzle en bois lettres --> Puzzles classiques
(137, 1), -- Puzzle en bois formes --> Jouets en Bois
(137, 16), -- Puzzle en bois formes --> Puzzles classiques
(138, 1), -- Figurine en bois cheval --> Jouets en Bois
(138, 33), -- Figurine en bois cheval --> Figurines en Bois
(139, 1), -- Figurine en bois chien --> Jouets en Bois
(139, 33), -- Figurine en bois chien --> Figurines en Bois
(140, 1), -- Figurine en bois chat --> Jouets en Bois
(140, 33), -- Figurine en bois chat --> Figurines en Bois
(141, 1), -- Figurine en bois éléphant --> Jouets en Bois
(141, 33), -- Figurine en bois éléphant --> Figurines en Bois
(142, 1), -- Figurine en bois lion --> Jouets en Bois
(142, 33), -- Figurine en bois lion --> Figurines en Bois
(19, 43), -- Kit de peinture --> Peinture
(29, 44), -- Kit de bricolage --> Bricolage
(39, 44); -- Kit de couture --> Bricolage

(138, 3), -- Figurine en bois cheval --> Figurines
(139, 3), -- Figurine en bois chien --> Figurines
(140, 3), -- Figurine en bois chat --> Figurines
(141, 3), -- Figurine en bois éléphant --> Figurines
(142, 3); -- Figurine en bois lion --> Figurines




INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(16, 34), -- Robot interactif --> Robots
(26, 35), -- Tablette éducative --> Tablettes
(36, 36), -- Drone pour enfants --> Jeux Interactifs
(45, 34), -- Hélicoptère télécommandé --> Robots
(56, 36), -- VTech --> Jeux Interactifs
(148, 34), -- Voiture de police --> Robots
(149, 36), -- Poupée princesse --> Jeux Interactifs
(150, 36), -- Nerf Blaster --> Jeux Interactifs
(143, 6), -- Robot éducatif Alpha --> Électronique
(143, 34), -- Robot éducatif Alpha --> Robots
(144, 6), -- Tablette KidPad --> Électronique
(144, 35), -- Tablette KidPad --> Tablettes
(145, 6), -- Jeu interactif MathMaster --> Électronique
(145, 36), -- Jeu interactif MathMaster --> Jeux Interactifs
(146, 6), -- Robot DinoBot --> Électronique
(146, 34), -- Robot DinoBot --> Robots
(147, 6), -- Tablette JuniorTab --> Électronique
(147, 35), -- Tablette JuniorTab --> Tablettes
(148, 6), -- Voiture de police --> Électronique
(149, 6), -- Poupée princesse --> Électronique
(150, 6); -- Nerf Blaster --> Électronique


-- Appartenance des nouveaux produits aux catégories
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(151, 37), -- Voiture de sport --> Voitures
(152, 37), -- Voiture de police --> Voitures
(153, 37), -- Voiture F1 --> Voitures
(154, 38), -- Camion de Police --> Camions
(155, 38), -- Camion Tortue Ninja --> Camions
(156, 38), -- Camion de livraison --> Camions
(157, 39), -- Avion de chasse --> Avions
(158, 39), -- Avion de ligne --> Avions
(159, 39), -- Hélicoptère de secours --> Avions
(151, 7), -- Voiture de sport --> Véhicules
(152, 7), -- Voiture de police --> Véhicules
(153, 7), -- Voiture F1 --> Véhicules
(154, 7), -- Camion de Police --> Véhicules
(155, 7), -- Camion Tortue Ninja --> Véhicules
(156, 7), -- Camion de livraison --> Véhicules
(157, 7), -- Avion de chasse --> Véhicules
(158, 7), -- Avion de ligne --> Véhicules
(159, 7), -- Hélicoptère de secours --> Véhicules
(3, 37),  -- Voiture télécommandée --> Voitures
(7, 38),  -- Camion de pompier --> Camions
(15, 37), -- Voiture de course --> Voitures
(25, 38), -- Camion benne --> Camions
(35, 37), -- Train électrique --> Voitures
(57, 37), -- Hot Wheels --> Voitures
(72, 37), -- Karting Ludorama --> Voitures
(77, 37), -- Ludorama voiture --> Voitures
(160, 40), -- Balançoire en bois --> Jeux de Jardin
(161, 40), -- Jeu de quilles géant --> Jeux de Jardin
(162, 40), -- Tente de jeu --> Jeux de Jardin
(163, 41), -- Piscine gonflable --> Piscines
(164, 41), -- Piscine avec toboggan --> Piscines
(165, 41), -- Piscine à balles --> Piscines
(166, 42), -- Toboggan en plastique --> Toboggans
(167, 42), -- Toboggan avec échelle --> Toboggans
(168, 42), -- Toboggan aquatique --> Toboggans
(160, 8), -- Balançoire en bois --> Extérieur
(161, 8), -- Jeu de quilles géant --> Extérieur
(162, 8), -- Tente de jeu --> Extérieur
(163, 8), -- Piscine gonflable --> Extérieur
(164, 8), -- Piscine avec toboggan --> Extérieur
(165, 8), -- Piscine à balles --> Extérieur
(166, 8), -- Toboggan en plastique --> Extérieur
(167, 8), -- Toboggan avec échelle --> Extérieur
(168, 8), -- Toboggan aquatique --> Extérieur
(17, 40), -- Tricycle --> Jeux de Jardin
(18, 42), -- Toboggan --> Toboggans
(27, 40), -- Balançoire --> Jeux de Jardin
(28, 41), -- Piscine gonflable --> Piscines
(37, 40), -- Trottinette --> Jeux de Jardin
(38, 40), -- Ballon sauteur --> Jeux de Jardin
(47, 41), -- Bateau gonflable --> Piscines
(48, 40), -- Cerf-volant --> Jeux de Jardin
(49, 40); -- Kit de jardinage --> Jeux de Jardin


-- Appartenance des nouveaux produits aux catégories
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(169, 43), -- Kit de peinture acrylique --> Peinture
(169, 9),  -- Kit de peinture acrylique --> Créativité
(170, 43), -- Chevalet de peinture --> Peinture
(170, 9),  -- Chevalet de peinture --> Créativité
(171, 43), -- Palette de peinture --> Peinture
(171, 9),  -- Palette de peinture --> Créativité
(172, 44), -- Kit de bricolage en bois --> Bricolage
(172, 9),  -- Kit de bricolage en bois --> Créativité
(173, 44), -- Boîte à outils pour enfants --> Bricolage
(173, 9),  -- Boîte à outils pour enfants --> Créativité
(174, 44), -- Kit de construction en bois --> Bricolage
(174, 9),  -- Kit de construction en bois --> Créativité
(175, 45), -- Kit de pâte à modeler --> Pâte à Modeler
(175, 9),  -- Kit de pâte à modeler --> Créativité
(176, 45), -- Pâte à modeler fluorescente --> Pâte à Modeler
(176, 9),  -- Pâte à modeler fluorescente --> Créativité
(177, 45), -- Pâte à modeler parfumée --> Pâte à Modeler
(177, 9),  -- Pâte à modeler parfumée --> Créativité
(178, 23), -- Peluches Woodie --> Collection ToyStory
(179, 23), -- Peluches Aliens --> Collection ToyStory
(178, 5),  -- Peluches Woodie --> Peluches
(179, 5);  -- Peluches Aliens --> Peluches





-- Insertion de commandes avec détails
INSERT INTO COMMANDE (IDCLIENT, IDTRANSPORTEUR, NUMCB, IDADRESSE, TYPEREGLEMENT, DATECOMMANDE, STATUTLIVRAISON, CODESUIVI) VALUES
(1, 1, '1234567890123456', 1, 'CB', '2024-02-01', 'En cours', 'ABC123456789'),
(2, 2, '2345678901234567', 2, 'CB', '2024-02-02', 'Livrée', 'DEF123456789'),
(3, 3, '3456789012345678', 3, 'CB', '2024-02-03', 'En cours', 'GHI123456789'),
(4, 4, '4567890123456789', 4, 'CB', '2024-02-04', 'En cours', 'JKL123456789'),
(5, 5, '5678901234567890', 5, 'CB', '2024-02-05', 'Livrée', 'MNO123456789'),
(6, 1, '6789012345678901', 6, 'CB', '2024-02-06', 'En cours', 'PQR123456789'),
(7, 2, '7890123456789012', 7, 'CB', '2024-02-07', 'En cours', 'STU123456789'),
(8, 3, '8901234567890123', 8, 'CB', '2024-02-08', 'Livrée', 'VWX123456789'),
(9, 4, '9012345678901234', 9, 'CB', '2024-02-09', 'En cours', 'YZA123456789'),
( 10, 5, '0123456789012345', 10, 'CB', '2024-02-10', 'En cours', 'BCD123456789'),
( 11, 1, '1234567890123456', 11, 'CB', '2024-02-11', 'Livrée', 'EFG123456789'),
( 12, 2, '2345678901234567', 12, 'CB', '2024-02-12', 'En cours', 'HIJ123456789'),
( 13, 3, '3456789012345678', 13, 'CB', '2024-02-13', 'En cours', 'KLM123456789'),
( 14, 4, '4567890123456789', 14, 'CB', '2024-02-14', 'Livrée', 'NOP123456789'),
( 15, 5, '5678901234567890', 15, 'CB', '2024-02-15', 'En cours', 'QRS123456789'),
( 16, 1, '6789012345678901', 16, 'CB', '2024-02-16', 'En cours', 'TUV123456789'),
( 17, 2, '7890123456789012', 17, 'CB', '2024-02-17', 'Livrée', 'WXY123456789'),
( 18, 3, '8901234567890123', 18, 'CB', '2024-02-18', 'En cours', 'ZAB123456789'),
( 19, 4, '9012345678901234', 19, 'CB', '2024-02-19', 'En cours', 'CDE123456789'),
( 20, 5, '0123456789012345', 20, 'CB', '2024-02-20', 'Livrée', 'FGH123456789'),
( 21, 1, '1234567890123456', 21, 'CB', '2024-02-21', 'En cours', 'IJK123456789'),
( 22, 2, '2345678901234567', 22, 'CB', '2024-02-22', 'Livrée', 'LMN123456789'),
( 23, 3, '3456789012345678', 23, 'CB', '2024-02-23', 'En cours', 'OPQ123456789'),
( 24, 4, '4567890123456789', 24, 'CB', '2024-02-24', 'En cours', 'RST123456789'),
( 25, 5, '5678901234567890', 25, 'CB', '2024-02-25', 'Livrée', 'UVW123456789'),
( 26, 1, '6789012345678901', 26, 'CB', '2024-02-26', 'En cours', 'XYZ123456789'),
( 27, 2, '7890123456789012', 27, 'CB', '2024-02-27', 'En cours', 'ABC123456789'),
( 28, 3, '8901234567890123', 28, 'CB', '2024-02-28', 'Livrée', 'DEF123456789'),
( 29, 4, '9012345678901234', 29, 'CB', '2024-02-29', 'En cours', 'GHI123456789'),
( 30, 5, '0123456789012345', 30, 'CB', '2024-03-01', 'En cours', 'JKL123456789'),
( 31, 1, '1234567890123456', 31, 'CB', '2024-03-02', 'Livrée', 'MNO123456789'),
( 32, 2, '2345678901234567', 32, 'CB', '2024-03-03', 'En cours', 'PQR123456789'),
( 33, 3, '3456789012345678', 33, 'CB', '2024-03-04', 'En cours', 'STU123456789'),
( 34, 4, '4567890123456789', 34, 'CB', '2024-03-05', 'Livrée', 'VWX123456789'),
( 35, 5, '5678901234567890', 35, 'CB', '2024-03-06', 'En cours', 'YZA123456789'),
( 36, 1, '6789012345678901', 36, 'CB', '2024-03-07', 'En cours', 'BCD123456789'),
( 37, 2, '7890123456789012', 37, 'CB', '2024-03-08', 'Livrée', 'EFG123456789'),
( 38, 3, '8901234567890123', 38, 'CB', '2024-03-09', 'En cours', 'HIJ123456789'),
( 39, 4, '9012345678901234', 39, 'CB', '2024-03-10', 'En cours', 'KLM123456789'),
( 40, 5, '0123456789012345', 40, 'CB', '2024-03-11', 'Livrée', 'NOP123456789'),
( 41, 1, '1234567890123456', 41, 'CB', '2024-03-12', 'En cours', 'QRS123456789'),
( 42, 2, '2345678901234567', 42, 'CB', '2024-03-13', 'En cours', 'TUV123456789'),
( 43, 3, '3456789012345678', 43, 'CB', '2024-03-14', 'Livrée', 'WXY123456789'),
( 44, 4, '4567890123456789', 44, 'CB', '2024-03-15', 'En cours', 'ZAB123456789'),
( 45, 5, '5678901234567890', 45, 'CB', '2024-03-16', 'En cours', 'CDE123456789'),
( 46, 1, '6789012345678901', 46, 'CB', '2024-03-17', 'Livrée', 'FGH123456789'),
( 47, 2, '7890123456789012', 47, 'CB', '2024-03-18', 'En cours', 'IJK123456789'),
( 48, 3, '8901234567890123', 48, 'CB', '2024-03-19', 'En cours', 'LMN123456789'),
( 49, 4, '9012345678901234', 49, 'CB', '2024-03-20', 'Livrée', 'OPQ123456789'),
( 50, 5, '0123456789012345', 50, 'CB', '2024-03-21', 'En cours', 'RST123456789'),
( 1, 1, '1234567890123456', 1, 'CB', '2024-03-22', 'En cours', 'UVW123456789'),
( 2, 2, '2345678901234567', 2, 'CB', '2024-03-23', 'Livrée', 'XYZ123456789'),
( 3, 3, '3456789012345678', 3, 'CB', '2024-03-24', 'En cours', 'ABC123456789'),
( 4, 4, '4567890123456789', 4, 'CB', '2024-03-25', 'En cours', 'DEF123456789'),
( 5, 5, '5678901234567890', 5, 'CB', '2024-03-26', 'Livrée', 'GHI123456789'),
( 6, 1, '6789012345678901', 6, 'CB', '2024-03-27', 'En cours', 'JKL123456789'),
( 7, 2, '7890123456789012', 7, 'CB', '2024-03-28', 'En cours', 'MNO123456789'),
( 8, 3, '8901234567890123', 8, 'CB', '2024-03-29', 'Livrée', 'PQR123456789'),
( 9, 4, '9012345678901234', 9, 'CB', '2024-03-30', 'En cours', 'STU123456789'),
( 10, 5, '0123456789012345', 10, 'CB', '2024-03-31', 'En cours', 'VWX123456789'),
( 11, 1, '1234567890123456', 11, 'CB', '2024-04-01', 'Livrée', 'YZA123456789'),
( 12, 2, '2345678901234567', 12, 'CB', '2024-04-02', 'En cours', 'BCD123456789'),
( 13, 3, '3456789012345678', 13, 'CB', '2024-04-03', 'En cours', 'EFG123456789'),
( 14, 4, '4567890123456789', 14, 'CB', '2024-04-04', 'Livrée', 'HIJ123456789'),
( 15, 5, '5678901234567890', 15, 'CB', '2024-04-05', 'En cours', 'KLM123456789'),
( 16, 1, '6789012345678901', 16, 'CB', '2024-04-06', 'En cours', 'NOP123456789'),
( 17, 2, '7890123456789012', 17, 'CB', '2024-04-07', 'Livrée', 'QRS123456789'),
( 18, 3, '8901234567890123', 18, 'CB', '2024-04-08', 'En cours', 'TUV123456789'),
( 19, 4, '9012345678901234', 19, 'CB', '2024-04-09', 'En cours', 'WXY123456789'),
( 20, 5, '0123456789012345', 20, 'CB', '2024-04-10', 'Livrée', 'ZAB123456789'),
( 21, 1, '1234567890123456', 21, 'CB', '2024-04-11', 'En cours', 'CDE123456789'),
( 22, 2, '2345678901234567', 22, 'CB', '2024-04-12', 'Livrée', 'FGH123456789'),
( 23, 3, '3456789012345678', 23, 'CB', '2024-04-13', 'En cours', 'IJK123456789'),
( 24, 4, '4567890123456789', 24, 'CB', '2024-04-14', 'En cours', 'LMN123456789'),
( 25, 5, '5678901234567890', 25, 'CB', '2024-04-15', 'Livrée', 'OPQ123456789'),
( 26, 1, '6789012345678901', 26, 'CB', '2024-04-16', 'En cours', 'RST123456789'),
( 27, 2, '7890123456789012', 27, 'CB', '2024-04-17', 'En cours', 'UVW123456789'),
( 28, 3, '8901234567890123', 28, 'CB', '2024-04-18', 'Livrée', 'XYZ123456789'),
( 29, 4, '9012345678901234', 29, 'CB', '2024-04-19', 'En cours', 'ABC123456789'),
( 30, 5, '0123456789012345', 30, 'CB', '2024-04-20', 'En cours', 'DEF123456789'),
( 31, 1, '1234567890123456', 31, 'CB', '2024-04-21', 'Livrée', 'GHI123456789'),
( 32, 2, '2345678901234567', 32, 'CB', '2024-04-22', 'En cours', 'JKL123456789'),
( 33, 3, '3456789012345678', 33, 'CB', '2024-04-23', 'En cours', 'MNO123456789'),
( 34, 4, '4567890123456789', 34, 'CB', '2024-04-24', 'Livrée', 'PQR123456789'),
( 35, 5, '5678901234567890', 35, 'CB', '2024-04-25', 'En cours', 'STU123456789'),
( 36, 1, '6789012345678901', 36, 'CB', '2024-04-26', 'En cours', 'VWX123456789'),
( 37, 2, '7890123456789012', 37, 'CB', '2024-04-27', 'Livrée', 'YZA123456789'),
( 38, 3, '8901234567890123', 38, 'CB', '2024-04-28', 'En cours', 'BCD123456789'),
( 39, 4, '9012345678901234', 39, 'CB', '2024-04-29', 'En cours', 'EFG123456789'),
( 40, 5, '0123456789012345', 40, 'CB', '2024-04-30', 'Livrée', 'HIJ123456789'),
( 41, 1, '1234567890123456', 41, 'CB', '2024-05-01', 'En cours', 'KLM123456789'),
( 42, 2, '2345678901234567', 42, 'CB', '2024-05-02', 'Livrée', 'NOP123456789'),
( 43, 3, '3456789012345678', 43, 'CB', '2024-05-03', 'En cours', 'QRS123456789'),
( 44, 4, '4567890123456789', 44, 'CB', '2024-05-04', 'En cours', 'TUV123456789'),
( 45, 5, '5678901234567890', 45, 'CB', '2024-05-05', 'Livrée', 'WXY123456789'),
( 46, 1, '6789012345678901', 46, 'CB', '2024-05-06', 'En cours', 'ZAB123456789'),
( 47, 2, '7890123456789012', 47, 'CB', '2024-05-07', 'En cours', 'CDE123456789'),
( 48, 3, '8901234567890123', 48, 'CB', '2024-05-08', 'Livrée', 'FGH123456789'),
( 49, 4, '9012345678901234', 49, 'CB', '2024-05-09', 'En cours', 'IJK123456789'),
( 50, 5, '0123456789012345', 50, 'CB', '2024-05-10', 'En cours', 'LMN123456789');

-- Insertion d'avis
INSERT INTO AVIS (IDCLIENT, IDPROD, DESCAVIS) VALUES
(1, 1, 'Excellent produit, mon fils adore !'),
(2, 2, 'Bon rapport qualité-prix.'),
(3, 3, 'Très amusant, mes enfants jouent avec tous les jours.'),
(4, 4, 'Bien conçu et solide.'),
(5, 5, 'Ma fille ne le quitte plus.'),
(6, 6, 'Jeu éducatif très intéressant.'),
(7, 7, 'Mon fils adore ce camion de pompier.'),
(8, 8, 'Parfait pour les petits chefs en herbe.'),
(9, 9, 'Très joli cheval à bascule.'),
(10, 10, 'Jeu éducatif et amusant.'),
(11, 11, 'Puzzle de bonne qualité.'),
(12, 12, 'Jeu de cartes très divertissant.'),
(13, 13, 'Mon fils adore les dinosaures, il est ravi.'),
(14, 14, 'Peluche très douce et mignonne.'),
(15, 15, 'Voiture de course très rapide.'),
(16, 16, 'Robot interactif très éducatif.'),
(17, 17, 'Tricycle solide et stable.'),
(18, 18, 'Toboggan parfait pour le jardin.'),
(19, 19, 'Kit de peinture très complet.'),
(20, 20, 'Blocs de construction de bonne qualité.');

-- Regroupement de produits
INSERT INTO ASSOPACK (IDPROD, IDPACK) VALUES
-- Pack bois
(4, 5), -- Maison de poupée dans pack bois
(11, 5), -- Puzzle en bois dans pack bois
(21, 5), -- Casse-tête en bois dans pack bois
(31, 5), -- Labyrinthe en bois dans pack bois
(41, 5), -- Boîte à formes dans pack bois
(131, 5), -- Abacus en bois dans pack bois
(132, 5), -- Alphabet en bois dans pack bois
(133, 5), -- Jeu de formes en bois dans pack bois
(134, 5), -- Puzzle en bois animaux dans pack bois
(135, 5), -- Puzzle en bois chiffres dans pack bois
(136, 5), -- Puzzle en bois lettres dans pack bois
(137, 5), -- Puzzle en bois formes dans pack bois


-- Pack poupées
(4, 6), -- Maison de poupée dans pack poupées
(24, 6), -- Peluche licorne dans pack poupées
(44, 6), -- Peluche chien dans pack poupées
(75, 6), -- Ludorama peluche dans pack poupées
(178, 6), -- Peluches Woodie dans pack poupées
(179, 6), -- Peluches Aliens dans pack poupées

-- Pack aventure
(17, 7), -- Tricycle dans pack aventure
(18, 7), -- Toboggan dans pack aventure
(23, 7), -- Figurine super-héros dans pack aventure
(47, 7), -- Bateau gonflable dans pack aventure
(48, 7), -- Cerf-volant dans pack aventure
(49, 7), -- Kit de jardinage dans pack aventure

-- Pack créatif
(19, 2), -- Kit de peinture dans pack créatif
(29, 2), -- Kit de bricolage dans pack créatif
(39, 2), -- Kit de couture dans pack créatif
(175, 2), -- Kit de pâte à modeler dans pack créatif
(176, 2), -- Pâte à modeler fluorescente dans pack créatif
(177, 2), -- Pâte à modeler parfumée dans pack créatif

-- Pack voitures
(3, 3), -- Voiture télécommandée dans pack voitures
(7, 3), -- Camion de pompier dans pack voitures
(15, 3), -- Voiture de course dans pack voitures
(25, 3), -- Camion benne dans pack voitures
(151, 3), -- Voiture de sport dans pack voitures
(152, 3), -- Voiture de police dans pack voitures
(153, 3), -- Voiture F1 dans pack voitures

-- Pack animaux
(13, 4), -- Figurine dinosaure dans pack animaux
(33, 4), -- Figurine animal dans pack animaux
(138, 4), -- Figurine en bois cheval dans pack animaux
(139, 4), -- Figurine en bois chien dans pack animaux
(140, 4), -- Figurine en bois chat dans pack animaux
(141, 4), -- Figurine en bois éléphant dans pack animaux
(142, 4), -- Figurine en bois lion dans pack animaux

-- Pack éducatif
(1, 1), -- Train en bois dans pack éducatif
(6, 1), -- Robot éducatif dans pack éducatif
(26, 1), -- Tablette éducative dans pack éducatif
(145, 1), -- Jeu interactif MathMaster dans pack éducatif
(146, 1), -- Robot DinoBot dans pack éducatif
(147, 1), -- Tablette JuniorTab dans pack éducatif

-- Pack jeu vidéo
(130, 8), 
(123, 8), 
(124, 8),
(125, 8),        
(126, 8),          
(127, 8), 
(128, 8), 
(129, 8);
