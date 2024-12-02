package application;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import application.view.AccueilViewController;

public class App extends Application {

    private Stage primaryStage;

    @Override
    public void start(Stage stage) throws IOException {
        this.primaryStage = stage;

        
        // Chargement du fichier FXML
        FXMLLoader loader = new FXMLLoader(getClass().getResource("/application/view/accueilView.fxml"));
        BorderPane root = loader.load();

        // Configuration de la scène
        Scene scene = new Scene(root);
        this.primaryStage.setScene(scene);
        this.primaryStage.setTitle("Accueil");

        // Initialisation du contrôleur
        AccueilViewController controller = loader.getController();
        controller.initContext(this.primaryStage);
        controller.displayDialog();

        this.primaryStage.show(); // Affiche la fenêtre principale
    }

    public static void main(String[] args) {
        launch();
    }
}
