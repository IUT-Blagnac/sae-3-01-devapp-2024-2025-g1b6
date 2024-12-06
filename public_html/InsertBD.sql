-- Insertion des transporteurs
INSERT INTO TRANSPORTEUR (IDTRANSPORTEUR, TYPEEXP, FRAISEXP, FRAISKG, DELAILIVRAISON) VALUES
(1, 'Standard', 5.99, 1.00, 5),
(2, 'Express', 9.99, 1.50, 2),
(3, 'Economique', 3.99, 0.80, 7),
(4, 'Premium', 12.99, 2.00, 1),
(5, 'International', 15.99, 2.50, 10);

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
(20, NULL, 'Enfants', 'Jeux pour enfants');
(21, NULL, 'Ado', 'Jeux pour ados');
(22, NULL, 'Jeune-adulte', 'Jeux pour jeunes adultes');
(23, NULL, 'Adulte', 'Jeux pour adultes');

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
(21, 'Ludorama', 'Marque de jouets ludorama qui vend de tout.');

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
(26, 19, 'Puzzle en bois Jungle', 'Puzzle éducatif pour enfants sur le thème de la jungle', 14.99, 'Multicolore', 'Bois', 0.5, 80), -- Enfant
(27, 9, 'Arbalète Nerf Elite', 'Arbalète Nerf pour des aventures excitantes', 34.99, 'Bleu', 'Plastique', 1.4, 50), -- Ado
(28, 14, 'Jeu de stratégie Catan', 'Jeu de société stratégique pour jeunes adultes', 39.99, 'Multicolore', 'Carton', 1.8, 40), -- Jeune-adulte
(29, 15, 'Puzzle 2000 pièces Montagnes', 'Puzzle de 2000 pièces représentant des montagnes majestueuses', 29.99, 'Multicolore', 'Carton', 1.5, 30), -- Adulte
(30, 13, 'Kit de crochet pour débutants', 'Kit créatif pour apprendre le crochet', 19.99, 'Multicolore', 'Laine et plastique', 0.6, 60), -- Adulte
(31, 14, 'Puzzle 3D Tour Eiffel', 'Puzzle en 3D représentant la Tour Eiffel', 19.99, 'Gris', 'Carton', 0.8, 30),
(32, 12, 'Figurine Cheval', 'Figurine réaliste de cheval', 12.99, 'Marron', 'Plastique', 0.3, 100),
(33, 5, 'Ours en peluche', 'Peluches douce pour câlins', 24.99, 'Beige', 'Polyester', 0.5, 80),
(34, 6, 'Tablette éducative VTech', 'Tablette éducative interactive pour enfants', 59.99, 'Bleu', 'Plastique', 0.9, 40),
(35, 7, 'Voiture de course Hot Wheels', 'Voiture miniature de course rapide', 9.99, 'Rouge', 'Métal', 0.2, 200),
(36, 17, 'Cuisinière extérieure Step2', 'Cuisinière pour jouer à l’extérieur', 99.99, 'Rose', 'Plastique', 8.0, 15),
(37, 13, 'Kit de peinture pour enfants', 'Kit de peinture pour développer la créativité', 14.99, 'Multicolore', 'Plastique et peinture', 0.6, 70),
(38, 10, 'Blocs de construction Mega Bloks', 'Blocs pour construire des structures amusantes', 34.99, 'Multicolore', 'Plastique', 1.5, 60),
(39, 19, 'Tambourin en bois', 'Tambourin éducatif en bois', 15.99, 'Naturel', 'Bois et peau synthétique', 0.4, 45),
(40, 20, 'Jeu de cartes Uno', 'Jeu de cartes familial et amusant', 9.99, 'Multicolore', 'Carton', 0.3, 150),
(41, 8, 'Poupée Barbie', 'Poupée Barbie avec accessoires', 25.99, 'Rose', 'Plastique', 0.4, 100),
(42, 15, 'Plateau de jeu Cluedo', 'Jeu de société classique Cluedo', 29.99, 'Multicolore', 'Carton', 1.2, 60),
(43, 2, 'Château Playmobil', 'Château Playmobil pour des aventures fantastiques', 74.99, 'Gris', 'Plastique', 3.0, 30),
(44, 1, 'Bateau pirate Lego', 'Bateau pirate à construire', 89.99, 'Noir', 'Plastique', 2.5, 25),
(45, 14, 'Puzzle classique 500 pièces', 'Puzzle traditionnel de 500 pièces', 14.99, 'Multicolore', 'Carton', 0.7, 70),
(46, 21, 'Kit de découverte scientifique', 'Kit éducatif pour expériences scientifiques', 49.99, 'Blanc', 'Plastique et métal', 2.0, 35),
(47, 4, 'Nerf Blaster', 'Lanceur Nerf pour des batailles amusantes', 39.99, 'Orange', 'Plastique', 1.5, 50),
(48, 3, 'Poupée Fisher-Price', 'Poupée éducative pour apprendre les couleurs', 19.99, 'Multicolore', 'Tissu', 0.8, 75),
(49, 16, 'LeapFrog Laptop', 'Ordinateur éducatif interactif LeapFrog', 59.99, 'Vert', 'Plastique', 1.3, 40),
(50, 12, 'Figurines d’animaux de ferme', 'Ensemble de figurines réalistes', 22.99, 'Multicolore', 'Plastique', 1.1, 80),
(51, 18, 'Maison en plastique Little Tikes', 'Maison pour jouer dans le jardin', 159.99, 'Rouge', 'Plastique', 10.0, 10),
(52, 9, 'Blocs aimantés créatifs', 'Blocs aimantés pour des créations originales', 39.99, 'Multicolore', 'Plastique', 1.7, 50),
(53, 13, 'Ensemble de cuisine en bois', 'Cuisine miniature éducative en bois', 79.99, 'Naturel', 'Bois', 5.0, 20),
(54, 15, 'Jeu de société innovant', 'Jeu de société Spin Master innovant et captivant', 34.99, 'Multicolore', 'Carton et plastique', 1.5, 40),
(55, 21, 'Cubes d''éveil colorés', 'Cubes empilables pour les plus petits, avec des couleurs vives et des motifs amusants', 12.99, 'Multicolore', 'Plastique', 0.4, 100), -- Enfant
(56, 21, 'Skateboard mini Ludorama', 'Skateboard compact et robuste pour débuter avec style', 29.99, 'Noir et rouge', 'Bois et plastique', 2.5, 50), -- Ado
(57, 21, 'Jeu de rôles Donjons & Ludorama', 'Kit complet pour s''initier aux jeux de rôle en groupe', 49.99, 'Multicolore', 'Papier et plastique', 1.2, 40), -- Jeune-adulte
(58, 21, 'Jeu d''échecs Deluxe', 'Échiquier en bois avec des pièces sculptées à la main', 59.99, 'Naturel', 'Bois', 2.0, 30); -- Adulte

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
(26, 20), -- Puzzle en bois Jungle (Enfant)
(27, 21), -- Arbalète Nerf Elite (Ado)
(28, 22), -- Jeu de stratégie Catan (Jeune-adulte)
(29, 23), -- Puzzle 2000 pièces Montagnes (Adulte)
(30, 23),
(31, 15), -- Puzzle 3D Tour Eiffel (Puzzles en 3D)
(32, 3), -- Figurine Cheval (Figurines)
(33, 5), -- Ours en peluche (Peluches)
(34, 6), -- Tablette éducative VTech (Électronique)
(35, 7), -- Voiture de course Hot Wheels (Véhicules)
(36, 8), -- Cuisinière extérieure Step2 (Extérieur)
(37, 9), -- Kit de peinture pour enfants (Créativité)
(38, 17), -- Blocs de construction Mega Bloks (Blocs de construction)
(39, 19), -- Tambourin en bois (Éveil musical)
(40, 13), -- Jeu de cartes Uno (Jeux de cartes)
(41, 21), -- Poupée Barbie (Ado)
(42, 14), -- Plateau de jeu Cluedo (Plateaux de jeux)
(43, 2), -- Château Playmobil (Jeux de Société)
(44, 10), -- Bateau pirate Lego (Construction)
(45, 16), -- Puzzle classique 500 pièces (Puzzles classiques)
(46, 11), -- Kit de découverte scientifique (Jeux éducatifs)
(47, 9), -- Nerf Blaster (Créativité)
(48, 12), -- Poupée Fisher-Price (Jouets d’éveil)
(49, 16), -- LeapFrog Laptop (Puzzles classiques)
(50, 3), -- Figurines d’animaux de ferme (Figurines)
(51, 8), -- Maison en plastique Little Tikes (Extérieur)
(52, 18), -- Blocs aimantés créatifs (Modèles complexes)
(53, 11), -- Ensemble de cuisine en bois (Jeux éducatifs)
(54, 2); -- Jeu de société innovant (Jeux de Société)
(55, 20), -- Cubes d'éveil colorés (Enfant)
(56, 21), -- Skateboard mini Ludorama (Ado)
(57, 22), -- Jeu de rôles Donjons & Ludorama (Jeune-adulte)
(58, 23); -- Jeu d'échecs Deluxe (Adulte)


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

