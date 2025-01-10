DELIMITER $$
CREATE DEFINER=`R2024MYSAE3012`@`%` PROCEDURE `SP_RECHERCHE_AVANCEE`(
    IN `mot_cle` VARCHAR(255),
    IN `categorie` INT,
    IN `marque` VARCHAR(255),
    IN `prix_min` DECIMAL(10,2),
    IN `prix_max` DECIMAL(10,2),
    IN `en_stock` BOOLEAN,
    IN `limit_count` INT,
    IN `offset_count` INT,
    IN `tri` VARCHAR(50)
)
BEGIN
    SELECT DISTINCT 
        p.IDPROD, 
        p.NOMPROD, 
        p.DESCPROD, 
        p.COULEUR,
        p.PRIXHT, 
        p.QTESTOCK, 
        m.NOMMARQUE
    FROM PRODUIT p
    LEFT JOIN MARQUE m ON p.IDMARQUE = m.IDMARQUE
    LEFT JOIN APPARTENIRCATEG ap ON ap.IDPROD = p.IDPROD
    LEFT JOIN CATEGORIE c ON ap.IDCATEG = c.IDCATEG
    WHERE 
        (mot_cle IS NULL OR 
         p.NOMPROD LIKE CONCAT('%', mot_cle, '%') OR
         p.DESCPROD LIKE CONCAT('%', mot_cle, '%') OR
         p.COULEUR LIKE CONCAT('%', mot_cle, '%') OR
         m.NOMMARQUE LIKE CONCAT('%', mot_cle, '%'))
        AND (c.IDCATEG = IFNULL(categorie, c.IDCATEG) OR categorie IS NULL)
        AND (m.NOMMARQUE = IFNULL(marque, m.NOMMARQUE) OR marque IS NULL)
        AND (p.PRIXHT >= IFNULL(prix_min, p.PRIXHT) OR prix_min IS NULL)
        AND (p.PRIXHT <= IFNULL(prix_max, p.PRIXHT) OR prix_max IS NULL)
        AND (p.QTESTOCK > 0 OR en_stock IS NULL)
    ORDER BY 
        CASE 
            WHEN tri = 'nom_asc' THEN p.NOMPROD
            WHEN tri = 'prix_asc' THEN p.PRIXHT
        END ASC,
        CASE 
            WHEN tri = 'nom_desc' THEN p.NOMPROD
            WHEN tri = 'prix_desc' THEN p.PRIXHT
        END DESC
    LIMIT limit_count OFFSET offset_count;
END$$
DELIMITER ;
