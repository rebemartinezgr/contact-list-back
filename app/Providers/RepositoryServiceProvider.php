<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Providers;

use App\Interfaces\ContactRepositoryInterface;
use App\Repositories\ContactRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
