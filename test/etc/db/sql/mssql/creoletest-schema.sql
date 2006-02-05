
/* ---------------------------------------------------------------------- */
/* products                                                    */
/* ---------------------------------------------------------------------- */

IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'products')
BEGIN
     DECLARE @reftable_1 nvarchar(60), @constraintname_1 nvarchar(60)
     DECLARE refcursor CURSOR FOR
     select reftables.name tablename, cons.name constraintname
      from sysobjects tables,
           sysobjects reftables,
           sysobjects cons,
           sysreferences ref
       where tables.id = ref.rkeyid
         and cons.id = ref.constid
         and reftables.id = ref.fkeyid
         and tables.name = 'products'
     OPEN refcursor
     FETCH NEXT from refcursor into @reftable_1, @constraintname_1     while @@FETCH_STATUS = 0
     BEGIN
       exec ('alter table '+@reftable_1+' drop constraint '+@constraintname_1)
       FETCH NEXT from refcursor into @reftable_1, @constraintname_1     END
     CLOSE refcursor
     DEALLOCATE refcursor
     DROP TABLE products	 
END

CREATE TABLE products(
 
	ProductID INT NOT NULL ,
 
	ProductName VARCHAR (40) default '' NOT NULL ,
 
	SupplierID INT NULL ,
 
	CategoryID INT NULL ,
 
	QuantityPerUnit VARCHAR (20) NULL ,
 
	UnitPrice FLOAT NULL ,
 
	UnitsInStock INT NULL ,
 
	UnitsOnOrder INT NULL ,
 
	ReorderLevel INT NULL ,
 
	Discontinued INT default 0 NOT NULL ,
 
	Notes TEXT NULL ,
 
	OrderDate DATETIME NULL ,
    CONSTRAINT products_PK PRIMARY KEY(ProductID));	
 


/* ---------------------------------------------------------------------- */
/* blobtest                                                    */
/* ---------------------------------------------------------------------- */

IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'blobtest')
BEGIN
     DECLARE @reftable_2 nvarchar(60), @constraintname_2 nvarchar(60)
     DECLARE refcursor CURSOR FOR
     select reftables.name tablename, cons.name constraintname
      from sysobjects tables,
           sysobjects reftables,
           sysobjects cons,
           sysreferences ref
       where tables.id = ref.rkeyid
         and cons.id = ref.constid
         and reftables.id = ref.fkeyid
         and tables.name = 'blobtest'
     OPEN refcursor
     FETCH NEXT from refcursor into @reftable_2, @constraintname_2     while @@FETCH_STATUS = 0
     BEGIN
       exec ('alter table '+@reftable_2+' drop constraint '+@constraintname_2)
       FETCH NEXT from refcursor into @reftable_2, @constraintname_2     END
     CLOSE refcursor
     DEALLOCATE refcursor
     DROP TABLE blobtest	 
END

CREATE TABLE blobtest(
 
	BlobID INT NOT NULL ,
 
	BlobName VARCHAR (30) NOT NULL ,
 
	BlobData IMAGE NOT NULL ,
    CONSTRAINT blobtest_PK PRIMARY KEY(BlobID));	
 


/* ---------------------------------------------------------------------- */
/* clobtest                                                    */
/* ---------------------------------------------------------------------- */

IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'clobtest')
BEGIN
     DECLARE @reftable_3 nvarchar(60), @constraintname_3 nvarchar(60)
     DECLARE refcursor CURSOR FOR
     select reftables.name tablename, cons.name constraintname
      from sysobjects tables,
           sysobjects reftables,
           sysobjects cons,
           sysreferences ref
       where tables.id = ref.rkeyid
         and cons.id = ref.constid
         and reftables.id = ref.fkeyid
         and tables.name = 'clobtest'
     OPEN refcursor
     FETCH NEXT from refcursor into @reftable_3, @constraintname_3     while @@FETCH_STATUS = 0
     BEGIN
       exec ('alter table '+@reftable_3+' drop constraint '+@constraintname_3)
       FETCH NEXT from refcursor into @reftable_3, @constraintname_3     END
     CLOSE refcursor
     DEALLOCATE refcursor
     DROP TABLE clobtest	 
END

CREATE TABLE clobtest(
 
	ClobID INT NOT NULL ,
 
	ClobName VARCHAR (30) NOT NULL ,
 
	ClobData TEXT NOT NULL ,
    CONSTRAINT clobtest_PK PRIMARY KEY(ClobID));	
 


/* ---------------------------------------------------------------------- */
/* idgentest                                                    */
/* ---------------------------------------------------------------------- */

IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'idgentest')
BEGIN
     DECLARE @reftable_4 nvarchar(60), @constraintname_4 nvarchar(60)
     DECLARE refcursor CURSOR FOR
     select reftables.name tablename, cons.name constraintname
      from sysobjects tables,
           sysobjects reftables,
           sysobjects cons,
           sysreferences ref
       where tables.id = ref.rkeyid
         and cons.id = ref.constid
         and reftables.id = ref.fkeyid
         and tables.name = 'idgentest'
     OPEN refcursor
     FETCH NEXT from refcursor into @reftable_4, @constraintname_4     while @@FETCH_STATUS = 0
     BEGIN
       exec ('alter table '+@reftable_4+' drop constraint '+@constraintname_4)
       FETCH NEXT from refcursor into @reftable_4, @constraintname_4     END
     CLOSE refcursor
     DEALLOCATE refcursor
     DROP TABLE idgentest	 
END

CREATE TABLE idgentest(
 
	ID INT NOT NULL IDENTITY,
 
	Name VARCHAR (40) default '' NOT NULL ,
    CONSTRAINT idgentest_PK PRIMARY KEY(ID));	
 


/* ---------------------------------------------------------------------- */
/* temporaltest                                                    */
/* ---------------------------------------------------------------------- */

IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = 'temporaltest')
BEGIN
     DECLARE @reftable_5 nvarchar(60), @constraintname_5 nvarchar(60)
     DECLARE refcursor CURSOR FOR
     select reftables.name tablename, cons.name constraintname
      from sysobjects tables,
           sysobjects reftables,
           sysobjects cons,
           sysreferences ref
       where tables.id = ref.rkeyid
         and cons.id = ref.constid
         and reftables.id = ref.fkeyid
         and tables.name = 'temporaltest'
     OPEN refcursor
     FETCH NEXT from refcursor into @reftable_5, @constraintname_5     while @@FETCH_STATUS = 0
     BEGIN
       exec ('alter table '+@reftable_5+' drop constraint '+@constraintname_5)
       FETCH NEXT from refcursor into @reftable_5, @constraintname_5     END
     CLOSE refcursor
     DEALLOCATE refcursor
     DROP TABLE temporaltest	 
END

CREATE TABLE temporaltest(
 
	ID INT NOT NULL ,
 
	timecol DATETIME NULL ,
 
	datecol DATETIME NULL ,
 
	timestampcol DATETIME NULL ,
    CONSTRAINT temporaltest_PK PRIMARY KEY(ID));	
 


/* ---------------------------------------------------------------------- */
/* products                                                      */
/* ---------------------------------------------------------------------- */



/* ---------------------------------------------------------------------- */
/* blobtest                                                      */
/* ---------------------------------------------------------------------- */



/* ---------------------------------------------------------------------- */
/* clobtest                                                      */
/* ---------------------------------------------------------------------- */



/* ---------------------------------------------------------------------- */
/* idgentest                                                      */
/* ---------------------------------------------------------------------- */



/* ---------------------------------------------------------------------- */
/* temporaltest                                                      */
/* ---------------------------------------------------------------------- */


