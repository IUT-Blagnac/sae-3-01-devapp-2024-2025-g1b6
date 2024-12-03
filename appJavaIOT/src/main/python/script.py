import sys
import random

def test_mqtt_connection():
    # Simule une tentative de connexion MQTT
    connected = random.choice([True, False])  # Simule un succès ou un échec aléatoire
    if connected:
        print("Connexion MQTT réussie.")
        return 0  # Code de retour pour succès
    else:
        print("Échec de la connexion MQTT.")
        return 1  # Code de retour pour échec

if __name__ == "__main__":
    # Exécute le test et retourne le code de retour
    exit_code = test_mqtt_connection()
    sys.exit(exit_code)
