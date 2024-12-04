# -----------------------------------------------------------------------------
#       TABLE : TRANSPORTEUR
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TRANSPORTEUR
 (
   IDTRANSPORTEUR INTEGER(5) NOT NULL  ,
   TYPEEXP VARCHAR(30) NOT NULL  ,
   FRAISEXP DECIMAL(6,2) NOT NULL  ,
   FRAISKG DECIMAL(5,2) NOT NULL  ,
   DELAILIVRAISON INTEGER(3) NULL  
   , PRIMARY KEY (IDTRANSPORTEUR) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : PACK
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS PACK
 (
   IDPACK INTEGER(5) NOT NULL  ,
   NOMPACK VARCHAR(30) NOT NULL  ,
   DESCPACK VARCHAR(150) NULL  
   , PRIMARY KEY (IDPACK) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : ADMINISTRATEUR
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ADMINISTRATEUR
 (
   EMAIL VARCHAR(128) NOT NULL  ,
   PASSWORD VARCHAR(128) NULL  
   , PRIMARY KEY (EMAIL) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MARQUE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MARQUE
 (
   IDMARQUE INTEGER(5) NOT NULL  ,
   NOMMARQUE VARCHAR(30) NOT NULL  ,
   DESCMARQUE VARCHAR(150) NULL  
   , PRIMARY KEY (IDMARQUE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : INFORMATIONPAIEMENT
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS INFORMATIONPAIEMENT
 (
   NUMCB VARCHAR(19) NOT NULL  ,
   NOMCOMPLETCB VARCHAR(50) NOT NULL  ,
   DATEEXP DATE NOT NULL  ,
   CRYPTOGRAMME INTEGER(4) NOT NULL  
   , PRIMARY KEY (NUMCB) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : COMMANDE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS COMMANDE
 (
   NUMCOMMANDE INTEGER(9) NOT NULL  ,
   IDCLIENT INTEGER(7) NOT NULL  ,
   IDTRANSPORTEUR INTEGER(5) NOT NULL  ,
   NUMCB VARCHAR(19) NULL  ,
   IDADRESSE INTEGER(7) NOT NULL  ,
   TYPEREGLEMENT VARCHAR(6) NOT NULL  ,
   DATECOMMANDE DATE NOT NULL  ,
   STATUTLIVRAISON VARCHAR(128) NOT NULL  ,
   CODESUIVI VARCHAR(255) NULL  
   , PRIMARY KEY (NUMCOMMANDE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE COMMANDE
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_COMMANDE_CLIENT
     ON COMMANDE (IDCLIENT ASC);

CREATE  INDEX I_FK_COMMANDE_TRANSPORTEUR
     ON COMMANDE (IDTRANSPORTEUR ASC);

CREATE  INDEX I_FK_COMMANDE_INFORMATIONPAIEMENT
     ON COMMANDE (NUMCB ASC);

CREATE  INDEX I_FK_COMMANDE_ADRESSE
     ON COMMANDE (IDADRESSE ASC);

# -----------------------------------------------------------------------------
#       TABLE : ADRESSE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ADRESSE
 (
   IDADRESSE INTEGER(7) NOT NULL  ,
   NUMRUE VARCHAR(10) NOT NULL  ,
   NOMRUE VARCHAR(60) NOT NULL  ,
   COMPLEMENTADR VARCHAR(60) NULL  ,
   NOMVILLE VARCHAR(30) NOT NULL  ,
   CODEPOSTAL INTEGER(8) NOT NULL  ,
   PAYS VARCHAR(30) NOT NULL  
   , PRIMARY KEY (IDADRESSE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : CLIENT
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS CLIENT
 (
   IDCLIENT INTEGER(7) NOT NULL  ,
   NOMCLIENT VARCHAR(20) NOT NULL  ,
   PRENOMCLIENT VARCHAR(15) NOT NULL  ,
   NUMTEL VARCHAR(15) NULL  ,
   EMAIL VARCHAR(150) NOT NULL  ,
   PASSWORD VARCHAR(128) NOT NULL  ,
   DATEN DATE NOT NULL  ,
   DATEINSCRIPTION DATE NOT NULL  
   , PRIMARY KEY (IDCLIENT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : PRODUIT
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS PRODUIT
 (
   IDPROD INTEGER(7) NOT NULL  ,
   IDMARQUE INTEGER(5) NOT NULL  ,
   NOMPROD VARCHAR(30) NOT NULL  ,
   DESCPROD VARCHAR(150) NULL  ,
   PRIXHT DECIMAL(6,2) NOT NULL  ,
   COULEUR VARCHAR(20) NULL  ,
   COMPOSITION VARCHAR(150) NULL  ,
   POIDSPRODUIT DECIMAL(5,2) NOT NULL  ,
   QTESTOCK INTEGER(5) NOT NULL  
   , PRIMARY KEY (IDPROD) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE PRODUIT
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_PRODUIT_MARQUE
     ON PRODUIT (IDMARQUE ASC);

# -----------------------------------------------------------------------------
#       TABLE : CATEGORIE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS CATEGORIE
 (
   IDCATEG INTEGER(5) NOT NULL  ,
   IDCATEG_CATPERE INTEGER(5) NULL  ,
   NOMCATEG VARCHAR(30) NOT NULL  ,
   DESCCATEG VARCHAR(50) NULL  
   , PRIMARY KEY (IDCATEG) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE CATEGORIE
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_CATEGORIE_CATEGORIE
     ON CATEGORIE (IDCATEG_CATPERE ASC);

# -----------------------------------------------------------------------------
#       TABLE : APPARTENIRCATEG
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS APPARTENIRCATEG
 (
   IDPROD INTEGER(7) NOT NULL  ,
   IDCATEG INTEGER(5) NOT NULL  
   , PRIMARY KEY (IDPROD,IDCATEG) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE APPARTENIRCATEG
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_APPARTENIRCATEG_PRODUIT
     ON APPARTENIRCATEG (IDPROD ASC);

CREATE  INDEX I_FK_APPARTENIRCATEG_CATEGORIE
     ON APPARTENIRCATEG (IDCATEG ASC);

# -----------------------------------------------------------------------------
#       TABLE : AVIS
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS AVIS
 (
   IDCLIENT INTEGER(7) NOT NULL  ,
   IDPROD INTEGER(7) NOT NULL  ,
   DESCAVIS VARCHAR(750) NOT NULL  
   , PRIMARY KEY (IDCLIENT,IDPROD) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE AVIS
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_AVIS_CLIENT
     ON AVIS (IDCLIENT ASC);

CREATE  INDEX I_FK_AVIS_PRODUIT
     ON AVIS (IDPROD ASC);

# -----------------------------------------------------------------------------
#       TABLE : PANIER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS PANIER
 (
   IDCLIENT INTEGER(7) NOT NULL  ,
   IDPROD INTEGER(7) NOT NULL  ,
   QUANTITEPROD INTEGER(5) NOT NULL  
   , PRIMARY KEY (IDCLIENT,IDPROD) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE PANIER
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_PANIER_CLIENT
     ON PANIER (IDCLIENT ASC);

CREATE  INDEX I_FK_PANIER_PRODUIT
     ON PANIER (IDPROD ASC);

# -----------------------------------------------------------------------------
#       TABLE : SOUHAITER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SOUHAITER
 (
   IDCLIENT INTEGER(7) NOT NULL  ,
   IDPROD INTEGER(7) NOT NULL  
   , PRIMARY KEY (IDCLIENT,IDPROD) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE SOUHAITER
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_SOUHAITER_CLIENT
     ON SOUHAITER (IDCLIENT ASC);

CREATE  INDEX I_FK_SOUHAITER_PRODUIT
     ON SOUHAITER (IDPROD ASC);

# -----------------------------------------------------------------------------
#       TABLE : POSSEDERIP
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS POSSEDERIP
 (
   NUMCB VARCHAR(19) NOT NULL  ,
   IDCLIENT INTEGER(7) NOT NULL  
   , PRIMARY KEY (NUMCB,IDCLIENT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE POSSEDERIP
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_POSSEDERIP_INFORMATIONPAIEMENT
     ON POSSEDERIP (NUMCB ASC);

CREATE  INDEX I_FK_POSSEDERIP_CLIENT
     ON POSSEDERIP (IDCLIENT ASC);

# -----------------------------------------------------------------------------
#       TABLE : POSSEDERADR
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS POSSEDERADR
 (
   IDADRESSE INTEGER(7) NOT NULL  ,
   IDCLIENT INTEGER(7) NOT NULL  
   , PRIMARY KEY (IDADRESSE,IDCLIENT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE POSSEDERADR
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_POSSEDERADR_ADRESSE
     ON POSSEDERADR (IDADRESSE ASC);

CREATE  INDEX I_FK_POSSEDERADR_CLIENT
     ON POSSEDERADR (IDCLIENT ASC);

# -----------------------------------------------------------------------------
#       TABLE : ASSOPACK
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ASSOPACK
 (
   IDPROD INTEGER(7) NOT NULL  ,
   IDPACK INTEGER(5) NOT NULL  
   , PRIMARY KEY (IDPROD,IDPACK) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE ASSOPACK
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_ASSOPACK_PRODUIT
     ON ASSOPACK (IDPROD ASC);

CREATE  INDEX I_FK_ASSOPACK_PACK
     ON ASSOPACK (IDPACK ASC);


# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------


ALTER TABLE COMMANDE 
  ADD FOREIGN KEY FK_COMMANDE_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE COMMANDE 
  ADD FOREIGN KEY FK_COMMANDE_TRANSPORTEUR (IDTRANSPORTEUR)
      REFERENCES TRANSPORTEUR (IDTRANSPORTEUR) ;


ALTER TABLE COMMANDE 
  ADD FOREIGN KEY FK_COMMANDE_INFORMATIONPAIEMENT (NUMCB)
      REFERENCES INFORMATIONPAIEMENT (NUMCB) ;


ALTER TABLE COMMANDE 
  ADD FOREIGN KEY FK_COMMANDE_ADRESSE (IDADRESSE)
      REFERENCES ADRESSE (IDADRESSE) ;


ALTER TABLE PRODUIT 
  ADD FOREIGN KEY FK_PRODUIT_MARQUE (IDMARQUE)
      REFERENCES MARQUE (IDMARQUE) ;


ALTER TABLE CATEGORIE 
  ADD FOREIGN KEY FK_CATEGORIE_CATEGORIE (IDCATEG_CATPERE)
      REFERENCES CATEGORIE (IDCATEG) ;


ALTER TABLE APPARTENIRCATEG 
  ADD FOREIGN KEY FK_APPARTENIRCATEG_PRODUIT (IDPROD)
      REFERENCES PRODUIT (IDPROD) ;


ALTER TABLE APPARTENIRCATEG 
  ADD FOREIGN KEY FK_APPARTENIRCATEG_CATEGORIE (IDCATEG)
      REFERENCES CATEGORIE (IDCATEG) ;


ALTER TABLE AVIS 
  ADD FOREIGN KEY FK_AVIS_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE AVIS 
  ADD FOREIGN KEY FK_AVIS_PRODUIT (IDPROD)
      REFERENCES PRODUIT (IDPROD) ;


ALTER TABLE PANIER 
  ADD FOREIGN KEY FK_PANIER_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE PANIER 
  ADD FOREIGN KEY FK_PANIER_PRODUIT (IDPROD)
      REFERENCES PRODUIT (IDPROD) ;


ALTER TABLE SOUHAITER 
  ADD FOREIGN KEY FK_SOUHAITER_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE SOUHAITER 
  ADD FOREIGN KEY FK_SOUHAITER_PRODUIT (IDPROD)
      REFERENCES PRODUIT (IDPROD) ;


ALTER TABLE POSSEDERIP 
  ADD FOREIGN KEY FK_POSSEDERIP_INFORMATIONPAIEMENT (NUMCB)
      REFERENCES INFORMATIONPAIEMENT (NUMCB) ;


ALTER TABLE POSSEDERIP 
  ADD FOREIGN KEY FK_POSSEDERIP_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE POSSEDERADR 
  ADD FOREIGN KEY FK_POSSEDERADR_ADRESSE (IDADRESSE)
      REFERENCES ADRESSE (IDADRESSE) ;


ALTER TABLE POSSEDERADR 
  ADD FOREIGN KEY FK_POSSEDERADR_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE ASSOPACK 
  ADD FOREIGN KEY FK_ASSOPACK_PRODUIT (IDPROD)
      REFERENCES PRODUIT (IDPROD) ;


ALTER TABLE ASSOPACK 
  ADD FOREIGN KEY FK_ASSOPACK_PACK (IDPACK)
      REFERENCES PACK (IDPACK) ;