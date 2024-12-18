package application.view;

import java.io.IOException;
import java.util.Collections;
import java.util.HashSet;
import java.util.Set;

import application.config.ConfigManager;
import application.LaunchApp;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.cell.CheckBoxListCell;
import javafx.stage.Stage;
import javafx.beans.property.BooleanProperty;
import javafx.beans.property.SimpleBooleanProperty;
import application.config.AppConstants;

/**
 * Contrôleur de la vue de configuration.
 * Gère les interactions entre l'interface utilisateur et les données de configuration.
 * @author Alex LOVIN
 */
public class configViewController {

    private final ConfigManager configManager = new ConfigManager();

    @FXML
    private TextField hote;

    @FXML
    private TextField frequence;

    @FXML
    private Button btnAlerte;

    @FXML
    private Button btnValider;

    @FXML
    private ListView<String> listCapteur;

    @FXML
    private ListView<String> listDataType; // Nouvelle ListView pour les types de données

    @FXML
    private RadioButton btnOuiCapteur;

    @FXML
    private RadioButton btnNonCapteur;

    @FXML
    private RadioButton btnOuiPanneau;

    @FXML
    private RadioButton btnNonPanneau;

    private final Set<String> selectedSalles = new HashSet<>();
    private final Set<String> selectedDataTypes = new HashSet<>(); // Stocke les types sélectionnés
    private boolean hasUnsavedChanges = false; // Variable pour détecter les modifications non sauvegardées

     /**
     * Méthode d'initialisation de la vue.
     * Elle charge les données de configuration, configure les composants et initialise les valeurs.
     * @author Alex LOVIN
     */
    @FXML
    private void initialize() {
        try {
            configManager.loadConfig(); // Charger la configuration
        } catch (IOException e) {
            e.printStackTrace();
        }

        // Charger les valeurs actuelles dans les champs texte
        String hostValue = configManager.readConfig("General.host");
        hote.setText(hostValue.isEmpty() ? "mqtt.iut-blagnac.fr" : hostValue); // Utiliser une valeur par défaut si vide

        String frequenceValue = configManager.readConfig("General.frequence");
        frequence.setText(frequenceValue.isEmpty() ? "1" : frequenceValue); // Valeur par défaut

        // Détection de modifications sur les champs texte (hôte et fréquence)
        hote.textProperty().addListener((observable, oldValue, newValue) -> {
            hasUnsavedChanges = !newValue.equals(oldValue);
            configManager.updateConfig("General.host", newValue);
        });

        frequence.textProperty().addListener((observable, oldValue, newValue) -> {
            hasUnsavedChanges = !newValue.equals(oldValue);
            configManager.updateConfig("General.frequence", newValue);
        });

        // Boutons radio pour capteurs
        String subscribeAllCapteurs = configManager.readConfig("Capteurs.subscribe");
        if ("on".equalsIgnoreCase(subscribeAllCapteurs)) {
            btnOuiCapteur.setSelected(true);
            listCapteur.setDisable(true);
        } else {
            btnNonCapteur.setSelected(true);
            listCapteur.setDisable(false);
        }

        // Boutons radio pour panneaux solaires
        String subscribeAllPanneaux = configManager.readConfig("Panneaux Solaires.subscribe");
        if ("on".equalsIgnoreCase(subscribeAllPanneaux)) {
            btnOuiPanneau.setSelected(true);
        } else {
            btnNonPanneau.setSelected(true);
        }

        // Charger les salles sélectionnées
        selectedSalles.clear();
        String existingSalles = configManager.readConfig("Capteurs.salles").trim();
        if (!existingSalles.equals("''")) { // Si ce n'est pas une chaîne vide, traiter les salles
            String[] salles = existingSalles.replace("'", "").split(",\\s*");
            Collections.addAll(selectedSalles, salles);
        }

        // Configurer la ListView pour les salles avec des CheckBox
        ObservableList<String> sallesCapteurs = AppConstants.SALLES_CAPTEURS; // Utiliser la liste depuis AppConstants
        FXCollections.sort(sallesCapteurs);
        listCapteur.setItems(sallesCapteurs);
        listCapteur.setCellFactory(CheckBoxListCell.forListView(item -> {
            BooleanProperty checked = new SimpleBooleanProperty(selectedSalles.contains(item));
            checked.addListener((obs, wasChecked, isNowChecked) -> {
                if (isNowChecked) {
                    selectedSalles.add(item);
                } else {
                    selectedSalles.remove(item);
                }
                hasUnsavedChanges = true; // Lorsque la sélection change, marquer comme modifiée
                updateSallesInConfig();
            });
            return checked;
        }));

        ObservableList<String> dataTypes = AppConstants.DATA_TYPE; // Utiliser la liste depuis AppConstants
        FXCollections.sort(dataTypes);
        listDataType.setItems(dataTypes);

        // Charger les dataType sélectionnés
        selectedDataTypes.clear();
        String existingDataTypes = configManager.readConfig("Capteurs.data_type").trim();
        if (!existingDataTypes.equals("''")) { // Si ce n'est pas une chaîne vide
            String[] dataTypesArray = existingDataTypes.replace("'", "").split(",\\s*");
            Collections.addAll(selectedDataTypes, dataTypesArray);
        }

        // Configurer la ListView pour les dataType avec des CheckBox
        listDataType.setCellFactory(CheckBoxListCell.forListView(item -> {
            BooleanProperty checked = new SimpleBooleanProperty(selectedDataTypes.contains(item));
            checked.addListener((obs, wasChecked, isNowChecked) -> {
                if (isNowChecked) {
                    selectedDataTypes.add(item);
                } else {
                    selectedDataTypes.remove(item);
                }
                hasUnsavedChanges = true; // Lorsque la sélection change, marquer comme modifiée
                updateDataTypesInConfig();
            });
            return checked;
        }));
    }


