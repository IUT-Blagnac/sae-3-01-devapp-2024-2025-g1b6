-- Insertion des catégories principales
INSERT INTO CATEGORIE (IDCATEG, IDCATEG_CATPERE, NOMCATEG, DESCCATEG) VALUES
(1, NULL, 'Jouets en Bois', 'Jouets éducatifs fabriqués en bois'),
(2, NULL, 'Jeux de Société', 'Jeux pour toute la famille'),
(3, NULL, 'Figurines', 'Figurines pour enfants'),
(4, NULL, 'Puzzles', 'Jeux de réflexion et puzzles'),
(5, NULL, 'Peluches', 'Peluches douces et câlines'),
(6, NULL, 'Électronique', 'Jouets électroniques et interactifs'),
(7, NULL, 'Véhicules', 'Voitures et véhicules pour enfants'),
(8, NULL, 'Extérieur', 'Jeux pour l’extérieur'),
(9, NULL, 'Créativité', 'Jeux pour développer la créativité'),
(10, NULL, 'Construction', 'Jeux de construction pour tous les âges');

-- Sous-catégories (au moins 2 par catégorie)
INSERT INTO CATEGORIE (IDCATEG, IDCATEG_CATPERE, NOMCATEG, DESCCATEG) VALUES
(11, 1, 'Jeux éducatifs', 'Jouets en bois éducatifs'),
(12, 1, 'Jouets d’éveil', 'Jouets d’éveil en bois'),
(13, 2, 'Jeux de cartes', 'Jeux de cartes amusants'),
(14, 2, 'Plateaux de jeux', 'Jeux de société classiques'),
(15, 4, 'Puzzles en 3D', 'Puzzles en trois dimensions'),
(16, 4, 'Puzzles classiques', 'Puzzles traditionnels'),
(17, 10, 'Blocs de construction', 'Blocs pour les petits'),
(18, 10, 'Modèles complexes', 'Jeux de construction avancés');

-- Sous-catégorie de niveau inférieur
INSERT INTO CATEGORIE (IDCATEG, IDCATEG_CATPERE, NOMCATEG, DESCCATEG) VALUES
(19, 11, 'Éveil musical', 'Jouets en bois pour développer la musique');



-- Insertion des marques
INSERT INTO MARQUE (IDMARQUE, NOMMARQUE, DESCMARQUE) VALUES
(1, 'Lego', 'Marque de jouets de construction en briques.'),
(2, 'Playmobil', 'Marque de jouets de figurines et de jeux de rôle.'),
(3, 'Mattel', 'Marque de jouets et de jeux pour enfants.'),
(4, 'Hasbro', 'Marque de jouets et de jeux de société.'),
(5, 'Fisher-Price', 'Marque de jouets éducatifs pour enfants.'),
(6, 'VTech', 'Marque de jouets électroniques et éducatifs.'),
(7, 'Hot Wheels', 'Marque de voitures miniatures et de circuits.'),
(8, 'Barbie', 'Marque de poupées et d’accessoires.'),
(9, 'Nerf', 'Marque de jouets de tir et de jeux d’action.'),
(10, 'Mega Bloks', 'Marque de jouets de construction en blocs.'),
(11, 'Brio', 'Marque de jouets en bois et de trains miniatures.'),
(12, 'Schleich', 'Marque de figurines réalistes d’animaux et de personnages.'),
(13, 'Melissa & Doug', 'Marque de jouets éducatifs et créatifs.'),
(14, 'Ravensburger', 'Marque de puzzles et de jeux de société.'),
(15, 'Spin Master', 'Marque de jouets innovants et de jeux de société.'),
(16, 'LeapFrog', 'Marque de jouets éducatifs électroniques.'),
(17, 'Little Tikes', 'Marque de jouets d’extérieur et de jeux de rôle.'),
(18, 'Step2', 'Marque de jouets d’extérieur et de jeux de rôle.'),
(19, 'Hape', 'Marque de jouets en bois et de jeux éducatifs.'),
(20, 'Janod', 'Marque de jouets en bois et de jeux éducatifs.');

