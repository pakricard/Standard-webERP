<?php


include('includes/session.php');

$Title = _('Sales Analysis Report Columns');
$ViewTopic = 'SalesAnalysis';
$BookMark = 'SalesAnalysis';

include('includes/header.php');


function DataOptions ($DataX){

/*Sales analysis headers group by data options */
 if ($DataX == 'Quantity'){
     echo '<option selected="selected" value="Quantity">' . _('Quantity') . '</option>';
 } else {
    echo '<option value="Quantity">' . _('Quantity') . '</option>';
 }
 if ($DataX == 'Gross Value'){
     echo '<option selected="selected" value="Gross Value">' . _('Gross Value') . '</option>';
 } else {
    echo '<option value="Gross Value">' . _('Gross Value') . '</option>';
 }
 if ($DataX == 'Net Value'){
     echo '<option selected="selected" value="Net Value">' . _('Net Value') . '</option>';
 } else {
    echo '<option value="Net Value">' . _('Net Value') . '</option>';
 }
 if ($DataX == 'Gross Profit'){
     echo '<option selected="selected" value="Gross Profit">' . _('Gross Profit') . '</option>';
 } else {
    echo '<option value="Gross Profit">' . _('Gross Profit') . '</option>';
 }
 if ($DataX == 'Cost'){
     echo '<option selected="selected" value="Cost">' . _('Cost') . '</option>';
 } else {
    echo '<option value="Cost">' . _('Cost') . '</option>';
 }
 if ($DataX == 'Discount'){
     echo '<option selected="selected" value="Discount">' . _('Discount') . '</option>';
 } else {
    echo '<option value="Discount">' . _('Discount') . '</option>';
 }

}
/* end of functions

Right ... now to the meat */

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($_GET['SelectedCol'])){
	$SelectedCol = $_GET['SelectedCol'];
} elseif (isset($_POST['SelectedCol'])){
	$SelectedCol = $_POST['SelectedCol'];
}


