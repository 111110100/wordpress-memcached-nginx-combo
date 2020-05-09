<?php
/**
 * @package search-permalink 
 * @author erwin lomibao 
 * @version 1.0 
 * @license commercial
 * 
 * @wordpress-plugin
 * Plugin Name: Search Permalink 
 * Description: Makes search results SEO friendly and cacheable. Accompanying plugin for https://github.com/111110100/wordpress-memcached-nginx-combo
 * Version: 1.0
 * Author: Erwin Lomibao
 * Author URI: https://github.com/111110100/wordpress-memcached-nginx-combo
 */

defined( 'ABSPATH' ) || exit;

function wpb_change_search_url() {
    if ( is_search() && ! empty( $_GET['s'] ) ) {
        wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
        exit();
    }   
}
add_action( 'template_redirect', 'wpb_change_search_url' );

?>