-- Insertion des produits
INSERT INTO PRODUIT (IDPROD, IDMARQUE, NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, POIDSPRODUIT, QTESTOCK) VALUES
(1, 1, 'Train en bois', 'Train avec rails en bois', 29.99, 'Multicolore', 'Bois', 1.2, 50),
(2, 2, 'Puzzle animaux', 'Puzzle éducatif en bois', 15.99, 'Multicolore', 'Bois', 0.8, 30),
(3, 3, 'Voiture télécommandée', 'Voiture électrique rapide', 49.99, 'Rouge', 'Plastique', 1.5, 25),
(4, 1, 'Maison de poupée', 'Maison en bois avec meubles', 69.99, 'Rose', 'Bois', 4.0, 20),
(5, 4, 'Peluche éléphant', 'Peluche douce pour câliner', 19.99, 'Gris', 'Tissu', 0.5, 100),
(6, 2, 'Jeu de mémoire', 'Jeu de société pour mémoire', 14.99, 'Multicolore', 'Carton', 0.7, 45),
(7, 5, 'Camion de pompier', 'Camion miniature avec échelle', 34.99, 'Rouge', 'Métal et plastique', 2.0, 30),
(8, 6, 'Cuisinière jouet', 'Cuisine miniature avec ustensiles', 59.99, 'Rose', 'Bois et plastique', 4.5, 15),
(9, 1, 'Cheval à bascule', 'Cheval en bois avec poignée', 49.99, 'Marron', 'Bois', 3.8, 10),
(10, 3, 'Jeu de société', 'Jeu éducatif sur les animaux', 24.99, 'Multicolore', 'Carton et plastique', 1.2, 40),
(11, 1, 'Puzzle en bois', 'Puzzle éducatif en bois', 12.99, 'Multicolore', 'Bois', 0.5, 50),
(12, 2, 'Jeu de cartes', 'Jeu de cartes pour enfants', 9.99, 'Multicolore', 'Carton', 0.2, 100),
(13, 3, 'Figurine dinosaure', 'Figurine de dinosaure en plastique', 14.99, 'Vert', 'Plastique', 0.3, 75),
(14, 4, 'Peluche ours', 'Peluche douce en forme d''ours', 19.99, 'Brun', 'Tissu', 0.6, 80),
(15, 5, 'Voiture de course', 'Voiture de course télécommandée', 39.99, 'Rouge', 'Plastique', 1.5, 40),
(16, 6, 'Robot interactif', 'Robot éducatif interactif', 49.99, 'Argent', 'Plastique et métal', 2.0, 30),
(17, 7, 'Tricycle', 'Tricycle pour enfants', 59.99, 'Bleu', 'Métal et plastique', 4.0, 20),
(18, 8, 'Toboggan', 'Toboggan pour jardin', 89.99, 'Jaune', 'Plastique', 10.0, 15),
(19, 9, 'Kit de peinture', 'Kit de peinture pour enfants', 19.99, 'Multicolore', 'Plastique et peinture', 1.0, 50),
(20, 10, 'Jeu de construction', 'Blocs de construction en bois', 29.99, 'Multicolore', 'Bois', 2.0, 60),
(21, 1, 'Casse-tête en bois', 'Casse-tête éducatif en bois', 14.99, 'Multicolore', 'Bois', 0.7, 40),
(22, 2, 'Jeu de société classique', 'Jeu de société pour toute la famille', 24.99, 'Multicolore', 'Carton', 1.2, 50),
(23, 3, 'Figurine super-héros', 'Figurine de super-héros en plastique', 19.99, 'Bleu', 'Plastique', 0.4, 70),
(24, 4, 'Peluche licorne', 'Peluche douce en forme de licorne', 24.99, 'Blanc', 'Tissu', 0.8, 60),
(25, 5, 'Camion benne', 'Camion benne miniature', 34.99, 'Jaune', 'Métal et plastique', 2.5, 25),
(26, 6, 'Tablette éducative', 'Tablette éducative pour enfants', 59.99, 'Noir', 'Plastique et électronique', 1.5, 20),
(27, 7, 'Balançoire', 'Balançoire pour jardin', 79.99, 'Vert', 'Métal et plastique', 8.0, 10),
(28, 8, 'Piscine gonflable', 'Piscine gonflable pour enfants', 49.99, 'Bleu', 'Plastique', 5.0, 20),
(29, 9, 'Kit de bricolage', 'Kit de bricolage pour enfants', 29.99, 'Multicolore', 'Plastique et métal', 1.5, 30),
(30, 10, 'Jeu de dominos', 'Jeu de dominos en bois', 19.99, 'Multicolore', 'Bois', 1.0, 50),
(31, 1, 'Labyrinthe en bois', 'Labyrinthe éducatif en bois', 16.99, 'Multicolore', 'Bois', 0.8, 35),
(32, 2, 'Jeu de mémoire', 'Jeu de mémoire pour enfants', 14.99, 'Multicolore', 'Carton', 0.5, 60),
(33, 3, 'Figurine animal', 'Figurine d''animal en plastique', 12.99, 'Marron', 'Plastique', 0.3, 80),
(34, 4, 'Peluche lapin', 'Peluche douce en forme de lapin', 19.99, 'Blanc', 'Tissu', 0.6, 70),
(35, 5, 'Train électrique', 'Train électrique miniature', 49.99, 'Noir', 'Plastique et métal', 3.0, 20),
(36, 6, 'Drone pour enfants', 'Drone télécommandé pour enfants', 69.99, 'Blanc', 'Plastique et électronique', 1.2, 15),
(37, 7, 'Trottinette', 'Trottinette pour enfants', 39.99, 'Rouge', 'Métal et plastique', 3.5, 25),
(38, 8, 'Ballon sauteur', 'Ballon sauteur pour enfants', 19.99, 'Rouge', 'Plastique', 1.0, 50),
(39, 9, 'Kit de couture', 'Kit de couture pour enfants', 24.99, 'Multicolore', 'Tissu et plastique', 0.8, 40),
(40, 10, 'Jeu de quilles', 'Jeu de quilles en bois', 29.99, 'Multicolore', 'Bois', 2.0, 30),
(41, 1, 'Boîte à formes', 'Boîte à formes en bois', 14.99, 'Multicolore', 'Bois', 0.7, 50),
(42, 2, 'Jeu de l''oie', 'Jeu de société classique', 19.99, 'Multicolore', 'Carton', 1.0, 40),
(43, 3, 'Figurine chevalier', 'Figurine de chevalier en plastique', 14.99, 'Gris', 'Plastique', 0.4, 60),
(44, 4, 'Peluche chien', 'Peluche douce en forme de chien', 19.99, 'Marron', 'Tissu', 0.6, 70),
(45, 5, 'Hélicoptère télécommandé', 'Hélicoptère miniature télécommandé', 39.99, 'Bleu', 'Plastique et métal', 1.5, 30),
(46, 6, 'Console de jeux', 'Console de jeux pour enfants', 59.99, 'Noir', 'Plastique et électronique', 1.2, 20),
(47, 7, 'Bateau gonflable', 'Bateau gonflable pour enfants', 49.99, 'Jaune', 'Plastique', 3.0, 15),
(48, 8, 'Cerf-volant', 'Cerf-volant pour enfants', 14.99, 'Multicolore', 'Tissu et plastique', 0.5, 50),
(49, 9, 'Kit de jardinage', 'Kit de jardinage pour enfants', 19.99, 'Vert', 'Plastique et métal', 1.0, 30),
(50, 2, 'Marionnettes en tissu', 'Ensemble de 4 marionnettes', 29.99, 'Multicolore', 'Tissu', 1.1, 25);


