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
        menuCo2.setOnAction(event -> handleTypeSelection("co2", "Co2"));
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
            String currentMin = configManager.readConfig(selectedType + "Min");
            hasUnsavedChanges = currentMin != null && !currentMin.equals(newValue);
        });
        seuilsMax.textProperty().addListener((observable, oldValue, newValue) -> {
            String currentMax = configManager.readConfig(selectedType + "Max");
            hasUnsavedChanges = currentMax != null && !currentMax.equals(newValue);
        });
    }

    private void handleTypeSelection(String type, String label) {
        if (hasUnsavedChanges) {
            // Si des modifications non sauvegardées existent, demander confirmation
            Alert alert = new Alert(AlertType.CONFIRMATION);
            alert.setTitle("Modifications non sauvegardées");
            alert.setHeaderText("Vous avez des modifications non sauvegardées.");
            alert.setContentText("Que voulez-vous faire ?");
            ButtonType btnSave = new ButtonType("Valider");
            ButtonType btnCancel = new ButtonType("Annuler");
            alert.getButtonTypes().setAll(btnSave, btnCancel);

            // Traiter la réponse de l'utilisateur
            alert.showAndWait().ifPresent(response -> {
                if (response == btnSave) {
                    saveCurrentSeuils(); // Sauvegarder les modifications
                } else {
                    reloadSelectedType(); // Recharger les anciennes valeurs
                }
            });
        }

        hasUnsavedChanges = false; // Réinitialiser l'indicateur après traitement
        selectedType = type; // Passer au nouveau type
        typeDonnees.setText(label);

        // Charger les valeurs Min et Max correspondantes
        if (selectedType != null) {
            String minKey = selectedType + "Min";
            String maxKey = selectedType + "Max";

            seuilsMin.setText(configManager.readConfig(minKey).isEmpty() ? "0" : configManager.readConfig(minKey));
            seuilsMax.setText(configManager.readConfig(maxKey).isEmpty() ? "0" : configManager.readConfig(maxKey));
        }
    }

    private void saveCurrentSeuils() {
        if (selectedType != null) {
            try {
                // Lire les valeurs actuelles des champs
                String minValueStr = seuilsMin.getText();
                String maxValueStr = seuilsMax.getText();

                // Vérifier si les valeurs sont valides
                int minValue = Integer.parseInt(minValueStr);
                int maxValue = Integer.parseInt(maxValueStr);

                if (maxValue < minValue) {
                    Alert alert = new Alert(AlertType.ERROR);
                    alert.setTitle("Erreur de validation");
                    alert.setHeaderText("Valeur impossible");
                    alert.setContentText("La valeur maximale ne peut pas être inférieure à la valeur minimale.");
                    alert.showAndWait();

                    // Recharger les valeurs actuelles pour éviter toute incohérence
                    reloadSelectedType();
                    return;
                }

                // Mettre à jour la configuration
                String minKey = selectedType + "Min";
                String maxKey = selectedType + "Max";
                configManager.updateConfig(minKey, minValueStr);
                configManager.updateConfig(maxKey, maxValueStr);

                configManager.saveConfig();
                hasUnsavedChanges = false; // Réinitialiser l'indicateur après sauvegarde
                System.out.println("Les valeurs pour " + selectedType + " ont été enregistrées.");
            } catch (NumberFormatException e) {
                Alert alert = new Alert(AlertType.ERROR);
                alert.setTitle("Erreur de saisie");
                alert.setHeaderText("Valeur non valide");
                alert.setContentText("Veuillez entrer des nombres valides pour les seuils.");
                alert.showAndWait();

                return;
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    private void reloadSelectedType() {
        if (selectedType != null) {
            String minKey = selectedType + "Min";
            String maxKey = selectedType + "Max";

            seuilsMin.setText(configManager.readConfig(minKey).isEmpty() ? "0" : configManager.readConfig(minKey));
            seuilsMax.setText(configManager.readConfig(maxKey).isEmpty() ? "0" : configManager.readConfig(maxKey));
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
