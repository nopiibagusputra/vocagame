<?php

namespace App\Http\Controllers\Api\V1\Products;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\AddProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductsResource;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Product::all();
        return ProductsResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddProductRequest $request)
    {
        Product::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'prices'        => $request->prices,
            'available'     => $request->available,
        ]);

        return response()->json([
            'message' => 'Product add successfully'
          ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $data = Product::findOrFail($id);
            $data->update($request->all());

            return new ProductsResource($data);
        } catch (Exception $e) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = Product::findOrFail($id);
            $data->delete();
    
            return response()->json([
                'message' => 'Product deleted successfully'
            ], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'Product not found'], 404);
        }       
    }
}
