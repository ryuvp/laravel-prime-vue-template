<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name')->default('api');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->string('category');
            $table->string('guard_name')->default('api');
            $table->timestamps();
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned();
            $table->morphs('model');

            $table->foreign('role_id')->references('id')->on('roles')
                ->onDelete('cascade');
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->bigInteger('permission_id')->unsigned();
            $table->morphs('model');

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onDelete('cascade');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->bigInteger('permission_id')->unsigned();
            $table->bigInteger('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onDelete('cascade');
        });

        $roles = [
            ['name' => 'superadmin', 'guard_name' => 'api'],
            ['name' => 'user', 'guard_name' => 'api'],
        ];
        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        $permissions = [
            ['name' => 'home', 'guard_name' => 'api', 'description' => 'home', 'route' => '', 'icon' => '', 'category' => 'section'],
            ['name' => 'home.dashboard', 'guard_name' => 'api', 'description' => 'dashboard', 'route' => '/intranet/dashboard', 'icon' => 'pi pi-home', 'category' => 'menu'],
            ['name' => 'manage', 'guard_name' => 'api', 'description' => 'manage', 'route' => '', 'icon' => '', 'category' => 'section'],
            ['name' => 'manage.users', 'guard_name' => 'api', 'description' => 'users', 'route' => '/intranet/users', 'icon' => 'pi pi-users', 'category' => 'menu'],
            ['name' => 'manage.roles', 'guard_name' => 'api', 'description' => 'roles', 'route' => '/intranet/roles', 'icon' => 'pi pi-lock', 'category' => 'menu'],
            ['name' => 'manage.permissions', 'guard_name' => 'api', 'description' => 'permissions', 'route' => '/intranet/permissions', 'icon' => 'pi pi-ban', 'category' => 'menu'],
            ['name' => 'profile', 'guard_name' => 'api', 'description' => 'profile', 'route' => '', 'icon' => '', 'category' => 'section'],
            ['name' => 'profile.user', 'guard_name' => 'api', 'description' => 'user', 'route' => '/intranet/profile', 'icon' => 'pi pi-user', 'category' => 'menu'],
        ];
        foreach ($permissions as $permissionData) {
            Permission::create($permissionData);
        }

        $superadminRole = Role::where('name', 'superadmin')->first();
        $userRole = Role::where('name', 'user')->first();

        $permissions = Permission::all();

        $superadminRole->givePermissionTo($permissions);
        $userRole->givePermissionTo(['profile', 'profile.user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
