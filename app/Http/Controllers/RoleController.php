<?php
    
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

    
class RoleController extends Controller
{
    
    public function addRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->can('role-create')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add role'
            ], 200);
        }
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permission);
    
        return response()->json([
            'status' => 'success',
            'message' => 'role created successfuly'
        ]);
    }
    
    public function showRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-list')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to show roles'
            ], 200);
        }
        $request->validate([
            'id' => 'required'
        ]);
        $role = Role::find($request->id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$request->id)
            ->get();
    
        return response()->json([
            'status' => 'success',
            'roles' => $role,
            'rolePermissions' => $rolePermissions
        ]);
         
    }
    
    public function updateRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-edit')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to update role'
            ], 200);
        }
        $request->validate( [
            'id'   => 'required',
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($request->id);
        $role->name = $request->name;
        $role->save();
    
        $role->syncPermissions($request->permission);
    
        return
        response()->json([
           'status' => 'success',
           'message' => 'Role updated successfully'
        ]);
    }

    public function deleteRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-delete')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to delete role'
            ], 200);
        }
        $request->validate([
            'id' => 'required'
        ]);
        Role::where('id',$request->id)->delete();
        return response()->json([
            'status' => 'succcess',
            'message' => 'Role deleted successfully'
        ]); 
    }
}