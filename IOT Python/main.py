import json
import re

import paho.mqtt.client as mqtt
from configparser import ConfigParser
import threading
import time

# Création d'un objet ConfigParser pour lire le fichier de configuration
parser = ConfigParser()
parser.read('config.ini')

# Récupération de l'hôte à partir du fichier de configuration
host = parser.get('General', 'host')

# Tableau contenant les topics MQTT à souscrire
tabTopics = []

# Tableau contenant les dernières valeurs qui seront écrites
tabValues = []


def handle_section(parser, section, key, topic):
    """
    Gère une section du fichier de configuration pour ajouter des topics MQTT à tabTopics.

    Args:
        parser (ConfigParser): Le parser de fichier de configuration.
        section (str): La section du fichier de configuration.
        key (str): La clé dans la section pour obtenir les items.
        topic (str): Le préfixe du topic MQTT.
    """
    # Vérifie si l'abonnement à tous les topics est nécessaire
    if not parser.getboolean(section, 'subscribe_all'):
        # Récupère les items de la section
        items = parser.get(section, key)
        for item in items:
            # Ajoute chaque item au tableau des topics
            tabTopics.append((f'{topic}by-room/{item}/data', 1))
    else:
        # Ajoute un topic générique pour souscrire à tous les sous-topics
        tabTopics.append((f'{topic}by-room/+/data', 1))


def afficher_metriques():
    """
    Fonction pour afficher les métriques (à implémenter).
    """
    #TODO
    pass


def on_connect(client, userdata, flags, reason_code, properties=None):
    """
    Callback appelée lors de la connexion au broker MQTT.

    Args:
        client (mqtt.Client): L'instance client MQTT.
        userdata: Les données utilisateur (non utilisé).
        flags: Les drapeaux de connexion (non utilisé).
        reason_code (int): Le code de résultat de la connexion.
        properties: Les propriétés de connexion (non utilisé).
    """
    print(f"Connected with result code {reason_code}")
    # Souscrit aux topics spécifiés dans tabTopics
    client.subscribe(tabTopics)


def on_message(client, userdata, msg):
    """
    Callback appelée lors de la réception d'un message sur un topic souscrit.

    Args:
        client (mqtt.Client): L'instance client MQTT.
        userdata: Les données utilisateur (non utilisé).
        msg (mqtt.MQTTMessage): Le message MQTT reçu.
    """
    global tabValues

    print(msg.topic + " " + str(msg.payload))
    # Désérialisation du message reçu
    jsonDict = json.loads(msg.payload)
    tabValues.append(jsonDict)


def save_data():
    global tabValues

    for di in tabValues:
        if re.match(r"^triphaso[0-9]+", di[1]['deviceName']):
            path = './data/SolarPanels/' + di[1]['Room'] + '.json'
        else:
            path = './data/' + di[1]['Building'] + '/' + di[1]['room'] + '.json'

        # Extrait le chemin du dossier à partir du chemin complet
        directory = os.path.dirname(path)

        # Crée le dossier s'il n'existe pas
        if not os.path.exists(directory):
            os.makedirs(directory)

        try:
            # Ouvre le fichier JSON correspondant en mode lecture-écriture
            with open(path, 'r+') as f:
                # Charge les données existantes
                data = json.load(f)
                # Ajoute le nouveau message aux données
                data.append(di)
                # Sauvegarde les données mises à jour
                json.dump(data, f)
        except (json.decoder.JSONDecodeError, FileNotFoundError):
            # Si le fichier est vide, corrompu ou n'existe pas, crée une nouvelle liste de données
            data = [di]
            with open(path, 'w') as f:
                json.dump(data, f)

    print('Sauvegarde des données effectuée.')



def periodic_save(interval):
    """
    Fonction qui appelle save_data() à des intervalles réguliers.

    Args:
        interval (int): L'intervalle en secondes entre chaque appel de save_data().
    """
    while True:
        save_data()
        time.sleep(interval)


# Gère les sections "Capteurs" et "Panneaux Solaires" du fichier de configuration
handle_section(parser, 'Capteurs', 'salles', 'AM107/')
handle_section(parser, 'Panneaux Solaires', 'panneaux', 'Triphaso/')

# Crée une instance client MQTT avec la version 2 de l'API de callback
client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)
# Se connecte au broker MQTT avec l'hôte et le port définis
client.connect(host, 1883, 60)

# Associe-les callbacks de connexion et de message au client MQTT
client.on_connect = on_connect
client.on_message = on_message

# Crée et démarre le thread pour l'enregistrement périodique des données
save_thread = threading.Thread(target=periodic_save, args=(parser.getint('General', 'frequence')*60,))
save_thread.daemon = True
save_thread.start()

# Lance la boucle réseau pour traiter les événements MQTT
client.loop_forever()