-- Insertion des adresses
INSERT INTO ADRESSE (IDADRESSE, NUMRUE, NOMRUE, COMPLEMENTADR, NOMVILLE, CODEPOSTAL, PAYS) VALUES
(1, '12', 'Rue de la Paix', NULL, 'Paris', 75002, 'France'),
(2, '34', 'Avenue des Champs-Élysées', 'Appartement 5B', 'Paris', 75008, 'France'),
(3, '56', 'Boulevard Saint-Germain', NULL, 'Paris', 75006, 'France'),
(4, '78', 'Rue de Rivoli', 'Bâtiment A', 'Paris', 75001, 'France'),
(5, '90', 'Avenue Montaigne', NULL, 'Paris', 75008, 'France'),
(6, '123', 'Rue de la République', NULL, 'Lyon', 69002, 'France'),
(7, '456', 'Cours Lafayette', 'Étage 3', 'Lyon', 69003, 'France'),
(8, '789', 'Rue Victor Hugo', NULL, 'Lyon', 69002, 'France'),
(9, '101', 'Avenue Jean Jaurès', NULL, 'Lyon', 69007, 'France'),
(10, '202', 'Rue Garibaldi', NULL, 'Lyon', 69003, 'France'),
(11, '15', 'Rue de la Liberté', NULL, 'Marseille', 13001, 'France'),
(12, '27', 'Avenue du Prado', 'Appartement 2A', 'Marseille', 13008, 'France'),
(13, '39', 'Boulevard Michelet', NULL, 'Marseille', 13009, 'France'),
(14, '51', 'Rue Saint-Ferréol', 'Bâtiment B', 'Marseille', 13001, 'France'),
(15, '63', 'Avenue de la Canebière', NULL, 'Marseille', 13001, 'France'),
(16, '75', 'Rue Paradis', NULL, 'Marseille', 13006, 'France'),
(17, '87', 'Avenue des Chartreux', 'Étage 4', 'Marseille', 13004, 'France'),
(18, '99', 'Boulevard Baille', NULL, 'Marseille', 13005, 'France'),
(19, '111', 'Rue de Rome', NULL, 'Marseille', 13006, 'France'),
(20, '123', 'Avenue du Prado', NULL, 'Marseille', 13008, 'France'),
(21, '135', 'Rue de la République', NULL, 'Nice', 06000, 'France'),
(22, '147', 'Avenue Jean Médecin', 'Appartement 3C', 'Nice', 06000, 'France'),
(23, '159', 'Boulevard Victor Hugo', NULL, 'Nice', 06000, 'France'),
(24, '171', 'Rue de France', 'Bâtiment C', 'Nice', 06000, 'France'),
(25, '183', 'Avenue des Fleurs', NULL, 'Nice', 06000, 'France'),
(26, '195', 'Rue de la Buffa', NULL, 'Nice', 06000, 'France'),
(27, '207', 'Avenue de la Californie', 'Étage 5', 'Nice', 06000, 'France'),
(28, '219', 'Boulevard Gambetta', NULL, 'Nice', 06000, 'France'),
(29, '231', 'Rue de l''Hôtel des Postes', NULL, 'Nice', 06000, 'France'),
(30, '243', 'Avenue Thiers', NULL, 'Nice', 06000, 'France'),
(31, '255', 'Rue de la Liberté', NULL, 'Bordeaux', 33000, 'France'),
(32, '267', 'Avenue des Quinconces', 'Appartement 4D', 'Bordeaux', 33000, 'France'),
(33, '279', 'Boulevard de la République', NULL, 'Bordeaux', 33000, 'France'),
(34, '291', 'Rue Sainte-Catherine', 'Bâtiment D', 'Bordeaux', 33000, 'France'),
(35, '303', 'Avenue de la Victoire', NULL, 'Bordeaux', 33000, 'France'),
(36, '315', 'Rue du Palais Gallien', NULL, 'Bordeaux', 33000, 'France'),
(37, '327', 'Avenue de la Libération', 'Étage 6', 'Bordeaux', 33000, 'France'),
(38, '339', 'Boulevard Georges V', NULL, 'Bordeaux', 33000, 'France'),
(39, '351', 'Rue du Château d''Eau', NULL, 'Bordeaux', 33000, 'France'),
(40, '363', 'Avenue de la Marne', NULL, 'Bordeaux', 33000, 'France'),
(41, '375', 'Rue de la République', NULL, 'Toulouse', 31000, 'France'),
(42, '387', 'Avenue Jean Jaurès', 'Appartement 5E', 'Toulouse', 31000, 'France'),
(43, '399', 'Boulevard de Strasbourg', NULL, 'Toulouse', 31000, 'France'),
(44, '411', 'Rue de Metz', 'Bâtiment E', 'Toulouse', 31000, 'France'),
(45, '423', 'Avenue des Minimes', NULL, 'Toulouse', 31000, 'France'),
(46, '435', 'Rue de la Colombette', NULL, 'Toulouse', 31000, 'France'),
(47, '447', 'Avenue de l''URSS', 'Étage 7', 'Toulouse', 31000, 'France'),
(48, '459', 'Boulevard de la Gare', NULL, 'Toulouse', 31000, 'France'),
(49, '471', 'Rue de la Pomme', NULL, 'Toulouse', 31000, 'France'),
(50, '483', 'Avenue de la Gloire', NULL, 'Toulouse', 31000, 'France');

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

