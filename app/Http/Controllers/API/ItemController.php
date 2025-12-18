<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $items = Item::all();
            
            return response()->json([
                'status' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('ItemController@index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch items: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        try {
            $item = Item::create([
                'name' => $request->name,
                'type' => $request->type,
                'quantity' => $request->quantity,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, string $id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            $item->name = $request->name;
            $item->type = $request->type;
            $item->quantity = $request->quantity;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete item: ' . $e->getMessage()
            ], 500);
        }
    }
}
