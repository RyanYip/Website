#!usr/bin/python3

import requests
import pymysql
from config import *

# API CONSTANTS
# format: <url>/<interface>/<method>/v<version>/?key=<key>&etc...
BASE_URL = "http://api.steampowered.com"
KEY = configkey
STEAMID = configid

db = pymysql.connect(host="localhost",user=dbuser,passwd=dbpasswd,db=dbdb,charset="utf8mb4")
cursor = db.cursor()

# get profile info
interface = "ISteamUser"
method = "GetPlayerSummaries"
version = "v0002"
request = "%s/%s/%s/%s/?key=%s&steamids=%s" % (BASE_URL, interface, method, version, KEY, STEAMID)
response = requests.get(request)

# get owned games
interface = "IPlayerService"
method = "GetOwnedGames"
version = "v0001"
request = "%s/%s/%s/%s/?include_appinfo=1&key=%s&steamid=%s" % (BASE_URL, interface, method, version, KEY, STEAMID)
response = requests.get(request)

if response.status_code == 200:
    json = response.json()
    for game in json["response"]["games"]:
        SQL = 'INSERT INTO steam_games (appid, name, playtime_forever, img_icon_url, img_logo_url) \
        VALUES ({0}, "{1}", {2}, "{3}", "{4}") \
        ON DUPLICATE KEY UPDATE appid={0}, name="{1}", playtime_forever={2}, img_icon_url="{3}", img_logo_url="{4}";'.format(
            game["appid"], game["name"], game["playtime_forever"], game["img_icon_url"], game["img_logo_url"])
        cursor.execute(SQL)
    db.commit()

# get recently played games
interface = "IPlayerService"
method = "GetRecentlyPlayedGames"
version = "v0001"
request = "%s/%s/%s/%s/?key=%s&steamid=%s" % (BASE_URL, interface, method, version, KEY, STEAMID)
response = requests.get(request)

if response.status_code == 200:
    json = response.json()
    SQL = 'UPDATE steam_games SET playtime_2weeks=NULL;'
    cursor.execute(SQL)
    for game in json["response"]["games"]:
        SQL = 'UPDATE steam_games SET playtime_2weeks=%s WHERE name="%s"' % (game["playtime_2weeks"], game["name"])
        cursor.execute(SQL)
    db.commit()

db.close()
