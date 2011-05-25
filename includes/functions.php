<?php

/* COPYRIGHT 2009-2010 Daniel Beames and WebOS-Internals                        */
/* Redistribute only with explicit permission in writing from original author.  */

if(!defined('IN_SCRIPT')) {
  die("Hacking attempt!!");
}

global $rootpath;

$get_webos_versions_array = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'webos_versions_array'");
$webos_versions_array = split(",", $get_webos_versions_array['value']);
$get_webos_versions_hide_array = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'webos_versions_hide_array'");
$webos_versions_hide_array = split(",", $get_webos_versions_hide_array['value']);

// GLOBAL VARIABLES
$categories = array(	"Select...",
			"App Catalog",
			"App Launcher",
			"Browser",
			"Calculator",
			"Calendar",
			"Camera",
			"Clock",
			"Contacts",
			"Dangerous",
			"Device Info",
			"Email",
			"Google Maps",
			"Memos",
			"Messaging",
			"Misc",
			"Mojo",
			"Music Player",
			"Navigation",
			"Notifications",
			"Pandora",
			"PDF Viewer",
			"Phone",
			"Photos",
			"Screen Lock",
			"Sounds and Alerts",
			"SprintNav",
			"System",
			"Tasks",
			"Top Bar",
			"Universal Search",
			"Video Player",
			"YouTube",
			"Other"
		);

$icon_array = array(	"App Catalog"		=>"http://www.webos-internals.org/images/0/03/Icon_WebOSInternals_Patches_Findapps.png",
			"App Launcher"		=>"http://www.webos-internals.org/images/b/b1/Icon_WebOSInternals_Patches_Applauncher.png",
			"Browser"		=>"http://www.webos-internals.org/images/4/4a/Icon_WebOSInternals_Patches_Browser.png",
			"Calculator"		=>"http://www.webos-internals.org/images/2/20/Icon_WebOSInternals_Patches_Calculator.png",
			"Calendar"		=>"http://www.webos-internals.org/images/d/d4/Icon_WebOSInternals_Patches_Calendar.png",
			"Camera"		=>"http://www.webos-internals.org/images/c/c5/Icon_WebOSInternals_Patches_Camera.png",
			"Clock"			=>"http://www.webos-internals.org/images/8/8d/Icon_WebOSInternals_Patches_Clock.png",
			"Contacts"		=>"http://www.webos-internals.org/images/c/ca/Icon_WebOSInternals_Patches_Contacts.png",
			"Dangerous"		=>"http://www.webos-internals.org/images/c/c6/Icon_Patch_Dangerous.png",
			"Device Info"		=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Email"			=>"http://www.webos-internals.org/images/2/29/Icon_WebOSInternals_Patches_Email.png",
			"Google Maps"		=>"http://www.webos-internals.org/images/c/c3/Icon_WebOSInternals_Patches_SprintNav.png",
			"Memos"			=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Messaging"		=>"http://www.webos-internals.org/images/2/24/Icon_WebOSInternals_Patches_Messaging.png",
			"Misc"			=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Mojo"			=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Music Player"		=>"http://www.webos-internals.org/images/d/df/Icon_WebOSInternals_Patches_Musicplayer.png",
			"Navigation"		=>"http://www.webos-internals.org/images/c/c3/Icon_WebOSInternals_Patches_SprintNav.png",
			"Notifications"		=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Pandora"		=>"http://www.webos-internals.org/images/d/d1/Icon_WebOSInternals_Patches_Pandora.png",
			"PDF Viewer"		=>"http://www.webos-internals.org/images/7/71/Icon_WebOSInternals_Patches_Pdfviewer.png",
			"Phone"			=>"http://www.webos-internals.org/images/2/2c/Icon_WebOSInternals_Patches_Phone.png",
			"Photos"		=>"http://www.webos-internals.org/images/7/7d/Icon_WebOSInternals_Patches_Photos.png",
			"Screen Lock"		=>"http://www.webos-internals.org/images/f/fa/Icon_WebOSInternals_Patches_Screenlock.png",
			"Sounds and Alerts"	=>"http://www.webos-internals.org/images/f/ff/Icon_WebOSInternals_Patches_Soundsandalerts.png",
			"SprintNav"		=>"http://www.webos-internals.org/images/c/c3/Icon_WebOSInternals_Patches_SprintNav.png",
			"System"		=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Tasks"			=>"http://www.webos-internals.org/images/8/83/Icon_WebOSInternals_Patches_Tasks.png",
			"Top Bar"		=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Universal Search"	=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png",
			"Video Player"		=>"http://www.webos-internals.org/images/f/ff/Icon_WebOSInternals_Patches_Videoplayer.png",
			"YouTube"		=>"http://www.webos-internals.org/images/8/8b/Icon_WebOSInternals_Patches_Youtube.png",
			"Other"			=>"http://www.webos-internals.org/images/f/f9/Icon_WebOSInternals_Patch.png"
		);