-- Insertion de commandes avec détails
INSERT INTO COMMANDE (NUMCOMMANDE, IDCLIENT, IDTRANSPORTEUR, NUMCB, IDADRESSE, TYPEREGLEMENT, DATECOMMANDE, STATUTLIVRAISON, CODESUIVI) VALUES
(1, 1, 1, '1234567890123456', 1, 'CB', '2024-02-01', 'En cours', 'ABC123456789'),
(2, 2, 2, '2345678901234567', 2, 'CB', '2024-02-02', 'Livrée', 'DEF123456789'),
(3, 3, 3, '3456789012345678', 3, 'CB', '2024-02-03', 'En cours', 'GHI123456789'),
(4, 4, 4, '4567890123456789', 4, 'CB', '2024-02-04', 'En cours', 'JKL123456789'),
(5, 5, 5, '5678901234567890', 5, 'CB', '2024-02-05', 'Livrée', 'MNO123456789'),
(6, 6, 1, '6789012345678901', 6, 'CB', '2024-02-06', 'En cours', 'PQR123456789'),
(7, 7, 2, '7890123456789012', 7, 'CB', '2024-02-07', 'En cours', 'STU123456789'),
(8, 8, 3, '8901234567890123', 8, 'CB', '2024-02-08', 'Livrée', 'VWX123456789'),
(9, 9, 4, '9012345678901234', 9, 'CB', '2024-02-09', 'En cours', 'YZA123456789'),
(10, 10, 5, '0123456789012345', 10, 'CB', '2024-02-10', 'En cours', 'BCD123456789'),
(11, 11, 1, '1234567890123456', 11, 'CB', '2024-02-11', 'Livrée', 'EFG123456789'),
(12, 12, 2, '2345678901234567', 12, 'CB', '2024-02-12', 'En cours', 'HIJ123456789'),
(13, 13, 3, '3456789012345678', 13, 'CB', '2024-02-13', 'En cours', 'KLM123456789'),
(14, 14, 4, '4567890123456789', 14, 'CB', '2024-02-14', 'Livrée', 'NOP123456789'),
(15, 15, 5, '5678901234567890', 15, 'CB', '2024-02-15', 'En cours', 'QRS123456789'),
(16, 16, 1, '6789012345678901', 16, 'CB', '2024-02-16', 'En cours', 'TUV123456789'),
(17, 17, 2, '7890123456789012', 17, 'CB', '2024-02-17', 'Livrée', 'WXY123456789'),
(18, 18, 3, '8901234567890123', 18, 'CB', '2024-02-18', 'En cours', 'ZAB123456789'),
(19, 19, 4, '9012345678901234', 19, 'CB', '2024-02-19', 'En cours', 'CDE123456789'),
(20, 20, 5, '0123456789012345', 20, 'CB', '2024-02-20', 'Livrée', 'FGH123456789'),
(21, 21, 1, '1234567890123456', 21, 'CB', '2024-02-21', 'En cours', 'IJK123456789'),
(22, 22, 2, '2345678901234567', 22, 'CB', '2024-02-22', 'Livrée', 'LMN123456789'),
(23, 23, 3, '3456789012345678', 23, 'CB', '2024-02-23', 'En cours', 'OPQ123456789'),
(24, 24, 4, '4567890123456789', 24, 'CB', '2024-02-24', 'En cours', 'RST123456789'),
(25, 25, 5, '5678901234567890', 25, 'CB', '2024-02-25', 'Livrée', 'UVW123456789'),
(26, 26, 1, '6789012345678901', 26, 'CB', '2024-02-26', 'En cours', 'XYZ123456789'),
(27, 27, 2, '7890123456789012', 27, 'CB', '2024-02-27', 'En cours', 'ABC123456789'),
(28, 28, 3, '8901234567890123', 28, 'CB', '2024-02-28', 'Livrée', 'DEF123456789'),
(29, 29, 4, '9012345678901234', 29, 'CB', '2024-02-29', 'En cours', 'GHI123456789'),
(30, 30, 5, '0123456789012345', 30, 'CB', '2024-03-01', 'En cours', 'JKL123456789'),
(31, 31, 1, '1234567890123456', 31, 'CB', '2024-03-02', 'Livrée', 'MNO123456789'),
(32, 32, 2, '2345678901234567', 32, 'CB', '2024-03-03', 'En cours', 'PQR123456789'),
(33, 33, 3, '3456789012345678', 33, 'CB', '2024-03-04', 'En cours', 'STU123456789'),
(34, 34, 4, '4567890123456789', 34, 'CB', '2024-03-05', 'Livrée', 'VWX123456789'),
(35, 35, 5, '5678901234567890', 35, 'CB', '2024-03-06', 'En cours', 'YZA123456789'),
(36, 36, 1, '6789012345678901', 36, 'CB', '2024-03-07', 'En cours', 'BCD123456789'),
(37, 37, 2, '7890123456789012', 37, 'CB', '2024-03-08', 'Livrée', 'EFG123456789'),
(38, 38, 3, '8901234567890123', 38, 'CB', '2024-03-09', 'En cours', 'HIJ123456789'),
(39, 39, 4, '9012345678901234', 39, 'CB', '2024-03-10', 'En cours', 'KLM123456789'),
(40, 40, 5, '0123456789012345', 40, 'CB', '2024-03-11', 'Livrée', 'NOP123456789'),
(41, 41, 1, '1234567890123456', 41, 'CB', '2024-03-12', 'En cours', 'QRS123456789'),
(42, 42, 2, '2345678901234567', 42, 'CB', '2024-03-13', 'En cours', 'TUV123456789'),
(43, 43, 3, '3456789012345678', 43, 'CB', '2024-03-14', 'Livrée', 'WXY123456789'),
(44, 44, 4, '4567890123456789', 44, 'CB', '2024-03-15', 'En cours', 'ZAB123456789'),
(45, 45, 5, '5678901234567890', 45, 'CB', '2024-03-16', 'En cours', 'CDE123456789'),
(46, 46, 1, '6789012345678901', 46, 'CB', '2024-03-17', 'Livrée', 'FGH123456789'),
(47, 47, 2, '7890123456789012', 47, 'CB', '2024-03-18', 'En cours', 'IJK123456789'),
(48, 48, 3, '8901234567890123', 48, 'CB', '2024-03-19', 'En cours', 'LMN123456789'),
(49, 49, 4, '9012345678901234', 49, 'CB', '2024-03-20', 'Livrée', 'OPQ123456789'),
(50, 50, 5, '0123456789012345', 50, 'CB', '2024-03-21', 'En cours', 'RST123456789'),
(51, 1, 1, '1234567890123456', 1, 'CB', '2024-03-22', 'En cours', 'UVW123456789'),
(52, 2, 2, '2345678901234567', 2, 'CB', '2024-03-23', 'Livrée', 'XYZ123456789'),
(53, 3, 3, '3456789012345678', 3, 'CB', '2024-03-24', 'En cours', 'ABC123456789'),
(54, 4, 4, '4567890123456789', 4, 'CB', '2024-03-25', 'En cours', 'DEF123456789'),
(55, 5, 5, '5678901234567890', 5, 'CB', '2024-03-26', 'Livrée', 'GHI123456789'),
(56, 6, 1, '6789012345678901', 6, 'CB', '2024-03-27', 'En cours', 'JKL123456789'),
(57, 7, 2, '7890123456789012', 7, 'CB', '2024-03-28', 'En cours', 'MNO123456789'),
(58, 8, 3, '8901234567890123', 8, 'CB', '2024-03-29', 'Livrée', 'PQR123456789'),
(59, 9, 4, '9012345678901234', 9, 'CB', '2024-03-30', 'En cours', 'STU123456789'),
(60, 10, 5, '0123456789012345', 10, 'CB', '2024-03-31', 'En cours', 'VWX123456789'),
(61, 11, 1, '1234567890123456', 11, 'CB', '2024-04-01', 'Livrée', 'YZA123456789'),
(62, 12, 2, '2345678901234567', 12, 'CB', '2024-04-02', 'En cours', 'BCD123456789'),
(63, 13, 3, '3456789012345678', 13, 'CB', '2024-04-03', 'En cours', 'EFG123456789'),
(64, 14, 4, '4567890123456789', 14, 'CB', '2024-04-04', 'Livrée', 'HIJ123456789'),
(65, 15, 5, '5678901234567890', 15, 'CB', '2024-04-05', 'En cours', 'KLM123456789'),
(66, 16, 1, '6789012345678901', 16, 'CB', '2024-04-06', 'En cours', 'NOP123456789'),
(67, 17, 2, '7890123456789012', 17, 'CB', '2024-04-07', 'Livrée', 'QRS123456789'),
(68, 18, 3, '8901234567890123', 18, 'CB', '2024-04-08', 'En cours', 'TUV123456789'),
(69, 19, 4, '9012345678901234', 19, 'CB', '2024-04-09', 'En cours', 'WXY123456789'),
(70, 20, 5, '0123456789012345', 20, 'CB', '2024-04-10', 'Livrée', 'ZAB123456789'),
(71, 21, 1, '1234567890123456', 21, 'CB', '2024-04-11', 'En cours', 'CDE123456789'),
(72, 22, 2, '2345678901234567', 22, 'CB', '2024-04-12', 'Livrée', 'FGH123456789'),
(73, 23, 3, '3456789012345678', 23, 'CB', '2024-04-13', 'En cours', 'IJK123456789'),
(74, 24, 4, '4567890123456789', 24, 'CB', '2024-04-14', 'En cours', 'LMN123456789'),
(75, 25, 5, '5678901234567890', 25, 'CB', '2024-04-15', 'Livrée', 'OPQ123456789'),
(76, 26, 1, '6789012345678901', 26, 'CB', '2024-04-16', 'En cours', 'RST123456789'),
(77, 27, 2, '7890123456789012', 27, 'CB', '2024-04-17', 'En cours', 'UVW123456789'),
(78, 28, 3, '8901234567890123', 28, 'CB', '2024-04-18', 'Livrée', 'XYZ123456789'),
(79, 29, 4, '9012345678901234', 29, 'CB', '2024-04-19', 'En cours', 'ABC123456789'),
(80, 30, 5, '0123456789012345', 30, 'CB', '2024-04-20', 'En cours', 'DEF123456789'),
(81, 31, 1, '1234567890123456', 31, 'CB', '2024-04-21', 'Livrée', 'GHI123456789'),
(82, 32, 2, '2345678901234567', 32, 'CB', '2024-04-22', 'En cours', 'JKL123456789'),
(83, 33, 3, '3456789012345678', 33, 'CB', '2024-04-23', 'En cours', 'MNO123456789'),
(84, 34, 4, '4567890123456789', 34, 'CB', '2024-04-24', 'Livrée', 'PQR123456789'),
(85, 35, 5, '5678901234567890', 35, 'CB', '2024-04-25', 'En cours', 'STU123456789'),
(86, 36, 1, '6789012345678901', 36, 'CB', '2024-04-26', 'En cours', 'VWX123456789'),
(87, 37, 2, '7890123456789012', 37, 'CB', '2024-04-27', 'Livrée', 'YZA123456789'),
(88, 38, 3, '8901234567890123', 38, 'CB', '2024-04-28', 'En cours', 'BCD123456789'),
(89, 39, 4, '9012345678901234', 39, 'CB', '2024-04-29', 'En cours', 'EFG123456789'),
(90, 40, 5, '0123456789012345', 40, 'CB', '2024-04-30', 'Livrée', 'HIJ123456789'),
(91, 41, 1, '1234567890123456', 41, 'CB', '2024-05-01', 'En cours', 'KLM123456789'),
(92, 42, 2, '2345678901234567', 42, 'CB', '2024-05-02', 'Livrée', 'NOP123456789'),
(93, 43, 3, '3456789012345678', 43, 'CB', '2024-05-03', 'En cours', 'QRS123456789'),
(94, 44, 4, '4567890123456789', 44, 'CB', '2024-05-04', 'En cours', 'TUV123456789'),
(95, 45, 5, '5678901234567890', 45, 'CB', '2024-05-05', 'Livrée', 'WXY123456789'),
(96, 46, 1, '6789012345678901', 46, 'CB', '2024-05-06', 'En cours', 'ZAB123456789'),
(97, 47, 2, '7890123456789012', 47, 'CB', '2024-05-07', 'En cours', 'CDE123456789'),
(98, 48, 3, '8901234567890123', 48, 'CB', '2024-05-08', 'Livrée', 'FGH123456789'),
(99, 49, 4, '9012345678901234', 49, 'CB', '2024-05-09', 'En cours', 'IJK123456789'),
(100, 50, 5, '0123456789012345', 50, 'CB', '2024-05-10', 'En cours', 'LMN123456789');

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


INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('0123456789012345', 1); -- Chloe Garcia
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('1234567890123456', 2); -- Jean Dupont
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('2345678901234567', 3); -- Sophie Martin
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('3456789012345678', 4); -- Luc Bernard
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('4567890123456789', 5); -- Marie Durand
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('5678901234567890', 6); -- Paul Lefevre
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('6789012345678901', 7); -- Julie Moreau
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('7890123456789012', 8); -- Pierre Simon
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('8901234567890123', 9); -- Emma Laurent
INSERT INTO POSSEDERIP (NUMCB, IDCLIENT) VALUES ('9012345678901234', 10); -- Lucas Michel


INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (42, 1); -- Dupont, Jean à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (43, 2); -- Martin, Sophie à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (44, 3); -- Bernard, Luc à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (45, 4); -- Durand, Marie à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (46, 5); -- Lefevre, Paul à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (47, 6); -- Moreau, Julie à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (48, 7); -- Simon, Pierre à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (49, 8); -- Laurent, Emma à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (50, 9); -- Michel, Lucas à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (41, 10); -- Garcia, Chloe à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (1, 11); -- Dupont, Jean à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (2, 12); -- Martin, Sophie à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (3, 13); -- Bernard, Luc à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (4, 14); -- Durand, Marie à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (5, 15); -- Lefevre, Paul à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (6, 16); -- Moreau, Julie à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (7, 17); -- Simon, Pierre à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (8, 18); -- Laurent, Emma à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (9, 19); -- Michel, Lucas à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (10, 20); -- Garcia, Chloe à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (11, 21); -- Lefevre, Luc à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (12, 22); -- Lefevre, Paul à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (13, 23); -- Lefevre, Julie à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (14, 24); -- Lefevre, Pierre à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (15, 25); -- Lefevre, Simon à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (16, 26); -- Lefevre, Emma à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (17, 27); -- Lefevre, Marie à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (18, 28); -- Lefevre, Lucie à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (19, 29); -- Lefevre, Marc à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (20, 30); -- Lefevre, Paul à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (21, 31); -- Lefevre, Thomas à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (22, 32); -- Lefevre, Simon à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (23, 33); -- Lefevre, Sophie à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (24, 34); -- Lefevre, Marie à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (25, 35); -- Lefevre, Sophie à Marseille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (26, 36); -- Lefevre, Julie à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (27, 37); -- Lefevre, Marc à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (28, 38); -- Lefevre, Pierre à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (29, 39); -- Lefevre, Claire à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (30, 40); -- Lefevre, Thomas à Lille
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (31, 41); -- Lefevre, Thierry à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (32, 42); -- Lefevre, Pierre à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (33, 43); -- Lefevre, Claire à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (34, 44); -- Lefevre, Anne à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (35, 45); -- Lefevre, Thomas à Lyon
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (36, 46); -- Lefevre, Marie à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (37, 47); -- Lefevre, Marc à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (38, 48); -- Lefevre, Paul à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (39, 49); -- Lefevre, Claire à Paris
INSERT INTO POSSEDERADR (IDCLIENT, IDADRESSE) VALUES (40, 50); -- Lefevre, Sylvie à Paris

