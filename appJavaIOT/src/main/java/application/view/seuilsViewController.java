package application.view;


import java.io.IOException;

import application.LaunchApp;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.MenuButton;
import javafx.scene.control.TextField;
import javafx.stage.Stage;


import application.ConfigManager;
import javafx.scene.control.MenuItem;

public class seuilsViewController {

    @FXML
    private TextField seuilsMin;

    @FXML
    private TextField seuilsMax;

    @FXML
    private Button btnRetour;

    @FXML
    private MenuButton typeDonnees;

    @FXML
    private MenuItem menuCo2;
    @FXML
    private MenuItem menuTemperature;
    @FXML
    private MenuItem menuHumidity;
    @FXML
    private MenuItem menuActivity;
    @FXML
    private MenuItem menuTvoc;
    @FXML
    private MenuItem menuIllumination;
    @FXML
    private MenuItem menuInfrared;
    @FXML
    private MenuItem menuInfraredAndVisible;
    @FXML
    private MenuItem menuPressure;
    private final ConfigManager configManager = new ConfigManager();
    private String selectedType = null; // Type actuellement sélectionné

    @FXML
    private void initialize() {
        try {
            // Charger la configuration
            configManager.loadConfig();
        } catch (IOException e) {
            e.printStackTrace();
        }

        // Associer les éléments du menu aux types de données
        menuCo2.setOnAction(event -> handleTypeSelection("co2", "Co2"));
        menuTemperature.setOnAction(event -> handleTypeSelection("temperature", "Température"));
        menuHumidity.setOnAction(event -> handleTypeSelection("humidity", "Humidité"));
        menuActivity.setOnAction(event -> handleTypeSelection("activity", "Activité"));
        menuTvoc.setOnAction(event -> handleTypeSelection("tvoc", "COV Total"));
        menuIllumination.setOnAction(event -> handleTypeSelection("illumination", "Éclairage"));
        menuInfrared.setOnAction(event -> handleTypeSelection("infrared", "Infrarouge"));
        menuInfraredAndVisible.setOnAction(event -> handleTypeSelection("infrared_and_visible", "Infrarouge et visible"));
        menuPressure.setOnAction(event -> handleTypeSelection("pressure", "Pression"));
    }

    private void handleTypeSelection(String type, String label) {
        // Mettre à jour le type sélectionné
        selectedType = type;
        typeDonnees.setText(label);

        // Charger les valeurs Min et Max correspondantes
        if (selectedType != null) {
            String minKey = selectedType + "Min";
            String maxKey = selectedType + "Max";

            seuilsMin.setText(configManager.readConfig(minKey).isEmpty() ? "0" : configManager.readConfig(minKey));
            seuilsMax.setText(configManager.readConfig(maxKey).isEmpty() ? "0" : configManager.readConfig(maxKey));
        }
    }

    @FXML
    private void handleSaveSeuils() {
        // Sauvegarder les valeurs Min et Max dans config.ini
        if (selectedType != null) {
            String minKey = selectedType + "Min";
            String maxKey = selectedType + "Max";

            configManager.updateConfig(minKey, seuilsMin.getText());
            configManager.updateConfig(maxKey, seuilsMax.getText());

            try {
                configManager.saveConfig();
                System.out.println("Les valeurs ont été enregistrées dans le fichier config.ini.");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    @FXML
    private void handleOpenConfig() {
        try {
            // Charger la vue principale
            FXMLLoader fxmlLoader = new FXMLLoader(application.LaunchApp.class.getResource("view/configView.fxml"));
            Parent config = fxmlLoader.load();

            // Afficher la vue principale dans une nouvelle fenêtre
            Stage stage = new Stage();
            stage.setTitle("Configuration");
            stage.setScene(new Scene(config));
            stage.show();

            // Fermer la fenêtre actuelle
            ((Stage) btnRetour.getScene().getWindow()).close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
