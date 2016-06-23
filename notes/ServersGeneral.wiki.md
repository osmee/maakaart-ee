# Serverid
  1. Live: kaart.maakaart.ee; toimivad ka aliased stiilis (a/b/c/misiganes).kaart.maakaart.ee. 16G RAM, 8 ketast kokku, 8-core. Ubuntu 11.10 (viimane) server
> > kettad (RAID kontrolleri taga):

> a) 2x500GB SATA kettad, RAID-1 (mirror)
> > / - 442GB

> b) 2x148GB SAS ketast RAID-0 (stripe)
> > /storage - kiire IO. Siin imposm planeet baas Mapserveri jaoks (osm), postgres port 5432.

> c) - 4x1TB, riistvara RAID-10, 2TB saadaval
> > /suur  - kaardiandmete, tilede, muude baaside jms jaoks. Siin on osm2pgsql Mapnik baas (gis), veel üks imposm planeet testiks (osm) ja Eesti imposm testbaas (osmee), postgres port 5433.


> võrgukonf: staatiline lan ip 192.168.1.100. Suunatud tulemüürist. IMPI management saadaval.

  1. ~~Testserver: 193.40.61.99 4G RAM. Siin on näiteks ADS baas.~~ ei ole 2012 enam
  1. Juniperi ruuter/tulemüür live-serveri ees. Väline IP: 193.40.61.100

# Tarkvara
  1. liveserveri üldjoonis: https://docs.google.com/drawings/d/1HGTn47iCXx4u17ZD3-FuSAXXMdwp4STH3OpoUr3ycr0/edit
  1. vaata detailse konfi osas siinseid wiki lehti

## Bittorrent OSM Planet-ile
  * BitTorrentInstall
  * Jookseb kasutaja jaakl byobu-i sees käsuga.
```
rtorrent -p 6881-6881
```
  * Juniperis on selle jaoks suunatud 6881 UDP port sisse

## Imposm
### Planet
  * kasutatakse mapserveri poolt WMS-i APIs.
  * kompileeritud sourcest **/opt/imposm** kataloogis lähtudes http://wiki.osgeo.org/wiki/Benchmarking_2011/Imposm juhendist
  * tekitab vahefailidena .cache failid, mis planeedi puhul võtavad kokku umbes 13GB. Nt kodukataloogis ei saa käivitada seetõttu
  * vaikimisi konf impordib place kihis vaid name-tagi, seega eestikeelse jaoks on vaja impordikonfi modida (lisada "name:et") ja importida uuesti ning modida kujundusfaili (.map faili)
  * kerneli konfi parandus:
```
sudo sh -c "echo 'kernel.shmmax=268435456' > /etc/sysctl.d/60-shmmax.conf
sudo service procps start
```
  * loo ja käivita andmebaas:
```
sudo mkdir /storage/imposmdb
sudo chown postgres /storage/imposmdb
sudo su postgres
initdb -D /storage/imposmdb/
nano /storage/imposmdb/postgresql.conf
 port = 5432
 shared_buffers = 128MB # 16384 for 8.1 and earlier
 checkpoint_segments = 20
 maintenance_work_mem = 256MB # 256000 for 8.1 and earlier
 autovacuum = off
 unix_socket_directory = '/tmp'
postgres -D /storage/imposmdb/
pg_ctl start -D /storage/imposmdb/
createdb osm -p 5432 -h localhost
psql -d osm -p 5432  -h localhost -f /usr/share/postgresql/contrib/postgis-1.5/postgis.sql
psql -d osm -p 5432  -h localhost -f /usr/share/postgresql/contrib/postgis-1.5/spatial_ref_sys.sql
```
  * käivitatud järgnev käivitatud kasutaja _postgres_ alt. See võttis aega ~9h lugemine, + ~29h baasi kirjutamine ja optimeerimine. Uuendus 02.nov.2012: imposm took 54h 48m 0 s (täisuuendus uue planeediga)
```
cd /home/jaakl/imposm_data
export LD_LIBRARY_PATH=/opt/imposm/lib
imposm --read --write --optimize --deploy-production-tables -d osm -m ../custommapping.py -p 5432 -c 4 /home/jaakl/planet-111123.osm.bz2
```
  * Täiendatud impordi konfi (/home/jaakl/custommaping.py), et eestikeelsed kohanimed oleksid ja hoonetel majanumbrid, ning andmebaasiühendused:
```
db_conf = Options(
    # db='osm',
    host='localhost',
    port=5432,
    user='postgres',
    password='',
    sslmode='allow',
    prefix='osm_new_',
    proj='epsg:3857',
)

...
set_default_name_type(LocalizedName(['name:et', 'int_name', 'name:en', 'name']))
...
buildings = Polygons(
    name = 'buildings',
    fields = (
        ('addr:housenumber', String()),
    ),
    mapping = {
        'building': (
            '__any__',
    )}
)

```

  * Õigused www-data kasutajale baasist andmeid pärida. NB! grant tuleb anda ka pärast iga baasi full update!
```
sudo -u postgres psql -p 5432 osm
psql (9.0.3, server 9.0.2)
Type "help" for help.
osm=# CREATE ROLE "www-data" LOGIN;
osm=# GRANT SELECT ON ALL TABLES IN SCHEMA public TO "www-data";
GRANT
osm=# \q
```


