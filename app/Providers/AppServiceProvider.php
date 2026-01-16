<?php

namespace App\Providers;

use Throwable;
use App\Models\Parametre;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

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
        //



        Schema::defaultStringLength(191);


        $this->app->booted(function () {
            try {
                if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
                    $permissions = Permission::pluck('id')->toArray();

                    $developpeurRole = Role::where('name', 'developpeur')->first();
                    $superadminRole = Role::where('name', 'superadmin')->first();

                    if ($developpeurRole) {
                        $developpeurRole->permissions()->sync($permissions);
                    }

                    if ($superadminRole) {
                        $superadminRole->permissions()->sync($permissions);
                    }
                }
            } catch (\Exception $e) {
                // Optionnel : log de l'erreur si besoin
                return back()->withErrors('Une erreur est survenue lors de la synchronisation des permissions.', 'Message d\'erreur:' . $e->getMessage());
            }
        });



        //recuperer les parametres
        if (Schema::hasTable('parametres')) {
            $data_parametre = Parametre::with('media')->first();
        }

        view()->share([
            'data_parametre' => $data_parametre ?? null,
        ]);
    }
}
