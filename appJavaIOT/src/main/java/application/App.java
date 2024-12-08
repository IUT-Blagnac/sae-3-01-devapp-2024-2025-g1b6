package application;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;

import application.model.Room;
import application.model.SyncData;
import application.view.SalleDetailsController;

/**
 * JavaFX App
 */
public class App extends Application {

    private static Scene scene;

    @Override
    public void start(Stage stage) throws IOException {
        // Exemple : Chargez une salle spécifique (nommée ici "Salle 1") pour démonstration
        SyncData syncData = SyncData.getInstance();
        syncData.fillRoomList(); // Charger les données des salles
        Room specificRoom = syncData.getRoomsMap().get("B108");

        // Charger la vue SalleDetails avec une salle spécifique
        FXMLLoader fxmlLoader = new FXMLLoader(App.class.getResource("view/SalleDetailsView.fxml"));
        Parent root = fxmlLoader.load();

        // Obtenir le contrôleur et configurer la salle
        SalleDetailsController salleDetailsController = fxmlLoader.getController();
        salleDetailsController.setRoom(specificRoom);

        // Configurer la scène
        scene = new Scene(root, 800, 600); // Ajustez la taille selon vos besoins
        scene.getStylesheets().add(App.class.getResource("css/styles.css").toExternalForm());
        stage.setScene(scene);
        stage.setTitle("Détails de la Salle : " + specificRoom.getRoomName());

        // Gérer la fermeture
        stage.setOnCloseRequest(event -> {
            salleDetailsController.stop(); // Libérer les ressources si nécessaire
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
