<?php
// Inclure la connexion à la base de données
include("connect.inc.php");

// Fonction pour vérifier l'existence de la table
function tableExiste($nomTable, $pdo) {
    try {
        $result = $pdo->query("DESCRIBE $nomTable");
        return $result !== false;
    } catch (PDOException $e) {
        return false;
    }
}

// Fonction de recherche avancée
function rechercheAvancee($termes, $pdo) {
    // Vérifier si la table "PRODUIT" existe
    if (!tableExiste('PRODUIT', $pdo)) {
        return null; // Retourner null si la table est absente
    }

    // Nettoyer et normaliser les termes
    $termes = strtolower(trim($termes));
    $mots = explode(" ", $termes); // Découper en mots

    // Construire une clause SQL pour les correspondances flexibles
    $conditions = [];
    $params = [];
    foreach ($mots as $index => $mot) {
        $conditions[] = "LOWER(NOMPROD) LIKE :mot$index";
        $params[":mot$index"] = "%$mot%";
    }
    $whereClause = implode(" OR ", $conditions);

    // Requête SQL pour rechercher les produits
    $sql = "
        SELECT *
        FROM PRODUIT
        WHERE $whereClause
        ORDER BY NOMPROD ASC
        LIMIT 20;
    ";

    // Exécuter la requête préparée
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Récupérer les résultats
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Initialisation des variables pour éviter les erreurs si aucune recherche n'est effectuée
$termes = '';
$produits = [];

// Vérifier si le formulaire a été soumis avec un terme de recherche
if (isset($_GET['recherche'])) {
    $termes = $_GET['recherche']; // Récupérer la valeur saisie par l'utilisateur

    // Appeler la fonction de recherche avancée avec le terme
    $produits = rechercheAvancee($termes, $pdo);
}
?>

<!-- Formulaire de recherche -->
<form method="get" action="">
    <input class="barreRecherche" type="text" name="recherche" 
           placeholder="Barre de recherche ..." 
           value="<?php echo htmlspecialchars($termes); ?>" 
           onkeydown="if(event.key === 'Enter') this.form.submit();">
</form>

<!-- Affichage des résultats de recherche -->
<?php if (!empty($termes)) : ?>
    <section class="resultatsRecherche">
        <h2>Résultats de recherche pour : <?php echo htmlspecialchars($termes); ?></h2>

        <?php if ($produits === null) : ?>
            <p>Erreur : La table "PRODUIT" est absente. Veuillez vérifier la base de données.</p>
        <?php elseif ($produits) : ?>
            <ul>
                <?php foreach ($produits as $produit) : ?>
                    <li>
                        <a href="produit.php?id=<?php echo $produit['IDPROD']; ?>">
                            <?php echo htmlspecialchars($produit['NOMPROD']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>Aucun produit trouvé.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>
