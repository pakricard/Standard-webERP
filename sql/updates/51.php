<?php

// ChangeColumnSize($Column, $Table, $Type, $Null, $Default, $Size)
ChangeColumnSize('cust_part', 'custitem',  'VARCHAR(64)', ' NOT NULL ', '', '64');
ChangeColumnSize('cust_description', 'custitem', 'VARCHAR(255)', ' NOT NULL ', '', '255');

if ($_SESSION['Updates']['Errors'] == 0) {
  UpdateDBNo(basename(__FILE__, '.php'), __('Increase customer part and description size'), true);
}
