package application;

import application.tasks.PythonScriptRunner;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.control.Alert;
import model.data.Measure;
import model.data.Room;
import model.data.Solar;

import java.io.File;
import java.io.IOException;
import java.nio.file.*;
import java.time.LocalDateTime;
import java.util.*;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.ScheduledFuture;
import java.util.concurrent.TimeUnit;

/**
 * Classe de gestion des données synchronisées provenant des fichiers de capteurs et des panneaux solaires.
 * <p>
 * Cette classe est responsable de la gestion des données des capteurs des différentes salles et des panneaux solaires.
 * Elle charge les données à partir de fichiers JSON, les organise dans des structures de données adaptées et permet
 * une mise à jour en temps réel de l'interface utilisateur via des listes observables.
 * <p>
 * La classe suit le modèle singleton et garantit qu'il n'existe qu'une seule instance qui centralise l'accès aux données
 * des capteurs et panneaux solaires.
 * <p>
 * Elle offre aussi la possibilité de gérer des alertes en fonction des mesures des capteurs, avec une interface qui informe
 * l'utilisateur en cas de nouvelles alertes.
 *
 * @see model.data.Room
 * @see model.data.Measure
 * @see model.data.Solar
 * @see application.tasks.PythonScriptRunner
 * @see javafx.collections.ObservableList
 * @author Yassir Boulouiha Gnaoui
 */
public class SyncData {

    private final Map<String, Room> roomsMap = new HashMap<>();
    private final ObservableList<Room> roomsOlist = FXCollections.observableArrayList();
    private final List<Solar> solarPanelValues = new ArrayList<>();
    private Map<String, Measure> newAlertMeasures = new HashMap<>();
    private PeriodicPythonTask perPythonTask = null;
    private static SyncData uniqueInstance;

    /**
     * Méthode statique permettant de récupérer l'instance unique de {@link SyncData}.
     * <p>
     * Cette méthode applique le principe du singleton pour s'assurer qu'il n'existe qu'une seule instance de la classe {@link SyncData}.
     *
     * @return L'instance unique de {@link SyncData}.
     */
    public static synchronized SyncData getInstance() {
        if (uniqueInstance == null) {
            uniqueInstance = new SyncData();
        }
        return uniqueInstance;
    }

    /**
     * Remplir la liste des mesures d'une salle à partir des données d'un fichier JSON.
     * <p>
     * Cette méthode prend un fichier JSON contenant des mesures pour une salle donnée. Si la salle existe déjà, les nouvelles
     * mesures sont ajoutées à la liste des mesures de la salle. Si une alerte est activée (le paramètre `alert` est vrai), les
     * mesures d'alerte sont ajoutées séparément et un mécanisme d'alerte est déclenché.
     * <p>
     * Les données sont extraites du fichier JSON et ajoutées à la salle correspondante (en mettant à jour les informations sur
     * les mesures ou les alertes).
     *
     * @param f Le fichier contenant les données de la salle. Ce fichier doit être un fichier JSON valide.
     * @param objectMapper L'objet {@link ObjectMapper} utilisé pour désérialiser le contenu JSON.
     * @param alert Si `true`, cela indique que les mesures doivent être traitées comme des alertes (ajoutées aux alertes de la salle).
     * @throws IOException Si une erreur se produit lors de la lecture du fichier JSON.
     */
    private void fillRoom(File f, ObjectMapper objectMapper, boolean alert) throws IOException {
        List<List<Map<String, Object>>> data = objectMapper.readValue(
                f,
                new TypeReference<List<List<Map<String, Object>>>>() {}
        );
        String roomName = f.getName().substring(0, f.getName().length() - 5); // On retire l'extension ".json"

        if (this.roomsMap.containsKey(roomName)) {
            // Si la salle existe déjà
            Room currentRoom = this.getRoomsMap().get(roomName);
            int size = currentRoom.getRoomValues().size();
            for (int i = size; i < data.size(); i++) {
                if (!alert) {
                    currentRoom.getRoomValues().add(new Measure(data.get(i).get(0), LocalDateTime.now()));
                } else {
                    Measure newAlertMeasure = new Measure(data.get(i).get(0), LocalDateTime.now());
                    currentRoom.getRoomAlertValues().add(newAlertMeasure);
                    newAlertMeasures.put(currentRoom.getRoomName(), newAlertMeasure);
                }
            }
        } else {
            // Si la salle n'existe pas, on crée une nouvelle entrée
            for (List<Map<String, Object>> list : data) {
                this.roomsMap.put(roomName, new Room(roomName,
                        ((String) data.get(0).get(1).get("Building")).charAt(0),
                        (int) data.get(0).get(1).get("floor")));
                Room currentRoom = this.roomsMap.get(roomName);
                if (!alert) {
                    currentRoom.getRoomValues().add(new Measure(list.get(0), LocalDateTime.now()));
                } else {
                    Measure newAlertMeasure = new Measure(list.get(0), LocalDateTime.now());
                    currentRoom.getRoomAlertValues().add(newAlertMeasure);
                    newAlertMeasures.put(currentRoom.getRoomName(), newAlertMeasure);
                }
            }
        }
    }

