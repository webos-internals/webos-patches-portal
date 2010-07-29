<?php

/* COPYRIGHT 2009-2010 Daniel Beames and WebOS-Internals			*/
/* Redistribute only with explicit permission in writing from original author. 	*/

session_start();
header("Cache-control: private");

error_reporting(E_ALL & ~E_NOTICE);
define('IN_SCRIPT', true);

$rootpath = './';
include($rootpath . 'includes/core.php');
include($rootpath . 'includes/functions.php');

// We Need to Be Sure URL's don't have PHPSESSID in it...

$URL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 
//Check if PHP is not in safe mode,
//and PHPSESSID is passed via URL
if (!ini_get ('safe_mode') && preg_match ('/'.session_name().'=([^=&\s]*)/i', $URL)) {
	//Remove PHPSESSID junk and unneeded characters ("&" or "?") at end of URL
	$URL = preg_replace ( array ('`'.session_name().'=([^=&\s]*)`', '`(&|\?)+$`'), '', $URL);
	//Send Moved Permanently header
	@header ("HTTP/1.1 301 Moved Permanently");
	//Redirect to clean URL
	@header ("Location: " . trim ($URL));
	//End current script
	exit();
}

// remove html, tags, and trim all post data
$_POST   = PreClean($_POST);
$_GET    = PreClean($_GET);
$_COOKIE = PreClean($_COOKIE);

// ############################ MAIN OUTPUT #################################

function MainHeader() {
echo '<html>
	  <head>
	  <title>webOS-Patches Web Portal</title>
	  <link rel="stylesheet" href="styles.css" />
	  <link rel="shortcut icon" href="favicon.ico" />
	  <meta http-equiv="expires" content="0" />
	  <meta http-equiv="Pragma" content="no-cache" />
	  </head>
	  <body>
	  <table width="100%" border="'.iif($_GET['do']=="browse", "1", "0").'" cellpadding="5" cellspacing="0">
	  <tr>
		<td colspan="11" align="center" class="header">WebOS-Patches Web Portal<br/>
		<a href="/">Home</a> | <a href="?do=submit_new">Submit New Patch</a> | <a href="?do=submit_update">Submit Update</a> | '.iif($_GET['do']=="browse", "Browse Patches", '<a href="?do=browse">Browse Patches</a>').'</td>
	  </tr>';
}

