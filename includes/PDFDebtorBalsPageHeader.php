<?php
/*PDF page header for aged analysis reports */
$PageNumber++;
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=8;
$YPos= $Page_Height-$Top_Margin;

$pdf->addText($Left_Margin, $YPos,$FontSize, $_SESSION['CompanyRecord']['coyname']);

$YPos -=$LineHeight;

$FontSize =10;
$pdf->addText($Left_Margin, $YPos, $FontSize, _('Customer Balances For Customers between') . ' ' . $_POST['FromCriteria'] .  ' ' . _('and') . ' ' . $_POST['ToCriteria'] . ' ' . _('as at') . ' ' . $PeriodEndDate);

$FontSize = 8;
$pdf->addText($Page_Width-$Right_Margin-120,$YPos,$FontSize, _('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(3*$LineHeight);

/*Draw a rectangle to put the headings in     */
$pdf->rectangle($Left_Margin, $YPos+$LineHeight, $Page_Width-$Right_Margin-$Left_Margin,$LineHeight+5 );

/*set up the headings */
$Xpos = $Left_Margin+3;

$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,220 - $Left_Margin,$FontSize,_('Customer'),'left');
$LeftOvers = $pdf->addTextWrap(220,$YPos,60,$FontSize,_('Balance'),'right');
$LeftOvers = $pdf->addTextWrap(280,$YPos,60,$FontSize,_('FX'),'right');
$LeftOvers = $pdf->addTextWrap(350,$YPos,60,$FontSize,_('Currency'),'left');

$pdf->rectangle($Left_Margin, $YPos+$LineHeight, $Page_Width-$Right_Margin-$Left_Margin,$Page_Height-($LineHeight*5)-$Bottom_Margin-5 );

$pdf->line(218, $YPos+$LineHeight, 218, $Bottom_Margin);
$pdf->line(282, $YPos+$LineHeight, 282, $Bottom_Margin);
$pdf->line(342, $YPos+$LineHeight, 342, $Bottom_Margin);

$YPos =$YPos - (2*$LineHeight);
