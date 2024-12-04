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
    private Button btnRetour;

    @FXML
    private Button btnAlerte;

    @FXML
    private ListView<String> listCapteur;

    @FXML
    private ToggleGroup groupCapteur;

    @FXML
    private ToggleGroup groupPanneau;

    @FXML
    private RadioButton btnOuiCapteur;

    @FXML
    private RadioButton btnNonCapteur;

    @FXML
    private RadioButton btnOuiPanneau;

    @FXML
    private RadioButton btnNonPanneau;

    private final Set<String> selectedSalles = new HashSet<>();

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

        // Boutons radio pour capteurs
        String subscribeAllCapteurs = configManager.readConfig("Capteurs.subscribe_all");
        if ("on".equalsIgnoreCase(subscribeAllCapteurs)) {
            btnOuiCapteur.setSelected(true);
            listCapteur.setDisable(true);
        } else {
            btnNonCapteur.setSelected(true);
            listCapteur.setDisable(false);
        }

        // Boutons radio pour panneaux solaires
        String subscribeAllPanneaux = configManager.readConfig("Panneaux Solaires.subscribe_all");
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
                updateSallesInConfig();
            });
            return checked;
        }));

        // Listeners pour détecter les modifications dans les champs texte
        hote.textProperty().addListener((observable, oldValue, newValue) -> {
            configManager.updateConfig("General.host", newValue);
            saveConfig();
        });

        frequence.textProperty().addListener((observable, oldValue, newValue) -> {
            configManager.updateConfig("General.frequence", newValue);
            saveConfig();
        });
    }

    @FXML
    private void handleSubscribeAllCapteurs() {
        boolean isOn = btnOuiCapteur.isSelected();
        configManager.updateConfig("Capteurs.subscribe_all", isOn ? "on" : "off");
        listCapteur.setDisable(isOn); // Désactiver la liste si "on"
        saveConfig();
    }

    @FXML
    private void handleSubscribeAllPanneaux() {
        boolean isOn = btnOuiPanneau.isSelected();
        configManager.updateConfig("Panneaux Solaires.subscribe_all", isOn ? "on" : "off");
        saveConfig();
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
        saveConfig();
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
        String subscribeAllCapteurs = configManager.readConfig("Capteurs.subscribe_all");
        String salles = configManager.readConfig("Capteurs.salles");

        if ("off".equalsIgnoreCase(subscribeAllCapteurs) && (salles.equals("''") || salles.isEmpty())) {
            Alert alert = new Alert(Alert.AlertType.WARNING);
            alert.setTitle("Alerte");
            alert.setHeaderText("Conditions non remplies");
            alert.setContentText("Vous devez activer l'abonnement aux capteurs ou sélectionner des salles pour accéder aux seuils d'alerte.");
            alert.showAndWait();
            return;
        }

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
