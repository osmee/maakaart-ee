<?php

// die(tanav_fix_personname("Tartu"));
// die(tanav_fix(remove_after_slash(remove_postcode("Paldiski mnt. 145 / Ehitajate tee 150"))));
// die(house_noletter("Keraamika tee 65-32ab"));

$debug=1;
include_once("lest.php");
include_once("config.php");

$id=$_GET[id];

$MAX_RESULT_ROWS=10;
 $dbconn = pg_connect("host=$db_host dbname=$db_name user=$db_username password=$db_password")
		or die('Could not connect: ' . pg_last_error());

#die(geocode("Parksepa Keskkool, Võru tee 4, Parksepa, Võru maakond"));

$query = "select id,raddress from test_requests where id in(12,20,23,27,29,37,43,45,46,55) ";
$query = "select id,raddress from test_requests where id=$id";
#$query = "select id,raddress from test_requests";

#$query = "select id,raddress from test_requests where ok=false or ok is null limit 1";

	//print $query;
 $qdb = pg_query($query) or die('Query failed: ' . pg_last_error());		

print "<pre>";

while (($line = pg_fetch_array($qdb, null, PGSQL_ASSOC))){
  $q = $line[raddress];
  print $line[id]." req:".$q ." ";
    $time_start = microtime(true);
  $res = geocode($q);
    $time_end = microtime(true);
    $time = $time_end - $time_start;
$insert = "update test_requests set taisaadress='".$res[address]."', code='".$res[code]."', dur_s=$time, sql='".mysql_escape_string($res[sql])."',lon=".$res[x].",lat=".$res[y]." where id=".$line[id];
#print $insert;
 pg_query($insert) or die('Query failed: ' . pg_last_error());	
	
  print "code:".$res[code]." time (s):".$time." address:".$res[address]." count:".$res[count]." x:".$res[x]." y:".$res[y]."\n";
  ob_flush();
}	

print "</pre>";


// Closing DB connection
pg_close($dbconn);

function geocode($q){ 
global $MAX_RESULT_ROWS,$dbconn;


if (strlen($q)<4)
 return ;
$code=-1;

// remove anything in brackets ()
$q=preg_replace('`\([^\)]*\)`','',$q);

// remove " tn"
$q=str_replace(" tn","",$q);
// cut request to components
$qaa=array();
$qaa = split(",",$q);


// remove "% side" component
$qa=array();
foreach($qaa as $qc){
 if(!strpos($qc," side")){
    $qa[]=trim($qc);
   }
}

$qalen=sizeof($qa);


/*
// viimane element maakond?
$mk=maakond(trim($qa[$qalen-1]));
print "maakond leitud: $mk\n";	

// eelviimane element valitsus?
$ov=ov(trim($qa[$qalen-2]));
print "omavalistus leitud: $ov\n";	

// asula ?
$asula=asula(trim($qa[$qalen-3]),$ov);
print "asulad  leitud: \n";	
print_r($asula);
*/
# forward try
$qq="";
for ($i=0;$i<=sizeof($qa);$i++){
 $qq.=maakond_fix($qa[$i])."% ";
}
#print "forward try $qq\n";
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,1);
 }

 # backward, only first and last has %
$qq="";
$qq.=maakond_fix($qa[sizeof($qa)-1])."% ";
for ($i=sizeof($qa)-2;$i>=0;$i--){
 $qq.=remove_postcode(maakond_fix($qa[$i])).", ";
}
$qq=substr($qq,0,-2)."%";
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,1.5);
 }
 
# add %
$qq=maakond_fix($qa[sizeof($qa)-1]);
for ($i=sizeof($qa)-2;$i>=0;$i--){
 $qq.=remove_postcode($qa[$i])."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,2);
 }

# remove_postcode, tn fix
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=remove_postcode($qa[$i])."% ";
}
 $qq.=tanav_fix(remove_postcode($qa[$i]))."% ";
#print "backward nonumber_leading,add tn try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,3);
 }

 # remove_postcode, tn fix, remove after /
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=remove_postcode($qa[$i])."% ";
}
 $qq.=remove_after_slash(tanav_fix(remove_postcode($qa[$i])))."% ";

