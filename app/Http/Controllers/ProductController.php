<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Product;
use Illuminate\Http\Request;

use PhpParser\Node\Stmt\Catch_;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAllProducts']]);
    }
    
    
    public function addProduct(Request $request)
    {
        $user = auth()->user();
        if(!$user->hasPermissionTo('product-create')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add product'
            ], 200);
        }
        $validateData = $request->validate([
            'title'        => 'required|string|max:200',
            'description'  => 'required|min:10|string',
            'price'        => 'required|numeric',
            'categorie_id' => 'required|numeric'
        ]);
        if (Categorie::count() === 0) {
            return response()->json([
                'status' => 'error',
                'messgae' => 'you should add a categorie before adding a product'
            ]);
        }
        $product = Product::where('title', $request->title)->first();
        if ($product) {
            return response()->json([
                'message' => 'this title of product already exist please enter another one'
            ]);
        }
        $categorie = Categorie::where('id', $request->categorie_id)->first();
        if (!$categorie) {
            return response()->json([
                'status' => 'error',
                'message' => 'categorie that you entered dosn\'t exsit please enter avlaid categorie'
            ]);
        }
        $validateData['user_id'] = $user->id;
        $product = Product::create($validateData);
        return response()->json([
            'status'  => 'success',
            'message' => 'product has been created successfuly',
            'product' => $product
        ]);
    }
    

    public function updateProduct(Request $request)
    {
        $product = Product::find($request->id);
        $user = auth()->user();
        if(!$user->hasPermissionTo('product-edit') || (!$user->hasRole('admin') && $user->id != $product->user_id )){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add product'
            ], 200);
        }

        $request->validate([
            'id'           => 'required|numeric',
            'title'        => 'string|max:200',
            'description'  => 'min:10|string',
            'price'        => 'numeric',
            'categorie_id' => 'numeric'
        ]);
        if ($product) {
            if ($request->has('title')) {
                $product->title = $request->title;
            }
            if ($request->has('description')) {
                $product->description = $request->description;
            }
            if ($request->has('price')) {
                $product->price = $request->price;
            }
            if ($request->has('categorie_id')) {
                $categorie = Categorie::find($request->categorie_id);
                if (!$categorie) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'the ctaegorie that you enter not found '
                    ], 404);
                }
                $product->categorie_id = $request->categorie_id;
            }
            $product->save();
            return response()->json([
                'status'  => 'success',
                'message' => 'product has been updated succeessfuly',
                'product' => $product
            ], 200);
        }
        return response()->json([
            'status'  =>  'error',
            'message' =>  'this id dosn\'t exist '
        ], 404);
    }
    

    public function deleteProduct(Request $request)
    {
        $user = auth()->user();
        if(!$user->hasPermissionTo('product-delete')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add product'
            ], 200);
        }
        $request->validate([
            'id' => 'required|numeric'
        ]);
        $product = Product::find($request->id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'messge' => 'the id that you enter not exist '
            ], 404);
        }
        $product->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'product deleted successfuly',
        ], 200);
    }
    

    public function getAllProducts()
    {
        $products = Product::all();
        if ($products->count() > 0) {
            return response()->json([
                'status' => 'success',
                'message' => $products
            ],200);
        }
        return response()->json([
            'message' => 'no data available'
        ],404);
    }
}