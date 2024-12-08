package application.view;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.ButtonType;

public class AccueilViewController {

    private Stage containingStage;

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


    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet de configurer les actions des boutons de l'interface graphique.
     * Les actions sont les suivantes :
     * - Tester la connexion MQTT
     * - Lancer l'application
     * - Afficher le manuel d'utilisation
     * - Afficher les crédits
     * - Quitter l'application
     * 
     */
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

    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet de quitter l'application en affichant une boîte de dialogue de confirmation.
     * Si l'utilisateur clique sur "Oui", l'application se ferme.
     * 
     */
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

    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet de tester la connexion MQTT en exécutant un script Python.
     * Le script Python se charge de tester la connexion et renvoie un code de retour.
     * Si le code de retour est 0, la connexion est validée et le bouton "Lancer" est activé.
     * Sinon, une alerte est affichée pour informer l'utilisateur de l'échec de la connexion.
     * 
     */
    private int testerConnexion() {
        int exitCode = -1; 
        Process process = null;
        try {
            String[] command = {"python3", "src/main/python/main.py", "conTest"};
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


    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet de tester la connexion MQTT en exécutant la méthode qui vérifie la connexion.
     * Afin de pouvoir ici l'afficher à l'utilisateur.
     * 
     */
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


    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet de lancer l'application. Elle redirige vers la suite de l'application
     * 
     */
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

        Stage newStage = new Stage();
        newStage.setTitle("Lancer");

        javafx.scene.control.Label lancerTitre = new javafx.scene.control.Label(
            "Redirigé vers l'application quand tout sera dans le même fichier"
        );
        lancerTitre.setWrapText(true);
        lancerTitre.setStyle("-fx-font-size: 16px; -fx-font-weight: bold; -fx-text-alignment: center;");
        lancerTitre.setAlignment(javafx.geometry.Pos.CENTER);

        Button btn = new Button("Fermer");
        btn.setOnAction(event -> newStage.close());

        javafx.scene.layout.VBox vbox = new javafx.scene.layout.VBox(10, lancerTitre, btn);
        vbox.setAlignment(javafx.geometry.Pos.CENTER);
        vbox.setPadding(new javafx.geometry.Insets(10));

        javafx.scene.Scene scene = new javafx.scene.Scene(vbox, 300, 200);
        newStage.setScene(scene);
        newStage.show();
    }


    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet d'afficher le manuel d'utilisation de l'application.
     * Le manuel est affiché dans une nouvelle fenêtre.
     * 
     */
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


    /* Développer par RUIZ Nicolas 
     * 
     * Cette méthode permet d'afficher les crédits de l'application.
     * Les crédits sont affichés dans une nouvelle fenêtre.
     * 
     */
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
}
