<?php
/**
 * @package index-cached.php
 * @author joseph wynn/erwin lomibao
 * @version 2.0
 * @license commercial
 * @website https://github.com/111110100/wordpress-memcached-nginx-combo
 */

$start = microtime(true);

$debug = True;

# Values in seconds. Adjust to your own liking.
function setCacheTime($s) {
    if ($s == '/') {
        $cacheTime = 1800; # homepage 30m; change this to 0 if using my plugin
    } elseif (strstr($s, '/tag/') || strstr($s, '/category/') || strstr($s, '/author/')) {
        $cacheTime = 86400; # archive pages 1day
    } else {
        $cacheTime = 3600; # other pages 1hr; change this to 0 if using my plugin
    }
    return $cacheTime;
}

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

$memcached->quit();

$finish = microtime(true);
$cacheExpiry = 'Cached for %d seconds';

echo $html;
if ($debug) echo '<!-- ' . sprintf($debugMessage, $finish - $start) . ' ' . sprintf($cacheExpiry, $cacheTime) . ' -->';
exit;
?>
