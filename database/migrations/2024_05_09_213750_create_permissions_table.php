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
        // Crear tablas
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name')->default('api');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
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

        // Insertar roles
        $roles = [
            ['name' => 'superadmin', 'guard_name' => 'api'],
            ['name' => 'user', 'guard_name' => 'api'],
            // Puedes agregar más roles aquí según sea necesario
        ];
        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Insertar permisos
        $permissions = [
            ['name' => 'manage_users', 'guard_name' => 'api'],
            ['name' => 'manage_roles', 'guard_name' => 'api'],
            ['name' => 'manage_permissions', 'guard_name' => 'api'],
            ['name' => 'access_admin_panel', 'guard_name' => 'api'],
            ['name' => 'view_profile', 'guard_name' => 'api'],
            ['name' => 'edit_profile', 'guard_name' => 'api'],
            // Puedes agregar más permisos aquí según sea necesario
        ];
        foreach ($permissions as $permissionData) {
            Permission::create($permissionData);
        }

        // Asignar permisos a roles
        $superadminRole = Role::where('name', 'superadmin')->first();
        $userRole = Role::where('name', 'user')->first();

        $permissions = Permission::all();

        $superadminRole->givePermissionTo($permissions);
        $userRole->givePermissionTo(['view_profile', 'edit_profile']);
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
