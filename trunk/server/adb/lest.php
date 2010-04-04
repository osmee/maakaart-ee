<?php

// Convert from WGS (EPSG:4326) to  L-EST (EPSG:3301) coordinate system
// Jaak.Laineste@gmail.com, 26.03.2007

// Test: Mapinfo gives: e= 26.702332, n=58.366588 -> X= 658108.9; Y= 6472749.8

/*
 $test_east=11.25; // Tartu
 $test_north=56.25;
 list($x,$y)=Wgs2Est($test_east,$test_north);

 echo "east= $test_east <br> north= $test_north <br> x=$x <br> y=$y ";
 
 list($lat,$long)=Est2Wgs($x,$y);

 
 echo "<br>Back: lat=$lat <br> long=$long ";

*/

function Wgs2Est($easting,$northing) {

$D2R= M_PI / 180.0;
$R2D = 180.0 / M_PI;

$_a = 6378137.0000000;		// ellipse semi-major axis
$_e = 0.0818191910428158;	// ellipse eccentricity

$_n = 0.85417585805;		// pre-calculated value
$_F = 1.7988478514;		// pre-calculated value
$_p0 = 4020205.4790000;		// pre-calculated value

$_x0 = 6375000.0000000;		// False Northing
$_y0 = 500000.0000000;		// False Easting
	
    $L = $D2R* $easting; 
    $B = $D2R* $northing;
    
    $Lo = 24 * M_PI / 180;
    
    $t = sqrt( (1 - sin($B)) / (1 + sin($B)) * pow( (1+$_e*sin($B))/(1-$_e*sin($B)), $_e) );
    
    $theta = $_n * ($L - $Lo);
    $p = $_a * $_F * pow($t, $_n);
    
    $x = $p * sin($theta) + $_y0;
    $y = $_p0 - $p * cos($theta) + $_x0;
    
    return array($x, $y); 
}

function Est2Wgs($x,$y) {

$D2R= M_PI / 180.0;
$R2D = 180.0 / M_PI;
	
$_a = 6378137.0000000;	// ellipse semi-major axis 
$_f = 298.257222101;		// ellipse flattening
  
$_e = 0.0818191910428158;	// ellipse eccentricity
$_n = 0.85417585805;		// pre-calculated value
$_F = 1.7988478514;		// pre-calculated value
$_p0 = 4020205.4790000;	// pre-calculated value
  
  //Coordinates of Origin
$_x0 = 6375000.0000000;	// False Northing
$_y0 = 500000.0000000;	// False Easting
  
      $Lo = 24 * M_PI / 180;

      $ux = $x - $_y0;
      $uy = $y - $_x0;
      $sx = $ux;
      $ux = $uy;
      $uy = $sx;
      
      $theta = atan(($uy / ( $_p0 - $ux )));
      $tmpL = $theta / $_n + $Lo;
      
      $p = ($_p0 - $ux);
      $p *= $p;
      $uy *= $uy;
      $p += $uy;
      $p = sqrt( $p );
      
      $t = pow(($p / ( $_a*$_F )), 1/$_n);
      
      $u = (M_PI / 2) - (2 * atan($t));
      
      $tmpB = $u + (pow($_e,2)/2 + 5*pow($_e,2)*pow($_e,2)/24 + pow($_e,6)/12 + 13*pow($_e, 8)/360) * sin(2*$u) +
                         (7*pow($_e,4)/48 + 29*pow($_e, 6)/240 + 811*pow($_e, 8)/11520)*sin(4*$u) +
                         (7*pow($_e, 6)/120 + 81*pow($_e, 8)/1120)*sin(6*$u);
      
      return array( $R2D*$tmpL, $R2D*$tmpB);
	
}

?>