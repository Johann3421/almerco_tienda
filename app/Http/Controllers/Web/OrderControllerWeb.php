<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\External\OrderServiceWeb;
use App\Services\Internal\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendOficialMail;
use Illuminate\Support\Facades\DB;

class OrderControllerWeb extends Controller
{
    protected $orderService;
    protected $productService;

    public function __construct(OrderServiceWeb $orderService, ProductService $productService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
    }

    public function store(StoreRequest $request)
    {
        $response = $this->orderService->store($request);

        if ($response['status']) {
            $order = $response['order'];

            // Enviar correo de confirmación
            $this->enviarCorreo($order);

            return response()->json([
                'message' => $response['message'],
                'order' => $order,
            ], 201);
        } else {
            return response()->json([
                'message' => $response['message'],
            ], 500);
        }
    }

    public function enviarCorreo($order)
    {
        try {
            // Obtener los datos necesarios para el correo
            $order_items = $this->obtenerOrderItems($order->id);

            $data = [
                'client_name' => $order->customer_name,
                'order_code' => $order->order_code,
                'order_date' => $order->created_at->format('d/m/Y'),
                'client_document' => $order->customer_document_number,
                'order_items' => $order_items,
                'order_subtotal' => number_format($order->order_total, 2, '.', ','),
                'order_igv' => 0.00, // Ajusta este valor según sea necesario
                'order_total' => number_format($order->order_total, 2, '.', ','),
            ];

            // Validar si el correo del cliente existe
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new SendOficialMail($data));
            } else {
                Log::warning("La orden #{$order->order_code} no tiene correo de cliente.");
            }
        } catch (\Exception $e) {
            Log::error("Error al enviar correo: " . $e->getMessage());
        }
    }

    public function obtenerOrderItems($orderId)
    {
        $tipoDeCambio = Setting::getValue('tipo_de_cambio');
        // Recuperar los items asociados a la orden con los datos del producto
        $items = OrderItem::with('getProduct') // Usa eager loading
            ->where('order_id', $orderId)
            ->get();

        // Verifica qué items se están recuperando
        Log::info("Items obtenidos para Order ID {$orderId}:", $items->toArray());

        return $items->map(function ($item) use ($tipoDeCambio) {
            return [
                'product_name' => $item->getProduct ? $item->getProduct->name : 'Producto no encontrado', // Accede al nombre del producto
                'product_quantity' => $item->amount, // Usamos 'amount' como cantidad
                'product_price' => $item->getProduct ? number_format(floatval($item->getProduct->price) * floatval($tipoDeCambio), 2, '.', ',') : '0.00', // Usamos el precio del producto
                'product_total' => number_format($item->total_price, 2, '.', ','), // Usamos 'total_price' como total
            ];

        })->toArray();
    }


    public function sendConfirmation($orderId)
    {
        // Cargar la orden
        $order = Order::find($orderId);

        // Verificar si la orden existe
        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        // Obtener los items de la orden
        $orderItems = $this->obtenerOrderItems($orderId);

        // Verificar si hay items en la orden
        if (empty($orderItems)) {
            return response()->json(['message' => 'No se encontraron items para la orden'], 400);
        }

        // Envía el correo
        try {
            Mail::to('ventas@grupoalmerco.com.pe')->send(new SendOficialMail([
                'client_name' => $order->customer_name,
                'order_code' => $order->order_code,
                'order_date' => now()->format('d/m/Y'),
                'client_document' => $order->customer_document_number,
                'order_items' => $orderItems,
                'order_subtotal' => $order->order_total,
                'order_igv' => 0,
                'order_total' => $order->order_total,
            ]));

            return response()->json(['message' => 'Correo enviado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al enviar el correo: ' . $e->getMessage()], 500);
        }
    }

    public function testProducts($orderId)
    {
        $order = Order::with('getProducts')->find($orderId);

        if ($order) {
            return response()->json($order->getProducts);
        }

        return response()->json(['message' => 'Orden no encontrada'], 404);
    }

    public function show($id)
    {
        // Obtener la orden por ID
        $order = Order::find($id);

        // Verificar si la orden existe
        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        // Obtener el total de productos de la orden
        $totalProducts = $order->getProductsTotal2();

        // Devolver la información de la orden y el total de productos
        return response()->json([
            'order' => $order,
            'total_products' => $totalProducts,
        ]);
    }
}
