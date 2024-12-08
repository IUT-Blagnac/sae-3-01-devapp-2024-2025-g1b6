package application.config;

import java.io.*;
import java.util.LinkedHashMap;
import java.util.Map;

public class ConfigManager {

    private static final String CONFIG_FILE = "src/main/python/config.ini";
    private final Map<String, String> configMap = new LinkedHashMap<>();
    private final StringBuilder comments = new StringBuilder();

    public ConfigManager() {
        checkOrCreateConfigFile();
    }

    // Vérifie ou crée le fichier config.ini
    public void checkOrCreateConfigFile() {
        File configFile = new File(CONFIG_FILE);
        File parentDir = configFile.getParentFile();

        if (!parentDir.exists()) {
            if (parentDir.mkdirs()) {
                System.out.println("Répertoire " + parentDir.getAbsolutePath() + " créé avec succès.");
            } else {
                System.err.println("Impossible de créer le répertoire " + parentDir.getAbsolutePath());
                return;
            }
        }

        if (!configFile.exists()) {
            try (BufferedWriter writer = new BufferedWriter(new FileWriter(configFile))) {
                writer.write(getDefaultConfig());
                System.out.println("Fichier config.ini créé avec les valeurs par défaut.");
            } catch (IOException e) {
                e.printStackTrace();
            }
        } else {
            try {
                loadConfig();
                System.out.println("Fichier config.ini chargé avec succès.");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    // Charger le contenu de config.ini
    public void loadConfig() throws IOException {
        configMap.clear();
        comments.setLength(0);

        String currentSection = "";

        try (BufferedReader reader = new BufferedReader(new FileReader(CONFIG_FILE))) {
            String line;
            while ((line = reader.readLine()) != null) {
                line = line.trim();

                if (line.startsWith("#") || line.isEmpty()) {
                    comments.append(line).append("\n");
                } else if (line.startsWith("[")) {
                    currentSection = line.substring(1, line.length() - 1).trim();
                } else if (line.contains(":")) {
                    String[] parts = line.split(":", 2);
                    String key = parts[0].trim();
                    String value = parts[1].trim();
                    if (!currentSection.isEmpty()) {
                        key = currentSection + "." + key;
                    }
                    configMap.put(key, value);
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
            if (configMap.isEmpty()) {
                System.out.println("Le fichier config.ini est vide. Utilisation des valeurs par défaut.");
                writer.write(getDefaultConfig());
            } else {
                writer.write(getFormattedConfig());
            }
        }
    }

    // Générer le fichier config formaté
    private String getFormattedConfig() {
        StringBuilder formattedConfig = new StringBuilder();

        // [General]
        formattedConfig.append("[General]\n");
        formattedConfig.append("# Hôte mqtt sur le quel se connecter\n");
        formattedConfig.append("host : ").append(configMap.getOrDefault("General.host", "mqtt.iut-blagnac.fr")).append("\n");
        formattedConfig.append("# Fréquence à laquelle seront sauvegardées les données en minutes\n");
        formattedConfig.append("frequence : ").append(configMap.getOrDefault("General.frequence", "1")).append("\n\n");

        // [Capteurs]
        formattedConfig.append("[Capteurs]\n");
        formattedConfig.append("# Permet de s'abonner a toutes les salles comportant des capteurs à l'iut\n");
        formattedConfig.append("# Valeurs possibles {on, off}\n");
        formattedConfig.append("subscribe_all : ").append(configMap.getOrDefault("Capteurs.subscribe_all", "on")).append("\n");
        formattedConfig.append("# Valeurs Possibles :\n");
        formattedConfig.append("salles : ").append(configMap.getOrDefault("Capteurs.salles", "''")).append("\n");
        formattedConfig.append("# Type de données du Capteur\n");
        formattedConfig.append("data_type : ").append(configMap.getOrDefault("Capteurs.data_type", "'co2', 'temperature', 'humidity'")).append("\n\n");

        // [Panneaux Solaires]
        formattedConfig.append("[Panneaux Solaires]\n");
        formattedConfig.append("# Permet de s'abonner au topic des panneaux solaires\n");
        formattedConfig.append("# Valeurs possibles {on, off}\n");
        formattedConfig.append("subscribe : ").append(configMap.getOrDefault("Panneaux Solaires.subscribe", "off")).append("\n\n");

        // [Seuils Alerte]
        formattedConfig.append("[Seuils Alerte]\n");
        formattedConfig.append("# Définit la valeur minimum et maximum pour chaque type de données\n");
        appendSeuils(formattedConfig, "co2");
        appendSeuils(formattedConfig, "temperature");
        appendSeuils(formattedConfig, "humidity");
        appendSeuils(formattedConfig, "activity");
        appendSeuils(formattedConfig, "tvoc");
        appendSeuils(formattedConfig, "illumination");
        appendSeuils(formattedConfig, "infrared");
        appendSeuils(formattedConfig, "infrared_and_visible");
        appendSeuils(formattedConfig, "pressure");

        return formattedConfig.toString();
    }

    private void appendSeuils(StringBuilder formattedConfig, String type) {
        formattedConfig.append("# ").append(type).append(" (valeur numérique positive)\n");
        formattedConfig.append(type).append("Min : ").append(configMap.getOrDefault("Seuils Alerte." + type + "Min", "0")).append("\n");
        formattedConfig.append(type).append("Max : ").append(configMap.getOrDefault("Seuils Alerte." + type + "Max", "0")).append("\n");
    }

    // Configuration par défaut
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
            subscribe_all : on
            salles : ''
            # Type de données du Capteur
            data_type : 'co2', 'temperature', 'humidity', 'activity'

            [Panneaux Solaires]
            # Permet de s'abonner au topic des panneaux solaires
            # Valeurs possibles {on, off}
            subscribe : on

            [Seuils Alerte]
            # Définit la valeur minimum et maximum pour chaque type de données
            co2Min : 400
            co2Max : 1000
            temperatureMin : 0
            temperatureMax : 80
            humidityMin : 40
            humidityMax : 60
            activityMin : 0
            activityMax : 100
            tvocMin : 0
            tvocMax : 500
            illuminationMin : 0
            illuminationMax : 10000
            infraredMin : 0
            infraredMax : 1000
            infrared_and_visibleMin : 0
            infrared_and_visibleMax : 2000
            pressureMin : 900
            pressureMax : 1100
            """;
    }
}