if (isset($_GET['ReportID'])){
	$ReportID = $_GET['ReportID'];
} elseif (isset($_POST['ReportID'])){
	$ReportID = $_POST['ReportID'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input
	first off validate inputs sensible */

	if (mb_strlen($_POST['ReportHeading']) >70) {
		$InputError = 1;
		prnMsg(_('The report heading must be 70 characters or less long'),'error');
	}
	if (!is_numeric($_POST['PeriodFrom']) AND $_POST['Calculation']==0){
		$InputError = 1;
		prnMsg(_('The period from must be numeric'),'error');
	}
	if (!is_numeric($_POST['PeriodTo']) AND $_POST['Calculation']==0){
		$InputError = 1;
		prnMsg(_('The period to must be numeric'),'error');
	}
	if (!is_numeric($_POST['Constant']) AND $_POST['Calculation']==1){
		$InputError = 1;
		prnMsg(_('The constant entered must be numeric'),'error');
	}
	if (isset($_POST['ColID']) AND !is_numeric($_POST['ColID'])){
		$InputError = 1;
		prnMsg(_('The column number must be numeric'),'error');
	} elseif(isset($_POST['ColID']) AND $_POST['ColID'] >10){
		$InputError = 1;
		prnMsg(_('The column number must be less than 11'),'error');
	}
	if ($_POST['Calculation']==0 AND $_POST['PeriodFrom'] > $_POST['PeriodTo']){
		$InputError = 1;
		prnMsg(_('The Period From must be before the Period To, otherwise the column will always have no value'),'error');
	}


	if (isset($SelectedCol) AND $InputError !=1) {


		$SQL = "UPDATE reportcolumns SET heading1='" . $_POST['Heading1'] . "',
                                     heading2='" . $_POST['Heading2'] . "',
                                     calculation='" . $_POST['Calculation'] . "',
                                     periodfrom='" . $_POST['PeriodFrom'] . "',
                                     periodto='" . $_POST['PeriodTo'] . "',
                                     datatype='" . $_POST['DataType'] . "',
                                     colnumerator='" . $_POST['ColNumerator'] . "',
                                     coldenominator='" . $_POST['ColDenominator'] . "',
                                     calcoperator='" . $_POST['CalcOperator'] . "',
                                     budgetoractual='" . $_POST['BudgetOrActual'] . "',
                                     valformat='" . $_POST['ValFormat'] . "',
                                     constant = '" . $_POST['Constant'] . "'
                                     WHERE
                                     reportid = '".$ReportID."' AND
                                     colno='". $SelectedCol ."'";
		$ErrMsg = _('The report column could not be updated because');
		$DbgMsg = _('The SQL used to update the report column was');

		$Result = DB_query($SQL,$ErrMsg,$DbgMsg);

		prnMsg(_('Column') . ' ' . $SelectedCol . ' ' . _('has been updated'),'info');
		unset($SelectedCol);
		unset($_POST['ColID']);
		unset($_POST['Heading1']);
		unset($_POST['Heading2']);
		unset($_POST['Calculation']);
		unset($_POST['PeriodFrom']);
		unset($_POST['PeriodTo']);
		unset($_POST['DataType']);
		unset($_POST['ColNumerator']);
		unset($_POST['ColDenominator']);
		unset($_POST['CalcOperator']);
		unset($_POST['Constant']);
		unset($_POST['BudgetOrActual']);


	} elseif ($InputError !=1 AND
			(($_POST['Calculation']==1 AND
			(($_POST['ColNumerator']>0 AND $_POST['Constant']!=0) OR ($_POST['ColNumerator']>0 AND $_POST['ColDenominator']>0))
			OR $_POST['Calculation']==0))) {

	/*SelectedReport is null cos no item selected on first time round so must be adding a new column to the report */

		$SQL = "INSERT INTO reportcolumns (reportid,
                                       colno,
                                       heading1,
                                       heading2,
                                       calculation,
                                       periodfrom,
                                       periodto,
                                       datatype,
                                       colnumerator,
                                       coldenominator,
                                       calcoperator,
                                       constant,
                                       budgetoractual,
                                       valformat )
                                       VALUES (
                                       $ReportID,
                                       '" . $_POST['ColID'] . "',
                                       '" . $_POST['Heading1'] . "',
                                       '" . $_POST['Heading2'] . "',
                                       '" . $_POST['Calculation'] . "',
                                       '" . $_POST['PeriodFrom'] . "',
                                       '" . $_POST['PeriodTo'] . "',
                                       '" . $_POST['DataType'] . "',
                                       '" . $_POST['ColNumerator'] . "',
                                       '" . $_POST['ColDenominator'] . "',
                                       '" . $_POST['CalcOperator'] . "',
                                       '" . $_POST['Constant'] . "',
                                       '" . $_POST['BudgetOrActual'] . "',
                                       '" . $_POST['ValFormat'] . "')";

		$ErrMsg = _('The column could not be added to the report because');
		$DbgMsg = _('The SQL used to add the column to the report was');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg);

		prnMsg(_('Column') . ' ' . $_POST['ColID'] . ' ' . _('has been added to the database'),'info');

		unset($SelectedCol);
		unset($_POST['ColID']);
		unset($_POST['Heading1']);
		unset($_POST['Heading2']);
		unset($_POST['Calculation']);
		unset($_POST['PeriodFrom']);
		unset($_POST['PeriodTo']);
		unset($_POST['DataType']);
		unset($_POST['ColNumerator']);
		unset($_POST['ColDenominator']);
		unset($_POST['CalcOperator']);
		unset($_POST['Constant']);
		unset($_POST['BudgetOrActual']);
		unset($_POST['ValFormat']);

	}


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$SQL="DELETE FROM reportcolumns WHERE reportid='".$ReportID."' AND colno='".$SelectedCol."'";

	$ErrMsg = _('The deletion of the column failed because');
	$DbgMsg = _('The SQL used to delete this report column was');
	$Result = DB_query($SQL,$ErrMsg,$DbgMsg);

	prnMsg(_('Column') . ' ' . $SelectedCol . ' ' . _('has been deleted'),'info');

}

/* List of Columns will be displayed with links to delete or edit each.
These will call the same page again and allow update/input or deletion of the records*/

