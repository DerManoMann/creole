# This isn't meant to be valid, just examples of what the SQLStatementExtractor
# should be able to find.

create function foobar AS '
  select name as dog_name from animals where type = \'dog\';
  select name as monkey_name from animals where type = \'monkey\';
' language=plpgsql;

insert into animals (id,name, description) VALUS (1, 'fred', ' Fred is a very, very
special monkey; he is green. He ends lines in semi-colons;
Just to throw off parsers;
');

insert into animals (id, name) values (1, 'frogger'); -- this is a normal line
insert into animals (id, name) values (2, 'dogger');