<?php
// #####################
// ### Gästebuch 2.4 ###
// #####################

// *********************
// *** Einstellungen ***
// *********************

// Titel des Gästebuchs.
$guestbook_title="Mein Gästebuch";

// Style des Gästebuches ("fire" oder "grey").
// (Die Stylesheet-Datei sollte natürlich an die eigene Seite angepasst werden.)
$guestbook_style="black";

// Relativer Pfad zu der Datei, in der die Daten gespeichert werden.
$data_file="gb.txt";

// Relativer Pfad zum Verzeichnis, in dem sich die Bilder (space1.gif) befinden.
$images_path="images/";

// Relativer Pfad zum Verzeichnis, in dem sich die Smilies befinden.
// Ist kein Pfad angegeben, werden keine Smilies angezeigt. 
$smilies_path="images/smilies/";

// Anzahl der Beiträge pro Seite.
$postspersite=7;

// E-Mail Adresse des Webmasters für die E-Mail-Benachrichtigung
// bei neuen Einträgen. Ist keine Adresse angegeben, wird keine
// Benachrichtigung gesendet.
$AdminNotifyMailTo="";

// Administration
$AdminLogin="admin";     // BITTE ÄNDERN!
$AdminPasswd="password"; // BITTE ÄNDERN!

// *********************

$CodeMD5 = "";
$ParamCode = GetParam("p_code", "P");
$ParamSID = GetParam("p_sid", "P");

$CodeValid = 0;
if ($ParamCode != "") {
  if (md5(strtoupper($ParamCode)) == $ParamSID) {
    $CodeValid = 1;
  } else {
    $CodeValid = -1;
  }
}

$action = substr(GetParam("g_action", "G"), 0, 5);
$entry = substr(GetParam("g_entry", "G"), 0, 14);
$first = intval(substr(GetParam("g_first", "G"), 0, 5));

$send = GetParam("p_send", "P");
$gb_name = GetParam("p_gb_name", "P");
$gb_mail = GetParam("p_gb_mail", "P");
$gb_home = GetParam("p_gb_home", "P");
$gb_text = GetParam("p_gb_text", "P");

if($action=="login") {
  AuthUser();
}

if($action=="del") {
  if((isset($_SERVER['PHP_AUTH_USER'])) && (isset($_SERVER['PHP_AUTH_PW'])) && ($_SERVER['PHP_AUTH_USER'] == $AdminLogin) && ($_SERVER['PHP_AUTH_PW'] == $AdminPasswd)) {
    DelPosting($data_file, $entry);
  }
}

// **************************************************************************
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Gästebuch</title>

<meta name="title" content="Gästebuch">
<meta name="description" content="Gästebuch">
<meta name="keywords" content="Gästebuch, G&auml;stebuch, Guestbook">
<meta name="author" content="Gaijin, http://www.gaijin.at/">
<meta http-equiv="content-language" content="de-at">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="<?=$guestbook_style?>.css" type="text/css">

<script language="javascript">
function InsertMailGB(mailnam,mailsvr) {
  document.write('<a href="mailto:'+mailnam+'@'+mailsvr+'">'+mailnam+'@'+mailsvr+'</a>');
}
function add_smilie(sn) {
  document.guestbook.p_gb_text.value=document.guestbook.p_gb_text.value+":_"+sn+"_:";
}
</script>

</head>
<body>

<!-- ############################### -->
<!-- ### Hier den Kopf einfügen #### -->
<!-- ############################### -->


<table border=0 cellspacing=0 cellpadding=0><tr><td>

<?php

$err_text = "";

if ($action=="post") {
  if (strlen($gb_text) > 1000) {
    $gb_text = substr($gb_text, 0, 1000)."... (Text wurde gekürzt!)";
  }
  $gb_name = str_replace(chr(34), "''", $gb_name);
  $gb_name = stripslashes($gb_name);
  $gb_mail = strtolower(stripslashes($gb_mail));
  $gb_home = strtolower(stripslashes($gb_home));
  $gb_text = stripslashes(trim($gb_text));

  if(trim($gb_name == "")) $err_text .= "Bitte gib Deinen Namen an.<br>";
  if(trim($gb_mail != "")) {
    if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$", $gb_mail))
      $err_text.="Bitte gib eine gültige E-Mail-Adresse an (z.B.: vorname.nachname@provider.at).<br>";
  }
  if(trim($gb_home!="") && trim($gb_home != "http://")) {
    if((!ereg("^http:\/\/(.{3,})\.(.{2,})", $gb_home)) || (ereg("\?", $gb_home)))
      $err_text .= "Bitte gib eine gültige URL an (z.B.: http://www.seite.at/).<br>";
  }
  
  if (trim($gb_text == "")) $err_text .= "Bitte gib einen Text ein.<br>";
  
  if ($CodeValid == 0) $err_text .= "Bitte gib den Sicherheitscode ein.<br>";
  if ($CodeValid == -1) $err_text .= "Bitte bib den richtigen Sicherheitscode ein.<br>";
}

