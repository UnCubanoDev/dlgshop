@component('mail::message')
# Nuevo Pedido Recibido

Se ha recibido un nuevo pedido con referencia: **{{ $order->reference }}**

**Detalles del Cliente:**
- Nombre: {{ $order->first_name }} {{ $order->last_name }}
- Email: {{ $order->email }}
- Teléfono: {{ $order->phone }}
- Total: ${{ number_format($order->total / 100, 2) }}

**Dirección de Envío:**
{{ $order->address }}
{{ $order->city }}, {{ $order->postcode }}
{{ $order->country }}

@component('mail::button', ['url' => $whatsappUrl])
📱 Enviar Notificación por WhatsApp
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
