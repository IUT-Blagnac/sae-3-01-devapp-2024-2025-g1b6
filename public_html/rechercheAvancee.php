<?php
// rechercheAvancee.php
// Recherche avec les critères dynamiques
function rechercheAvancee($criteres, $pdo, $limit, $offset)
{
    try {
        // Convertir les critères des prix en NULL si vides
        $prix_min = isset($criteres['prix_min']) && $criteres['prix_min'] !== '' ? (float)$criteres['prix_min'] : NULL;
        $prix_max = isset($criteres['prix_max']) && $criteres['prix_max'] !== '' ? (float)$criteres['prix_max'] : NULL;

        // Préparer l'appel à la procédure stockée
        $stmt = $pdo->prepare("CALL SP_RECHERCHE_AVANCEE(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock, :limit, :offset)");

        // Passer les paramètres correctement typés
        $stmt->bindValue(':mot_cle', $criteres['mot_cle'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(
            ':categorie',
            isset($criteres['categorie']) && $criteres['categorie'] !== '' ? (int)$criteres['categorie'] : NULL,
            isset($criteres['categorie']) && $criteres['categorie'] !== '' ? PDO::PARAM_INT : PDO::PARAM_NULL
        );

        $stmt->bindValue(':marque', $criteres['marque'] !== '' ? $criteres['marque'] : NULL, PDO::PARAM_STR);
        $stmt->bindValue(':prix_min', $prix_min, $prix_min === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $prix_max, $prix_max === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':en_stock', $criteres['en_stock'] !== NULL ? (int)$criteres['en_stock'] : NULL, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        // Débogage : Requête simulée
        $requeteDebug = sprintf(
            "CALL SP_RECHERCHE_AVANCEE(:mot_cle = '%s', :categorie = %s, :marque = '%s', :prix_min = %s, :prix_max = %s, :en_stock = %s, :limit = %d, :offset = %d)",
            $criteres['mot_cle'] ?? '',
            $criteres['categorie'] !== '' ? (int)$criteres['categorie'] : "NULL",
            $criteres['marque'] ?? "NULL",
            $prix_min !== NULL ? $prix_min : "NULL",
            $prix_max !== NULL ? $prix_max : "NULL",
            $criteres['en_stock'] !== NULL ? (int)$criteres['en_stock'] : "NULL",
            (int)$limit,
            (int)$offset
        );

        // Exécuter la procédure stockée
        $stmt->execute();

        // Récupérer les résultats
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