?>


<?php
// ******************************
// *** Neuer Gästebucheintrag ***
// ******************************
if ( ($action=="new") or trim($err_text != "") ) {
?>

<center>
<h1><?php echo $guestbook_title; ?></h1>

<h2>Neuen Eintrag hinzufügen</h2>
</center>

<table border="0" cellspacing="0" cellpadding="1"><tr><form action="<?php echo $_SERVER['PHP_SELF']; ?>?g_action=post" method="post" name="guestbook"><td id="guestbooktable">
<table border="0" cellspacing="0" cellpadding="5"><tr><td id="guestbookinfo">

<?php
if(($send=="1") && trim($err_text!="")) {
  echo "<p><big><b>Fehler:</b></big><br>";
  echo "<span id=red>$err_text</span></p>";
}
if(trim($gb_home=="") && (!$gb_home)) $gb_home="http://";
?>

<table border=0 cellspacing=2 cellpadding=0 width=100%><tr>
<td valign=top>
<table border=0 cellspacing=2 cellpadding=0 width=100%>
<tr><td nowrap align=right id="guestbookinfo">Dein Name:</td><td nowrap align=right id="guestbookinfo">&nbsp;</td>
<td width=100%><input type="text" name="p_gb_name" size=50 maxlength=25 value="<?=$gb_name?>"></td></tr>
<tr><td nowrap align=right id="guestbookinfo">Deine E-Mail-Adresse: <span id=red><b>*</b></span></td><td></td>
<td width=100%><input type="text" name="p_gb_mail" size=50 maxlength=50 value="<?=$gb_mail?>"></td></tr>
<tr><td nowrap align=right id="guestbookinfo">Deine Homepage: <span id=red><b>*</b></span></td><td>&nbsp;</td>
<td width=100%><input type="text" name="p_gb_home" size=50 maxlength=65 value="<?=$gb_home?>"></td></tr>
<tr><td nowrap align=right valign=top id="guestbookinfo">Text:<br><i>(max. 1.000 Zeichen,<br>keine HTML-Tags)</i></td><td></td>
<td width=100%><textarea cols="50" rows="8" name="p_gb_text"><?=$gb_text?></textarea></td></tr>

<tr><td nowrap align=right id="guestbookinfo">Sicherheitscode:</td><td>&nbsp;</td>
<td width=100%><?php echo CaptchaImageString($CodeMD5); ?></td></tr>
<tr><td nowrap align=right id="guestbookinfo">Sicherheitsabfrage:</td><td>&nbsp;</td>
<td width=100%><input type=text size=10 maxlen=6 name=p_code value=""></td></tr>
<tr><td nowrap align=right id="guestbookinfo">&nbsp;</td><td>&nbsp;</td>
<td width=100% id="guestbookinfo">Bitte geben Sie den 6-stelligen Sicherheitscode ein.</td></tr>

<tr><td id="guestbookinfo"><span id=red><b>*</b></span><i> = optionale Felder</i></td><td></td><td>
<input type="hidden" name="p_sid" value="<?=$CodeMD5?>">
<input type="hidden" value="1" name="p_send">
<input type="submit" value="Senden" name="submit">
<input type="reset" value="Zurücksetzen" name="reset">
</td></tr>
</table>
</td><td>&nbsp;&nbsp;</td><td width=100% valign=top id="guestbookinfo">
<?php
if($smilies_path) {
?>
<b>SMILIES:</b><br>
<img src="<?=$images_path?>space1.gif" width="1" height="5" alt="" border="0"><br>
<a href="javascript:add_smilie('smile');"><img src="<?=$smilies_path?>smile.gif" border=0 alt=":-)"></a>&nbsp;
<a href="javascript:add_smilie('wink');"><img src="<?=$smilies_path?>wink.gif" border=0 alt=";-)"></a>&nbsp;
<a href="javascript:add_smilie('happy');"><img src="<?=$smilies_path?>happy.gif" border=0 alt=":-))"></a>&nbsp;
<a href="javascript:add_smilie('sad');"><img src="<?=$smilies_path?>sad.gif" border=0 alt=":-("></a>&nbsp;
<a href="javascript:add_smilie('puh');"><img src="<?=$smilies_path?>puh.gif" border=0 alt=":-P"></a>&nbsp;
<a href="javascript:add_smilie('yummie');"><img src="<?=$smilies_path?>yummie.gif" border=0 alt=":_yummie_:"></a>&nbsp;
<a href="javascript:add_smilie('coool');"><img src="<?=$smilies_path?>coool.gif" border=0 alt=":_coool_:"></a><br>
<a href="javascript:add_smilie('pukey');"><img src="<?=$smilies_path?>pukey.gif" border=0 alt=":_pukey_:"></a>&nbsp;
<a href="javascript:add_smilie('devil');"><img src="<?=$smilies_path?>devil.gif" border=0 alt=">:->"></a>&nbsp;
<a href="javascript:add_smilie('frown');"><img src="<?=$smilies_path?>frown.gif" border=0 alt=":_frown_:"></a>&nbsp;
<a href="javascript:add_smilie('redface');"><img src="<?=$smilies_path?>redface.gif" border=0 alt=":_redface_:"></a>&nbsp;
<a href="javascript:add_smilie('clown');"><img src="<?=$smilies_path?>clown.gif" border=0 alt=":_clown_:"></a>&nbsp;
<a href="javascript:add_smilie('cry');"><img src="<?=$smilies_path?>cry.gif" border=0 alt=":_cry_:"></a>&nbsp;
<a href="javascript:add_smilie('idea');"><img src="<?=$smilies_path?>idea.gif" border=0 alt=":_idea_:"></a><br>
<a href="javascript:add_smilie('cwink');"><img src="<?=$smilies_path?>cwink.gif" border=0 alt=":_cwink_:"></a>&nbsp;
<a href="javascript:add_smilie('grrr');"><img src="<?=$smilies_path?>grrr.gif" border=0 alt=":_grrr_:"></a>&nbsp;
<a href="javascript:add_smilie('ill');"><img src="<?=$smilies_path?>ill.gif" border=0 alt=":_ill_:"></a>&nbsp;
<a href="javascript:add_smilie('tooth');"><img src="<?=$smilies_path?>tooth.gif" border=0 alt=":_tooth_:"></a>&nbsp;
<a href="javascript:add_smilie('psycho');"><img src="<?=$smilies_path?>psycho.gif" border=0 alt=":_psycho_:"></a>&nbsp;
<a href="javascript:add_smilie('monster');"><img src="<?=$smilies_path?>monster.gif" border=0 alt=":_monster_:"></a>&nbsp;
<a href="javascript:add_smilie('halt');"><img src="<?=$smilies_path?>halt.gif" border=0 alt=":_halt_:"></a><br>
<a href="javascript:add_smilie('glass');"><img src="<?=$smilies_path?>glass.gif" border=0 alt=":_glass_:"></a>&nbsp;
<a href="javascript:add_smilie('seek');"><img src="<?=$smilies_path?>seek.gif" border=0 alt=":_seek_:"></a>&nbsp;
<a href="javascript:add_smilie('super');"><img src="<?=$smilies_path?>super.gif" border=0 alt=":_super_:"></a>&nbsp;
<a href="javascript:add_smilie('help');"><img src="<?=$smilies_path?>help.gif" border=0 alt=":_help_:"></a>&nbsp;
<a href="javascript:add_smilie('boxer');"><img src="<?=$smilies_path?>boxer.gif" border=0 alt=":_boxer_:"></a><br>
<a href="javascript:add_smilie('dance');"><img src="<?=$smilies_path?>dance.gif" border=0 alt=":_dance_:"></a>&nbsp;
<a href="javascript:add_smilie('alcohol');"><img src="<?=$smilies_path?>alcohol.gif" border=0 alt=":_alcohol_:"></a><br>
<a href="javascript:add_smilie('space1');"><img src="<?=$images_path?>space1.gif" width="1" height="10" alt="" border="0"><br>
<?php
}
?>
<b>TAGS:</b><br>
<img src="<?=$images_path?>space1.gif" width="1" height="5" alt="" border="0"><br>

<table border=0 cellspacing=0 cellpadding=0
<tr><td id="guestbookinfo" align="center"><b id="red">:b:</b></td><td id="guestbookinfo" align="center">&nbsp;bzw.&nbsp;</td><td id="guestbookinfo" align="center"><b id="red">:/b:</b></td><td id="guestbookinfo">&nbsp;für <b>fett</b></tr>
<tr><td id="guestbookinfo" align="center"><b id="red">:i:</b></td><td id="guestbookinfo" align="center">&nbsp;bzw.&nbsp;</td><td id="guestbookinfo" align="center"><b id="red">:/i:</b></td><td id="guestbookinfo">&nbsp;für <i>kursiv</i></tr>
<tr><td id="guestbookinfo" align="center"><b id="red">:u:</b></td><td id="guestbookinfo" align="center">&nbsp;bzw.&nbsp;</td><td id="guestbookinfo" align="center"><b id="red">:/u:</b></td><td id="guestbookinfo">&nbsp;für <u>unterstrichen</u></tr>
</table>

</td>
</tr></table>

</td></tr></table>
</td></form></tr></table>

<center>
<br>
<big><b><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zurück zum Gästebuch</a></b></big><br>
<br>
<table border=0 cellspacing=0 cellpadding=0 width=570><tr><td id=guestbooktable><img src="space1.gif" border=0 height=1 width=1 alt=""></tr></td></table><br>
<a href="http://www.lackinger.cc/">zurueck</a>
<br>
</center>

</td></tr></table>
</body>
</html>

<?php
exit;
}

