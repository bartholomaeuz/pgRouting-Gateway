#! /usr/bin/perl -w

use DBI;

$dhb = DBI->connect("dbi:Pg:dbname=pgr;host=localhost;port=5432","pgr","pgr");


$sth = $dhb->prepare("select st_asgeojson(w.the_geom) FROM pgr_dijkstra('
                SELECT gid AS id,
                         source::integer,
                         target::integer,
                         length::double precision AS cost
                        FROM ways',
                1897, 99, false, false) p,ways w where p.id2 = w.gid;");

$sth->execute();
$first_entry = 1;

print '{ "type": "FeatureCollection", "features": [';

$sth->bind_columns(\$json);
while($sth->fetch()) {
		if($first_entry){
			print "$json \n";
			$first_entry = 0;
		}else{
			print ",$json \n";
		}
}

print '],"crs": {
  "type": "name",
  "properties": {
    "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
    }
  },"bbox":[15.31,47.44,15.34,47.47]}';

