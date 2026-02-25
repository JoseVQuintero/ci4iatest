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
                        <label for="name"><?= esc(lang('App.product_name')) ?> *</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?? $product['name'] ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sku">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" value="<?= $product['sku'] ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="brand"><?= esc(lang('App.brand')) ?></label>
                            <input type="text" name="brand" id="brand" class="form-control" value="<?= old('brand') ?? $product['brand'] ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="price"><?= esc(lang('App.price')) ?> *</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?= old('price') ?? $product['price'] ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="offer_price"><?= esc(lang('App.offer_price')) ?></label>
                            <input type="number" name="offer_price" id="offer_price" class="form-control" step="0.01" value="<?= old('offer_price') ?? $product['offer_price'] ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="type"><?= esc(lang('App.type')) ?></label>
                            <input type="text" name="type" id="type" class="form-control" value="<?= old('type') ?? $product['type'] ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stock"><?= esc(lang('App.stock')) ?></label>
                            <input type="number" name="stock" id="stock" class="form-control" value="<?= old('stock') ?? $product['stock'] ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description"><?= esc(lang('App.description')) ?></label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?= old('description') ?? $product['description'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="categories"><?= esc(lang('App.categories')) ?></label>
                        <select name="categories[]" id="categories" class="form-control" multiple>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= in_array($category['id'], $categoryIds) ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image"><?= esc(lang('App.product_image')) ?></label>
                        <?php if (!empty($product['image']) || !empty($product['image_data'])): ?>
                            <div class="mb-2">
                                <img src="<?= site_url('products/' . $product['id'] . '/image') ?>" alt="<?= esc(lang('App.product')) ?>" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="status"><?= esc(lang('App.status')) ?></label>
                        <select name="status" id="status" class="form-control">
                            <option value="active" <?= (old('status') ?? $product['status']) === 'active' ? 'selected' : '' ?>><?= esc(lang('App.active')) ?></option>
                            <option value="inactive" <?= (old('status') ?? $product['status']) === 'inactive' ? 'selected' : '' ?>><?= esc(lang('App.inactive')) ?></option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><?= esc(lang('App.update_product')) ?></button>
                    <a href="<?= site_url('products') ?>" class="btn btn-secondary"><?= esc(lang('App.cancel')) ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
