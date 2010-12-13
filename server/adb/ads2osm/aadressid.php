<?php
$debug=1;
include_once("lest.php");
include_once("config.php");

$MAX_RESULT_ROWS=10;
 $dbconn = pg_connect("host=$db_host dbname=$db_name user=$db_username password=$db_password")
		or die('Could not connect: ' . pg_last_error());

// /?bbox=left,bottom,right,top		

if (isset($_GET['bbox'])) {
  $bbox=$_GET["bbox"];
}else{
//  $bbox="26.67807,58.35005,26.68592,58.35435";
  $bbox="0,0,80,80";
 }

if (isset($_GET['mk'])) {
  $mk="tase1_id = '".$_GET["mk"]."' and ";
}else{
  $mk="1=1 and ";
 }

 
list($left,$bottom,$right,$top)=split(",",$bbox);

list($x1,$y1)=Wgs2Est($left,$bottom);
list($x2,$y2)=Wgs2Est($right,$top);

$query = "select *,x(viitepunkt) as x,y(viitepunkt) as y from aadressid where $mk viitepunkt && SetSRID('BOX3D($x1 $y1,$x2 $y2)'::box3d,3301)";

print "$query <br/>";
die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());		

print "<?xml version='1.0' encoding='UTF-8'?>";
print '<osm version="0.6" generator="Maakaart.ee server">';
$nid=-1;
while (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){

#  $ta = format($line["taisaadress"]);
#  $la = format($line["lahiaadress"]);
  $x = $line["x"];
  $y = $line["y"];
  list($lon,$lat)=Est2Wgs($x,$y);
  $tags = "";
  foreach(array_keys($line) as $key){
    if($line[$key] && $key!="geom"&& $key!="x"&& $key!="y"&& $key!="viitepunkt_x"&& $key!="viitepunkt_y"){
	  $tags.="<tag k='$key' v='".format($line[$key])."'/>";
	}
  }
  
  print "<node id='$nid' lat='$lat' lon='$lon'>$tags</node>\n";
 $nid--;
}
print "</osm>";

function format($t){
 return htmlspecialchars($t,ENT_QUOTES);
}

?>
