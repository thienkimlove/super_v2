### configuration on 2 type of sites

* In `config/site.php`

```textmate
 'list' => [
        //'super',
        'new_azoffers',
        'appsdude',
        //'justapp',
        'mobifaster',
        'richxyz',
        'richnet',
    ],

    '2nd_list' => [
        'affapk',
    ],
```

* For running cron for clear list offers which not have leads for days

we setting the environment in `/etc/enviroment`

```textmate
export SITE_NAME=list 
```
or 
```textmate
export SITE_NAME=2nd_list
```
and run in `/etc/crontab`

* For adding virtual clicks when have traffics we using function in `MainController`

```textmate
                                #put in queues for process multi click.
                                if ($offer->number_when_click > 0 && in_array(env('DB_DATABASE'), config('site.list'))) {
                                    try {
                                        for ($i = 0; $i < $offer->number_when_click; $i++) {

                                            $true_link  = str_replace('#subid', md5(time()).$i, $offer->redirect_link);
                                            $true_link  = str_replace('#subId', md5(time()).$i, $true_link);

                                            \DB::connection('virtual')->table('logs')->insert([
                                                //'link' => url('check?offer_id='.$offer_id),
                                                'link' => $true_link,
                                                'allow' => $offer->allow_devices,
                                                'country' => $checkLocation,
                                            ]);
                                        }
                                    } catch (\Exception $e) {

                                    }
                                }
```

