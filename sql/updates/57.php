<?php

// Complete conversion of incorrectly named "Manufacturers" feature to "Brands"
// - Reference https://github.com/timschofield/webERP/wiki/PLM-Features-Rename-manufacturers-table
// - Resolves https://github.com/timschofield/webERP/issues/678
// - Code changes include renaming Manufacturers.php to Brands.php and changing all references in scripts
//   from the manufacturers table to the brands table (and appropriate column names).

// 1. Make changes to db schema
// 1.1 brands table
// RenameTable($OldName, $NewName)
RenameTable('manufacturers', 'brands');

// rename table columns to indicate "Brands" 
// URL and image are not required when inserting data
// ChangeColumnName($OldName, $Table, $Type, $Null, $Default, $NewName, $AutoIncrement = '')
ChangeColumnName('manufacturers_id', 'brands', 'INT(11)', 'NOT NULL', '', 'brands_id', 'AUTO_INCREMENT');
ChangeColumnName('manufacturers_name', 'brands', 'VARCHAR(32)', 'NOT NULL', '', 'brands_name', '');
ChangeColumnName('manufacturers_url', 'brands', 'VARCHAR(50)', 'NULL', '', 'brands_url', '');
ChangeColumnName('manufacturers_image', 'brands', 'VARCHAR(64)', 'NULL', '', 'brands_image', '');

// rename secondary index(s)
// DropIndex($Table, $Name)
DropIndex('brands', 'manufacturers_name');
// AddIndex($Columns, $Table, $Name)
AddIndex(array('brands_name'), 'brands', 'idx_brands_name');

// 1.2 salescatprod table
// rename manufacturers_id to brands_id
// URL and image are not required when inserting data
// ChangeColumnName($OldName, $Table, $Type, $Null, $Default, $NewName, $AutoIncrement = '')
//ChangeColumnName('manufacturers_id', 'salescatprod', 'INT(11)', 'NOT NULL', '', 'brands_id', '');
// Use direct SQL because ChangeColumnName() fails due to "invalid value"
$SQL = "ALTER TABLE `salescatprod` CHANGE `manufacturers_id` `brands_id` INT(11) NOT NULL;";
$Result = DB_query($SQL);

// rename secondary index(s)
// DropIndex($Table, $Name)
DropIndex('salescatprod', 'manufacturer_id');
// AddIndex($Columns, $Table, $Name)
AddIndex(array('brands_id'), 'salescatprod', 'idx_brands_id');

// 2. De-register "Manufacturers.php" script and register "Brands.php" instead
// RemoveScript($ScriptName)
RemoveScript('Manufacturers.php');
// NewScript($ScriptName, $PageSecurity)
NewScript('Brands.php', 15);  // Security Token: User Management and System Admistration - TODO reasign as appropriate

// 3. Update menu
// Update Inventory module Brands Management menu item [Inventory > Maintenance > Brands Management]
// RemoveMenuItem($Link, $Section, $Caption, $URL)
RemoveMenuItem('stock', 'Maintenance', __('Brands Maintenance'), '/Manufacturers.php');
// NewMenuItem($Link, $Section, $Caption, $URL, $Sequence)
NewMenuItem('stock', 'Maintenance', __('Brands Maintenance'), '/Brands.php', 5);

// 4. Wrap-up
UpdateDBNo(basename(__FILE__, '.php'), __('Manufacturers to Brands - Part 2'));
