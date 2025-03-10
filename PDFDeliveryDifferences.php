<?php


include ('includes/session.php');
if (isset($_POST['FromDate'])){$_POST['FromDate'] = ConvertSQLDate($_POST['FromDate']);};
if (isset($_POST['ToDate'])){$_POST['ToDate'] = ConvertSQLDate($_POST['ToDate']);};
include('includes/SQL_CommonFunctions.inc');

$InputError=0;

if (isset($_POST['FromDate']) AND !Is_Date($_POST['FromDate'])){
	$Msg = _('The date from must be specified in the format') . ' ' . $_SESSION['DefaultDateFormat'];
	$InputError=1;
}
if (isset($_POST['ToDate']) AND !Is_Date($_POST['ToDate'])){
	$Msg =  _('The date to must be specified in the format') . ' ' .  $_SESSION['DefaultDateFormat'];
	$InputError=1;
}

if (!isset($_POST['FromDate']) OR !isset($_POST['ToDate']) OR $InputError==1){

	 $Title = _('Delivery Differences Report');
	$ViewTopic = 'Sales';
	$BookMark = '';
	 include ('includes/header.php');

	echo '<div class="centre"><p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . $Title . '" alt="" />' . ' '
		. _('Delivery Differences Report') . '</p></div>';

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<fieldset>
			<legend>', _('Report Criteria'), '</legend>
			<field>
				<label for="FromDate">' . _('Enter the date from which variances between orders and deliveries are to be listed') . ':</label>
				<input required="required" autofocus="autofocus" type="date" name="FromDate" maxlength="10" size="11" value="' . Date('Y-m-d', Mktime(0,0,0,Date('m')-1,0,Date('y'))) . '" />
			</field>';
	echo '<field>
			<label for="ToDate">' . _('Enter the date to which variances between orders and deliveries are to be listed') . ':</label>
			<input required="required" type="date" name="ToDate" maxlength="10" size="11" value="' . Date('Y-m-d') . '" />
		</field>';
	echo '<field>
			<label for="CategoryID">' . _('Inventory Category') . '</label>';

	$SQL = "SELECT categorydescription,
					categoryid
			FROM stockcategory
			WHERE stocktype<>'D'
			AND stocktype<>'L'";

	$Result = DB_query($SQL);


	 echo '<select name="CategoryID">
			<option selected="selected" value="All">' . _('Over All Categories') . '</option>';

	while ($MyRow=DB_fetch_array($Result)){
		echo '<option value="' . $MyRow['categoryid'] . '">' . $MyRow['categorydescription']  . '</option>';
	}

	 echo '</select>
		</field>
		<field>
			<label for="Location">' . _('Inventory Location') . ':</label>
			<select name="Location">
				<option selected="selected" value="All">' . _('All Locations')  . '</option>';

	$Result= DB_query("SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1");
	while ($MyRow=DB_fetch_array($Result)){
		echo '<option value="' . $MyRow['loccode'] . '">' . $MyRow['locationname']  . '</option>';
	}
	 echo '</select>
		</field>';

	 echo '<field>
				<label for="Email">' . _('Email the report off') . ':</label>
				<select name="Email">
					<option selected="selected" value="No">' . _('No')  . '</option>
					<option value="Yes">' . _('Yes')  . '</option>
				</select>
			</field>
			</fieldset>
			<div class="centre">
				<input type="submit" name="Go" value="' . _('Create PDF') . '" />
			</div>
		</form>';

	 if ($InputError==1){
	 	prnMsg($Msg,'error');
	 }
	 include('includes/footer.php');
	 exit;
} else {
	include('includes/ConnectDB.inc');
}

