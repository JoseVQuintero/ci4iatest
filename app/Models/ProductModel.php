<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'offer_price',
        'brand',
        'type',
        'image',
        'image_data',
        'image_mime',
        'stock',
        'status',
    ];

    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[150]',
        'sku'   => 'required|min_length[3]|max_length[50]|is_unique[products.sku]',
        'price' => 'required|numeric|greater_than[0]',
    ];

    public function getWithCategories($product_id)
    {
        return $this->select('products.*')
            ->join('product_category', 'product_category.product_id = products.id', 'left')
            ->join('categories', 'categories.id = product_category.category_id', 'left')
            ->where('products.id', $product_id)
            ->findAll();
    }

    public function getCategories($product_id)
    {
        return $this->db->table('product_category')
            ->select('categories.*')
            ->join('categories', 'categories.id = product_category.category_id')
            ->where('product_category.product_id', $product_id)
            ->get()
            ->getResultArray();
    }
}
