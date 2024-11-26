package application;

import java.io.FileOutputStream;
import java.io.IOException;
import java.util.Properties;

public class ConfigManager {
    private static final String CONFIG_FILE = "config.ini";

    public static void updateConfig(String section, String key, String value) throws IOException {
        Properties props = new Properties();
        props.load(new java.io.FileReader(CONFIG_FILE));

        // Ajouter ou mettre à jour la clé
        props.setProperty(section + "." + key, value);

        // Sauvegarder les modifications
        try (FileOutputStream out = new FileOutputStream(CONFIG_FILE)) {
            props.store(out, "Updated " + key + " in section " + section);
        }
    }

    public static String readConfig(String section, String key) throws IOException {
        Properties props = new Properties();
        props.load(new java.io.FileReader(CONFIG_FILE));
        return props.getProperty(section + "." + key);
    }
    
}