if ($_POST['CategoryID']=='All' AND $_POST['Location']=='All'){
	$SQL= "SELECT invoiceno,
			orderdeliverydifferenceslog.orderno,
			orderdeliverydifferenceslog.stockid,
			stockmaster.description,
			stockmaster.decimalplaces,
			quantitydiff,
			trandate,
			orderdeliverydifferenceslog.debtorno,
			orderdeliverydifferenceslog.branch
		FROM orderdeliverydifferenceslog INNER JOIN stockmaster
			ON orderdeliverydifferenceslog.stockid=stockmaster.stockid
		INNER JOIN salesorders ON orderdeliverydifferenceslog.orderno = salesorders.orderno
		INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		INNER JOIN debtortrans ON orderdeliverydifferenceslog.invoiceno=debtortrans.transno
			AND debtortrans.type=10
			AND trandate >='" . FormatDateForSQL($_POST['FromDate']) . "'
			AND trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'";

} elseif ($_POST['CategoryID']!='All' AND $_POST['Location']=='All') {
	$SQL= "SELECT invoiceno,
			orderdeliverydifferenceslog.orderno,
			orderdeliverydifferenceslog.stockid,
			stockmaster.description,
			stockmaster.decimalplaces,
			quantitydiff,
			trandate,
			orderdeliverydifferenceslog.debtorno,
			orderdeliverydifferenceslog.branch
		FROM orderdeliverydifferenceslog INNER JOIN stockmaster
			ON orderdeliverydifferenceslog.stockid=stockmaster.stockid
			INNER JOIN salesorders ON orderdeliverydifferenceslog.orderno = salesorders.orderno
			INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			INNER JOIN debtortrans ON orderdeliverydifferenceslog.invoiceno=debtortrans.transno
			AND debtortrans.type=10
			AND trandate >='" . FormatDateForSQL($_POST['FromDate']) . "'
			AND trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
			AND categoryid='" . $_POST['CategoryID'] ."'";

} elseif ($_POST['CategoryID']=='All' AND $_POST['Location']!='All') {
	$SQL = "SELECT invoiceno,
			orderdeliverydifferenceslog.orderno,
			orderdeliverydifferenceslog.stockid,
			stockmaster.description,
			stockmaster.decimalplaces,
			quantitydiff,
			trandate,
			orderdeliverydifferenceslog.debtorno,
			orderdeliverydifferenceslog.branch
		FROM orderdeliverydifferenceslog INNER JOIN stockmaster
			ON orderdeliverydifferenceslog.stockid=stockmaster.stockid
			INNER JOIN debtortrans
				ON orderdeliverydifferenceslog.invoiceno=debtortrans.transno
				INNER JOIN salesorders
					ON orderdeliverydifferenceslog.orderno=salesorders.orderno
				INNER JOIN locationusers
					ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		WHERE debtortrans.type=10
		AND salesorders.fromstkloc='". $_POST['Location'] . "'
		AND trandate >='" . FormatDateForSQL($_POST['FromDate']) . "'
		AND trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'";

} elseif ($_POST['CategoryID']!='All' AND $_POST['location']!='All'){

	$SQL = "SELECT invoiceno,
			orderdeliverydifferenceslog.orderno,
			orderdeliverydifferenceslog.stockid,
			stockmaster.description,
			stockmaster.decimalplaces,
			quantitydiff,
			trandate,
			orderdeliverydifferenceslog.debtorno,
			orderdeliverydifferenceslog.branch
		FROM orderdeliverydifferenceslog INNER JOIN stockmaster
			ON orderdeliverydifferenceslog.stockid=stockmaster.stockid
			INNER JOIN debtortrans
				ON orderdeliverydifferenceslog.invoiceno=debtortrans.transno
				AND debtortrans.type=10
				INNER JOIN salesorders
					ON orderdeliverydifferenceslog.orderno = salesorders.orderno
				INNER JOIN locationusers
					ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		WHERE salesorders.fromstkloc='" . $_POST['Location'] . "'
		AND categoryid='" . $_POST['CategoryID'] . "'
		AND trandate >='" . FormatDateForSQL($_POST['FromDate']) . "'
		AND trandate <= '" . FormatDateForSQL($_POST['ToDate']) . "'";
}