// **************************
// *** Gästebuch anzeigen ***
// **************************

// *** Zu langen Text abschneiden ***

// *** Wenn Posting "gesendet" wurde und kein Fehlertext ausgegeben wurde ***
if(($send=="1") && trim($err_text=="")) {
  $gb_date=date("YmdHis"); // Datum setzen
  $m_date=date("d.m.Y, H:i:s"); // Datum für E-Mail-Benachrichtigung
  $line=file($data_file); // Daten in Array einlesen
  rsort($line);  // Array in umgekehrter Reihenfolgen sortieren
  
  $gb_name=str_replace("<","&lt;",$gb_name);
  $gb_name=str_replace(">","&gt;",$gb_name);
  $gb_name=str_replace("\"","&quot;",$gb_name);
  $gb_name=str_replace("~","-",$gb_name);
  $gb_name=str_replace("  "," &nbsp;",$gb_name);
  $gb_name=strip_tags($gb_name,"<b><i><u><a><img>");
  
  $gb_home=ereg_replace("(<|>| |\(|\)|\||\"|\')","",$gb_home);
  $gb_home=str_replace("~","-",$gb_home);
  $gb_home=urlencode($gb_home);
  if(trim($gb_home=="http://")) $gb_home="";
  
  $gb_text=str_replace("<","&lt;",$gb_text);
  $gb_text=str_replace(">","&gt;",$gb_text);
  $gb_text=str_replace("\"","&quot;",$gb_text);
  $gb_text=str_replace("~","-",$gb_text);
  $gb_text=str_replace("  "," &nbsp;",$gb_text);
  $gb_text=str_replace("\r\n","<br>",$gb_text);
  $gb_text=urlencode($gb_text);
  $gb_text=strip_tags($gb_text,"<b><i><u><a><img>");
  
  // *** Datei öffnen und mit neuem Eintrag überschreiben ***
  $fp=fopen($data_file,"w");
  flock($fp,2);
  fputs($fp,"$gb_date|~#~|$gb_name|~#~|$gb_mail|~#~|$gb_home|~#~|$gb_text".chr(13).chr(10));
  
  // *** Alte Einträge anhängen ***
  for($i=0;$i<count($line);$i++) {
    fputs($fp,$line[$i]);
  }
  flock($fp,3);
  fclose($fp);
  
  // *** Bei neuem Eintrag eine E-Mail an den Webmaster senden ***
  if($AdminNotifyMailTo) {
    $m_txt="Neuer Eintrag am <b>".$m_date."</b> Uhr:<br>\n";
    $m_txt.="Von: <b>".$gb_name."</b> &lt;<a href=\"mailto:".$gb_mail."\">".$gb_mail."</a>&gt;";
    $m_txt.=" (<a href=\"".$gb_home."\">".$gb_home."</a>)<br>\n<br>\n";
    $m_txt.=$gb_text."\n";
    $m_txt=urldecode($m_txt);
    $m_txt=stripslashes($m_txt);
    // *** Tags ersetzen ***
    $m_txt=eregi_replace("(:)(b|\/b)(:)","<\\2>",$m_txt);
    $m_txt=eregi_replace("(:)(i|\/i)(:)","<\\2>",$m_txt);
    $m_txt=eregi_replace("(:)(u|\/u)(:)","<\\2>",$m_txt);
    
    $mail_date=gmdate("D, d M Y H:i:s")." GMT";
    $header="Date: ".$mail_date."\n";
    $header.="From: Gästebuch <".$AdminNotifyMailTo.">\n";
    $header.="X-Mailer: Guestbook FormMailer (www.gaijin.at)\n";
    $header.="MIME-Version: 1.0\n";
    $header.="Content-Type: text/html; charset=us-ascii\n";
    $header.="Content-Transfer-Encoding: 7bit\n";
    $mail_text="<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
    $mail_text.="<HTML><HEAD><TITLE></TITLE>\n</HEAD>\n<BODY>\n";
    $mail_text.=$m_txt;
    $mail_text.="</BODY>\n</HTML>\n";
    @mail($AdminNotifyMailTo,"Neuer Gaestebucheintrag!",$mail_text,$header); // keinen Fehler ausgeben
    $m_txt="";
    $mail_text="";
    $header="";
    $mail_date="";
  }
  
  echo "<p id=red><big><b>Danke für Deinen Eintrag!</b></big></p>\n";
  
  $gb_name="";
  $gb_mail="";
  $gb_home="";
  $gb_text="";
  $send="";
  $err_text="";
  
}

