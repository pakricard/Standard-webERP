<?php


// Script to do some Sales Integrity checks
// No SQL updates or Inserts - so safe to run


include('includes/session.php');
$Title = _('Sales Integrity');
$ViewTopic = 'SpecialUtilities';
$BookMark = basename(__FILE__, '.php'); ;
include('includes/header.php');


echo '<div class="centre"><h3>' . _('Sales Integrity Check') . '</h3></div>';

echo '<br /><br />' . _('Check every Invoice has a Sales Order') . '<br />';
echo '<br /><br />' . _('Check every Invoice has a Tax Entry') . '<br />';
echo '<br /><br />' . _('Check every Invoice has a GL Entry') . '<br />';
$SQL = 'SELECT id, transno, order_, trandate FROM debtortrans WHERE type = 10';
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT orderno, orddate FROM salesorders WHERE orderno = '" . $MyRow['order_'] . "'";
	$Result2 = DB_query($SQL2);

	if ( DB_num_rows($Result2) == 0) {
		echo '<br />' . _('Invoice '). ' '. $MyRow['transno'] . ' : ';
		echo '<div style="color:red">' . _('No Sales Order') . '</div>';
	}

	$SQL3 = "SELECT debtortransid FROM debtortranstaxes WHERE debtortransid = '" . $MyRow['id'] . "'";
	$Result3 = DB_query($SQL3);

	if ( DB_num_rows($Result3) == 0) {
		echo '<br />' .  _('Invoice '). ' ' . $MyRow['transno'] . ' : ';
		echo '<div style="color:red">' . _('Has no Tax Entry') . '</div>';
	}

	$SQL4 = "SELECT typeno
				FROM gltrans
				WHERE type = 10
				AND typeno = '" . $MyRow['transno'] . "'";
	$Result4 = DB_query($SQL4);

	if ( DB_num_rows($Result4) == 0) {
		echo '<br />' . _('Invoice') . ' ' . $MyRow['transno'] . ' : ';
		echo '<div style="color:red">' . _('has no GL Entry') . '</div>';
	}
}


echo '<br /><br />' . _('Check for orphan GL Entries') . '<br />';
$SQL = "SELECT DISTINCT typeno, counterindex FROM gltrans WHERE type = 10";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT id,
					transno,
					trandate
				FROM debtortrans
				WHERE type = 10
				AND transno = '" . $MyRow['typeno'] . "'";
	$Result2 = DB_query($SQL2);

	if ( DB_num_rows($Result2) == 0) {
			echo "<br />"._('GL Entry ') . $MyRow['counterindex'] . " : ";
			echo ', <div style="color:red">' . _('Invoice ') . $MyRow['typeno'] . _(' could not be found') . '</div>';
	}
}

echo '<br /><br />' . _('Check Receipt totals') . '<br />';
$SQL = "SELECT typeno,
				amount
		FROM gltrans
		WHERE type = 12
		AND account = '" . $_SESSION['CompanyRecord']['debtorsact'] . "'";

$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT SUM((ovamount+ovgst)/rate)
			FROM debtortrans
			WHERE type = 12
			AND transno = '" . $MyRow['typeno'] . "'";

	$Result2 = DB_query($SQL2);
	$MyRow2 = DB_fetch_row($Result2);

	if ( $MyRow2[0] + $MyRow['amount'] == 0 ) {
			echo '<br />' . _('Receipt') . ' ' . $MyRow['typeno'] . " : ";
			echo '<div style="color:red">' . $MyRow['amount']. ' ' . _('in GL but found'). ' ' . $MyRow2[0] . ' ' . _('in debtorstrans') . '</div>';
	}
}

echo '<br /><br />' . _('Check for orphan Receipts') . '<br />';
$SQL = "SELECT transno FROM debtortrans WHERE type = 12";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT amount FROM gltrans WHERE type = 12 AND typeno = '" . $MyRow['transno'] . "'";
	$Result2 = DB_query($SQL2);
	$MyRow2 = DB_fetch_row($Result2);

	if ( !$MyRow2[0] ) {
		echo '<br />' . _('Receipt') . ' ' . $MyRow['transno'] . " : ";
		echo '<div style="color:red">' . $MyRow['transno'] . ' ' ._('not found in GL')."</div>";
	}
}


