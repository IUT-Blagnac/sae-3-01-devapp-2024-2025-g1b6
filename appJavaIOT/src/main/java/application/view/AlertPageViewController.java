package application.view;

import application.SyncData;
import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.ListView;
import javafx.scene.control.SelectionMode;
import javafx.scene.control.cell.TextFieldListCell;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;
import javafx.util.Callback;
import model.data.Measure;
import model.data.Room;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

/**
 * Contrôleur de la vue des alertes dans l'application.
 * <p>
 * Cette classe est responsable de l'affichage de toutes les alertes relatives aux salles et leurs capteurs.
 * Elle affiche une liste des salles avec leurs mesures d'alerte sous forme de texte, et permet à l'utilisateur de consulter
 * ces alertes dans une interface graphique.
 *
 * @see model.data.Room
 * @see model.data.Measure
 * @see application.SyncData
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class AlertPageViewController {

    /** La fenêtre principale contenant cette vue. */
    private Stage containingStage;

    /** La carte des salles et de leurs mesures d'alerte associées. */
    private Map<Room, List<Measure>> alertMap;

    /** La ListView permettant d'afficher la liste des alertes des salles. */
    @FXML
    ListView<String> lvRooms;  // Change to ListView<String> to hold custom text for display

    /**
     * Initialise le contrôleur avec la fenêtre contenant cette vue.
     * <p>
     * Cette méthode lie le contrôleur à la fenêtre contenant la vue des alertes et configure les paramètres d'affichage.
     *
     * @param _containingStage La fenêtre principale qui contient cette vue.
     */
    public void initContext(Stage _containingStage) {
        this.containingStage = _containingStage;
        this.configure();
    }

    /**
     * Configure les paramètres de la fenêtre et de la liste des alertes.
     * <p>
     * Cette méthode récupère les salles et leurs mesures d'alerte, les prépare sous forme de texte personnalisé
     * pour l'affichage dans la ListView. Elle configure également l'affichage personnalisé des éléments de la liste.
     */
    private void configure() {
        // Initialiser alertMap
        this.alertMap = new java.util.HashMap<>();

        // Gérer l'événement de fermeture de la fenêtre
        this.containingStage.setOnCloseRequest(e -> this.closeWindow(e));

        // Récupérer les données des salles via SyncData
        SyncData inst = SyncData.getInstance();
        Map<String, Room> roomsMap = inst.getRoomsMap();

        // Ajouter les salles avec leurs alertes dans alertMap
        for (Room currentRoom : roomsMap.values()) {
            if (!currentRoom.getRoomAlertValues().isEmpty()) {
                alertMap.put(currentRoom, currentRoom.getRoomAlertValues());
            }
        }

        // Créer une liste pour afficher les données dans la ListView
        List<String> displayList = new ArrayList<>();

        // Ajouter les salles avec leurs mesures dans la liste à afficher
        for (Room room : roomsMap.values()) {
            if (!room.getRoomAlertValues().isEmpty()) {
                displayList.add("**" + room.getRoomName() + "**");  // Ajouter la salle en gras

                // Ajouter les mesures sous la salle avec une tabulation
                for (Measure measure : room.getRoomAlertValues()) {
                    displayList.add("\t" + measure.toString());  // Mesures avec tabulation
                }
            }
        }

        // Configurer la ListView pour afficher le texte personnalisé
        lvRooms.setItems(FXCollections.observableArrayList(displayList));

        // Utiliser un CellFactory pour personnaliser l'affichage des cellules
        lvRooms.setCellFactory(new Callback<ListView<String>, javafx.scene.control.ListCell<String>>() {
            @Override
            public javafx.scene.control.ListCell<String> call(ListView<String> param) {
                return new TextFieldListCell<String>() {
                    @Override
                    public void updateItem(String item, boolean empty) {
                        super.updateItem(item, empty);
                        if (item != null) {
                            if (item.startsWith("**")) {
                                // Afficher la salle en gras
                                setText(item.replace("**", ""));
                                setStyle("-fx-font-weight: bold;");
                            } else {
                                // Afficher les mesures avec une tabulation
                                setText(item);
                                setStyle("-fx-font-weight: normal;");
                            }
                        }
                    }
                };
            }
        });

        // Configurer la sélection des éléments dans la ListView
        this.lvRooms.getSelectionModel().setSelectionMode(SelectionMode.SINGLE);
        this.lvRooms.getFocusModel().focus(-1);
    }

    /**
     * Ferme la fenêtre lorsque l'utilisateur tente de la fermer.
     * <p>
     * Cette méthode est appelée lorsqu'un événement de fermeture de fenêtre est détecté.
     * Elle ferme la fenêtre et empêche l'action par défaut de la fenêtre de se produire.
     *
     * @param e L'événement de fermeture de la fenêtre.
     */
    private void closeWindow(WindowEvent e) {
        this.containingStage.close();  // Fermer la fenêtre
        e.consume();  // Consommer l'événement pour empêcher l'action par défaut
    }

    /**
     * Affiche la fenêtre et attend que l'utilisateur interagisse avec elle.
     * <p>
     * Cette méthode est utilisée pour afficher la fenêtre contenant la vue des alertes et mettre l'application en
     * pause en attendant l'interaction de l'utilisateur.
     */
    public void show() {
        this.containingStage.showAndWait();
    }
}
