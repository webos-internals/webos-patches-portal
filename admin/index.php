<?php

/* COPYRIGHT 2009-2010 Daniel Beames and WebOS-Internals			*/
/* Redistribute only with explicit permission in writing from original author.	*/

session_start();
header("Cache-control: private");

error_reporting(E_ALL & ~E_NOTICE);
define('IN_SCRIPT', true);

$rootpath = '../';
include($rootpath . 'includes/core.php');
include($rootpath . 'includes/functions.php');

// We Need to Be Sure URL's don't have PHPSESSID in it...

$URL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 
//Check if PHP is not in safe mode,
//and PHPSESSID is passed via URL
if (!ini_get ('safe_mode') && preg_match ('/'.session_name().'=([^=&\s]*)/i', $URL))
{
   //Remove PHPSESSID junk and unneeded characters ("&" or "?") at end of URL
   $URL = preg_replace ( array ('`'.session_name().'=([^=&\s]*)`', '`(&|\?)+$`'), '', $URL);
   //Send Moved Permanently header
   @ header ("HTTP/1.1 301 Moved Permanently");
   //Redirect to clean URL
   @ header ("Location: " . trim ($URL));
   //End current script
   exit();
}

// remove html, tags, and trim all post data
$_POST   = PreClean($_POST);
$_GET    = PreClean($_GET);
$_COOKIE = PreClean($_COOKIE);

// ############################ MAIN OUTPUT #################################
function MainHeader() {
	global $DB;
	$do = $_GET['do'];
	$new_count = $DB->query_first("SELECT COUNT(pid) AS num_new FROM ".TABLE_PREFIX."patches WHERE status = '0' AND update_pid IS NULL");
	$update_count = $DB->query_first("SELECT COUNT(pid) AS num_update FROM ".TABLE_PREFIX."patches WHERE status = '0' AND update_pid IS NOT NULL");
echo '<html>
	  <head>
	  <title>dBsooner\'s webOS-Patches Admin Area</title>
	  <link rel="stylesheet" href="http://webos-patches.dbsooner.com/styles.css" />
	  <link rel="shortcut icon" href="http://webos-patches.dbsooner.com/favicon.ico" />
	  <meta http-equiv="expires" content="0" />
	  <meta http-equiv="Pragma" content="no-cache" />
	  </head>
	  <body>
	  <table width="100%" border="1" cellpadding="5" cellspacing="0">
	  <tr>
		<td colspan="11" align="center" class="header">WebsOS-Patches Admin<br/>
		<a href="/admin/">Home</a> | ';
	if($do != "new") {
		echo '<a href="?do=new">'.iif($new_count[num_new]>=1, "<b>", "").'New Submissions ('.$new_count[num_new].')'.iif($new_count[num_new]>=1, "</b>", "").'</a>';
	} else {
		echo 'New Submissions ('.$new_count[num_new].')';
	}
	if($do != "updates") {
		echo ' | <a href="?do=updates">'.iif($update_count[num_update]>=1, "<b>", "").'Update Submissions ('.$update_count[num_update].')'.iif($update_count[num_update]>=1, "</b>", "").'</a>';
	} else {
		echo ' | Update Submissions ('.$update_count[num_update].')';
	}
echo ' | '.iif($do=="browse", "Browse Patches", '<a href="?do=browse">Browse Patches</a>').' | <a href="?do=git&cmd=status">Git Status</a> | <a href="?do=git&cmd=add">Add All</a> | <a href="?do=git&cmd=commit">Commit All</a> | <a href="?do=git&cmd=push_mods">Push Mods</a> | <a href="?do=git&cmd=tag">Tag Mods</a> | <a href="?do=git&cmd=push_build">Push Build</a></td>
	  </tr>';
}

