-- -----------------------------------------------------------------------
-- products-- -----------------------------------------------------------------------
drop table products;

CREATE TABLE products(
 
	ProductID INTEGER NOT NULL ,
 
	ProductName VARCHAR (40) default '' NOT NULL ,
 
	SupplierID INTEGER ,
 
	CategoryID INTEGER ,
 
	QuantityPerUnit VARCHAR (20) ,
 
	UnitPrice DOUBLE ,
 
	UnitsInStock INTEGER ,
 
	UnitsOnOrder INTEGER ,
 
	ReorderLevel INTEGER ,
 
	Discontinued INTEGER default 0 NOT NULL ,
 
	Notes MEDIUMTEXT ,
 
	OrderDate DATETIME  
);

-- -----------------------------------------------------------------------
-- blobtest-- -----------------------------------------------------------------------
drop table blobtest;

CREATE TABLE blobtest(

	BlobID INTEGER NOT NULL ,
 
	BlobName VARCHAR (30) NOT NULL ,
 
	BlobData LONGBLOB NOT NULL  
);

-- -----------------------------------------------------------------------
-- clobtest-- -----------------------------------------------------------------------
drop table clobtest;

CREATE TABLE clobtest(
 
	ClobID INTEGER NOT NULL ,
 
	ClobName VARCHAR (30) NOT NULL ,
 
	ClobData LONGTEXT NOT NULL  
);

-- -----------------------------------------------------------------------
-- idgentest-- -----------------------------------------------------------------------
drop table idgentest;

CREATE TABLE idgentest(
 
	ID INTEGER PRIMARY KEY,
 
	Name VARCHAR (40) default '' NOT NULL  
);

  
  
  
  