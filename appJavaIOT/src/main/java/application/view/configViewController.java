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

    private final ObservableList<String> sallesCapteurs = FXCollections.observableArrayList(
            "B112", "C002", "B217", "E001", "B108", "C102", "E007", "amphi1",
            "B203", "E208", "E210", "E207", "B103", "E101", "C006", "E100",
            "hall-amphi", "hall-entrée-principale", "E103", "E102", "B110",
            "B106", "B001", "E004", "E106", "Local-velo", "B202", "C004",
            "Foyer-personnels", "B201", "B109", "C001", "B002", "Salle-conseil",
            "B105", "Foyer-etudiants-entrée", "C101", "B111", "B113", "E006",
            "E104", "E209", "E003");

    private final Set<String> selectedSalles = new HashSet<>();

    @FXML
private void initialize() {
    try {
        configManager.loadConfig();  // Charger la configuration depuis le fichier
    } catch (IOException e) {
        e.printStackTrace();
    }

    // Initialiser les champs texte avec les valeurs lues du fichier de configuration
    String hostValue = configManager.readConfig("General.host");
    hote.setText(hostValue.isEmpty() ? "mqtt.iut-blagnac.fr" : hostValue);  // Valeur par défaut si vide
    
    String frequenceValue = configManager.readConfig("General.frequence");
    frequence.setText(frequenceValue.isEmpty() ? "1" : frequenceValue);  // Valeur par défaut si vide

    // Initialiser les boutons radio pour les capteurs
    String subscribeAllCapteurs = configManager.readConfig("Capteurs.subscribe_all");
    if (subscribeAllCapteurs.isEmpty()) {
        subscribeAllCapteurs = "on";  // Définit une valeur par défaut si vide
        configManager.updateConfig("Capteurs.subscribe_all", subscribeAllCapteurs);
        saveConfig();
    }
    btnOuiCapteur.setSelected("on".equalsIgnoreCase(subscribeAllCapteurs));
    btnNonCapteur.setSelected("off".equalsIgnoreCase(subscribeAllCapteurs));
    listCapteur.setDisable(btnOuiCapteur.isSelected());

    // Initialiser les boutons radio pour les panneaux solaires
    String subscribeAllPanneaux = configManager.readConfig("Panneaux Solaires.subscribe_all");
    if (subscribeAllPanneaux.isEmpty()) {
        subscribeAllPanneaux = "off";  // Définit une valeur par défaut si vide
        configManager.updateConfig("Panneaux Solaires.subscribe_all", subscribeAllPanneaux);
        saveConfig();
    }
    btnOuiPanneau.setSelected("on".equalsIgnoreCase(subscribeAllPanneaux));
    btnNonPanneau.setSelected("off".equalsIgnoreCase(subscribeAllPanneaux));

    // Charger les salles sélectionnées
    selectedSalles.clear();
    String existingSalles = configManager.readConfig("Capteurs.salles");
    if (!existingSalles.isEmpty()) {
        String[] salles = existingSalles.replace("'", "").split(", ");
        Collections.addAll(selectedSalles, salles);
    }

    // Configurer la ListView avec des CheckBox
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
        listCapteur.setDisable(isOn); // Activer/Désactiver la liste selon le choix
        saveConfig();
    }

    @FXML
    private void handleSubscribeAllPanneaux() {
        boolean isOn = btnOuiPanneau.isSelected();
        configManager.updateConfig("Panneaux Solaires.subscribe_all", isOn ? "on" : "off");
        saveConfig();
    }

    private void updateSallesInConfig() {
        String updatedSalles = String.join(", ", selectedSalles).replaceAll("([^,]+)", "'$1'");
        configManager.updateConfig("Capteurs.salles", updatedSalles);
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
        try {
            configManager.loadConfig();  // Charger la configuration depuis le fichier
        } catch (IOException e) {
            e.printStackTrace();
        }
    
        // Recharger les valeurs dans les champs texte
        hote.setText(configManager.readConfig("General.host"));
        frequence.setText(configManager.readConfig("General.frequence"));
    
        // Recharger les valeurs pour les boutons radio
        String subscribeAllCapteurs = configManager.readConfig("Capteurs.subscribe_all");
        btnOuiCapteur.setSelected("on".equalsIgnoreCase(subscribeAllCapteurs));
        btnNonCapteur.setSelected("off".equalsIgnoreCase(subscribeAllCapteurs));
        listCapteur.setDisable(btnOuiCapteur.isSelected());
    
        String subscribeAllPanneaux = configManager.readConfig("Panneaux Solaires.subscribe_all");
        btnOuiPanneau.setSelected("on".equalsIgnoreCase(subscribeAllPanneaux));
        btnNonPanneau.setSelected("off".equalsIgnoreCase(subscribeAllPanneaux));
    
        // Recharger les salles sélectionnées
        selectedSalles.clear();
        String existingSalles = configManager.readConfig("Capteurs.salles");
        if (!existingSalles.isEmpty()) {
            String[] salles = existingSalles.replace("'", "").split(", ");
            Collections.addAll(selectedSalles, salles);
        }
    }
    @FXML
    private void handleOpenAlerte() {
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
