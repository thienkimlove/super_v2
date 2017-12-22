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

