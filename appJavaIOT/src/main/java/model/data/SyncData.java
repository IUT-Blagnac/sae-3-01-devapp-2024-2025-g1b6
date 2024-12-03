package model.data;

import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;

import java.io.File;
import java.io.IOException;
import java.nio.file.DirectoryStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.List;
import java.util.Map;

public class SyncData {

    private Map<String, Room> roomsMap;
    private static SyncData uniqueInstance;

    public static synchronized SyncData getInstance() {
        if (uniqueInstance == null)
            uniqueInstance = new SyncData();

        return uniqueInstance;
    }

    public Map<String,Room> getRoomsMap(){
        return this.roomsMap;
    }

    private void fillRoom (File f, ObjectMapper objectMapper, boolean alert) throws IOException {
        List<List<Map<String, Object>>> data = objectMapper.readValue(
                f,
                new TypeReference<List<List<Map<String, Object>>>>() {}
        );
        String roomName = f.getName().toString().substring(0, f.getName().toString().length() - 5);

        if (this.roomsMap.containsKey(roomName)){
            //Si la salle existe
            Room currentRoom = this.getRoomsMap().get(roomName);
            System.out.println(currentRoom);
            int size = currentRoom.getRoomValues().size();
            for (int i = size; i < data.size(); i++){
                currentRoom.getRoomValues().add(new Measure(data.get(i).get(0),
                        alert));
            }
        }else {
            //Si jamis la salle n'existe pas
            Room currentRoom = this.roomsMap.put(roomName, new Room(roomName,
                    (char) data.get(0).get(1).get("Building"),
                    (int) data.get(0).get(1).get("floor")));
            System.out.println(currentRoom);
            for (List<Map<String, Object>> list : data)
                currentRoom.getRoomValues().add(new Measure(list.get(0), alert));

        }
    }

    private void fillSolar(File f, ObjectMapper objectMapper) throws IOException {
        List<Map<String, Object>> data = objectMapper.readValue(
                f,
                new TypeReference<List<Map<String, Object>>>() {}
        );
        String roomName = f.getName().toString().substring(0, f.getName().toString().length() - 5);
    }

    public void fillRoomList(){
        String directoryPath = "src/main/resources/data";
        ObjectMapper objectMapper = new ObjectMapper();
        // Création de l'objet Path représentant le dossier
        Path directory = Paths.get(directoryPath);//

        System.out.println(directory);

        // Utilisation d'un DirectoryStream pour parcourir le dossier
        try (DirectoryStream<Path> stream = Files.newDirectoryStream(directory)) {
            for (Path dir : stream) {

                if (Files.isDirectory(dir) && dir.getFileName().toString().equals("Alert")){
                    try (DirectoryStream<Path> alertStream = Files.newDirectoryStream(dir)){
                        for (Path alertFile : alertStream)
                            fillRoom(alertFile.toFile(), objectMapper,true);
                    }
                }else if (Files.isDirectory(dir) && dir.getFileName().toString().equals("SolarPanel")) {
                    //TODO
                    try (DirectoryStream<Path> solarStream = Files.newDirectoryStream(dir)){
                        for (Path solarFile : solarStream) {
                            fillSolar(solarFile.toFile(), objectMapper);
                        }
                    }
                }else {
                    try (DirectoryStream<Path> building = Files.newDirectoryStream(dir)) {
                        for (Path room : building) {
                            fillRoom(room.toFile(), objectMapper, true);
                        }
                    }
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
