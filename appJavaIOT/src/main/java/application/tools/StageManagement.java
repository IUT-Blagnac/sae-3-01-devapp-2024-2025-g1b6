package application.tools;

import javafx.stage.Stage;

/**
 * Classe utilitaire pour centrer automatiquement une fenêtre (Stage) sur une autre.
 *
 * <p>Cette classe contient une méthode statique qui permet de centrer une fenêtre principale (primary Stage)
 * par rapport à une fenêtre parente (parent Stage). Le centrage est calculé en fonction de la position et de la taille
 * de la fenêtre parente.
 *
 * @see javafx.stage.Stage
 *
 */
public class StageManagement {

    /**
     * Centre la fenêtre principale (primary Stage) par rapport à la fenêtre parente (parent Stage).
     *
     * <p>Cette méthode calcule la position centrale de la fenêtre parente et ajuste la position de la fenêtre principale
     * pour qu'elle soit centrée par rapport à la fenêtre parente. La fenêtre principale est cachée avant d'être montrée
     * pour éviter qu'elle ne soit repositionnée pendant le processus.
     *
     * @param parent  La fenêtre parente par rapport à laquelle la fenêtre principale sera centrée.
     * @param primary La fenêtre principale à centrer.
     */
    public static void manageCenteringStage(Stage parent, Stage primary) {

        // Calculer la position centrale de la fenêtre parente
        double centerXPosition = parent.getX() + parent.getWidth() / 2d;
        double centerYPosition = parent.getY() + parent.getHeight() / 2d;

        // Cacher la fenêtre principale avant qu'elle soit montrée et repositionnée
        primary.setOnShowing(ev -> primary.hide());

        // Repositionner la fenêtre principale lorsqu'elle est montrée
        primary.setOnShown(ev -> {
            primary.setX(centerXPosition - primary.getWidth() / 2d);
            primary.setY(centerYPosition - primary.getHeight() / 2d);
            primary.show();
        });
    }
}
