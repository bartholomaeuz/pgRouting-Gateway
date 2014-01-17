SELECT p.seq, p.id1 AS node, p.id2 AS edge, p.cost,st_asgeojson(w.the_geom) FROM pgr_dijkstra('
                SELECT gid AS id,
                         source::integer,
                         target::integer,
                         length::double precision AS cost
                        FROM ways',
                2632, 102, false, false) p,ways w where p.id2 = w.gid;