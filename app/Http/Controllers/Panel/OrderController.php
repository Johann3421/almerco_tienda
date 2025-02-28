<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\Internal\OrderService;
use App\Services\Internal\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected $orderService;
    protected $orderItemService;

    protected $productService;
    public function __construct(OrderService $orderService, ProductService $productService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
    }

    public function index()
    {
        $products = Product::select('id', 'name', 'price', 'stock')
            ->where('active', true)
            ->where('stock', '>', 0)
            ->get();

        return Inertia::render('Panel/Orders/Index', [
            'products' => $products
        ]);
    }

    public function load(Request $request)
    {
        $query = Order::query();

        if ($search = $request->input('search')) {
            $query->where('order_code', 'like', "%{$search}%");
        }

        if ($sortBy = $request->input('sortBy')) {
            foreach ($sortBy as $sort) {
                $query->orderBy($sort['key'], $sort['order']);
            }
        }

        $query->with('getProducts.getProduct');

        $orders = $query->paginate($request->input('itemsPerPage', 10));

        return [
            'orders' => $orders
        ];
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
       $response = $this->orderService->store($request);

        if ($response['status']) {

            return response()->json([
                'message' => $response['message'],
            ], 201);

        } else {
            return response()->json([
                'message' => $response['message'],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $response = $this->orderService->updateStatus($request);

        if ($response['status']) {

            return response()->json([
                'message' => $response['message'],
            ], 200);

        } else {
            return response()->json([
                'message' => $response['message'],
            ], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        $response = $this->orderService->updateOrder($request);

        if ($response['status']) {

            return response()->json([
                'message' => $response['message'],
            ], 200);

        } else {
            return response()->json([
                'message' => $response['message'],
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