function BrowsePatches($do, $webosver, $category, $order, $desc) {
	global $DB, $webos_versions_array, $categories;
	echo '<tr>
			<td colspan="11" class="header2" align="center">';
	if($do == "browse") {
		foreach($webos_versions_array as $key=>$webos_version) {
			$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE versions LIKE '%".$webos_version."%'");
			if($count['num'] > '0') {
				$versions[] = $webos_version;
			}
		}
		$i=0;
		foreach($versions as $key=>$version) {
			echo iif($i==0, "", " | ").iif($webosver==$version, $version, '<a href="?do=browse&webosver='.$version.'">'.$version.'</a>');
			$i++;
		}
		echo iif($i==0, "", " | ").iif($webosver=="all", 'All', '<a href="?do=browse&webosver=all">All</a>').'</td>
			</tr>';
		if($webosver) {
			echo '<tr>
					<td colspan="11" class="header2" align="center">';
			foreach($categories as $key=>$category1) {
				if($webosver == "all") {
					$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE category = '".$category1."'");
				} else {
					$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE category = '".$category1."' AND versions LIKE '%".$webosver."%'");
				}
				if($count['num'] > '0') {
					$category_list[] = $category1;
				}
			}
			$i=0;
			foreach($category_list as $key=>$category1) {
				echo iif($i==0, "", " | ").iif($category==$category1, $category1, '<a href="?do=browse&webosver='.$webosver.'&category='.$category1.'">'.$category1.'</a>');
				$i++;
			}
			echo iif($i==0, "", " | ").iif($category==all, 'All', '<a href="?do=browse&webosver='.$webosver.'&category=all">All</a>').'</td>
				</tr>';
		}
	}
	if($category || $do != "browse") {
		$order = strtolower($order);
		if($do != "browse") {
			$verorder = 'webos_versions';
			$ssorder1 = 'screenshot_1_type';
			$ssorder2 = 'screenshot_2_type';
			$ssorder3 = 'screenshot_3_type';
		} else {
			$verorder = 'versions';
			$ssorder1 = 'screenshot_1';
			$ssorder2 = 'screenshot_2';
			$ssorder3 = 'screenshot_3';
		}
		if($order == "versions" || $order == "webos_versions") {
			$verorder2 = "1";
		}
		if($order == "screenshot_1" || $order == "screenshot_1_type") {
			$ssorder_1 = "1";
		}
		if($order == "screenshot_2" || $order == "screenshot_2_type") {
			$ssorder_2 = "1";
		}
		if($order == "screenshot_3" || $order == "screenshot_3_type") {
			$ssorder_3 = "1";
		}
		if(!$order) {
			$order = 'category';
		}
		$image = "<img src=\"../images/s_asc.png\" alt=\"Ascending\" title=\"Ascending\" id=\"orderimage\" height=\"9\" width=\"11\" border=\"0\">";
		if($desc) {
			$dir = 'DESC';
			$imageurl = "onmouseover=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='../images/s_asc.png'; }\" onmouseout=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='../images/s_desc.png'; }\"";
			$image = "<img src=\"../images/s_desc.png\" alt=\"Descending\" title=\"Descending\" id=\"orderimage\" height=\"9\" width=\"11\" border=\"0\">";
		} else {
			$dir = 'ASC';
			$imageurl = "onmouseover=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='../images/s_desc.png'; }\" onmouseout=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='../images/s_asc.png'; }\"";
		}
		echo '<tr>
				<td width="64px">&nbsp;</td>
				<td width="175px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order=title';
		if($order=="title" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="title", $imageurl, "").'>Title</a> '.iif($order=="title", $image, "").'</b></td>
				<td width="275px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order=description';
		if($order=="description" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="description", $imageurl, "").'>Description</a> '.iif($order=="description", $image, "").'</b></td>';
		if($do == "new" || $do == "updates") {
			echo '<td width="30px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order=patch_file';
			if($order=="patch_file" && $desc!="1") {
				echo "&desc=1";
			}
			echo '" '.iif($order=="patch_file", $imageurl, "").'>Patch</a> '.iif($order=="patch_file", $image, "").'</b></td>';
		}
		echo '	<td width="100px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order=category';
		if($order=="category" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="category", $imageurl, "").'>Category</a> '.iif($order=="category", $image, "").'</b></td>
				<td width="30px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order='.$ssorder1;
		if(($order=="screenshot_1" || $order=="screenshot_1_type") && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($ssorder_1=="1", $imageurl, "").'>SS1</a> '.iif($ssorder_1=="1", $image, "").'</b></td>
				<td width="30px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order='.$ssorder2;
		if(($order=="screenshot_2" || $order=="screenshot_2_type") && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($ssorder_2=="1", $imageurl, "").'>SS2</a> '.iif($ssorder_2=="1", $image, "").'</b></td>
				<td width="30px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order='.$ssorder3;
		if(($order=="screenshot_3" || $order=="screenshot_3_type") && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($ssorder_3=="1", $imageurl, "").'>SS3</a> '.iif($ssorder_3=="1", $image, "").'</b></td>
				<td width="75px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order=maintainer';
		if($order=="maintainer" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="maintainer", $imageurl, "").'>Maintainer</a> '.iif($order=="maintainer", $image, "").'</b></td>
				<td width="50px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order=homepage';
		if($order=="homepage" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="homepage", $imageurl, "").'>Homepage</a> '.iif($order=="homepage", $image, "").'</b></td>
				<td width="50px" align="center"><b><a href="?do='.$do.'&webosver='.$webosver.'&category='.$category.'&order='.$verorder;
		if(($order=="versions" || $order=="webos_versions") && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($verorder2=="1", $imageurl, "").'>Versions</a> '.iif($verorder2=="1", $image, "").'</b></td>
		  	</tr>';
		switch($do) {
			case "browse":
				if($webosver == 'all') {
					$webosver_query = '';
				} else {
					$webosver_query = $webosver;
				}
				if($category == 'all') {
					$category_query = '';
				} else {
					$category_query = " AND category = '".$category."'";
				}
				$getpatches = $DB->query("SELECT * FROM ".TABLE_PREFIX."patches WHERE status = '1' AND versions LIKE '%".$webosver_query."%'".$category_query." ORDER BY ".$order." ".$dir.", title ASC");
				break;
			case "updates":
				$getpatches = $DB->query("SELECT * FROM ".TABLE_PREFIX."patches WHERE status = '0' AND update_pid IS NOT NULL ORDER BY ".$order." ".$dir.", title ASC");
				break;
			case "new":
				$getpatches = $DB->query("SELECT * FROM ".TABLE_PREFIX."patches WHERE status = '0' AND update_pid IS NULL ORDER BY ".$order." ".$dir.", title ASC");
				break;
		}
		while($patch = $DB->fetch_array($getpatches)) {
			$maintainer_array = explode(',', $patch['maintainer']);
			$num_maintainers = count($maintainer_array);
			for($i=0; $i < $num_maintainers; $i++) {
				if($patch['email'] && ($patch['private'] != '1') && ($i=="0")) {
					$maintainer_out .= '<a href="mailto://'.$patch[email].'">'.ltrim($maintainer_array[$i]).'</a><br/>';
				} else {
					$maintainer_out .= ltrim($maintainer_array[$i]).'<br/>';
				}
			}
			$maintainer_array = array();
			$num_maintainers = 0;
			echo '<tr>
				<td><img src="'.$patch[icon].'"></img></td>
				<td><a href="?do=build_form&pid='.$patch[pid].'">'.$patch[title].'</a></td>
				<td align="justify">'.$patch[description].'</td>';
			if($do != "browse") {
				echo '<td align="center"><a href="?do=get_patch&pid='.$patch[pid].'">Show</a><br/><br/><a href="?do=testpatch&pid='.$patch[pid].'">Test</a></td>';
			}
			echo '<td align="center">'.$patch[category].'</td>';
			if($do == "browse") {
				echo '<td align="center">'.iif(strlen($patch[screenshot_1])>=1, "<a href=\"".$patch[screenshot_1]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_2])>=1, "<a href=\"".$patch[screenshot_2]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_3])>=1, "<a href=\"".$patch[screenshot_3]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>';
			} else {
				echo '<td>'.iif(strlen($patch[screenshot_1_type])>=1, "<a href=\"?do=get_image&ss=1&pid=$patch[pid]\">Show</a>", "&nbsp;").'</td>
				<td>'.iif(strlen($patch[screenshot_2_type])>=1, "<a href=\"?do=get_image&ss=2&pid=$patch[pid]\">Show</a>", "&nbsp;").'</td>
				<td>'.iif(strlen($patch[screenshot_3_type])>=1, "<a href=\"?do=get_image&ss=3&pid=$patch[pid]\">Show</a>", "&nbsp;").'</td>';
			}
			echo '<td align="center">'.$maintainer_out.'</td>
				<td align="center">'.iif(strlen($patch[homepage])>=1, "<a href=$patch[homepage]>Link</a>", "None").'</td>';
			if ($do == "browse") {
				echo '<td>'.str_replace(" ", "<br/>", $patch[versions]).iif(strlen($patch[changelog])>=1, "<br/><br/><a href=\"?do=get_changelog&pid=".$patch[pid]."\" target=\"changelog\">Changelog</a>", "").'</td>';
			} else {
				echo '<td>'.str_replace(" ", "<br/>", $patch[webos_versions]).'</td>';
			}
			echo '</tr>';
			$maintainer_out = NULL;
		}
	}
}

function BuildForm($errors, $pid) {
	global $DB, $categories, $webos_versions_array;
	$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");

	if(($pid >= "1") && (!$errors) && ($patch['status'] != "0")) {
		$get_webos_versions = explode(' ', $patch['versions']);
		foreach($get_webos_versions as $key=>$value) {
			$patch[webos_versions][] .= array_shift(explode('-',$value,2));
		}
	} else if($errors) {
		$patch = $_POST;
		foreach($_POST as $key => $value) {
			$$key = $value;
		}
	} else {
		$patch[webos_versions] = explode(' ', $patch['webos_versions']);
	}
	if($patch['update_pid'] >= '1') {
		$original = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$patch[update_pid]."'");
		$updateform = '1';
	}
	echo '		<form name="submitpatch" method="post" action="/admin/?do=submitform&pid='.$patch[pid].'" enctype="multipart/form-data">
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td colspan="2" align="center" class="header">Submit Patch to webOS-Patches Admins</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="header2">(*) = Required</td>
		  	<input type="hidden" name="submitaction" value="1">
			<input type="hidden" name="pid" value="'.$patch[pid].'">
			'.iif($updateform==1, '<input type="hidden" name="update_pid" value="'.$patch[update_pid].'">', '').'
		</tr>';
	if($errors) {
		foreach($errors as $key => $value) {
			echo '		<tr>
			<td colspan="2" align="center" class="errors">'.$value.'<br/>
		</tr>';
		}
	}
	echo '		<tr>
			<td width="15%" class="'.iif($errors, "cell11", "cell3").'" valign="top">Title: (*)</td>
			<td width="85%" class="'.iif($errors, "cell12", "cell4").'"><input type="text" class="uploadpatch" name="title" value="'.FormatForForm($patch[title]).'" size="50" maxlength="40"'.
			iif($updateform==1, ' DISABLED><input type="hidden" name="title" value="'.$patch[title].'"><br/><b>Original:</b> '.$original[title], '>').'<br/>
			<b>Note:</b> Do not use category name, personalizations (your name, username,<br/>
			company name, tagline, etc), webOS Version. Be short and sweet. Limit 40<br/>
			characters. Numbers, Letters, apostrophes or spaces only.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Description: (*)</td>
			<td width="85%" class="cell4"><TEXTAREA class="uploadpatch" name="description" cols="50" rows="3">'.FormatForForm($patch[description]).'</TEXTAREA></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Patch File: (*)</td>
			<td width="85%" class="cell4"><a href="?do=get_patch&pid='.$patch[pid].'">View Patch</a></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Dependants:</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="depends" value="'.FormatForForm($patch[depends]).'" size="50" maxlength="512"><br/>
			'.iif($updateform==1, "<b>Original:</b> ".$original[depends]."<br/>", "").'<b>Note:</b> Enter the packageid of all packages this patch is dependant on. Seperate packages with a comma. Example: org.webosinternals.patches.univseral-search-command-line</td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Category: (*)</td>
			<td width="85%" class="cell4"><SELECT name="category" class="uploadpatch"'.iif($updateform==1, "DISABLED>", ">");
	foreach($categories as $key => $category1) {
		echo '				<OPTION value="'.$category1.'"';
		if($patch[category] == $category1) {
			echo ' SELECTED';
		}
		echo '>'.$category1.'</OPTION>';
	}
	echo '			</SELECT>'.iif($updateform==1, '<input type="hidden" name="category" value="'.$patch[category].'"><br/>
		<b>Original:</b> '.$original[category], '').'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 1:'.iif(strlen($patch[screenshot_1_type])>=1, "<br/><a href=\"?do=get_image&ss=1&pid=$patch[pid]\">NEW SS</a>", "").'</td>
		  	<td width="85%" class="cell4">'.iif(strlen($patch[screenshot_1_type])>=1, '<input type="checkbox" name="screenshot1" value="1" id="screenshot1" CHECKED><label for="screenshot1">Save the New SS 1 to the Wiki?</label>', 'No Screenshot 1 attached.').'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 2:'.iif(strlen($patch[screenshot_2_type])>=1, "<br/><a href=\"?do=get_image&ss=2&pid=$patch[pid]\">NEW SS</a>", "").'</td>
		  	<td width="85%" class="cell4">'.iif(strlen($patch[screenshot_2_type])>=1, '<input type="checkbox" name="screenshot2" value="1" id="screenshot2" CHECKED><label for="screenshot2">Save the New SS 2 to the Wiki?</label>', 'No Screenshot 2 attached.').'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 3:'.iif(strlen($patch[screenshot_3_type])>=1, "<br/><a href=\"?do=get_image&ss=3&pid=$patch[pid]\">NEW SS</a>", "").'</td>
		  	<td width="85%" class="cell4">'.iif(strlen($patch[screenshot_3_type])>=1, '<input type="checkbox" name="screenshot3" value="1" id="screenshot3" CHECKED><label for="screenshot1">Save the New SS 3 to the Wiki?</label>', 'No Screenshot 3 attached.').'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Compatible webOS Version(s): (*)</font></td>
			<td width="85%" class="cell4">';
	foreach($webos_versions_array as $key => $webos_version) {
		echo '			<input type="checkbox" name="webos_versions[]" value="'.$webos_version.'"';
		if(in_array($webos_version, $patch['webos_versions'])) {
			echo 'CHECKED';
		}
		echo '>&nbsp;&nbsp;'.$webos_version.'<br/>';
	}
	echo '	'.iif($updateform==1, '<b>Original:</b> '.$original[versions], '').'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Maintainer: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="maintainer" value="'.FormatForForm($patch[maintainer]).'" size="50" maxlength="50"><br/>
			'.iif($updateform==1, '<b>Original:</b> '.$original[maintainer].'<br/>', '').'
			<b>Note:</b> Use your PreCentral.net or webOS-Internals.org username if you do not<br/>
			want to give your real name. This information is published in the package\'s<br/>
			meta-data. It will be viewable by the public.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Email: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="email" value="'.FormatForForm($patch[email]).'" size="50" maxlength="128">&nbsp;&nbsp;<input type="checkbox" name="private" value="1"'.iif($patch['private']==1, " CHECKED", "").'> Keep Private?<br/>';
	if($updateform == '1') {
		echo '<b>Original:</b> '.iif(strlen($original[email])>=1, $original[email], 'None').' - '.iif($original['private']==1, 'Private<br/>', 'Not Private<br/>');
	}
	echo '	<b>Note:</b> This information is published in the package\'s meta-data if the above box is not checked. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responsible for support. Giving your email makes it easier for a user to<br/>
			request support.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Patch Homepage:</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="homepage" value="'.FormatForForm($patch[homepage]).'" size="50" maxlength="256"><br/>
			'.iif($updateform==1, '<b>Original:</b> '.$original[homepage].'<br/>', '').'
			<b>Note:</b> This information is published in the package\'s meta-data. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responisble for support. Giving a URL to the patch\'s thread on PreCentral.net<br/>
			or wiki page on webOS-Internals.org makes it easier for a user to request support.</td>
		</tr>';
	if($patch['update_pid'] != NULL) {
		echo '<tr>
				<td width="15%" class="cell3" valign="top">Changelog:</td>
				<td width="85%" class="cell4"><textarea class="uploadpatch" name="changelog" cols="50" rows="3">'.FormatForForm($patch[changelog]).'</textarea><br/>
				<b>Note:</b> Recommended format is \'YYYY-MM-DD: What Was Done\'. Separate entries by newline.</td>
			</tr>';
	}
	echo '<tr>
			<td width="15%" class="cell3" valign="top">Note to Admins:</td>
			<td width="85%" class="cell4"><textarea class="uploadpatch" name="note_to_admins" cols="50" rows="3" disabled>'.FormatForForm($patch[note_to_admins]).'</textarea><br/>
			<b>Note:</b> This will not be published. It is simply a note to the Admins.</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="cell5"><input type="submit" value="Approve It!"></td>
		</tr>
		</table>
		</form>';

}

function HandleForm($pid) {
	global $DB, $icon_array;

	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	
	if(strlen($title) < '1') {
		$errors[] = 'You must enter a title.';
	} else {
		if(preg_replace('/^[0-9A-Za-z- ]*$/', '', $title) != "") {
			$errors[] = 'Title can only contain letters, numbers and -\'s.';
		}
	}

	if(strlen($description) < '10') {
		$errors[] = 'Please be more descriptive.';
	}
	if($category == 'Select...') {
		$errors[] = 'You must select a category.';
	}
	if(!$webos_versions) {
		$errors[] = 'You must select at least one webOS Version.';
	}
	if(!$maintainer) {
		$errors[] = 'You must enter the maintainer.';
	}
	if($email) {
		if(!validateEmail($email)) {
			$errors[] = 'Email address is invalid.';
		}
	} else {
		$errors[] = 'You must supply your email address.';
	}
	if($homepage) {
		if(!validateUrlSyntax($homepage, '')) {
			$errors[] = 'Patch homepage is invalid.';
		}
	}

	if($errors) {
		MainHeader();
		BuildForm($errors, $_POST['pid']);
		return;
	}

	if($update_pid) {
		$updateform = '1';
		$original = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$update_pid."'");
	}

	$icon = $icon_array[$category];

	if($screenshot1 == "1") {
		$screenshot1 = UploadImage($pid, "1", $title);
	} else {
		$screenshot1 = NULL;
	}
	if($screenshot2 == "1") {
		$screenshot2 = UploadImage($pid, "2", $title);
	} else {
		$screenshot2 = NULL;
	}
	if($screenshot1 == "1") {
		$screenshot3 = UploadImage($pid, "3", $title);
	} else {
		$screenshot3 = NULL;
	}
	$description2 = mynl2br($description);
	$description2 = stripslashes(str_replace('"', "'", $description2));

	$changelog2 = mynl2br($changelog);
	$changelog2 = stripslashes(str_replace('"', "'", $changelog2));

	if(strlen($depends) >= '1') {
		$depends_array = explode(',', $depends);
		for($i=0; $i < count($depends_array); $i++) {
			$depends_array2[] = trim($depends_array[$i]);
		}
		$depends = implode(',', $depends_array2);
	}
	
	$maintainer_array = explode(',', $maintainer);
	for($i=0; $i < count($maintainer_array); $i++) {
		$maintainer_array2[] = trim($maintainer_array[$i]);
	}
	$maintainer = implode(',', $maintainer_array2);

	foreach($webos_versions as $key => $webos_version) {
		$get_sub_version = array();
		exec('cd ../../git/modifications/v'.$webos_version.' ; /usr/bin/git tag | grep '.$webos_version.' | cut -d- -f2', $get_sub_version);
		$sub_version = max($get_sub_version);
		$sub_version++;
		$new_versions[] = $webos_version.'-'.$sub_version;
	}

	if($updateform == '1') {
		$versions_array = explode(' ', $original['versions']);
		foreach($versions_array as $key=>$version) {
			$main_ver .= array_shift(explode('-',$version,2));
			if(!in_array($main_ver, $webos_versions)) {
				$keep_versions[] = $version;
			}
			unset($main_ver);
		}
	}
	foreach($new_versions as $key=>$version) {
		$keep_versions[] = $version;
	}
	sort($keep_versions);
	$versions2 = implode(' ', $keep_versions);
	
	$get_patch_file = $DB->query_first("SELECT patch_file FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$patch_file_contents = $get_patch_file['patch_file'];
	
	// GOING TO NEED TO UPDATE 'UPDATE_PID' AND DELETE PID(?)
	
	$DB->query("UPDATE ".TABLE_PREFIX."patches SET title = '".mysql_real_escape_string($title)."',
											description = '".mysql_real_escape_string($description2)."',
											patch_file = NULL,
											category = '$category',
											depends = '$depends',
											screenshot_1_blob = NULL,
											screenshot_1_type = NULL,
											screenshot_2_blob = NULL,
											screenshot_2_type = NULL,
											screenshot_3_blob = NULL,
											screenshot_3_type = NULL,
											screenshot_1 = '$screenshot1',
											screenshot_2 = '$screenshot2',
											screenshot_3 = '$screenshot3',
											icon = '$icon',
											webos_versions = NULL,
											maintainer = '".mysql_real_escape_string($maintainer)."',
											email = '$email',
											private = '$private',
											homepage = '$homepage',
											status = '1',
											versions = '$versions2',
											note_to_admins = '".mysql_real_escape_string($note_to_admins)."',
											changelog = '".mysql_real_escape_string($changelog2)."',
											".iif($updateform==1, "lastupdated", "dateaccepted")." = '".time()."'
									WHERE pid = '".iif($updateform==1, $update_pid, $pid)."'"); 
	foreach($new_versions as $key=>$version) {
		$gitversions = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'gitversions'");
		$gitversions_array = explode(',',$gitversions['value']);
		if(!in_array($version, $gitversions_array)) {
			$gitversions_array[] = $version;
		}
		$gitversions_out = implode(',', $gitversions_array);
		$DB->query("UPDATE ".TABLE_PREFIX."settings SET value = '".$gitversions_out."' WHERE setting = 'gitversions'");
	}

	if($updateform == '1') {
		$DB->query("DELETE FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."' LIMIT 1");
		$pid = $update_pid;
	}

	$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$title2 = strtolower($patch['title']);
	$title2 = str_replace(" ", "-", $title2);
	$title2 = str_replace("_", "-", $title2);
	$title2 = str_replace("/", "-", $title2);
	$title2 = str_replace("\\", "-", $title2);
	$category2 = strtolower(str_replace(" ", "-", $patch['category']));
	$patchname = $category2.'-'.$title2;
	$versions = 'VERSIONS = '.$versions2;
	foreach($webos_versions as $key => $webos_version) {
		file_put_contents('../../git/modifications/v'.$webos_version.'/'.$category2.'/'.$patchname.'.patch', $patch_file_contents);
	}

	$ssout=NULL;
	$sscount=0;
	if(strlen($screenshot1) >= 1) {
		$ssout .= '\"'.$screenshot1.'\"';
		$sscount++;
	}
	if(strlen($screenshot2) >= 1) {
		$ssout .= iif(strlen($ssout)>=1, ",\\\n", "").'\"'.$screenshot2.'\"';
		$sscount++;
	}	
	if(strlen($screenshot3) >= 1) {
		$ssout .= iif(strlen($ssout)>=1, ",\\\n", "").'\"'.$screenshot3.'\"';
		$sscount++;
	}
	if($sscount >= 2) {
		$screenshots2 = "SCREENSHOTS = [\\\n".$ssout." ]";
	} else if($sscount == '1') {
		$screenshots2 = "SCREENSHOTS = [ ".$ssout." ]";
	} else {
		$screenshots2 = "SCREENSHOTS =";
	}
	$maintainer_array = explode(',', $patch['maintainer']);
	$num_maintainers = count($maintainer_array);
	for($i=0; $i < $num_maintainers; $i++) {
		if($patch['email'] && ($patch['private'] != '1') && ($i=="0")) {
			$maintainer_out .= ' '.trim($maintainer_array[$i]).' <'.$patch[email].'>';
		} else {
			$maintainer_out .= iif($i>=1, ', ', ' ').trim($maintainer_array[$i]);
		}
	}
	if(strlen($homepage) > '0') {
		$homepage2 = ' '.$homepage;
	} else {
		$homepage2 = '';
	}

	$makefile_content = "NAME = \$(shell basename \$(shell pwd))
PATCH = $category2/\${NAME}.patch
TITLE = $patch[title]
DESCRIPTION = $description2
CATEGORY = $patch[category]
$versions
ICON = $icon
$screenshots2
META_SUB_VERSION = 1

include ../common.mk
".iif(strlen($patch[depends])>=1, "DEPENDS := \${DEPENDS}, $patch[depends]", "")."
include ../modifications.mk

MAINTAINER =$maintainer_out
HOMEPAGE =$homepage2";

	if(!is_dir($dir = '../../git/build/autopatch/'.$patchname.'/')) {
		mkdir($dir);
	}
	file_put_contents('../../git/build/autopatch/'.$patchname.'/Makefile', $makefile_content);
	
	//Let the user know the outcome
	 return '
			<tr>
				<td colspan="2" align="center" class="header">Patch Accepted!</td>
			</tr>
			<tr>
				<td colspan="2" class="header2" align="center">The patch has been accepted! Thank you!</td>
			</tr>
			</table>'; 
}

function GitExec($cmd) {
	global $DB, $webos_versions_array;
	$fetch_gitversions = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'gitversions'");
	$gitversions = explode(',', $fetch_gitversions[value]);
	foreach($gitversions as $key=>$gitversion) {
		$gitversions_main[array_shift(explode('-',$gitversion,2))] = substr(strstr($gitversion, '-'), 1);
	}
	switch($cmd) {
		case 'status':
			$output = '<b>Preware/build.git:</b><br/><pre>';
			$output .= "cd ../../git/build/ ; /usr/bin/git status 2>&1\n";
			$output .= `cd ../../git/build/ ; /usr/bin/git status 2>&1`;
			$output .= '</pre>';
			foreach($webos_versions_array as $key=>$version) {
				$output .= '<hr><b>modifications.git/webos-'.$version.':</b><pre>';
				$output .= "cd ../../git/modifications/v$version ; /usr/bin/git status 2>&1\n";
				$output .= `cd ../../git/modifications/v$version ; /usr/bin/git status 2>&1`;
				$output .= '</pre>';
			}
			break;
		case 'add':
			$output = '<b>Preware/build.git:</b><pre>';
			$output .= "cd ../../git/build/autopatch ; /usr/bin/git add . 2>&1\n";
			$output .= `cd ../../git/build/autopatch ; /usr/bin/git add . 2>&1`;
			$output .= "</pre>";
			foreach($webos_versions_array as $key=>$version) {
				$output .= '<hr><b>modifications.git/webos-'.$version.':</b>';
				if(!$gitversions_main[$version]) {
					$output .= ' No Add Necessary.';
				} else {
					$output .= "<pre>cd ../../git/modifications/v$version ; /usr/bin/git all . 2>&1\n";
					$output .= `cd ../../git/modifications/v$version ; /usr/bin/git add . 2>&1`;
					$output .= "</pre>";
				}
			}
			break;
		case 'commit':
			if($_POST['submitaction'] == '1') {
				$output = '<b>Preware/build.git:</b> ';
				$output .= "<pre>cd ../../git/build/autopatch ; /usr/bin/git commit -m \"".$_POST['commit_message']."\" 2>&1\n";
				$output .= `cd ../../git/build/autopatch ; /usr/bin/git commit -m "$_POST[commit_message]" 2>&1`;
				$output .= "</pre>";
				foreach($webos_versions_array as $key=>$version) {
					$output .= '<hr><b>modifications.git/webos-'.$version.':</b>';
					if(!$gitversions_main[$version]) {
						$output .= ' No Commit Necessary.';
					} else {
						$output .= "<pre>cd ../../git/modifications/v$version ; /usr/bin/git commit -m \"$_POST[commit_message]\" 2>&1\n";
						$output .= `cd ../../git/modifications/v$version ; /usr/bin/git commit -m "$_POST[commit_message]" 2>&1`;
						$output .= "</pre>";
					}
				}
			} else {
				echo '<form method="post" name="commitform" action="?do=git&cmd=commit">
					<tr>
						<td width="20%">Commit Message:</td>
						<td width="80%"><input type="text" name="commit_message" size="50" maxlength="512"></td>
					</tr>
					<tr>
						<td colspan="2"><input type="hidden" name="submitaction" value="1"><input type="submit" value="Commit"></td>
					</tr>';
			}
			break;
		case 'push_mods':
			$loop=0;
			$output= NULL;
			foreach($webos_versions_array as $key=>$version) {
				$output .= iif($loop==0, '', '<hr>').'<b>modifications.git/webos-'.$version.':</b>';
				if(!$gitversions_main[$version]) {
					$output .= ' No Push Necessary.';
				} else {
					$output .= "<pre>cd ../../git/modifications/v$version ; /usr/bin/git pull 2>&1\n";
					$output .= `cd ../../git/modifications/v$version ; /usr/bin/git pull 2>&1`;
					$output .= "<pre>cd ../../git/modifications/v$version ; /usr/bin/git push 2>&1\n";
					$output .= `cd ../../git/modifications/v$version ; /usr/bin/git push 2>&1`;
					$output .= "</pre>";
					unset($gitversions_main[$version]);
				}
				$loop++;
			}
			$count=0;
			foreach($gitversions_main as $key=>$value) {
				if(strlen($value)>=1) {
					if($count==0) {
						$gitversions_return = $key.'-'.$value;
					} else {
						$gitversions_return = ','.$key.'-'.$value;
					}
					$count++;
				}
			}
			$DB->query("UPDATE ".TABLE_PREFIX."settings SET value = '".$gitversions_return."' WHERE setting = 'gitversions'");
			break;
		case 'tag':
			$loop=0;
			$output=NULL;
			foreach($webos_versions_array as $key=>$version) {
				$output .= iif($loop==0, '', '<hr>').'<b>modifications.git/webos-'.$version.':</b>';
				if(!$gitversions_main[$version]) {
					$output .= ' No Tag Necessary.';
				} else {
					$output .= "<pre>cd ../../git/modifications/v$version ; /usr/bin/git tag v$version-$gitversions_main[$version] 2>&1\n";
					$output .= `cd ../../git/modifications/v$version ; /usr/bin/git tag v$version-$gitversions_main[$version] 2>&1`;
					$output .= "\ncd ../../git/modifications/v$version ; /usr/bin/git push --tags 2>&1\n";
					$output .= `cd ../../git/modifications/v$version ; /usr/bin/git push --tags 2>&1`;
					$output .= '</pre>';
				}
				$loop++;
			}
			break;
		case 'push_build':
			$output = '<b>Preware/build.git:</b><pre>';
			$output .= "cd ../../git/build/autopatch ; /usr/bin/git pull 2>&1\n";
			$output .= `cd ../../git/build/autopatch ; /usr/bin/git pull 2>&1`;
			$output .= "cd ../../git/build/autopatch ; /usr/bin/git push 2>&1\n";
			$output .= `cd ../../git/build/autopatch ; /usr/bin/git push 2>&1`;
			$output .= '</pre>';
			break;
	}
	echo '<tr>
			<td>'.$output.'</td>
		</tr>';
}

function TestPatch($pid) {
	global $DB, $webos_versions_array;
	$patch = $DB->query_first("SELECT patch_file,webos_versions FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$versions = explode(' ', $patch['webos_versions']);
	system('rm -f /tmp/tmp/patch');
	file_put_contents('/tmp/tmp.patch', $patch['patch_file']);
	foreach($versions as $key=>$version) {
		if(in_array($version, $webos_versions_array)) {
			echo "<tr>
					<td>Testing ".$version.":<br/><pre>/usr/bin/patch -p1 --dry-run -d ../../git/StockWebOS/v".$version."/ < /tmp/tmp.patch 2<&1\n";
			echo `/usr/bin/patch -p1 --dry-run -d ../../git/StockWebOS/v$version/ < /tmp/tmp.patch 2<&1`;
			echo "</pre></td>
				</tr>";
		}
	}
	system('rm -f /tmp/tmp.patch');
}

function MainFooter() {
	echo '	  <tr>
		<td colspan="11" width="100%" align="center" class="copyright"><center>&copy; 2009 - 2010 Daniel Beames (dBsooner) and webOS-Internals Group</center></td>
	  </tr>
	  </table>
	  </body>
	  </html>';
}

// LET'S BUILD THE PAGE!

switch($do) {
	case 'new':
		MainHeader();
		BrowsePatches($_GET['do'], 'all', 'all', $_GET['order'], $_GET['desc']);
		MainFooter();
		break;
	case 'updates':
		MainHeader();
		BrowsePatches($_GET['do'], 'all', 'all', $_GET['order'], $_GET['desc']);
		MainFooter();
		break;
	case 'browse':
		MainHeader();
		BrowsePatches($_GET['do'], $_GET['webosver'], $_GET['category'], $_GET['order'], $_GET['desc']);
		MainFooter();
		break;
	case 'get_patch':
		GetPatch($_GET['pid']);
		break;
	case 'get_image':
		GetImage($_GET['pid'], $_GET['ss']);
		break;
	case 'get_changelog':
		MainHeader();
		GetChangelog($_GET['pid']);
		MainFooter();
		break;
	case 'build_form':
		MainHeader();
		BuildForm('', $_GET['pid']);
		MainFooter();
		break;
	case 'submitform':
		$output = HandleForm($_GET['pid']);
		if($output) {
			MainHeader();
			echo $output;
		}
		MainFooter();
		break;
	case 'git':
		MainHeader();
		GitExec($_GET['cmd']);
		MainFooter();
		break;
	case 'testpatch':
		MainHeader();
		TestPatch($_GET['pid']);
		MainFooter();
		break;
	default:
		MainHeader();
		MainFooter();
}
?>
