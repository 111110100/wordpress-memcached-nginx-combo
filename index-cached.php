<?php
$debug = True;

# Values in seconds. Adjust to your own liking.
function setCacheTime($s) {
  if ($s == '/') {
    $cacheTime = 60; # homepage 1min
  } elseif (strstr($s, '/tag/') || strstr($s, '/category/') || strstr($s, '/author/')) {
    $cacheTime = 86400; # archive pages 1day
  } else {
    $cacheTime = 300; # other pages 5mins
  }
  return $cacheTime;
}


$start = microtime(true);

$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 11211);
#use line below if memcached is running as socket
#$memcached->addServer('/var/run/memcached.sock', 0);
$memcached->setOption( Memcached::OPT_COMPRESSION, false );
$memcached->setOption( Memcached::OPT_BUFFER_WRITES, true );
$memcached->setOption( Memcached::OPT_BINARY_PROTOCOL, true );

$cacheKey = "fullpage:{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$cacheTime = setCacheTime($_SERVER['REQUEST_URI']);

$debugMessage = 'Page retrieved from cache in %f seconds.';
$html = $memcached->get($cacheKey);

if (! $html) {
  $debugMessage = 'Page generated in %f seconds.';

  ob_start();

  require 'index.php';
  $html = ob_get_contents();

  $memcached->set($cacheKey, $html, $cacheTime);

  ob_end_clean();
}

$finish = microtime(true);
$cacheExpiry = 'Cached for %d seconds';

echo $html;
if ($debug) echo '<!-- ' . sprintf($debugMessage, $finish - $start) . ' ' . sprintf($cacheExpiry, $cacheTime) . ' -->';
exit;
?>