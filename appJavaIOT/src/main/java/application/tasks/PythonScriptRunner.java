package application.tasks;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

public class PythonScriptRunner implements Runnable {

    private final String pythonScriptPath;
    private String pythonExecutable;

    private Process process;

    public PythonScriptRunner(String pythonScriptPath) {
        this.pythonScriptPath = pythonScriptPath;
        this.process = null;
        checkAndSetupPythonEnvironment();
    }

    private void checkAndSetupPythonEnvironment() {
        try {
            // Vérifier si Python est installé
            if (!isCommandAvailable("python --version") && !isCommandAvailable("python3 --version")) {
                throw new RuntimeException("Python n'est pas installé.");
            }

            // Déterminer l'exécutable Python
            if (isCommandAvailable("python --version")) {
                pythonExecutable = "python";
            } else {
                pythonExecutable = "python3";
            }

            // Vérifier si pip est installé
            if (!isCommandAvailable(pythonExecutable + " -m pip --version")) {
                // Installer pip si non disponible
                installPip();
            }

            // Vérifier si paho-mqtt est installé
            if (!isPackageInstalled("paho-mqtt")) {
                installPythonPackage("paho-mqtt");
            }

        } catch (IOException | InterruptedException e) {
            throw new RuntimeException("Erreur lors de la configuration de l'environnement Python", e);
        }
    }

    private boolean isCommandAvailable(String command) throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder("cmd.exe", "/c", command);
        Process process = processBuilder.start();
        process.waitFor();
        return process.exitValue() == 0;
    }

    private void installPip() throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder(pythonExecutable, "-m", "ensurepip");
        Process process = processBuilder.start();
        process.waitFor();
        if (process.exitValue() != 0) {
            throw new RuntimeException("L'installation de pip a échoué.");
        }
    }

    private boolean isPackageInstalled(String packageName) throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder(pythonExecutable, "-m", "pip", "show", packageName);
        Process process = processBuilder.start();
        process.waitFor();
        return process.exitValue() == 0;
    }

    private void installPythonPackage(String packageName) throws IOException, InterruptedException {
        ProcessBuilder processBuilder = new ProcessBuilder(pythonExecutable, "-m", "pip", "install", packageName);
        Process process = processBuilder.start();
        process.waitFor();
        if (process.exitValue() != 0) {
            throw new RuntimeException("L'installation du package " + packageName + " a échoué.");
        }
    }

    @Override
    public void run() {
        String[] command = {pythonExecutable, "-u", pythonScriptPath};
        ProcessBuilder pb = new ProcessBuilder(command);
        try {
            pb.redirectErrorStream(true);
            process = pb.start();

            Thread outputThread = new Thread(() -> {
                try (BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()))) {
                    String line;
                    while ((line = reader.readLine()) != null) {
                        System.out.println(line);
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }
            });

            outputThread.start();

        } catch (IOException e) {
            throw new RuntimeException("Erreur lors de l'exécution du script Python", e);
        }
    }

    public void stop() {
        if (process != null) {
            process.destroy();
        }
    }
}
