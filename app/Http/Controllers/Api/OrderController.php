<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lunar\Facades\CartSession;
use Lunar\Models\Order;

class OrderController extends ApiController
{
    /**
     * Create order from cart
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'country' => 'required|string'
        ]);

        $cart = CartSession::current();

        if (!$cart || $cart->lines->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        try {
            // Create order from cart
            $order = Order::create([
                'cart_id' => $cart->id,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'reference' => 'ORD-' . strtoupper(uniqid()),
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'country' => $request->country,
                'total' => $cart->total,
                'sub_total' => $cart->sub_total,
                'tax_total' => $cart->tax_total,
                'shipping_total' => $cart->shipping_total,
                'discount_total' => $cart->discount_total
            ]);

            // Generate WhatsApp notification link
            $whatsappUrl = $this->generateWhatsAppNotificationLink($order);

            // Clear cart after order creation
            CartSession::destroy();

            return $this->successResponse([
                'order_id' => $order->id,
                'reference' => $order->reference,
                'total' => $order->total,
                'whatsapp_notification_url' => $whatsappUrl,
                'message' => 'Order created successfully. Please send the notification via WhatsApp to confirm your order.'
            ], 'Order created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Generate WhatsApp notification link for the customer
     */
    private function generateWhatsAppNotificationLink($order): string
    {
        $message = $this->createOrderMessage($order);
        $phoneNumber = '+5354173844'; // Tu número de WhatsApp corporativo
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
    }

    /**
     * Create the order message for WhatsApp
     */
    private function createOrderMessage($order): string
    {
        $customerName = $order->first_name . ' ' . $order->last_name;
        $total = number_format($order->total / 100, 2); // Convertir de centavos a dólares

        $message = "🛍️ *NUEVO PEDIDO - {$order->reference}*\n\n";
        $message .= " *Datos del Cliente:*\n";
        $message .= "• Nombre: {$customerName}\n";
        $message .= "• Email: {$order->email}\n";
        $message .= "• Teléfono: {$order->phone}\n";
        $message .= "• Total: \${$total}\n\n";

        $message .= " *Dirección de Envío:*\n";
        $message .= "{$order->address}\n";
        $message .= "{$order->city}, {$order->postcode}\n";
        $message .= "{$order->country}\n\n";

        $message .= " *Productos Solicitados:*\n";
        foreach ($order->lines as $line) {
            $productName = $line->purchasable->product->name ?? 'Producto';
            $lineTotal = number_format($line->total / 100, 2);
            $message .= "• {$productName} (x{$line->quantity}) - \${$lineTotal}\n";
        }

        $message .= "\n📅 Fecha del Pedido: " . $order->created_at->format('d/m/Y H:i') . "\n\n";
        $message .= "Por favor, confirma este pedido. ¡Gracias! 🙏";

        return $message;
    }

    /**
     * Get user orders
     */
    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['lines.purchasable.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference' => $order->reference,
                    'status' => $order->status,
                    'total' => $order->total,
                    'created_at' => $order->created_at,
                    'items_count' => $order->lines->count(),
                    'whatsapp_notification_url' => $this->generateWhatsAppNotificationLink($order)
                ];
            });

        return $this->successResponse($orders, 'Orders retrieved successfully');
    }

    /**
     * Get order details
     */
    public function show($orderId): JsonResponse
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['lines.purchasable.product'])
            ->find($orderId);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        $data = [
            'id' => $order->id,
            'reference' => $order->reference,
            'status' => $order->status,
            'email' => $order->email,
            'first_name' => $order->first_name,
            'last_name' => $order->last_name,
            'phone' => $order->phone,
            'address' => $order->address,
            'city' => $order->city,
            'postcode' => $order->postcode,
            'country' => $order->country,
            'total' => $order->total,
            'sub_total' => $order->sub_total,
            'tax_total' => $order->tax_total,
            'shipping_total' => $order->shipping_total,
            'discount_total' => $order->discount_total,
            'created_at' => $order->created_at,
            'whatsapp_notification_url' => $this->generateWhatsAppNotificationLink($order),
            'items' => $order->lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'product_name' => $line->purchasable->product->name,
                    'variant_name' => $line->purchasable->name,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'total_price' => $line->total
                ];
            })
        ];

        return $this->successResponse($data, 'Order details retrieved successfully');
    }
}
