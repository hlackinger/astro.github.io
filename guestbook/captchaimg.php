<?php
// Header für PNG-Bild senden
header("Content-type: image/png");
// Captcha-Einstellungen
$ValidChars = "ABCEDFGHJKLMNPQRSTUVWXYZ123456789abcdefhknrstuvxz";
$CodeLength = 6;
// Code aus Seed zusammenstellen
$seed = GetParam("s", "G", 0);
if (($seed < 5000) || ($seed > 1000000)) $seed = 0;
mt_srand($seed);
$code = "";
for($i = 0; $i < $CodeLength; $i++) {
  $code .= substr($ValidChars, mt_rand(0, strlen($ValidChars) - 1), 1);
}
// Bild-Einstellungen
$fontsize = 5;
$charwidth = imagefontwidth($fontsize);
$imgwidth = ($charwidth * strlen($code)) + 7;
$imgheight = imagefontheight($fontsize) + 3;
$image = imagecreate($imgwidth, $imgheight);
$imgcolorback = imagecolorallocate($image, 230, 230, 230);
$imgcolortext = imagecolorallocate($image, 100, 100, 100);
$imgcolorline = imagecolorallocate($image, 170, 170, 170);
// Text ausgeben
$fs = $fontsize;
for ($i = 0; $i < strlen($code); $i++) {
  $top = 0;
  $left = 0;
  $mod = bcmod(mt_rand(1,4), 4);
  if ($mod == 1) {
    $fs = $fontsize - 1;
  } else if ($mod == 2) {
    $fs = $fontsize - 2;
    $left = 1;
    $top = mt_rand(0, 2);
  } else if ($mod == 3) {
    $fs = $fontsize - 3;
    $left = 1;
    $top = mt_rand(0, 2);
  }
  imagestring($image, $fs, 4 + ($charwidth * $i) + $left, 1 + $top, substr($code, $i, 1), $imgcolortext);
}
// Rahmen
imagerectangle($image, 0, 0, $imgwidth - 1, $imgheight - 1, $imgcolorline);
// Bild erstellen
imagepng($image);
imagedestroy($image);

function GetParam($ParamName, $Method = "P", $DefaultValue = "") {
  if ($Method == "P") {
    if (isset($_POST[$ParamName])) return $_POST[$ParamName]; else return $DefaultValue;
  } else if ($Method == "G") {
    if (isset($_GET[$ParamName])) return $_GET[$ParamName]; else return $DefaultValue;
  } else if ($Method == "S") {
    if (isset($_SERVER[$ParamName])) return $_SERVER[$ParamName]; else return $DefaultValue;
  }
}
?>