echo '<br /><br />' . _('Check for orphan Sales Orders') . '<br />';
$SQL = "SELECT orderno, orddate FROM salesorders";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT transno,
					order_,
					trandate
				FROM debtortrans
				WHERE type = 10
				AND order_ = '" . $MyRow['orderno'] . "'";

	$Result2 = DB_query($SQL2);

	if ( DB_num_rows($Result2) == 0) {
		echo '<br />' . _('Sales Order') . ' ' . $MyRow['orderno'] . ' : ';
		echo '<div style="color:red">' . _('Has no Invoice') . '</div>';
	}
}

echo '<br /><br />' . _('Check for orphan Order Items') . '<br />';
echo '<br /><br />' . _('Check Order Item Amounts') . '<br />';
$SQL = "SELECT orderno FROM salesorderdetails";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT orderno, orddate FROM salesorders WHERE orderno = '" . $MyRow['orderno'] . "'";
	$Result2 = DB_query($SQL2);

	if ( DB_num_rows($Result2) == 0) {
			echo '<br />' . _('Order Item') . ' ' . $MyRow['orderno'] . ' : ';
			echo ', <div style="color:red">' . _('Has no Sales Order') . '</div>';
	}

	$sumsql = "SELECT ROUND(SUM(qtyinvoiced * unitprice * (1 - discountpercent)), 3) AS InvoiceTotal
				FROM salesorderdetails
				WHERE orderno = '" . $MyRow['orderno'] . "'";
	$sumresult = DB_query($sumsql);

	if ($sumrow = DB_fetch_array($sumresult)) {
		$invSQL = "SELECT transno,
							type,
							trandate,
							settled,
							rate,
							SUM(ovamount) AS ovamount,
							ovgst
				 	FROM debtortrans WHERE order_ = '" . $MyRow['orderno'] . "'";
		$invResult = DB_query($invSQL);

		while( $invrow = DB_fetch_array($invResult) ) {
			// Ignore credit notes
			if ( $invrow['type'] != 11 ) {
					// Do an integrity check on sales order items
					if ( $sumrow['InvoiceTotal'] != $invrow['ovamount'] ) {
						echo '<br /><div style="color:red">' . _('Debtors trans') . ' ' . $invrow['ovamount'] . ' ' . _('differ from salesorderdetails') . ' ' . $sumrow['InvoiceTotal'] . '</div>';
					}
			}
		}
	}
}


echo '<br /><br />' . _('Check for orphan Stock Moves') . '<br />';
$SQL = "SELECT stkmoveno, transno FROM stockmoves";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT transno,
					order_,
					trandate
				FROM debtortrans
				WHERE type BETWEEN 10 AND 11
				AND transno = '" . $MyRow['transno'] . "'";

	$Result2 = DB_query($SQL2);

	if ( DB_num_rows($Result2) == 0) {
			echo '<br />' . _('Stock Move') . ' ' . $MyRow['stkmoveno'] . ' : ';
			echo ', <div style="color:red">' . _('Has no Invoice') . '</div>';
	}
}


echo '<br /><br />' . _('Check for orphan Tax Entries') . '<br />';
$SQL = "SELECT debtortransid FROM debtortranstaxes";
$Result = DB_query($SQL);

while ($MyRow = DB_fetch_array($Result)) {
	$SQL2 = "SELECT id, transno, trandate FROM debtortrans WHERE type BETWEEN 10 AND 11 AND id = '" . $MyRow['debtortransid'] . "'";
	$Result2 = DB_query($SQL2);

	if ( DB_num_rows($Result2) == 0) {
			echo '<br />' . _('Tax Entry') . ' ' . $MyRow['debtortransid'] . ' : ';
			echo ', <div style="color:red">' . _('Has no Invoice') . '</div>';
	}
}

echo '<br /><br />' . _('Sales Integrity Check completed.') . '<br /><br />';

prnMsg(_('Sales Integrity Check completed.'),'info');

include('includes/footer.php');
