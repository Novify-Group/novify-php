<?php

namespace App\Providers;

use App\Contracts\Services\SMSServiceContract;
use App\Services\SMS\AfricasTalkingSMSService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Payment\MobileMoneyContract;
use App\Services\Payment\Integrations\InterswitchApi;
use App\Contracts\Payment\CardPaymentContract;
use App\Services\Payment\Integrations\CardPaymentGateway;
use App\Contracts\Wallet\WalletBalanceContract;
use App\Services\Wallet\InternalWalletBalanceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SMSServiceContract::class, function ($app) {
            return new AfricasTalkingSMSService();
        }); 

        $this->app->bind(MobileMoneyContract::class, function ($app) {
            return new InterswitchApi();
        });

        $this->app->bind(CardPaymentContract::class, function ($app) {
            return new CardPaymentGateway();
        });

        $this->app->bind(WalletBalanceContract::class, function ($app) {
            return new InternalWalletBalanceService();
        });
    }   

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