function BrowsePatches($webosver, $category, $order, $desc) {
	global $DB, $webos_versions_array, $webos_versions_hide_array, $categories;
	echo '<tr>
			<td colspan="11" class="header2" align="center">';
	if($webosver && in_array($webosver, $webos_versions_hide_array)) {
		echo 'Ooops! An error has occurred.</td>
			</tr>';
		return;
	}
	foreach($webos_versions_array as $key=>$webos_version) {
		$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE versions LIKE '%".$webos_version."%'");
		if($count['num'] > '0') {
			if(!in_array($webos_version, $webos_versions_hide_array)) {
				$versions[] = $webos_version;
			}
		}
	}
	$i=0;
	foreach($versions as $key=>$version) {
		echo iif($i==0, "", " | ").iif($webosver==$version, '<font color="#0066cc">'.$version.'</font>', '<a href="?do=browse&webosver='.$version.'">'.$version.'</a>');
		$i++;
	}
	echo iif($i==0, "", " | ").iif($webosver=="all", '<font color="#0066cc">All</font>', '<a href="?do=browse&webosver=all">All</a>').'</td>
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
			echo iif($i==0, "", " | ").iif($category==$category1, '<font color="#0066cc">'.$category1.'</font>', '<a href="?do=browse&webosver='.$webosver.'&category='.$category1.'" class="header2">'.$category1.'</a>');
			$i++;
		}
		echo iif($i==0, "", " | ").iif($category==all, '<font color="#0066cc">All</font>', '<a href="?do=browse&webosver='.$webosver.'&category=all">All</a>').'</td>
			</tr>';
	}
	if($category) {
		$order = strtolower($order);
		if(!$order) {
			$order = 'category';
		}
		$image = "<img src=\"./images/s_asc.png\" alt=\"Ascending\" title=\"Ascending\" id=\"orderimage\" height=\"9\" width=\"11\" border=\"0\">";
		if($desc) {
			$dir = 'DESC';
			$imageurl = "onmouseover=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='./images/s_asc.png'; }\" onmouseout=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='./images/s_desc.png'; }\"";
			$image = "<img src=\"./images/s_desc.png\" alt=\"Descending\" title=\"Descending\" id=\"orderimage\" height=\"9\" width=\"11\" border=\"0\">";
		} else {
			$dir = 'ASC';
			$imageurl = "onmouseover=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='./images/s_desc.png'; }\" onmouseout=\"if(document.getElementById('orderimage')){ document.getElementById('orderimage').src='./images/s_asc.png'; }\"";
		}
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
		$numpatches = $DB->get_num_rows($getpatches);
		echo '<tr>
				<td colspan="11" class="header2" align="center">Displaying <b>'.$numpatches.'</b> Patches</td>
			</tr>
			<tr>
				<td width="64px">&nbsp;</td>
				<td width="175px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=title';
		if($order=="title" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="title", $imageurl, "").'>Title</a> '.iif($order=="title", $image, "").'</b></td>
				<td width="220px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=description';
		if($order=="description" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="description", $imageurl, "").'>Description</a> '.iif($order=="description", $image, "").'</b></td>
				<td width="80px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=category';
		if($order=="category" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="category", $imageurl, "").'>Category</a> '.iif($order=="category", $image, "").'</b></td>
				<td width="25px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=screenshot_1';
		if($order=="screenshot_1" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="screenshot_1", $imageurl, "").'>SS1</a> '.iif($order=="screenshot_1", $image, "").'</b></td>
				<td width="25px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=screenshot_2';
		if($order=="screenshot_2" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="screenshot_2", $imageurl, "").'>SS2</a> '.iif($order=="screenshot_2", $image, "").'</b></td>
				<td width="25px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=screenshot_3';
		if($order=="screenshot_3" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="screenshot_3", $imageurl, "").'>SS3</a> '.iif($order=="screenshot_3", $image, "").'</b></td>
				<td width="56px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=maintainer';
		if($order=="maintainer" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="maintainer", $imageurl, "").'>Maintainer</a> '.iif($order=="maintainer", $image, "").'</b></td>
				<td width="40px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=homepage';
		if($order=="homepage" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="homepage", $imageurl, "").'>Homepage</a> '.iif($order=="homepage", $image, "").'</b></td>
				<td width="40px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=versions';
		if($order=="versions" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="versions", $imageurl, "").'>Versions</a> '.iif($order=="versions", $image, "").'</b></td>
				<td width="100px" align="center"><b><a href="?do=browse&webosver='.$webosver.'&category='.$category.'&order=lastupdated';
		if($order=="lastupdated" && $desc!="1") {
			echo "&desc=1";
		}
		echo '" '.iif($order=="lastupdated", $imageurl, "").'>Updated</a> '.iif($order=="lastupdated", $image, "").'</b></td>
		  	</tr>';			
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
			$versions_out = NULL;
			$ver_count = 0;
			$get_webos_versions = explode(' ', $patch['versions']);
			foreach($get_webos_versions as $key=>$value) {
				if(!in_array(array_shift(explode('-',$value,2)), $webos_versions_hide_array)) {
					$versions_out .= iif($ver_count==0, '', ' ').$value;
					$ver_count++;
				}
			}
			$get_webos_versions = array();
			echo '<tr>
				<td><img src="'.$patch[icon].'"></img></td>
				<td>'.$patch[title].'</td>
				<td align="justify">'.$patch[description].'</td>
				<td align="center">'.$patch[category].'</td>
				<td align="center">'.iif(strlen($patch[screenshot_1])>=1, "<a href=\"".$patch[screenshot_1]."\" onclick=\"window.open(this.href, 'SS_OUT', 'width=340,height=500,fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,directories=no,location=no'); return false;\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_2])>=1, "<a href=\"".$patch[screenshot_2]."\" onclick=\"window.open(this.href, 'SS_OUT', 'width=340,height=500,fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,directories=no,location=no'); return false;\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_3])>=1, "<a href=\"".$patch[screenshot_3]."\" onclick=\"window.open(this.href, 'SS_OUT', 'width=340,height=500,fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,directories=no,location=no'); return false;\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.$maintainer_out.'</td>
				<td align="center">'.iif(strlen($patch[homepage])>=1, "<a href=\"".$patch[homepage]."\" target=\"homepage\">Link</a>", "&nbsp;").'</td>
				<td align="center">'.str_replace(" ", "<br/>", $versions_out).iif(strlen($patch[changelog])>=1, "<br/><br/><a href=\"?do=get_changelog&pid=".$patch[pid]."\" target=\"changelog\">Changelog</a>", "").'</td>
				<td align="center">'.gmdate("D, d M Y H:i:s \U\T\C", $patch[lastupdated]).'</td>
			</tr>';
			$maintainer_out = NULL;
		}
	}
}

