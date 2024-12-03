package application.view;


import com.fasterxml.jackson.databind.ObjectMapper;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;
import model.data.SyncData;

import java.nio.file.Path;
import java.nio.file.Paths;

public class RoomsViewControler {

    private Stage containingStage;


    @FXML
    Button btnVoirMesures;

    @FXML
    Button btnVoirGraphiques;

    @FXML
    Button btnRetour;

    @FXML
    ListView lvCapteurs;

    // Manipulation de la fenêtre

    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        this.configure();
        fillRoomList();
    }

    private void configure() {
        this.containingStage.setOnCloseRequest(e -> this.closeWindow(e));
    }

    // Gestion du stage
    private Object closeWindow(WindowEvent e) {
        this.containingStage.close();
        e.consume();
        return null;
    }

    public void show(){
        this.containingStage.showAndWait();
    }


    @FXML
    public void onBtnVoirMesures() {
        System.out.println("Bouton voir mesures cliqué");
    }

    @FXML
    public void onBtnVoirGraphiques() {
        System.out.println("Bouton voir graphiques cliqué");
    }

    @FXML
    public void onBtnRetour() {
        System.out.println("Bouton retour cliqué");
    }

    @FXML
    public void fillRoomList() {
        String directoryPath = "src/main/resources/data";
        ObjectMapper objectMapper = new ObjectMapper();
        // Création de l'objet Path représentant le dossier
        Path directory = Paths.get(directoryPath);//

        System.out.println(directory);

        SyncData rec = SyncData.getInstance();
        rec.fillRoomList();

    }

}