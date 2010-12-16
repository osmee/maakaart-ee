<?php


// Eesti Posti allalaetava csv faili tabeli põhjal aadresside otsing
/* Liigid:
"küla";9017
"alev";475
"asum";6
"aiandusühistu";709
"alevik";3334
"linn";11796
*/

function sihtnumber($maakond,$vald,$asula,$tanav,$maja,$dbconn){

$maakond=substr($maakond,0,-3); //strip ...maa
@list($asula,$liik)=@split(" ",$asula); // split "Äksi alevik" -> Äksi;alevik
@list($vald,$vliik)=@split(" ",$vald); // split "Tartu vald" -> Tartu;vald


if($vliik=="linn"){
   $asula=$vald;
 }
// küla maal

$query="select sihtnumber 
	from sihtnumbrid 
	where maakond = '$maakond' 
		and vald = '$vald' 
		and asula = '$asula'
		and aadressiliik in ('küla','alevik','alev','asum','aiandusühistu')
	group by sihtnumber
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	$m = pg_fetch_array($qdb, null, PGSQL_ASSOC)? " *" : "";
if($m){
 print "<br/>multi ";
 print " mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja ".$line["sihtnumber"];
}
	return $line["sihtnumber"] . $m;
 }

// linnas
// ehk on täpne maja vaste olemas ?
$query="select sihtnumber 
	from sihtnumbrid 
	where tanav = '$tanav' 
		and asula = '$asula' 
		and majaalgus=upper('$maja') and majalopp=upper('$maja')
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

// vahemikud linnas
$query="select sihtnumber 
	from sihtnumbrid 
	where tanav = '$tanav' 
		and asula = '$asula' 
		and pc_chartoint('$maja')>=pc_chartoint(majaalgus)
		and pc_chartoint('$maja')<=pc_chartoint(majalopp)
		and 
			mod( pc_chartoint(majaalgus),2)
			= mod(pc_chartoint('$maja'),2);
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error(). ' '." mk=$maakond,asula=$asula,tn=$tanav,maja=$maja ".$query);		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

 
 
 print " mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja";
 
 return -1;

}
?>
