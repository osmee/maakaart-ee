<?php
// point in polygon in OSM DB
require ("config.php"); 

  $lat= pg_escape_string($_GET["lat"]);
  $lon= pg_escape_string($_GET["lon"]);

  if(!$lat|| !$lon){
   $lat=59.43367;
   $lon=24.737108;

    echo "OSM Point-in-Polygon service finds all OSM polygons where given point is. Using test data: Tallinn (lat=$lat lon=$lon). Append to URL ?lat=<lat>&lon=<lon>, in wgs84 to get make real query<br/>";
  }

  // Connect to the postgresql database server
$dbconn = pg_connect("host=" . $config['Database']['servername'] . 
                     " port=" . $config['Database']['port'] . 
                     " dbname=" . $config['Database']['dbname'] . 
                     " user=".$config['Database']['username'] . 
                     " password=" . $config['Database']['password']) 
          or die('Could not connect: ' . pg_last_error());
pg_set_client_encoding("UTF-8");

$sql="SELECT *, x(centroid(transform(way, 4326))) AS lon, y(centroid(transform(way, 4326))) AS lat FROM planet_osm_polygon WHERE st_contains(way,transform(SetSRID(st_point($lon,$lat),4326),900913));";
  
#print ($sql."<br/>");
// Run query
$result = pg_query($sql) or die('Query failed: ' . pg_last_error());

print "<table><tr><td>osm_id</td><td>name</td><td>admin_level</td><td>name:et</td><td>name:en</td><td>lon</td><td>lat</td><td>all tags</td></tr>";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
 print "<tr><td>";
 print "<a href=\"http://www.openstreetmap.org/browse/way/".$line[osm_id]."\">".$line[osm_id]."</a> </td><td>";
 print $line[name]." </td><td>";
 print $line[admin_level]." </td><td>";
 print $line["name:et"]." </td><td>";
 print $line["name:en"]." </td><td>";
 print $line[lon]." </td><td>";
 print $line[lat]." </td><td>";
 
 foreach($line as $key=>$val){
   if($val && $key!="way" && $key != "lon" && $key != "lat" && $key != "name" && $key != "name:en" && $key != "name:et" && $key != "osm_id"){
     print $key."=".$val.",";
   }
 }
 
 print " </td></tr>";
}
print "</table>";

?>