function BuildForm($errors, $pid) {
	global $DB, $categories, $webos_versions_array;
	echo '		<form name="submitpatch" method="post" action="/?do=submit_'.iif(strlen($pid)>=1, "update", "new").'" enctype="multipart/form-data">
		<tr>
			<td colspan="2" align="center" class="header">Submit Patch for webOS-Patches</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="header2">(*) = Required</td>
		  	<input type="hidden" name="submitaction" value="1">'.iif(strlen($pid)>=1, "<input type=\"hidden\" name=\"pid\" value=\"".$pid."\">", "").'
		</tr>';
	if(strlen($pid) >= "1" && (!$errors)) {
		$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
		$get_webos_versions = explode(' ', $patch['versions']);
		foreach($get_webos_versions as $key=>$value) {
			$patch[webos_versions][] .= array_shift(explode('-',$value,2));
		}
	}
	if($errors) {
		$patch = $_POST;
		foreach($errors as $key => $value) {
			echo '		<tr>
			<td colspan="2" align="center" class="errors">'.$value.'<br/>
		</tr>';
		}
	}
	echo '		<tr>
			<td width="15%" class="'.iif($errors, "cell11", "cell3").'" valign="top">Title: (*)</td>
			<td width="85%" class="'.iif($errors, "cell12", "cell4").'"><input type="text" class="uploadpatch" name="title" value="'.FormatForForm($patch[title]).'" size="50" maxlength="45"'.iif(strlen($pid)>=1, "disabled><input type=\"hidden\" name=\"title\" value=\"".$patch[title]."\">", ">").'<br/>
			<b>Note:</b> Do not use category name, personalizations (your name, username,<br/>
			company name, tagline, etc), webOS Version. Be short and sweet. Limit 45<br/>
			characters. Numbers, Letters, apostrophes or spaces only.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Description: (*)</td>
			<td width="85%" class="cell4"><TEXTAREA class="uploadpatch" name="description" cols="50" rows="3">'.FormatForForm($patch[description]).'</TEXTAREA></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Patch File: (*)</td>
			<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="patch"></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Category: (*)</td>
			<td width="85%" class="cell4"><SELECT name="category" class="uploadpatch" '.iif(strlen($pid)>=1, "disabled", "").'>';
	foreach($categories as $key => $category) {
		echo '				<OPTION value="'.$category.'"';
		if($patch['category'] == $category) {
			echo ' SELECTED';
		}
		echo '>'.$category.'</OPTION>';
	}
	echo '			</SELECT>'.iif(strlen($pid)>=1, "<input type=\"hidden\" name=\"category\" value=\"".$patch[category]."\">", "").'</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 1:'.iif(strlen($patch[screenshot_1])>=1, "<br/><a href=\"".$patch[screenshot_1]."\">Current Image</a><input type=\"hidden\" name=\"screenshot_1\" value=\"".$patch[screenshot_1]."\">", "").'</td>
		  	<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="screenshot1"><br/>
			<b>Note:</b> Screenshots should be 320x480. They should not contain any other<br/>
			modifications in the picture as this can be misleading and cause confusion.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 2:'.iif(strlen($patch[screenshot_2])>=1, "<br/><a href=\"".$patch[screenshot_2]."\">Current Image</a><input type=\"hidden\" name=\"screenshot_2\" value=\"".$patch[screenshot_2]."\">", "").'</td>
			<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="screenshot2"><br/>
                        <b>Note:</b> Screenshots should be 320x480. They should not contain any other<br/>
                        modifications in the picture as this can be misleading and cause confusion.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 3:'.iif(strlen($patch[screenshot_3])>=1, "<br/><a href=\"".$patch[screenshot_3]."\">Current Image</a><input type=\"hidden\" name=\"screenshot_3\" value=\"".$patch[screenshot_3]."\">", "").'</td>
			<td width="85%" class="cell4"><input type="file" class="uploadpatch" name="screenshot3"><br/>
                        <b>Note:</b> Screenshots should be 320x480. They should not contain any other<br/>
                        modifications in the picture as this can be misleading and cause confusion.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Compatible webOS Version(s): (*)</font></td>
			<td width="85%" class="cell4">';
	foreach($webos_versions_array as $key => $webos_version) {
		echo '			<input type="checkbox" name="webos_versions[]" value="'.$webos_version.'"';
		if($patch[webos_versions]) {
			if(in_array($webos_version, $patch[webos_versions])) {
				echo 'CHECKED';
			}
		}
		echo '>&nbsp;&nbsp;'.$webos_version.'<br/>';
	}
	echo '			</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Maintainer: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="maintainer" value="'.FormatForForm($patch[maintainer]).'" size="50" maxlength="50"><br/>
			<b>Note:</b> Use your PreCentral.net or WebOS-Internals.org username if you do not<br/>
			want to give your real name. This information is published in the package\'s<br/>
			meta-data. It will be viewable by the public.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Email: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="email" value="'.FormatForForm($patch[email]).'" size="50" maxlength="128">&nbsp;&nbsp;<input type="checkbox" name="private" value="1"'.iif($patch["private"]==1, " CHECKED", "").'> Keep Private?<br/>
			<b>Note:</b> This information is published in the package\'s meta-data if the above box is not checked. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responsible for support. Giving your email makes it easier for a user to<br/>
			request support.<br/>
			<b>Important:</b>This is the email address that will be used by the admins to<br/>
			contact you when the patch is approved or denied, or if there are any issues.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Patch Homepage:</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="homepage" value="'.FormatForForm($patch[homepage]).'" size="50" maxlength="256"><br/>
			<b>Note:</b> This information is published in the package\'s meta-data. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responisble for support. Giving a URL to the patch\'s thread on PreCentral.net<br/>
			or wiki page on WebOS-Internals.org makes it easier for a user to request support.</td>
		</tr>';
	if($pid) {
		echo '<tr>
				<td width="15%" class="cell3" valign="top">Changelog:</td>
				<td width="85%" class="cell4"><textarea class="uploadpatch" name="changelog" cols="50" rows="3">'.FormatForForm($patch[changelog]).'</textarea><br/>
				<b>Note:</b> Recommended format is \'YYYY-MM-DD: What Was Done\'. Separate entries by newline.</td>
			</tr>';
	}
	echo '<tr>
			<td width="15%" class="cell3" valign="top">Note to Admins:</td>
			<td width="85%" class="cell4"><textarea class="uploadpatch" name="note_to_admins" cols="50" rows="3">'.FormatForForm($patch[note_to_admins]).'</textarea><br/>
			<b>Note:</b> This will not be published. It is simply a note to the Admins.</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="cell5"><input type="submit" value="Send it Off!">
			<hr class="bodysep"><center>By submitting this form, you, the submitter, explicitly agree you are either the original author of the patch or have the right to submit the patch under the <b>MIT Open Source
			License</b>. Further, you agree the patch will be licensed under the <b>MIT Open Source License</b>. This is to allow Palm to use the
			patch as-is to incorporate them into future webOS releases. Information regarding this license can be found at: 
			<a href="http://www.webos-internals.org/wiki/MIT_Open_Source_License_-_webOS-Patches" target="_blank">
			http://www.webos-internals.org/wiki/MIT_Open_Source_License_-_webOS-Patches</a></td>
		</tr>
		</form>';
}

