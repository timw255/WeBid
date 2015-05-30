<?php
/***************************************************************************
 *   copyright				: (C) 2008 - 2014 WeBid
 *   site					: http://www.webidsupport.com/
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version. Although none of the code may be
 *   sold. If you have been sold this script, get a refund.
 ***************************************************************************/

include 'common.php';

// If user is not logged in redirect to login page
if (!$user->is_logged_in())
{
	$_SESSION['REDIRECT_AFTER_LOGIN'] = 'yourauctions.php';
	header('location: user_login.php');
	exit;
}

$NOW = time();
$NOWB = date('Ymd');
// DELETE OR CLOSE OPEN AUCTIONS
if (isset($_POST['action']) && $_POST['action'] == 'delopenauctions')
{
	if (is_array($_POST['O_delete']) && count($_POST['O_delete']) > 0)
	{
		$removed = 0;
		foreach ($_POST['O_delete'] as $k => $v)
		{
			$v = intval($v);
			// Pictures Gallery
			if ($dir = @opendir($upload_path . $v))
			{
				while ($file = readdir($dir))
				{
					if ($file != '.' && $file != '..')
					{
						@unlink($upload_path . $v . '/' . $file);
					}
				}
				closedir($dir);
				@rmdir($upload_path . $v);
			}

			// Delete auction views
			$query = "DELETE FROM " . $DBPrefix . "auccounter WHERE auction_id = :auc_id";
			$params = array();
			$params[] = array(':auc_id', $v, 'int');
			$db->query($query, $params);

			// Auction
			$query = "DELETE FROM " . $DBPrefix . "auctions WHERE id = :auc_id";
			$params = array();
			$params[] = array(':auc_id', $v, 'int');
			$db->query($query, $params);
			$removed++;
		}

		$query = "UPDATE " . $DBPrefix . "counters SET auctions = (auctions - :removed)";
		$params = array();
		$params[] = array(':removed', $removed, 'int');
		$db->query($query, $params);
	}

	if (is_array($_POST['closenow']))
	{
		foreach ($_POST['closenow'] as $k => $v)
		{
			// Update end time to the current time
			$query = "UPDATE " . $DBPrefix . "auctions SET ends = :time, relist = relisted WHERE id = :auc_id";
			$params = array();
			$params[] = array(':time', $NOW, 'int');
			$params[] = array(':auc_id', $v, 'int');
			$db->query($query, $params);
		}
		include 'cron.php';
	}
}
// Retrieve active auctions from the database
$query = "SELECT count(id) AS COUNT FROM " . $DBPrefix . "auctions WHERE user = :user_id AND closed = 0 AND starts <= :time AND suspended = 0";
$params = array();
$params[] = array(':time', $NOW, 'int');
$params[] = array(':user_id', $user->user_data['id'], 'int');
$db->query($query, $params);
$TOTALAUCTIONS = $db->result('COUNT');

if (!isset($_GET['PAGE']) || $_GET['PAGE'] <= 1 || $_GET['PAGE'] == '')
{
	$OFFSET = 0;
	$PAGE = 1;
}
else
{
	$PAGE = intval($_GET['PAGE']);
	$OFFSET = ($PAGE - 1) * $system->SETTINGS['perpage'];
}
$PAGES = ($TOTALAUCTIONS == 0) ? 1 : ceil($TOTALAUCTIONS / $system->SETTINGS['perpage']);
// Handle columns sorting variables
if (!isset($_SESSION['oa_ord']) && empty($_GET['oa_ord']))
{
	$_SESSION['oa_ord'] = 'title';
	$_SESSION['oa_type'] = 'asc';
}
elseif (!empty($_GET['oa_ord']))
{
	$_SESSION['oa_ord'] = $_GET['oa_ord'];
	$_SESSION['oa_type'] = $_GET['oa_type'];
}
elseif (isset($_SESSION['oa_ord']) && empty($_GET['oa_ord']))
{
	$_SESSION['oa_nexttype'] = $_SESSION['oa_type'];
}
if (!isset($_SESSION['oa_nexttype']) || $_SESSION['oa_nexttype'] == 'desc')
{
	$_SESSION['oa_nexttype'] = 'asc';
}
else
{
	$_SESSION['oa_nexttype'] = 'desc';
}
if (!isset($_SESSION['oa_type']) || $_SESSION['oa_type'] == 'desc') {
	$_SESSION['oa_type_img'] = '<img src="images/arrow_up.gif" align="center" hspace="2" border="0" />';
}
else
{
	$_SESSION['oa_type_img'] = '<img src="images/arrow_down.gif" align="center" hspace="2" border="0" />';
}

