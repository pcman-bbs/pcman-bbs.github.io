<?php
if( ! $_GET['url'] )
{
  echo '
<html>
<head>
<title>Open Search 內嵌圖示轉換器 for PCMan 2007</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
  因為 Open PCMan 2007 無法支援 png 格式的圖示，
  造成某些 Search Plugin 裡面的圖無法使用。<br />
  本程式用來將 Search Plugins 裡面的 png 圖檔換成 gif 格式。<br />
  請貼上 firefox search plugin 的網址 (<a target="_blank" href="http://wiki.moztw.org/index.php?title=Search_Plugins#.E8.BE.AD.E5.85.B8.E8.88.87.E7.BF.BB.E8.AD.AF">可在此找到不少好用搜尋引擎</a>)
  <br />
  <br />
  <form method="GET" action="'.$_SERVER['PHP_SELF'].'">
        <input size="80" name="url" />
        <input type="submit">
  </form>
  Copyright &copy; 2007 洪任諭 (2007.05.23)
</body>
</html>
  ';
  exit;
}

$file = file_get_contents($_GET[url]);
if( ! $file )
    die('無法開啟網址');

$pimg = strpos( $file, '<Image ' );
if( ! $pimg )
    die('沒有內嵌圖片');

$p1 = strpos( $file, 'data:image/png;base64,' );
if( ! $p1 )
    die( '無法支援的圖片格式' );
$p1 += 22;
$p2 = strpos( $file, '</Image>', $p1 );
if( ! $p2 )
    die( '無效的 Open Search 檔案');

$b64 = substr( $file, $p1, $p2 - $p1 );
$dec = base64_decode( $b64 );
if( !$dec )
    die('base 64 解碼失敗');

$tmpf1 = tempnam( '/tmp', 'tmp_img1' );
if( !$tmpf1 || !file_put_contents( $tmpf1, $dec ) )
    die('圖片載入發生不明錯誤');

$tmpf2 = tempnam( '/tmp', 'tmp_img2' );
exec( 'composite -size 16x16 png:'.$tmpf1.' blank.gif '.$tmpf2 );
unlink( $tmpf1 );

$dec = file_get_contents( $tmpf2 );
$b64 = base64_encode( $dec );

unlink( $tmpf2 );

if( !$dec || !$b64 )
    die('圖片處理發生不明錯誤');

header('Content-type: text/xml');

$result = substr( $file, 0, $pimg );
$result .= '<Image width="16" height="16">data:image/gif;base64,';
$result .= $b64;

$result .= substr( $file, $p2 );
echo $result;
?>
