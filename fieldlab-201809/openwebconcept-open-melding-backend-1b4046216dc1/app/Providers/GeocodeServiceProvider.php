<?php

namespace App\Providers;

use Geocoder\Geocoder;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Illuminate\Support\ServiceProvider;
use Swis\Geocoder\NationaalGeoregister\NationaalGeoregister;

class GeocodeServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(Provider::class, NationaalGeoregister::class);
    }

}