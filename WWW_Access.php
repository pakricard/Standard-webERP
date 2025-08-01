<?php
/* This script is to maintaining access permissions. */

include('includes/session.php');
$Title = _('Access Permissions Maintenance');// Screen identificator.
$ViewTopic = 'SecuritySchema';// Filename's id in ManualContents.php's TOC.
$BookMark = 'WWW_Access';// Anchor's id in the manual's html document.
include('includes/header.php');
echo '<p class="page_title_text"><img alt="" src="'.$RootPath.'/css/'.$Theme.
	'/images/group_add.png" title="' .
	_('Access Permissions Maintenance') . '" /> ' .// Icon title.
	_('Access Permissions Maintenance') . '</p>';// Page title.

if($AllowDemoMode) {
	prnMsg(_('The the system is in demo mode and the security model administration is disabled'), 'warn');
	include('includes/footer.php');
	exit();
}

if(isset($_GET['SelectedRole'])) {
	$SelectedRole = $_GET['SelectedRole'];
} elseif (isset($_POST['SelectedRole'])) {
	$SelectedRole = $_POST['SelectedRole'];
}

if (isset($_POST['submit']) OR isset($_GET['remove']) OR isset($_GET['add']) ) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	if ($AllowDemoMode){
		$InputError =1;
		prnMsg('The demo functionality is crippled to prevent access problems. No changes will be made','warn');
	}
	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */
	//first off validate inputs sensible
	if (isset($_POST['SecRoleName']) AND mb_strlen($_POST['SecRoleName'])<4){
		$InputError = 1;
		prnMsg(_('The role description entered must be at least 4 characters long'),'error');
	}

	// if $_POST['SecRoleName'] then it is a modifications on a SecRole
	// else it is either an add or remove of a page token
	unset($SQL);
	if (isset($_POST['SecRoleName']) ){ // Update or Add Security Headings
		if(isset($SelectedRole)) { // Update Security Heading
			$SQL = "UPDATE securityroles SET secrolename = '" . $_POST['SecRoleName'] . "'
					WHERE secroleid = '".$SelectedRole . "'";
			$ErrMsg = _('The update of the security role description failed because');
			$ResMsg = _('The Security role description was updated.');
		} else { // Add Security Heading
			$SQL = "INSERT INTO securityroles (secrolename) VALUES ('" . $_POST['SecRoleName'] ."')";
			$ErrMsg = _('The update of the security role failed because');
			$ResMsg = _('The Security role was created.');
		}
		unset($_POST['SecRoleName']);
		unset($SelectedRole);
	} elseif (isset($SelectedRole) ) {
		$PageTokenId = $_GET['PageToken'];
		if( isset($_GET['add']) ) { // updating Security Groups add a page token
			$SQL = "INSERT INTO securitygroups (secroleid,
											tokenid)
									VALUES ('".$SelectedRole."',
											'".$PageTokenId."' )";
			$ErrMsg = _('The addition of the page group access failed because');
			$ResMsg = _('The page group access was added.');
		} elseif ( isset($_GET['remove']) ) { // updating Security Groups remove a page token
			$SQL = "DELETE FROM securitygroups
					WHERE secroleid = '".$SelectedRole."'
					AND tokenid = '".$PageTokenId . "'";
			$ErrMsg = _('The removal of this page-group access failed because');
			$ResMsg = _('This page-group access was removed.');
		}
		unset($_GET['add']);
		unset($_GET['remove']);
		unset($_GET['PageToken']);
	}
	// Need to exec the query
	if (isset($SQL) AND $InputError != 1 ) {
		$Result = DB_query($SQL,$ErrMsg);
		if( $Result ) {
			prnMsg( $ResMsg,'success');
		}
	}
} elseif (isset($_GET['delete'])) {
	//the Security heading wants to be deleted but some checks need to be performed fist
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'www_users'
	$SQL= "SELECT COUNT(*) FROM www_users WHERE fullaccess='" . $_GET['SelectedRole'] . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0]>0) {
		prnMsg( _('Cannot delete this role because user accounts are setup using it'),'warn');
		echo '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('user accounts that have this security role setting') . '</font>';
	} else {
		$SQL="DELETE FROM securitygroups WHERE secroleid='" . $_GET['SelectedRole'] . "'";
		$Result = DB_query($SQL);
		$SQL="DELETE FROM securityroles WHERE secroleid='" . $_GET['SelectedRole'] . "'";
		$Result = DB_query($SQL);
		prnMsg( $_GET['SecRoleName'] . ' ' . _('security role has been deleted') . '!','success');

	} //end if account group used in GL accounts
	unset($SelectedRole);
	unset($_GET['SecRoleName']);
}