?>

<center>
<h1><?echo $guestbook_title; ?></h1>
<big><b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?g_action=new">Neuen Gästebucheintrag hinzufügen</a></b></big><br>
<br>
<a href="http://www.lackinger.cc/">zurueck</a>
<br>
</center>

<?php

$line = @file($data_file);

// *** Startwert überprüfen und ggf. setzen ***
if($first < 0) $first = 0;
if($first > count($line) - 1) $first = count($line) - 1;
// *** Anzahl der Postings/Seite überprüfen und ggf. setzen ***
if($postspersite>count($line)) $postspersite = count($line);

// *** Postings nach Startwert und Anzahl/Seite anzeigen ***
$c = $first + $postspersite;
if ($c > count($line)) $c = count($line);
for($i = $first; $i < $c; $i++) {
  $p = explode("|~#~|", $line[$i], 5);
  if((isset($p[0])) && ($i < count($line))) {
    PrintPosting(count($line) - $i, $p[1], $p[2], $p[3], $p[0], $p[4]."<br>\n");
    PrintPostingSpace();
  }
}

if ($line) {
  // *** Navigationslinks generieren ***
  $i = count($line) + $postspersite;
  $j = $i;
  echo "<center><b><i>Postings:</i></b> ";
  while($j > 1) {
    // *** Startwert für Link ***
    $i-=$postspersite;
    if($i<1) $i=1;
    // *** Endwert für Link ***
    $j=$i-$postspersite+1;
    if($j<1) $j=1;
    // *** Umgekehrte Reihenfolge der Postings!!! ;-) ***
    $k=count($line)-$i;
    // *** Navigationslinks ausgeben ***
    if($first==$k) {
      if($i!=$j) {echo "$i-$j";} else {echo "$i";}
    }else{
      echo "<nobr><a href=\"".$_SERVER['PHP_SELF']."?g_first=$k\">";
      if($i!=$j) {echo "$i-$j";} else {echo "$i";}
      echo "</a>";
    }
    if($j>1) echo "&nbsp;|</nobr> ";
  }
  echo "</nobr><br></center>\n\n";
}

