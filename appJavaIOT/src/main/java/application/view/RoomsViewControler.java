package application.view;

import com.fasterxml.jackson.databind.ObjectMapper;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.SelectionMode;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;
import model.data.Room;
import model.data.SyncData;

import java.nio.file.Path;
import java.nio.file.Paths;
import java.text.Collator;
import java.util.Locale;

/**
 * Contrôleur de la vue des salles dans l'application.
 * <p>Ce contrôleur gère l'interface graphique permettant de visualiser les capteurs des différentes salles,
 * avec des boutons pour afficher les mesures, les graphiques et revenir à la vue précédente.
 * La liste des salles est récupérée et affichée à l'ouverture de la fenêtre.
 *
 * @see model.data.Room
 * @see model.data.SyncData
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class RoomsViewControler {

    private Stage containingStage;

    @FXML
    Button btnVoirMesures;

    @FXML
    Button btnVoirGraphiques;

    @FXML
    Button btnRetour;

    @FXML
    ListView<Room> lvCapteurs;

    /**
     * Initialise le contrôleur avec la fenêtre contenant cette vue.
     *
     * @param _containingStage La fenêtre principale qui contient cette vue.
     */
    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        this.configure();
        fillRoomList();
    }

    /**
     * Configure les paramètres de la fenêtre et de la liste des capteurs.
     */
    private void configure() {
        this.containingStage.setOnCloseRequest(e -> this.closeWindow(e));
        SyncData inst = SyncData.getInstance();
        this.lvCapteurs.setItems(inst.getRoomsOlist());
        this.lvCapteurs.getSelectionModel().setSelectionMode(SelectionMode.SINGLE);
        this.lvCapteurs.getFocusModel().focus(-1);
    }

    /**
     * Ferme la fenêtre lorsque l'utilisateur tente de la fermer.
     *
     * @param e L'événement de fermeture de la fenêtre.
     * @return Un objet null après la fermeture de la fenêtre.
     */
    private Object closeWindow(WindowEvent e) {
        this.containingStage.close();
        e.consume();
        return null;
    }

    /**
     * Affiche la fenêtre et attend que l'utilisateur interagisse avec elle.
     */
    public void show(){
        this.containingStage.showAndWait();
    }

    /**
     * Gère le clic sur le bouton "Voir Mesures".
     * Affiche un message dans la console lorsque ce bouton est cliqué.
     */
    @FXML
    public void onBtnVoirMesures() {
        System.out.println("Bouton voir mesures cliqué");
    }

    /**
     * Gère le clic sur le bouton "Voir Graphiques".
     * Affiche un message dans la console lorsque ce bouton est cliqué.
     */
    @FXML
    public void onBtnVoirGraphiques() {
        System.out.println("Bouton voir graphiques cliqué");
    }

    /**
     * Gère le clic sur le bouton "Retour".
     * Affiche un message dans la console lorsque ce bouton est cliqué.
     */
    @FXML
    public void onBtnRetour() {
        System.out.println("Bouton retour cliqué");
    }

    /**
     * Remplit la liste des salles à afficher dans la vue.
     * Récupère la liste des salles depuis l'instance {@link SyncData} et la met à jour dans la vue.
     */
    @FXML
    public void fillRoomList() {
        SyncData rec = SyncData.getInstance();
        rec.fillRoomList();
        System.out.println(rec.getSolarPanelValues());
    }
}