-- Insertion des packs
INSERT INTO PACK (IDPACK, NOMPACK, DESCPACK) VALUES
(1, 'Pack éducatif', 'Ensemble de jeux éducatifs pour enfants'),
(2, 'Pack créatif', 'Stimule la créativité des enfants'),
(3, 'Pack voitures', '3 voitures de différentes couleurs'),
(4, 'Pack animaux', 'Ensemble de jouets sur les animaux'),
(5, 'Pack miniatures', 'Miniatures de véhicules divers'),
(6, 'Pack poupées', 'Poupées et accessoires'),
(7, 'Pack aventure', 'Jeux pour petits explorateurs'),
(8, 'Pack cuisine', 'Jouets pour futurs chefs');

-- Appartenance des produits aux catégories et sous-catégories
INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES
(1, 11), -- Train en bois -> Jeux éducatifs
(2, 16), -- Puzzle animaux -> Puzzles classiques
(3, 7),  -- Voiture télécommandée -> Véhicules
(4, 12), -- Maison de poupée -> Jouets d’éveil
(5, 15), -- Peluche éléphant -> Puzzles en 3D
(6, 14), -- Jeu de mémoire -> Plateaux de jeux
(7, 17), -- Camion de pompier -> Blocs de construction
(8, 18), -- Cuisinière jouet -> Modèles complexes
(9, 11), -- Cheval à bascule -> Jeux éducatifs
(10, 12), -- Jeu de société -> Jouets d’éveil
(11, 16), -- Puzzle en bois -> Puzzles classiques
(12, 14), -- Jeu de cartes -> Plateaux de jeux
(13, 13), -- Figurine dinosaure -> Figurines
(14, 15), -- Peluche ours -> Puzzles en 3D
(15, 7),  -- Voiture de course -> Véhicules
(16, 6),  -- Robot interactif -> Électronique
(17, 17), -- Tricycle -> Blocs de construction
(18, 8),  -- Toboggan -> Extérieur
(19, 9),  -- Kit de peinture -> Créativité
(20, 10), -- Jeu de construction -> Construction
(21, 16), -- Casse-tête en bois -> Puzzles classiques
(22, 14), -- Jeu de société classique -> Plateaux de jeux
(23, 13), -- Figurine super-héros -> Figurines
(24, 15), -- Peluche licorne -> Puzzles en 3D
(25, 7),  -- Camion benne -> Véhicules
(26, 6),  -- Tablette éducative -> Électronique
(27, 17), -- Balançoire -> Blocs de construction
(28, 8),  -- Piscine gonflable -> Extérieur
(29, 9),  -- Kit de bricolage -> Créativité
(30, 10), -- Jeu de dominos -> Construction
(31, 16), -- Labyrinthe en bois -> Puzzles classiques
(32, 14), -- Jeu de mémoire -> Plateaux de jeux
(33, 13); -- Figurine animal -> Figurines


