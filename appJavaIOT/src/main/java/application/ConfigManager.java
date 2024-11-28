package application;

import java.io.*;
import java.util.LinkedHashMap;
import java.util.Map;

public class ConfigManager {

    private static final String CONFIG_FILE = "config.ini";
    private final Map<String, String> configMap = new LinkedHashMap<>();
    private final StringBuilder comments = new StringBuilder();

    public ConfigManager() {
        checkOrCreateConfigFile();
    }

    public void checkOrCreateConfigFile() {
        File configFile = new File(CONFIG_FILE);
        if (!configFile.exists()) {
            try (BufferedWriter writer = new BufferedWriter(new FileWriter(configFile))) {
                writer.write(getDefaultConfig());
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    public void loadConfig() throws IOException {
        configMap.clear();
        comments.setLength(0);

        try (BufferedReader reader = new BufferedReader(new FileReader(CONFIG_FILE))) {
            String line;
            while ((line = reader.readLine()) != null) {
                if (line.startsWith("#") || line.trim().isEmpty()) {
                    comments.append(line).append("\n");
                } else if (line.contains(":") || line.contains("=")) {
                    String[] parts = line.split("[:=]", 2);
                    configMap.put(parts[0].trim(), parts[1].trim());
                }
            }
        }
    }

    public void updateConfig(String key, String value) {
        configMap.put(key, value);
    }

    public void saveConfig() throws IOException {
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(CONFIG_FILE))) {
            writer.write(getDefaultConfig()); // Réécrit le fichier avec les commentaires initiaux.
            for (Map.Entry<String, String> entry : configMap.entrySet()) {
                String section = "[" + entry.getKey().split("\\.")[0] + "]";
                if (!comments.toString().contains(section)) {
                    writer.write("\n" + section + "\n");
                }
                writer.write(entry.getKey() + " : " + entry.getValue() + "\n");
            }
        }
    }

    private String getDefaultConfig() {
        return """
            [General]
            # Hôte mqtt sur le quel se connecter
            host : mqtt.iut-blagnac.fr
            # Fréquence à laquelle seront sauvegardées les données en minutes
            frequence : 1

            [Capteurs]
            # Permet de s'abonner a toutes les salles comportant des capteurs à l'iut
            # Valeurs possibles {on, off}
            subscribe_all : off

            [Panneaux Solaires]
            # Permet de s'abonner au topic des panneaux solaires
            # Valeurs possibles {on, off}
            subscribe_all : off

            [Seuils Alerte]
            # Définit la valeur minimum et maximum pour chaque type de données toutes les valeurs en dehors de l'interval [min, max]
            # seront enregistrées dans un dossier spécifique regroupant toutes les mesures au dessus ou en dessous d'un seuil d'alerte min ou max
            co2Min : 400
            co2Max : 1000
            temperatureMin : 80
            temperatureMax : 0
            humidityMin : 40
            humidityMax : 60
            activityMin : 0
            activityMax : 100
            """;
    }

    public String readConfig(String key) {
        return configMap.getOrDefault(key, "");
    }
}
