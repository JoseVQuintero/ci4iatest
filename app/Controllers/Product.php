<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class Product extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    private const MAX_IMAGE_SIZE = 5242880; // 5MB

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $products = $this->productModel
            ->select('products.id, products.user_id, products.name, products.slug, products.description, products.sku, products.price, products.offer_price, products.brand, products.type, products.image, products.image_mime, products.stock, products.status, products.created_at, products.updated_at')
            ->select('CASE WHEN products.image_data IS NULL THEN 0 ELSE 1 END AS has_image_data', false)
            ->where('user_id', $userId)
            ->findAll();

        foreach ($products as &$product) {
            $product['has_image'] = (! empty($product['image']) || (int) ($product['has_image_data'] ?? 0) === 1);
        }
        unset($product);

        $categories = $this->categoryModel->findAll();
        return view('products/index', [
            'title' => lang('App.products'),
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->findAll();
        return view('products/create', [
            'title' => lang('App.create_product'),
            'categories' => $categories,
        ]);
    }

    public function store()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('products');
        }

        $name     = $this->request->getPost('name');
        $sku      = $this->request->getPost('sku');
        $price    = $this->request->getPost('price');
        $description = $this->request->getPost('description');
        $offerPrice  = $this->request->getPost('offer_price');
        $brand    = $this->request->getPost('brand');
        $type     = $this->request->getPost('type');
        $stock    = $this->request->getPost('stock') ?? 0;
        $status   = $this->request->getPost('status') ?? 'active';
        $categories = $this->request->getPost('categories') ?? [];

        $rules = [
            'name'  => 'required|min_length[3]|max_length[150]',
            'sku'   => 'required|min_length[3]|max_length[50]|is_unique[products.sku]',
            'price' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $image = $this->request->getFile('image');
        $imagePayload = $this->prepareImageForDatabase($image);
        if (! empty($imagePayload['error'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['image' => $imagePayload['error']],
                ]);
            }
            return redirect()->back()->withInput()->with('errors', ['image' => $imagePayload['error']]);
        }

        // Create slug from name
        $slug = url_title($name, '-', true);

        $saveData = [
            'user_id'     => session()->get('user_id'),
            'name'        => $name,
            'slug'        => $slug,
            'sku'         => $sku,
            'price'       => $price,
            'offer_price' => $offerPrice ?: null,
            'description' => $description,
            'brand'       => $brand,
            'type'        => $type,
            'image'       => null,
            'image_data'  => $imagePayload['data'],
            'image_mime'  => $imagePayload['mime'],
            'stock'       => $stock,
            'status'      => $status,
        ];

        if (! $this->productModel->skipValidation(true)->save($saveData)) {
            $errors = $this->productModel->errors() ?: ['Unable to save product.'];
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors,
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $productId = $this->productModel->getInsertID();

        // Save categories
        if (! empty($categories)) {
            foreach ($categories as $categoryId) {
                $this->productModel->db->table('product_category')->insert([
                    'product_id'  => $productId,
                    'category_id' => $categoryId,
                ]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => lang('App.product_created_successfully'),
            ]);
        }

        return redirect()->to('products')->with('success', lang('App.product_created_successfully'));
    }

    public function edit($id)
    {
        $userId = session()->get('user_id');
        $product = $this->productModel->where('id', $id)->where('user_id', $userId)->first();
        if (! $product) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => lang('App.product_not_found_or_unauthorized')]);
            }
            return redirect()->to('products')->with('error', lang('App.product_not_found_or_unauthorized'));
        }

        $productCategories = $this->productModel->getCategories($id);
        $categoryIds = array_column($productCategories, 'id');

        if ($this->request->isAJAX()) {
            $imageUrl = $this->getProductImageUrl($product);
            unset($product['image_data']);
            $product['image_url'] = $imageUrl;
            return $this->response->setJSON([
                'success' => true,
                'product' => $product,
                'category_ids' => $categoryIds,
            ]);
        }

        $categories = $this->categoryModel->findAll();
        return view('products/edit', [
            'title' => lang('App.edit_product'),
            'product' => $product,
            'categories' => $categories,
            'categoryIds' => $categoryIds,
        ]);
    }

    public function update($id)
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('products');
        }

        $userId = session()->get('user_id');
        $product = $this->productModel->where('id', $id)->where('user_id', $userId)->first();
        if (! $product) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => [lang('App.product_not_found_or_unauthorized')],
                ]);
            }
            return redirect()->to('products')->with('error', lang('App.product_not_found_or_unauthorized'));
        }

        $name     = $this->request->getPost('name');
        $sku      = $this->request->getPost('sku');
        $price    = $this->request->getPost('price');
        $description = $this->request->getPost('description');
        $offerPrice  = $this->request->getPost('offer_price');
        $brand    = $this->request->getPost('brand');
        $type     = $this->request->getPost('type');
        $stock    = $this->request->getPost('stock') ?? 0;
        $status   = $this->request->getPost('status') ?? 'active';
        $categories = $this->request->getPost('categories') ?? [];

        $rules = [
            'name'  => 'required|min_length[3]|max_length[150]',
            'price' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $image = $this->request->getFile('image');
        $imagePayload = $this->prepareImageForDatabase($image);
        if (! empty($imagePayload['error'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['image' => $imagePayload['error']],
                ]);
            }
            return redirect()->back()->withInput()->with('errors', ['image' => $imagePayload['error']]);
        }

        $slug = url_title($name, '-', true);

        $updateData = [
            'name'        => $name,
            'slug'        => $slug,
            'price'       => $price,
            'offer_price' => $offerPrice ?: null,
            'description' => $description,
            'brand'       => $brand,
            'type'        => $type,
            'stock'       => $stock,
            'status'      => $status,
        ];

        if ($imagePayload['hasUpload']) {
            if (! empty($product['image']) && file_exists(FCPATH . 'uploads/products/' . $product['image'])) {
                unlink(FCPATH . 'uploads/products/' . $product['image']);
            }
            $updateData['image'] = null;
            $updateData['image_data'] = $imagePayload['data'];
            $updateData['image_mime'] = $imagePayload['mime'];
        }

        if (! $this->productModel->update($id, $updateData)) {
            $errors = $this->productModel->errors() ?: ['Unable to update product.'];
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $errors,
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Update categories
        $this->productModel->db->table('product_category')->where('product_id', $id)->delete();
        if (! empty($categories)) {
            foreach ($categories as $categoryId) {
                $this->productModel->db->table('product_category')->insert([
                    'product_id'  => $id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => lang('App.product_updated_successfully'),
            ]);
        }

        return redirect()->to('products')->with('success', lang('App.product_updated_successfully'));
    }

    public function delete($id)
    {
        $userId = session()->get('user_id');
        $product = $this->productModel->where('id', $id)->where('user_id', $userId)->first();
        if (! $product) {
            return redirect()->to('products')->with('error', lang('App.product_not_found_or_unauthorized'));
        }

        if ($product['image'] && file_exists(FCPATH . 'uploads/products/' . $product['image'])) {
            unlink(FCPATH . 'uploads/products/' . $product['image']);
        }

        $this->productModel->db->table('product_category')->where('product_id', $id)->delete();
        $this->productModel->delete($id);

        return redirect()->to('products')->with('success', lang('App.product_deleted_successfully'));
    }

    public function image($id)
    {
        $userId = session()->get('user_id');
        $product = $this->productModel->where('id', $id)->where('user_id', $userId)->first();
        if (! $product) {
            return $this->response->setStatusCode(404);
        }

        if (! empty($product['image_data'])) {
            $mime = ! empty($product['image_mime']) ? $product['image_mime'] : 'application/octet-stream';
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setBody($product['image_data']);
        }

        if (! empty($product['image'])) {
            $path = FCPATH . 'uploads/products/' . $product['image'];
            if (is_file($path)) {
                $mime = mime_content_type($path) ?: 'application/octet-stream';
                return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setBody((string) file_get_contents($path));
            }
        }

        return $this->response->setStatusCode(404);
    }

    private function prepareImageForDatabase(?UploadedFile $image): array
    {
        $result = [
            'hasUpload' => false,
            'data' => null,
            'mime' => null,
            'error' => null,
        ];

        if (! $image || $image->getError() === UPLOAD_ERR_NO_FILE) {
            return $result;
        }

        if (! $image->isValid()) {
            $result['error'] = $image->getErrorString();
            return $result;
        }

        if ($image->getSize() > self::MAX_IMAGE_SIZE) {
            $result['error'] = lang('App.image_size_exceeded');
            return $result;
        }

        $mime = $image->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowed, true)) {
            $result['error'] = lang('App.invalid_image_format');
            return $result;
        }

        $tempPath = $image->getTempName();
        $content = @file_get_contents($tempPath);
        if ($content === false) {
            $result['error'] = lang('App.unable_to_read_uploaded_image');
            return $result;
        }

        $result['hasUpload'] = true;
        $result['data'] = $content;
        $result['mime'] = $mime;

        return $result;
    }

    private function getProductImageUrl(array $product): ?string
    {
        if (! empty($product['image_data']) || ! empty($product['image'])) {
            return site_url('products/' . $product['id'] . '/image');
        }

        return null;
    }
}