if ($_SESSION['SalesmanLogin'] != '') {
	$SQL .= " AND debtortrans.salesperson='" . $_SESSION['SalesmanLogin'] . "'";
}

$Result=DB_query($SQL,'','',false,false); //dont error check - see below

if (DB_error_no()!=0){
	$Title = _('Delivery Differences Log Report Error');
	include('includes/header.php');
	prnMsg( _('An error occurred getting the variances between deliveries and orders'),'error');
	if ($Debug==1){
		prnMsg( _('The SQL used to get the variances between deliveries and orders that failed was') . '<br />' . $SQL,'error');
	}
	include ('includes/footer.php');
	exit;
} elseif (DB_num_rows($Result) == 0){
	$Title = _('Delivery Differences Log Report Error');
  	include('includes/header.php');
	prnMsg( _('There were no variances between deliveries and orders found in the database within the period from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate'] . '. ' . _('Please try again selecting a different date range'), 'info');
	if ($Debug==1) {
		prnMsg( _('The SQL that returned no rows was') . '<br />' . $SQL,'error');
	}
	include('includes/footer.php');
	exit;
}

include('includes/PDFStarter.php');

/*PDFStarter.php has all the variables for page size and width set up depending on the users default preferences for paper size */

$pdf->addInfo('Title',_('Variances Between Deliveries and Orders'));
$pdf->addInfo('Subject',_('Variances Between Deliveries and Orders from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);
$LineHeight=12;
$PageNumber = 1;
$TotalDiffs = 0;

include ('includes/PDFDeliveryDifferencesPageHeader.inc');

while ($MyRow=DB_fetch_array($Result)){

	  $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,40,$FontSize,$MyRow['invoiceno'], 'left');
	  $LeftOvers = $pdf->addTextWrap($Left_Margin+40,$YPos,40,$FontSize,$MyRow['orderno'], 'left');
	  $LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,200,$FontSize,$MyRow['stockid'] . ' - ' . $MyRow['description'], 'left');

	  $LeftOvers = $pdf->addTextWrap($Left_Margin+280,$YPos,50,$FontSize,locale_number_format($MyRow['quantitydiff'],$MyRow['decimalplaces']), 'right');
	  $LeftOvers = $pdf->addTextWrap($Left_Margin+335,$YPos,50,$FontSize,$MyRow['debtorno'], 'left');
	  $LeftOvers = $pdf->addTextWrap($Left_Margin+385,$YPos,50,$FontSize,$MyRow['branch'], 'left');
	  $LeftOvers = $pdf->addTextWrap($Left_Margin+435,$YPos,50,$FontSize,ConvertSQLDate($MyRow['trandate']), 'left');

	  $YPos -= ($LineHeight);
	  $TotalDiffs++;

	  if ($YPos - (2 *$LineHeight) < $Bottom_Margin){
		  /*Then set up a new page */
			  $PageNumber++;
		  include ('includes/PDFDeliveryDifferencesPageHeader.inc');
	  } /*end of new page header  */
} /* end of while there are delivery differences to print */


$YPos-=$LineHeight;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,_('Total number of differences') . ' ' . locale_number_format($TotalDiffs), 'left');