$query = "SELECT * FROM " . $DBPrefix . "auctions
	WHERE user = :user_id AND closed = 0
	AND starts <= :time AND suspended = 0
	ORDER BY " . $_SESSION['ca_ord'] . " " . $_SESSION['ca_type'] . " LIMIT :offset, :perpage";
$params = array();
$params[] = array(':user_id', $user->user_data['id'], 'int');
$params[] = array(':time', $NOW, 'int');
$params[] = array(':offset', $OFFSET, 'int');
$params[] = array(':perpage', $system->SETTINGS['perpage'], 'int');
$db->query($query, $params);

$i = 0;
while ($item = $db->fetch())
{
	if ($item['num_bids'] > 0)
	{
		$query = "SELECT bid FROM " . $DBPrefix . "bids WHERE auction = :auc_id ORDER BY bid DESC, id DESC LIMIT 1";
		$params = array();
		$params[] = array(':auc_id', $item['id'], 'int');
		$db->query($query, $params);
		if ($db->numrows() > 0)
		{
			$high_bid = $db->result('bid');
		}
	}
	// Retrieve counter
	$query = "SELECT counter FROM " . $DBPrefix . "auccounter WHERE auction_id = :auc_id";
	$params = array();
	$params[] = array(':auc_id', $item['id'], 'int');
	$db->query($query, $params);
	if ($db->numrows() > 0)
	{
		$viewcounter = $db->result('counter');
	}
	else
	{
		$viewcounter = 0;
	}

	$template->assign_block_vars('items', array(
			'BGCOLOUR' => (!($i % 2)) ? '' : 'class="alt-row"',
			'ID' => $item['id'],
			'TITLE' => $system->uncleanvars($item['title']),
			'STARTS' => FormatDate($item['starts']),
			'ENDS' => FormatDate($item['ends']),
			'BID' => $system->print_money($item['current_bid']),
			'BIDS' => $item['num_bids'],
			'RELIST' => $item['relist'],
			'RELISTED' => $item['relisted'],
			'COUNTER' => $viewcounter,

			'B_HASNOBIDS' => ($item['current_bid'] == 0)
			));
	$i++;
}
// get pagenation
$PREV = intval($PAGE - 1);
$NEXT = intval($PAGE + 1);
if ($PAGES > 1)
{
	$LOW = $PAGE - 5;
	if ($LOW <= 0) $LOW = 1;
	$COUNTER = $LOW;
	while ($COUNTER <= $PAGES && $COUNTER < ($PAGE + 6))
	{
		$template->assign_block_vars('pages', array(
				'PAGE' => ($PAGE == $COUNTER) ? '<b>' . $COUNTER . '</b>' : '<a href="' . $system->SETTINGS['siteurl'] . 'yourauctions.php?PAGE=' . $COUNTER . '"><u>' . $COUNTER . '</u></a>'
				));
		$COUNTER++;
	}
}

$template->assign_vars(array(
		'BGCOLOUR' => (!($i % 2)) ? '' : 'class="alt-row"',
		'ORDERCOL' => $_SESSION['oa_ord'],
		'ORDERNEXT' => $_SESSION['oa_nexttype'],
		'ORDERTYPEIMG' => $_SESSION['oa_type_img'],

		'PREV' => ($PAGES > 1 && $PAGE > 1) ? '<a href="' . $system->SETTINGS['siteurl'] . 'yourauctions.php?PAGE=' . $PREV . '"><u>' . $MSG['5119'] . '</u></a>&nbsp;&nbsp;' : '',
		'NEXT' => ($PAGE < $PAGES) ? '<a href="' . $system->SETTINGS['siteurl'] . 'yourauctions.php?PAGE=' . $NEXT . '"><u>' . $MSG['5120'] . '</u></a>' : '',
		'PAGE' => $PAGE,
		'PAGES' => $PAGES,

		'B_AREITEMS' => ($i > 0)
		));

include 'header.php';
$TMP_usmenutitle = $MSG['619'];
include $include_path . 'user_cp.php';
$template->set_filenames(array(
		'body' => 'yourauctions.tpl'
		));
$template->display('body');
include 'footer.php';
?>