function HandleForm($pid) {

	global $DB, $icon_array	;

	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	foreach($_FILES as $key => $value) {
		$$key = $value;
	}

	$known_image_types = array(
	                         'image/pjpeg' => 'jpg',
	                         'image/jpeg'  => 'jpg',
	                         'image/bmp'   => 'bmp',
	                         'image/x-png' => 'png',
				 'image/png'   => 'png'
	                       );
	$allowedpatchext = array(".patch");

   	$patchext = strrchr(strtolower($patch['name']),'.');

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

	if($patch['size']) {
		if(!in_array($patchext, $allowedpatchext)) {
			$errors[] = 'Only patch files are allowed.';
		} else {
			if($patch['size'] > 1048576) {
				$errors[] = 'Patch cannot be larger than 1MB.';
			}
		}
	} else {
		$errors[] = 'You must include a patch file.';
	}

	if(($screenshot1['size']) && !$known_image_types[$screenshot1['type']]) {
		  	$errors[] = 'You can only choose jpg, jpeg, bmp or png images for screenshot 1.';
  	}
	if(($screenshot2['size']) && !$known_image_types[$screenshot2['type']]) {
		  	$errors[] = 'You can only choose jpg, jpeg, bmp or png images for screenshot 2.';
  	}
	if(($screenshot3['size']) && !$known_image_types[$screenshot3['type']]) {
		  	$errors[] = 'You can only choose jpg, jpeg, bmp or png images for screenshot 3.';
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
	if(!$pid) {
		$dupcheck = $DB->query_first("SELECT pid FROM ".TABLE_PREFIX."patches WHERE category = '".$category."' AND title = '".$title."'");
		if($dupcheck['pid'] >= "1") {
			$errors[] = 'There is already a	 '.$category.': '.$title.' patch. If this is an update to that patch, please select "Submit Update" above. Otherwise, choose a new title.';
		}
	}

	if($errors) {
		BuildForm($errors, $_POST['pid']);
		return;
	}

	$ver_count=0;
	foreach ($webos_versions as $key => $webos_version) {
		if($ver_count==0) {
			$webos_versions_out = $webos_version;
		} else {
			$webos_versions_out .= ' '.$webos_version;
		}
		$ver_count++;
	}

	$icon = $icon_array[$category];

	$screenshots = '0';
	if($screenshot1['size']) {
		$screenshots++;
	}
	if($screenshot2['size']) {
		$screenshots++;
	}	
	if($screenshot3['size']) {
		$screenshots++;
	}
	if($screenshots > '0') {
		if($screenshot1['size']) {
			$screenshot_1_type = $screenshot1['type'];
			$screenshot_1_blob = file_get_contents($screenshot1['tmp_name'], FILE_BINARY);
		}
		if($screenshot2['size']) {
			$screenshot_2_type = $screenshot2['type'];
			$screenshot_2_blob = file_get_contents($screenshot2['tmp_name'], FILE_BINARY);
		}
		if($screenshot3['size']) {
			$screenshot_3_type = $screenshot3['type'];
			$screenshot_3_blob = file_get_contents($screenshot3['tmp_name'], FILE_BINARY);
		}
	}

	$description2 = mynl2br($description);
	$description2 = stripslashes(str_replace('"', "'", $description2));

	$changelog2 = mynl2br($changelog);
	$changelog2 = stripslashes(str_replace('"', "'", $changelog2));

	$patch_file_contents = file_get_contents($patch['tmp_name'], FILE_BINARY);
	
	$maintainer_array = explode(',', $maintainer);
	for($i=0; $i < count($maintainer_array); $i++) {
		$maintainer_array2[] = trim($maintainer_array[$i]);
	}
	$maintainer = implode(',', $maintainer_array2);

	if($pid) {
		$cur = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
		$extra  = ", update_pid, versions, meta_sub_version, depends, screenshot_1, screenshot_2, screenshot_3";
		$extra2 = ", '".$pid."', '".$cur[versions]."', '".$cur[meta_sub_version]."', '".$cur[depends]."', '".$cur[screenshot_1]."', '".$cur[screenshot_2]."', '".$cur[screenshot_3]."'";
		$update = "1";
	}

	$DB->query("INSERT INTO ".TABLE_PREFIX."patches (pid, title, description, patch_file, category, screenshot_1_blob, 
				screenshot_1_type, screenshot_2_blob, screenshot_2_type, screenshot_3_blob, screenshot_3_type, icon, 
				webos_versions, maintainer, email, private, homepage, changelog, note_to_admins, status, datesubmitted".$extra.")
				VALUES ('',
						'".mysql_real_escape_string($title)."',
						'".mysql_real_escape_string($description2)."',
						'".mysql_real_escape_string($patch_file_contents)."',
						'$category',
						'".mysql_real_escape_string($screenshot_1_blob)."',
						'$screenshot_1_type',
						'".mysql_real_escape_string($screenshot_2_blob)."',
						'$screenshot_2_type',
						'".mysql_real_escape_string($screenshot_3_blob)."',
						'$screenshot_3_type',
						'$icon',
						'$webos_versions_out',
						'".mysql_real_escape_string($maintainer)."',
						'$email',
						'$private',
						'$homepage',
						'".mysql_real_escape_string($changelog2)."',
						'".mysql_real_escape_string($note_to_admins)."',
						'0',
						'".time()."'".$extra2.")
				");
	$pid = $DB->insert_id(); 

	//Let the user know the outcome
	echo '			<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td colspan="2" align="center" class="header">Submit Patch for webOS-Patches - Results</td>
			</tr>
			<tr>
				<td colspan="2" class="header2" align="center">Thank you for submitting a patch! Look for it very soon in the webOS-Patches feed in Preware (and other programs that use the webOS-Patches feed)!<br/>
<br/>You should receive an email at the email address you provided confirming the successful submitting of this patch. You will also receive an email when it is approved or denied. Please add "support@webos-internals.org" to your safe sender list.<br/>
<br/>Please note you will not be able to submit another patch for 5 minutes. Do not use your browser\'s back button and try to submit again, it will not let you. Instead, <a href="webospatchupload.php">click here</a> and refresh that page. It will let you know when you can submit again.<br/>
<br/>If there are any questions, please contact <a href="mailto:support@webos-internals.org">support@webos-internals.org</a>.</td>
			</tr>';
	SendEmail(iif($update=="1", "submit_update", "submit_new"), $pid);
	PreventSpam(GetRemoteAddress());
}

function SpamWait() {
	echo '			<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td colspan="2" align="center" class="header">Submit Patch to webOS-Patches Admins</td>
			</tr>
			<tr>
				<td colspan="2" align="center" class="header2">You have submitted a patch within the past 5 minutes. Please try again later.</td>
			</tr>
			</table>';

}

function MainFooter() {
	echo '	  <tr>
		<td colspan="11" align="center" class="copyright">&copy; 2009 - 2010 Daniel Beames (dBsooner) and WebOS Internals<br/><a href="http://www.webos-internals.org/wiki/WebOS_Internals:Site_support" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0"></img></a><br/>Donations help offset hosting costs and fund future development.</center></td>
	  </tr>
	  </table>
	  </body>
	  </html>';
}

// LET'S BUILD THE PAGE!

switch($_GET['do']) {
	case 'browse':
		MainHeader();
		BrowsePatches($_GET['webosver'], $_GET['category'], $_GET['order'], $_GET['desc']);
		MainFooter();
		break;
	case 'get_changelog':
		MainHeader();
		GetChangelog($_GET['pid']);
		MainFooter();
		break;
	case 'submit_new':
		MainHeader();
		if(!SpamCheck(GetRemoteAddress())) {
			switch($_POST['submitaction']) {
				case '1':
					HandleForm('');
					break;
				default:
					BuildForm('', '');
			}
		} else {
			SpamWait();
		}
		MainFooter();
		break;
	case 'submit_update':
		MainHeader();
		if(!$_POST['pid']) {
			global $DB;
			echo '<tr>
					<td colspan=2" class="header" align="center"><b>Select Patch to Update</b></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><form name="submit_update" method="post" action="/?do=submit_update"><select name="pid">';
			$getpatches = $DB->query("SELECT pid,title,category FROM ".TABLE_PREFIX."patches WHERE status = '1'");
			while($patch = $DB->fetch_array($getpatches)) {
				$output_array[$patch[pid]] = '<b>'.$patch[category].':</b> '.$patch[title];
			}
			asort($output_array);
			foreach($output_array as $pid=>$output) {
				echo '<OPTION value='.$pid.'>'.$output.'</OPTION>';
			}
			echo '</SELECT><br/><br/><input type="submit" value="Next">
					</td>
				</tr>
				</form>';
		} else {
			if(!SpamCheck(GetRemoteAddress())) {
				switch($_POST['submitaction']) {
					case '1':
						HandleForm($_POST['pid']);
						break;
					default:
						BuildForm('', $_POST['pid']);
				}
			} else {
				SpamWait();
			}
		}	
		MainFooter();
		break;
	default:
		MainHeader();
		echo '<tr>
				<td colspan="2" class="cell">Welcome to the new WebOS-Patches Web Portal! The goal of this portal is to make it easier for developers to submit their patches for inclusion in the WebOS-Patches feed as well as provide a way for everyone to browse the patches in the WebOS-Patches feed from their browsers.<br/>
				<br/>
				To browse the patches, click <a href="?do=browse">Browse Patches</a> above. You will be able to browse by WebOS Version and by Category. You will also be able to sort each list by all table values. This should help you find what you are looking for faster. All links in the tables open to separate tabs/windows, so please ensure your popup blocker allows this.<br/>
				<br/>
				Developers: If you are submitting an update to an existing patch (one that is available in the feed already), please click the <a href="?do=submit_update">Submit Update</a> link above. You will be presented with a drop down list of all patches in the feed. Select the one you are updating and click next. This will bring you to the submission form with most values already filled in.<br/>
				If you are submitting a new patch, please click <a href="?do=submit_new">Submit New Patch</a> above. Fill out the form with as much detail as possible.<br/>
				<br/>
				<a href="http://www.webos-internals.org/wiki/WebOS_Internals:Site_support">Donations</a> are greatly appreciated and go towards operating costs of the servers we maintain as well as further development for WebOS.<br/>
				<br/>
				Thank you for visiting the WebOS-Patches Web Portal!<br/>
				<br/>
				--Daniel Beames (dBsooner) and <a href="http://www.webos-internals.org/">WebOS Internals</a></td>
			</tr>';
		MainFooter();
}
?>

