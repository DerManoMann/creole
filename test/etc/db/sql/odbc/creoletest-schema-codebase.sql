-- -----------------------------------------------------------------------
-- products-- -----------------------------------------------------------------------
drop table products;

CREATE TABLE products
(
	ProductID  NUMERIC(19,0) NOT NULL,
	ProductNam VARCHAR(40) NOT NULL,
 	SupplierID NUMERIC(19,0),
 	CategoryID NUMERIC(19,0),
 	QtyPerUnit VARCHAR(20),
 	UnitPrice  NUMERIC(5,2),
 	UnitsInStk NUMERIC(19,0),
 	UnitsOnOrd NUMERIC(19,0),
 	ReorderLvl NUMERIC(19,0),
 	Discontinu NUMERIC(19,0) NOT NULL,
 	Notes      MEMO,
 	OrderDate  DATE
);

CREATE UNIQUE INDEX pk ON products ( ProductID );

-- -----------------------------------------------------------------------
-- blobtest-- -----------------------------------------------------------------------
drop table blobtest;

CREATE TABLE blobtest
(
	BlobID     NUMERIC(19,0) NOT NULL,
 	BlobName   VARCHAR(30) NOT NULL,
 	BlobData   LONGVARBINARY NOT NULL
);

CREATE UNIQUE INDEX pk ON blobtest ( BlobID );

-- -----------------------------------------------------------------------
-- clobtest-- -----------------------------------------------------------------------
drop table clobtest;

CREATE TABLE clobtest
(
	ClobID     NUMERIC(19,0) NOT NULL,
 	ClobName   VARCHAR(30) NOT NULL,
 	ClobData   MEMO NOT NULL  
);

CREATE UNIQUE INDEX pk ON clobtest ( ClobID );

-- -----------------------------------------------------------------------
-- idgentest-- -----------------------------------------------------------------------
drop table idgentest;

CREATE TABLE idgentest
(
	ID        NUMERIC(19,0),
 	Name      VARCHAR(40) NOT NULL  
);

CREATE UNIQUE INDEX pk ON idgentest ( ID );