# -----------------------------------------------------------------------
# products 
# -----------------------------------------------------------------------
drop table if exists products;

CREATE TABLE products(
 
	ProductID INTEGER NOT NULL ,
 
	ProductName VARCHAR(40) default '' NOT NULL ,
 
	SupplierID INTEGER ,
 
	CategoryID INTEGER ,
 
	QuantityPerUnit VARCHAR(20) ,
 
	UnitPrice DOUBLE ,
 
	UnitsInStock INTEGER ,
 
	UnitsOnOrder INTEGER ,
 
	ReorderLevel INTEGER ,
 
	Discontinued INTEGER default 0 NOT NULL ,
 
	Notes MEDIUMTEXT ,
 
	OrderDate DATETIME ,
    
    PRIMARY KEY(ProductID)) Type=InnoDB;
# -----------------------------------------------------------------------
# blobtest 
# -----------------------------------------------------------------------
drop table if exists blobtest;

CREATE TABLE blobtest(
 
	BlobID INTEGER NOT NULL ,
 
	BlobName VARCHAR(30) NOT NULL ,
 
	BlobData LONGBLOB NOT NULL ,
    
    PRIMARY KEY(BlobID)) Type=InnoDB;
# -----------------------------------------------------------------------
# clobtest 
# -----------------------------------------------------------------------
drop table if exists clobtest;

CREATE TABLE clobtest(
 
	ClobID INTEGER NOT NULL ,
 
	ClobName VARCHAR(30) NOT NULL ,
 
	ClobData LONGTEXT NOT NULL ,
    
    PRIMARY KEY(ClobID)) Type=InnoDB;
# -----------------------------------------------------------------------
# idgentest 
# -----------------------------------------------------------------------
drop table if exists idgentest;

CREATE TABLE idgentest(
 
	ID INTEGER NOT NULL AUTO_INCREMENT,
 
	Name VARCHAR(40) default '' NOT NULL ,
    
    PRIMARY KEY(ID)) Type=InnoDB;
  
  
  
  
