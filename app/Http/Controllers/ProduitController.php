<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Http\Requests\StoreProduitRequest;
use App\Http\Requests\UpdateProduitRequest;

class ProduitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produits = Produit::orderBy('id')->get();

        return response()->json([
            'status' => 'success',
            'produits' => $produits
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduitRequest $request)
    {
        $produit = Produit::create($request->all());

        return response()->json([
            'status' => true,
            'message' => "Produit Created successfully!",
            'produit' => $produit
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function show(Produit $produit)
    {
        $produit->find($produit->id);
        if (!$produit) {
            return response()->json(['message' => 'Produit not found'], 404);
        }
        return response()->json($produit, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProduitRequest $request, Produit $produit)
    {
        $produit->update($request->all());

        if (!$produit) {
            return response()->json(['message' => 'Produit not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Produit Updated successfully!",
            'produit' => $produit
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produit  $produit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produit $produit)
    {
        $produit->delete();

        if (!$produit) {
            return response()->json([
                'message' => 'Produit not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Produit deleted successfully'
        ], 200);
    }
}
