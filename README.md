# wordpress-memcached-nginx-combo
Wordpress full page caching using NGINX &amp; Memcached. **No plugins required.**

Saw this post on [Blazing fast WordPress with Nginx and Memcached](https://wildlyinaccurate.com/blazing-fast-wordpress-with-nginx-and-memcached/)

Modified it a bit. Full credit goes to [Joseph Wynn](https://twitter.com/Joseph_Wynn)

The [original](https://wildlyinaccurate.com/blazing-fast-wordpress-with-nginx-and-memcached/) relies on PHP to get the content from WordPress and save/get it from Memcached. NGINX has a memcached module that can retrieve the content that been set from the PHP code. This will reduce latency as NGINX will directly serve the content from Memcached rather than going to PHP. This requires the [ngx_http_memcached_module module](http://nginx.org/en/docs/http/ngx_http_memcached_module.html). Run *nginx -V* to see if it's included in your setup. It's easy to add if it's not.

## How to install
* Copy/move **index-cached.php** on your WordPress home folder.
* Copy/move **memcached.conf** inside */etc/nginx*
* Add the line **include memcached.conf;** inside your /etc/nginx/sites-enabled/whatever.conf file:

```
server {
  listen 80;
  root /var/www/your/root/folder;
  ...
  include memcached.conf;
}
```

Test, run and you should see some debug stuff at the end of each page

## Notes:
* Use PHP-FPM sockets because it's faster.
* Play around with ***$cacheTime*** depending on how often you update your site. Homepage is cached 30min, archive pages 1day, other pages 1hr.
* If your site is responsive, you can comment out the browser checks inside ***memcached.conf***.
* If you're using W3 Total Cache, disable Full Page Caching for this to work.
* For additional performance gain, enable query caching in MySQL:
'''
query_cache_limit = 1M
query_cache_size = 16M
query_cache_type = 1
'''
