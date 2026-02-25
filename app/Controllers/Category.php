<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Category extends BaseController
{
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get all categories for a product
     */
    public function getProductCategories($productId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        
        // Verify product ownership
        $product = $this->productModel->where('id', $productId)->where('user_id', $userId)->first();
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('App.product_not_found_or_unauthorized')
            ]);
        }

        // Get product categories
        $productCategories = $this->productModel->getCategories($productId);
        $allCategories = $this->categoryModel->findAll();

        return $this->response->setJSON([
            'success' => true,
            'product_id' => $productId,
            'product_name' => $product['name'],
            'product_categories' => $productCategories,
            'all_categories' => $allCategories,
        ]);
    }

    /**
     * Create a new category (AJAX)
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description') ?? '';

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $slug = url_title($name, '-', true);

        $saveData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ];

        if (!$this->categoryModel->skipValidation(true)->save($saveData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('App.unable_to_create_category'),
            ]);
        }

        $categoryId = $this->categoryModel->getInsertID();

        return $this->response->setJSON([
            'success' => true,
            'message' => lang('App.category_created_successfully'),
            'category' => [
                'id' => $categoryId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ]
        ]);
    }

    /**
     * Update product categories (AJAX)
     */
    public function updateProductCategories($productId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        
        // Verify product ownership
        $product = $this->productModel->where('id', $productId)->where('user_id', $userId)->first();
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('App.product_not_found_or_unauthorized')
            ]);
        }

        $categories = $this->request->getPost('categories') ?? [];

        // Delete existing categories for this product
        $this->categoryModel->db->table('product_category')
            ->where('product_id', $productId)
            ->delete();

        // Insert new categories
        if (!empty($categories)) {
            foreach ($categories as $categoryId) {
                $this->categoryModel->db->table('product_category')->insert([
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                ]);
            }
        }

        // Get updated categories
        $productCategories = $this->productModel->getCategories($productId);

        return $this->response->setJSON([
            'success' => true,
            'message' => lang('App.product_categories_updated_successfully'),
            'product_categories' => $productCategories,
        ]);
    }
}
