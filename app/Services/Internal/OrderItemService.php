<?php

namespace App\Services\Internal;

use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderItemService
{
    public function store(Request $data)
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
            $paddedId = str_pad($contador, 10, '0', STR_PAD_LEFT);
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
            $order->order_status = 'Pendiente de Pago';
            $order->order_code_otp = Str::random(6);
            $order->order_total = 0;
            $order->observation = $data->observation;
            $order->delivery_date = $data->delivery_date;
            $order->active = true;
            $order->save();

            // $orderItems = json_decode($data->order_items);

            // foreach ($orderItems as $item) {
            //     $orderItem = new OrderItems();
            //     $orderItem->order_id = $order->id;
            //     $orderItem->product_id = $item->product_id;
            //     $orderItem->quantity = $item->quantity;
            //     $orderItem->price = $item->price;
            //     $orderItem->total = $item->total;
            //     $orderItem->save();
            // }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Orden creada correctamente',
                'order' => $order
            ];
        } catch (QueryException $qe) {

            DB::rollBack();

            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());

            return [
                'status' => false,
                'message' => 'Error al crear la orden',
                'error' => $qe->getMessage()
            ];
        }
    }

}