if (!isset($SelectedRole)) {

/* If its the first time the page has been displayed with no parameters then none of the above are true and the list of Users will be displayed with links to delete or edit each. These will call the same page again and allow update/input or deletion of the records*/

	$SQL = "SELECT secroleid,
			secrolename
		FROM securityroles
		ORDER BY secrolename";
	$Result = DB_query($SQL);

	echo '<table class="selection">
		<tr>
			<th>' . _('Role') . '</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>';

	while($MyRow = DB_fetch_array($Result)) {

		/*The SecurityHeadings array is defined in config.php */

		echo '<tr class="striped_row">
				<td>', $MyRow['secrolename'], '</td>
				<td><a href="', htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?&amp;SelectedRole=', $MyRow['secroleid'], '">' . _('Edit') . '</a></td>
				<td><a href="', htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?&amp;SelectedRole=', $MyRow['secroleid'], '&amp;delete=1&amp;SecRoleName=', urlencode($MyRow['secrolename']), '" onclick="return confirm(\'' . _('Are you sure you wish to delete this role?') . '\');">' . _('Delete') . '</a></td>
			</tr>';

	} //END WHILE LIST LOOP
	echo '</table>';
} //end of ifs and buts!


if (isset($SelectedRole)) {
	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Review Existing Roles') . '</a></div>';
}

if (isset($SelectedRole)) {
	//editing an existing role

	$SQL = "SELECT secroleid,
			secrolename
		FROM securityroles
		WHERE secroleid='" . $SelectedRole . "'";
	$Result = DB_query($SQL);
	if ( DB_num_rows($Result) == 0 ) {
		prnMsg( _('The selected role is no longer available.'),'warn');
	} else {
		$MyRow = DB_fetch_array($Result);
		$_POST['SelectedRole'] = $MyRow['secroleid'];
		$_POST['SecRoleName'] = $MyRow['secrolename'];
	}
}
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if( isset($_POST['SelectedRole'])) {
	echo '<input type="hidden" name="SelectedRole" value="' . $_POST['SelectedRole'] . '" />';
}
echo '<fieldset>';
if (!isset($_POST['SecRoleName'])) {
	$_POST['SecRoleName']='';
	echo '<legend>', _('Create Role'), '</legend>';
} else {
	echo '<legend>', _('Amend Role'), '</legend>';
}
echo '<field>
		<label for="SecRoleName">' . _('Role') . ':</label>
		<input type="text" name="SecRoleName" pattern=".{4,}" size="40" maxlength="40" value="' . $_POST['SecRoleName'] . '" required="true" title="" />
		<fieldhelp>'._("The role description entered must be at least 4 characters long").'</fieldhelp>
	</field>';
echo '</fieldset>
	<div class="centre">
		<input type="submit" name="submit" value="' . _('Enter Role') . '" />
	</div>
	</form>';

if (isset($SelectedRole)) {
	$SQL = "SELECT tokenid, tokenname
			FROM securitytokens";

	$SQLUsed = "SELECT tokenid FROM securitygroups WHERE secroleid='". $SelectedRole . "'";

	$Result = DB_query($SQL);

	/*Make an array of the used tokens */
	$UsedResult = DB_query($SQLUsed);
	$TokensUsed = array();
	$i=0;
	while ($MyRow=DB_fetch_row($UsedResult)){
		$TokensUsed[$i] =$MyRow[0];
		$i++;
	}

	echo '<table class="selection"><tr>';

	if (DB_num_rows($Result)>0 ) {
		echo '<th colspan="3"><div class="centre">' . _('Assigned Security Tokens') . '</div></th>';
		echo '<th colspan="3"><div class="centre">' . _('Available Security Tokens') . '</div></th>';
	}
	echo '</tr>';

	while($AvailRow = DB_fetch_array($Result)) {

		if (in_array($AvailRow['tokenid'],$TokensUsed)){
			printf('<tr class="striped_row">
					<td>%s</td>
					<td>%s</td>
					<td><a href="%sSelectedRole=%s&amp;remove=1&amp;PageToken=%s" onclick="return confirm(\'' . _('Are you sure you wish to delete this security token from this role?') . '\');">' . _('Remove') . '</a></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					</tr>',
					$AvailRow['tokenid'],
					$AvailRow['tokenname'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$SelectedRole,
					$AvailRow['tokenid'] );
		} else {
			printf('<tr class="striped_row">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>%s</td>
					<td>%s</td>
					<td><a href="%sSelectedRole=%s&amp;add=1&amp;PageToken=%s">' . _('Add') . '</a></td>
					</tr>',
					$AvailRow['tokenid'],
					$AvailRow['tokenname'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$SelectedRole,
					$AvailRow['tokenid'] );
		}
	}
	echo '</table>';
}

include('includes/footer.php');
