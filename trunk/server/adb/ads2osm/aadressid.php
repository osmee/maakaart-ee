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

#$query = "select *,x(viitepunkt) as x,y(viitepunkt) as y from aadressid where $mk viitepunkt && SetSRID('BOX3D($x1 $y1,$x2 $y2)'::box3d,3301)";
$query="SELECT 
  a.*,
  x(a.viitepunkt) as x,
  y(a.viitepunkt) as y,
  k1.nimetus as tase1, 
  k2.nimetus as tase2, 
  k3.nimetus as tase3, 
  k4.nimetus as tase4, 
  k5.nimetus as tase5, 
  k6.nimetus as tase6, 
  k7.nimetus as tase7, 
  k8.nimetus as tase8
FROM 
  (select * from public.aadressid where olek = 'K' AND $mk viitepunkt && SetSRID('BOX3D($x1 $y1,$x2 $y2)'::box3d,3301)) as a 
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=1) as k1 
	ON (a.tase1_id=k1.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=2) as k2 
	ON (a.tase2_id=k2.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=3) as k3 
	ON (a.tase3_id=k3.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=4) as k4 
	ON (a.tase4_id=k4.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=5) as k5 
	ON (a.tase5_id=k5.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=6) as k6 
	ON (a.tase6_id=k6.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=7) as k7 
	ON (a.tase7_id=k7.komp_id)
  LEFT OUTER JOIN (select * from public.aadress_komponendid where tase=8) as k8
	ON (a.tase8_id=k8.komp_id)
	";
#print "$query <br/>";
#die();

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
    if($line[$key] && $key!="viitepunkt"&& $key!="x"&& $key!="y"&& $key!="viitepunkt_x"&& $key!="viitepunkt_y"){
	  $tags.="<tag k='ADS_$key' v='".format($line[$key])."'/>";
	}
  }
	  if($line["tase8"])
			$numbrilisa="-".format($line["tase8"]);
		else
			$numbrilisa="";
  
	  if($line["tase7"])
		$tags.="<tag k='addr:housenumber' v='".format($line["tase7"]).$numbrilisa."'/>";
	  if($line["tase6"])
		$tags.="<tag k='addr:housename' v='".format($line["tase6"])."'/>";
	  if($line["tase5"])
		$tags.="<tag k='addr:street' v='".format_tn($line["tase5"])."'/>";
	  if($line["tase4"]) // aiandusühistud jms kahtlased kohad
		$tags.="<tag k='addr:street' v='".format_tn($line["tase4"])."'/>";
  	  if($line["tase3"])
		$tags.="<tag k='addr:district' v='".format($line["tase3"])."'/>";
 	  if($line["tase2"])
		$tags.="<tag k='addr:city' v='".format_linn($line["tase2"])."'/>";
	  if($line["tase1"]) // maakond
		$tags.="<tag k='addr:province' v='".format_mk($line["tase1"])."'/>";
		
	  $tags.="<tag k='addr:country' v='EE'/>";
  
  
  print "<node id='$nid' lat='$lat' lon='$lon'>$tags</node>\n";
 $nid--;
}
print "</osm>";


// ************
// spetsiifilised formaadimuutused
function format_tn($t){
  return format(str_replace(" tn","",$t));
}


function format_mk($t){
  return format(str_replace(" maakond","maa",$t));
}

function format_linn($t){
  return format(str_replace(" linn","",$t));
}

function format($t){
 return htmlspecialchars($t,ENT_QUOTES);
}

?>
