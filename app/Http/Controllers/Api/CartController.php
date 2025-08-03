<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\ProductVariant;

class CartController extends ApiController
{
    /**
     * Get current cart
     */
    public function show(): JsonResponse
    {
        $cart = CartSession::current();

        if (!$cart) {
            return $this->successResponse([
                'items' => [],
                'total' => 0,
                'sub_total' => 0,
                'tax_total' => 0,
                'shipping_total' => 0,
                'discount_total' => 0
            ], 'Empty cart');
        }

        $data = [
            'id' => $cart->id,
            'items' => $cart->lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'product_id' => $line->purchasable->product_id,
                    'variant_id' => $line->purchasable_id,
                    'name' => $line->purchasable->product->name,
                    'variant_name' => $line->purchasable->name,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'total_price' => $line->total,
                    'thumbnail' => $line->purchasable->product->thumbnail
                ];
            }),
            'total' => $cart->total,
            'sub_total' => $cart->sub_total,
            'tax_total' => $cart->tax_total,
            'shipping_total' => $cart->shipping_total,
            'discount_total' => $cart->discount_total
        ];

        return $this->successResponse($data, 'Cart retrieved successfully');
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::find($request->variant_id);

        if (!$variant) {
            return $this->errorResponse('Product variant not found', 404);
        }

        try {
            CartSession::add($variant, $request->quantity);

            return $this->successResponse(null, 'Item added to cart successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to add item to cart: ' . $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(Request $request, $lineId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = CartSession::current();

        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }

        $line = $cart->lines->find($lineId);

        if (!$line) {
            return $this->errorResponse('Cart line not found', 404);
        }

        try {
            CartSession::updateLine($line, $request->quantity);

            return $this->successResponse(null, 'Cart item updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update cart item: ' . $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($lineId): JsonResponse
    {
        $cart = CartSession::current();

        if (!$cart) {
            return $this->errorResponse('Cart not found', 404);
        }

        $line = $cart->lines->find($lineId);

        if (!$line) {
            return $this->errorResponse('Cart line not found', 404);
        }

        try {
            CartSession::removeLine($line);

            return $this->successResponse(null, 'Item removed from cart successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to remove item from cart: ' . $e->getMessage());
        }
    }

    /**
     * Clear cart
     */
    public function clear(): JsonResponse
    {
        try {
            CartSession::destroy();

            return $this->successResponse(null, 'Cart cleared successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clear cart: ' . $e->getMessage());
        }
    }
}