$tweaks_icon_array = array(
		   	"App Catalog"		=>"http://www.webos-internals.org/images/4/45/Icon_WebOSInternals_Patches_Plus_Findapps.png",
			"App Launcher"		=>"http://www.webos-internals.org/images/b/b4/Icon_WebOSInternals_Patches_Plus_Applauncher.png",
			"Browser"		=>"http://www.webos-internals.org/images/3/37/Icon_WebOSInternals_Patches_Plus_Browser.png",
			"Calculator"		=>"http://www.webos-internals.org/images/5/5e/Icon_WebOSInternals_Patches_Plus_Calculator.png",
			"Camera"		=>"http://www.webos-internals.org/images/2/2f/Icon_WebOSInternals_Patches_Plus_Camera.png",
			"Clock"			=>"http://www.webos-internals.org/images/9/9a/Icon_WebOSInternals_Patches_Plus_Clock.png",
			"Contacts"		=>"http://www.webos-internals.org/images/a/a5/Icon_WebOSInternals_Patches_Plus_Calendar.png",
			"Dangerous"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Device Info"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Email"			=>"http://www.webos-internals.org/images/e/ee/Icon_WebOSInternals_Patches_Plus_Email.png",
			"Google Maps"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Memos"			=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Messaging"		=>"http://www.webos-internals.org/images/0/04/Icon_WebOSInternals_Patches_Plus_Messaging.png",
			"Misc"			=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Mojo"			=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Music Player"		=>"http://www.webos-internals.org/images/8/86/Icon_WebOSInternals_Patches_Plus_Musicplayer.png",
			"Navigation"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Notifications"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Pandora"		=>"http://www.webos-internals.org/images/8/86/Icon_WebOSInternals_Patches_Plus_Musicplayer.png",
			"PDF Viewer"		=>"http://www.webos-internals.org/images/0/0c/Icon_WebOSInternals_Patches_Plus_Pdfviewer.png",
			"Phone"			=>"http://www.webos-internals.org/images/1/1b/Icon_WebOSInternals_Patches_Plus_Phone.png",
			"Photos"		=>"http://www.webos-internals.org/images/1/1c/Icon_WebOSInternals_Patches_Plus_Photos.png",
			"Screen Lock"		=>"http://www.webos-internals.org/images/b/be/Icon_WebOSInternals_Patches_Plus_Screenlock.png",
			"Sounds and Alerts"	=>"http://www.webos-internals.org/images/8/80/Icon_WebOSInternals_Patches_Plus_Soundalerts.png",
			"SprintNav"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"System"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Tasks"			=>"http://www.webos-internals.org/images/4/4f/Icon_WebOSInternals_Patches_Plus_Tasks.png",
			"Top Bar"		=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Universal Search"	=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png",
			"Video Player"		=>"http://www.webos-internals.org/images/5/5b/Icon_WebOSInternals_Patches_Plus_Videoplayer.png",
			"YouTube"		=>"http://www.webos-internals.org/images/8/82/Icon_WebOSInternals_Patches_Plus_Youtube.png",
			"Other"			=>"http://www.webos-internals.org/images/f/f1/Icon_WebOSInternals_Patch_Plus.png"
		);

