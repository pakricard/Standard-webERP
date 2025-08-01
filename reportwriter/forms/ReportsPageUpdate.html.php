<?php echo '<html lang="' . str_replace('_', '-', substr($Language, 0, 5)) . ">"; ?>
<head></head>
<body>
<h2 align="center"><?php echo $Prefs['reportname'].' - '.RPT_BTN_PGSETUP; ?></h2>
<form name="RptPageSetup" method="post" action="ReportMaker.php?action=go">
	<input type="hidden" name="FormID" value="<?php echo $_SESSION['FormID']; ?>" />
  <input name="PageForm" type="hidden" value="1">
  <input name="ReportID" type="hidden" value="<?php echo $ReportID ?>">
  <input name="GoBackURL" type="hidden" value="<?php echo $GoBackURL; ?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
   <tr>
	<td width="20%"><input name="todo" type="submit" id="back" value="<?php echo RPT_BTN_CANCEL ?>"></td>
	<td width="20%"><div align="center"><input name="todo" type="submit" id="update" value="<?php echo RPT_BTN_CRIT ?>"></div></td>
	<td width="20%">&nbsp;</td>
	<td width="20%"><div align="center"><input name="todo" type="submit" value="<?php echo RPT_BTN_EXPCSV ?>"></div></td>
	<td width="20%"><div align="right"><input name="todo" type="submit" value="<?php echo RPT_BTN_EXPPDF ?>"></div></td>
  </tr>