$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,3.5);
 }

 # remove_postcode, tn fix personfix, remove after slash
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=tanav_fix_personname(remove_postcode($qa[$i]))."% ";
}
 $qq.=remove_after_slash(tanav_fix_personname(remove_postcode($qa[$i])))."% ";

$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,3.6);
 }
 
# remove_postcode, tn fix, remove after /, nimisasula (repeat middle)
$qq="";
 $qq.=maakond_fix($qa[2])."%";
 $qq.=remove_postcode($qa[1])."%";
 $qq.=remove_postcode($qa[1])."%";
 $qq.=remove_after_slash(tanav_fix(remove_postcode($qa[0])))."% ";

$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,3.75);
 }
 
 # remove_postcode, tn fix, remove after /, nimisasula (repeat middle), remove letters fron house
$qq="";
 $qq.=maakond_fix($qa[2])."%";
 $qq.=remove_postcode($qa[1])."%";
 $qq.=remove_postcode($qa[1])."%";
 $qq.=remove_after_slash(house_noletter(tanav_fix(remove_postcode($qa[0]))))."%";

$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,3.8);
 }
 

# remove_postcode
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=maakond_fix(remove_postcode($qa[$i]))."% ";
}
#print "backward nonumber_leading try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,4);
 }
 
 # remove_postcode, remove text before - (Rootsi-Kallavere küla -> Kallavere küla)
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=remove_before_hypern(maakond_fix(remove_postcode($qa[$i])))."%";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,5.45);
 }
 
 # remove_postcode, remove text after - (muhu-liiva -> muhu)
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=remove_after_hypern(maakond_fix(remove_postcode($qa[$i])))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,5.5);
 }

  # remove_postcode, replace mnt with tn
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=replace_mnt(maakond_fix(remove_postcode($qa[$i])))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,5.55);
 }

 
# remove_postcode, replace space
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=replace_space(maakond_fix(remove_postcode($qa[$i])))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,6);
 }

#
$qq="";
for ($i=0;$i<=sizeof($qa);$i++){
 $qq.=maakond_fix(remove_numbers($qa[$i]))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,7);
 }

#
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=maakond_fix(remove_numbers($qa[$i]))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,8);
 }

 # nonumber, remove " talu"
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 $qq.=maakond_fix(remove_talu(remove_numbers($qa[$i])))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,8.5);
 }


# SLOW queries
 
# start %, remove_postcode, no tn fix, no maakond
$qq="%";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=remove_postcode($qa[$i])."% ";
}
 $qq.=remove_postcode($qa[$i])."% ";
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,4.5);
 }

 
  # remove_postcode, tn fix, no maakond
$qq="%";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=remove_postcode($qa[$i])."% ";
}
 $qq.=tanav_fix(remove_postcode($qa[$i]))."% ";
#print "backward nonumber_leading,add tn, no maakond try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,5);
 }

  # remove_postcode, tn fix, no maakond, house noletter
$qq="%";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=house_noletter(remove_postcode($qa[$i]))."% ";
}
 $qq.=house_noletter(tanav_fix(remove_postcode($qa[$i])))."% ";

 $res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,5.05);
}

 
  # remove_postcode, tn fix, no maakond, remove after slash
$qq="%";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=remove_postcode($qa[$i])."% ";
}
 $qq.=tanav_fix(remove_after_slash(remove_postcode($qa[$i])))."% ";
#print "backward nonumber_leading,add tn, no maakond try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,5.1);
 }

 // GENERAL queries
 
# remove_leading and last element
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=maakond_fix(remove_postcode($qa[$i]))."% ";
}
#print "backward nonumber_leading without last try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,9);
 }

# remove_leading and last element and housenumber
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=remove_numbers(maakond_fix(remove_postcode($qa[$i])))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,9.5);
 }
 
 
# remove_leading and last element, replace space with %
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=replace_space(maakond_fix(remove_postcode($qa[$i])))."% ";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,10);
}

