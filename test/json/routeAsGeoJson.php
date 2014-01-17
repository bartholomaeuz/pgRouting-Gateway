<?php
// Verbindungsaufbau und Auswahl der Datenbank
$dbconn = pg_connect("host=localhost dbname=pgr user=pgr password=pgr")
    or die('Verbindungsaufbau fehlgeschlagen: ' . pg_last_error());

// Eine SQL-Abfrge ausführen
$query = "select st_asgeojson(w.the_geom) FROM pgr_dijkstra('
                SELECT gid AS id,
                         source::integer,
                         target::integer,
                         length::double precision AS cost
                        FROM ways',
                1897, 99, false, false) p,ways w where p.id2 = w.gid";
$result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

// JSON

header('Content-type: application/json',true);
echo '{ "type": "FeatureCollection", "features": [';
$first_entry=TRUE;
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    
    foreach ($line as $col_value) {
		if($first_entry){
			echo "$col_value";
			$first_entry=FALSE;
		}
        echo ",$col_value";
    }
   
}
echo '],"crs": {
  "type": "name",
  "properties": {
    "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
    }
  },"bbox":[15.31,47.44,15.34,47.47]}';





// Speicher freigeben
pg_free_result($result);

// Verbindung schließen
pg_close($dbconn);
?>