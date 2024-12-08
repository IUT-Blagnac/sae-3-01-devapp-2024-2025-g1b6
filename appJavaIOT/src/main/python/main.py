import json
import os
import sys
import paho.mqtt.client as mqtt
from configparser import ConfigParser
import threading
import time
import uuid

# Création d'un objet ConfigParser pour lire le fichier de configuration
parser = ConfigParser()
# Chemin absolu vers le fichier config.ini
config_path = os.path.join(os.path.dirname(__file__), 'config.ini')

# Vérification de l'existence du fichier de configuration
if not os.path.exists(config_path):
    raise FileNotFoundError(f"The configuration file '{config_path}' was not found.")

# Lecture du fichier de configuration
parser.read(config_path)

# Récupération de l'hôte à partir du fichier de configuration
host = parser.get('General', 'host')

# Tableau contenant les topics MQTT à souscrire
tabTopics = []

# Tableau contenant les dernières valeurs qui seront écrites
tabValues = []

def handle_section(parser, section, key, topic):
    """
    Fonction permettant de gérer les sections du fichier de configuration,
    en fonction des valeurs de l'option 'subscribe' ou 'subscribe_all'.
    Ajoute les topics MQTT à souscrire à la liste 'tabTopics'.
    """
    if section == 'Panneaux Solaires':
        option = 'subscribe'
    else:
        option = 'subscribe_all'

    # Si l'option 'subscribe' ou 'subscribe_all' est activée, souscrire aux topics spécifiques
    if not parser.getboolean(section, option):
        items = parser.get(section, key)
        for item in items:
            tabTopics.append((f'{topic}by-room/{item}/data', 1))
    else:
        tabTopics.append((f'{topic}by-room/+/data', 1))

def afficher_metriques():
    """
    Fonction à compléter pour afficher les métriques (encore à définir).
    """
    # TODO
    pass

def on_connect(client, userdata, flags, reason_code, properties=None):
    """
    Fonction appelée lors de la connexion au serveur MQTT.
    Abonne les topics après une connexion réussie.
    """
    if reason_code == 0:
        if len(sys.argv) == 2 and sys.argv[1] == 'conTest':
            exit(0)
        print("Connected with result code", reason_code)
        client.subscribe(tabTopics)
    else:
        if len(sys.argv) == 2 and sys.argv[1] == 'conTest':
            exit(1)
        print("Failed to connect with result code", reason_code)

def on_message(client, userdata, msg):
    """
    Fonction appelée à chaque réception de message sur un topic MQTT.
    Ajoute les messages reçus à la liste 'tabValues' si ces derniers
    ne sont pas déjà présents, en fonction de leur 'lastUpdateTime'.
    """
    global tabValues

    # Décodage du message reçu
    jsonDict = json.loads(msg.payload)
    if 'lastUpdateTime' in jsonDict:
        existing = next((item for item in tabValues if item.get('lastUpdateTime') == jsonDict['lastUpdateTime']), None)
        if existing is not None:
            # Si un message avec le même 'lastUpdateTime' existe déjà, on ignore ce message
            return
        else:
            tabValues.append(jsonDict)
    else:
        tabValues.append(jsonDict)

def write_data(path, di):
    """
    Fonction permettant d'écrire les données dans un fichier JSON à un emplacement spécifié.
    Crée les répertoires nécessaires si ils n'existent pas encore.
    """
    # Conversion du chemin relatif en chemin absolu
    abs_path = os.path.abspath(path)
    directory = os.path.dirname(abs_path)

    # Créer les répertoires nécessaires si nécessaire
    try:
        os.makedirs(directory, exist_ok=True)
    except Exception as e:
        print(f"Error creating directories {directory}: {e}")
        return

    # Tentative d'écriture des données dans le fichier
    try:
        with open(abs_path, 'r+', encoding='utf-8') as f:
            data = json.load(f)
            data.append(di)
            f.seek(0)
            json.dump(data, f)
            f.truncate()
    except (json.decoder.JSONDecodeError, FileNotFoundError):
        # Si le fichier n'existe pas, on le crée et on y écrit les données
        data = [di]
        with open(abs_path, 'w', encoding='utf-8') as f:
            json.dump(data, f)
    except Exception as e:
        print(f"Error writing data to {abs_path}: {e}")

def save_data():
    """
    Fonction qui enregistre les données de 'tabValues' dans les fichiers correspondants.
    Si les données dépassent les seuils d'alerte, elles sont sauvegardées dans un fichier d'alerte.
    """
    global tabValues
    for di in tabValues:
        tab_data_t = parser.get('Capteurs', 'data_type')
        alertpath = None

        # Pour chaque type de donnée, déterminer le fichier cible
        if len(di) == 2:
            path = 'src/main/resources/data/' + di[1]['Building'] + '/' + di[1]['room'] + '.json'
            alert_di = [{}, di[1]]
            for datatype, value in list(di[0].items()):
                # Vérifier si les données sont dans les types acceptés
                if datatype not in tab_data_t:
                    del di[0][datatype]
                elif not (parser.getfloat('Seuils Alerte', datatype + 'Min') <= value <= parser.getfloat('Seuils Alerte', datatype + 'Max')):
                    alertpath = 'src/main/resources/data/Alert/' + di[1]['room'] + '.json'
                    alert_di[0][datatype] = value
                    del di[0][datatype]
        else:
            path = 'src/main/resources/data/SolarPanel/solar.json'

        # Écriture des données dans le fichier
        if di[0]:
            write_data(path, di)

        # Écriture des données d'alerte si applicable
        if alertpath is not None:
            write_data(alertpath, alert_di)

    # Si des données ont été sauvegardées, on vide la liste 'tabValues'
    if len(tabValues) > 0:
        print('Data save completed.')
    tabValues.clear()

def periodic_save(interval):
    """
    Fonction qui sauvegarde les données à intervalle régulier.
    """
    while True:
        save_data()
        time.sleep(interval)

# Ajout des topics à souscrire pour les capteurs
if not parser.getboolean('Capteurs', 'subscribe_all'):
    items = parser.get('Capteurs', 'salles')
    for item in items:
        tabTopics.append((f'AM107/by-room/{item}/data', 1))
else:
    tabTopics.append((f'AM107/by-room/+/data', 1))

# Ajout du topic pour les panneaux solaires si nécessaire
if parser.getboolean('Panneaux Solaires', 'subscribe'):
    tabTopics.append(('solaredge/blagnac/overview', 1))

# Création d'un identifiant unique pour le client MQTT
client_id = f'client-{uuid.uuid4()}'

# Création d'un client MQTT
client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)
client.connect(host, 1883, 60)
client.on_connect = on_connect
client.on_message = on_message

# Lancement du thread pour la sauvegarde périodique des données
save_thread = threading.Thread(target=periodic_save, args=(parser.getint('General', 'frequence') * 60,))
save_thread.daemon = True
save_thread.start()

# Démarrage de la boucle MQTT si le script n'est pas exécuté en mode 'conTest'
if len(sys.argv) == 2 and not sys.argv[1] == 'conTest':
    print("Starting MQTT client loop")
client.loop_forever()
