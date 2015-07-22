  1. Peab olema kasutajanimi kaart.maakaart.ee masinas, SSH ligipääs. Jaak teeb kasutaja. Minu tasuta lemmik SSH klient win all on [winscp](http://winscp.net/)
  1. kodukataloogis on  näidisfail osm-google-est.map. Kui ei, kopeeri näiteks /opt/mapserver alt endale.
  1. map faili formaadi referents : http://mapserver.org/mapfile/
  1. modi sedasama faili, või tekita endale teine fail - oma valik. Allpoololev usermap.html toimib küll vaid täpselt sama failinimega, wms-päringus saab teisi kasutada.
  1. andmebaasiühendustes kaks varianti:
    * _Eesti andmed, testimiseks pisut kiirem:_ CONNECTION "dbname=osmee port=5432"
    * _Kogu planeedi andmed:_ CONNECTION "dbname=osm port=5432"
  1. Kontrolli viited teistes kataloogides olevatele asjadele, tõenäoliselt pole vaja muuta:
    * /opt/mapserver/ ... failidele/kataloogidele - fonts, data
    * kui oma fonte, stiile vms vaja kasutada, võib kopida oma kodukausta
  1. WMS URL - siin asenda KASUTAJA oma kasutajanimega ja kontrolli kas .map faili nimi on sama mida tahad näidata: **http://kaart.maakaart.ee/cgi-bin/mapserv?MAP=/home/KASUTAJA/osm-google-est.map&**
  1. testimine - kasuta oma lemmik-WMS klienti ja eeltoodud URL-i. Mina kasutan QGIS-i. Vähemalt QGIS-is on vajalik on valida üks kiht, kasvõi kõige esimene (land0), miskipärast kihigruppi (default) valides kaarti ei renderdata.
  1. kui kaarti ei tule, ja on üldine veateade, vaata mapserveri logi: **/tmp/mserr.log**
  1. eelvaade - http://kaart.maakaart.ee/usermap.html on käsitsi valitud kasutajad kihtidesse lisatud, eeldusel et map faili nime pole muudetud
  1. Palun ära muuda faile teistes kataloogides (nt /osm/mapserver)