<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Product;
use App\Models\Group;
use App\Models\Subgroup;
use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterItem;
use App\Models\Banner;
use App\Models\ProductImage;
use App\Models\ProductSpecification;

class ProductControllerWeb extends Controller
{
    public function index()
    {
        $subgroups = Subgroup::with(['products' => function ($query) { $query->where('active', true)->where('on_sale', true); }, 'group'])->get();

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();

        $banners = Banner::all();
        $images = ProductImage::all();
        $brands = Filter::with(['filterItems.products'])->where('active', true)->where('id', 1)->get();
        // return $brands;
        return Inertia::render('Web/Web', [
            'subgroups' => $subgroups,
            'categories' => $categories,
            'banners' => $banners,
            'images' => $images,
            'brands' => $brands
        ]);
    }
    public function showProduct($slug)
    {
        $producto = Product::with('filterItems')->where('slug', $slug)->firstOrFail();
        $subgroups = Subgroup::with(['products' => function ($query) { $query->where('active', true)->where('on_sale', true); }, 'group', 'category'])->get();
        $productSpecifications = ProductSpecification::where('product_id', $producto->id)->get();
        $products = collect();
        $productref = collect();
        $firstLetter = substr($producto->name, 0, 1);
        $subgroup = collect();
        foreach ($subgroups as $listproduct) {
            foreach ($listproduct->products as $product) {
                // Verificar si el nombre del producto comienza con la misma letra
                if ($product->id !== $producto->id && stripos($product->name, $firstLetter) === 0)
                {
                    $products->push($product);
                }
                elseif($product->id == $producto->id)
                {
                    $productref->push($listproduct);
                    $subgroup = Subgroup::where('id', $listproduct->id)->where('active', true)->get();
                }
            }
        }

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();

        $productimages = ProductImage::all()->where('product_id', $producto->id);
        $images = ProductImage::all();
        $brands = Filter::with(['filterItems.products'])->where('active', true)->where('id', 1)->get();
        // return $productSpecifications;
        return Inertia::render('Web/Products/Index', [
            'producto' => $producto,
            'subgroups' => $subgroups,
            'categories' => $categories,
            'productimages' => $productimages,
            'images' => $images,
            'products' => $products,
            'productref' => $productref,
            'subgroup' => $subgroup,
            'brands' => $brands,
            'productSpecifications' => $productSpecifications
        ]);
    }

    public function showForCategory($slug)
    {

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();

        $category_id = Category::where('slug', $slug)->firstOrFail()->id;
        $subgroups = Subgroup::with(['products' => function ($query) { $query->where('active', true)->where('on_sale', true); }, 'group'])->where('category_id', $category_id)->get();
        $products = Product::whereHas('subgroups.group.category', function ($query) use ($category_id) {
            $query->where('id', $category_id);
        })->has('filterItems')->with('filterItems')->where('active', true)->get();
        $images = ProductImage::all();
        $brands = Filter::with(['filterItems.products'])->where('active', true)->where('id', 1)->get();
        // return $images;
        return Inertia::render('Web/Categories/Index', [
            'subgroups' => $subgroups,
            'categories' => $categories,
            'products' => $products,
            'images' => $images,
            'brands' => $brands
        ]);
    }
    public function showForGroup($slug)
    {
        $groupid = Group::where('slug', $slug)->firstOrFail()->id;
        $subgroups = Subgroup::with(['products' => function ($query) { $query->where('active', true)->where('on_sale', true); }, 'group'])->where('group_id', $groupid)->get();

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();

        $products = Product::whereHas('subgroups', function ($query) use ($groupid) { // Cambia $groupId a $id
            $query->whereHas('group', function ($query) use ($groupid) { // Cambia $groupId a $id
                $query->where('group_id', $groupid);
            });
        })->has('filterItems')->with('filterItems')->where('active', true)->get();

        $images = ProductImage::all();
        $brands = Filter::with(['filterItems.products'])->where('active', true)->where('id', 1)->get();
        // return $categories;
        return Inertia::render('Web/Groups/Index', [
            'subgroups' => $subgroups,
            'categories' => $categories,
            'products' => $products,
            'images' => $images,
            'brands' => $brands
        ]);
    }
    public function showForSubgroup($slug)
    {
        $subgroup = Subgroup::with(['subgroups', 'products' => function ($query) { $query->where('active', true)->where('on_sale', true); }])->where('slug', $slug)->firstOrFail();

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();

        $products = Product::whereHas('subgroups', function ($query) use ($slug) { $query->where('slug', $slug); })->has('filterItems')->with('filterItems')->where('active', true)->get();
        $images = ProductImage::all();
        $brands = Filter::with(['filterItems.products'])->where('active', true)->where('id', 1)->get();
        // return $categories;
        return Inertia::render('Web/SubGroups/Index', [
            'subgroup' => $subgroup,
            'categories' => $categories,
            'products' => $products,
            'images' => $images,
            'brands' => $brands
        ]);
    }
    public function showBays()
    {

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();
        // $products = Product::where('active', true)->where('on_sale', true)->get();
        $products = Product::where('active', true)->where('on_sale', true)->where('on_promotion', true)->get();
        $images = ProductImage::all();
        return Inertia::render('Web/Pasarelas/Index', [
            'categories' => $categories,
            'images' => $images,
            'products' => $products
        ]);
    }
    public function filterProducts(Request $request)
    {
        $filters = $request->input('filters');
        $idtipo = $request->input('idtipo');
        $tipo = $request->input('tipo');

        if (empty($filters)) {
            return response()->json(['products' => []]);
        }

        // Agrupar los filtros por filter_id
        $groupedFilters = [];
        foreach ($filters as $filter) {
            $groupedFilters[$filter['filter_id']][] = $filter['id'];
        }

        // Inicializar la consulta de productos
        $productsQuery = Product::query();

        // Iterar sobre cada grupo de filtros
        foreach ($groupedFilters as $filterIds) {
            $productsQuery->whereHas('filterItems', function ($query) use ($filterIds) {
                $query->whereIn('filter_item_id', $filterIds);
            });
        }

        // Obtener productos que coincidan con todos los grupos de filtros
        $productsFilters = $productsQuery
            ->select('products.*')
            ->join('filter_items_products', 'products.id', '=', 'filter_items_products.product_id')
            ->groupBy('products.id')
            ->havingRaw('COUNT(DISTINCT filter_items_products.filter_item_id) >= ?', [count($filters)]) // Aseguramos que haya al menos un filtro de cada grupo
            ->with(['filterItems', 'images'])
            ->get();

        $productsFilter = $productsFilters->isEmpty() ? collect() : $this->filterByType($tipo, $idtipo, $productsFilters->pluck('id'));

        // Formatear los productos
        $formattedProducts = $productsFilter->map(function ($product) {
            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'part_number' => $product->part_number,
                'description' => $product->description,
                'active' => $product->active,
                'created_at' => $product->created_at,
                'discount' => $product->discount,
                'manufacturer_url' => $product->manufacturer_url,
                'name' => $product->name,
                'on_promotion' => $product->on_promotion,
                'on_sale' => $product->on_sale,
                'previous_price' => $product->previous_price,
                'price' => $product->price,
                'slug' => $product->slug,
                'stock' => $product->stock,
                'token' => $product->token,
                'updated_at' => $product->updated_at,
                'valoration' => $product->valoration,
            ];
        });

