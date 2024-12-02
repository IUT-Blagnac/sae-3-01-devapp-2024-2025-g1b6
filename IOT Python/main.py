import json
import os
import sys

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
    Fonction pour afficher les métriques .
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
    if reason_code == 0:
        if len(sys.argv) and sys.argv[1] == 'conTest':
            exit(0)
        print(f"Connected with result code {reason_code}")
        # Souscrit aux topics spécifiés dans tabTopics
        client.subscribe(tabTopics)
    else:
        if len(sys.argv) and sys.argv[1] == 'conTest':
            exit(1)
        print(f"Connected with result code {reason_code}")


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
    # Ajoute le message désérialisé au tableau des valeurs
    tabValues.append(jsonDict)


def write_data(path, di):
    """
    Écrit les données dans un fichier JSON.

    Args:
        path (str): Le chemin du fichier JSON.
        di (dict): Les données à écrire.
    """
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
            f.seek(0)
            json.dump(data, f)
            f.truncate()
    except (json.decoder.JSONDecodeError, FileNotFoundError):
        # Si le fichier est vide, corrompu ou n'existe pas, crée une nouvelle liste de données
        data = [di]
        with open(path, 'w') as f:
            json.dump(data, f)


def save_data():
    """
    Sauvegarde périodiquement les données de tabValues dans des fichiers JSON.
    """
    global tabValues

    # Parcourt chaque dictionnaire de données dans tabValues
    for di in tabValues:
        # Récupère le type de données depuis le fichier de configuration
        tab_data_t = parser.get('Capteurs', 'data_type')
        alertpath = None
        # Détermine le chemin de sauvegarde en fonction du type de dispositif
        if len(di) == 2:
            path = './data/' + di[1]['Building'] + '/' + di[1]['room'] + '.json'
            alert_di = [{}, di[1]]
            # Utilisation d'un itérateur pour parcourir et modifier les valeurs
            for datatype, value in list(di[0].items()):
                if datatype not in tab_data_t:
                    # Supprime le type de donnée si non présent dans tab_data_t
                    del di[0][datatype]
                elif not (parser.getfloat('Seuils Alerte', datatype + 'Min') <= value <= parser.getfloat('Seuils Alerte', datatype + 'Max')):
                    # Ajoute aux alertes si les seuils sont dépassés
                    alertpath = './data/' + di[1]['Building'] + '/Alert/' + di[1]['room'] + '.json'
                    alert_di[0][datatype] = value
                    del di[0][datatype]
        else:
            path = './data/SolarPanel/solar.json'

        # Écrit les données filtrées par les seuils d'alerte dans le fichier JSON approprié
        write_data(path, di)

        # Si des seuils d'alerte sont dépassés, écrit les données d'alerte dans un fichier JSON
        if alertpath is not None:
            write_data(alertpath, alert_di)

    # Vide le tableau des valeurs après sauvegarde
    tabValues.clear()
    print('Sauvegarde des données effectuée.')


def periodic_save(interval):
    """
    Fonction qui appelle save_data() à des intervalles réguliers.

    Args:
        interval (int): L'intervalle en secondes entre chaque appel de save_data().
    """
    while True:
        # Attend l'intervalle spécifié avant de commencer
        time.sleep(interval)
        # Appelle la fonction save_data pour sauvegarder les données
        save_data()


# Vérifie si l'abonnement à tous les topics est nécessaire
if not parser.getboolean('Capteurs', 'subscribe_all'):
    # Récupère les items de la section
    items = parser.get('Capteurs', 'salles')
    for item in items:
        # Ajoute chaque item au tableau des topics
        tabTopics.append((f'AM107/by-room/{item}/data', 1))
else:
    # Ajoute un topic générique pour souscrire à tous les sous-topics
    tabTopics.append((f'AM107/by-room/+/data', 1))

if parser.getboolean('Panneaux Solaires', 'subscribe'):
    # Ajout du topic des panneaux solaires
    tabTopics.append(('solaredge/blagnac/overview',1))

# Crée une instance client MQTT avec la version 2 de l'API de callback
client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)
# Se connecte au broker MQTT avec l'hôte et le port définis
client.connect(host, 1883, 60)

# Associe les callbacks de connexion et de message au client MQTT
client.on_connect = on_connect
client.on_message = on_message

# Crée et démarre le thread pour l'enregistrement périodique des données
save_thread = threading.Thread(target=periodic_save, args=(parser.getint('General', 'frequence')*60,))
save_thread.daemon = True
save_thread.start()

# Lance la boucle réseau pour traiter les événements MQTT
client.loop_forever()
