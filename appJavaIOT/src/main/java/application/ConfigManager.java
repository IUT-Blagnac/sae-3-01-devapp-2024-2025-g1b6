package application;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Properties;

public class ConfigManager {
    private static final String CONFIG_FILE = "config.ini";

    // Vérifier ou créer config.ini avec des valeurs par défaut
    public static void checkOrCreateConfigFile() {
        File configFile = new File(CONFIG_FILE);
        if (!configFile.exists()) {
            try (FileWriter writer = new FileWriter(configFile)) {
                writer.write("[General]\n");
                writer.write("host: mqtt.iut-blagnac.fr\n");
                writer.write("frequence: 1\n");
                writer.write("[Capteurs]\n");
                writer.write("subscribe_all: off\n");
                writer.write("salles: ''\n");
                writer.write("[Panneaux Solaires]\n");
                writer.write("subscribe_all: off\n");
                writer.write("panneaux: ''\n");
                System.out.println("Fichier config.ini créé avec succès.");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }



    public static void updateConfig(String section, String key, String value) throws IOException {
        Properties props = new Properties();
        props.load(new FileReader(CONFIG_FILE));

        // Ajouter ou mettre à jour la clé
        props.setProperty(section + "." + key, value);

        // Sauvegarder les modifications
        try (FileWriter writer = new FileWriter(CONFIG_FILE)) {
            props.store(writer, null);
        }
    }


        // Lire une valeur de config.ini
        public static String readConfig(String section, String key) throws IOException {
            Properties props = new Properties();
            props.load(new FileReader(CONFIG_FILE));
            return props.getProperty(section + "." + key);
        }
        
    
    
}

