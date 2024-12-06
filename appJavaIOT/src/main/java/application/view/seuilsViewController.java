package application.view;

import java.io.IOException;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.scene.control.MenuButton;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

import application.ConfigManager;
import application.LaunchApp;
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

    @FXML
    private Button valider;

    private final ConfigManager configManager = new ConfigManager();
    private String selectedType = null; // Type actuellement sélectionné
    private boolean hasUnsavedChanges = false; // Indicateur de changements non sauvegardés

    @FXML
    private void initialize() {
        try {
            // Charger la configuration
            configManager.loadConfig();
        } catch (IOException e) {
            e.printStackTrace();
        }

        // Associer les éléments du menu aux types de données
        menuCo2.setOnAction(event -> handleTypeSelection("co2", "CO2"));
        menuTemperature.setOnAction(event -> handleTypeSelection("temperature", "Température"));
        menuHumidity.setOnAction(event -> handleTypeSelection("humidity", "Humidité"));
        menuActivity.setOnAction(event -> handleTypeSelection("activity", "Activité"));
        menuTvoc.setOnAction(event -> handleTypeSelection("tvoc", "COV Total"));
        menuIllumination.setOnAction(event -> handleTypeSelection("illumination", "Éclairage"));
        menuInfrared.setOnAction(event -> handleTypeSelection("infrared", "Infrarouge"));
        menuInfraredAndVisible.setOnAction(event -> handleTypeSelection("infrared_and_visible", "Infrarouge et visible"));
        menuPressure.setOnAction(event -> handleTypeSelection("pressure", "Pression"));

        // Détecter les modifications dans les champs de texte
        seuilsMin.textProperty().addListener((observable, oldValue, newValue) -> {
            if (selectedType != null) {
                String currentMin = configManager.readConfig("Seuils Alerte."+selectedType + "Min");
                hasUnsavedChanges = !newValue.trim().equals(currentMin);
            }
        });

        seuilsMax.textProperty().addListener((observable, oldValue, newValue) -> {
            if (selectedType != null) {
                String currentMax = configManager.readConfig("Seuils Alerte."+selectedType + "Max");
                hasUnsavedChanges = !newValue.trim().equals(currentMax);
            }
        });
    }

    private void handleTypeSelection(String type, String label) {
        if (hasUnsavedChanges) {
            Alert alert = new Alert(AlertType.CONFIRMATION);
            alert.setTitle("Modifications non sauvegardées");
            alert.setHeaderText("Vous avez des modifications non sauvegardées.");
            alert.setContentText("Que voulez-vous faire ?");
            ButtonType btnSave = new ButtonType("Valider");
            ButtonType btnCancel = new ButtonType("Annuler");
            alert.getButtonTypes().setAll(btnSave, btnCancel);

            alert.showAndWait().ifPresent(response -> {
                if (response == btnSave) {
                    saveCurrentSeuils(); // Sauvegarder les modifications
                } else {
                    reloadSelectedType(); // Recharger les anciennes valeurs
                }
            });
        }

        hasUnsavedChanges = false; // Réinitialiser l'indicateur
        selectedType = type; // Mettre à jour le type sélectionné
        typeDonnees.setText(label);

        reloadSelectedType(); // Recharger les valeurs Min et Max pour le type sélectionné
    }

    private void saveCurrentSeuils() {
        if (selectedType != null) {
            try {
                String minValueStr = seuilsMin.getText().trim();
                String maxValueStr = seuilsMax.getText().trim();

                int minValue = Integer.parseInt(minValueStr);
                int maxValue = Integer.parseInt(maxValueStr);

                if (maxValue < minValue) {
                    Alert alert = new Alert(AlertType.ERROR);
                    alert.setTitle("Erreur de validation");
                    alert.setHeaderText("Valeur impossible");
                    alert.setContentText("Max doit être supérieur ou égal à Min.");
                    alert.showAndWait();
                    reloadSelectedType(); // Recharger les valeurs actuelles
                    return;
                }

                // Mettre à jour et sauvegarder
                configManager.updateConfig("Seuils Alerte."+selectedType + "Min", minValueStr);
                configManager.updateConfig("Seuils Alerte."+selectedType + "Max", maxValueStr);
                configManager.saveConfig();

                // Recharger la configuration après la sauvegarde pour garantir la mise à jour des données
                configManager.loadConfig();

                // Recharger l'affichage
                reloadSelectedType();

                hasUnsavedChanges = false;

            } catch (NumberFormatException e) {
                Alert alert = new Alert(AlertType.ERROR);
                alert.setTitle("Erreur de saisie");
                alert.setHeaderText("Valeur non valide");
                alert.setContentText("Veuillez entrer des nombres valides pour les seuils.");
                alert.showAndWait();
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    private void reloadSelectedType() {
        if (selectedType != null) {
            String minKey = "Seuils Alerte." + selectedType + "Min";
            String maxKey = "Seuils Alerte." + selectedType + "Max";

            // Lire les valeurs depuis configMap
            String minValue = configManager.readConfig(minKey);
            String maxValue = configManager.readConfig(maxKey);

            // Log de débogage
            System.out.println("Reloading values for " + selectedType + ": Min=" + minValue + ", Max=" + maxValue);

            // Mettre à jour les champs avec les valeurs chargées
            seuilsMin.setText(minValue);
            seuilsMax.setText(maxValue);
        }
    }

    @FXML
    private void handleSaveSeuils() {
        saveCurrentSeuils(); // Appel à la méthode qui gère la validation et la sauvegarde
    }

    @FXML
    private void handleOpenConfig() {
        if (hasUnsavedChanges) {
            Alert alert = new Alert(AlertType.CONFIRMATION);
            alert.setTitle("Modifications non sauvegardées");
            alert.setHeaderText("Vous avez des modifications non sauvegardées.");
            alert.setContentText("Que voulez-vous faire ?");
            ButtonType btnSave = new ButtonType("Valider");
            ButtonType btnCancel = new ButtonType("Annuler");
            alert.getButtonTypes().setAll(btnSave, btnCancel);

            alert.showAndWait().ifPresent(response -> {
                if (response == btnSave) {
                    saveCurrentSeuils();
                }
            });
        }

        try {
            FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/configView.fxml"));
            Parent configPage = fxmlLoader.load();

            // Appeler reloadView() après le chargement
            configViewController controller = fxmlLoader.getController();
            controller.reloadView();
            Stage stage = new Stage();
            stage.setTitle("Configuration");
            stage.setScene(new Scene(configPage));
            stage.show();
            ((Stage) btnRetour.getScene().getWindow()).close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
