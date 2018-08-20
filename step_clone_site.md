### Step to cron new site.

* `mysqldump -uroot -ptieungao mobifaster > /tmp/mobifaster.sql`

* `mysql -uroot -ptieungao -e "CREATE DATABASE cybernet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`

* `mysql -uroot -ptieungao cybernet < /tmp/mobifaster.sql`

* Edit `/var/www/html/super_v2/config/site.php`:

```text
return [
    'list' => [
        'new_azoffers',
        'appsdude',
        'mobifaster',
        'richxyz',
        'richnet',
        'inmob',
        'inmobxyz',
        'cybernet',
        'cyberxyz',
```

* Edit `/var/www/html/super_v2/config/database.php`:

```text
 'cyberxyz' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'cyberxyz',
            'username' => 'root',
            'password' => 'tieungao',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'unix_socket' => '/var/run/mysqld/mysqld.sock'
        ],
```

* Add Nginx files :

```text
cd /etc/nginx/sites-enabled
root@ubuntu:/etc/nginx/sites-enabled# cp inmob.xyz appcyber.net
root@ubuntu:/etc/nginx/sites-enabled# cp inmob.xyz appcyber.xyz
```

* Edit Nginx File

```text
server {
    listen 80;

    root /var/www/html/super_v2/public;
    index index.php index.html index.htm;

    server_name appcyber.net;
    access_log /var/log/nginx/cybernet_access.log;
    error_log /var/log/nginx/cybernet_error.log;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param DB_DATABASE cybernet;
        fastcgi_param GOOGLE_CALLBACK http://appcyber.net/admin/callback;
        fastcgi_param RATE_CRON 1;
        include fastcgi_params;
        fastcgi_read_timeout 300000;
    }
    location ~* .(woff|eot|ttf|svg|mp4|webm|jpg|jpeg|png|gif|ico|css|js)$ {
        expires 365d;
    }

}

```

* Restart Nginx `service nginx restart`
* Go to `https://console.developers.google.com/apis/credentials/oauthclient/260388185441-3g755s6hm5evmiej9kqco8fu18am4ae9.apps.googleusercontent.com?project=bookstore7-1258&authuser=1`

* Clear database 

```text
SET foreign_key_checks = 0;
TRUNCATE clicks;
TRUNCATE groups;
TRUNCATE media_offers;
TRUNCATE network_clicks;
TRUNCATE networks;
TRUNCATE offers;
TRUNCATE virtual_logs;
TRUNCATE users;
SET foreign_key_checks = 1;
```
* Create Admin Users:

```text
php artisan --db=cybernet add:admin --email="quan.dm@teko.vn"
php artisan --db=cybernet add:admin --email="namdoitntn@gmail.com"
```

* Add to `Cron Project` by going to `/var/www/html/project/project/settings.py` and add database lines.

* Restart `service uwsgi restart`

* Add to Boot CLick Project

go to `/var/www/html/python_lumen/core/management/commands/basic.py`

```text
if site_name == 'cybernet' or site_name == 'cyberxyz':
        credentials = 'lum-customer-xxxxx-country-' + country.lower() + '-session-' + rand.lower() + ':xxxxx'
```

* Go to `http://appcyber.net/admin/networks` and add new network `https://api.adsfast.com/affiliate/offer/findAll/?token=zxtqg6o4OpsQPGBxMmEo3t2jSr7stNX0&approved=1`

* Start Cron to get offers.