    /**
     * Remplir la liste des panneaux solaires à partir des données d'un fichier JSON.
     * <p>
     * Cette méthode prend un fichier JSON contenant des informations sur un panneau solaire et extrait les données associées
     * pour les ajouter à la liste des panneaux solaires.
     *
     * @param f Le fichier contenant les données du panneau solaire, formaté en JSON.
     * @param objectMapper L'objet {@link ObjectMapper} utilisé pour la désérialisation du contenu JSON.
     * @throws IOException Si une erreur se produit lors de la lecture du fichier JSON.
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
     * Remplir la liste des salles et panneaux solaires à partir des fichiers JSON situés dans un répertoire spécifique.
     * <p>
     * Cette méthode parcourt un répertoire contenant des sous-dossiers et charge les données des salles et panneaux solaires
     * depuis les fichiers JSON trouvés. Les fichiers sont triés en fonction de leur contenu (panneaux solaires, alertes, etc.)
     * et les données sont ajoutées aux structures appropriées.
     */
    public void fillRoomList() {
        String directoryPath = "src/main/resources/data";
        ObjectMapper objectMapper = new ObjectMapper();
        Path directory = Paths.get(directoryPath);

        if (directory.toFile().exists()) {
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
        }

        this.roomsOlist.setAll(this.roomsMap.values());
        Platform.runLater(() -> {
            // Affichage des alertes si elles existent
            Iterator<Map.Entry<String, Measure>> iterator = newAlertMeasures.entrySet().iterator();
            while (iterator.hasNext()) {
                Map.Entry<String, Measure> alertMeasure = iterator.next();
                Alert alert = new Alert(Alert.AlertType.WARNING);
                alert.setTitle("Mesure d'alerte");
                alert.setHeaderText("Une mesure d'alerte a été enregistrée pour la salle " + alertMeasure.getKey());
                alert.setContentText("Mesure d'alerte : \n" + alertMeasure.getValue());
                alert.showAndWait();
            }
            newAlertMeasures.clear();
        });
    }

    /**
     * Retourne la map des salles, où la clé est le nom de la salle et la valeur est l'objet {@link Room}.
     * <p>
     * Cette méthode permet d'accéder directement à la map des salles en mémoire. Elle peut être utilisée pour obtenir
     * des informations détaillées sur une salle donnée.
     *
     * @return La map des salles, où la clé est le nom de la salle.
     */
    public Map<String, Room> getRoomsMap() {
        return this.roomsMap;
    }

    /**
     * Retourne la liste observable des salles.
     * <p>
     * Cette liste permet une mise à jour en temps réel de l'interface utilisateur, afin que les ajouts ou modifications
     * des salles dans la classe {@link SyncData} se reflètent instantanément dans la vue.
     *
     * @return La liste observable des salles.
     */
    public ObservableList<Room> getRoomsOlist() {
        return roomsOlist;
    }

    /**
     * Retourne la liste des panneaux solaires.
     * <p>
     * Cette méthode permet d'obtenir la liste des panneaux solaires disponibles et de la mettre à jour.
     *
     * @return La liste des panneaux solaires.
     */
    public List<Solar> getSolarPanelValues() {
        return this.solarPanelValues;
    }

    /**
     * Démarre la sauvegarde périodique des données via un script Python.
     * <p>
     * Cette méthode permet de démarrer l'exécution périodique d'un script Python à une fréquence donnée en minutes.
     * Le script Python sera exécuté à la fréquence spécifiée et le processus sera géré en arrière-plan.
     *
     * @param frequency La fréquence d'exécution du script Python en minutes.
     */
    public void startPeriodicSave(int frequency){
        if (this.perPythonTask == null) {
            this.perPythonTask = new PeriodicPythonTask(frequency);
            this.perPythonTask.start();
        }
    }

    /**
     * Arrête la sauvegarde périodique des données et le script Python en cours d'exécution.
     * <p>
     * Cette méthode permet d'arrêter toute tâche périodique en cours et de stopper l'exécution du script Python.
     */
    public void stopPeriodicSave() {
        if (this.perPythonTask != null) {
            this.perPythonTask.stop();
        }
    }

    /**
     * Efface toutes les données des salles et des panneaux solaires.
     * <p>
     * Cette méthode permet de vider toutes les informations actuellement stockées en mémoire, notamment les salles et les panneaux solaires.
     */
    public void clearData() {
        roomsMap.clear();
    }

