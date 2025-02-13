package application.view;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import application.LaunchApp;
import application.SyncData;

import application.control.RoomsPane;
import application.tools.StageManagement;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.ButtonType;
import org.ini4j.Ini;
import org.ini4j.Profile;


public class AccueilViewController {

    private Stage containingStage;
    private String pythonExecutable;

    @FXML
    private Button btnTesterCo;
    @FXML
    private Button btnLancer;
    @FXML
    private Button btnManuel;
    @FXML
    private Button btnCredits;
    @FXML
    private Button btnQuitter;

    private boolean isConnectionValid = false; // Par défaut, la connexion n'est pas validée.

    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        configure();
    }

    public void displayDialog() {
        containingStage.show();
    }

    private void configure() {
        this.btnTesterCo.setOnAction(event -> actionTesterCo());
        this.btnLancer.setOnAction(event -> actionLancer());
        this.btnManuel.setOnAction(event -> actionManuel());
        this.btnCredits.setOnAction(event -> actionCredits());
        this.btnQuitter.setOnAction(event -> actionQuitter());
        this.containingStage.setOnCloseRequest(event -> {
            event.consume();
            actionQuitter();
        });

        btnLancer.setDisable(true); // Désactiver le bouton "Lancer" au démarrage
    }

    @FXML
    private void actionQuitter() {
        Alert alert = new Alert(AlertType.CONFIRMATION, "Êtes-vous sûr de vouloir quitter ?", ButtonType.YES, ButtonType.NO);
        alert.setTitle("Confirmation");
        alert.setHeaderText(null);

        alert.showAndWait().ifPresent(response -> {
            if (response == ButtonType.YES) {
                this.containingStage.close();
            }
        });
    }

    private void checkAndSetupPythonEnvironment() {
        try {
            // Vérifier si Python est installé
            if (!isCommandAvailable("python --version") && !isCommandAvailable("python3 --version")) {
                throw new RuntimeException("Python n'est pas installé.");
            }

            // Déterminer l'exécutable Python
            if (isCommandAvailable("python --version")) {
                pythonExecutable = "python";
            } else {
                pythonExecutable = "python3";
            }

            // Vérifier si pip est installé
            if (!isCommandAvailable(pythonExecutable + " -m pip --version")) {
                // Installer pip si non disponible
                installPip();
            }

            // Vérifier si paho-mqtt est installé
            if (!isPackageInstalled("paho-mqtt")) {
                installPythonPackage("paho-mqtt");
            }

        } catch (IOException | InterruptedException e) {
            throw new RuntimeException("Erreur lors de la configuration de l'environnement Python", e);
        }
    }

    private boolean isCommandAvailable(String command) throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder("cmd.exe", "/c", command);
        Process process = processBuilder.start();
        process.waitFor();
        return process.exitValue() == 0;
    }

    private void installPip() throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder(pythonExecutable, "-m", "ensurepip");
        Process process = processBuilder.start();
        process.waitFor();
        if (process.exitValue() != 0) {
            throw new RuntimeException("L'installation de pip a échoué.");
        }
    }

    private boolean isPackageInstalled(String packageName) throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder(pythonExecutable, "-m", "pip", "show", packageName);
        Process process = processBuilder.start();
        process.waitFor();
        return process.exitValue() == 0;
    }

    private void installPythonPackage(String packageName) throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder(pythonExecutable, "-m", "pip", "install", packageName);
        Process process = processBuilder.start();
        process.waitFor();
        if (process.exitValue() != 0) {
            throw new RuntimeException("L'installation du package " + packageName + " a échoué.");
        }
    }

    private int testerConnexion() {
        checkAndSetupPythonEnvironment();
        int exitCode = -1;
        Process process = null;
        try {
            String[] command = {pythonExecutable, "src/main/python/main.py", "conTest"};
            ProcessBuilder pb = new ProcessBuilder(command);
            pb.redirectErrorStream(true); // Redirige les erreurs vers la sortie standard
            process = pb.start();

            // Lire la sortie pour debug
            try (BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()))) {
                String line;
                while ((line = reader.readLine()) != null) {
                    System.out.println(line); // Affiche la sortie du script Python
                }
            }

            exitCode = process.waitFor(); // Attendre la fin du processus
        } catch (IOException | InterruptedException e) {
            e.printStackTrace();
        } finally {
            if (process != null) {
                process.destroy(); // Arrêter le processus si encore en cours
            }
        }

        return exitCode;
    }

    @FXML
    private void actionTesterCo() {
        int result = testerConnexion();

        // Préparer une alerte pour afficher le résultat
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Résultat de la connexion MQTT");

        if (result == 0) {
            alert.setHeaderText("Connexion réussie !");
            alert.setContentText("La connexion au serveur MQTT a été établie avec succès.");
            isConnectionValid = true; // Connexion validée
            btnLancer.setDisable(false); // Activer le bouton "Lancer"
        } else {
            alert.setHeaderText("Échec de la connexion");
            alert.setContentText("Une erreur est survenue lors de la connexion au serveur MQTT.\n"
                                + "Code de retour : " + result);
            isConnectionValid = false; // Connexion invalide
            btnLancer.setDisable(true); // Désactiver le bouton "Lancer"
        }

        alert.showAndWait();
    }

    @FXML
    private void actionLancer() {
        if (!isConnectionValid) {
            Alert alert = new Alert(AlertType.WARNING);
            alert.setTitle("Connexion requise");
            alert.setHeaderText("Impossible de lancer l'application");
            alert.setContentText("Veuillez tester et valider la connexion avant de lancer l'application.");
            alert.showAndWait();
            return;
        }

        // Charger le fichier INI
        Ini ini = null;
        try {
            ini = new Ini(new File("src/main/python/config.ini"));
        } catch (IOException e) {
            throw new RuntimeException(e);
        }

        // Obtenir la section [General]
        Profile.Section generalSection = ini.get("General");

        // Lire la valeur de 'frequence'
        String frequenceValue = generalSection.get("frequence");

        if (!frequenceValue.isEmpty()){
            SyncData syncInstance = SyncData.getInstance();
            syncInstance.startPeriodicSave(Integer.parseInt(frequenceValue));
            RoomsPane rp = new RoomsPane(this.containingStage);
            rp.show();
        }

    }

    @FXML
    private void actionManuel() {
        Stage newStage = new Stage();
        newStage.setTitle("Manuel");

        javafx.scene.control.Label manuelTitre = new javafx.scene.control.Label(
            "Voici le manuel d'utilisation de l'application :"
        );
        manuelTitre.setWrapText(true);
        manuelTitre.setStyle("-fx-font-size: 16px; -fx-font-weight: bold; -fx-text-alignment: center;");
        manuelTitre.setAlignment(javafx.geometry.Pos.CENTER);

        javafx.scene.control.Label manuelText = new javafx.scene.control.Label(
            "\n"
            + "1. Pour tester la connexion MQTT, cliquez sur le bouton 'Tester la connexion'.\n"
            + "2. Pour lancer l'application, cliquez sur le bouton 'Lancer'.\n"
            + "3. L'application ne peut pas être lancée si la connexion MQTT n'a pas été testée.\n"
            + "4. Veillez à ne pas être connecté à eduroam avant de tester la connexion, auquel cas il ne sera pas possible de lancer l'application.\n"
        );
        manuelText.setWrapText(true);
        manuelText.setStyle("-fx-font-size: 14px; -fx-text-alignment: justify;");
        manuelText.setAlignment(javafx.geometry.Pos.TOP_LEFT);

        javafx.scene.layout.VBox textContainer = new javafx.scene.layout.VBox(manuelTitre, manuelText);
        textContainer.setSpacing(10);
        textContainer.setPadding(new javafx.geometry.Insets(10));
        textContainer.setAlignment(javafx.geometry.Pos.TOP_CENTER);

        Button closeButton = new Button("Fermer");
        closeButton.setOnAction(event -> newStage.close());
        javafx.scene.layout.HBox buttonContainer = new javafx.scene.layout.HBox(closeButton);
        buttonContainer.setAlignment(javafx.geometry.Pos.BOTTOM_RIGHT);
        buttonContainer.setPadding(new javafx.geometry.Insets(10));

        javafx.scene.layout.BorderPane layout = new javafx.scene.layout.BorderPane();
        layout.setCenter(textContainer);
        layout.setBottom(buttonContainer);

        javafx.scene.Scene scene = new javafx.scene.Scene(layout, 500, 300);
        newStage.setScene(scene);
        newStage.show();
    }

    @FXML
    private void actionCredits() {
        Stage newStage = new Stage();
        newStage.setTitle("Crédits");

        javafx.scene.control.Label creditsText = new javafx.scene.control.Label(
            "Cette application a été réalisée par : \n"
            + "Nicolas Ruiz\n"
            + "Marwane Ibrahim\n"
            + "Yassir Boulouiha Gnaoui\n"
            + "Alex Lovin\n"
        );
        creditsText.setWrapText(true);
        creditsText.setStyle("-fx-font-size: 14px; -fx-text-alignment: center;");
        creditsText.setAlignment(javafx.geometry.Pos.CENTER);

        Button closeButton = new Button("Fermer");
        closeButton.setOnAction(event -> newStage.close());
        javafx.scene.layout.HBox buttonContainer = new javafx.scene.layout.HBox(closeButton);
        buttonContainer.setAlignment(javafx.geometry.Pos.BOTTOM_RIGHT);
        buttonContainer.setPadding(new javafx.geometry.Insets(10));

        javafx.scene.layout.BorderPane layout = new javafx.scene.layout.BorderPane();
        layout.setCenter(creditsText);
        layout.setBottom(buttonContainer);

        javafx.scene.Scene scene = new javafx.scene.Scene(layout, 400, 200);
        newStage.setScene(scene);
        newStage.show();
    }

    @FXML
    private void actionConfig(){
        Stage configStage = new Stage();

        FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/configView.fxml"));
        Scene scene = null;
        try {
            scene = new Scene(fxmlLoader.load(), 700, 500);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
        configStage.setTitle("Config");
        configStage.setScene(scene);
        StageManagement.manageCenteringStage(this.containingStage, configStage);
        configStage.show();
    }


}