$SQL = "SELECT reportheaders.reportheading,
               reportcolumns.colno,
               reportcolumns.heading1,
               reportcolumns.heading2,
               reportcolumns.calculation,
               reportcolumns.periodfrom,
               reportcolumns.periodto,
               reportcolumns.datatype,
               reportcolumns.colnumerator,
               reportcolumns.coldenominator,
               reportcolumns.calcoperator,
               reportcolumns.budgetoractual,
               reportcolumns.constant
         FROM
               reportheaders,
               reportcolumns
        WHERE  reportheaders.reportid = reportcolumns.reportid
	AND    reportcolumns.reportid='".$ReportID. "'
        ORDER BY reportcolumns.colno";

$ErrMsg = _('The column definitions could not be retrieved from the database because');
$DbgMsg = _('The SQL used to retrieve the columns for the report was');
$Result = DB_query($SQL,$ErrMsg,$DbgMsg);

if (DB_num_rows($Result)!=0){

	$MyRow = DB_fetch_array($Result);
	echo '<div class="centre"><b>' . $MyRow['reportheading'] . '</b>
		<br />
		</div>
		<table class="selection">
		<tr>
			<th>' . _('Col') . ' #</th>
            <th>' . _('Heading 1') . '</th>
            <th>' . _('Heading 2') . '</th>
			<th>' . _('Calc') . '</th>
			<th>' . _('Prd From') . '</th>
			<th>' . _('Prd To') . '</th>
			<th>' . _('Data') . '</th>
			<th>' . _('Col') . ' #<br />' . _('Numerator') . '</th>
			<th>' . _('Col') . ' #<br />' . _('Denominator') . '</th>
			<th>' . _('Operator') . '</th>
			<th>' . _('Budget') . '<br />' . _('Or Actual') . '</th>
			<th></th>
		</tr>';

	do {
	if ($MyRow[11]==1){
		$BudOrAct = _('Actual');
	} else {
		$BudOrAct = _('Budget');
	}
	if ($MyRow[4]==0){
		$Calc = _('No');
	} else {
		$Calc = _('Yes');
		$BudOrAct = _('N/A');
	}

		echo '<tr class="striped_row">
				<td><a href="', htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?ReportID=', $ReportID, '&amp;SelectedCol=', $MyRow[1], '">', $MyRow[1], '</a></td>
				<td>', $MyRow[2], '</td>
				<td>', $MyRow[3], '</td>
				<td>', $Calc, '</td>
				<td>', $MyRow[5], '</td>
				<td>', $MyRow[6], '</td>
				<td>', $MyRow[7], '</td>
				<td>', $MyRow[8], '</td>
				<td>', $MyRow[9], '</td>
				<td>', $MyRow[10], '</td>
				<td>', $BudOrAct, '</td>
				<td><a href="', htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?ReportID=', $ReportID, '&amp;SelectedCol=', $MyRow[1], '&amp;delete=1">' . _('Delete') . '</a></td>
			</tr>';

	} while ($MyRow = DB_fetch_array($Result));
	//END WHILE LIST LOOP
 }

echo '</table>
		<div class="centre">
			<a href="' . $RootPath . '/SalesAnalRepts.php">' . _('Maintain Report Headers') . '</a>
		</div>';
if (DB_num_rows($Result)>10){
    prnMsg(_('WARNING') . ': ' . _('User defined reports can have up to 10 columns defined') . '. ' . _('The report will not be able to be run until some columns are deleted'),'warn');
}

