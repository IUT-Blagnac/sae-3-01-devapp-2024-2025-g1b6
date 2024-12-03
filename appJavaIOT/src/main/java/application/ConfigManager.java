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

    // Vérifie ou crée le fichier config.ini
    public void checkOrCreateConfigFile() {
        File configFile = new File(CONFIG_FILE);
        if (!configFile.exists()) {
            try (BufferedWriter writer = new BufferedWriter(new FileWriter(configFile))) {
                writer.write(getDefaultConfig());
                System.out.println("Fichier config.ini créé avec succès.");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    // Charger le contenu de config.ini
    public void loadConfig() throws IOException {
        configMap.clear();
        comments.setLength(0);

        try (BufferedReader reader = new BufferedReader(new FileReader(CONFIG_FILE))) {
            String line;
            while ((line = reader.readLine()) != null) {
                if (line.startsWith("#") || line.trim().isEmpty()) {
                    comments.append(line).append("\n");
                } else if (line.contains(":")) {
                    String[] parts = line.split(":", 2);
                    configMap.put(parts[0].trim(), parts[1].trim());
                }
            }
        }
    }

    // Lire une valeur
    public String readConfig(String key) {
        return configMap.getOrDefault(key, "");
    }

    // Mettre à jour une valeur
    public void updateConfig(String key, String value) {
        configMap.put(key, value);
    }

    // Sauvegarder dans config.ini
    public void saveConfig() throws IOException {
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(CONFIG_FILE))) {
            writer.write(comments.toString());
            for (Map.Entry<String, String> entry : configMap.entrySet()) {
                writer.write(entry.getKey() + " : " + entry.getValue() + "\n");
            }
        }
    }

    // Configuration par défaut
    private String getDefaultConfig() {
        return """
            [General]
            # Hôte mqtt sur le quel se connecter
            General.host : mqtt.iut-blagnac.fr
            # Fréquence à laquelle seront sauvegardées les données en minutes
            General.frequence : 1

            [Capteurs]
            # Permet de s'abonner a toutes les salles comportant des capteurs à l'iut
            # Valeurs possibles {on, off}
            subscribe_all : off
            salles : ''

            [Panneaux Solaires]
            # Permet de s'abonner au topic des panneaux solaires
            # Valeurs possibles {on, off}
            Panneaux Solaires.subscribe_all : off

            [Seuils Alerte]
            # Définit la valeur minimum et maximum pour chaque type de données
            Seuils Alerte.co2Min : 400
            Seuils Alerte.co2Max : 1000
            Seuils Alerte.temperatureMin : 13
            Seuils Alerte.temperatureMax : 27
            """;
    }
}
