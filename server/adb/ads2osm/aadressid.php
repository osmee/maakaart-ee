<?php
// võtab Aadressregistri (ADS) aadressid Postgresql 
// baasist, väljastab OSM XML formaadis faili maakonna või bbox piirkonna kohta
// lisab ka Eesti Posti sihtnumbrid 

// 2010 Jaak Laineste jaak.laineste-at-gmail.com
// Kasutamine GPL litsentsitingimuste kohaselt

$debug=1;
include_once("lest.php");
include_once("config.php");
include_once("sihtnumbrid.php");
include_once("aprint.php");

$MAX_RESULT_ROWS=10;
 $dbconn = pg_connect("host=$db_host dbname=$db_name user=$db_username password=$db_password")
		or die('Could not connect: ' . pg_last_error());

// input formats: /aadressid.php?bbox=left,bottom,right,top - bounding box
// või /aadressid.php?mk=78  -- maakond

#die(sihtnumber("Tartumaa","Tartu","Akadeemia","1B",$dbconn));
#die(sihtnumber(format_mk("Harju maakond"),format_linn2("Saue linn"),format(""),format_tn("Rauna põik"),format("13"),$dbconn));
#die(sihtnumber(format_mk("Harju maakond"),format_linn2("Tallinna linn"),format("Kesklinna linnaosa"),format_tn("Narva mnt"),format("28"),$dbconn));

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
  (select * from public.aadressid where olek = 'K' AND (tase6_id>0 or tase7_id>0) AND $mk viitepunkt && SetSRID('BOX3D($x1 $y1,$x2 $y2)'::box3d,3301)) as a 
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
  $multi=0; $nok=0;$ok=0;$total=0;

