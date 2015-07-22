#Teenus mis arvutab punktist X tundi raadiuse ringi

# Tarkvara ettevalmistus
# installi pgrouting, postgis 1.5.2 peale. 2.0.x versiooni jaoks oleks vaja lisategevusi
# installi osm2po (java teenus)
# loo pgrouting baas, sellesse installi postgis ja pgroutingu sql funktsioonid


# Andmete ettevalmistus

Kasutame osm2po konverterit. See sisaldab nii ruutinguteenust APIga kui konverterit pgrouting-u SQL-iks.

```

# teeb konvertimise ja käivitab ka teenuse. Euroopa konvertimine võttis umbes 7 tundi:
java -Xmx6g -jar osm2po-core-4.7.7-signed.jar tileSize=x,c prefix=eur europe-latest.osm.bz2
# impordi andmed pgroutingu baasi. Võtab samuti tunde.
sudo -u postgres psql -U postgres pgrouting -f eur_2po_4pgr.sql
# tee tabelist view et driving_distance wrapperile sobiv struktuur oleks
sudo -u postgres psql -U postgres pgrouting
create view eur as select *,id as gid, geom_way as the_geom from eur_2po_4pgr;
#loo geoveerud ja indeks:
SELECT populate_geometry_columns('eur'::regclass);
# järgnev võtab mõned minutid:
create index eurgindx on eur_2po_4pgr using gist (geom_way);
# õigused:
grant select on all tables in schema public to geoserver;
# testpäring:
select st_asgeojson(setSRID(the_geom,4326)) as geo from driving_distance('eur',24.74786, 59.43475, 0.2, 'reverse_cost', 'cost', false, false);
```

# Kasutamine

Näidispäring pgroutinguga: http://kaart.maakaart.ee/dd/?output=geojson&lat=58.3755229&lon=26.7216627&dist=0.1

OSM2PO testveebi liides: http://kaart.maakaart.ee/Osm2poService API päringuid on kõige lihtsam vaadata javascript debuggeri network logist