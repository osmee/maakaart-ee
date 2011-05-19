<?php

// Eesti Posti allalaetava csv faili tabeli põhjal aadresside otsing
// 2010 Jaak Laineste jaak.laineste-at-gmail.com
// Kasutamine GPL litsentsitingimuste kohaselt

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

if($vliik=="linn" || $liik=="linnaosa"){
   $asula=$vald;
 }
 
// kui majanr stiilis "70i/1", eemalda / tagune
@list($maja,$spam)=split("/",$maja);

 // linnas
// ehk on täpne maja vaste olemas ?
$query="select sihtnumber 
	from sihtnumbrid 
	where 
		maakond = '$maakond'
		and tanav = '$tanav' 
		and asula = '$asula' 
		and majaalgus=upper('$maja') and majalopp=upper('$maja')
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

// vahemikud linnas, paarsuse kontrolliga
$query="select sihtnumber 
	from sihtnumbrid 
	where maakond = '$maakond'
		and tanav = '$tanav' 
		and asula = '$asula' 
		and pc_chartoint('$maja')>=pc_chartoint(majaalgus)
		and pc_chartoint('$maja')<=pc_chartoint(majalopp)
		and 
			mod(pc_chartoint(majaalgus),2)
			= mod(pc_chartoint('$maja'),2)
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error(). ' '." mk=$maakond,asula=$asula,tn=$tanav,maja=$maja ".$query);		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

 // linnas kogu tänav sama sihtnumbriga (nii paaris kui paaritu
$query="select sihtnumber 
	from sihtnumbrid 
	where 
		maakond ='$maakond'
		and asula = '$asula' 
		and tanav = '$tanav' 
		and majaalgus='0'
		and majalopp='9999'
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error(). ' '." mk=$maakond,asula=$asula,tn=$tanav,maja=$maja ".$query);		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

 // linnas isiku nimega tänavanimi Eesti Posti baasis valesti (Nt "Amandus Adamsoni tn" asemel "Adamsoni" lihtsalt)
 // samad mis 2 eelmist, aga nuditud tänavanimega
// vahemikud linnas
$tanav_nudi = nudi_tanav($tanav);
$query="select sihtnumber 
	from sihtnumbrid 
	where maakond = '$maakond'
		and tanav = '$tanav_nudi' 
		and asula = '$asula' 
		and pc_chartoint('$maja')>=pc_chartoint(majaalgus)
		and pc_chartoint('$maja')<=pc_chartoint(majalopp)
		and 
			mod(pc_chartoint(majaalgus),2)
			= mod(pc_chartoint('$maja'),2);
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error(). ' '." mk=$maakond,asula=$asula,tn=$tanav,maja=$maja ".$query);		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

 // linnas kogu tänav sama sihtnumbriga (nii paaris kui paaritu
$query="select sihtnumber 
	from sihtnumbrid 
	where 
		maakond ='$maakond'
		and asula = '$asula' 
		and tanav = '$tanav_nudi' 
		and majaalgus='0'
		and majalopp='9999'
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error(). ' '." mk=$maakond,asula=$asula,tn=$tanav,maja=$maja ".$query);		

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	return $line["sihtnumber"];
 }

// küla maal, tänavanime kontrolliga
$query="select sihtnumber 
	from sihtnumbrid 
	where maakond = '$maakond' 
		and vald = '$vald' 
		and asula = '$asula'
		and aadressiliik in ('küla')
		and (
			tanav = '$tanav' or (tanav='Kõik' and '$tanav'='')
			)
		and tanav not like '%postipunkt%'
		and tanav not like '%postkontor%'
	group by sihtnumber
	";
	//run2: ,'alevik','alev','asum','aiandusühistu' 
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	$m = pg_fetch_array($qdb, null, PGSQL_ASSOC)? " ?" : "";
if($m){
 print "<br/>multi1 $query<br/>";
 print "<br/>1 mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja ".$line["sihtnumber"];
 #print "*";
}
	return $line["sihtnumber"] . $m;
 }
 
 // küla maal, tänavanime kontrollita
$query="select sihtnumber 
	from sihtnumbrid 
	where maakond = '$maakond' 
		and vald = '$vald' 
		and asula = '$asula'
		and aadressiliik ='$liik'
		and aadressiliik in ('küla','alevik','alev','asum','aiandusühistu')
		and tanav not like '%postipunkt%'
		and tanav not like '%postkontor%'
	group by sihtnumber
	";

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	$m = pg_fetch_array($qdb, null, PGSQL_ASSOC)? " ?" : "";
if($m){
	// mitu vastet, äkki on keegi neist "tänav='Kõik'"?
		$query="select sihtnumber 
			from sihtnumbrid 
			where maakond = '$maakond' 
				and vald = '$vald' 
				and asula = '$asula'
				and aadressiliik ='$liik'
				and aadressiliik in ('küla','alevik','alev','asum','aiandusühistu')
				and tanav = 'Kõik'
			 group by sihtnumber
			";

		$qdb = pg_query($query) or die('Query failed: ' . pg_last_error());

		if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
			if(pg_fetch_array($qdb, null, PGSQL_ASSOC)){
				print "<br/>multi4 $query<br/>";
				print "<br/>4 mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja ".$line["sihtnumber"];
			}
			return $line["sihtnumber"]." ?";

		}

 print "<br/>multi3 $query<br/>";
 print "<br/>3 mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja ".$line["sihtnumber"];
 #print "*";
 
 } // kui mitu vastet (m)
	return $line["sihtnumber"] . $m;
 }//if külas leitud
 
 
 // alevikud/alevid millel on vaid üks sihtnumber 

$query="select sihtnumber 
	from sihtnumbrid 
	where maakond = '$maakond' 
		and vald = '$vald' 
		and asula = '$asula'
		and aadressiliik in ('küla','alevik','alev','asum','aiandusühistu')
		and tanav not like '%postipunkt%'
		and tanav not like '%postkontor%'
	group by sihtnumber
	";
#print "$query <br/>";
#die();

 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());

if (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
	$m = pg_fetch_array($qdb, null, PGSQL_ASSOC)? " ?" : "";
if($m){
 print "<br/>multi2 $query<br/>";
 print "<br/>2 mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja ".$line["sihtnumber"];
 #print "*";
}
	return $line["sihtnumber"] . $m;
 }

 
 
// print "<br/>NF: mk=$maakond,vald=$vald,vliik=$vliik,asula=$asula,liik=$liik,tn=$tanav,maja=$maja";
 
 return -1;

}

function nudi_tanav($t){
 // A. Adamsoni -> Adamsoni
 $lastname="";
 if(str_word_count($t)>1){
	$words=split(" ",$t);
    $newname=array();
	for($i=sizeof($words)-1;$i>=0;$i--){ // tagant-ette kõik sõnad
	  if ((substr($words[$i],0,1)==strtoupper(substr($words[$i],0,1))) && !$lastname){ // kui on suurtäht ja enne pole olnud perenime, siis ilmselt on perenimi
 	    $lastname=$words[$i];
		$newname[$i]=$lastname;
	  }else{
	   if(substr($words[$i],0,1)==strtoupper(substr($words[$i],0,1)) && $lastname){ 
	    // on leitud perenimi, ja ikka algab suurega, siis järelikult eesnimi mis JÄTA ÄRA
		// do nothing
		}else{
	    // mistahes muu sõna nimes
	    $newname[$i]=$words[$i];
	   }
	  }
#	  print_r($newname);
	} // for
  ksort($newname);
  return trim(implode($newname," "));
  } // if multiwords
 
 return $t;

}
?>