while (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){


if(!housenameok($line["tase6"])){
 continue;
}

// tegelikult peaks juba päringust kõik välja jääma millel pole majanumbrit või majanime
// tänav on, aga majanumbrit mitte
 if ($line["tase5"] && !$line["tase7"]){
  continue;
 }
// ainult vald/linn, pole asulat ega tänavat
 if ($line["tase2"] && !($line["tase3"] || $line["tase5"])){
  continue;
 }
// ainult maakond
 if ($line["tase1"] && !$line["tase2"]){
  continue;
 }

// ainult küla
 if ($line["tase3"] && !($line["tase5"] || $line["tase6"] || $line["tase7"]) ){
  continue;
 }
 
 
// puudulikud aadressid, tänavat pole antud, on tase6 all tänavanimi. Majanumbrita tänavakrundid jms sodi
 if ($line["tase2"] && !$line["tase5"] && substr($line["tase3"],-8)=="linnaosa"){
 // print "<br/>SKIP:".aprint($line);
  continue;
 }

 

#  $ta = format($line["taisaadress"]);
#  $la = format($line["lahiaadress"]);
  $x = $line["x"];
  $y = $line["y"];
  list($lon,$lat)=Est2Wgs($x,$y);
  $tags = "";
  foreach(array_keys($line) as $key){
    if($line[$key] && $key!="viitepunkt" && $key!="x" && $key!="y" && $key!="viitepunkt_x"&& $key!="viitepunkt_y"){
	#  $tags.="<tag k='ADS_$key' v='".format($line[$key])."'/>";
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
		$tags.="<tag k='addr:street' v='".format_tn2($line["tase5"])."'/>";
	  if($line["tase4"]) // aiandusühistud jms kahtlased kohad
		$tags.="<tag k='addr:street' v='".format_tn2($line["tase4"])."'/>";
  	  if($line["tase3"])
		$tags.="<tag k='addr:district' v='".format($line["tase3"])."'/>";
 	  if($line["tase2"])
		$tags.="<tag k='addr:city' v='".format_linn($line["tase2"])."'/>";
	  if($line["tase1"]) // maakond
		$tags.="<tag k='addr:province' v='".format_mk($line["tase1"])."'/>";

  	  $sihtnumber = sihtnumber(format_mk($line["tase1"]),format_linn2($line["tase2"]),format($line["tase3"]),format_tn($line["tase5"]),format($line["tase7"]),$dbconn);

	$total++;
		if(substr($sihtnumber,-1,1)=="?"){
			$multi++;
			$tags.="<tag k='addr:postcode' v='$sihtnumber'/>";
			}else if($sihtnumber>0){
		#	print "+";
			$tags.="<tag k='addr:postcode' v='$sihtnumber'/>";
			$ok++;
		}else{
	//		print "<br/>sihtnumber(format_mk(".$line["tase1"]."),format_linn2(".$line["tase2"]."),format(".$line["tase3"]."),format_tn(".$line["tase5"]."),format(".$line["tase7"]."),dbconn)";
			$nok++;
		}
	  $tags.="<tag k='addr:country' v='EE'/>";
	  $tags.="<tag k='source' v='ADS 12.2010, post.ee 12.2010'/>";
#  if($multi>100)
#   exit;
  print "<node id='$nid' lat='$lat' lon='$lon'>$tags</node>\n";
 $nid--;
}
print "</osm>";

#print "<br/>total=$total ; ok=$ok ; multi=$multi ; nok=$nok";

// ************
// spetsiifilised formaadimuutused
// tänavanimi eesnime lühendamisega
function format_tn($t){
  $t=format(str_replace(" tn","",$t));
  // lühenda isikunimega tänavad "Jaan Koorti" -> "J. Koorti"; Karl August Hermanni -> "K. A. Hermanni"
  $lastname="";
  if(str_word_count($t)>1){
	$words=split(" ",$t);
    $newname=array();
	for($i=sizeof($words)-1;$i>=0;$i--){ // tagant-ette kõik sõnad
	 # print "sub1:".(substr($words[$i],0,1));
	  if ((substr($words[$i],0,1)==strtoupper(substr($words[$i],0,1))) && !$lastname){ // kui on suurtäht ja enne pole olnud perenime, siis ilmselt on perenimi
 	    $lastname=$words[$i];
		$newname[$i]=$lastname;
	#	print "last:".$lastname;
	  }else{
	   if(substr($words[$i],0,1)==strtoupper(substr($words[$i],0,1)) && $lastname){ 
	    // on leitud perenimi, ja ikka algab suurega, siis järelikult eesnimi mida lühenda armutult
		 if($words[$i]=="Friedrich"){ // erand(id) laulutaadile
			$newname[$i]="Fr.";
		   }else{
			$newname[$i]=strtoupper(substr($words[$i],0,1)).".";
			}
	   }else{
	    // mistahes muu sõna nimes
	    $newname[$i]=$words[$i];
	   }
	  }
#	  print_r($newname);
	} // for
  ksort($newname);
  return implode($newname," ");
  } // if multiwords
  return $t;
}

// tänavanimi ilma eesnime lühendamiseta
function format_tn2($t){
  $t=format(str_replace(" tn","",$t));
  return $t;
}


function format_mk($t){
  return format(str_replace(" maakond","maa",$t));
}

function format_linn($t){
  $t = format(str_replace(" linn","",$t));
  $t = str_replace("Tallinna","Tallinn",$t);
  return $t;
}

function format_linn2($t){ // indeksi jaoks
  $t = str_replace("Tallinna","Tallinn",$t);
  return $t;
}


function format($t){
 return htmlspecialchars($t,ENT_QUOTES);
}

function housenameok($n){

$noends=array(
	"järv",
	"laht",
	"põld",
	"kraav",
	"karjäär",
	"punkt",
	"sõlm",
	"rand",
	"tulepaagi",
	"tulepaak",
	"tuletorn",
    "karjäär",
    "raba",
	"km",
	"külavahe",
	"põik",
    "jaam"
	);

$nowords=array(
 "Parkla",
 "maantee",
 "mnt",
 "raudtee",
 "kõnnitee",
 "ristmik",
 "alajaam",
 "pumbajaam",
 "puurkaev",
 "tugijaam",
 "trafo",
 "Maaüksus",
 "Töökoja",
 " plats",
 "OÜ",
 "AS",
 "kergliiklustee",
 "tankla",
 "bensiinijaam",
 "kauplus",
 "haigla",
 "veemõõdupost",
 "garaa",
 "Garaa",
 "puhasti",
 "metskond",
 "metskonna",
 "maatükk",
 "parkla",
 "tänav",
 "üldmaa",
 " tee",
 " tn",
 "tee ",
 "päevamärk",
 "radarmast",
 "üldmaa",
 "tootmisala",
 "T-",
 	"T1",
	"T2",
	"T3",
	"T4",
	"T5",
	"T6",
	"T7",
	"T8",
	"T9",
 );
 
foreach($noends as $end){
 if(substr($n,0-strlen($end))==$end)
   return false;
 } 

foreach($nowords as $word){
  if(strpos($n,$word)!==false)
   return false;
 }
 
 return true;
}

?>
