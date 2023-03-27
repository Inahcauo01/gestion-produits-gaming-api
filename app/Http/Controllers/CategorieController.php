<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function addCategorie(Request $request)
   {
      $user = auth()->user();
        if(!$user->hasPermissionTo('categorie-create')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add categorie'
            ], 200);
        }
      $request->validate([
         'name' => 'required|string|max:100'
      ]);
      $categorie = Categorie::where('name', $request->name)->first();
      if ($categorie) {
         return response()->json([
            'status' => 'error',
            'message' => 'Category already exists'
         ], 409);
      } else {
         $categorie = Categorie::create([
            'name' => $request->name
         ]);
         return response()->json([
            'status' => 'success',
            'message' => 'categorie created successfuly',
            'Categorie' => $categorie
         ], 200);
      }
   }

   public function deleteCategorie(Request $request)
   {
      $user = auth()->user();
        if(!$user->hasPermissionTo('categorie-delete')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to delete categorie'
            ], 200);
        }
      $request->validate([
         'id'  => 'required|integer',
      ]);
      $categorie = Categorie::find($request->id);
      if ($categorie) {
         $categorie->delete();
         return response()->json([
            'status' => 'success',
            'message' => 'categorie has been deleted successfuly'
         ], 200);
      } else {
         return response()->json([
            'status'  => 'error',
            'message' => 'categorie not found'
         ], 404);
      }
   }

   public function updateCategorie(Request $request)
   {
      $user = auth()->user();
        if(!$user->hasPermissionTo('categorie-edit')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to update categorie'
            ], 200);
        }
      $request->validate([
         'id' => 'required|integer '
      ]);
      $categorie = Categorie::find($request->id);
      if ($categorie) {
         if ($request->has('name')) {
            $categorie->name = $request->name;
         }
         $categorie->save();
         return response()->json([
            'status' => 'success',
            'message' => 'categorie updated successfuly',
            'categorie' => $categorie
         ], 200);
      } else {
         return response()->json([
            'status' => 'error',
            'message' => 'categorie not found'
         ], 404);
      }
   }

   public function getAllCategories()
   {
      $userauth=auth()->user();
      if(!$userauth->hasPermissionTo('categorie-list')){
          return response()->json([
              'status' => 'error',
              'message' => 'You dont have permission to show categories'
          ], 200);
      }
      $categories = Categorie::all('id', 'name');
      if ($categories->count() > 0) {
         return response()->json([
            'status' => 'success',
            'categories' => $categories
         ]);
      } else {
         return response()->json([
            'message' => 'no ctegorie available'
         ]);
      }
   }
    
}
