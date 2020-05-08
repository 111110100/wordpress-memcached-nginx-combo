<?php
/**
 * @package wp-flush-mc 
 * @author erwin lomibao 
 * @version 1.0 
 * @license commercial
 * 
 * @wordpress-plugin
 * Plugin Name: Wordpress Flush Memory Cache
 * Description: Accompanying plugin for https://github.com/111110100/wordpress-memcached-nginx-combo
 * Version: 1.0
 * Author: Erwin Lomibao
 * Author URI: https://github.com/111110100/wordpress-memcached-nginx-combo
 */

defined( 'ABSPATH' ) || exit;

add_action('save_post', 'wp_flush_mc');

function wp_flush_mc($post_id) {
    $post_type = get_post_type($post_id);
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || 'revision' === $post_type) {
        return;
    } elseif (! current_user_can('edit_post', $post_id) && (! defined('DOING_CRON') || ! DOING_CRON)) {
        return;
    }

    $permalink = get_permalink($post_id);
    $protocols = array('http://', 'https://');
    $permalink = str_replace($protocols, '', $permalink);
    $site = get_site_url();
    $site = str_replace($protocols, '', $site) . '/';

    $memcached = new Memcached();
    $memcached->addServer('127.0.0.1', 11211);
    #$memcached->addServer('/tmp/memcached.sock', 0); # use this if memcached is running as socket
    $memcached->setOption( Memcached::OPT_COMPRESSION, false );
    $memcached->setOption( Memcached::OPT_BUFFER_WRITES, true );
    $memcached->setOption( Memcached::OPT_BINARY_PROTOCOL, true );

    $memcached->deleteMulti(array("fullpage:{$permalink}", "fullpage:{$site}"), 0);
    $memcached->quit();
}
?>