        // Obtener las imágenes de los productos
        $productImages = ProductImage::whereIn('product_id', $productsFilter->pluck('id'))->get();

        return response()->json([
            'products' => $formattedProducts->values(),
            'images' => $productImages,
        ]);
    }


    private function filterByType($tipo, $idtipo, $productsIds)
    {
        $productsFilter = collect();

        // Convertir la colección a un array
        $productIdsArray = $productsIds->toArray();

        if ($tipo == 'subgroup') {
            $subgroups = Subgroup::with(['products' => function ($query) {
                $query->where('active', true)->where('on_sale', true);
            }])->where('id', $idtipo)->get();

            foreach ($subgroups as $subgroup) {
                foreach ($subgroup->products as $product) {
                    if (in_array($product->id, $productIdsArray)) {
                        $productsFilter->push($product);
                    }
                }
            }
        } elseif ($tipo == 'group') {
            $groups = Group::with(['subgroups.products' => function ($query) {
                $query->where('active', true)->where('on_sale', true);
            }])->where('id', $idtipo)->get();

            foreach ($groups as $group) {
                foreach ($group->subgroups as $subgroup) {
                    foreach ($subgroup->products as $product) {
                        if (in_array($product->id, $productIdsArray)) {
                            $productsFilter->push($product);
                        }
                    }
                }
            }
        } elseif ($tipo == 'category') {
            $categories = Category::with(['groups.subgroups.products' => function ($query) {
                $query->where('active', true)->where('on_sale', true);
            }])->where('id', $idtipo)->get();

            foreach ($categories as $category) {
                foreach ($category->groups as $group) {
                    foreach ($group->subgroups as $subgroup) {
                        foreach ($subgroup->products as $product) {
                            if (in_array($product->id, $productIdsArray)) {
                                $productsFilter->push($product);
                            }
                        }
                    }
                }
            }
        }

        return $productsFilter;
    }

    public function searchProducts($s)
    {
        $products = Product::where('name', 'like', "%$s%")->where('active', true)->where('on_sale', true)->get();

        $categories = Category::where('active', true)->with(['groups' => function($query) {
            $query->where('active', true)->with(['subgroups' => function($subquery) {
                $subquery->where('active', true); // Filtra solo subgrupos activos
            }]);
        }])->where('active', true)->get();

        $images = ProductImage::all();
        $brands = Filter::with(['filterItems.products'])->where('active', true)->where('id', 1)->get();
        // return $products;
        return Inertia::render('Web/Search/Index', [
            'products' => $products,
            'categories' => $categories,
            'images' => $images,
            'brands' => $brands
        ]);
    }
    public function filterProductsSearch(Request $request)
    {
        $search = $request->input('search');
        $products = Product::where('name', 'like', "%$search%")->where('active', true)->where('on_sale', true)->get();
        $productImages = ProductImage::all();

        $response = [
            'products' => $products->values(),
            'images' => $productImages->values()
        ];
        return response()->json($response);
    }
}
