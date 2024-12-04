package model.data;

import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

import java.io.File;
import java.io.IOException;
import java.nio.file.DirectoryStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Classe de gestion des données synchronisées provenant des fichiers de capteurs et des panneaux solaires.
 * <p>
 * Cette classe fournit des méthodes pour charger les données des capteurs et des panneaux solaires à partir de fichiers JSON,
 * les organiser dans des structures adaptées, et permettre l'accès aux informations des salles et des panneaux solaires.
 * Les données sont stockées sous forme de listes observables qui permettent de suivre les modifications des données en temps réel.
 *
 * @see model.data.Room
 * @see model.data.Measure
 * @see model.data.Solar
 * @author Yassir Boulouiha Gnaoui
 */
public class SyncData {

    private final Map<String, Room> roomsMap = new HashMap<>();
    private final ObservableList<Room> roomsOlist = FXCollections.observableArrayList();
    private final List<Solar> solarPanelValues = new ArrayList<>();
    private static SyncData uniqueInstance;

    /**
     * Méthode statique permettant de récupérer l'instance unique de {@link SyncData}.
     * <p>
     * Cette méthode applique le principe du singleton pour s'assurer qu'il n'existe qu'une seule instance de la classe {@link SyncData}.
     *
     * @return L'instance unique de {@link SyncData}.
     */
    public static synchronized SyncData getInstance() {
        if (uniqueInstance == null)
            uniqueInstance = new SyncData();
        return uniqueInstance;
    }

    /**
     * Remplir la liste des mesures d'une salle à partir des données d'un fichier.
     * <p>
     * Ce fichier contient des mesures pour une salle donnée. Les données sont extraites du fichier JSON et ajoutées à la salle correspondante.
     * Si la salle existe déjà, les nouvelles mesures sont ajoutées à la liste existante.
     * Si une alerte est activée, un message sera affiché (gestion future d'une popup).
     *
     * @param f Le fichier contenant les données de la salle.
     * @param objectMapper L'objet {@link ObjectMapper} utilisé pour la désérialisation JSON.
     * @param alert Si l'alerte est activée, cette valeur est vraie.
     * @throws IOException Si une erreur se produit lors de la lecture du fichier.
     */
    private void fillRoom(File f, ObjectMapper objectMapper, boolean alert) throws IOException {
        List<List<Map<String, Object>>> data = objectMapper.readValue(
                f,
                new TypeReference<List<List<Map<String, Object>>>>() {}
        );
        String roomName = f.getName().substring(0, f.getName().length() - 5);

        if (this.roomsMap.containsKey(roomName)) {
            // Si la salle existe déjà
            Room currentRoom = this.getRoomsMap().get(roomName);
            int size = currentRoom.getRoomValues().size();
            for (int i = size; i < data.size(); i++) {
                if (!alert)
                    currentRoom.getRoomValues().add(new Measure(data.get(i).get(0), alert));
                else System.out.println("popup Alert"); // TODO: Gestion des alertes par popup
            }
        } else {
            // Si la salle n'existe pas, créer une nouvelle entrée
            for (List<Map<String, Object>> list : data) {
                if (!alert) {
                    this.roomsMap.put(roomName, new Room(roomName,
                            ((String) data.get(0).get(1).get("Building")).charAt(0),
                            (int) data.get(0).get(1).get("floor")));
                    Room currentRoom = this.roomsMap.get(roomName);
                    currentRoom.getRoomValues().add(new Measure(list.get(0), alert));
                } else {
                    System.out.println("popup Alert"); // TODO: Gestion des alertes par popup
                }
            }
        }
    }

    /**
     * Remplir la liste des panneaux solaires à partir des données d'un fichier.
     * <p>
     * Ce fichier contient les données relatives à un panneau solaire, qui sont extraites du fichier JSON et ajoutées à la liste des panneaux solaires.
     *
     * @param f Le fichier contenant les données du panneau solaire.
     * @param objectMapper L'objet {@link ObjectMapper} utilisé pour la désérialisation JSON.
     * @throws IOException Si une erreur se produit lors de la lecture du fichier.
     */
    private void fillSolar(File f, ObjectMapper objectMapper) throws IOException {
        List<Map<String, Object>> data = objectMapper.readValue(
                f,
                new TypeReference<List<Map<String, Object>>>() {}
        );
        for (int i = this.solarPanelValues.size(); i < data.size(); i++) {
            this.solarPanelValues.add(new Solar(
                    (String) data.get(i).get("lastUpdateTime"),
                    ((Number) ((Map<String, Object>) data.get(i).get("lifeTimeData")).get("energy")).floatValue(),
                    ((Number) ((Map<String, Object>) data.get(i).get("lastYearData")).get("energy")).floatValue(),
                    ((Number) ((Map<String, Object>) data.get(i).get("lastMonthData")).get("energy")).floatValue(),
                    ((Number) ((Map<String, Object>) data.get(i).get("lastDayData")).get("energy")).floatValue(),
                    ((Number) ((Map<String, Object>) data.get(i).get("currentPower")).get("power")).floatValue()
            ));
        }
    }

    /**
     * Remplir la liste des salles et panneaux solaires à partir des fichiers JSON dans un répertoire.
     * <p>
     * Cette méthode parcourt un répertoire contenant des sous-dossiers pour charger les données des salles et panneaux solaires.
     * Les données sont extraites et ajoutées aux listes correspondantes.
     */
    public void fillRoomList() {
        String directoryPath = "src/main/resources/data";
        ObjectMapper objectMapper = new ObjectMapper();
        Path directory = Paths.get(directoryPath);

        System.out.println(directory);

        // Parcours du répertoire contenant les fichiers de données
        try (DirectoryStream<Path> stream = Files.newDirectoryStream(directory)) {
            for (Path dir : stream) {
                if (Files.isDirectory(dir) && dir.getFileName().toString().equals("Alert")) {
                    try (DirectoryStream<Path> alertStream = Files.newDirectoryStream(dir)) {
                        for (Path alertFile : alertStream) {
                            fillRoom(alertFile.toFile(), objectMapper, true);
                        }
                    }
                } else if (Files.isDirectory(dir) && dir.getFileName().toString().equals("SolarPanel")) {
                    try (DirectoryStream<Path> solarStream = Files.newDirectoryStream(dir)) {
                        for (Path solarFile : solarStream) fillSolar(solarFile.toFile(), objectMapper);
                    }
                } else {
                    try (DirectoryStream<Path> building = Files.newDirectoryStream(dir)) {
                        for (Path room : building) {
                            fillRoom(room.toFile(), objectMapper, false);
                        }
                    }
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }

        this.roomsOlist.setAll(this.roomsMap.values());
    }

    /**
     * Retourne la map des salles, où la clé est le nom de la salle et la valeur est l'objet {@link Room}.
     *
     * @return La map des salles.
     */
    public Map<String, Room> getRoomsMap() {
        return this.roomsMap;
    }

    /**
     * Retourne la liste observable des salles, permettant une mise à jour en temps réel dans l'interface utilisateur.
     *
     * @return La liste observable des salles.
     */
    public ObservableList<Room> getRoomsOlist() {
        return roomsOlist;
    }

    /**
     * Retourne la liste des panneaux solaires.
     *
     * @return La liste des panneaux solaires.
     */
    public List<Solar> getSolarPanelValues() {
        return this.solarPanelValues;
    }
}