     /**
     * Met à jour les types de données sélectionnés dans la configuration.
     * @author Alex LOVIN
     */
    private void updateDataTypesInConfig() {
        if (selectedDataTypes.isEmpty()) {
            configManager.updateConfig("Capteurs.data_type", "''");
        } else {
            String updatedDataTypes = selectedDataTypes.stream()
                    .map(type -> "'" + type.trim() + "'")
                    .reduce((a, b) -> a + ", " + b)
                    .orElse("''");
            configManager.updateConfig("Capteurs.data_type", updatedDataTypes);
        }
    }

     /**
     * Gère la sélection des capteurs via les boutons radio.
     * Met à jour la configuration et désactive la liste de capteurs si nécessaire.
     * @author Alex LOVIN
     */
    @FXML
    private void handleSubscribeAllCapteurs() {
        boolean isOn = btnOuiCapteur.isSelected();
        configManager.updateConfig("Capteurs.subscribe", isOn ? "on" : "off");
        listCapteur.setDisable(isOn); // Désactiver la liste si "on"
        hasUnsavedChanges = true; // Marquer comme modifié
    }

     /**
     * Gère la sélection des panneaux solaires via les boutons radio.
     * Met à jour la configuration en conséquence.
     * @author Alex LOVIN
     */
    @FXML
    private void handleSubscribeAllPanneaux() {
        boolean isOn = btnOuiPanneau.isSelected();
        configManager.updateConfig("Panneaux Solaires.subscribe", isOn ? "on" : "off");
        hasUnsavedChanges = true; // Marquer comme modifié
    }

    /**
     * Gère la validation des modifications apportées par l'utilisateur.
     * Affiche une alerte en cas de champs invalides ou vides.
     * @author Alex LOVIN
     */
    @FXML
    private void handleValider() {
        String frequenceInput = frequence.getText().trim();
        String hoteInput = hote.getText().trim();

        // Vérification que les champs ne sont pas vides
        if (hoteInput.isEmpty() || frequenceInput.isEmpty()) {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur de validation");
            alert.setHeaderText("Champs requis manquants");
            alert.setContentText("Veuillez remplir les champs Hôte et Fréquence.");
            alert.showAndWait();
            return;
        }

        // Vérification que la fréquence est un entier positif
        if (!isPositiveInteger(frequenceInput)) {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur de validation");
            alert.setHeaderText("Valeur invalide pour la fréquence");
            alert.setContentText("La fréquence doit être un entier positif.");
            alert.showAndWait();
            return;
        }

        // Mettre à jour les configurations finales
        configManager.updateConfig("General.host", hoteInput);
        configManager.updateConfig("General.frequence", frequenceInput);

        // Sauvegarder la configuration dans le fichier
        saveConfig();

        // Confirmation
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle("Validation réussie");
        alert.setHeaderText(null);
        alert.setContentText("Les modifications ont été sauvegardées avec succès.");
        alert.showAndWait();

        // Réinitialiser l'indicateur de modifications non sauvegardées
        hasUnsavedChanges = false;
    }

    /**
     * Vérifie si une chaîne est un entier positif.
     * @param input la chaîne à vérifier
     * @return true si la chaîne est un entier positif, false sinon
     * @author Alex LOVIN
     */
    private boolean isPositiveInteger(String input) {
        try {
            int value = Integer.parseInt(input);
            return value > 0;
        } catch (NumberFormatException e) {
            return false;
        }
    }

