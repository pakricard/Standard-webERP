<?php echo '<html lang="' . str_replace('_', '-', substr($Language, 0, 5)) . ">"; ?>
<head></head>
<body>
<form name="formhome" method="post" action="FormMaker.php<?php echo $QueryString; ?>">
	<input type="hidden" name="FormID" value="<?php echo $_SESSION['FormID']; ?>" />
  <input name="GoBackURL" type="hidden" value="<?php echo $GoBackURL; ?>">
  <table width="550" align="center" border="1" cellspacing="1" cellpadding="1">
		<tr bgcolor="#CCCCCC"><td colspan="3" align="center"><?php echo RPT_FORMOUTPUT; ?></td></tr>
		<?php echo $OutputString; ?>
		<tr>
		  <td width="33%"><input type="submit" name="todo" value="<?php echo RPT_BTN_CANCEL; ?>"></td>
		  <td width="33%" align="center"><input type="submit" name="todo" value="<?php echo RPT_BTN_CRIT; ?>"></td>
		  <td width="33%" align="right"><input type="submit" name="todo" value="<?php echo RPT_BTN_EXPPDF; ?>"></td>
		</tr>
  </table>
</form>
</body>
</html>