-- Insertion de 50 clients répartis en France
INSERT INTO CLIENT (IDCLIENT, NOMCLIENT, PRENOMCLIENT, NUMTEL, EMAIL, PASSWORD, DATEN, DATEINSCRIPTION) VALUES
(1, 'Dupont', 'Jean', '0612345678', 'jean.dupont@gmail.com', 'hashed_password1', '1985-03-15', '2024-01-01'),
(2, 'Martin', 'Sophie', '0623456789', 'sophie.martin@gmail.com', 'hashed_password2', '1990-07-20', '2024-01-02'),
(3, 'Bernard', 'Luc', '0634567890', 'luc.bernard@gmail.com', 'hashed_password3', '1982-05-10', '2024-01-03'),
(4, 'Durand', 'Marie', '0645678901', 'marie.durand@gmail.com', 'hashed_password4', '1995-08-25', '2024-01-04'),
(5, 'Lefevre', 'Paul', '0656789012', 'paul.lefevre@gmail.com', 'hashed_password5', '1988-11-30', '2024-01-05'),
(6, 'Moreau', 'Julie', '0667890123', 'julie.moreau@gmail.com', 'hashed_password6', '1992-02-14', '2024-01-06'),
(7, 'Simon', 'Pierre', '0678901234', 'pierre.simon@gmail.com', 'hashed_password7', '1980-07-22', '2024-01-07'),
(8, 'Laurent', 'Emma', '0689012345', 'emma.laurent@gmail.com', 'hashed_password8', '1993-09-18', '2024-01-08'),
(9, 'Michel', 'Lucas', '0690123456', 'lucas.michel@gmail.com', 'hashed_password9', '1987-12-05', '2024-01-09'),
(10, 'Garcia', 'Chloe', '0612345679', 'chloe.garcia@gmail.com', 'hashed_password10', '1991-03-21', '2024-01-10'),
(11, 'Martinez', 'Hugo', '0623456780', 'hugo.martinez@gmail.com', 'hashed_password11', '1984-06-15', '2024-01-11'),
(12, 'Rodriguez', 'Alice', '0634567891', 'alice.rodriguez@gmail.com', 'hashed_password12', '1996-10-10', '2024-01-12'),
(13, 'Hernandez', 'Louis', '0645678902', 'louis.hernandez@gmail.com', 'hashed_password13', '1989-01-25', '2024-01-13'),
(14, 'Lopez', 'Sarah', '0656789013', 'sarah.lopez@gmail.com', 'hashed_password14', '1994-04-30', '2024-01-14'),
(15, 'Gonzalez', 'Thomas', '0667890124', 'thomas.gonzalez@gmail.com', 'hashed_password15', '1983-08-05', '2024-01-15'),
(16, 'Wilson', 'Laura', '0678901235', 'laura.wilson@gmail.com', 'hashed_password16', '1990-12-20', '2024-01-16'),
(17, 'Anderson', 'Maxime', '0689012346', 'maxime.anderson@gmail.com', 'hashed_password17', '1986-03-10', '2024-01-17'),
(18, 'Thomas', 'Camille', '0690123457', 'camille.thomas@gmail.com', 'hashed_password18', '1992-06-25', '2024-01-18'),
(19, 'Taylor', 'Nathan', '0612345680', 'nathan.taylor@gmail.com', 'hashed_password19', '1985-09-15', '2024-01-19'),
(20, 'Moore', 'Lea', '0623456781', 'lea.moore@gmail.com', 'hashed_password20', '1991-11-30', '2024-01-20'),
(21, 'Jackson', 'Ethan', '0634567892', 'ethan.jackson@gmail.com', 'hashed_password21', '1988-02-14', '2024-01-21'),
(22, 'Martin', 'Olivia', '0645678903', 'olivia.martin@gmail.com', 'hashed_password22', '1993-05-10', '2024-01-22'),
(23, 'Lee', 'Gabriel', '0656789014', 'gabriel.lee@gmail.com', 'hashed_password23', '1987-08-25', '2024-01-23'),
(24, 'Perez', 'Manon', '0667890125', 'manon.perez@gmail.com', 'hashed_password24', '1990-11-30', '2024-01-24'),
(25, 'Thompson', 'Arthur', '0678901236', 'arthur.thompson@gmail.com', 'hashed_password25', '1984-02-14', '2024-01-25'),
(26, 'White', 'Emma', '0689012347', 'emma.white@gmail.com', 'hashed_password26', '1995-05-10', '2024-01-26'),
(27, 'Harris', 'Leo', '0690123458', 'leo.harris@gmail.com', 'hashed_password27', '1989-08-25', '2024-01-27'),
(28, 'Clark', 'Sophie', '0612345681', 'sophie.clark@gmail.com', 'hashed_password28', '1992-11-30', '2024-01-28'),
(29, 'Lewis', 'Lucas', '0623456782', 'lucas.lewis@gmail.com', 'hashed_password29', '1986-02-14', '2024-01-29'),
(30, 'Robinson', 'Julie', '0634567893', 'julie.robinson@gmail.com', 'hashed_password30', '1991-05-10', '2024-01-30'),
(31, 'Walker', 'Nathan', '0645678904', 'nathan.walker@gmail.com', 'hashed_password31', '1985-08-25', '2024-01-31'),
(32, 'Young', 'Alice', '0656789015', 'alice.young@gmail.com', 'hashed_password32', '1993-11-30', '2024-02-01'),
(33, 'Allen', 'Thomas', '0667890126', 'thomas.allen@gmail.com', 'hashed_password33', '1988-02-14', '2024-02-02'),
(34, 'King', 'Emma', '0678901237', 'emma.king@gmail.com', 'hashed_password34', '1990-05-10', '2024-02-03'),
(35, 'Scott', 'Leo', '0689012348', 'leo.scott@gmail.com', 'hashed_password35', '1984-08-25', '2024-02-04'),
(36, 'Green', 'Sophie', '0690123459', 'sophie.green@gmail.com', 'hashed_password36', '1992-11-30', '2024-02-05'),
(37, 'Baker', 'Lucas', '0612345682', 'lucas.baker@gmail.com', 'hashed_password37', '1987-02-14', '2024-02-06'),
(38, 'Adams', 'Julie', '0623456783', 'julie.adams@gmail.com', 'hashed_password38', '1991-05-10', '2024-02-07'),
(39, 'Nelson', 'Nathan', '0634567894', 'nathan.nelson@gmail.com', 'hashed_password39', '1985-08-25', '2024-02-08'),
(40, 'Carter', 'Alice', '0645678905', 'alice.carter@gmail.com', 'hashed_password40', '1993-11-30', '2024-02-09'),
(41, 'Mitchell', 'Thomas', '0656789016', 'thomas.mitchell@gmail.com', 'hashed_password41', '1988-02-14', '2024-02-10'),
(42, 'Perez', 'Emma', '0667890127', 'emma.perez@gmail.com', 'hashed_password42', '1990-05-10', '2024-02-11'),
(43, 'Roberts', 'Leo', '0678901238', 'leo.roberts@gmail.com', 'hashed_password43', '1984-08-25', '2024-02-12'),
(44, 'Turner', 'Sophie', '0689012349', 'sophie.turner@gmail.com', 'hashed_password44', '1992-11-30', '2024-02-13'),
(45, 'Phillips', 'Lucas', '0690123460', 'lucas.phillips@gmail.com', 'hashed_password45', '1987-02-14', '2024-02-14'),
(46, 'Campbell', 'Julie', '0612345683', 'julie.campbell@gmail.com', 'hashed_password46', '1991-05-10', '2024-02-15'),
(47, 'Parker', 'Nathan', '0623456784', 'nathan.parker@gmail.com', 'hashed_password47', '1985-08-25', '2024-02-16'),
(48, 'Evans', 'Alice', '0634567895', 'alice.evans@gmail.com', 'hashed_password48', '1993-11-30', '2024-02-17'),
(49, 'Edwards', 'Thomas', '0645678906', 'thomas.edwards@gmail.com', 'hashed_password49', '1988-02-14', '2024-02-18'),
(50, 'Collins', 'Emma', '0656789017', 'emma.collins@gmail.com', 'hashed_password50', '1990-05-10', '2024-02-19');


