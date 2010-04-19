<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title>Download POI data file </title>
</head>
<body>
<span style="font-weight: bold;">Download OSM POI data</span><br>
<br>
<form method="GET" action="download.php">
Output:
  <select name="output">
                        <option value="kml">Google Earth (kml)</option>
                        <option value="ov2">TomTom overlay (ov2)</option>
                        <option value="csv">Garmin (csv)</option>
                        <option value="gpx">GPS Exchange format (gpx)</option>
			<option value="wpt">OziExplorer (wpt)</option>
			<option value="geojson">GeoJSON (geojson)</option>
                        <!--<option value="osm">OpenStreetMap (osm)</option>-->
  </select>
  <br>
Key: <input value="amenity" name="k"><br>
Value: <input value="fuel" name="v"><br>
Tile:<br/>
X: <input value="2352" name="x"><br>
Y: <input value="1225" name="y"><br>
Zoom: <input value="12" name="zoom"><br>
max returns: <input value="20" name="max"><br>

  <br><br>
  <br>
  <input value="Download" name="filename" type="submit"><br>
  <br>
<b>Samples:</b><br/>
  amenity:fuel  =  Fuel<br/>
  amenity:atm  =  ATM<br/>
  amenity:speed_camera  =  Speed camera<br/>
  highway:bus_stop  =  Bus stop<br/>
  amenity:parking  =  Parking<br/>
  amenity:bicycle_parking  =  Bicycle parking<br/>
  amenity:place_of_worship  =  Place of worship<br/>
  amenity:hospital  =  Hospital<br/>
  shop:supermarket  =  Supermarket<br/>
  amenity:theatre  =  Theatre<br/>
  amenity:police  =  Police<br/>
  amenity:fire_station  =  Fire station<br/>
  amenity:post_box  =  Post box<br/>
  amenity:post_office  =  Post office<br/>
   amenity:recycling  =  Recycling<br/>
   amenity:restaurant  =  Restaurant<br/>
   amenity:fast_food  =  Fast food<br/>
   amenity:toilets  =  Toilets<br/>
   amenity:pub  =  Pub<br/>
   amenity:waste_basket  =  Waste basket<br/>
   barrier:cattle_grid  =  Cattle grid<br/>
   tourism:camp_site  =  Camp site<br/>
   tourism:hotel  =  Hotel<br/>
   tourism:museum  =  Museum<br/>
   tourism:zoo  =  Zoo<br/>
   historic:castle  =  Castle<br/>
  man_made:windmill  =  Windmill<br/>
  man_made:lighthouse  =  Lighthouse<br/>
  man_made:watermill  =  Watermill<br/>
  man_made:water_tower  =  Water tower<br/>
  amenity:nightclub  =  Nightclub<br/>
  amenity:stripclub  =  Stripclub<br/>
                    </select>

  <br>
</form>
</body>
</html>
