<?php

namespace App\Services\Internal;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function getAllProducts()
    {
        return Product::select(
            'id',
            'name',
            'discount',
            'price',
            'previous_price',
            'stock',
            'on_sale',
            'active',
        )->where('active', true)->get();
    }

    public function queryProducts($search)
    {
        $query = Product::query();

        if (isset($search['name'])) {
            $query->where('name', 'like', '%' . $search['name'] . '%');
        }

        if (isset($search['calories'])) {
            $query->where('calories', '>=', $search['calories']);
        }

        return $query;
    }


    public function getAllProductsForWeb()
    {
        return Product::select(
            'id',
            'name',
            'discount',
            'price',
            'previous_price',
            'stock',
            'on_sale',
            'active',
        )->where('active', true)->where('on_sale', true)->get();
    }

    public function getAllProductsForWebByCategory($id)
    {
        return Product::select(
            'id',
            'name',
            'discount',
            'price',
            'previous_price',
            'stock',
            'on_sale',
            'active',
        )->where('active', true)->where('on_sale', true)->whereHas('subgroups', function ($query) use ($id) {
            $query->where('subgroup_id', $id);
        })->get();
    }

    public function getLastInsertedProductId(): int | null
    {
        return Product::max('id');
    }

    public function getProductById($id): Product
    {
        return Product::findOrFail($id);
    }

    public function getProductByIdForWeb($id): Product
    {
        return Product::where('active', true)->where('on_sale', true)->findOrFail($id);
    }

    public function getProductBySlugForWeb($slug): Product
    {
        return Product::where('active', true)->where('on_sale', true)->where('slug', $slug)->first();
    }

    public function getProductByIdForEdit($id): Product
    {
        return Product::with('subgroups', 'filters_items', 'images', 'specifications')->findOrFail($id);
    }

    public function updateVisibility($id, $on_sale): bool
    {
        try {

            DB::beginTransaction();

            $product = $this->getProductById($id);
            $product->on_sale = $on_sale;
            $product->save();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            return false;
        }
    }

    public function store(Request $request): int | null
    {
        $lastId = $this->getLastInsertedProductId();

        if ($lastId === null) {
            $lastId = 0;
        }

        $paddedId = str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);

        $sku = 'SKU-' . $paddedId;

        try {
            $token = str::uuid();

            DB::beginTransaction();

            $product = new Product();
            $product->sku = $sku;
            $product->part_number = $request->part_number;
            $product->description = $request->description;
            $product->name = $request->name;
            $product->slug = $request->slug;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->discount = $request->discount;
            $product->previous_price = $request->previous_price;
            $product->manufacturer_url = $request->manufacturer_url;
            $product->active = $request->active;
            $product->on_sale = $request->on_sale;
            $product->on_promotion = $request->on_promotion;
            $product->token = $token;
            $product->save();

            $product->subgroups()->attach($request->subgroups);
            $product->filters()->attach($request->filters);

            if (isset($request->specifications)) {
                foreach ($request->specifications as $specification) {
                    $product->specifications()->create([
                        'key' => $specification['key'],
                        'value' => $specification['value'],
                    ]);
                }
            }

            DB::commit();

            return $product->id;
        } catch (QueryException $qe) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            DB::rollBack();
            return null;
        }
    }

    public function update(Request $request, $id): int | null
    {
        try {
            DB::beginTransaction();

            $product = $this->getProductById($id);
            $product->sku = $request->sku;
            $product->part_number = $request->part_number;
            $product->description = $request->description;
            $product->name = $request->name;
            $product->slug = $request->slug;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->discount = $request->discount;
            $product->previous_price = $request->previous_price;
            $product->manufacturer_url = $request->manufacturer_url;
            $product->on_sale = $request->on_sale;
            $product->on_promotion = $request->on_promotion;
            $product->active = $request->active;
            $product->save();

            $product->subgroups()->sync(ids: $request->subgroups);
            $product->filters()->sync(ids: $request->filters);

            if (isset($request->specifications)) {
                $product->specifications()->delete();

                foreach ($request->specifications as $specification) {
                    $product->specifications()->create([
                        'key' => $specification['key'],
                        'value' => $specification['value'],
                    ]);
                }
            }

            DB::commit();

            return $product->id;
        } catch (\Throwable $th) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $th->getMessage());
            DB::rollBack();

            return null;
        }
    }

    public function destroy($id): bool
    {
        try {
            DB::beginTransaction();

            $product = $this->getProductById($id);

            $folderPath = 'products/' . $product->token;

            if (Storage::exists('public/' . $folderPath)) {
                Storage::deleteDirectory('public/' . $folderPath);
            }

            ProductImage::where('product_id', $product->id)->delete();

            $product->subgroups()->detach();
            $product->filters()->detach();

            $product->active = false;
            $product->save();

            DB::commit();

            return true;
        } catch (QueryException $qe) {
            Log::error(__METHOD__ . '::' . __LINE__ . '::' . $qe->getMessage());
            DB::rollBack();
            return false;
        }
    }
}
