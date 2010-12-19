<?php
function aprint($a){
 if(!is_array($a))
  return;
  
 ob_start();
 print_r($a);
 $o=ob_get_contents();
 ob_end_flush();
 
 return $o;

}

?>