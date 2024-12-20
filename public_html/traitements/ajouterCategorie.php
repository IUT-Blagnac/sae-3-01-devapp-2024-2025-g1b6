<?php
session_start();
require_once('../connect.inc.php');

header('Content-Type: application/json');

try {
    // Vérification des données reçues
    if (!isset($_POST['nom']) || empty($_POST['nom']) || !isset($_POST['niveau']) || empty($_POST['niveau'])) {
        throw new Exception('Données manquantes');
    }

    $nom = trim($_POST['nom']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $niveau = $_POST['niveau'];

    // Vérifier si le nom de la catégorie existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM CATEGORIE WHERE NOMCATEG = ?");
    $stmt->execute([$nom]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Une catégorie avec ce nom existe déjà');
    }

    // Début de la transaction
    $pdo->beginTransaction();

    // Insertion de la nouvelle catégorie
    $stmt = $pdo->prepare("
        INSERT INTO CATEGORIE (NOMCATEG, DESCCATEG) 
        VALUES (?, ?)
    ");
    $stmt->execute([$nom, $description]);
    $newCategoryId = $pdo->lastInsertId();

    // Gestion des relations selon le niveau
    if ($niveau === 'Principale') {
        if (!isset($_POST['categories_meres']) || !is_array($_POST['categories_meres'])) {
            throw new Exception('Veuillez sélectionner au moins une catégorie mère');
        }

        $stmt = $pdo->prepare("
            INSERT INTO CATPERE (IDCATEG, IDCATEG_PERE) 
            VALUES (?, ?)
        ");

        foreach ($_POST['categories_meres'] as $idMere) {
            $stmt->execute([$newCategoryId, $idMere]);
        }
    } 
    elseif ($niveau === 'Secondaire') {
        if (!isset($_POST['categories_principales']) || !is_array($_POST['categories_principales'])) {
            throw new Exception('Veuillez sélectionner au moins une catégorie principale');
        }

        $stmt = $pdo->prepare("
            INSERT INTO CATPERE (IDCATEG, IDCATEG_PERE) 
            VALUES (?, ?)
        ");

        foreach ($_POST['categories_principales'] as $idPrincipale) {
            $stmt->execute([$newCategoryId, $idPrincipale]);
        }
    }

    // Validation de la transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Catégorie ajoutée avec succès',
        'categoryId' => $newCategoryId
    ]);

} catch (Exception $e) {
    // En cas d'erreur, annulation de la transaction
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 