
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- products
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `products`;


CREATE TABLE `products` 
(
	`ProductID` INTEGER  NOT NULL,
	`ProductName` VARCHAR(40) default '' NOT NULL,
	`SupplierID` INTEGER,
	`CategoryID` INTEGER,
	`QuantityPerUnit` VARCHAR(20),
	`UnitPrice` DOUBLE,
	`UnitsInStock` INTEGER,
	`UnitsOnOrder` INTEGER,
	`ReorderLevel` INTEGER,
	`Discontinued` INTEGER default 0 NOT NULL,
	`Notes` TEXT,
	`OrderDate` DATE,
	PRIMARY KEY (`ProductID`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- blobtest
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `blobtest`;


CREATE TABLE `blobtest` 
(
	`BlobID` INTEGER  NOT NULL,
	`BlobName` VARCHAR(30)  NOT NULL,
	`BlobData` LONGBLOB  NOT NULL,
	PRIMARY KEY (`BlobID`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- clobtest
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `clobtest`;


CREATE TABLE `clobtest` 
(
	`ClobID` INTEGER  NOT NULL,
	`ClobName` VARCHAR(30)  NOT NULL,
	`ClobData` LONGTEXT  NOT NULL,
	PRIMARY KEY (`ClobID`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- idgentest
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `idgentest`;


CREATE TABLE `idgentest` 
(
	`ID` INTEGER  NOT NULL AUTO_INCREMENT,
	`Name` VARCHAR(40) default '' NOT NULL,
	PRIMARY KEY (`ID`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- temporaltest
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `temporaltest`;


CREATE TABLE `temporaltest` 
(
	`ID` INTEGER  NOT NULL,
	`timecol` TIME,
	`datecol` DATE,
	`timestampcol` DATETIME,
	PRIMARY KEY (`ID`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
