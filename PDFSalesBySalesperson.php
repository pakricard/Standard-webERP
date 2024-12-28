<?php

$DatabaseName='weberp';
$AllowAnyone = true;

include ('includes/session.php');
include('includes/SQL_CommonFunctions.inc');
include ('includes/class.pdf.php');
$_POST['FromDate']=date('Y-m-01');
$_POST['ToDate']= FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));
$WeekStartDate = Date(($_SESSION['DefaultDateFormat']), strtotime($WeekStartDate . ' - 7 days'));
$Recipients = GetMailList('salesbysalesperson');
if (sizeOf($Recipients) == 0) {
	$Title = _('Weekly Orders') . ' - ' . _('Problem Report');
      	include('includes/header.php');
	prnMsg( _('There are no members of the Weekly Orders Recipients email group'), 'warn');
	include('includes/footer.php');
	exit;
}

$SQL= "SELECT salesorders.orderno,
			  salesorders.orddate,
			  salesorderdetails.stkcode,
			  salesorderdetails.unitprice,
			  stockmaster.description,
			  stockmaster.units,
			  stockmaster.decimalplaces,
			  salesorderdetails.quantity,
			  salesorderdetails.qtyinvoiced,
			  salesorderdetails.completed,
			  salesorderdetails.discountpercent,
			  stockmaster.actualcost AS standardcost,
			  debtorsmaster.name,
			  salesman.salesmanname
		 FROM salesorders
			 INNER JOIN salesorderdetails
			 ON salesorders.orderno = salesorderdetails.orderno
			 INNER JOIN stockmaster
			 ON salesorderdetails.stkcode = stockmaster.stockid
			 INNER JOIN debtorsmaster
			 ON salesorders.debtorno=debtorsmaster.debtorno
			 INNER JOIN custbranch ON custbranch.debtorno=salesorders.debtorno
			 AND custbranch.branchcode=salesorders.branchcode
			 INNER JOIN salesman ON salesman.salesmancode=custbranch.salesman
		 WHERE salesorders.orddate >='" . FormatDateForSQL($WeekStartDate) . "'
			  AND salesorders.orddate <='" . $_POST['ToDate'] . "'
		 AND salesorders.quotation=0
		 ORDER BY custbranch.salesman, salesorders.orderno";

$Result=DB_query($SQL,'','',false,false); //dont trap errors here