</table>
<table width="100%"  border="2" cellspacing="1" cellpadding="1">
    <tr bgcolor="#CCCCCC">
      <td colspan="2"><div align="center"><?php echo RPT_PGLAYOUT ?></div></td>
    </tr>
    <tr>
	  <td width="50%"><?php echo RPT_PAPER ?>
        <select name="PaperSize">
		<?php foreach($PaperSizes as $key=>$value) {
			if ($Prefs['papersize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?>
        </select></td>
	  <td width="50%"><?php echo RPT_ORIEN ?>
		<?php if ($Prefs['paperorientation']=='P') $selected = ' checked'; else  $selected = ''; ?>
        <label><input name="PaperOrientation" type="radio" value="P"<?php echo $selected ?>><?php echo RPT_PORTRAIT ?></label>
		<?php if ($Prefs['paperorientation']=='L') $selected = ' checked'; else  $selected = ''; ?>
        <label><input name="PaperOrientation" type="radio" value="L"<?php echo $selected ?>><?php echo RPT_LANDSCAPE ?></label></td>
	  </tr>
  </table>
  <table width="100%"  border="2" cellspacing="1" cellpadding="1">
    <tr bgcolor="#CCCCCC">
      <td colspan="6"><div align="center"><?php echo RPT_PGMARGIN ?></div></td>
    </tr>
    <tr>
      <td><div align="center"><?php echo RPT_TOP ?>
          <input name="MarginTop" type="text" value="<?php echo $Prefs['margintop']; ?>" size="8" maxlength="7">
          <?php echo RPT_MM ?></div></td>
      <td><div align="center"><?php echo RPT_BOTTOM ?>
          <input name="MarginBottom" type="text" value="<?php echo $Prefs['marginbottom']; ?>" size="8" maxlength="7">
          <?php echo RPT_MM ?></div></td>
      <td><div align="center"><?php echo RPT_LEFT ?>
          <input name="MarginLeft" type="text" value="<?php echo $Prefs['marginleft']; ?>" size="8" maxlength="7">
          <?php echo RPT_MM ?></div></td>
      <td><div align="center"><?php echo RPT_RIGHT ?>
          <input name="MarginRight" type="text" value="<?php echo $Prefs['marginright']; ?>" size="8" maxlength="7">
          <?php echo RPT_MM ?></div></td>
    </tr>
  </table>
  <table width="100%"  border="2" cellspacing="1" cellpadding="1">
    <tr bgcolor="#CCCCCC">
      <td colspan="8"><div align="center"><?php echo RPT_PGHEADER ?></div></td>
    </tr>
    <tr>
      <td colspan="3"><?php echo RPT_PGCOYNM ?></td>
      <td>
	  	<?php if ($Prefs['coynameshow']=='1') $selected = ' checked'; else  $selected = ''; ?><?php echo RPT_SHOW ?>
		<input name="CoyNameShow" type="checkbox" id="CoyNameShow" value="1"<?php echo $selected ?>></td>
      <td colspan="4"><?php echo RPT_FONT ?>
        <select name="CoyNameFont" id="CoyNameFont">
		<?php foreach($Fonts as $key => $value) {
			if ($Prefs['coynamefont']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_SIZE ?><select name="CoyNameFontSize">
		<?php foreach($FontSizes as $key => $value) {
			if ($Prefs['coynamefontsize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_COLOR ?><select name="CoyNameFontColor">
		<?php foreach($FontColors as $key => $value) {
			if ($Prefs['coynamefontcolor']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_ALIGN ?><select name="CoyNameAlign">
		<?php foreach($FontAlign as $key => $value) {
			if ($Prefs['coynamealign']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
	  </td>
    </tr>
    <tr>
      <td colspan="3"><?php echo RPT_PGTITL1 ?>
      <input name="Title1Desc" type="text" id="Title1Desc" value="<?php echo $Prefs['title1desc']; ?>" size="30" maxlength="50"></td>
      <td><?php echo RPT_SHOW ?>
		<?php if ($Prefs['title1show']=='1') $selected = ' checked'; else  $selected = ''; ?>
      <input name="Title1Show" type="checkbox" id="Title1Show" value="1"<?php echo $selected ?>></td>
      <td colspan="4"><?php echo RPT_FONT ?>
        <select name="Title1Font" id="Title1Font">
		<?php foreach($Fonts as $key => $value) {
			if ($Prefs['title1font']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_SIZE ?><select name="Title1FontSize">
		<?php foreach($FontSizes as $key => $value) {
			if ($Prefs['title1fontsize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_COLOR ?><select name="Title1FontColor">
		<?php foreach($FontColors as $key => $value) {
			if ($Prefs['title1fontcolor']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_ALIGN ?><select name="Title1FontAlign">
		<?php foreach($FontAlign as $key => $value) {
			if ($Prefs['title1fontalign']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
	</td>
    </tr>
    <tr>
      <td colspan="3"><?php echo RPT_PGTITL2 ?>
      <input name="Title2Desc" type="text" id="Title2Desc" value="<?php echo $Prefs['title2desc']; ?>" size="30" maxlength="50"></td>
      <td><?php echo RPT_SHOW ?>
		<?php if ($Prefs['title2show']=='1') $selected = ' checked'; else  $selected = ''; ?>
      <input name="Title2Show" type="checkbox" id="Title2Show" value="1"<?php echo $selected ?>></td>
      <td colspan="4"><?php echo RPT_FONT ?>
        <select name="Title2Font" id="Title2Font">
		<?php foreach($Fonts as $key => $value) {
			if ($Prefs['title2font']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_SIZE ?><select name="Title2FontSize">
		<?php foreach($FontSizes as $key => $value) {
			if ($Prefs['title2fontsize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_COLOR ?><select name="Title2FontColor">
		<?php foreach($FontColors as $key => $value) {
			if ($Prefs['title2fontcolor']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_ALIGN ?><select name="Title2FontAlign">
		<?php foreach($FontAlign as $key => $value) {
			if ($Prefs['title2fontalign']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
	</td>
    </tr>
    <tr>
      <td colspan="4"><?php echo RPT_PGFILDESC ?></td>
      <td colspan="4"><?php echo RPT_FONT ?>
          <select name="FilterFont" id="FilterFont">
		<?php foreach($Fonts as $key => $value) {
			if ($Prefs['filterfont']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_SIZE ?><select name="FilterFontSize">
		<?php foreach($FontSizes as $key => $value) {
			if ($Prefs['filterfontsize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_COLOR ?><select name="FilterFontColor">
		<?php foreach($FontColors as $key => $value) {
			if ($Prefs['filterfontcolor']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_ALIGN ?><select name="FilterFontAlign">
		<?php foreach($FontAlign as $key => $value) {
			if ($Prefs['filterfontalign']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
  </td>
    </tr>
    <tr>
      <td colspan="4"><?php echo RPT_RPTDATA ?></td>
      <td colspan="4"><?php echo RPT_FONT ?>
          <select name="DataFont" id="DataFont">
	  <?php foreach($Fonts as $key => $value) {
			if ($Prefs['datafont']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_SIZE ?><select name="DataFontSize">
		<?php foreach($FontSizes as $key => $value) {
			if ($Prefs['datafontsize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_COLOR ?><select name="DataFontColor">
		<?php foreach($FontColors as $key => $value) {
			if ($Prefs['datafontcolor']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_ALIGN ?><select name="DataFontAlign">
		<?php foreach($FontAlign as $key => $value) {
			if ($Prefs['datafontalign']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
</td>
    </tr>
    <tr>
      <td colspan="4"><?php echo RPT_TOTALS ?>
      </td>
      <td colspan="4"><?php echo RPT_FONT ?>
          <select name="TotalsFont" id="TotalsFont">
		<?php foreach($Fonts as $key => $value) {
			if ($Prefs['totalsfont']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_SIZE ?><select name="TotalsFontSize">
		<?php foreach($FontSizes as $key => $value) {
			if ($Prefs['totalsfontsize']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_COLOR ?><select name="TotalsFontColor">
		<?php foreach($FontColors as $key => $value) {
			if ($Prefs['totalsfontcolor']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
      <?php echo RPT_ALIGN ?><select name="TotalsFontAlign">
		<?php foreach($FontAlign as $key => $value) {
			if ($Prefs['totalsfontalign']==$key) $selected = ' selected'; else  $selected = '';
			echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		} ?> </select>
</td>
    </tr>
  </table>
  <table width="100%"  border="2" cellspacing="1" cellpadding="1">
    <tr bgcolor="#CCCCCC">
      <td colspan="8"><div align="center"><?php echo RPT_CWDEF ?> </div></td>
    </tr>
    <tr>
      <td><div align="center"><?php echo RPT_COL1W ?></div></td>
      <td><div align="center"><?php echo RPT_COL2W ?></div></td>
      <td><div align="center"><?php echo RPT_COL3W ?></div></td>
      <td><div align="center"><?php echo RPT_COL4W ?></div></td>
      <td><div align="center"><?php echo RPT_COL5W ?></div></td>
      <td><div align="center"><?php echo RPT_COL6W ?></div></td>
      <td><div align="center"><?php echo RPT_COL7W ?></div></td>
      <td><div align="center"><?php echo RPT_COL8W ?></div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="Col1Width" type="text" id="Col1Width" value="<?php echo $Prefs['col1width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col2Width" type="text" id="Col2Width" value="<?php echo $Prefs['col2width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col3Width" type="text" id="Col3Width" value="<?php echo $Prefs['col3width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col4Width" type="text" id="Col4Width" value="<?php echo $Prefs['col4width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col5Width" type="text" id="Col5Width" value="<?php echo $Prefs['col5width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col6Width" type="text" id="Col6Width" value="<?php echo $Prefs['col6width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col7Width" type="text" id="Col7Width" value="<?php echo $Prefs['col7width']; ?>" size="5" maxlength="3"></div></td>
      <td><div align="center">
        <input name="Col8Width" type="text" id="Col8Width" value="<?php echo $Prefs['col8width']; ?>" size="5" maxlength="3"></div></td>
    </tr>
  </table>
</form>
</body>
</html>