-- Insertion de commandes avec détails
INSERT INTO COMMANDE (NUMCOMMANDE, IDCLIENT, NUMCB, IDADRESSE, IDLIVRAISON, TYPEREGLEMENT, DATECOMMANDE) VALUES
(1, 1, '1234567890123456', 1, 1, 'CB', '2024-02-01'),
(2, 2, '2345678901234567', 2, 2, 'CB', '2024-02-02'),
(3, 3, '3456789012345678', 3, 3, 'CB', '2024-02-03'),
(4, 4, '4567890123456789', 4, 4, 'CB', '2024-02-04'),
(5, 5, '5678901234567890', 5, 5, 'CB', '2024-02-05'),
(6, 6, '6789012345678901', 6, 6, 'CB', '2024-02-06'),
(7, 7, '7890123456789012', 7, 7, 'CB', '2024-02-07'),
(8, 8, '8901234567890123', 8, 8, 'CB', '2024-02-08'),
(9, 9, '9012345678901234', 9, 9, 'CB', '2024-02-09'),
(10, 10, '0123456789012345', 10, 10, 'CB', '2024-02-10'),
(11, 11, '1234567890123456', 11, 11, 'CB', '2024-02-11'),
(12, 12, '2345678901234567', 12, 12, 'CB', '2024-02-12'),
(13, 13, '3456789012345678', 13, 13, 'CB', '2024-02-13'),
(14, 14, '4567890123456789', 14, 14, 'CB', '2024-02-14'),
(15, 15, '5678901234567890', 15, 15, 'CB', '2024-02-15'),
(16, 16, '6789012345678901', 16, 16, 'CB', '2024-02-16'),
(17, 17, '7890123456789012', 17, 17, 'CB', '2024-02-17'),
(18, 18, '8901234567890123', 18, 18, 'CB', '2024-02-18'),
(19, 19, '9012345678901234', 19, 19, 'CB', '2024-02-19'),
(20, 20, '0123456789012345', 20, 20, 'CB', '2024-02-20'),
(21, 21, '1234567890123456', 21, 21, 'CB', '2024-02-21'),
(22, 22, '2345678901234567', 22, 22, 'CB', '2024-02-22'),
(23, 23, '3456789012345678', 23, 23, 'CB', '2024-02-23'),
(24, 24, '4567890123456789', 24, 24, 'CB', '2024-02-24'),
(25, 25, '5678901234567890', 25, 25, 'CB', '2024-02-25'),
(26, 26, '6789012345678901', 26, 26, 'CB', '2024-02-26'),
(27, 27, '7890123456789012', 27, 27, 'CB', '2024-02-27'),
(28, 28, '8901234567890123', 28, 28, 'CB', '2024-02-28'),
(29, 29, '9012345678901234', 29, 29, 'CB', '2024-02-29'),
(30, 30, '0123456789012345', 30, 30, 'CB', '2024-03-01'),
(31, 31, '1234567890123456', 31, 31, 'CB', '2024-03-02'),
(32, 32, '2345678901234567', 32, 32, 'CB', '2024-03-03'),
(33, 33, '3456789012345678', 33, 33, 'CB', '2024-03-04'),
(34, 34, '4567890123456789', 34, 34, 'CB', '2024-03-05'),
(35, 35, '5678901234567890', 35, 35, 'CB', '2024-03-06'),
(36, 36, '6789012345678901', 36, 36, 'CB', '2024-03-07'),
(37, 37, '7890123456789012', 37, 37, 'CB', '2024-03-08'),
(38, 38, '8901234567890123', 38, 38, 'CB', '2024-03-09'),
(39, 39, '9012345678901234', 39, 39, 'CB', '2024-03-10'),
(40, 40, '0123456789012345', 40, 40, 'CB', '2024-03-11'),
(41, 41, '1234567890123456', 41, 41, 'CB', '2024-03-12'),
(42, 42, '2345678901234567', 42, 42, 'CB', '2024-03-13'),
(43, 43, '3456789012345678', 43, 43, 'CB', '2024-03-14'),
(44, 44, '4567890123456789', 44, 44, 'CB', '2024-03-15'),
(45, 45, '5678901234567890', 45, 45, 'CB', '2024-03-16'),
(46, 46, '6789012345678901', 46, 46, 'CB', '2024-03-17'),
(47, 47, '7890123456789012', 47, 47, 'CB', '2024-03-18'),
(48, 48, '8901234567890123', 48, 48, 'CB', '2024-03-19'),
(49, 49, '9012345678901234', 49, 49, 'CB', '2024-03-20'),
(50, 50, '0123456789012345', 50, 50, 'CB', '2024-03-21'),
(51, 1, '1234567890123456', 1, 1, 'CB', '2024-03-22'),
(52, 2, '2345678901234567', 2, 2, 'CB', '2024-03-23'),
(53, 3, '3456789012345678', 3, 3, 'CB', '2024-03-24'),
(54, 4, '4567890123456789', 4, 4, 'CB', '2024-03-25'),
(55, 5, '5678901234567890', 5, 5, 'CB', '2024-03-26'),
(56, 6, '6789012345678901', 6, 6, 'CB', '2024-03-27'),
(57, 7, '7890123456789012', 7, 7, 'CB', '2024-03-28'),
(58, 8, '8901234567890123', 8, 8, 'CB', '2024-03-29'),
(59, 9, '9012345678901234', 9, 9, 'CB', '2024-03-30'),
(60, 10, '0123456789012345', 10, 10, 'CB', '2024-03-31'),
(61, 11, '1234567890123456', 11, 11, 'CB', '2024-04-01'),
(62, 12, '2345678901234567', 12, 12, 'CB', '2024-04-02'),
(63, 13, '3456789012345678', 13, 13, 'CB', '2024-04-03'),
(64, 14, '4567890123456789', 14, 14, 'CB', '2024-04-04'),
(65, 15, '5678901234567890', 15, 15, 'CB', '2024-04-05'),
(66, 16, '6789012345678901', 16, 16, 'CB', '2024-04-06'),
(67, 17, '7890123456789012', 17, 17, 'CB', '2024-04-07'),
(68, 18, '8901234567890123', 18, 18, 'CB', '2024-04-08'),
(69, 19, '9012345678901234', 19, 19, 'CB', '2024-04-09'),
(70, 20, '0123456789012345', 20, 20, 'CB', '2024-04-10'),
(71, 21, '1234567890123456', 21, 21, 'CB', '2024-04-11'),
(72, 22, '2345678901234567', 22, 22, 'CB', '2024-04-12'),
(73, 23, '3456789012345678', 23, 23, 'CB', '2024-04-13'),
(74, 24, '4567890123456789', 24, 24, 'CB', '2024-04-14'),
(75, 25, '5678901234567890', 25, 25, 'CB', '2024-04-15'),
(76, 26, '6789012345678901', 26, 26, 'CB', '2024-04-16'),
(77, 27, '7890123456789012', 27, 27, 'CB', '2024-04-17'),
(78, 28, '8901234567890123', 28, 28, 'CB', '2024-04-18'),
(79, 29, '9012345678901234', 29, 29, 'CB', '2024-04-19'),
(80, 30, '0123456789012345', 30, 30, 'CB', '2024-04-20'),
(81, 31, '1234567890123456', 31, 31, 'CB', '2024-04-21'),
(82, 32, '2345678901234567', 32, 32, 'CB', '2024-04-22'),
(83, 33, '3456789012345678', 33, 33, 'CB', '2024-04-23'),
(84, 34, '4567890123456789', 34, 34, 'CB', '2024-04-24'),
(85, 35, '5678901234567890', 35, 35, 'CB', '2024-04-25'),
(86, 36, '6789012345678901', 36, 36, 'CB', '2024-04-26'),
(87, 37, '7890123456789012', 37, 37, 'CB', '2024-04-27'),
(88, 38, '8901234567890123', 38, 38, 'CB', '2024-04-28'),
(89, 39, '9012345678901234', 39, 39, 'CB', '2024-04-29'),
(90, 40, '0123456789012345', 40, 40, 'CB', '2024-04-30'),
(91, 41, '1234567890123456', 41, 41, 'CB', '2024-05-01'),
(92, 42, '2345678901234567', 42, 42, 'CB', '2024-05-02'),
(93, 43, '3456789012345678', 43, 43, 'CB', '2024-05-03'),
(94, 44, '4567890123456789', 44, 44, 'CB', '2024-05-04'),
(95, 45, '5678901234567890', 45, 45, 'CB', '2024-05-05'),
(96, 46, '6789012345678901', 46, 46, 'CB', '2024-05-06'),
(97, 47, '7890123456789012', 47, 47, 'CB', '2024-05-07'),
(98, 48, '8901234567890123', 48, 48, 'CB', '2024-05-08'),
(99, 49, '9012345678901234', 49, 49, 'CB', '2024-05-09'),
(100, 50, '0123456789012345', 50, 50, 'CB', '2024-05-10');

