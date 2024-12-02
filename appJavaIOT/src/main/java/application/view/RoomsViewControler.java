package application.view;


import com.fasterxml.jackson.core.JsonParser;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ListView;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;

import java.io.IOException;
import java.nio.file.DirectoryStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.List;
import java.util.Map;

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
    public void fillRoomList(){
        String directoryPath = "src/main/resources/data";
        ObjectMapper objectMapper = new ObjectMapper();
        // Création de l'objet Path représentant le dossier
        Path directory = Paths.get(directoryPath);//

        System.out.println(directory);

        // Utilisation d'un DirectoryStream pour parcourir le dossier
        try (DirectoryStream<Path> stream = Files.newDirectoryStream(directory)) {
          for (Path dir : stream) {

              try (DirectoryStream<Path> subStream = Files.newDirectoryStream(dir)){
                  for (Path file : subStream){
                      if (Files.isDirectory(file) && file.getFileName().toString().equals("Alert")){
                          try (DirectoryStream<Path> alertStream = Files.newDirectoryStream(file)){
                              for (Path alertFile : alertStream) {
                                  System.out.println("Fichier alerte : " + alertFile.getFileName());
                              }
                          }
                      }else {

                          if (file.getFileName().equals("solar.json")) {
                              List<Map<String, Object>> solarData = objectMapper.readValue(
                                      file.toFile(),
                                      new TypeReference<List<Map<String, Object>>>() {}
                              );
                              System.out.println(solarData.getLast().get("lifeTimeData"));
                          }else {
                              List<List<Map<String, Object>>> data = objectMapper.readValue(
                                      file.toFile(),
                                      new TypeReference<List<List<Map<String, Object>>>>() {}
                              );
                          }
                      }

                  }
              }catch (IOException e) {
                  e.printStackTrace();
              }

          }
        } catch (IOException e) {
          e.printStackTrace();
        }
    }

}