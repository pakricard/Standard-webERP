<?php

ChangeColumnType('stockmaster', 'lastcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('stockmaster', 'materialcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('stockmaster', 'labourcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('stockmaster', 'overheadcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('stockmaster', 'actualcost', 'decimal(24,8)', ' NOT NULL ', '');

ChangeColumnType('lastcostrollup', 'matcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('lastcostrollup', 'labcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('lastcostrollup', 'oheadcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('lastcostrollup', 'newmatcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('lastcostrollup', 'newlabcost', 'decimal(24,8)', ' NOT NULL ', '');
ChangeColumnType('lastcostrollup', 'newoheadcost', 'decimal(24,8)', ' NOT NULL ', '');	

if ($_SESSION['Updates']['Errors'] == 0) {
	UpdateDBNo(basename(__FILE__, '.php'), __('Increase precision of cost fields to 8 decimal places'), true);
}