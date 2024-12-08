package application.tasks;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

/**
 * Classe responsable de l'exécution d'un script Python dans un processus séparé.
 * <p>
 * Cette classe permet d'exécuter un script Python à partir de l'application Java. Elle gère la création du processus
 * d'exécution du script, la lecture de sa sortie standard, ainsi que la possibilité d'arrêter le processus si nécessaire.
 * Elle supporte actuellement l'exécution sur des systèmes Windows.
 *
 * @author Yassir Boulouiha Gnaoui
 */
public class PythonScriptRunner implements Runnable {

    /** Le chemin vers le script Python à exécuter. */
    private final String pythonScriptPath;

    /** Le chemin vers l'exécutable Python à utiliser. */
    private final String pythonExecutable;

    /** Le processus en cours d'exécution du script Python. */
    private Process process;

    /**
     * Constructeur de la classe PythonScriptRunner.
     * <p>
     * Ce constructeur initialise les variables nécessaires à l'exécution du script Python, en fonction du système d'exploitation.
     * Si l'OS est Windows, il configure l'exécutable Python, sinon il lève une exception non supportée pour les autres OS.
     *
     * @param pythonScriptPath Le chemin vers le script Python à exécuter.
     * @throws UnsupportedOperationException Si le système d'exploitation n'est pas pris en charge.
     */
    public PythonScriptRunner(String pythonScriptPath) {

        if (System.getProperty("os.name").toLowerCase().contains("win")) {
            // Chemin vers Python sur Windows
            this.pythonExecutable = "src/main/resources/python/windows/python.exe";
        } else if (System.getProperty("os.name").toLowerCase().contains("mac")) {
            //TODO à l'avenir si on a le temps
            throw new UnsupportedOperationException("OS non supporté");
        } else {
            throw new UnsupportedOperationException("OS non supporté");
        }
        this.pythonScriptPath = pythonScriptPath;
        this.process = null;
    }

    /**
     * Exécute le script Python dans un processus séparé.
     * <p>
     * Cette méthode construit la commande à exécuter, lance le processus, puis crée un thread séparé pour lire
     * la sortie standard du script Python et l'afficher dans la console.
     */
    @Override
    public void run() {
        String[] command = {pythonExecutable, "-u", pythonScriptPath};
        ProcessBuilder pb = new ProcessBuilder(command);
        try {
            pb.redirectErrorStream(true);  // Rediriger les erreurs vers la sortie standard
            process = pb.start();  // Démarrer le processus d'exécution du script Python

            // Créer un thread pour lire la sortie du script Python
            Thread outputThread = new Thread(() -> {
                try (BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()))) {
                    String line;
                    while ((line = reader.readLine()) != null) {
                        System.out.println(line);  // Afficher la sortie standard
                    }
                } catch (Exception e) {
                    e.printStackTrace();  // Gérer les erreurs lors de la lecture de la sortie
                }
            });

            // Démarrer le thread
            outputThread.start();

        } catch (IOException e) {
            throw new RuntimeException("Erreur lors de l'exécution du script Python", e);
        }
    }

    /**
     * Arrête le processus Python en cours d'exécution.
     * <p>
     * Cette méthode permet d'arrêter le processus Python en envoyant une commande pour le détruire si le processus est actif.
     */
    public void stop() {
        if (process != null) {
            process.destroy();  // Arrêter le processus
        }
    }
}
