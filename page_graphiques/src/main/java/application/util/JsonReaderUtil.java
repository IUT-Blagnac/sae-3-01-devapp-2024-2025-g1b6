package application.util;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import application.model.DeviceMetaData;
import application.model.SensorData;

import java.io.FileReader;
import java.io.IOException;

public class JsonReaderUtil {

    public static SensorData loadSensorData(String path) throws IOException {
        Gson gson = new Gson();
        try (FileReader reader = new FileReader(path)) {
            JsonArray jsonArray = JsonParser.parseReader(reader).getAsJsonArray();

            // Extraire la première partie des données (ex: co2)
            JsonObject dataObject = jsonArray.get(0).getAsJsonObject();
            return gson.fromJson(dataObject, SensorData.class);
        }
    }

    public static DeviceMetaData loadDeviceMetadata(String path) throws IOException {
        Gson gson = new Gson();
        try (FileReader reader = new FileReader(path)) {
            JsonArray jsonArray = JsonParser.parseReader(reader).getAsJsonArray();

            // Extraire la deuxième partie des données (ex: deviceName, room)
            JsonObject metadataObject = jsonArray.get(1).getAsJsonObject();
            return gson.fromJson(metadataObject, DeviceMetaData.class);
        }
    }
}