-- Insertion des livraisons
INSERT INTO LIVRAISON (IDLIVRAISON, IDTRANSPORTEUR, IDADRESSE, NUMCOMMANDE, STATUTLIVRAISON, CODESUIVI) VALUES
(1, 1, 1, 1, 'En cours', 'ABC123456789'),
(2, 2, 2, 2, 'Livrée', 'DEF123456789'),
(3, 3, 3, 3, 'En cours', 'GHI123456789'),
(4, 4, 4, 4, 'En cours', 'JKL123456789'),
(5, 5, 5, 5, 'Livrée', 'MNO123456789'),
(6, 1, 6, 6, 'En cours', 'PQR123456789'),
(7, 2, 7, 7, 'En cours', 'STU123456789'),
(8, 3, 8, 8, 'Livrée', 'VWX123456789'),
(9, 4, 9, 9, 'En cours', 'YZA123456789'),
(10, 5, 10, 10, 'En cours', 'BCD123456789'),
(11, 1, 11, 11, 'Livrée', 'EFG123456789'),
(12, 2, 12, 12, 'En cours', 'HIJ123456789'),
(13, 3, 13, 13, 'En cours', 'KLM123456789'),
(14, 4, 14, 14, 'Livrée', 'NOP123456789'),
(15, 5, 15, 15, 'En cours', 'QRS123456789'),
(16, 1, 16, 16, 'En cours', 'TUV123456789'),
(17, 2, 17, 17, 'Livrée', 'WXY123456789'),
(18, 3, 18, 18, 'En cours', 'ZAB123456789'),
(19, 4, 19, 19, 'En cours', 'CDE123456789'),
(20, 5, 20, 20, 'Livrée', 'FGH123456789');

