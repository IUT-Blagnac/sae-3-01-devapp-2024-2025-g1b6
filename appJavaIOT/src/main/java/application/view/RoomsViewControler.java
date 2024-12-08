package application.view;

import application.LaunchApp;
import application.tools.StageManagement;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.scene.control.SelectionMode;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;
import model.data.Room;
import application.SyncData;

import java.io.IOException;
import java.util.List;
import java.util.stream.Collectors;

/**
 * Contrôleur de la vue des salles dans l'application.
 * <p>Ce contrôleur gère l'interface graphique permettant de visualiser les capteurs des différentes salles,
 * avec des boutons pour afficher les mesures, les graphiques et revenir à la vue précédente.
 * La liste des salles est récupérée et affichée à l'ouverture de la fenêtre.
 *
 * @see model.data.Room
 * @see SyncData
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class RoomsViewControler {

    /** La fenêtre principale contenant cette vue. */
    private Stage containingStage;

    /** Bouton permettant de voir les mesures des capteurs. */
    @FXML
    Button btnVoirMesures;

    /** Bouton permettant de voir les graphiques des capteurs. */
    @FXML
    Button btnVoirGraphiques;

    /** Bouton permettant de revenir à la vue précédente. */
    @FXML
    Button btnRetour;

    /** Liste affichant les différentes salles et leurs capteurs associés. */
    @FXML
    ListView<Room> lvCapteurs;

    /** Champ de texte permettant de filtrer les salles affichées. */
    @FXML
    TextField txtFieldRechercher;

    /**
     * Initialise le contrôleur avec la fenêtre contenant cette vue.
     * <p>
     * Ce constructeur permet de lier cette vue à une fenêtre principale et d'initialiser la configuration de la vue.
     *
     * @param _containingStage La fenêtre principale qui contient cette vue.
     */
    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        this.configure();
    }

    /**
     * Configure les paramètres de la fenêtre et de la liste des capteurs.
     * <p>
     * Cette méthode configure la fenêtre en gérant l'événement de fermeture. Elle initialise également la liste des salles
     * en utilisant les données récupérées de la classe {@link SyncData}, et permet la sélection multiple dans la liste des salles.
     */
    private void configure() {
        this.containingStage.setOnCloseRequest(e -> this.closeWindow(e));
        SyncData inst = SyncData.getInstance();
        this.lvCapteurs.setItems(inst.getRoomsOlist());
        this.lvCapteurs.getSelectionModel().setSelectionMode(SelectionMode.MULTIPLE);
        this.lvCapteurs.getFocusModel().focus(-1);
    }

    /**
     * Ferme la fenêtre lorsque l'utilisateur tente de la fermer.
     * <p>
     * Cette méthode est appelée lors de la fermeture de la fenêtre pour effectuer des actions de nettoyage, telles que
     * l'arrêt de la sauvegarde périodique des données.
     *
     * @param e L'événement de fermeture de la fenêtre.
     * @return Un objet null après la fermeture de la fenêtre.
     */
    private Object closeWindow(WindowEvent e) {
        this.containingStage.close();
        SyncData syncInstance = SyncData.getInstance();
        syncInstance.stopPeriodicSave();
        e.consume();
        return null;
    }

    /**
     * Affiche la fenêtre et attend que l'utilisateur interagisse avec elle.
     * <p>
     * Cette méthode permet d'afficher la fenêtre principale contenant la vue des salles et de mettre l'application en
     * pause en attendant que l'utilisateur interagisse avec la fenêtre.
     */
    public void show(){
        this.containingStage.showAndWait();
    }

    /**
     * Gère le clic sur le bouton "Voir Mesures".
     * <p>
     * Cette méthode est appelée lorsqu'on clique sur le bouton permettant de voir les mesures des capteurs associés aux salles.
     * Actuellement, elle affiche un message dans la console pour indiquer que le bouton a été cliqué.
     */
    @FXML
    public void onBtnVoirMesures() {
        System.out.println("Bouton voir mesures cliqué");
    }

    /**
     * Gère le clic sur le bouton "Voir Graphiques".
     * <p>
     * Cette méthode est appelée lorsqu'on clique sur le bouton permettant de voir les graphiques associés aux capteurs.
     * Actuellement, elle affiche un message dans la console pour indiquer que le bouton a été cliqué.
     */
    @FXML
    public void onBtnVoirGraphiques() {
        System.out.println("Bouton voir graphiques cliqué");
    }

    /**
     * Gère le clic sur le bouton "Retour".
     * <p>
     * Cette méthode est appelée lorsqu'on clique sur le bouton permettant de revenir à la vue précédente.
     * Elle ferme la fenêtre actuelle et effectue les actions nécessaires à la fermeture de la fenêtre.
     */
    @FXML
    public void onBtnRetour() {
        this.closeWindow(null);
    }

    /**
     * Gère le clic sur le bouton "Rechercher".
     * <p>
     * Cette méthode est appelée lorsque l'utilisateur clique sur le bouton de recherche pour filtrer la liste des salles.
     * Elle récupère le texte du champ de recherche, le convertit en minuscules, et filtre la liste des salles en fonction
     * de ce texte (recherche par nom de salle).
     */
    @FXML
    private void onBtnRechercher() {
        String searchText = txtFieldRechercher.getText().toLowerCase(); // Texte entré en minuscules
        SyncData inst = SyncData.getInstance();
        List<Room> filteredRooms = inst.getRoomsOlist().stream()
                .filter(room -> room.getRoomName().toLowerCase().startsWith(searchText)) // Recherche par nom de salle
                .collect(Collectors.toList());

        // Met à jour la ListView avec les salles filtrées
        lvCapteurs.setItems(FXCollections.observableArrayList(filteredRooms));
    }

    /**
     * Gère le clic sur le bouton "Panneaux Solaires".
     * <p>
     * Cette méthode est appelée lorsqu'on clique sur le bouton pour afficher les graphiques relatifs aux panneaux solaires.
     * Elle charge une nouvelle fenêtre pour afficher les données des panneaux solaires, en utilisant le fichier FXML associé.
     */
    @FXML
    public void onBtnPanneauxSolaires(){
        FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/solarpanel.fxml"));
        Stage graphicsStage = new Stage();

        Scene graphicsScene = null;
        try {
            graphicsScene = new Scene(fxmlLoader.load(), 800, 600);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
        graphicsStage.setTitle("Panneaux Solaires");
        graphicsStage.setScene(graphicsScene);
        StageManagement.manageCenteringStage(this.containingStage, graphicsStage);
        graphicsStage.show();
        graphicsScene.getStylesheets().add(LaunchApp.class.getResource("css/styles.css").toExternalForm());
    }

    /**
     * Gère le clic sur le bouton "Mesures Alerte".
     * <p>
     * Cette méthode est appelée lorsqu'on clique sur le bouton pour afficher la page des alertes des mesures.
     * Elle charge une nouvelle fenêtre pour afficher les alertes, en utilisant le fichier FXML associé à la vue des alertes.
     */
    @FXML
    public void onBtnMesuresAlerte(){
        FXMLLoader fxmlLoader = new FXMLLoader(LaunchApp.class.getResource("view/AlertPageView.fxml"));
        Stage alertStage = new Stage();

        Scene alertScene = null;
        try {
            alertScene = new Scene(fxmlLoader.load(), 800, 400);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
        alertStage.setTitle("Toutes les alertes");
        alertStage.setScene(alertScene);
        StageManagement.manageCenteringStage(this.containingStage, alertStage);
        AlertPageViewController alCtrl = fxmlLoader.getController();
        alertStage.setResizable(false);
        alCtrl.initContext(alertStage);
        alertStage.show();
    }
}
