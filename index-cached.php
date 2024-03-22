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
$contentType = "Content-Type: text/html";

# Values in seconds. Adjust to your own liking.
function setCacheTime($s) {
    if ($s == '/') {
        $cacheTime = 1800; # homepage 30m
    } elseif (strstr($s, '/tag/') || strstr($s, '/category/') || strstr($s, '/author/') || strstr($s, '/search/')) {
        $cacheTime = 86400; # archive pages 1day
    } elseif (strstr($s, '/feed/')) {
        $contentType = "application/rss+xml"; # set content type for rss feeds
    } elseif (strstr($s, '/atom/')) {
        $contentType = "application/atom+xml";# set content type for rss feeds
    } else {
        $cacheTime = 3600; # other pages 1hr; change this to 0 if using my plugin
    }
    return $cacheTime;
}

header($contentType);

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
