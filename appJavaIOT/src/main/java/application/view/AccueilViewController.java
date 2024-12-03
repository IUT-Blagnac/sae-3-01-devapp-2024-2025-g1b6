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
import application.App;


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

    
    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        configure();
    }

    public void displayDialog() {
        containingStage.show();
    }

    private void configure(){
        this.btnTesterCo.setOnAction(event -> actionTesterCo());
        this.btnLancer.setOnAction(event -> actionLancer());
        this.btnManuel.setOnAction(event -> actionManuel());
        this.btnCredits.setOnAction(event -> actionCredits());
        this.btnQuitter.setOnAction(event -> actionQuitter());
        this.containingStage.setOnCloseRequest(event -> {
            event.consume();
            actionQuitter();
        });
        }

    @FXML
    private void actionQuitter() {
        Alert alert = new Alert(AlertType.CONFIRMATION, "Etes vous sur de vouloir quitter ?", ButtonType.YES, ButtonType.NO);
        alert.setTitle("Confirmation");
        alert.setHeaderText(null);

        alert.showAndWait().ifPresent(response -> {
            if (response == ButtonType.YES) {
            this.containingStage.close();
            }
        });
    }


    
    private int testerConnexion() {
    int exitCode = -1; 
    Process process = null;
    try {
        // Assurez-vous que chaque partie de la commande est bien séparée
        String[] command = {"python3", "src/main/python/main.py", "conTest"};
        ProcessBuilder pb = new ProcessBuilder(command);
        pb.redirectErrorStream(true); // Pour rediriger les erreurs dans la sortie standard (utile pour debug)
        process = pb.start();

        // Lire la sortie du processus pour debug
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
            process.destroy(); // Arrêter le processus si toujours en cours
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
        } else {
            alert.setHeaderText("Échec de la connexion");
            alert.setContentText("Une erreur est survenue lors de la connexion au serveur MQTT.\n"
                                + "Code de retour : " + result);
        }
    
        alert.showAndWait();
    }
    

    @FXML
    private void actionLancer() {
        Stage newStage = new Stage();
        newStage.setTitle("Lancer");

        // Créer un label pour le titre
        javafx.scene.control.Label lancerTitre = new javafx.scene.control.Label(
            "Redirigé vers l'application quand tout sera dans le même fichier"
        );
        lancerTitre.setWrapText(true); // Permet le retour à la ligne automatique
        lancerTitre.setStyle("-fx-font-size: 16px; -fx-font-weight: bold; -fx-text-alignment: center;");
        lancerTitre.setAlignment(javafx.geometry.Pos.CENTER);

        // Créer un bouton pour fermer la fenêtre
        Button btn = new Button("Fermer");
        btn.setOnAction(event -> newStage.close());

        // Ajouter le titre et le bouton dans un VBox
        javafx.scene.layout.VBox vbox = new javafx.scene.layout.VBox(10, lancerTitre, btn);
        vbox.setAlignment(javafx.geometry.Pos.CENTER);
        vbox.setPadding(new javafx.geometry.Insets(10));

        // Définir la scène et l'afficher
        javafx.scene.Scene scene = new javafx.scene.Scene(vbox, 300, 200);
        newStage.setScene(scene);
        newStage.show();
    }

    @FXML
private void actionManuel() {
    // Crée une nouvelle fenêtre (Stage)
    Stage newStage = new Stage();
    newStage.setTitle("Manuel");

    // Titre du manuel
    javafx.scene.control.Label manuelTitre = new javafx.scene.control.Label(
        "Voici le manuel d'utilisation de l'application :"
    );
    manuelTitre.setWrapText(true); // Permet le retour à la ligne automatique
    manuelTitre.setStyle("-fx-font-size: 16px; -fx-font-weight: bold; -fx-text-alignment: center;");
    manuelTitre.setAlignment(javafx.geometry.Pos.CENTER);

    // Contenu du manuel
    javafx.scene.control.Label manuelText = new javafx.scene.control.Label(
        "\n"
        + "1. Pour tester la connexion MQTT, cliquez sur le bouton 'Tester la connexion'.\n"
        + "2. Pour lancer l'application, cliquez sur le bouton 'Lancer'.\n"
        + "3. L'application ne peut pas être lancée si la connexion MQTT ne fonctionne pas correctement.\n"
    );
    manuelText.setWrapText(true); // Permet le retour à la ligne automatique
    manuelText.setStyle("-fx-font-size: 14px; -fx-text-alignment: justify;");
    manuelText.setAlignment(javafx.geometry.Pos.TOP_LEFT);

    // Conteneur pour le texte
    javafx.scene.layout.VBox textContainer = new javafx.scene.layout.VBox(manuelTitre, manuelText);
    textContainer.setSpacing(10); // Espace entre le titre et le texte
    textContainer.setPadding(new javafx.geometry.Insets(10));
    textContainer.setAlignment(javafx.geometry.Pos.TOP_CENTER);

    // Bouton "Fermer" en bas à droite
    Button closeButton = new Button("Fermer");
    closeButton.setOnAction(event -> newStage.close());
    javafx.scene.layout.HBox buttonContainer = new javafx.scene.layout.HBox(closeButton);
    buttonContainer.setAlignment(javafx.geometry.Pos.BOTTOM_RIGHT);
    buttonContainer.setPadding(new javafx.geometry.Insets(10));

    // Organisation de la fenêtre avec un BorderPane
    javafx.scene.layout.BorderPane layout = new javafx.scene.layout.BorderPane();
    layout.setCenter(textContainer); // Place le texte au centre
    layout.setBottom(buttonContainer); // Place le bouton en bas

    // Création et configuration de la scène
    javafx.scene.Scene scene = new javafx.scene.Scene(layout, 500, 300);
    newStage.setScene(scene);
    newStage.show();
}



    @FXML
    private void actionCredits() {
        // Crée une nouvelle fenêtre (Stage)
        Stage newStage = new Stage();
        newStage.setTitle("Crédits");

        // Texte statique au centre
        javafx.scene.control.Label creditsText = new javafx.scene.control.Label(
            "Cette application à été réalisé par : \n"
            + "Nicolas Ruiz\n"  
            + "Marwane Ibrahim\n"
            + "Yassir Boulouiha Gnaoui\n"
            + "Alex Lovin\n"
        );
        creditsText.setWrapText(true); // Permet le retour à la ligne automatique
        creditsText.setStyle("-fx-font-size: 14px; -fx-text-alignment: center;"); 
        creditsText.setAlignment(javafx.geometry.Pos.CENTER);

        // Bouton "Fermer" en bas à droite
        Button closeButton = new Button("Fermer");
        closeButton.setOnAction(event -> newStage.close());
        closeButton.setAlignment(javafx.geometry.Pos.CENTER_RIGHT);

        // Conteneur pour positionner le bouton en bas à droite
        javafx.scene.layout.HBox buttonContainer = new javafx.scene.layout.HBox(closeButton);
        buttonContainer.setAlignment(javafx.geometry.Pos.BOTTOM_RIGHT);
        buttonContainer.setPadding(new javafx.geometry.Insets(10));

        // Organisation de la fenêtre avec un BorderPane
        javafx.scene.layout.BorderPane layout = new javafx.scene.layout.BorderPane();
        layout.setCenter(creditsText); // Place le texte au centre
        layout.setBottom(buttonContainer); // Place le bouton en bas

        // Création et configuration de la scène
        javafx.scene.Scene scene = new javafx.scene.Scene(layout, 400, 200); // Largeur: 400, Hauteur: 200
        newStage.setScene(scene);
        newStage.show();
    }

}