if (!isset($_GET['delete'])) {

	$SQL = "SELECT reportheading FROM reportheaders WHERE reportid='".$ReportID."'";
	$Result = DB_query($SQL);
	$MyRow=DB_fetch_array($Result);
	$ReportHeading=$MyRow['reportheading'];
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="hidden" name="ReportHeading" value="'.$ReportHeading.'" />';
	echo '<input type="hidden" name="ReportID" value="' . $ReportID . '" />';
	if (isset($SelectedCol)) {
		//editing an existing Column

		$SQL = "SELECT reportid,
                   	colno,
                   	heading1,
                   	heading2,
                   	calculation,
                   	periodfrom,
                   	periodto,
                   	datatype,
                   	colnumerator,
                   	coldenominator,
                   	calcoperator,
                   	constant,
                   	budgetoractual,
                   	valformat
                   	FROM
                   	reportcolumns
                   	WHERE
                   	reportcolumns.reportid='".$ReportID."' AND
                   	reportcolumns.colno='". $SelectedCol ."'";


		$ErrMsg =  _('The column') . ' ' . $SelectedCol . ' ' . _('could not be retrieved because');
		$DbgMsg =  _('The SQL used to retrieve the this column was');

		$Result = DB_query($SQL,$ErrMsg, $DbgMsg);

		$MyRow = DB_fetch_array($Result);

		$_POST['Heading1']=$MyRow['heading1'];
		$_POST['Heading2']= $MyRow['heading2'];
		$_POST['Calculation']=$MyRow['calculation'];
		$_POST['PeriodFrom']=$MyRow['periodfrom'];
		$_POST['PeriodTo']=$MyRow['periodto'];
		$_POST['DataType'] = $MyRow['datatype'];
		$_POST['ColNumerator']=$MyRow['colnumerator'];
		$_POST['ColDenominator']=$MyRow['coldenominator'];
		$_POST['CalcOperator']=$MyRow['calcoperator'];
		$_POST['Constant']=$MyRow['constant'];
		$_POST['BudgetOrActual']=$MyRow['budgetoractual'];
		$_POST['ValFormat']=$MyRow['valformat'];

		echo '<input type="hidden" name="SelectedCol" value="' . $SelectedCol . '" />';
		echo '<fieldset>
				<legend>', _('Sales Analysis Columns'), '</legend>';

	} else {
		echo '<fieldset>
				<legend>', _('Sales Analysis Columns'), '</legend>';
		if (!isset($_POST['ColID'])) {
			$_POST['ColID']=1;
		}
		echo '<field>
				<label for="ColID">' . _('Column Number') . ':</label>
				<input type="text" class="number" name="ColID" size="3" maxlength="3" value="' . $_POST['ColID'] . '" />
				<fieldhelp>' . _('A number between 1 and 10 is expected'), '</fieldhelp>
			</field>';
	}
	if (!isset($_POST['Heading1'])) {
		$_POST['Heading1']='';
	}
	echo '<field>
			<label for="Heading1">' . _('Heading line 1') . ':</label>
			<input type="text" size="16" maxlength="15" name="Heading1" value="' . $_POST['Heading1'] . '" />
		</field>';
	if (!isset($_POST['Heading2'])) {
		$_POST['Heading2']='';
	}
	echo '<field>
			<label for="Heading2">' . _('Heading line 2') . ':</label>
			<input type="text" size="16" maxlength="15" name="Heading2" value="' . $_POST['Heading2'] . '" />
		</field>';
	echo '<field>
			<label for="Calculation">' . _('Calculation') . ':</label>
			<select name="Calculation">';
	if (!isset($_POST['Calculation'])) {
		$_POST['Calculation']=0;
	}
	if ($_POST['Calculation'] ==1){
		echo '<option selected="selected" value="1">' . _('Yes') . '</option>';
		echo '<option value="0">' . _('No') . '</option>';
	} else {
		echo '<option value="1">' . _('Yes') . '</option>';
		echo '<option selected="selected" value="0">' . _('No') . '</option>';
	}
	echo '</select>
		</field>';

	if ($_POST['Calculation']==0){ /*Its not a calculated column */

		echo '<field>
				<label for="PeriodFrom">' . _('From Period') . ':</label>
				<select name="PeriodFrom">';
		$SQL = "SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC";
		$ErrMsg = _('Could not load periods table');
		$Result = DB_query($SQL,$ErrMsg);
		while ($PeriodRow = DB_fetch_row($Result)){
			if ($_POST['PeriodFrom']==$PeriodRow[0]){
				echo  '<option selected="selected" value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[1]) . '</option>';
			} else {
				echo  '<option value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[1]) . '</option>';
			}
		}
		echo '</select>
			</field>';

		echo '<field>
				<label for="PeriodTo">' . _('ToPeriod') . ':</label>
				<select name="PeriodTo">';
		$SQL = "SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC";
		$ErrMsg = _('Could not load periods table');
		$Result = DB_query($SQL,$ErrMsg);
		while ($PeriodRow = DB_fetch_row($Result)){
			if ($_POST['PeriodTo']==$PeriodRow[0]){
				echo  '<option selected="selected" value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[1]) . '</option>';
			} else {
				echo  '<option value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[1]) . '</option>';
			}
		}
		echo '</select>
			</field>';

		echo '<field>
				<label for="BudgetOrActual">' . _('Data to show') . ':</label>
				<select name="DataType">';
		DataOptions($_POST['DataType']);
		echo '</select>
			</field>';

		echo '<field>
				<label for="BudgetOrActual">' . _('Budget or Actual') . ':</label>
				<select name="BudgetOrActual">';
		if ($_POST['BudgetOrActual']==0){
			echo '<option selected="selected" value="0">' . _('Budget') . '</option>';
			echo '<option value="1">' . _('Actual') . '</option>';
		} else {
		      echo '<option value="0">' . _('Budget') . '</option>';
		      echo '<option selected="selected" value="1">' . _('Actual') . '</option>';
		}
		echo '</select>';
		echo '<input type="hidden" name="ValFormat" value="N" />
				<input type="hidden" name="ColNumerator" value="0" />
				<input type="hidden" name="ColDenominator" value="0" />
				<input type="hidden" name="CalcOperator" value="" />
				<input type="hidden" name="Constant" value="0" />';
        echo '</field>';
	} else {  /*it IS a calculated column */

		echo '<field>
				<label for="ColNumerator">' . _('Numerator Column') . ' #:</label>
				<input type="text" size="4" maxlength="3" name="ColNumerator" value="' . $_POST['ColNumerator'] . '" />
			</field>';
		echo '<field>
				<label for="ColDenominator">' . _('Denominator Column') . ' #:</label>
				<input type="text" size="4" maxlength="3" name="ColDenominator" value="' . $_POST['ColDenominator'] . '" />
			</field>';
		echo '<field>
				<label for="CalcOperator">' . _('Calculation Operator') . ':</label>
				<select name="CalcOperator">';
		if ($_POST['CalcOperator'] == '/'){
		     echo '<option selected="selected" value="/">' . _('Numerator Divided By Denominator') . '</option>';
		} else {
		    echo '<option value="/">' . _('Numerator Divided By Denominator') . '</option>';
		}
		if ($_POST['CalcOperator'] == 'C'){
		     echo '<option selected="selected" value="/">' . _('Numerator Divided By Constant') . '</option>';
		} else {
		    echo '<option value="/C">' . _('Numerator Divided By Constant') . '</option>';
		}
		if ($_POST['CalcOperator'] == '*'){
		     echo '<option selected="selected" value="*">' . _('Numerator Col x Constant') . '</option>';
		} else {
		    echo '<option value="*">' . _('Numerator Col x Constant') . '</option>';
		}
		if ($_POST['CalcOperator'] == '+'){
		     echo '<option selected="selected" value="+">' . _('Add to') . '</option>';
		} else {
		    echo '<option value="+">' . _('Add to') . '</option>';
		}
		if ($_POST['CalcOperator'] == '-'){
		     echo '<option selected="selected" value="-">' . _('Numerator Minus Denominator') . '</option>';
		} else {
		    echo '<option value="-">' . _('Numerator Minus Denominator') . '</option>';
		}

		echo '</select>
			</field>';

		echo '<field>
				<label for="Constant">' . _('Constant') . ':</label>
				<input type="text" size="10" maxlength="10" name="Constant" value="' . $_POST['Constant'] . '" />
			</field>';
		echo '<field>
				<label for="ValFormat">' . _('Format Type') . ':</label>
				<select name="ValFormat">';
		if ($_POST['ValFormat']=='N'){
			  echo '<option selected="selected" value="N">' . _('Numeric') . '</option>';
			  echo '<option value="P">' . _('Percentage') . '</option>';
		} else {
			  echo '<option value="N">' . _('Numeric') . '</option>';
		  	echo '<option selected="selected" value="P">' . _('Percentage') . '</option>';
		}
		echo '</select>
		</field>';

		echo '<input type="hidden" name="BudgetOrActual" value="0" />';
		echo '<input type="hidden" name="DataType" value="" />';
		echo '<input type="hidden" name="PeriodFrom" value="0" />';
		echo '<input type="hidden" name="PeriodTo" value="0" />';
	}

	echo '</fieldset>';

	echo '<br />
			<div class="centre">
				<input type="submit" name="submit" value="' . _('Enter Information') . '" />
			</div>
        </div>
		</form>';

} //end if record deleted no point displaying form to add record

include('includes/footer.php');
