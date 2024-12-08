package application.view;

import java.io.IOException;
import java.nio.file.DirectoryStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;

import application.SyncData;
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

import application.config.ConfigManager;
import application.LaunchApp;
import javafx.scene.control.MenuItem;
/**
 * Contrôleur pour la gestion des seuils de données.
 * Permet à l'utilisateur de configurer les seuils d'alerte pour différents types de données.
 * @author Alex LOVIN
 */
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

    /**
     * Méthode d'initialisation appelée après le chargement du fichier FXML.
     * Configure les actions des menus et détecte les changements dans les champs de texte.
     * @author Alex LOVIN
     */
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
    /**
     * Gère la sélection d'un type de données dans le menu déroulant.
     * Charge les valeurs associées au type sélectionné et demande de sauvegarder les changements non sauvegardés, le cas échéant.
     * @param type Le type de données sélectionné.
     * @param label L'étiquette affichée pour le type de données sélectionné.
     * @author Alex LOVIN
     */
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
    /**
     * Sauvegarde les seuils actuels pour le type sélectionné après validation.
     * Inclut des vérifications pour s'assurer de la cohérence des données saisies par l'utilisateur.
     * @author Alex LOVIN
     */
    private void saveCurrentSeuils() {
        if (selectedType != null) {
            if (!validateFields()) {
                return; // Arrêter l'exécution si des champs sont vides
            }

            try {
                String minValueStr = seuilsMin.getText().trim();
                String maxValueStr = seuilsMax.getText().trim();

                // Validation des valeurs
                int minValue = Integer.parseInt(minValueStr);
                int maxValue = Integer.parseInt(maxValueStr);

                // Vérification que les valeurs sont positives sauf pour la température
                if (!selectedType.equals("temperature") && (minValue < 0 || maxValue < 0)) {
                    Alert alert = new Alert(AlertType.ERROR);
                    alert.setTitle("Erreur de validation");
                    alert.setHeaderText("Valeur impossible");
                    alert.setContentText("Les valeurs doivent être positives, sauf pour la température.");
                    alert.showAndWait();
                    reloadSelectedType(); // Recharger les valeurs actuelles
                    return;
                }

                // Vérification que max >= min
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
                configManager.updateConfig("Seuils Alerte." + selectedType + "Min", minValueStr);
                configManager.updateConfig("Seuils Alerte." + selectedType + "Max", maxValueStr);
                configManager.saveConfig();

                // Recharger la configuration après la sauvegarde pour garantir la mise à jour des données
                configManager.loadConfig();

                // Recharger l'affichage
                reloadSelectedType();

                //Supprimer les données actuellement enregitrées dans l'application vu que les seuils ont changé
                SyncData syncInst = SyncData.getInstance();
                syncInst.clearData();
                //De même avec les fichiers dans le dossier Alert
                deleteAlertData();

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

    /**
     * Valide les champs de seuils pour s'assurer qu'ils ne sont pas vides.
     * Affiche un message d'erreur si une validation échoue.
     * @return true si les champs sont valides, false sinon.
     * @author Alex LOVIN
     */
    private boolean validateFields() {
        if (seuilsMin.getText().trim().isEmpty() || seuilsMax.getText().trim().isEmpty()) {
            Alert alert = new Alert(AlertType.ERROR);
            alert.setTitle("Erreur de validation");
            alert.setHeaderText("Champs vides");
            alert.setContentText("Veuillez remplir les deux champs avant de valider.");
            alert.showAndWait();
            return false;
        }
        return true;
    }

    /**
     * Recharge les valeurs Min et Max pour le type de données actuellement sélectionné.
     * Met à jour les champs de saisie avec les valeurs lues depuis la configuration.
     * @author Alex LOVIN
     */
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

    /**
     * Supprime les données enregistrées dans le dossier "Alert" pour réinitialiser les alertes.
     * Cette méthode est appelée après la modification des seuils d'alerte.
     * @author Alex LOVIN
     */
    private void deleteAlertData(){
        String directoryPath = "src/main/resources/data";
        Path directory = Paths.get(directoryPath);
        if (directory.toFile().exists()) {
            try (DirectoryStream<Path> stream = Files.newDirectoryStream(directory)) {
                for (Path dir : stream) {
                    // Vérifier si le dossier est "Alert"
                    if (Files.isDirectory(dir) && dir.getFileName().toString().equals("Alert")) {
                        try (DirectoryStream<Path> alertStream = Files.newDirectoryStream(dir)) {
                            // Supprimer tous les fichiers dans le dossier "Alert"
                            for (Path alertFile : alertStream) {
                                if (Files.isRegularFile(alertFile)) {
                                    Files.delete(alertFile);  // Supprimer le fichier
                                }
                            }
                        }
                    }
                }
            } catch (IOException e) {
                throw new RuntimeException(e);
            }
        }
    }

    /**
     * Méthode liée au bouton de validation des seuils.
     * Appelle la méthode de sauvegarde pour valider les changements actuels.
     * @author Alex LOVIN
     */
    @FXML
    private void handleSaveSeuils() {
        saveCurrentSeuils(); // Appel à la méthode qui gère la validation et la sauvegarde
    }

     /**
     * Ouvre l'interface de configuration et permet de basculer vers la vue associée.
     * Propose de sauvegarder les changements non sauvegardés avant d'effectuer la transition.
     * @author Alex LOVIN
     */
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
