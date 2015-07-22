# pgRouting install

## Sõltuvused

### PostgreSQL
http://www.postgresql.org/ftp/source/

Postgre peaks oleam juba eelnevalt paigaldatud. Vaata Postgre paigaldust siit.

### PostGIS
http://www.postgresql.org/ftp/source/

PostGIS peaks olema juba eelnevalt paigaldatud. Vaata PostGIS -i paigaldust siit.

### Proj
http://download.osgeo.org/proj/

Proj peaks olema eelnevalt juba paigaldatud läbi mapniku sõltuvuse. Vaata mapniku paigaldust siit

### GEOS
http://trac.osgeo.org/geos/wiki/BuildingOnUnixWithCMake

GEOS peaks olema eelnevalt juba paigaldatud läbi mapniku sõltuvuse. Vaata mapniku paigaldust siit

### BGL (Boost)
http://www.boost.org/users/download/

Boost peaks olema eelnevalt juba paigaldatud läbi mapniku sõltuvuse. Vaata mapniku paigaldust

### GAUL
http://gaul.sourceforge.net/downloads.html

  * ./configure --prefix=/usr --enable-slang=no

  * LC\_ALL=C make

  * make install

### CGAL
http://www.cgal.org/download.html

  1. mkdir build; cd build
  1. ccmake ../
    * Soovitav valida CMAKE\_INSTALL\_PREFIX -ks /usr
    * cmake käsk -c
    * cmake käsk -g
  1. make
  1. make install

## pgRouting paigaldus
http://pgrouting.postlbs.org/wiki/pgRoutingDownload

Selleks, et pgRouting kompileeruks tuleb muuta järgmisi faile ja neile lisada üks rida:
  * core/src/dijkstra.c
  * core/src/astar.c
  * core/src/shooting\_star.c
  * extra/driving\_distance/src/alpha.c
  * extra/driving\_distance/src/drivedist.c
> peale _"executor/spi.h"_ tuleb lisada rida **#include "catalog/pg\_type.h"**

Tuleb teha kaks sümboolset linki
  * ln -s /usr/include/boost/property\_map/vector\_property\_map.hpp /usr/include/boost/vector\_property\_map.hpp
  * n -s /usr/include/boost/property\_map/property\_map.hpp /usr/include/boost/property\_map.hpp

  1. ccmake .
> > valida võib kõik extra laiendused (WITH\_DD - Driving Distance, WITH\_TSP - Traveling Salesperson functionality)
  1. make
  1. make install

## Routingu baasi loomine
  1. Loo routingi baas ja lae PostGIS
    * createdb -U postgres -E UNICODE routing
    * createlang -U postgres plpgsql routing
    * psql -U postgres -f /usr/share/postgresql/contrib/postgis-1.5/postgis.sql routing
    * psql -U postgres -f /usr/share/postgresql/contrib/postgis-1.5/spatial\_ref\_sys.sql routing
  1. Lisa pgRouting peamised funktsionaalsused (nõutud)
    * psql -U postgres -f /usr/share/postlbs/routing\_core.sql routing
    * psql -U postgres -f /usr/share/postlbs/routing\_core\_wrappers.sql routing
  1. Kui on soovi võib lisada pgRoutingu lisa funktsionaalsused
    1. TSP - Traveling Salesperson
    * psql -U postgres -f /usr/share/postlbs/routing\_tsp.sql routing
    * psql -U postgres -f /usr/share/postlbs/routing\_tsp\_wrappers.sql routing
    1. DD - Driving Distance laadimine hetkel ei tööta