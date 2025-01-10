<?php
// rechercheAvancee.php
// Recherche avec les critères dynamiques
function rechercheAvancee($criteres, $pdo, $limit, $offset)
{
    try {
        // Validation des critères
        $criteres['mot_cle'] = $criteres['mot_cle'] ?? '';
        $criteres['categorie'] = empty($criteres['categorie']) ? NULL : (int)$criteres['categorie'];  // Catégorie vide devient NULL
        $criteres['marque'] = empty($criteres['marque']) ? NULL : $criteres['marque'];  // Marque vide devient NULL
        $criteres['prix_min'] = !empty($criteres['prix_min']) ? (float)$criteres['prix_min'] : NULL;
        $criteres['prix_max'] = !empty($criteres['prix_max']) ? (float)$criteres['prix_max'] : NULL;
        $criteres['en_stock'] = isset($criteres['en_stock']) ? 1 : NULL;  // 1 si coché, NULL si non
        $criteres['tri'] = isset($criteres['tri']) && in_array($criteres['tri'], ['nom_asc', 'nom_desc', 'prix_asc', 'prix_desc']) ? $criteres['tri'] : 'nom_asc';

        // Préparer la requête avec la procédure stockée
        $stmt = $pdo->prepare("CALL SP_RECHERCHE_AVANCEE(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock, :limit_count, :offset_count, :tri)");

        // Passer les paramètres
        $stmt->bindValue(':mot_cle', $criteres['mot_cle'], PDO::PARAM_STR);
        $stmt->bindValue(':categorie', $criteres['categorie'], PDO::PARAM_INT);
        $stmt->bindValue(':marque', $criteres['marque'], PDO::PARAM_STR);
        $stmt->bindValue(':prix_min', $criteres['prix_min'], PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $criteres['prix_max'], PDO::PARAM_STR);
        $stmt->bindValue(':en_stock', $criteres['en_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':limit_count', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset_count', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':tri', $criteres['tri'], PDO::PARAM_STR);

        // Exécuter la requête
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retourner les résultats
        return $resultats;
    } catch (PDOException $e) {
        // Gérer les erreurs SQL
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return [];
    }
}

// Fonction pour compter le total des produits
function countProduits($criteres, $pdo)
{
    try {
        // Conversion des critères de prix
        $prix_min = !empty($criteres['prix_min']) ? (float)$criteres['prix_min'] : NULL;
        $prix_max = !empty($criteres['prix_max']) ? (float)$criteres['prix_max'] : NULL;

        // Préparer l'appel à la procédure stockée
        $stmt = $pdo->prepare("CALL SP_COUNT_PRODUITS(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock)");

        // Binder les paramètres dynamiquement
        $stmt->bindValue(':mot_cle', $criteres['mot_cle'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':categorie', isset($criteres['categorie']) && $criteres['categorie'] !== '' ? (int)$criteres['categorie'] : NULL, PDO::PARAM_INT);
        $stmt->bindValue(':marque', $criteres['marque'] ?? NULL, PDO::PARAM_STR);
        $stmt->bindValue(':prix_min', $prix_min, is_null($prix_min) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $prix_max, is_null($prix_max) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':en_stock', isset($criteres['en_stock']) ? (int)$criteres['en_stock'] : NULL, PDO::PARAM_INT);

        // Exécuter la procédure
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourner le total ou 0 si non défini
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        // Afficher les erreurs SQL pour débogage
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return 0;
    }
}
?>