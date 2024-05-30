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
            $table->tinyInteger('category')->comment('0:section, 1:menu, 2:menu_link');
            $table->string('guard_name')->default('api');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('permissions')->onDelete('cascade');
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
            ['name' => 'home', 'guard_name' => 'api', 'description' => 'home', 'route' => '', 'icon' => '', 'category' => 0, 'parent_id' => null],
            ['name' => 'home.dashboard', 'guard_name' => 'api', 'description' => 'dashboard', 'route' => '/intranet/dashboard', 'icon' => 'pi pi-home', 'category' => 2, 'parent_id' => 1],
            ['name' => 'manage', 'guard_name' => 'api', 'description' => 'manage', 'route' => '', 'icon' => '', 'category' => 0, 'parent_id' => null],
            ['name' => 'manage.users', 'guard_name' => 'api', 'description' => 'users', 'route' => '/intranet/users', 'icon' => 'pi pi-users', 'category' => 2, 'parent_id' => 3],
            ['name' => 'manage.roles', 'guard_name' => 'api', 'description' => 'roles', 'route' => '/intranet/roles', 'icon' => 'pi pi-lock', 'category' => 2, 'parent_id' => 3],
            ['name' => 'manage.permissions', 'guard_name' => 'api', 'description' => 'permissions', 'route' => '/intranet/permissions', 'icon' => 'pi pi-ban', 'category' => 2, 'parent_id' => 3],
            ['name' => 'profile', 'guard_name' => 'api', 'description' => 'profile', 'route' => '', 'icon' => '', 'category' => 0, 'parent_id' => null],
            ['name' => 'profile.user', 'guard_name' => 'api', 'description' => 'user', 'route' => '', 'icon' => 'pi pi-user', 'category' => 1, 'parent_id' => 7],
            ['name' => 'profile.user.new', 'guard_name' => 'api', 'description' => 'New user', 'route' => '/intranet/profile/user/new', 'icon' => 'pi pi-user-plus', 'category' => 2, 'parent_id' => 8],
            ['name' => 'profile.user.edit', 'guard_name' => 'api', 'description' => 'Edit user', 'route' => '/intranet/profile/user/edit', 'icon' => 'pi pi-user-edit', 'category' => 2, 'parent_id' => 8],
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
