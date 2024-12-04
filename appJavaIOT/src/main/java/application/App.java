package application;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;

import application.view.GraphiquesController;

/**
 * JavaFX App
 */
public class App extends Application {

    private static Scene scene;
    private GraphiquesController graphiquesController;

    @Override
    public void start(Stage stage) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(App.class.getResource("view/solarpanel.fxml"));
        Parent root = fxmlLoader.load();
        graphiquesController = fxmlLoader.getController(); // Récupérer le contrôleur

        scene = new Scene(root, 800, 600); // Ajustez la taille selon vos besoins
        stage.setScene(scene);
        scene.getStylesheets().add(App.class.getResource("css/styles.css").toExternalForm());
        stage.setTitle("Application de Graphiques");

        stage.setOnCloseRequest(event -> {
            graphiquesController.stop(); // Arrêter les threads du contrôleur
            System.exit(0); // Forcer la fermeture complète de l'application
        });

        stage.show();
    }

    public static void setRoot(String fxml) throws IOException {
        scene.setRoot(loadFXML(fxml));
    }

    private static Parent loadFXML(String fxml) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(App.class.getResource(fxml + ".fxml"));
        return fxmlLoader.load();
    }

    public static void main(String[] args) {
        launch();
    }
}
