#!usr/bin/python3

import pymysql
import requests
import urllib.request
from config import *

# format: <url>/<appid>/<hash>.jpg
read_db = pymysql.connect(host="localhost",user=dbuser,passwd=dbpasswd,db=dbdb,charset="utf8mb4",cursorclass=pymysql.cursors.DictCursor)
read_cursor = read_db.cursor()

write_db = pymysql.connect(host="localhost",user=dbuser,passwd=dbpasswd,db=dbdb,charset="utf8mb4")
write_cursor = write_db.cursor()

SQL = 'SELECT appid,img_icon_url FROM steam_games;'
read_cursor.execute(SQL)
all_results = read_cursor.fetchall()
for result in all_results:
    appid = result["appid"]
    img_icon_url = result["img_icon_url"]
    img_url = "http://media.steampowered.com/steamcommunity/public/images/apps/%d/%s.jpg" % (appid, img_icon_url)
    img_icon_path = "/var/www/html/resources/thumbnails/%s.jpg" % (appid)
    response = requests.get(img_url)
    if (response.status_code == 200):
        urllib.request.urlretrieve(img_url, img_icon_path)
        SQL = 'UPDATE steam_games SET img_icon_path="%s" WHERE appid=%d' % (img_icon_path, appid)
        write_cursor.execute(SQL)


write_db.commit()
write_db.close()
read_db.close()