if($line)
{
  echo "<center>\n";
  echo "<br>\n";
  echo "<big><b><a href=\"".$_SERVER['PHP_SELF']."?g_action=new\">Neuen Gästebucheintrag hinzufügen</a></b></big><br>\n";
  echo "</center>\n";
}

?>

<center>
<br>
<table border=0 cellspacing=0 cellpadding=0 width=570><tr><td id=guestbooktable><img src="space1.gif" border=0 height=1 width=1 alt=""></tr></td></table><br>
Administration: <b><a href="<?php echo $_SERVER['PHP_SELF']."?g_action=login"; ?>">Login</a></b><br>
<br>
<a href="http://www.lackinger.cc/">zurueck</a>
<br>
</center>

</td></tr></table>
</body>
</html>

<?php


// ############################################################################

function PrintPosting($PostNo,$PostName,$PostMail,$PostHome,$PostTime,$PostMsg)
{
  global $smilies_path;
  global $images_path;
  global $AdminLogin;
  global $AdminPasswd;
  
  $OrigTime=$PostTime;
  $PostTime=substr($PostTime,6,2).".".substr($PostTime,4,2).".".substr($PostTime,0,4).", ".substr($PostTime,8,2).":".substr($PostTime,10,2).":".substr($PostTime,12,2);
  $PostHome=urldecode($PostHome);
  $PostMsg=urldecode($PostMsg);
  $PostMsg=stripslashes($PostMsg);

  // *** Smilies ersetzen ***
  if($smilies_path) {
    $PostMsg=eregi_replace("(\:\_)(.{1,8})(\_\:)"," <img src=\"".$smilies_path."\\2.gif\" border=\"\" alt=\"\\2\"> ",$PostMsg);
    $PostMsg=str_replace(":-))","<img src=\"".$smilies_path."happy.gif\" border=\"\" alt=\"Smile\">",$PostMsg);
    $PostMsg=str_replace(":-)","<img src=\"".$smilies_path."smile.gif\" border=\"\" alt=\"Smile\">",$PostMsg);
    $PostMsg=str_replace(";-)","<img src=\"".$smilies_path."wink.gif\" border=\"\" alt=\"Smile\">",$PostMsg);
    $PostMsg=str_replace(":-(","<img src=\"".$smilies_path."sad.gif\" border=\"\" alt=\"Smile\">",$PostMsg);
    $PostMsg=str_replace("&gt;:-&gt;","<img src=\"".$smilies_path."devil.gif\" border=\"\" alt=\"Smile\">",$PostMsg);
    $PostMsg=str_replace(":-P","<img src=\"".$smilies_path."puh.gif\" border=\"\" alt=\"Smile\">",$PostMsg);
  }
  // *** Tags ersetzen ***
  $PostMsg=eregi_replace("(:)(b|\/b)(:)","<\\2>",$PostMsg);
  $PostMsg=eregi_replace("(:)(i|\/i)(:)","<\\2>",$PostMsg);
  $PostMsg=eregi_replace("(:)(u|\/u)(:)","<\\2>",$PostMsg);

  echo "<table border=0 cellspacing=0 cellpadding=1 width=570><tr><td id=guestbooktable>";
  echo "<table border=0 cellspacing=0 cellpadding=0 width=100%><tr><td id=guestbooktitle>";
  echo "<table border=0 cellspacing=0 cellpadding=1 width=100%><tr><td id=guestbooktitle width=100%>&nbsp;".$PostName."</td>";
  echo "<td id=guestbooktitleinfo nowrap>Eintrag #".$PostNo." vom ".$PostTime." Uhr&nbsp;</td></tr></table>";
  echo "</td></tr><tr><td id=guestbooktable><img src=\"".$images_path."space1.gif\" width=1 height=1 alt=\"\" border=0></td></tr>";
  echo "<tr><td id=guestbookcell><table border=0 cellspacing=0 cellpadding=0 width=100%><tr>";
  echo "<td colspan=2><img src=\"".$images_path."space1.gif\" width=1 height=1 alt=\"\" border=0></td></tr>";
  echo "<tr><td>&nbsp;</td><td width=100% id=guestbookcell><img src=\"".$images_path."space1.gif\" width=1 height=6 alt=\"\" border=0><br>";
  echo $PostMsg."<img src=\"".$images_path."space1.gif\" width=1 height=6 alt=\"\" border=0><br>";
  echo "</td></tr><tr><td colspan=2><img src=\"".$images_path."space1.gif\" width=1 height=2 alt=\"\" border=0></td></tr>";
  echo "<tr><td id=guestbooktable colspan=2><img src=\"".$images_path."space1.gif\" width=1 height=1 alt=\"\" border=0></td></tr></table>";
  echo "<table border=0 cellspacing=0 cellpadding=1 width=100%>";
  echo "<tr><td id=guestbookinfo nowrap align=right>&nbsp;E-Mail:&nbsp;</td>";
  
  if($PostMail=="")
    echo "<td id=guestbookinfolight width=100%><i>(Nicht angegeben)</i></td></tr>";
  else{
    $em=explode("@",$PostMail);
    $m=str_replace("@"," [at] ",$PostMail);
    $m=str_replace("."," [dot] ",$m);
    echo "<td id=guestbookinfo width=100%><script language=\"javascript\">\n<!--\nInsertMailGB(\"$em[0]\",\"$em[1]\");\n//-->\n</script><noscript>$m</noscript></a></td></tr>";
  }

  echo "<tr><td id=guestbookinfo nowrap align=right>&nbsp;Website:&nbsp;</td>";
  if((!$PostHome) || ($PostHome=="http://")) {
    echo "<td id=guestbookinfolight width=100%><i>(Nicht angegeben)</i></td></tr>";
  } else {
    echo "<td id=guestbookinfo width=100%><a href=\"$PostHome\" target=\"_blank\">$PostHome</a></td></tr>";
  }
  echo "</table></td></tr></table></td></tr></table>\n\n";
  if((isset($_SERVER['PHP_AUTH_USER'])) && (isset($_SERVER['PHP_AUTH_PW'])) && ($_SERVER['PHP_AUTH_USER'] == $AdminLogin) && ($_SERVER['PHP_AUTH_PW'] == $AdminPasswd)){
    echo "<b id=red>Admin:</b> <a href=\"".$_SERVER['PHP_SELF']."?g_action=del&g_entry=".$OrigTime."\">Posting löschen</a><br>\n";
  }
  
}