# remove_leading and last element, replace space with %, start with %
$qq="%";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 $qq.=replace_space(maakond_fix(remove_postcode($qa[$i])))."%";
}
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,10.5);
}

# remove_leading_num, second element, space with %, 
$qq="";
for ($i=sizeof($qa)-1;$i>=0;$i--){
 if($i!=sizeof($qa)-2)
  $qq.=replace_space(maakond_fix(remove_postcode($qa[$i])))."% ";
}
#print "backward nonumber_leading without last and second, replace space with % try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,11);
 }

# remove_leading_num, last and second element, space with %, 
$qq="";
for ($i=sizeof($qa)-1;$i>=1;$i--){
 if($i!=sizeof($qa)-2)
  $qq.=replace_space(maakond_fix(remove_postcode($qa[$i])))."% ";
}
#print "backward nonumber_leading without last and second, replace space with % try $qa\n";
#print_r(address_try($qq));
$res=address_try($qq);
if (sizeof($res)>=1){
 return mk_response($res,12);
 }

 // very GENERAL queries

// no response reply
$resp=array();
 $resp[code]=-1;
 $resp[address]="";
 $resp[x]=0;
 $resp[y]=0;
 $resp[count]=0;
return $resp;

} 
function remove_talu($t){
 return str_replace(" talu","",$t);
}

function replace_mnt($t){
 return str_replace("mnt","tn",$t);
}

function remove_after_slash($t){
 list($m,$d)=split("/",$t);
return $m;
}

function remove_after_hypern($t){
 list($m,$d)=split("-",$t);
return $m;
}

function remove_before_hypern($t){
  if(strpos($t,"-")>0)
    list($m,$d)=split("-",$t);
  else
    $d=$t;
return $d;
}

function maakond_fix($m){
 list($m,$d)=split("maa",$m);
// $m=str_replace(" maakond","",$m);
return trim($m);
}

function tanav_fix($m){
 $m=trim($m);
 $lastSpace = strrpos($m," ");
 $t=substr($m,0,$lastSpace);
 $n=substr($m,$lastSpace+1);
 // remove suffix, like "H."
// list($dmy,$t2)=split("\.",$t);
// remove "tee"
 $t=str_replace(" tee","",$t);
 $t=str_replace(" mnt.","",$t);
 return trim($t)." % ".$n;
}

function house_noletter($t){
 $m=trim($t);
 $lastSpace = strrpos($m," ");
 $t=substr($m,0,$lastSpace);
 $n=substr($m,$lastSpace+1);
 $num="";$i=0;
 while(is_numeric($n[$i]) && $i<strlen($n)){
  $num.=$n[$i];
  $i++;
 }
 return trim($t)."%".$num;
}

function tanav_fix_personname($m){
 $lastSpace = strrpos($m," ");
 if(!$lastSpace)
  return $m;
  
 $t=substr($m,0,$lastSpace);
 $n=substr($m,$lastSpace+1);
 // remove suffix, like "H."
 #print "t $t n $n";
 if(strpos($t,'.')>0){
  list($dmy,$t2)=split("\.",$t);
 # print "$dmy,$t2";
  }else{
  $t2=$t;
  }
 return trim($t2)."%".$n;
}

function replace_space($m){
 return str_replace(" ","%",$m);
}

function mk_response($res,$i){
 $ret=array();
 $ret[code]=$i;
 $ret[address]=$res[0][address];
 $ret[x]=$res[0][x];
 $ret[y]=$res[0][y];
 $ret[count]=sizeof($res);
 $ret[sql]=$res[0][sql];
return $ret;
} 
 