    /**
     * Classe interne responsable de la gestion des tâches périodiques pour l'exécution d'un script Python.
     * <p>
     * Cette classe permet de lancer périodiquement un script Python à un intervalle spécifié en minutes.
     * Elle gère l'exécution du script Python, l'arrêt du script, et la planification des tâches périodiques.
     * Lors de chaque exécution, la tâche lancera la méthode {@link SyncData#fillRoomList()} pour récupérer
     * les dernières données des salles et des panneaux solaires.
     * <p>
     * La classe utilise un planificateur de tâches (via {@link ScheduledExecutorService}) pour effectuer les tâches
     * à intervalles réguliers. L'exécution du script Python se fait dans un thread séparé.
     *
     * @author Yassir Boulouiha Gnaoui
     * @see ScheduledExecutorService
     * @see PythonScriptRunner
     * @see SyncData#fillRoomList()
     */
    private class PeriodicPythonTask {

        private final ScheduledExecutorService scheduler = Executors.newScheduledThreadPool(1);
        private ScheduledFuture<?> scheduledFuture;
        private int frequency; // La fréquence d'exécution en minutes
        private Thread pythonThread;
        private final PythonScriptRunner pythonScriptRunner;

        /**
         * Constructeur de {@link PeriodicPythonTask}.
         * <p>
         * Ce constructeur initialise la fréquence d'exécution du script Python et crée une instance du runner Python
         * pour l'exécution du script. La fréquence d'exécution doit être un entier strictement positif.
         *
         * @param initialFrequency La fréquence d'exécution du script Python en minutes.
         * @throws IllegalArgumentException Si la fréquence spécifiée est inférieure ou égale à 0.
         */
        public PeriodicPythonTask(int initialFrequency) {
            if (initialFrequency <= 0) {
                throw new IllegalArgumentException("La fréquence doit être un entier strictement positif.");
            }
            this.frequency = initialFrequency;
            this.pythonScriptRunner = new PythonScriptRunner("src/main/python/main.py");
        }

        /**
         * Démarre l'exécution du script Python et programme la tâche périodique.
         * <p>
         * Cette méthode lance l'exécution du script Python dans un thread séparé, puis planifie son exécution périodique
         * avec la fréquence spécifiée lors de l'initialisation.
         */
        public void start() {
            startPythonScript();
            scheduleTask();
        }

        /**
         * Lance le script Python dans un thread séparé.
         * <p>
         * Cette méthode crée et démarre un thread pour exécuter le script Python via l'objet {@link PythonScriptRunner}.
         * Avant de démarrer un nouveau script, elle s'assure qu'une exécution précédente a été correctement arrêtée.
         */
        private void startPythonScript() {
            stopPythonScript(); // Arrête le script Python s'il est déjà en cours d'exécution
            pythonThread = new Thread(pythonScriptRunner);
            pythonThread.start();
        }

        /**
         * Arrête l'exécution du script Python en cours.
         * <p>
         * Cette méthode appelle la méthode {@link PythonScriptRunner#stop()} pour arrêter l'exécution du script Python
         * et attend la fin du thread exécutant le script.
         */
        private void stopPythonScript() {
            pythonScriptRunner.stop();

            if (pythonThread != null) {
                try {
                    pythonThread.join(); // Attend que le thread Python termine son exécution
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
            }
        }

        /**
         * Programme l'exécution périodique de la tâche.
         * <p>
         * Cette méthode configure l'exécution de la tâche à des intervalles réguliers selon la fréquence spécifiée.
         * Elle utilise un planificateur de tâches pour exécuter la méthode `runTask` à chaque période, en commençant
         * après un délai initial.
         */
        private void scheduleTask() {
            long initialDelay = 3; // Délai initial en secondes avant la première exécution du script Python
            if (scheduledFuture != null) {
                scheduledFuture.cancel(false); // Annule toute exécution précédente en attente
            }
            scheduledFuture = scheduler.scheduleAtFixedRate(this::runTask, initialDelay, frequency * 60L, TimeUnit.SECONDS);
        }

        /**
         * Exécute la tâche périodique qui récupère les données mises à jour.
         * <p>
         * Cette méthode est exécutée à chaque période planifiée. Elle appelle la méthode {@link SyncData#fillRoomList()},
         * permettant de mettre à jour les données des salles et des panneaux solaires à partir des fichiers JSON.
         */
        private void runTask() {
            Platform.runLater(SyncData.this::fillRoomList); // Met à jour les données des salles et des panneaux solaires
        }

        /**
         * Arrête la tâche périodique et le script Python en cours d'exécution.
         * <p>
         * Cette méthode arrête la tâche périodique en cours, annule la planification d'exécution future et arrête le script Python
         * en appelant la méthode {@link #stopPythonScript()}.
         */
        public void stop() {
            if (scheduledFuture != null) {
                scheduledFuture.cancel(false); // Annule la tâche périodique
            }
            stopPythonScript(); // Arrête l'exécution du script Python
            scheduler.shutdown(); // Arrête le planificateur de tâches
        }
    }

}