    /**
     * Met à jour les salles sélectionnées dans la configuration.
     * @author Alex LOVIN
     */
    private void updateSallesInConfig() {
        if (selectedSalles.isEmpty()) {
            configManager.updateConfig("Capteurs.salles", "''");
        } else {
            String updatedSalles = selectedSalles.stream()
                    .map(salle -> "'" + salle.trim() + "'")
                    .reduce((a, b) -> a + ", " + b)
                    .orElse("''");
            configManager.updateConfig("Capteurs.salles", updatedSalles);
        }
    }
    /**
     * Sauvegarde les données de configuration dans le fichier approprié.
     * @author Alex LOVIN
     */
    private void saveConfig() {
        try {
            configManager.saveConfig();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    /**
     * Recharge les valeurs de la vue.
     * Utilisé pour rafraîchir les valeurs après la modification des seuils d'alerte.
     * @author Alex LOVIN
     */
    public void reloadView() {
        initialize(); // Recharger les valeurs depuis l'initialize
    }

    /**
     * Vérifie les champs requis avant de permettre la navigation.
     * Affiche une alerte en cas de champs invalides ou vides.
     * @return true si les champs sont valides, false sinon
     * @author Alex LOVIN
     */
    private boolean validateRequiredFields() {
        String frequenceInput = frequence.getText().trim();
        String hoteInput = hote.getText().trim();

        if (hoteInput.isEmpty() || frequenceInput.isEmpty()) {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur de validation");
            alert.setHeaderText("Champs requis manquants");
            alert.setContentText("Veuillez remplir les champs Hôte et Fréquence.");
            alert.showAndWait();
            return false;
        }

        if (!isPositiveInteger(frequenceInput)) {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur de validation");
            alert.setHeaderText("Valeur invalide pour la fréquence");
            alert.setContentText("La fréquence doit être un entier positif.");
            alert.showAndWait();
            return false;
        }

        return true;
    }

    /**
     * Gère l'ouverture de la vue des seuils d'alerte.
     * Vérifie les champs requis avant de permettre la navigation.
     * @author Alex LOVIN
     */
    @FXML
    private void handleOpenAlerte() {
        if (!validateRequiredFields()) {
            return; // Arrêter si les champs requis ne sont pas valides
        }

        if (hasUnsavedChanges) {
            Alert alert = new Alert(AlertType.CONFIRMATION);
            alert.setTitle("Modifications non sauvegardées");
            alert.setHeaderText("Vous avez des modifications non sauvegardées.");
            alert.setContentText("Voulez-vous enregistrer avant de quitter ?");
            ButtonType btnSave = new ButtonType("Enregistrer");
            ButtonType btnCancel = new ButtonType("Annuler");
            ButtonType btnDiscard = new ButtonType("Ignorer");

            alert.getButtonTypes().setAll(btnSave, btnCancel, btnDiscard);

            alert.showAndWait().ifPresent(response -> {
                if (response == btnSave) {
                    saveConfig();
                    hasUnsavedChanges = false;
                    openAlertePage();
                } else if (response == btnDiscard) {
                    openAlertePage();
                }
            });
        } else {
            openAlertePage();
        }
    }

    /**
     * Ouvre la vue des seuils d'alerte.
     *  @author Alex LOVIN
     */
    private void openAlertePage() {
        try {
            FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/seuilsAlerteView.fxml"));
            Parent alertePage = fxmlLoader.load();

            Stage stage = new Stage();
            stage.setTitle("Seuils Alerte");
            stage.setScene(new Scene(alertePage));

            stage.show();

            ((Stage) btnAlerte.getScene().getWindow()).close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    /**
     * Ferme la vue actuelle et retourne à la vue précédente.
     * * @author Alex LOVIN
     */
    @FXML
    private void onBtnRetour() {
        if (!validateRequiredFields()) {
            return; // Arrêter si les champs requis ne sont pas valides
        }

        if (hasUnsavedChanges) {
            Alert alert = new Alert(AlertType.CONFIRMATION);
            alert.setTitle("Modifications non sauvegardées");
            alert.setHeaderText("Vous avez des modifications non sauvegardées.");
            alert.setContentText("Voulez-vous enregistrer avant de quitter ?");
            ButtonType btnSave = new ButtonType("Enregistrer");
            ButtonType btnCancel = new ButtonType("Annuler");
            ButtonType btnDiscard = new ButtonType("Ignorer");

            alert.getButtonTypes().setAll(btnSave, btnCancel, btnDiscard);

            alert.showAndWait().ifPresent(response -> {
                if (response == btnSave) {
                    saveConfig();
                    hasUnsavedChanges = false;
                    closeCurrentPage();
                } else if (response == btnDiscard) {
                    closeCurrentPage();
                }
            });
        } else {
            closeCurrentPage();
        }
    }
    /**
     * Ferme la page actuelle.
     * @author Alex LOVIN
     */
    private void closeCurrentPage() {
        ((Stage) this.btnAlerte.getScene().getWindow()).close();
    }

}
