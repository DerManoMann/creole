
-----------------------------------------------------------------------------
-- products-----------------------------------------------------------------------------
DROP TABLE products;
 
 

CREATE TABLE products 
(
	 
	ProductID INTEGER NOT NULL ,
 
	ProductName VARCHAR(40) default '' NOT NULL ,
 
	SupplierID INTEGER ,
 
	CategoryID INTEGER ,
 
	QuantityPerUnit VARCHAR(20) ,
 
	UnitPrice DOUBLE PRECISION ,
 
	UnitsInStock INTEGER ,
 
	UnitsOnOrder INTEGER ,
 
	ReorderLevel INTEGER ,
 
	Discontinued BOOLEAN default 'f' NOT NULL ,
 
	Notes TEXT ,
 
	OrderDate DATE ,
PRIMARY KEY (ProductID) 
);


-----------------------------------------------------------------------------
-- blobtest-----------------------------------------------------------------------------
DROP TABLE blobtest;
 
 

CREATE TABLE blobtest 
(
	 
	BlobID INTEGER NOT NULL ,
 
	BlobName VARCHAR(30) NOT NULL ,
 
	BlobData BYTEA NOT NULL ,
PRIMARY KEY (BlobID) 
);


-----------------------------------------------------------------------------
-- clobtest-----------------------------------------------------------------------------
DROP TABLE clobtest;
 
 

CREATE TABLE clobtest 
(
	 
	ClobID INTEGER NOT NULL ,
 
	ClobName VARCHAR(30) NOT NULL ,
 
	ClobData TEXT NOT NULL ,
PRIMARY KEY (ClobID) 
);


-----------------------------------------------------------------------------
-- idgentest-----------------------------------------------------------------------------
DROP TABLE idgentest;
 
DROP SEQUENCE idgentest_SEQ;
 
 
CREATE SEQUENCE idgentest_SEQ;
 

CREATE TABLE idgentest 
(
	 
	ID INTEGER NOT NULL ,
 
	Name VARCHAR(40) default '' NOT NULL ,
PRIMARY KEY (ID) 
);


DROP TABLE temporaltest;
 
DROP SEQUENCE temporaltest_SEQ;
CREATE SEQUENCE temporaltest_SEQ;

CREATE TABLE temporaltest(
	ID INTEGER NOT NULL, 
	timecol TIME,
	datecol DATE,
	timestampcol TIMESTAMP,
	PRIMARY KEY (ID) 
);

  
  
----------------------------------------------------------------------
-- products 
----------------------------------------------------------------------

 

----------------------------------------------------------------------
-- blobtest 
----------------------------------------------------------------------

 

----------------------------------------------------------------------
-- clobtest 
----------------------------------------------------------------------

 

----------------------------------------------------------------------
-- idgentest 
----------------------------------------------------------------------

 
