<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
            </div>
            <form action="<?= site_url('products/' . $product['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">
                    <?php if (session()->get('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->get('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?? $product['name'] ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sku">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" value="<?= $product['sku'] ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" id="brand" class="form-control" value="<?= old('brand') ?? $product['brand'] ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="price">Price *</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?= old('price') ?? $product['price'] ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="offer_price">Offer Price</label>
                            <input type="number" name="offer_price" id="offer_price" class="form-control" step="0.01" value="<?= old('offer_price') ?? $product['offer_price'] ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="type">Type</label>
                            <input type="text" name="type" id="type" class="form-control" value="<?= old('type') ?? $product['type'] ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stock">Stock</label>
                            <input type="number" name="stock" id="stock" class="form-control" value="<?= old('stock') ?? $product['stock'] ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?= old('description') ?? $product['description'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="categories">Categories</label>
                        <select name="categories[]" id="categories" class="form-control" multiple>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= in_array($category['id'], $categoryIds) ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <?php if (!empty($product['image']) || !empty($product['image_data'])): ?>
                            <div class="mb-2">
                                <img src="<?= site_url('products/' . $product['id'] . '/image') ?>" alt="Product" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active" <?= (old('status') ?? $product['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (old('status') ?? $product['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="<?= site_url('products') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
