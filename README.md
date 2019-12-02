# wordpress-memcached-nginx-combo
Wordpress full page caching using nginx &amp; memcached. No plugins required.

Saw this post on [Blazing fast WordPress with Nginx and Memcached](https://wildlyinaccurate.com/blazing-fast-wordpress-with-nginx-and-memcached/)

Modified it a bit. Full credit goes up ^

* Put index-cache.php on your WordPress home folder.
* Put memcached.conf inside /etc/nginx
* Put the following lines inside your /etc/nginx/sites-enabled/whatever.conf file:

```
server {
  listen 80;
  root /var/www/your/root/folder;
  ...
  include memcached.conf;
}
```

Test, run and you should see some debug stuff at the end of each page

NOTE: I'm using PHP-FPM because it's awesome.
