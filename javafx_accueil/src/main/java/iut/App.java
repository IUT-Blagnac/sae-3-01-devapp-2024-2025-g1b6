package iut;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import javafx.scene.layout.BorderPane;

import java.io.IOException;

/**
 * JavaFX App
 */
public class App extends Application {

    private static Scene scene;
    private Stage primaryStage;

    @Override
    public void start(Stage stage) throws IOException {

        this.primaryStage = stage;
        

        FXMLLoader loader = new FXMLLoader(AccueilController.class.getResource("primary.fxml"));
        BorderPane root = loader.load();

        Scene scene = new Scene(root);
        this.primaryStage.setScene(scene);
        this.primaryStage.setTitle("Accueil");

        AccueilController controller = loader.getController();
        controller.initContext(this.primaryStage);
        controller.displayDialog();

    }

    static void setRoot(String fxml) throws IOException {
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