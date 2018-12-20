<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    private static $eagerAssociations = [
        'authorships.author',
        'images',
        'period',
        'legacyInventoryNumbers',
        'productType',
        'productStyle',
        'materials',
        'upholstery',
        'publicationState',
    ];

    public function index()
    {
        // Missing objects when ordering by 'datmaj'.
        $products  = Product::with(static::$eagerAssociations)->orderBy('id')->paginate(100);
        return ProductResource::collection($products);
    }

    /**
     * Display a single product.
     */
    public function show($inventory)
    {
        $product = Product::byInventory($inventory)->with(static::$eagerAssociations)->firstOrFail();
        return new ProductResource($product);
    }
}
