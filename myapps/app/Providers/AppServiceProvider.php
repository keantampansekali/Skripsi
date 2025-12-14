<?php

namespace App\Providers;

use App\Events\StokHabis;
use App\Events\StokRendah;
use App\Listeners\SendWhatsAppNotification;
use App\Listeners\SendWhatsAppStockLowNotification;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Observers\BahanBakuObserver;
use App\Observers\ProdukObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Observers
        BahanBaku::observe(BahanBakuObserver::class);
        Produk::observe(ProdukObserver::class);

        // Register Event Listeners
        Event::listen(
            StokHabis::class,
            SendWhatsAppNotification::class
        );

        Event::listen(
            StokRendah::class,
            SendWhatsAppStockLowNotification::class
        );
    }
}