-- Insertion d’avis
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
-- Regroupement de produits
INSERT INTO ASSOPACK (IDPROD, IDPACK) VALUES
(1, 1), -- Train en bois dans pack éducatif
(3, 3), -- Voiture télécommandée dans pack voitures
(4, 1), -- Maison de poupée dans pack éducatif
(5, 2), -- Peluche éléphant dans pack peluches
(6, 1), -- Jeu de mémoire dans pack éducatif
(7, 3), -- Camion de pompier dans pack voitures
(8, 1), -- Cuisinière jouet dans pack éducatif
(9, 1), -- Cheval à bascule dans pack éducatif
(10, 1), -- Jeu de société dans pack éducatif
(11, 1), -- Puzzle en bois dans pack éducatif
(12, 1), -- Jeu de cartes dans pack éducatif
(13, 4), -- Figurine dinosaure dans pack aventure
(14, 2), -- Peluche ours dans pack peluches
(15, 3), -- Voiture de course dans pack voitures
(16, 1), -- Robot interactif dans pack éducatif
(17, 4), -- Tricycle dans pack aventure
(18, 4), -- Toboggan dans pack aventure
(19, 1), -- Kit de peinture dans pack éducatif
(20, 1), -- Jeu de construction dans pack éducatif
(21, 1), -- Casse-tête en bois dans pack éducatif
(22, 1), -- Jeu de société classique dans pack éducatif
(23, 4), -- Figurine super-héros dans pack aventure
(24, 2), -- Peluche licorne dans pack peluches
(25, 3), -- Camion benne dans pack voitures
