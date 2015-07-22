# Sissejuhatus

Siin on üldised ideed teenuste arenduseks. Üldine lähenemine oleks, et paneme aluseks üles sama kaardivaate mis on openstreetmap.org all, lisateenused lisame tab-idena ja linkidena vasakule. Kasutame maksimaalselt OSM ümber tehtud vabavaralisi lahendusi, täiendame neid endale vajalikus osas ja selle koodi jagame algprojektiga. Originaalarendused hoiame ja jagame siin Google Code projekti repos.


# Teenused

|**Teenus**|**Baastarkvara**|**staatus**|
|:---------|:---------------|:----------|
|[Kiire aadressotsing](AadressOtsing.md) : OSM + ADS (aadressibaas)|Nominatim?      |Prototüüp olemas, arendada. Võiks täiendada globaalset teenust Eesti spetsiifikaga, siis oma eraldi teenust pole ehk vaja.|
|Bulk geocoding eelneva põhjal|custom          |arendada   |
|Struktueeritud aadressotring: AJAX : ADS (aadressibaas)|custom          |alus baas olemas, arendada 3 JSON päringut: all\_cities, streets\_in\_city, houses\_in\_street. Võibolla lisada Eesti Posti sihtnumbrid nende tabeli põhjal|
|Standard tile server, mitme eri stiiliga (default mapnik, eestindatud, maakaart jms), võimalikult värsked andmed|Tirex           |test toimib: http://193.40.61.100/map.html, konfida, lisada täiendavad kujundused. Tagada andmete automaatne uuendus|
|OSMXAPI üles panna|OSMXAPI         |installida |
|OSM veast/puudusest lihtne teavitamine|openstreetbugs  |kirjeldada,arendada|
|Lihtne kaardieksport Garmini jaoks|olemas mitmeid  |installida,konfida|
|Teemakaartide valik|OpenLayers+web custom|arendada   |
|Lihtne teekonnaotsing (A->B)|[OSRM](http://sourceforge.net/apps/trac/routed/), Graphserver, pgrouting, http://opentripplanner.org/ ?|pgrouting balti andmetega olemas, lisa installida,konfida. Pandud üles http://maakaart.ee/opentransit/|
|Interaktiivsed (klikitavad) POI punktid kaardil|OpenLayers,web custom|arendada   |
|Eri kaartide võrdleja|vt Geofabrik vahendit|installida,konfida|
|Punkti kohta info (Point-in-Polygon)|postgis baasile oma vahend|Olemas [prototüüp API](http://193.40.61.100/~jaakl/pip/?lon=24.8415156163091&lat=59.4334641149756), ootab täiendavaid nõuteid |
Ideid veel:
  * GIS-väljund - export shapefiles

Vt. ka http://maakaart.uservoice.com/forums/76179-general
