<?php

namespace App\Services\Internal;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
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

    public function updateStatus(Request $data): array
    {
        try {
            DB::beginTransaction();

            $order = Order::where('order_code', $data->code)->with('getProducts')->first();

            if ($order) {
                $order->order_status = $data->order_status;
                $order->delivery_date = ($data->order_status === 'Completado') ? $data->delivery_data : null;
                $order->save();

                if ($data->order_status === 'Cancelado' || $data->order_status === 'Reembolsado') {
                    foreach ($order->getProducts as $item) {
                        $product = Product::find($item->product_id);
                        $product->stock += $item->amount;
                        $product->save();
                    }
                }

                DB::commit();

                return [
                    'status' => true,
                    'message' => 'Estado del pedido actualizado correctamente',
                ];
            } else {
                DB::rollBack();

                return [
                    'status' => false,
                    'message' => 'Pedido con código ( ' . $data->code . ' ) no encontrado',
                ];
            }
        } catch (QueryException $qe) {
            DB::rollBack();

            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());

            return [
                'status' => false,
                'message' => 'Error al actualizar el estado del pedido',
                'error' => $qe->getMessage()
            ];
        }
    }

    public function updateOrder(Request $data)
    {
        try {
            DB::beginTransaction();

            $order = Order::where('order_code', $data->code)->with('getProducts')->first();
            // return $data;
            if ($order) {

                $order->customer_name = $data->customer_name;
                $order->customer_email = $data->customer_email;
                $order->customer_phone = $data->customer_phone;
                $order->customer_address = $data->customer_address;
                $order->customer_document_number = $data->customer_document_number;
                $order->customer_document_type = $data->customer_document_type;
                $order->order_status = $data->order_status;
                $order->delivery_date = ($data->order_status === 'Completado') ? $data->delivery_data : null;
                $order->order_total = $data->order_total;
                $order->observation = $data->observation;
                $order->active = true;
                $order->save();

                foreach ($order->getProducts as $item) {
                    $product = Product::find($item->product_id);
                    $product->stock += $item->amount;
                    $product->save();
                }

                $Total = 0;

                foreach ($data->items as $item) {

                    if ($item['id'] === 0) {

                        $newItem = new OrderItem();
                        $newItem->order_id = $order->id;
                        $newItem->product_id = $item['product_id'];
                        $newItem->amount = $item['amount'];
                        $newItem->unit_price = $item['unit_price'];
                        $newItem->total_price = $item['total'];
                        $newItem->active = true;
                        $newItem->save();

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

                        $Total += $item['total'];

                    } else {

                        $orderItem = OrderItem::find($item['id']);
                        $orderItem->amount = $item['amount'];
                        $orderItem->unit_price = $item['unit_price'];
                        $orderItem->total_price = $item['total'];
                        $orderItem->save();

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

                        $Total += $item['total'];
                    }

                }

                $order->order_total = $Total;
                $order->save();

                DB::commit();

                return [
                    'status' => true,
                    'message' => 'El pedido se actualizó correctamente',
                ];
            } else {
                DB::rollBack();

                return [
                    'status' => false,
                    'message' => 'Pedido con código ( ' . $data->code . ' ) no encontrado',
                ];
            }
        } catch (QueryException $qe) {
            DB::rollBack();

            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());

            return [
                'status' => false,
                'message' => 'Error al actualizar el pedido',
                'error' => $qe->getMessage()
            ];
        }
    }

    // public function getAllProducts()
    // {
    //     return Product::select(
    //         'id',
    //         'name',
    //         'discount',
    //         'price',
    //         'previous_price',
    //         'stock',
    //         'on_sale',
    //         'active',
    //     )->where('active', true)->get();
    // }

    // public function queryProducts($search)
    // {
    //     $query = Product::query();

    //     if (isset($search['name'])) {
    //         $query->where('name', 'like', '%' . $search['name'] . '%');
    //     }

    //     if (isset($search['calories'])) {
    //         $query->where('calories', '>=', $search['calories']);
    //     }

    //     return $query;
    // }
    // public function getAllProductsForWeb()
    // {
    //     return Product::select(
    //         'id',
    //         'name',
    //         'discount',
    //         'price',
    //         'previous_price',
    //         'stock',
    //         'on_sale',
    //         'active',
    //     )->where('active', true)->where('on_sale', true)->get();
    // }

    // public function getAllProductsForWebByCategory($id)
    // {
    //     return Product::select(
    //         'id',
    //         'name',
    //         'discount',
    //         'price',
    //         'previous_price',
    //         'stock',
    //         'on_sale',
    //         'active',
    //     )->where('active', true)->where('on_sale', true)->whereHas('subgroups', function ($query) use ($id) {
    //         $query->where('subgroup_id', $id);
    //     })->get();
    // }

    // public function getLastInsertedProductId(): int | null
    // {
    //     return Product::max('id');
    // }

    // public function getProductById($id): Product
    // {
    //     return Product::findOrFail($id);
    // }

    // public function getProductByIdForWeb($id): Product
    // {
    //     return Product::where('active', true)->where('on_sale', true)->findOrFail($id);
    // }

    // public function getProductBySlugForWeb($slug): Product
    // {
    //     return Product::where('active', true)->where('on_sale', true)->where('slug', $slug)->first();
    // }

    // public function getProductByIdForEdit($id): Product
    // {
    //     return Product::with('subgroups', 'filters_items', 'images')->findOrFail($id);
    // }

    // public function updateVisibility($id, $on_sale): bool
    // {
    //     try {

    //         DB::beginTransaction();

    //         $product = $this->getProductById($id);
    //         $product->on_sale = $on_sale;
    //         $product->save();

    //         DB::commit();

    //         return true;
    //     } catch (QueryException $qe) {
    //         DB::rollBack();
    //         Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
    //         return false;
    //     }
    // }

    // public function store(Request $request): bool
    // {
    //     $data = $request->all();

    //     $on_sale = filter_var($data['on_sale'], FILTER_VALIDATE_BOOLEAN);
    //     $on_promotion = filter_var($data['on_promotion'], FILTER_VALIDATE_BOOLEAN);
    //     $active = filter_var($data['active'], FILTER_VALIDATE_BOOLEAN);
    //     $subgroups = explode(',', $data['subgroups']);
    //     $filters = explode(',', $data['filters']);

    //     $lastId = $this->getLastInsertedProductId();

    //     if ($lastId === null) {
    //         $lastId = 0;
    //     }

    //     $paddedId = str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);

    //     $sku = 'SKU-' . $paddedId;

    //     try {
    //         $token = str::uuid();

    //         DB::beginTransaction();

    //         $product = new Product();
    //         $product->sku = $sku;
    //         $product->part_number = $request->part_number;
    //         $product->name = $request->name;
    //         $product->slug = $request->slug;
    //         $product->specification = $request->specification;
    //         $product->description = $request->description;
    //         $product->price = $request->price;
    //         $product->stock = $request->stock;
    //         $product->discount = $request->discount;
    //         $product->previous_price = $request->previous_price;
    //         $product->active = $active;
    //         $product->on_sale = $on_sale;
    //         $product->on_promotion = $on_promotion;
    //         $product->token = $token;
    //         $product->save();

    //         if ($request->hasFile('image1')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image1->getClientOriginalExtension();
    //             $route = 'products/' . $token;

    //             $image = $request->file('image1');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             $product_image = new ProductImage();
    //             $product_image->product_id = $product->id;
    //             $product_image->file_path = $route;
    //             $product_image->file = $filename;
    //             $product_image->save();
    //         }

    //         if ($request->hasFile('image2')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image2->getClientOriginalExtension();
    //             $route = 'products/' . $token;

    //             $image = $request->file('image2');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             $product_image = new ProductImage();
    //             $product_image->product_id = $product->id;
    //             $product_image->file_path = $route;
    //             $product_image->file = $filename;
    //             $product_image->save();
    //         }

    //         if ($request->hasFile('image3')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image3->getClientOriginalExtension();
    //             $route = 'products/' . $token;

    //             $image = $request->file('image3');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             $product_image = new ProductImage();
    //             $product_image->product_id = $product->id;
    //             $product_image->file_path = $route;
    //             $product_image->file = $filename;
    //             $product_image->save();
    //         }

    //         if ($request->hasFile('image4')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image4->getClientOriginalExtension();
    //             $route = 'products/' . $token;

    //             $image = $request->file('image4');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             $product_image = new ProductImage();
    //             $product_image->product_id = $product->id;
    //             $product_image->file_path = $route;
    //             $product_image->file = $filename;
    //             $product_image->save();
    //         }

    //         $product->subgroups()->attach($subgroups);
    //         $product->filters()->attach($filters);

    //         DB::commit();

    //         return true;
    //     } catch (QueryException $qe) {
    //         Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
    //         DB::rollBack();
    //         return false;
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     $data = $request->all();

    //     $on_sale = filter_var($data['on_sale'], FILTER_VALIDATE_BOOLEAN);
    //     $on_promotion = filter_var($data['on_promotion'], FILTER_VALIDATE_BOOLEAN);
    //     $active = filter_var($data['active'], FILTER_VALIDATE_BOOLEAN);
    //     $images = explode(',', $data['images']);
    //     $subgroups = explode(',', $data['subgroups']);
    //     $filters = explode(',', $data['filters']);

    //     try {
    //         DB::beginTransaction();

    //         $product = $this->getProductById($id);
    //         $product->sku = $request->sku;
    //         $product->part_number = $request->part_number;
    //         $product->description = $request->description;
    //         $product->name = $request->name;
    //         $product->slug = $request->slug;
    //         $product->specification = $request->specification;
    //         $product->price = $request->price;
    //         $product->stock = $request->stock;
    //         $product->discount = $request->discount;
    //         $product->previous_price = $request->previous_price;
    //         $product->active = $active;
    //         $product->on_sale = $on_sale;
    //         $product->on_promotion = $on_promotion;
    //         $product->save();

    //         if ($request->hasFile('image1')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image1->getClientOriginalExtension();
    //             $route = 'products/' . $product->token;

    //             $image = $request->file('image1');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             if (isset($images[0]) && !empty($images[0])){

    //                 $product_image = ProductImage::find($images[0]);

    //                 if (Storage::exists('public/' . $product_image->file_path . '/' . $product_image->file)) {
    //                     Storage::delete('public/' . $product_image->file_path . '/' . $product_image->file);
    //                 }

    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             } else {
    //                 $product_image = new ProductImage();
    //                 $product_image->product_id = $product->id;
    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             }
    //         }

    //         if ($request->hasFile('image2')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image2->getClientOriginalExtension();
    //             $route = 'products/' . $product->token;

    //             $image = $request->file('image2');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             if (isset($images[1]) && !empty($images[1])){

    //                 $product_image = ProductImage::find($images[1]);

    //                 if (Storage::exists('public/' . $product_image->file_path . '/' . $product_image->file)) {
    //                     Storage::delete('public/' . $product_image->file_path . '/' . $product_image->file);
    //                 }

    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             } else {
    //                 $product_image = new ProductImage();
    //                 $product_image->product_id = $product->id;
    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             }
    //         }

    //         if ($request->hasFile('image3')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image3->getClientOriginalExtension();
    //             $route = 'products/' . $product->token;

    //             $image = $request->file('image3');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));


    //             if (isset($images[2]) && !empty($images[2])){

    //                 $product_image = ProductImage::find($images[2]);

    //                 if (Storage::exists('public/' . $product_image->file_path . '/' . $product_image->file)) {
    //                     Storage::delete('public/' . $product_image->file_path . '/' . $product_image->file);
    //                 }

    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             } else {
    //                 $product_image = new ProductImage();
    //                 $product_image->product_id = $product->id;
    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             }
    //         }

    //         if ($request->hasFile('image4')) {
    //             $filename = time() . '-' . Str::random(10) . '.' . $request->image4->getClientOriginalExtension();
    //             $route = 'products/' . $product->token;

    //             $image = $request->file('image4');

    //             Storage::put('public/' . $route . '/' . $filename, file_get_contents($image));

    //             if (isset($images[3]) && !empty($images[3])){

    //                 $product_image = ProductImage::find($images[3]);

    //                 if (Storage::exists('public/' . $product_image->file_path . '/' . $product_image->file)) {
    //                     Storage::delete('public/' . $product_image->file_path . '/' . $product_image->file);
    //                 }

    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             } else {
    //                 $product_image = new ProductImage();
    //                 $product_image->product_id = $product->id;
    //                 $product_image->file_path = $route;
    //                 $product_image->file = $filename;
    //                 $product_image->save();
    //             }
    //         }

    //         $product->subgroups()->sync($subgroups);
    //         $product->filters()->sync($filters);

    //         DB::commit();

    //         return true;
    //     } catch (QueryException $qe) {
    //         Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
    //         DB::rollBack();
    //         return false;
    //     }
    // }

    // public function destroy($id): bool
    // {
    //     try {
    //         DB::beginTransaction();

    //         $product = $this->getProductById($id);

    //         $folderPath = 'products/' . $product->token;

    //         if (Storage::exists('public/' . $folderPath)) {
    //             Storage::deleteDirectory('public/' . $folderPath);
    //         }

    //         ProductImage::where('product_id', $product->id)->delete();

    //         $product->subgroups()->detach();
    //         $product->filters()->detach();

    //         $product->active = false;
    //         $product->save();

    //         DB::commit();

    //         return true;
    //     } catch (QueryException $qe) {
    //         Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
    //         DB::rollBack();
    //         return false;
    //     }
    // }
}
