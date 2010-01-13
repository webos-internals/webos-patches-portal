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
	  <link rel="stylesheet" href="http://www.dbsooner.com/webospatchuploadstyles.css" />
	  <meta http-equiv="expires" content="0" />
	  <meta http-equiv="Pragma" content="no-cache" />
	  </head>
	  <body>
	  <table width="100%" border="1" cellpadding="5" cellspacing="0">
	  <tr>
		<td colspan="10" align="center" class="header">WebsOS-Patches Admin<br/>
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
echo ' | '.iif($do=="list", "List Patches", '<a href="?do=list">List Patches</a>').'</td>
	  </tr>';
}

function DisplayUpdatesNew($do) {
	global $DB;
	
	echo '<tr>
			<td width="175px" align="center"><b>Title</b></td>
			<td width="275px" align="center"><b>Description</b></td>
			<td width="30px" align="center"><b>Patch</b></td>
			<td width="100px" align="center"><b>Category</b></td>
			<td width="30px" align="center"><b>SS1</b></td>
			<td width="30px" align="center"><b>SS2</b></td>
			<td width="30px" align="center"><b>SS3</b></td>
			<td width="50px" align="center"><b>WebOS Versions</b></td>
			<td width="75px" align="center"><b>Maintainer</b></td>
			<td width="50px" align="center"><b>Homepage</b></td>
		  </tr>';

	$getnew = $DB->query("SELECT * FROM ".TABLE_PREFIX."patches WHERE status = '0' AND update_pid ".iif($do=="new", "IS", "IS NOT")." NULL");
	while($patch = $DB->fetch_array($getnew)) {
		echo '<tr>
			<td><a href="?do=build_form&pid='.$patch[pid].'">'.$patch[title].'</a></td>
			<td>'.$patch[description].'</td>
			<td><a href="?do=get_patch&pid='.$patch[pid].'">Show</a></td>
			<td>'.$patch[category].'</td>
			<td>'.iif(strlen($patch[screenshot_1_type])>=1, "<a href=\"?do=get_image&ss=1&pid=$patch[pid]\">Show</a>", "&nbsp;").'</td>
			<td>'.iif(strlen($patch[screenshot_2_type])>=1, "<a href=\"?do=get_image&ss=2&pid=$patch[pid]\">Show</a>", "&nbsp;").'</td>
			<td>'.iif(strlen($patch[screenshot_3_type])>=1, "<a href=\"?do=get_image&ss=3&pid=$patch[pid]\">Show</a>", "&nbsp;").'</td>
			<td>'.str_replace(" ", "<br/>", $patch[webos_versions]).'</td>
			<td>'.iif(strlen($patch[email])>=1, "<a href=mailto://$patch[email]>", "").$patch[maintainer].iif(strlen($patch[email])>=1, "</a>", "").'</td>
			<td>'.iif(strlen($patch[homepage])>=1, "<a href=$patch[homepage]>Link</a>", "None").'</td>
		</tr>';
	}
}

function ListPatches($webosver, $category) {
	global $DB, $webos_versions_array, $categories;
	echo '<tr>
			<td colspan="10" class="header2" align="center">';
	foreach($webos_versions_array as $key=>$webos_version) {
		$count = $DB->query_first("SELECT count(pid) as num FROM ".TABLE_PREFIX."patches WHERE versions LIKE '%".$webos_version."%'");
		if($count['num'] > '0') {
			$versions[] = $webos_version;
		}
	}
	$i=0;
	foreach($versions as $key=>$version) {
		echo iif($i==0, "", " | ").iif($webosver==$version, $version, '<a href="?do=list&webosver='.$version.'">'.$version.'</a>');
		$i++;
	}
	echo iif($i==0, "", " | ").iif($webosver=="all", 'All', '<a href="?do=list&webosver=all">All</a>').'</td>
		</tr>';
	if($webosver) {
		echo '<tr>
				<td colspan="10" class="header2" align="center">';
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
			echo iif($i==0, "", " | ").iif($category==$category1, $category1, '<a href="?do=list&webosver='.$webosver.'&category='.$category1.'">'.$category1.'</a>');
			$i++;
		}
		echo iif($i==0, "", " | ").iif($category==all, 'All', '<a href="?do=list&webosver='.$webosver.'&category=all">All</a>').'</td>
			</tr>';
	}
	if($category) {
		echo '<tr>
				<td width="64px">&nbsp;</td>
				<td width="175px" align="center"><b>Title</b></td>
				<td width="275px" align="center"><b>Description</b></td>
				<td width="100px" align="center"><b>Category</b></td>
				<td width="30px" align="center"><b>SS1</b></td>
				<td width="30px" align="center"><b>SS2</b></td>
				<td width="30px" align="center"><b>SS3</b></td>
				<td width="75px" align="center"><b>Maintainer</b></td>
				<td width="50px" align="center"><b>Homepage</b></td>
				<td width="50px" align="center"><b>Versions</b></td>
		  	</tr>';
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
		$getpatches = $DB->query("SELECT * FROM ".TABLE_PREFIX."patches WHERE status = '1' AND versions LIKE '%".$webosver_query."%'".$category_query);
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
				<td>'.$patch[title].'</td>
				<td align="justify">'.$patch[description].'</td>
				<td align="center">'.$patch[category].'</td>
				<td align="center">'.iif(strlen($patch[screenshot_1])>=1, "<a href=\"".$patch[screenshot_1]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_2])>=1, "<a href=\"".$patch[screenshot_2]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.iif(strlen($patch[screenshot_3])>=1, "<a href=\"".$patch[screenshot_3]."\" target=\"SS_OUT\">Show</a>", "&nbsp;").'</td>
				<td align="center">'.$maintainer_out.'</td>
				<td align="center">'.iif(strlen($patch[homepage])>=1, "<a href=$patch[homepage]>Link</a>", "None").'</td>
				<td>'.str_replace(" ", "<br/>", $patch[versions]).iif(strlen($patch[changelog])>=1, "<br/><br/><a href=\"?do=get_changelog&pid=".$patch[pid]."\" target=\"changelog\">Changelog</a>", "").'</td>
			</tr>';
			$maintainer_out = NULL;
		}
	}
}

