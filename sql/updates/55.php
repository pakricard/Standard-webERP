<?php

AddColumn('stockid', 'hremployees', 'VARCHAR(64)', ' Null ', 'NULL', 'locationid');
AddColumn('normalhours', 'hremployees', 'DOUBLE', ' NOT Null ', '40', 'stockid');

AddIndex(array('stockid'), 'hremployees', 'idx_stockid');

UpdateDBNo(basename(__FILE__, '.php'), __('Unify employees and hremployees tables'));
