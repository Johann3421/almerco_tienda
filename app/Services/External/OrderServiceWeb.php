<?php

namespace App\Services\External;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderServiceWeb
{
    public function getAllOrders(){
        return Order::select(
            'id',
            'order_code',
            'customer_name',
            'customer_email',
            'customer_phone',
            'customer_address',
            'customer_document_number',
            'customer_document_type',
            'order_status',
            'order_code_otp',
            'order_total',
            'observation',
            'active',
        )
        ->with('getProducts', 'getProduct')
        ->where('active', true)
        ->get();
    }

    public function store(Request $data): array
    {
        try {

            $year = date('Y');

            $contador = 0;

            $order_counter_last = Order::where('order_year', $year)
                                    ->orderBy('order_counter', 'desc')
                                    ->pluck('order_counter')
                                    ->first();

            if ($order_counter_last) {
                $contador = (int)$order_counter_last;
            }

            $contador += 1;
            $paddedId = str_pad($contador, 8, '0', STR_PAD_LEFT);
            $order_code = 'ORD-' . $year . $paddedId;

            DB::beginTransaction();

            $order = new Order();
            $order->order_year = $year;
            $order->order_counter = $contador;
            $order->order_code = $order_code;
            $order->customer_name = $data->customer_name;
            $order->customer_email = $data->customer_email;
            $order->customer_phone = $data->customer_phone;
            $order->customer_address = $data->customer_address;
            $order->customer_document_number = $data->customer_document_number;
            $order->customer_document_type = $data->customer_document_type;
            $order->order_status = $data->order_status;
            $order->order_code_otp = Str::random(6);
            $order->order_total = $data->order_total;
            $order->observation = $data->observation;
            $order->delivery_date = ($data->order_status === 'Completado') ? $data->delivery_data : null;
            $order->active = true;
            $order->save();

            $Total = 0;

            foreach ($data->items as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->amount = $item['amount'];
                $orderItem->unit_price = $item['unit_price'];
                $orderItem->total_price = $item['total'];
                $orderItem->save();

                if ($data->order_status === 'Cancelado' || $data->order_status === 'Reembolsado') {
                    $product = Product::find($item['product_id']);
                    $product->stock += $item['amount'];
                    $product->save();
                } else {

                    $product = Product::find($item['product_id']);

                    if ($product) {

                        if ($product->stock >= $item['amount']) {

                            $product->stock -= $item['amount'];
                            $product->save();
                        } else {
                            DB::rollBack();

                            return [
                                'status' => false,
                                'message' => 'Stock insuficiente para el producto: ' . $item['unit_name'].'. Stock actual: '.$product->stock
                            ];
                        }

                    } else {
                        DB::rollBack();

                        return [
                            'status' => false,
                            'message' => 'Producto no encontrado con ID: ' . $item['product_id']
                        ];
                    }

                }

                $Total += $item['total'];
            }

            $order->order_total = $Total;
            $order->save();

            DB::commit();

            return [
                'status' => true,
                'message' => 'Pedido creado correctamente',
                'order' => $order
            ];
        } catch (QueryException $qe) {

            DB::rollBack();

            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());

            return [
                'status' => false,
                'message' => 'Error al crear el pedido',
                'error' => $qe->getMessage()
            ];
        }
    }

}
