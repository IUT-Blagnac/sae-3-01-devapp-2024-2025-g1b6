<?php
$stmt = $pdo->prepare("SELECT * FROM CATEGORIE WHERE IDCATEG_CATPERE IS NULL");
$stmt->execute();
$categories = $stmt->fetchAll();
foreach ($categories as $categorie) {
    echo "<li><a href='categorie.php?id=" . $categorie['IDCATEG'] . "'>" . $categorie['NOMCATEG'] . "</a></li>";
}
?>