package application.view;

import java.io.IOException;
import java.util.Collections;
import java.util.HashSet;
import java.util.Set;

import application.ConfigManager;
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
import application.AppConstants;

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
    private RadioButton btnOuiCapteur;

    @FXML
    private RadioButton btnNonCapteur;

    @FXML
    private RadioButton btnOuiPanneau;

    @FXML
    private RadioButton btnNonPanneau;

    private final Set<String> selectedSalles = new HashSet<>();
    private boolean hasUnsavedChanges = false; // Variable pour détecter les modifications non sauvegardées

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

        // Configurer la ListView avec des CheckBox
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
    }

    @FXML
    private void handleSubscribeAllCapteurs() {
        boolean isOn = btnOuiCapteur.isSelected();
        configManager.updateConfig("Capteurs.subscribe", isOn ? "on" : "off");
        listCapteur.setDisable(isOn); // Désactiver la liste si "on"
        hasUnsavedChanges = true; // Marquer comme modifié
    }

    @FXML
    private void handleSubscribeAllPanneaux() {
        boolean isOn = btnOuiPanneau.isSelected();
        configManager.updateConfig("Panneaux Solaires.subscribe", isOn ? "on" : "off");
        hasUnsavedChanges = true; // Marquer comme modifié
    }

    @FXML
    private void handleValider() {
        String frequenceInput = frequence.getText();
        String hoteInput = hote.getText();

        // Vérification que la fréquence est un entier positif
        if (!isPositiveInteger(frequenceInput)) {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Erreur de Validation");
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

    // Méthode pour vérifier si une chaîne est un entier positif
    private boolean isPositiveInteger(String input) {
        try {
            int value = Integer.parseInt(input);
            return value > 0;
        } catch (NumberFormatException e) {
            return false;
        }
    }

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

    private void saveConfig() {
        try {
            configManager.saveConfig();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void reloadView() {
        initialize(); // Recharger les valeurs depuis l'initialize
    }

    @FXML
    private void handleOpenAlerte() {
        String subscribeAllCapteurs = configManager.readConfig("Capteurs.subscribe");
        String salles = configManager.readConfig("Capteurs.salles");

        if ("off".equalsIgnoreCase(subscribeAllCapteurs) && (salles.equals("''") || salles.isEmpty())) {
            Alert alert = new Alert(Alert.AlertType.WARNING);
            alert.setTitle("Alerte");
            alert.setHeaderText("Conditions non remplies");
            alert.setContentText("Vous devez activer l'abonnement aux capteurs ou sélectionner des salles pour accéder aux seuils d'alerte.");
            alert.showAndWait();
            return;
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
                    saveConfig(); // Sauvegarder les modifications avant de quitter
                    hasUnsavedChanges=false;
                }
                   else if (response == btnDiscard) {
                    ((Stage) btnAlerte.getScene().getWindow()).close(); // Fermer sans sauvegarder
                }
            });
        } else {
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
    }

    @FXML
    private void onBtnRetour() {
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
                    saveConfig(); // Sauvegarder les modifications avant de quitter
                    hasUnsavedChanges=false;
                    ((Stage) btnAlerte.getScene().getWindow()).close();
                } else if (response == btnDiscard) {
                    ((Stage) btnAlerte.getScene().getWindow()).close(); // Fermer sans sauvegarder
                }
            });
        } else {
            ((Stage) this.btnAlerte.getScene().getWindow()).close(); // Fermer directement si aucune modification
        }
    }
}