if (DB_error_no()!=0){
	include('includes/header.php');
	echo '<br />' . _('An error occurred getting the orders details');
	if ($Debug==1){
		echo '<br />' . _('The SQL used to get the orders that failed was') . '<br />' . $SQL;
	}
	include ('includes/footer.php');
	exit;
}
$PaperSize="Letter_Landscape";
include('includes/PDFStarter.php');
$pdf->addInfo('Title',_('Weekly Orders Report'));
$pdf->addInfo('Subject',_('Orders from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);
$LineHeight=12;
$PageNumber = 1;
$TotalDiffs = 0;
include ('includes/PDFWeeklyOrdersPageHeader.inc');
$Col1=2;
$Col2=40;
$Col3=160;
$Col4=210;
$Col5=260;
$Col6=390;
$Col7=450;
$Col8=510;
$Col9=570;
$Col10=650;
$Col11=660;

$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col1,$YPos,$Col2-$Col1-5,$FontSize,_('Order'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col2,$YPos,$Col3-$Col2-5,$FontSize,_('Customer'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col3,$YPos,$Col4-$Col3-5,$FontSize,_('Order Date'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col4,$YPos,$Col5-$Col4-5,$FontSize,_('Item'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col5,$YPos,$Col6-$Col5-5,$FontSize,_('Description'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col6,$YPos,$Col7-$Col6-5,$FontSize,_('Quantity'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col7,$YPos,$Col8-$Col7-5,$FontSize,_('Sales'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col8,$YPos,$Col9-$Col8-5,$FontSize,_('Status'), 'Left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col9,$YPos,$Col10-$Col9-5,$FontSize,_('Salesperson'), 'Left');

$YPos-=$LineHeight;
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);
$YPos-=$LineHeight;
$Salesman='';
while ($MyRow=DB_fetch_array($Result)){

	if ($MyRow['completed']==1) {
		$Status="Closed";
		$Qty=$MyRow['qtyinvoiced'];
	} else {
		$Qty=$MyRow['quantity'];
		if ($MyRow['qtyinvoiced']==0) {
			$Status= _('Ordered');
		} else {
			$Status= _('Partial');
		}
	}
	$SalesValue=$Qty*$MyRow['unitprice']*(1-$MyRow['discountpercent']);
	$SalesCost=$Qty*$MyRow['standardcost'];
	if ($SalesValue <> 0) {
		$GP=($SalesValue-$SalesCost)/$SalesValue *100;
	} else {
		$GP=0;
	}

	if ($Salesman > '' and $Salesman <> $MyRow['salesmanname']){
		$PageNumber++;
		include ('includes/PDFWeeklyOrdersPageHeader.inc');
	} /*end of new page header  */
	$Salesman = $MyRow['salesmanname'];

	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col1,$YPos,$Col2-$Col1-5,$FontSize,$MyRow['orderno'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col2,$YPos,$Col3-$Col2-5,$FontSize,html_entity_decode($MyRow['name'],ENT_QUOTES,'UTF-8'), 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col3,$YPos,$Col4-$Col3-5,$FontSize,ConvertSQLDate($MyRow['orddate']), 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col4,$YPos,$Col5-$Col4-5,$FontSize,$MyRow['stkcode'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col5,$YPos,$Col6-$Col5-5,$FontSize,$MyRow['description'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col6,$YPos,$Col7-$Col6-5,$FontSize,locale_number_format($MyRow['quantity'],$_SESSION['CompanyRecord']['decimalplaces']), 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col7,$YPos,$Col8-$Col7-5,$FontSize,locale_number_format($SalesValue,$_SESSION['CompanyRecord']['decimalplaces']), 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col8,$YPos,$Col9-$Col8-5,$FontSize,$Status, 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+$Col9,$YPos,$Col10-$Col9-5,$FontSize,$MyRow['salesmanname'], 'left');
	if ($YPos - (2 *$LineHeight) < $Bottom_Margin){
		$PageNumber++;
		include ('includes/PDFWeeklyOrdersPageHeader.inc');
	} /*end of new page header  */
	$YPos -= $LineHeight;

} //while

include('includes/htmlMimeMail.php');
$FileName=$_SESSION['reports_dir'] .  '/SalesBySalesperson.pdf';
$pdf->Output($FileName, 'F');
$pdf->__destruct();
$mail = new htmlMimeMail();
$Attachment = $mail->getFile($FileName);
$mail->setText(_('Please find the Sales By Salesperson report'));
$mail->setSubject(_('Sales By Salesperson Report'));
$mail->addAttachment($Attachment, $FileName, 'application/pdf');
//echo '<br /><div class="centre"><a href="' . $RootPath . '/' . $FileName . '">' . _('click here') . '</a> ' . _('to view the file') . '</div>';
if($_SESSION['SmtpSetting']==0){
	$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . '<' . $_SESSION['CompanyRecord']['email'] . '>');
	$Result = $mail->send($Recipients);
}else{
	$Result = SendmailBySmtp($mail,$Recipients);
}
if($Result){
		$Title = _('Print Weekly Orders');
		include('includes/header.php');
		prnMsg(_('The Weekly Orders report has been mailed'),'success');
		echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
		include('includes/footer.php');
		exit;

}else{
		$Title = _('Print Weekly Orders Error');
		include('includes/header.php');
		prnMsg(_('There are errors lead to mails not sent'),'error');
		echo '<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
		include('includes/footer.php');
		exit;

}
?>