if ($_POST['CategoryID']=='All' AND $_POST['Location']=='All'){
	$SQL = "SELECT COUNT(salesorderdetails.orderno)
			FROM salesorderdetails INNER JOIN debtortrans
				ON salesorderdetails.orderno=debtortrans.order_ INNER JOIN salesorders
				ON salesorderdetails.orderno = salesorders.orderno INNER JOIN locationusers
				ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE debtortrans.trandate>='" . FormatDateForSQL($_POST['FromDate']) . "'
			AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'";

} elseif ($_POST['CategoryID']!='All' AND $_POST['Location']=='All') {
	$SQL = "SELECT COUNT(salesorderdetails.orderno)
		FROM salesorderdetails INNER JOIN debtortrans
			ON salesorderdetails.orderno=debtortrans.order_ INNER JOIN stockmaster
			ON salesorderdetails.stkcode=stockmaster.stockid INNER JOIN salesorders
			ON salesorderdetails.orderno = salesorders.orderno INNER JOIN locationusers
			ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		WHERE debtortrans.trandate>='" . FormatDateForSQL($_POST['FromDate']) . "'
		AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
		AND stockmaster.categoryid='" . $_POST['CategoryID'] . "'";

} elseif ($_POST['CategoryID']=='All' AND $_POST['Location']!='All'){

	$SQL = "SELECT COUNT(salesorderdetails.orderno)
		FROM salesorderdetails INNER JOIN debtortrans
			ON salesorderdetails.orderno=debtortrans.order_ INNER JOIN salesorders
			ON salesorderdetails.orderno = salesorders.orderno INNER JOIN locationusers
			ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		WHERE debtortrans.trandate>='". FormatDateForSQL($_POST['FromDate']) . "'
		AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
		AND salesorders.fromstkloc='" . $_POST['Location'] . "'";

} elseif ($_POST['CategoryID'] !='All' AND $_POST['Location'] !='All'){

	$SQL = "SELECT COUNT(salesorderdetails.orderno)
		FROM salesorderdetails INNER JOIN debtortrans ON salesorderdetails.orderno=debtortrans.order_
			INNER JOIN salesorders ON salesorderdetails.orderno = salesorders.orderno
			INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			INNER JOIN stockmaster ON salesorderdetails.stkcode = stockmaster.stockid
		WHERE salesorders.fromstkloc ='" . $_POST['Location'] . "'
		AND categoryid='" . $_POST['CategoryID'] . "'
		AND trandate >='" . FormatDateForSQL($_POST['FromDate']) . "'
		AND trandate <= '" . FormatDateForSQL($_POST['ToDate']) . "'";

}

if ($_SESSION['SalesmanLogin'] != '') {
	$SQL .= " AND debtortrans.salesperson='" . $_SESSION['SalesmanLogin'] . "'";
}

$ErrMsg = _('Could not retrieve the count of sales order lines in the period under review');
$Result = DB_query($SQL,$ErrMsg);


$MyRow=DB_fetch_row($Result);
$YPos-=$LineHeight;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,_('Total number of order lines') . ' ' . locale_number_format($MyRow[0]), 'left');

$YPos-=$LineHeight;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,_('DIFOT') . ' ' . locale_number_format((1-($TotalDiffs/$MyRow[0])) * 100,2) . '%', 'left');

$ReportFileName = $_SESSION['DatabaseName'] . '_DeliveryDifferences_' . date('Y-m-d').'.pdf';
$pdf->OutputD($ReportFileName);

if ($_POST['Email']=='Yes'){
	if (file_exists($_SESSION['reports_dir'] . '/'.$ReportFileName)){
		unlink($_SESSION['reports_dir'] . '/'.$ReportFileName);
	}
	$pdf->Output($_SESSION['reports_dir'].'/'.$ReportFileName,'F');

	include('includes/htmlMimeMail.php');

	$mail = new htmlMimeMail();
	$Attachment = $mail->getFile($_SESSION['reports_dir'] . '/'.$ReportFileName);
	$mail->setText(_('Please find herewith delivery differences report from') . ' ' . $_POST['FromDate'] .  ' '. _('to') . ' ' . $_POST['ToDate']);
	$mail->addAttachment($Attachment, $ReportFileName, 'application/pdf');
	if($_SESSION['SmtpSetting'] == 0){
		$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
		$Result = $mail->send(array($_SESSION['FactoryManagerEmail']));
	}else{
		$Result = SendmailBySmtp($mail,array($_SESSION['FactoryManagerEmail']));
	}


}
$pdf->__destruct();

?>