function GetPatch($pid, $dl) {
	global $DB;
	$getpatch = $DB->query_first("SELECT title,category,patch_file FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$title = strtolower($getpatch['title']);
	$title = str_replace(" ", "-", $title);
	$title = str_replace("_", "-", $title);
	$title = str_replace("/", "-", $title);
	$title = str_replace("\\", "-", $title);
	$category = strtolower($getpatch['category']);
	$category = str_replace(" ", "-", $category);
	$name = $category.'-'.$title.'.patch';
	if($dl == "1") {
		header('Content-type: text/x-diff');
		header('Content-Disposition: attachment; filename="'.$name.'"');
	} else {
		header('Content-type: text/plain');
	}
	echo $getpatch['patch_file'];
}

function GetTweaks($pid, $dl) {
	global $DB;
	$getpatch = $DB->query_first("SELECT title,category,tweaks_file FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$title = strtolower($getpatch['title']);
	$title = str_replace(" ", "-", $title);
	$title = str_replace("_", "-", $title);
	$title = str_replace("/", "-", $title);
	$title = str_replace("\\", "-", $title);
	$category = strtolower($getpatch['category']);
	$category = str_replace(" ", "-", $category);
	$name = $category.'-'.$title.'.json';
	if($dl == "1") {
		header('Content-type: application/json');
		header('Content-Disposition: attachment; filename="'.$name.'"');
	} else {
		header('Content-type: text/plain');
	}
	echo $getpatch['tweaks_file'];
}

function GetImage($pid, $ss, $src) {
	global $DB;

	switch($ss) {
		case '1':
			$sstype = 'screenshot_1_type';
			$ssblob = 'screenshot_1_blob';
			break;
		case '2':
			$sstype = 'screenshot_2_type';
			$ssblob = 'screenshot_2_blob';
			break;
		case '3':
			$sstype = 'screenshot_3_type';
			$ssblob = 'screenshot_3_blob';
			break;
	}

	$getpatch = $DB->query_first("SELECT title,category,".$ssblob.",".$sstype." FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$title = strtolower($getpatch['title']);
	$title = str_replace(" ", "-", $title);
	$title = str_replace("_", "-", $title);
	$title = str_replace("/", "-", $title);
	$title = str_replace("\\", "-", $title);
	$category = strtolower($getpatch['category']);
	$category = str_replace(" ", "-", $category);
	$known_image_types = array(
	                         'image/pjpeg' => 'jpg',
	                         'image/jpeg'  => 'jpg',
	                         'image/bmp'   => 'bmp',
	                         'image/x-png' => 'png',
							 'image/png'   => 'png'
	                       );
	$image_type = $getpatch[$sstype];
	$ext = $known_image_types[$image_type];
	$name = $category.'-'.$title.'-'.$ss.'.'.$ext;
	if(!$src) {
		echo '<html><head><title>'.$name.'</title></head><body><center><img src="?do=get_image&ss='.$ss.'&pid='.$pid.'&src=1" border="0"></img></center></body></html>';
	} else {
		header('Content-type: '.$getpatch[$sstype]);
		header('Content-Disposition: attachment; filename="'.$name.'"');
		echo $getpatch[$ssblob];
	}
}

function GetChangelog($pid) {
	global $DB;
	$getpatch = $DB->query_first("SELECT title, category, changelog FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	echo '<tr>
			<td align="center"><b>'.$getpatch[category].':</b> '.$getpatch[title].' - Changelog</td>
		</tr>
		<tr>
			<td>'.$getpatch[changelog].'</td>
		</tr>';
}

function UploadImage($pid, $ss, $title) {
	global $DB, $wikiun, $wikipw;

	switch($ss) {
		case '1':
			$sstype = 'screenshot_1_type';
			$ssblob = 'screenshot_1_blob';
			break;
		case '2':
			$sstype = 'screenshot_2_type';
			$ssblob = 'screenshot_2_blob';
			break;
		case '3':
			$sstype = 'screenshot_3_type';
			$ssblob = 'screenshot_3_blob';
			break;
	}

	$getpatch = $DB->query_first("SELECT title,category,".$ssblob.",".$sstype." FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	if(strlen($title) < "1") {
		$title = strtolower($getpatch['title']);
	} else {
		$title = strtolower($title);
	}
	$title = str_replace(" ", "-", $title);
	$title = str_replace("_", "-", $title);
	$title = str_replace("/", "-", $title);
	$title = str_replace("\\", "-", $title);
	$category = strtolower($getpatch['category']);
	$category = str_replace(" ", "-", $category);
	$known_image_types = array(
	                         'image/pjpeg' => 'jpg',
	                         'image/jpeg'  => 'jpg',
	                         'image/bmp'   => 'bmp',
	                         'image/x-png' => 'png',
							 'image/png'   => 'png'
	                       );
	$image_type = $getpatch[$sstype];
	$ext = $known_image_types[$image_type];
	$name = ucfirst($category.'-'.$title.'-'.$ss.'.'.$ext);
	system('rm -f /tmp/'.$name);
	file_put_contents('/tmp/'.$name, $getpatch[$ssblob]);
	require_once('simpletest/browser.php');
	$browser = &new SimpleBrowser();
	$browser->get('http://www.webos-internals.org/wiki/Special:Userlogin');
	$browser->setFieldById('wpName1', $wikiun);
	$browser->setFieldById('wpPassword1', $wikipw);
	$browser->clickSubmitById('wpLoginAttempt');
	$browser->get('http://www.webos-internals.org/wiki/Special:Upload');
	$browser->setFieldById('wpUploadFile', '/tmp/'.$name);
	$browser->setFieldById('wpDestFile', $name);
	$browser->setFieldById('wpIgnoreWarning', 'true');
	$page = $browser->clickSubmitByName('wpUpload');
	system('rm -f /tmp/'.$name);
	preg_match('/img alt="File:'.$name.'" src=(.*)/', $page, $matches);
	$start_pos = strpos($matches[0], "=", 10)+2;
	$temp_url = substr($matches[0], $start_pos);
	$stop_pos = strpos($temp_url, '"');
	$image_url = substr($temp_url, 0, $stop_pos);
	return 'http://www.webos-internals.org'.$image_url;
}

function SendEmail($emailtype, $pid) {
	global $DB;
	$admin_email = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'admin_emails'");
	$patch = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."patches WHERE pid = '".$pid."'");
	$to = $patch['email'];
	$from = 'WebOS-Patches Web Portal <support@webos-internals.org>';
	$random_hash = md5(date('r', time()));
	$headers = "From: ".$from."\r\n";
	$headers .= "Return-Path: ".$from."\r\n";
	$headers .= "Reply-To: ".$from."\r\n";
	$headers .= "Bcc: ".$admin_email['value']."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	if($emailtype == "submit_update" || $emailtype == "submit_new") {
		$subject = '[Patch-Submitted] '.$patch['title'];
		ob_start();
?>
<html>
<head>
  <title>Patch Submitted via the WebOS-Patches Web Portal!</title>
</head>
<body>
<h2>Patch Submitted via the <a href="http://patches.webos-internals.org/">WebOS-Patches Web Portal</a>!</h2>
<p>Thank you for submitting <?php echo iif($emailtype=="submit_update", "an update to the", "a new"); ?> patch entitled <?php echo '<b>'.$patch['category'].':</b> '.$patch['title']; ?>.</p>
<p>You will receive another email once the patch is either approved or denied.<br/>
If you did not submit this patch and are receiving this email, it is more than likely because your email is still listed as the primary contact for this patch. If you would like your email removed, please reply to this email requesting so.</p>
<p>-WebOS Internals<br/><a href="http://patches.webos-internals.org/">http://patches.webos-internals.org/</a></p>
</body>
</html>
<?php
	}
	if($emailtype == "approved" || $emailtype == "denied") {
		$subject = '[Patch-'.ucfirst($emailtype).'] '.$patch['title'];
		ob_start();
?>
<html>
<head>
  <title>Patch <?php echo ucfirst($emailtype); ?> at the WebOS-Patches Web Portal!</title>
</head>
<body>
<h2>Patch <?php echo ucfirst($emailtype); ?> at the <a href="http://patches.webos-internals.org/">WebOS-Patches Web Portal</a>!</h2>
<p>Thank you for submitting <?php echo iif($patch['update_pid']>="1", "an update to the", "a new"); ?> patch entitled <?php echo '<b>'.$patch['category'].':</b> '.$patch['title']; ?>.</p>
<p>This email is to inform you the patch has been <?php echo $emailtype; ?>!<br/>
<br/>
<?php
	if($emailtype == "denied") {
		echo "It was denied for the following reason:<br/><br/>".$patch['denied_reason']."<br/><br/>If you feel this is a mistake, please contact the admins. Otherwise, if applicable, correct the patch and resubmit it.";
	} else {
		echo "You should see it in the WebOS-Patches Feed in Preware (and other installers) within the next couple of hours.";
	}
?><br/>
<br/>
If you did not submit this patch and are receiving this email, it is more than likely because your email is still listed as the primary contact for this patch. If you would like your email removed, please reply to this email requesting so.</p>
<p>-WebOS Internals<br/><a href="http://patches.webos-internals.org/">http://patches.webos-internals.org/</a></p>
</body>
</html>
<?php
	}
	$message = ob_get_clean();
	if(!@mail( $to, $subject, $message, $headers, "-rsupport@webos-internals.org -fsupport@webos-internals.org" )) {
		mail($admin_email['value'], "WebOS-Patches Web Portal Email Error", "There was an error in sending the email to the developer.", "From: support@webos-internals.org");
	}

}

function FormatForForm($input) {
	$return = htmlspecialchars(stripslashes(br2nl($input)));
	return $return;
}

function br2nl($string){
  $return=eregi_replace('<br[[:space:]]*/?'.
    '[[:space:]]*>',chr(13).chr(10),$string);
  return $return;
}

function mynl2br($string) {
   return strtr($string, array("\r\n" => '<br/>', "\r" => '<br/>', "\n" => '<br/>'));
}

function SpamCheck($ip) {
	global $DB;
	$wait = (60);
	$get_safe_ip_list = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'safe_ip_list'");
	$safe_ip_list = explode(',', $get_safe_ip_list['value']);
	if(!in_array($ip, $safe_ip_list)) {
		$spamcheck = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."spamcheck WHERE ip = '".$ip."'");
		if($spamcheck['sid'] >= "1") {
			if(time() >= ($spamcheck['time']+$wait)) {
				$DB->query("DELETE FROM ".TABLE_PREFIX."spamcheck WHERE sid = '".$spamcheck."' LIMIT 1");
			} else {
				return 1;
			}
		}
	} else {
		$DB->query("DELETE FROM ".TABLE_PREFIX."spamcheck WHERE ip = '".$ip."' LIMIT 1");
	}
	return 0;
}

function PreventSpam($ip) {
	global $DB;
	$time = time();
	$get_safe_ip_list = $DB->query_first("SELECT value FROM ".TABLE_PREFIX."settings WHERE setting = 'safe_ip_list'");
	$safe_ip_list = explode(',', $get_safe_ip_list['value']);
	if(!in_array($ip, $safe_ip_list)) {
		$dupcheck = $DB->query_first("SELECT * FROM ".TABLE_PREFIX."spamcheck WHERE ip = '".$ip."'");
		if($dupcheck['sid'] >= "1") {
			$query = "UPDATE ".TABLE_PREFIX."spamcheck SET time = '".$time."' WHERE sid = '".$dupcheck['sid']."'";
			echo $query;
			$DB->query($query);
		} else {
			$DB->query("INSERT INTO ".TABLE_PREFIX."spamcheck ( ip, time ) VALUES ( '".$ip."', '".$time."')");
		}
	} else {
		$DB->query("DELETE FROM ".TABLE_PREFIX."spamcheck WHERE ip = '".$ip."' LIMIT 1");
	}
}

function GetRemoteAddress() {
	$remote_address = $_SERVER['REMOTE_ADDR'];
			
	// If HTTP_X_FORWARDED_FOR is set, we try to grab the first non-LAN IP
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		if (preg_match_all('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $_SERVER['HTTP_X_FORWARDED_FOR'], $address_list))
		{
			$lan_ips = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/', '/^224\..*/', '/^240\..*/');
			$address_list = preg_replace($lan_ips, null, $address_list[0]);

			while (list(, $cur_address) = each($address_list))
			{
				if ($cur_address)
				{
					$remote_address = $cur_address;
					break;
				}
			}
		}
	}

	return $remote_address;
}

function validateUrlSyntax( $urladdr, $options="" ) {
    // Force Options parameter to be lower case
    // DISABLED PERMAMENTLY - OK to remove from code
    //    $options = strtolower($options);

    // Check Options Parameter
    if (!ereg( '^([sHSEFuPaIpfqr][+?-])*$', $options ))
    {
        trigger_error("Options attribute malformed", E_USER_ERROR);
    }

    // Set Options Array, set defaults if options are not specified
    // Scheme
    if (strpos( $options, 's') === false) $aOptions['s'] = '?';
    else $aOptions['s'] = substr( $options, strpos( $options, 's') + 1, 1);
    // http://
    if (strpos( $options, 'H') === false) $aOptions['H'] = '?';
    else $aOptions['H'] = substr( $options, strpos( $options, 'H') + 1, 1);
    // https:// (SSL)
    if (strpos( $options, 'S') === false) $aOptions['S'] = '?';
    else $aOptions['S'] = substr( $options, strpos( $options, 'S') + 1, 1);
    // mailto: (email)
    if (strpos( $options, 'E') === false) $aOptions['E'] = '-';
    else $aOptions['E'] = substr( $options, strpos( $options, 'E') + 1, 1);
    // ftp://
    if (strpos( $options, 'F') === false) $aOptions['F'] = '-';
    else $aOptions['F'] = substr( $options, strpos( $options, 'F') + 1, 1);
    // User section
    if (strpos( $options, 'u') === false) $aOptions['u'] = '?';
    else $aOptions['u'] = substr( $options, strpos( $options, 'u') + 1, 1);
    // Password in user section
    if (strpos( $options, 'P') === false) $aOptions['P'] = '?';
    else $aOptions['P'] = substr( $options, strpos( $options, 'P') + 1, 1);
    // Address Section
    if (strpos( $options, 'a') === false) $aOptions['a'] = '+';
    else $aOptions['a'] = substr( $options, strpos( $options, 'a') + 1, 1);
    // IP Address in address section
    if (strpos( $options, 'I') === false) $aOptions['I'] = '?';
    else $aOptions['I'] = substr( $options, strpos( $options, 'I') + 1, 1);
    // Port number
    if (strpos( $options, 'p') === false) $aOptions['p'] = '?';
    else $aOptions['p'] = substr( $options, strpos( $options, 'p') + 1, 1);
    // File Path
    if (strpos( $options, 'f') === false) $aOptions['f'] = '?';
    else $aOptions['f'] = substr( $options, strpos( $options, 'f') + 1, 1);
    // Query Section
    if (strpos( $options, 'q') === false) $aOptions['q'] = '?';
    else $aOptions['q'] = substr( $options, strpos( $options, 'q') + 1, 1);
    // Fragment (Anchor)
    if (strpos( $options, 'r') === false) $aOptions['r'] = '?';
    else $aOptions['r'] = substr( $options, strpos( $options, 'r') + 1, 1);


    // Loop through options array, to search for and replace "-" to "{0}" and "+" to ""
    foreach($aOptions as $key => $value)
    {
        if ($value == '-')
        {
            $aOptions[$key] = '{0}';
        }
        if ($value == '+')
        {
            $aOptions[$key] = '';
        }
    }

    // DEBUGGING - Unescape following line to display to screen current option values
    // echo '<pre>'; print_r($aOptions); echo '</pre>';


    // Preset Allowed Characters
    $alphanum    = '[a-zA-Z0-9]';  // Alpha Numeric
    $unreserved  = '[a-zA-Z0-9_.!~*' . '\'' . '()-]';
    $escaped     = '(%[0-9a-fA-F]{2})'; // Escape sequence - In Hex - %6d would be a 'm'
    $reserved    = '[;/?:@&=+$,]'; // Special characters in the URI

    // Beginning Regular Expression
                       // Scheme - Allows for 'http://', 'https://', 'mailto:', or 'ftp://'
    $scheme            = '(';
    if     ($aOptions['H'] === '') { $scheme .= 'http://'; }
    elseif ($aOptions['S'] === '') { $scheme .= 'https://'; }
    elseif ($aOptions['E'] === '') { $scheme .= 'mailto:'; }
    elseif ($aOptions['F'] === '') { $scheme .= 'ftp://'; }
    else
    {
        if ($aOptions['H'] === '?') { $scheme .= '|(http://)'; }
        if ($aOptions['S'] === '?') { $scheme .= '|(https://)'; }
        if ($aOptions['E'] === '?') { $scheme .= '|(mailto:)'; }
        if ($aOptions['F'] === '?') { $scheme .= '|(ftp://)'; }
        $scheme = str_replace('(|', '(', $scheme); // fix first pipe
    }
    $scheme            .= ')' . $aOptions['s'];
    // End setting scheme

                       // User Info - Allows for 'username@' or 'username:password@'. Note: contrary to rfc, I removed ':' from username section, allowing it only in password.
                       //   /---------------- Username -----------------------\  /-------------------------------- Password ------------------------------\
    $userinfo          = '((' . $unreserved . '|' . $escaped . '|[;&=+$,]' . ')+(:(' . $unreserved . '|' . $escaped . '|[;:&=+$,]' . ')+)' . $aOptions['P'] . '@)' . $aOptions['u'];

                       // IP ADDRESS - Allows 0.0.0.0 to 255.255.255.255
    $ipaddress         = '((((2(([0-4][0-9])|(5[0-5])))|([01]?[0-9]?[0-9]))\.){3}((2(([0-4][0-9])|(5[0-5])))|([01]?[0-9]?[0-9])))';

                       // Tertiary Domain(s) - Optional - Multi - Although some sites may use other characters, the RFC says tertiary domains have the same naming restrictions as second level domains
    $domain_tertiary   = '(' . $alphanum . '(([a-zA-Z0-9-]{0,62})' . $alphanum . ')?\.)*';

                       // Second Level Domain - Required - First and last characters must be Alpha-numeric. Hyphens are allowed inside.
    $domain_secondary  = '(' . $alphanum . '(([a-zA-Z0-9-]{0,62})' . $alphanum . ')?\.)';

    /* // This regex is disabled on purpose in favour of the more exact version below
                       // Top Level Domain - First character must be Alpha. Last character must be AlphaNumeric. Hyphens are allowed inside.
    $domain_toplevel   = '([a-zA-Z](([a-zA-Z0-9-]*)[a-zA-Z0-9])?)';
    */

                       // Top Level Domain - Required - Domain List Current As Of December 2004. Use above escaped line to be forgiving of possible future TLD's
    $domain_toplevel   = '(aero|biz|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|post|pro|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ax|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)';


                       // Address can be IP address or Domain
    if ($aOptions['I'] === '{0}') {       // IP Address Not Allowed
        $address       = '(' . $domain_tertiary . $domain_secondary . $domain_toplevel . ')';
    } elseif ($aOptions['I'] === '') {  // IP Address Required
        $address       = '(' . $ipaddress . ')';
    } else {                            // IP Address Optional
        $address       = '((' . $ipaddress . ')|(' . $domain_tertiary . $domain_secondary . $domain_toplevel . '))';
    }
    $address = $address . $aOptions['a'];

                       // Port Number - :80 or :8080 or :65534 Allows range of :0 to :65535
                       //    (0-59999)         |(60000-64999)   |(65000-65499)    |(65500-65529)  |(65530-65535)
    $port_number       = '(:(([0-5]?[0-9]{1,4})|(6[0-4][0-9]{3})|(65[0-4][0-9]{2})|(655[0-2][0-9])|(6553[0-5])))' . $aOptions['p'];

                       // Path - Can be as simple as '/' or have multiple folders and filenames
    $path              = '(/((;)?(' . $unreserved . '|' . $escaped . '|' . '[:@&=+$,]' . ')+(/)?)*)' . $aOptions['f'];

                       // Query Section - Accepts ?var1=value1&var2=value2 or ?2393,1221 and much more
    $querystring       = '(\?(' . $reserved . '|' . $unreserved . '|' . $escaped . ')*)' . $aOptions['q'];

                       // Fragment Section - Accepts anchors such as #top
    $fragment          = '(#(' . $reserved . '|' . $unreserved . '|' . $escaped . ')*)' . $aOptions['r'];


    // Building Regular Expression
    $regexp = '^' . $scheme . $userinfo . $address . $port_number . $path . $querystring . $fragment . '$';

    // DEBUGGING - Uncomment Line Below To Display The Regular Expression Built
    // echo '<pre>' . htmlentities(wordwrap($regexp,70,"\n",1)) . '</pre>';

    // Running the regular expression
    if (eregi( $regexp, $urladdr ))
    {
        return true; // The domain passed
    }
    else
    {
        return false; // The domain didn't pass the expression
    }

}

function validateEmail($email) {
	// checks proper syntax
	if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\+=._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
	    return false;
	}
	// gets domain name
	list($username,$domain)=split('@',$email);
	// checks for if MX records in the DNS
	$mxhosts = array();
	if(!getmxrr($domain, $mxhosts)) {
		// no mx records, can't email
		return false;
	} else {
		// Found MX record.. Might be ok.
		return true;
	}
}

// ########################## PRE CLEAN ##########################
// preclean: cleans all post, get, and cookie data - before entering database

function PreClean($data) {
	if(is_array($data)) {
		foreach($data as $key => $val) {
			$return[$key] = PreClean($val);
		}
	    return ($return);
	} else {
    	return (strip_tags(trim($data)));
  	}
}

// ################################### IIF  ####################################

function iif($expression, $returntrue, $returnfalse) {
	if ($expression==0) {
		return $returnfalse;
	} else {
		return $returntrue;
  	}
}

?>
