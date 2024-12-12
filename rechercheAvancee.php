<?php
// rechercheAvancee.php
// Recherche avec les critères dynamiquess
function rechercheAvancee($criteres, $pdo, $limit, $offset) {
    try {
        // Vérifier les critères des prix et les convertir en NULL si vides
        $prix_min = (isset($criteres['prix_min']) && $criteres['prix_min'] !== '') ? (float)$criteres['prix_min'] : NULL;
        $prix_max = (isset($criteres['prix_max']) && $criteres['prix_max'] !== '') ? (float)$criteres['prix_max'] : NULL;

        // Appel de la procédure stockée pour la recherche
        $stmt = $pdo->prepare("CALL SP_RECHERCHE_AVANCEE(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock, :limit, :offset)");

        // Passage des paramètres dynamiques
        $stmt->bindValue(':mot_cle', $criteres['mot_cle'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':categorie', $criteres['categorie'] ?? NULL, PDO::PARAM_INT);
        $stmt->bindValue(':marque', $criteres['marque'] ?? NULL, PDO::PARAM_STR);
        
        // Lier les valeurs des prix ou NULL si vides
        $stmt->bindValue(':prix_min', $prix_min, $prix_min === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $prix_max, $prix_max === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);

        
        // Lier la disponibilité en stock (NULL si vide)
        $stmt->bindValue(':en_stock', isset($criteres['en_stock']) ? $criteres['en_stock'] : NULL, PDO::PARAM_INT);

        // Limite et offset pour la pagination
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        // Exécution de la requête
        $stmt->execute();

        // Récupérer les résultats
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les critères pour le débogage
        echo "<h4>Critères :</h4><pre>" . print_r($criteres, true) . "</pre>";
        echo "Requête exécutée : " . $stmt->queryString . "<br><br>";

        // Afficher les résultats
        echo "<h4>Résultats :</h4><pre>" . print_r($result, true) . "</pre>";

        // Retourner les résultats
        return $result;
    } catch (PDOException $e) {
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return [];
    }
}



// Fonction pour compter le total des produits
function countProduits($criteres, $pdo) {
    try {
        // Appel de la procédure stockée pour compter les produits
        $stmt = $pdo->prepare("CALL SP_COUNT_PRODUITS(:mot_cle, :categorie, :marque, :prix_min, :prix_max, :en_stock)");
        
        // Passage des paramètres
        $stmt->bindValue(':mot_cle', $criteres['mot_cle'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':categorie', $criteres['categorie'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':marque', $criteres['marque'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':prix_min', $criteres['prix_min'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':prix_max', $criteres['prix_max'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':en_stock', $criteres['en_stock'] ?? 0, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        echo "<h4>Erreur SQL :</h4><pre>" . $e->getMessage() . "</pre>";
        return 0;
    }
}
?>
