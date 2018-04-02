### Work with site

* Migration 

`php artisan migration --database=<DATABASE_NAME>`

* Run commands.

All command will run for all databases. For Example

```php
 foreach (config('site.list') as $site) {
            \DB::connection($site)->statement("ALTER TABLE clicks DROP FOREIGN KEY clicks_offer_id_foreign");
            \DB::connection($site)->statement("ALTER TABLE clicks DROP FOREIGN KEY clicks_user_id_foreign");
        }
```

#### Step to create new site

* Create new database `create database new_azoffers`

* Rename database in `config/database.php`

```textmate
 'azoffers' => [
    ...
    'database' => 'new_azoffers',
```
* Upload `config/database.php` to host.

* Change Nginx 

```textmate
oot@ubuntu:/etc/nginx/sites-enabled# rm -f azoffers.net 
root@ubuntu:/etc/nginx/sites-enabled# cp richoffers.net azoffers.net
root@ubuntu:/etc/nginx/sites-enabled# vim azoffers.net 
root@ubuntu:/etc/nginx/sites-enabled# service nginx restart
```
* Run `migrate` on new database 

`root@ubuntu:/var/www/html/super_v2# php artisan migrate --database=azoffers`

* Add users to new database 

`insert into new_azoffers.users select * from richnet.users where email= 'quan.dm@teko.vn' or email = 'boyeucon09@gmail.com'`