package application.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;

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
    public void fillRoomList(){

    }

}