function BuildForm($errors, $pid) {
	global $DB, $categories, $webos_versions_array;

	$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");

	if($errors) {
		foreach($_POST as $key => $value) {
			$$key = $value;
		}
	} else {
		$title = $patch['title'];
		$description = $patch['description'];
		$category = str_replace(" ", "-", $patch['category']);
		$webos_versions = explode(" ", $patch['webos_versions']);
		$maintainer = $patch['maintainer'];
		$email = $patch['email'];
		$private = $patch['private'];
		$homepage = $patch['homepage'];
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
		</tr>';
	if($errors) {
		foreach($errors as $key => $value) {
			echo '		<tr>
			<td colspan="2" align="center" class="errors">'.$value.'<br/>
		</tr>';
		}
	}
	echo '		<tr>
			<td width="15%" class="'.iif($errors, "cell11", "cell3").'">Title: (*)</td>
			<td width="85%" class="'.iif($errors, "cell12", "cell4").'"><input type="text" class="uploadpatch" name="title" value="'.FormatForForm($title).'" size="50" maxlength="40"><br/>
			<b>Note:</b> Do not use category name, personalizations (your name, username,<br/>
			company name, tagline, etc), webOS Version. Be short and sweet. Limit 40<br/>
			characters. Numbers, Letters, apostrophes or spaces only.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Description: (*)</td>
			<td width="85%" class="cell4"><TEXTAREA class="uploadpatch" name="description" cols="50" rows="3">'.FormatForForm($description).'</TEXTAREA></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Patch File: (*)</td>
			<td width="85%" class="cell4"><a href="?do=get_patch&pid='.$patch[pid].'">View Patch</a></td>
		</tr>
		<tr>
			<td width="15%" class="cell3">Category: (*)</td>
			<td width="85%" class="cell4"><SELECT name="category" class="uploadpatch">';
	foreach($categories as $key => $category1) {
		echo '				<OPTION value="'.$category1.'"';
		if($category == $category1) {
			echo ' SELECTED';
		}
		echo '>'.$category1.'</OPTION>';
	}
	echo '			</SELECT></td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 1:</td>
		  	<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="screenshot1" size="50"><br/>
			<b>Note:</b> Direct address of image file on WebOS-Internals Wiki.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 2:</td>
		  	<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="screenshot2" size="50"><br/>
			<b>Note:</b> Direct address of image file on WebOS-Internals Wiki.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Screenshot 3:</td>
		  	<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="screenshot3" size="50"><br/>
			<b>Note:</b> Direct address of image file on WebOS-Internals Wiki.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Compatible webOS Version(s): (*)</font></td>
			<td width="85%" class="cell4">';
	foreach($webos_versions_array as $key => $webos_version) {
		echo '			<input type="checkbox" name="webos_versions[]" value="'.$webos_version.'"';
		if(in_array($webos_version, $webos_versions)) {
			echo 'CHECKED';
		}
		echo '>&nbsp;&nbsp;'.$webos_version.'<br/>';
	}
	echo '			</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Maintainer: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="maintainer" value="'.FormatForForm($maintainer).'" size="50" maxlength="50"><br/>
			<b>Note:</b> Use your PreCentral.net or webOS-Internals.org username if you do not<br/>
			want to give your real name. This information is published in the package\'s<br/>
			meta-data. It will be viewable by the public.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Email: (*)</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="email" value="'.FormatForForm($email).'" size="50" maxlength="128">&nbsp;&nbsp;<input type="checkbox" name="private" value="1"'.iif($private==1, " CHECKED", "").'> Keep Private?<br/>
			<b>Note:</b> This information is published in the package\'s meta-data if the above box is not checked. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responsible for support. Giving your email makes it easier for a user to<br/>
			request support.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Patch Homepage:</td>
			<td width="85%" class="cell4"><input type="text" class="uploadpatch" name="homepage" value="'.FormatForForm($homepage).'" size="50" maxlength="256"><br/>
			<b>Note:</b> This information is published in the package\'s meta-data. It will be<br/>
			viewable by the public. Remember you, as the developer of the patch, are<br/>
			responisble for support. Giving a URL to the patch\'s thread on PreCentral.net<br/>
			or wiki page on webOS-Internals.org makes it easier for a user to request support.</td>
		</tr>
		<tr>
			<td width="15%" class="cell3" valign="top">Note to Admins:</td>
			<td width="85%" class="cell4"><textarea class="uploadpatch" name="notes_to_admin" cols="50" rows="3" disabled>'.FormatForForm($notes_to_admin).'</textarea><br/>
			<b>Note:</b> This will not be published. It is simply a note to the Admins.</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="cell5"><input type="submit" value="Approve It!"></td>
		</tr>
		</table>
		</form>';

}

function SubmitForm() {
	global $DB, $icon_array;

	foreach($_POST as $key => $value) {
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
		BuildForm($errors, '');
		return;
	}

	$description2 = str_replace("\r\n", "<br/> ", $patch[description]);
	$description2 = str_replace("\n", "<br/> ", $description2);
	$description2 = stripslashes(str_replace('"', "'", $description2));
	$icon = $icon_array[$category];

	unset($versions2);
	$ver_count=0;
	foreach($webos_versions as $key => $webos_version) {
		exec('cd ../../git/modifications/v'.$webos_version.' ; /usr/bin/git tag | grep '.$webos_version.' | cut -d- -f2', $get_sub_version);
		$sub_version = max($get_sub_version);
		$sub_version++;
		if($ver_count==0) {
			$versions2 = $webos_version.'-'.$sub_version;
		} else {
			$versions2 .= ' '.$webos_version.'-'.$sub_version;
		}
		$ver_count++;
		unset($sub_version);
		unset($get_sub_version);
	}
	
	// CHECK CATEGORY STR_REPLACE - Probably doesn't need to replace '-' with ' '.
	
	$DB->query("UPDATE ".TABLE_PREFIX."patches SET title = '".mysql_real_escape_string($title)."',
											description = '".mysql_real_escape_string($description2)."',
											patch_file = NULL,
											category = '".str_replace('-', ' ', $category)."', 
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
											versions = '$versions2'
									WHERE pid = '".$pid."'");

	$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$title2 = strtolower($patch['title']);
	$title2 = str_replace(" ", "-", $title2);
	$title2 = str_replace("_", "-", $title2);
	$title2 = str_replace("/", "-", $title2);
	$title2 = str_replace("\\", "-", $title2);
	$category2 = strtolower(str_replace(" ", "-", $patch['category']));
	$patchname = $category2.'-'.$title2;
	$versions = 'VERSIONS =';
	foreach($webos_versions as $key => $webos_version) {
		file_put_contents('../../git/modifications/v'.$webos_version.'/'.$category2.'/'.$patchname.'.patch', $patch['patch_file']);
	}

	$screenshots2 = 'SCREENSHOTS = [ ';
	if(strlen($screenshot1) >= 1) {
		$screenshots2 .= '\"'.$screenshot1.'\", ';
	}
	if(strlen($screenshot2) >= 1) {
		$screenshots2 .= '\"'.$screenshot2.'\", ';
	}	
	if(strlen($screenshot3) >= 1) {
		$screenshots2 .= '\"'.$screenshot3.'\" ';
	}
	$screenshots2 .= ']';
	if((strlen($patch['email']) > '0') && ($patch['private'] != '1')) {
		$maintainer2 = ' '.$patch[maintainer].' <'.$patch[email].'>';
	} else {
		$maintainer2 = ' '.$patch[maintainer];
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
include ../modifications.mk

MAINTAINER =$maintainer2
HOMEPAGE =$homepage2";

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

function MainFooter() {
	echo '	  <tr>
		<td colspan="10" width="100%" align="center" class="copyright"><center>&copy; 2009 - 2010 Daniel Beames (dBsooner) and webOS-Internals Group</center></td>
	  </tr>
	  </table>
	  </body>
	  </html>';
}

// LET'S BUILD THE PAGE!

switch($do) {
	case 'new':
		MainHeader();
		DisplayUpdatesNew($_GET['do']);
		MainFooter();
		break;
	case 'updates':
		MainHeader();
		DisplayUpdatesNew($_GET['do']);
		MainFooter();
		break;
	case 'list':
		MainHeader();
		ListPatches($_GET['webosver'], $_GET['category']);
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
		$output = SubmitForm($_GET['pid']);
		if($output) {
			MainHeader();
			echo $output;
		}
		MainFooter();
		break;
	default:
		MainHeader();
		MainFooter();
}
?>