function address_try($q){
global $MAX_RESULT_ROWS,$dbconn,$debug;

    $time_start = microtime(true);
  
$q=trim($q);
	$query = "select * from aadressid where taisaadress like '$q' and olek='K' order by length(taisaadress)";
	if ($debug){
	 print "\n-- $query\n";
	 }
	//die();
	$pr = pg_query($query) or die('Query failed: ' . pg_last_error());		
		
		$n_count=0;
		$resp=array();
		
		    while (($ll = pg_fetch_array($pr, null, PGSQL_ASSOC)) && ($n_count<$MAX_RESULT_ROWS)){
			
			 if ($debug){
			  print_r($ll);
			  }

				$resp[$n_count]["address"] = $ll[taisaadress];
				if (!empty($ll[viitepunkt_x])){
				 list($x,$y) = Est2Wgs(str_replace(",",".",$ll[viitepunkt_x]),str_replace(",",".",$ll[viitepunkt_y]));
				 }else{
				 list($x,$y) = Array(0,0);
				 }
				$resp[$n_count]["x"]=$x;
				$resp[$n_count]["y"]=$y;
				$resp[$n_count]["zoom"]=1000;
				$resp[$n_count]["sql"]=$query;
			$n_count++;
		  } // while
		    $time_end = microtime(true);
    $time = $time_end - $time_start;
#	print " time(s):$time \n";
return $resp;
}

// leiab maakonna koodi
function maakond($mk){
$mk=trim($mk);
print "otsin maakonda $mk\n";
 $m_query = "select adkomp_kood from adkomp_synonyymid where otsistring = upper('$mk') and adkomp_tase=1";
 print "otsin maakonda $m_query\n";
 $m_result = pg_query($m_query) or die('Query failed: ' . pg_last_error());		
 if (($m_line = pg_fetch_array($m_result, null, PGSQL_ASSOC))){
return $m_line[adkomp_kood];
}else{
  return null;
 }
}

function ov($ov){
print "otsin omavalitsust $ov\n";
$ov=remove_numbers(trim($ov));

 $m_query = "select adkomp_kood from adkomp_synonyymid where otsistring = upper('$ov') and adkomp_tase=2";
 print "otsin omavalistust $m_query\n";
 $m_result = pg_query($m_query) or die('Query failed: ' . pg_last_error());		
 if (($m_line = pg_fetch_array($m_result, null, PGSQL_ASSOC))){
return $m_line[adkomp_kood];
}else{
  return null;
 }
}

function asula($as){
print "otsin oasulat $as\n";
$as=remove_numbers(trim($as));

 $m_query = "select adkomp_kood from adkomp_synonyymid where otsistring like '".strtoupper($as)."%' and adkomp_tase=3";
 print "otsin omavalistust $m_query\n";
 $m_result = pg_query($m_query) or die('Query failed: ' . pg_last_error());		
 
 $res=array();
 if (($m_line = pg_fetch_array($m_result, null, PGSQL_ASSOC))){
 $res[] = $m_line[adkomp_kood];
}else{
  return null;
 }
 return $res;
}

function all_synonyms($as){
print "otsin sünonüüme $as\n";
$as=remove_numbers(trim($as));

 $m_query = "select adkomp_kood from adkomp_synonyymid where otsistring like '".strtoupper($as)."%'";
 print "otsin sünonüüme $m_query\n";
 $m_result = pg_query($m_query) or die('Query failed: ' . pg_last_error());		
 
 $res=array();
 if (($m_line = pg_fetch_array($m_result, null, PGSQL_ASSOC))){
 $res[] = $m_line[adkomp_kood];
}else{
  return null;
 }
 return $res;
}

// remove all numbers separated by space
function remove_numbers($t){
$vv="";
// eemalda numbrid (näiteks indeks) osa
$ovkomp=split(" ",$t);
foreach($ovkomp as $komp){
 if (!is_numeric($komp))
  $vv.=$komp." ";
}
return trim($vv);
}

// remove leading numbers only 
function remove_postcode($t){
$vv="";
$t=trim($t);
$num="";
for($i=0;$i<=strlen($t);$i++){
 if (is_numeric($t[$i])){
   $num.=$t[$i];
  }else{
    if (strlen($num)>0 && strlen($num)<5){ // add buffer so far
	  $vv.=$num;
	  $num="";
	}
    $vv.=$t[$i];
  }
}
    if (strlen($num)>0 && strlen($num)<5){ // add buffer so far
	  $vv.=$num;
	  $num="";
	}

return trim($vv);
}


?>