// ############################################################################

function PrintPostingSpace()
{
  global $images_path;
  echo "<table border=0 cellspacing=0 cellpadding=0 width=100%><tr><td><img src=\"".$images_path."space1.gif\" width=1 height=10 border=0></td></tr></table>\n";
}

// ############################################################################

function DelPosting($data_file,$entry){
  if(!file_exists($data_file)) return 0;
  $lines=file($data_file);
  @unlink($data_file);
  $fp=fopen($data_file,"w");
  flock($fp,2);
  foreach($lines as $line){
    $l=explode("|~#~|",$line);
    if((chop($line)) && ($l[0]!=$entry)) fputs($fp,$line);
  }
  flock($fp,3);
  fclose($fp);
  return 1;
}

// ############################################################################

function CaptchaImageString(&$CodeMD5) {
  // Captcha-Einstellungen
  $ValidChars = "ABCEDFGHJKLMNPQRSTUVWXYZ123456789abcdefhknrstuvxz";
  $CodeLength = 6;
  // Code zusammenstellen
  mt_srand((double)microtime() * 1000000);
  $seed = mt_rand(5000, 1000000);
  mt_srand($seed);
  $code = "";
  for($i = 0; $i < $CodeLength; $i++) {
    $code .= substr($ValidChars, mt_rand(0, strlen($ValidChars) - 1), 1);
  }
  $CodeMD5 = md5(strtoupper($code));
  return '<img src="captchaimg.php?s='.$seed.'">';
}

// ############################################################################

function GetParam($ParamName, $Method = "P", $DefaultValue = "") {
  if ($Method == "P") {
    if (isset($_POST[$ParamName])) return $_POST[$ParamName]; else return $DefaultValue;
  } else if ($Method == "G") {
    if (isset($_GET[$ParamName])) return $_GET[$ParamName]; else return $DefaultValue;
  } else if ($Method == "S") {
    if (isset($_SERVER[$ParamName])) return $_SERVER[$ParamName]; else return $DefaultValue;
  }
}

// ############################################################################

function AuthUser() {
  global $AdminLogin;
  global $AdminPasswd;
  if((!isset($_SERVER['PHP_AUTH_USER'])) || (!isset($_SERVER['PHP_AUTH_PW'])) || ($_SERVER['PHP_AUTH_USER'] != $AdminLogin) || ($_SERVER['PHP_AUTH_PW'] != $AdminPasswd)){
    header('WWW-Authenticate: Basic realm="Gaijins Guestbook - Administration"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<html><head></head><body>Authentifizierung erforderlich!<br><br>';
    echo '<a href="guestbook.php">Zurück zum Gästebuch</a><br></body></html>';
    exit;
  }
}

// ############################################################################

?>