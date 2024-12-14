<?php
// rechercheAvancee.php
// Recherche avec les critères dynamiquess
function rechercheAvancee($criteres, $pdo, $limit, $offset) {
    try {
        // Convertir les critères des prix en NULL si vides
        $prix_min = isset($criteres['prix_min']) && $criteres['prix_min'] !== '' ? (float)$criteres['prix_min'] : NULL;
        $prix_max = isset($criteres['prix_max']) && $criteres['prix_max'] !== '' ? (float)$criteres['prix_max'] : NULL;

        // Préparer l'appel à la procédure stockée
        $stmt = $pdo->prepare("CALL SP_RECHERCHE_AVANCEE(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock, :limit, :offset)");

        // Passer les paramètres correctement typés
        $stmt->bindValue(':mot_cle', $criteres['mot_cle'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':categorie', $criteres['categorie'] !== NULL ? (int)$criteres['categorie'] : NULL, $criteres['categorie'] !== NULL ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':marque', $criteres['marque'] ?? NULL, PDO::PARAM_STR);

        $stmt->bindValue(':prix_min', $prix_min, $prix_min === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $prix_max, $prix_max === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $stmt->bindValue(':en_stock', isset($criteres['en_stock']) && $criteres['en_stock'] !== NULL ? (int)$criteres['en_stock'] : NULL, PDO::PARAM_INT);

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        // Construire une requête avec les valeurs réelles pour le débogage
        $requeteDebug = sprintf(
            "CALL SP_RECHERCHE_AVANCEE(:mot_cle = '%s', :categorie = %s, :marque = '%s', :prix_min = %s, :prix_max = %s, :en_stock = %s, :limit = %d, :offset = %d)",
            $criteres['mot_cle'] ?? '',
            $criteres['categorie'] !== NULL ? (int)$criteres['categorie'] : "NULL",
            $criteres['marque'] ?? '',
            $prix_min !== NULL ? $prix_min : "NULL",
            $prix_max !== NULL ? $prix_max : "NULL",
            isset($criteres['en_stock']) ? (int)$criteres['en_stock'] : "NULL",
            (int)$limit,
            (int)$offset
        );

        // Afficher la requête pour le débogage
        echo "<h4>Requête avec les valeurs réelles :</h4><pre>$requeteDebug</pre>";

        // Exécuter la procédure stockée
        $stmt->execute();

        // Récupérer les résultats
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les résultats pour le débogage
        echo "<h4>Résultats :</h4><pre>" . print_r($result, true) . "</pre>";

        // Retourner les résultats
        return $result;
    } catch (PDOException $e) {
        // Gérer les erreurs SQL
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return [];
    }
}






// Fonction pour compter le total des produits
function countProduits($criteres, $pdo) {
    try {
        $prix_min = isset($criteres['prix_min']) && $criteres['prix_min'] !== '' ? (float)$criteres['prix_min'] : NULL;
        $prix_max = isset($criteres['prix_max']) && $criteres['prix_max'] !== '' ? (float)$criteres['prix_max'] : NULL;

        $stmt = $pdo->prepare("CALL SP_COUNT_PRODUITS(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock)");

        $stmt->bindValue(':mot_cle', $criteres['mot_cle'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':categorie', $criteres['categorie'] !== NULL ? (int)$criteres['categorie'] : NULL, PDO::PARAM_INT);
        $stmt->bindValue(':marque', $criteres['marque'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':prix_min', $prix_min, $prix_min === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $prix_max, $prix_max === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':en_stock', isset($criteres['en_stock']) && $criteres['en_stock'] !== NULL ? (int)$criteres['en_stock'] : NULL, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return 0;
    }
}

?>