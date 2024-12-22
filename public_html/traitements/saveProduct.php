<?php
session_start();
include("./../connect.inc.php");
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pdo->beginTransaction();

        // Insertion du produit de base
        $query = $pdo->prepare("
            INSERT INTO PRODUIT (
                NOMPROD, DESCPROD, PRIXHT, COULEUR, COMPOSITION, 
                POIDSPRODUIT, QTESTOCK, DISPONIBLE, IDMARQUE
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $query->execute([
            $_POST['nom'],
            $_POST['description'],
            $_POST['prix'],
            $_POST['couleur'],
            $_POST['composition'],
            $_POST['poids'],
            $_POST['stock'],
            $_POST['disponible'],
            $_POST['idMarque']
        ]);

        $idProd = $pdo->lastInsertId();

        // Gestion de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../images/';
            $imageFileName = 'prod' . $idProd . '.png';
            $uploadFile = $uploadDir . $imageFileName;

            // Vérification du type de fichier
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);
            
            if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes)) {
                throw new Exception('Format d\'image non supporté');
            }

            // Conversion en PNG si nécessaire
            $sourceImage = null;
            switch ($imageInfo['mime']) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceImage = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($_FILES['image']['tmp_name']);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($_FILES['image']['tmp_name']);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($_FILES['image']['tmp_name']);
                    break;
            }

            if ($sourceImage) {
                // Préserver la transparence
                imagesavealpha($sourceImage, true);
                imagepng($sourceImage, $uploadFile);
                imagedestroy($sourceImage);
            } else {
                throw new Exception('Erreur lors du traitement de l\'image');
            }
        }

        // Gestion des catégories mères
        if (isset($_POST['categories_meres']) && is_array($_POST['categories_meres'])) {
            $queryInsertCatMere = $pdo->prepare("
                INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES (?, ?)
            ");
            foreach ($_POST['categories_meres'] as $catMere) {
                $queryInsertCatMere->execute([$idProd, $catMere]);
            }
        }

        // Gestion de la catégorie principale
        if (!empty($_POST['categorie_principale'])) {
            $queryInsertCatPrincipale = $pdo->prepare("
                INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES (?, ?)
            ");
            $queryInsertCatPrincipale->execute([$idProd, $_POST['categorie_principale']]);

            // Gestion des catégories secondaires
            if (isset($_POST['categories_secondaires']) && is_array($_POST['categories_secondaires'])) {
                foreach ($_POST['categories_secondaires'] as $categId) {
                    // Vérifier que la catégorie est bien une sous-catégorie de la catégorie principale
                    $queryVerif = $pdo->prepare("
                        SELECT 1 FROM CATPERE 
                        WHERE IDCATEG_PERE = ? AND IDCATEG = ?
                    ");
                    $queryVerif->execute([$_POST['categorie_principale'], $categId]);
                    
                    if ($queryVerif->fetchColumn()) {
                        $queryInsertCatPrincipale->execute([$idProd, $categId]);
                    }
                }
            }
        }

        $pdo->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Produit ajouté avec succès',
            'idProd' => $idProd
        ]);

    } else {
        throw new Exception('Méthode non autorisée');
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 