package application.view;

import java.io.IOException;
import application.ConfigManager;
import application.LaunchApp;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.RadioButton;
import javafx.scene.control.TextField;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;

public class configViewController {

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

    @FXML
    private void initialize() {
        // Assurez-vous que le fichier config.ini est prêt
        ConfigManager.checkOrCreateConfigFile();

        try {
            // Charger les valeurs depuis le fichier config.ini
            hote.setText(ConfigManager.readConfig("General", "host"));
            frequence.setText(ConfigManager.readConfig("General", "frequence"));

            // Définir les boutons Oui/Non en fonction de la configuration
            String subscribeAll = ConfigManager.readConfig("Capteurs", "subscribe_all");
            if ("on".equalsIgnoreCase(subscribeAll)) {
                btnOuiCapteur.setSelected(true);
            } else {
                btnNonCapteur.setSelected(true);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }

        // Ajouter des listeners pour mettre à jour les valeurs automatiquement
        hote.textProperty().addListener((observable, oldValue, newValue) -> {
            try {
                ConfigManager.updateConfig("General", "host", newValue);
            } catch (IOException e) {
                e.printStackTrace();
            }
        });

        frequence.textProperty().addListener((observable, oldValue, newValue) -> {
            try {
                ConfigManager.updateConfig("General", "frequence", newValue);
            } catch (IOException e) {
                e.printStackTrace();
            }
        });
    }
    @FXML
    private void handleSubscribeAllCapteurs() {
        try {
            String value = btnOuiCapteur.isSelected() ? "on" : "off";
            ConfigManager.updateConfig("Capteurs", "subscribe_all", value);
            System.out.println("Valeur de subscribe_all mise à jour : " + value);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    
    @FXML
    private void handleToggleCapteur() {
        try {
            // Mettre à jour subscribe_all en fonction du bouton sélectionné
            String value = btnOuiCapteur.isSelected() ? "on" : "off";
            ConfigManager.updateConfig("Capteurs", "subscribe_all", value);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleAddCapteur() {
        // Ajouter une salle sélectionnée dans la liste
        String selectedSalle = listCapteur.getSelectionModel().getSelectedItem();
        if (selectedSalle != null) {
            try {
                String salles = ConfigManager.readConfig("Capteurs", "salles");
                if (!salles.contains(selectedSalle)) {
                    String updatedSalles = salles.isEmpty() ? "'" + selectedSalle + "'" : salles + ", '" + selectedSalle + "'";
                    ConfigManager.updateConfig("Capteurs", "salles", updatedSalles);
                }
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    @FXML
    private void handleOpenAlerte() {
        try {
            // Charger une nouvelle vue pour gérer les seuils
            FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/seuilsAlerteView.fxml"));
            Parent alertePage = fxmlLoader.load();

            // Créer une nouvelle fenêtre pour afficher la vue
            Stage stage = new Stage();
            stage.setTitle("Seuils Alerte");
            stage.setScene(new Scene(alertePage));
            stage.show();

            // Optionnel : fermer la fenêtre actuelle
            ((Stage) btnAlerte.getScene().getWindow()).close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}