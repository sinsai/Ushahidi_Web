<?php
/**
 *
 * 翻訳の進捗を出力するバッチ
 *
 */
header("Content-type: text/plain; charset=UTF-8;");

$main_loc = "ja_JP";
$pwd = $_SERVER["DOCUMENT_ROOT"] . '/ushahidi/application/i18n/';

/* loation をディレクトリ名から拾う */
$locations = array();
$dp = opendir($pwd);
while ( $file = readdir($dp) )
{
  if ( is_dir($pwd . $file) && $file != '.' 
    && $file != ".." && $file != $main_loc) {
    $locations[] = $file;
  }
}
closedir($dp);

/* 母国のファイルリストを作成 */
$files = array();
$dp = opendir($pwd . $main_loc);
while ( $file = readdir($dp) )
{
  if ( is_file($pwd . $main_loc . "/" . $file) ) {
    $files[] = $file;
  }
}
closedir($dp);


$trdiff = array();
for ($i=0;$i<count($files);$i++)
{
  /* 母国語のファイルを読み込み */
  include($pwd . $main_loc . "/" . $files[$i]);
  arrset(null, $lang, $main_loc);
  unset($lang);
  
  /* 他の言語ファイルを読み込み */
  for ($j=0;$j<count($locations);$j++)
  {
    $targetfile = $pwd . $locations[$j] . "/" . $files[$i];
    if (! file_exists($targetfile) )
      continue;
    include($targetfile);
    arrset(null, $lang, $locations[$j]);
    unset($lang);
  }
}
/* top line */
echo '"keyword",';
echo '"' . $main_loc . '",';
foreach ($locations as $loc_str ) {
  echo $loc_str . '",';
}
echo "\n";

/* transration progress... */
foreach ($trdiff as $keyword => $transed) {
  echo '"' . $keyword . '",';
  echo '"' . preg_replace("/\r\n/","",@$transed[$main_loc]) . '",';
  foreach ($locations as $lc ) {
    echo '"' . preg_replace("/\r\n/","",@$transed[$lc]) . '",';
  }
  echo "\n";
}


function arrset ($key, $buf, $lc) {
  global $trdiff;
  if ( is_array($buf) ) {
      foreach ( $buf as $k => $v ) {
        arrset($key.":".$k, $v, $lc);
      }
  } else {
    $trdiff[$key][$lc] = $buf;
  }
}
