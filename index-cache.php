<?php
$debug = True;

$start = microtime(true);

$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 11211);
#use line below if memcached is running as socket
#$memcached->addServer('/var/run/memcached.sock', 0);

$cacheKey = "fullpage:{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$cacheTime = $_SERVER['REQUEST_URI'] == '/' ? 60 : 300; # homepage 1min, 5min the rest. Adjust to your own liking.

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
if ($debug) { echo '<-- ' . sprintf($debugMessage, $finish - $start) . ' ' . sprintf($cacheExpiry, $cacheTime) . ' -->'; }
exit;
?>
