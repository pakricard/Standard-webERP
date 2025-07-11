<?php
/*PDF page header for inventory valuation report */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);

$YPos -=$LineHeight;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,_('Low GP Sales Between') . ' ' . $_POST['FromDate'] . ' ' . _('and') . ' ' . $_POST['ToDate'] . ' ' . _('less than') . ' ' . $_POST['GPMin'] . '%');
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize,_('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '    ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$LineHeight);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$LineHeight,$Page_Width-$Right_Margin, $YPos+$LineHeight);
$pdf->line($Left_Margin, $YPos+$LineHeight,$Left_Margin, $YPos- $LineHeight);
$pdf->line($Left_Margin, $YPos- $LineHeight,$Page_Width-$Right_Margin, $YPos- $LineHeight);
$pdf->line($Page_Width-$Right_Margin, $YPos+$LineHeight,$Page_Width-$Right_Margin, $YPos- $LineHeight);

$pdf->line($Left_Margin, $YPos+$LineHeight,$Page_Width-$Right_Margin, $YPos+$LineHeight);
$pdf->line($Left_Margin, $YPos+$LineHeight,$Left_Margin, $Bottom_Margin);
$pdf->line($Left_Margin, $Bottom_Margin, $Page_Width-$Right_Margin, $Bottom_Margin);
$pdf->line($Page_Width-$Right_Margin, $YPos+$LineHeight,$Page_Width-$Right_Margin, $Bottom_Margin);

$pdf->line(98, $YPos+$LineHeight, 98, $Bottom_Margin);
$pdf->line(128, $YPos+$LineHeight, 128, $Bottom_Margin);
$pdf->line(218, $YPos+$LineHeight, 218, $Bottom_Margin);
$pdf->line(338, $YPos+$LineHeight, 338, $Bottom_Margin);
$pdf->line(398, $YPos+$LineHeight, 398, $Bottom_Margin);
$pdf->line(448, $YPos+$LineHeight, 448, $Bottom_Margin);
$pdf->line(503, $YPos+$LineHeight, 503, $Bottom_Margin);

/*set up the headings */
$Xpos = $Left_Margin+1;

$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,100-$Left_Margin,$FontSize,_('Trans'), 'centre');
$LeftOvers = $pdf->addTextWrap(100,$YPos,50,$FontSize,_('No'), 'centre');
$LeftOvers = $pdf->addTextWrap(130,$YPos,50,$FontSize,_('Item'), 'centre');
$LeftOvers = $pdf->addTextWrap(220,$YPos,130,$FontSize,_('Customer'), 'centre');
$LeftOvers = $pdf->addTextWrap(340,$YPos,50,$FontSize,_('Sell Price'), 'right');
$LeftOvers = $pdf->addTextWrap(380,$YPos,62,$FontSize,_('Cost'), 'right');
$LeftOvers = $pdf->addTextWrap(440,$YPos,60,$FontSize,_('GP'), 'right');
$LeftOvers = $pdf->addTextWrap(500,$YPos,60,$FontSize,_('GP') . ' %', 'right');

$FontSize=8;
$YPos =$YPos - (2*$LineHeight);

$PageNumber++;
