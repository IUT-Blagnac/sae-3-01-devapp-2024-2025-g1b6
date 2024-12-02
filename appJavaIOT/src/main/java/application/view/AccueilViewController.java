package iut.view;

import java.io.IOException;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.ButtonType;
import iut.App;


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


    
    private int testerConnexion(){
        int exitCode = null;
        try {
            // Commande pour exécuter le script Python
            String[] command = {"python3", "script.py"};

            // Créer le processus
            ProcessBuilder pb = new ProcessBuilder(command);
            Process process = pb.start();

            // Attendre que le processus se termine et obtenir le code de retour
            exitCode = process.waitFor();
            return exitCode;

        } catch (Exception e) {
            e.printStackTrace();
        }

        return exitCode;
    }


    @FXML
    private void actionTesterCo() {
        Stage newStage = new Stage();
        newStage.setTitle("Tester MQTT");

        Button btn = new Button("Fermer");
        btn.setOnAction(event -> newStage.close());

        javafx.scene.layout.VBox vbox = new javafx.scene.layout.VBox(btn);
        vbox.setAlignment(javafx.geometry.Pos.CENTER);
        vbox.setPadding(new javafx.geometry.Insets(10));

        javafx.scene.Scene scene = new javafx.scene.Scene(vbox, 300, 200);
        newStage.setScene(scene);
        newStage.show();
    }

    @FXML
    private void actionLancer() {
        Stage newStage = new Stage();
        newStage.setTitle("Lancer");

        Button btn = new Button("Fermer");
        btn.setOnAction(event -> newStage.close());

        javafx.scene.layout.VBox vbox = new javafx.scene.layout.VBox(btn);
        vbox.setAlignment(javafx.geometry.Pos.CENTER);
        vbox.setPadding(new javafx.geometry.Insets(10));

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
        + "2. Pour lancer l'application, cliquez sur le bouton 'Lancer'."
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
