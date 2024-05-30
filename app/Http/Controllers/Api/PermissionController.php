<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class PermissionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->verifyComponents();
        return PermissionResource::collection(Permission::all());

        /*$permissions = Permission::whereNull('parent_id')
        ->where('category', 0)
        ->with('children.children.children')
        ->get();

        return PermissionResource::collection($permissions);*/
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions',
            'description' => 'nullable|string',
            'route' => 'nullable|string',
            'icon' => 'nullable|string',
            'category' => 'required|string'
        ]);

        $permission = Permission::create($request->all());

        $superadminRole = Role::where('name', 'superadmin')->first();
        $superadminRole->givePermissionTo($permission);

        return new PermissionResource($permission);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
            'route' => 'nullable|string',
            'icon' => 'nullable|string',
            'category' => 'required|string'
        ]);

        $permission->update($request->all());

        return new PermissionResource($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(null, 204);
    }

    private function verifyComponents()
    {
        //$permissions = Permission::select('name', 'category')->get();
        $permissions = Permission::whereNull('parent_id')
        ->where('category', 0)
        ->with('children.children.children')
        ->get();

        //dd($permissions);
        foreach ($permissions as $permission) {
            $folderName = $permission->name;
            $folderPath = resource_path("js/app/views/$folderName");

            
    
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }
            $this->verifyComponentChildren($permission->children, $folderPath);
        }
    }
    private function verifyComponentChildren($children, $parentPath)
    {
        //dd($children);
        foreach ($children as $child) {
            $childName = explode('.', $child->name);
            $childFolderName = end($childName);

            $childPath = $parentPath . '/' . $childFolderName;

            if (!file_exists($childPath)) {
                mkdir($childPath, 0755, true);
            }

            if ($child->category === 1) {                
                $this->verifyComponentChildren($child->children, $childPath);
            } elseif ($child->category === 2) {
                $indexPath = $childPath . '/index.vue';
                if (!file_exists($indexPath)) {
                    File::put($indexPath, '<template><div>Component in development!</div></template>');
                }   
            }
        }
    }

}
