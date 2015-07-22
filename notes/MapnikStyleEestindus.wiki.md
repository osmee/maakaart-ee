Modifitseeritud failid:
  * osm\_et.xml , inc/entities\_et.xml.inc, inf/layers\_et.xml.inc - muudetud vaid viidet failile, failinime muutus
  * Peamne muudatus sisult:

**diff layer-placenames\_et.xml.inc layer-placenames.xml.inc**
```
24,25c24
<     <!--
<      <Rule>
---
>     <Rule>
31d29
< 	-->
34c32
<       &maxscale_zoom6;
---
>       &maxscale_zoom5;
151c149
<       (select way,place,COALESCE("name:et", "name") AS name,ref
---
>       (select way,place,name,ref
162c160
<       (select way,place,COALESCE("name:et", "name") AS name,ref
---
>       (select way,place,name,ref
174c172
<       (select way,place,COALESCE("name:et", "name") AS name
---
>       (select way,place,name
188c186
<       (select way,place,COALESCE("name:et", "name") AS name
---
>       (select way,place,name
```