```
# puhasta mapproxy cache, kasulik pärast uuendust. peab jooksma www-data kasutaja alt, sest cache (apache) on selle salvestanud selle alt
jaakl@kaart:/opt/mapproxy$ sudo -u www-data mapproxy-seed -f mapproxy.yaml --cleanup clean1 -c 4 seed.yaml 

# puhaste maaameti ortod
jaakl@kaart:/opt/mapproxy$ sudo -u www-data mapproxy-seed -f mapproxy.yaml --cleanup clean_maaamet -c 4 seed.yaml

```

### Eesti (kiiremaks testimiseks)
```
sudo su postgres
createdb osmee
psql -d osmee -f /usr/share/postgresql/contrib/postgis-1.5/postgis.sql
psql -d osmee -f /usr/share/postgresql/contrib/postgis-1.5/spatial_ref_sys.sql
exit
cd /home/jaakl
mkdir imposm_dataee
wget http://download.geofabrik.de/osm/europe/estonia.osm.bz2
export LD_LIBRARY_PATH=/opt/imposm/lib
imposm --read --write --optimize --deploy-production-tables -d osmee  -m ../custommapping.py -c 4 estonia.osm.bz2
sudo -u postgres psql osmee
GRANT SELECT ON ALL TABLES IN SCHEMA public TO "www-data";
\q
```
  * imposm import võttis umbes 3 minutit, download umbes 5 minutit.
  * moditud kujundusfail : /opt/mapserver/osm-google-est.map
    1. _Kõikidel kihtidel:_ **CONNECTION "dbname=osmee port=5432"**
    1. Kommenteeri välja: #"ows\_onlineresource"      "http://kaart.maakaart.ee/cgi-bin/mapserv?MAP=/opt/mapserver/osm-google-est.map&"
  * WMS URL: http://kaart.maakaart.ee/cgi-bin/mapserv?MAP=/opt/mapserver/osm-google-est.map&

## Mapserver
  * Installitud **sudo apt-get install cgi-mapserver**
  * Konf **/opt/mapserver** kataloogis, kujundus **/opt/mapserver/osm-google-p5432.map**
  * Binary on **/usr/lib/cgi-bin** kaustas, apache oskab seda sealt leida
  * proj confile google projection lisada faili **/usr/share/proj/epsg**
```
#Google Web map SRS defs
<900913> +proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs
<3857> +proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs
```
  * URL wrapper **/usr/lib/cgi-bin/wms** on ise lisatud, see sisaldab viidet .map failile (kujundusega).
```
#!/bin/sh
MAPSERV="/usr/lib/cgi-bin/mapserv"
MS_MAPFILE="/opt/mapserver/osm-google-p5432.map" exec ${MAPSERV}
```
  * logid: **/var/log/apache2/error.log** ja **/tmp/mserr.log**
  * avalik WMS URL: http://kaart.maakaart.ee/cgi-bin/wms . NB! see on aeglane, avalikult kasutada tuleks Mapproxy URL-i, vt järgmine ptk

## Mapproxy
  * install: **sudo pip install MapProxy**
  * conf: /opt/mapproxy/ kataloogis, vt MapProxyConf. Konfitud lokaalset mapserveri WMS kasutama + katseks Maaameti WMS (ei pruugi toimida täielikult). Tilede cache jaoks kataloog:
```
sudo mkdir /suur/mapproxy_cache
sudo chmod a+w /suur/mapproxy_cache
```
  * Testserveri käivitamine:
```
sudo su -u jaakl
screen -RRD
cd /opt/mapproxy
mapproxy-util serve-develop mapproxy.yaml
```
  * live conf Apache mod\_wsgi abil, vastavalt mapproxy manuaalile deploytud. Apache konfis **/etc/apache2/sites-available/default** määratud teenuse URL on /osm, aga töötab ka /service ja /tiles läbi URL rewrite:
```
        # mapproxy forward short URL-s
        RewriteEngine on
        RewriteRule   ^/service$  /osm/service [PT]
        RewriteRule   ^/orto/(.*)$  /osm/tiles/1.0.0/maaamet_orto_EPSG900913/$1?origin=nw [PT]
        RewriteRule   ^/tiles/(.*)$  /osm/tiles/$1 [PT]

        # tileproxy in wsgi
        WSGIScriptAlias /osm /opt/mapproxy/config.py

        <Directory /opt/mapproxy/>
                Order deny,allow
                Allow from all
        </Directory>
```
  * Live logid: **/var/log/mapproxy** . Erroreid tasub otsida ka Apache error.log failist.
  * konfitud on ka seedfail mis madalamaid zoome pregenereerib cache sisse. Kasutamine:
```
cd /opt/mapproxy
sudo -u www-data mapproxy-seed -f mapproxy.yaml -c 1 seed.yaml
```
  * Avalik OSM WMS URL: http://kaart.maakaart.ee/service
  * Avalik Maaameti ortopiltide tile url kasutamiseks 
```
# Potlatch2 (flash web editor) sees:
http://kaart.maakaart.ee/orto/$z/$x/$y.jpeg

# iD (javascript web editor) sees:
http://kaart.maakaart.ee/tiles/1.0.0/maaamet_orto_EPSG900913/{z}/{x}/{y}.jpeg?origin=nw

```

MapProxy demo site: http://kaart.maakaart.ee/osm/demo/


