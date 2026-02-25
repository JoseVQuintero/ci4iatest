<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $productModel = new ProductModel();
        $db = \Config\Database::connect();

        $products = $productModel->where('user_id', $userId)->findAll();

        $totalProducts = count($products);
        $activeProducts = 0;
        $inactiveProducts = 0;
        $productsWithOffer = 0;
        $totalPrice = 0.0;
        $minPrice = null;
        $maxPrice = null;
        $stockAvailable = 0;
        $stockOut = 0;

        foreach ($products as $product) {
            $price = (float) $product['price'];
            $totalPrice += $price;
            $minPrice = $minPrice === null ? $price : min($minPrice, $price);
            $maxPrice = $maxPrice === null ? $price : max($maxPrice, $price);

            if (($product['status'] ?? '') === 'active') {
                $activeProducts++;
            } else {
                $inactiveProducts++;
            }

            if (! empty($product['offer_price'])) {
                $productsWithOffer++;
            }

            if ((int) ($product['stock'] ?? 0) > 0) {
                $stockAvailable++;
            } else {
                $stockOut++;
            }
        }

        $avgPrice = $totalProducts > 0 ? round($totalPrice / $totalProducts, 2) : 0;

        $brandRows = $db->table('products')
            ->select("COALESCE(NULLIF(TRIM(brand), ''), '" . addslashes(lang('App.no_brand')) . "') as brand_name, COUNT(*) as total", false)
            ->where('user_id', $userId)
            ->groupBy("COALESCE(NULLIF(TRIM(brand), ''), '" . addslashes(lang('App.no_brand')) . "')", false)
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $categoryRows = $db->table('product_category pc')
            ->select('c.name as category_name, COUNT(pc.product_id) as total')
            ->join('categories c', 'c.id = pc.category_id')
            ->join('products p', 'p.id = pc.product_id')
            ->where('p.user_id', $userId)
            ->groupBy('c.id, c.name')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $priceRangeRows = $db->table('products')
            ->select("
                CASE
                    WHEN price < 50 THEN '0 - 49'
                    WHEN price >= 50 AND price < 100 THEN '50 - 99'
                    WHEN price >= 100 AND price < 250 THEN '100 - 249'
                    WHEN price >= 250 AND price < 500 THEN '250 - 499'
                    ELSE '500+'
                END AS price_range,
                COUNT(*) AS total
            ", false)
            ->where('user_id', $userId)
            ->groupBy('price_range')
            ->orderBy('MIN(price)', 'ASC', false)
            ->get()
            ->getResultArray();

        return view('dashboard', [
            'title' => lang('App.dashboard'),
            'stats' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'inactive_products' => $inactiveProducts,
                'products_with_offer' => $productsWithOffer,
                'stock_available' => $stockAvailable,
                'stock_out' => $stockOut,
                'avg_price' => $avgPrice,
                'min_price' => $minPrice ?? 0,
                'max_price' => $maxPrice ?? 0,
            ],
            'brand_rows' => $brandRows,
            'category_rows' => $categoryRows,
            'price_range_rows' => $priceRangeRows,
        ]);
    }
}
