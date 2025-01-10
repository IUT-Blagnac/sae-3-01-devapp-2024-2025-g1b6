<?php
session_start();
require_once('../connect.inc.php');

header('Content-Type: application/json');

if (!isset($_SESSION["admin"])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

try {
    if ($_GET['action'] === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $pdo->beginTransaction();

        // Mise à jour des informations de base du produit
        $query = $pdo->prepare("
            UPDATE PRODUIT SET 
                NOMPROD = ?,
                DESCPROD = ?,
                PRIXHT = ?,
                COULEUR = ?,
                COMPOSITION = ?,
                POIDSPRODUIT = ?,
                QTESTOCK = ?,
                DISPONIBLE = ?,
                IDMARQUE = ?
            WHERE IDPROD = ?
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
            $_POST['idMarque'],
            $_POST['idProd']
        ]);

        // Supprimer toutes les anciennes catégories du produit
        $queryDelete = $pdo->prepare("DELETE FROM APPARTENIRCATEG WHERE IDPROD = ?");
        $queryDelete->execute([$_POST['idProd']]);

        // Ajouter la catégorie principale
        if (!empty($_POST['categorie_principale'])) {
            $queryInsert = $pdo->prepare("INSERT INTO APPARTENIRCATEG (IDPROD, IDCATEG) VALUES (?, ?)");
            $queryInsert->execute([$_POST['idProd'], $_POST['categorie_principale']]);

            // Ajouter les catégories secondaires si elles existent
            if (isset($_POST['categories_secondaires']) && is_array($_POST['categories_secondaires'])) {
                foreach ($_POST['categories_secondaires'] as $categId) {
                    // Vérifier que la catégorie est bien une sous-catégorie de la catégorie principale
                    $queryVerif = $pdo->prepare("
                        SELECT 1 FROM CATPERE 
                        WHERE IDCATEG_PERE = ? AND IDCATEG = ?
                    ");
                    $queryVerif->execute([$_POST['categorie_principale'], $categId]);
                    
                    if ($queryVerif->fetchColumn()) {
                        $queryInsert->execute([$_POST['idProd'], $categId]);
                    }
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } else {
        throw new Exception('Action non valide');
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