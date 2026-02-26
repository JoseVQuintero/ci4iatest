<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#productModal" onclick="resetForm()">
                        <i class="fas fa-plus"></i> <?= esc(lang('App.new_product')) ?>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" id="successAlert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive products-table-wrap">
                    <table id="productsTable" class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= esc(lang('App.image')) ?></th>
                                <th><?= esc(lang('App.name')) ?></th>
                                <th>SKU</th>
                                <th><?= esc(lang('App.brand')) ?></th>
                                <th><?= esc(lang('App.price')) ?></th>
                                <th><?= esc(lang('App.offer_price')) ?></th>
                                <th><?= esc(lang('App.stock')) ?></th>
                                <th><?= esc(lang('App.status')) ?></th>
                                <th style="width: 200px;"><?= esc(lang('App.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <?php if (!empty($product['has_image'])): ?>
                                            <img src="<?= site_url('products/' . $product['id'] . '/image') ?>" alt="<?= esc($product['name']) ?>" style="max-width: 60px; max-height: 60px; border-radius: 4px;">
                                        <?php else: ?>
                                            <span class="text-muted"><?= esc(lang('App.no_image')) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($product['name']) ?></td>
                                    <td><?= esc($product['sku']) ?></td>
                                    <td><?= esc($product['brand'] ?? '-') ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td>
                                        <?php if ($product['offer_price']): ?>
                                            $<?= number_format($product['offer_price'], 2) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $product['stock'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $product['status'] === 'active' ? 'success' : 'warning' ?>">
                                            <?= esc($product['status'] === 'active' ? lang('App.active') : lang('App.inactive')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-info" onclick="openCategoriesModal(<?= $product['id'] ?>, '<?= esc($product['name']) ?>')">
                                            <i class="fas fa-tags"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-warning" onclick="editProduct(<?= $product['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel"><?= esc(lang('App.create_product')) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" id="productId" name="product_id" value="">
                <input type="hidden" id="isEdit" value="false">
                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger" style="display:none;"></div>

                    <div class="form-group">
                        <label for="name"><?= esc(lang('App.product_name')) ?> *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sku">SKU *</label>
                            <input type="text" name="sku" id="sku" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="brand"><?= esc(lang('App.brand')) ?></label>
                            <input type="text" name="brand" id="brand" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="price"><?= esc(lang('App.price')) ?> *</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="offer_price"><?= esc(lang('App.offer_price')) ?></label>
                            <input type="number" name="offer_price" id="offer_price" class="form-control" step="0.01">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="type"><?= esc(lang('App.type')) ?></label>
                            <input type="text" name="type" id="type" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stock"><?= esc(lang('App.stock')) ?></label>
                            <input type="number" name="stock" id="stock" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description"><?= esc(lang('App.description')) ?></label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="categories"><?= esc(lang('App.categories')) ?></label>
                        <div class="input-group">
                            <select name="categories[]" id="categories" class="form-control" multiple="multiple">
                                <?php foreach ($categories ?? [] as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="manageCategoriesBtn" title="<?= esc(lang('App.manage_categories')) ?>" onclick="openManageCategoriesFromProductForm()">
                                    <i class="fas fa-tags"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image"><?= esc(lang('App.product_image')) ?></label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <small class="form-text text-muted"><?= esc(lang('App.image_formats_help')) ?></small>
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <p><strong><?= esc(lang('App.current_preview_image')) ?></strong></p>
                            <img id="previewImg" src="" alt="Preview" style="max-width: 250px; max-height: 250px; border-radius: 4px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status"><?= esc(lang('App.status')) ?></label>
                        <select name="status" id="status" class="form-control">
                            <option value="active"><?= esc(lang('App.active')) ?></option>
                            <option value="inactive"><?= esc(lang('App.inactive')) ?></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= esc(lang('App.cancel')) ?></button>
                    <button type="submit" class="btn btn-primary"><?= esc(lang('App.save_product')) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Categories Modal -->
<div class="modal fade" id="categoriesModal" tabindex="-1" role="dialog" aria-labelledby="categoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoriesModalLabel"><?= esc(lang('App.manage_categories')) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentProductId" value="">
                <div id="categoriesErrors" class="alert alert-danger" style="display:none;"></div>

                <!-- Current categories -->
                <div class="mb-4">
                    <h6><?= esc(lang('App.current_categories')) ?></h6>
                    <div id="currentCategoriesList" class="mb-3">
                        <!-- Dynamically populated -->
                    </div>
                </div>

                <!-- Add new category section -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><?= esc(lang('App.add_category_to_product')) ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="availableCategories"><?= esc(lang('App.select_or_create_category')) ?></label>
                                <select id="availableCategories" class="form-control" >
                                    <option value=""><?= esc(lang('App.select_category_placeholder')) ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="addCategoryToProduct()">
                                    <i class="fas fa-plus"></i> <?= esc(lang('App.add')) ?>
                                </button>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3"><?= esc(lang('App.create_new_category')) ?></h6>
                        <div class="form-group">
                            <label for="newCategoryName"><?= esc(lang('App.category_name')) ?></label>
                            <input type="text" id="newCategoryName" class="form-control" placeholder="<?= esc(lang('App.enter_category_name')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="newCategoryDesc"><?= esc(lang('App.description_optional')) ?></label>
                            <textarea id="newCategoryDesc" class="form-control" rows="2" placeholder="<?= esc(lang('App.category_description')) ?>"></textarea>
                        </div>
                        <button type="button" class="btn btn-success btn-block" onclick="createNewCategory()">
                            <i class="fas fa-plus-circle"></i> <?= esc(lang('App.create_new_category')) ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= esc(lang('App.close')) ?></button>
                <button type="button" class="btn btn-primary" onclick="saveProductCategories()"><?= esc(lang('App.save_changes')) ?></button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" crossorigin="anonymous" />
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<style>
    .products-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #productsTable {
        width: 100% !important;
    }

    .dataTables_wrapper {
        width: 100%;
    }

    .dataTables_wrapper .row {
        margin-left: 0;
        margin-right: 0;
    }

    .dataTables_wrapper .col-sm-12 {
        padding-left: 0;
        padding-right: 0;
    }

    @media (max-width: 767.98px) {
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: left !important;
        }
    }

    /* Allow stacked modals (Product + Manage Categories) */
    .modal-backdrop.modal-stack {
        opacity: 0.5;
    }
    .select2-container--open {
        z-index: 2000;
    }
</style>
<script>
    let table;
    let categories = <?= json_encode($categories ?? []) ?>;
    const i18n = {
        createProduct: <?= json_encode(lang('App.create_product')) ?>,
        editProduct: <?= json_encode(lang('App.edit_product')) ?>,
        selectCategoryPlaceholder: <?= json_encode(lang('App.select_category_placeholder')) ?>,
        noCategoriesAssignedYet: <?= json_encode(lang('App.no_categories_assigned_yet')) ?>,
        saveProductFirst: <?= json_encode(lang('App.save_product_first_then_manage_categories')) ?>,
        errorLoadingProduct: <?= json_encode(lang('App.error_loading_product')) ?>,
        errorLoadingCategories: <?= json_encode(lang('App.error_loading_categories')) ?>,
        selectCategoryPlease: <?= json_encode(lang('App.please_select_category')) ?>,
        categoryNameRequired: <?= json_encode(lang('App.category_name_required')) ?>,
        categoryCreatedSuccess: <?= json_encode(lang('App.category_created_successfully')) ?>,
        errorCreatingCategory: <?= json_encode(lang('App.error_creating_category')) ?>,
        errorSavingCategories: <?= json_encode(lang('App.error_saving_categories')) ?>,
        confirmDeleteProduct: <?= json_encode(lang('App.confirm_delete_product')) ?>,
        manageCategoriesFor: <?= json_encode(lang('App.manage_categories_for')) ?>,
        productFallback: <?= json_encode(lang('App.product')) ?>,
        selectCategories: <?= json_encode(lang('App.select_categories')) ?>,
        active: <?= json_encode(lang('App.active')) ?>,
        inactive: <?= json_encode(lang('App.inactive')) ?>
    };

    $(document).ready(function() {
        table = $('#productsTable').DataTable({
            responsive: true,
            autoWidth: false,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                orderable: false,
                targets: 9
            }]
        });

        const syncTableLayout = () => {
            if (!table) {
                return;
            }
            table.columns.adjust();
            table.responsive.recalc();
        };
        $(window).on('resize orientationchange', syncTableLayout);
        setTimeout(syncTableLayout, 50);

        // Multi-select categories with Select2 in product modal
        $('#categories').select2({
            theme: 'classic',
            width: '80%',
            placeholder: i18n.selectCategories,
            closeOnSelect: false,
        });

        initAvailableCategoriesSelect2();

        // Bootstrap modal stacking with z-index
        $(document).on('show.bs.modal', '.modal', function() {
            const zIndex = 1040 + (10 * $('.modal.show').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').first().css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });

        $(document).on('hidden.bs.modal', '.modal', function() {
            if ($('.modal.show').length > 0) {
                $('body').addClass('modal-open');
            }
        });

        // Form submission with AJAX
        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            saveProduct();
        });

        // Image preview on file selection
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    console.log('Previewing image:', event.target.result);
                    document.getElementById('previewImg').src = event.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });

    function resetForm() {
        document.getElementById('productForm').reset();
        document.getElementById('isEdit').value = 'false';
        document.getElementById('productId').value = '';
        document.getElementById('productModalLabel').textContent = i18n.createProduct;
        document.getElementById('formErrors').style.display = 'none';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('previewImg').src = '';
        document.getElementById('sku').removeAttribute('readonly');
        $('#categories').val(null).trigger('change');
    }

    function editProduct(productId) {
        $.ajax({
            url: '<?= site_url('products') ?>/' + productId + '/edit',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const product = data.product;
                    document.getElementById('productId').value = product.id;
                    document.getElementById('isEdit').value = 'true';
                    document.getElementById('productModalLabel').textContent = i18n.editProduct;
                    document.getElementById('name').value = product.name;
                    document.getElementById('sku').value = product.sku;
                    document.getElementById('sku').setAttribute('readonly', 'readonly');
                    document.getElementById('brand').value = product.brand || '';
                    document.getElementById('price').value = product.price;
                    document.getElementById('offer_price').value = product.offer_price || '';
                    document.getElementById('type').value = product.type || '';
                    document.getElementById('stock').value = product.stock;
                    document.getElementById('description').value = product.description || '';
                    document.getElementById('status').value = product.status;
                    document.getElementById('formErrors').style.display = 'none';

                    // Set categories
                    const categoryIds = data.category_ids || [];
                    $('#categories').val(categoryIds).trigger('change');

                    // Show image preview if exists
                    if (product.image_url) {
                        document.getElementById('previewImg').src = product.image_url;
                        document.getElementById('imagePreview').style.display = 'block';
                    } else {
                        document.getElementById('imagePreview').style.display = 'none';
                    }

                    // Clear file input so user can upload new image if desired
                    document.getElementById('image').value = '';

                    $('#productModal').modal('show');
                }
            },
            error: function(xhr) {
                alert(i18n.errorLoadingProduct);
            }
        });
    }

    function saveProduct() {
        const formData = new FormData(document.getElementById('productForm'));
        const isEdit = document.getElementById('isEdit').value === 'true';
        const productId = document.getElementById('productId').value;

        let url = '<?= site_url('products/store') ?>';
        if (isEdit) {
            url = '<?= site_url('products') ?>/' + productId + '/update';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#productModal').modal('hide');
                    // Reload table data
                    location.reload();
                } else {
                    // Show errors
                    if (response.errors) {
                        let errorHtml = '<ul class="mb-0">';
                        for (let field in response.errors) {
                            errorHtml += '<li>' + response.errors[field] + '</li>';
                        }
                        errorHtml += '</ul>';
                        document.getElementById('formErrors').innerHTML = errorHtml;
                        document.getElementById('formErrors').style.display = 'block';
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorHtml = '<ul class="mb-0">';
                    for (let field in response.errors) {
                        errorHtml += '<li>' + response.errors[field] + '</li>';
                    }
                    errorHtml += '</ul>';
                    document.getElementById('formErrors').innerHTML = errorHtml;
                    document.getElementById('formErrors').style.display = 'block';
                }
            }
        });
    }

    function deleteProduct(productId) {
        if (confirm(i18n.confirmDeleteProduct)) {
            window.location.href = '<?= site_url('products') ?>/' + productId + '/delete';
        }
    }

    function openManageCategoriesFromProductForm() {
        const productId = document.getElementById('productId').value;
        const productName = document.getElementById('name').value || i18n.productFallback;

        if (!productId) {
            alert(i18n.saveProductFirst);
            return;
        }

        openCategoriesModal(productId, productName);
    }

    // Global variables for categories modal
    let currentProductCategories = [];
    let allAvailableCategories = [];

    function toCategoryId(value) {
        const id = parseInt(value, 10);
        return Number.isNaN(id) ? null : id;
    }

    function openCategoriesModal(productId, productName) {
        document.getElementById('currentProductId').value = productId;
        document.getElementById('categoriesModalLabel').textContent = i18n.manageCategoriesFor + ' - ' + productName;
        document.getElementById('categoriesErrors').style.display = 'none';
        document.getElementById('newCategoryName').value = '';
        document.getElementById('newCategoryDesc').value = '';

        // Load categories for this product
        $.ajax({
            url: '<?= site_url('products') ?>/' + productId + '/categories',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    allAvailableCategories = data.all_categories.map(category => ({
                        ...category,
                        id: toCategoryId(category.id)
                    }));
                    currentProductCategories = data.product_categories
                        .map(cat => toCategoryId(cat.id))
                        .filter(id => id !== null);

                    // Populate current categories
                    populateCurrentCategories();

                    // Populate available categories dropdown
                    populateAvailableCategories();

                    $('#categoriesModal').modal('show');
                }
            },
            error: function(xhr) {
                alert(i18n.errorLoadingCategories);
            }
        });
    }

    function populateCurrentCategories() {
        const container = document.getElementById('currentCategoriesList');

        if (currentProductCategories.length === 0) {
            container.innerHTML = '<p class="text-muted">' + i18n.noCategoriesAssignedYet + '</p>';
            return;
        }

        let html = '<div class="list-group">';
        currentProductCategories.forEach(categoryId => {
            const category = allAvailableCategories.find(c => toCategoryId(c.id) === toCategoryId(categoryId));
            if (category) {
                html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${category.name}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeCategoryFromProduct(${categoryId})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
            }
        });
        html += '</div>';
        container.innerHTML = html;
    }

    function populateAvailableCategories() {
        const $select = $('#availableCategories');
        $select.empty();
        $select.append(new Option(i18n.selectCategoryPlaceholder, ''));
                                    
        allAvailableCategories.forEach(category => {
            const categoryId = toCategoryId(category.id);
            // Don't show categories already assigned
            
                $select.append(new Option(category.name, String(categoryId)));
            
        });

        $select.val('').trigger('change');
    }

    function addCategoryToProduct() {
        const categoryId = toCategoryId(document.getElementById('availableCategories').value);

        if (categoryId === null) {
            showCategoryError(i18n.selectCategoryPlease);
            return;
        }

        if (!currentProductCategories.includes(categoryId)) {
            currentProductCategories.push(categoryId);
            populateCurrentCategories();
            populateAvailableCategories();
            document.getElementById('availableCategories').value = '';
            document.getElementById('categoriesErrors').style.display = 'none';
        }
    }

    function removeCategoryFromProduct(categoryId) {
        const idToRemove = toCategoryId(categoryId);
        if (idToRemove === null) {
            return;
        }
        currentProductCategories = currentProductCategories.filter(id => toCategoryId(id) !== idToRemove);
        populateCurrentCategories();
        populateAvailableCategories();
    }

    function createNewCategory() {
        const name = document.getElementById('newCategoryName').value.trim();
        const description = document.getElementById('newCategoryDesc').value.trim();

        if (!name) {
            showCategoryError(i18n.categoryNameRequired);
            return;
        }

        $.ajax({
            url: '<?= site_url('categories/store') ?>',
            method: 'POST',
            dataType: 'json',
            data: {
                name: name,
                description: description,
                [<?= json_encode(csrf_token()) ?>]: <?= json_encode(csrf_hash()) ?>
            },
            success: function(response) {
                if (response.success) {
                    const newCategory = {
                        ...response.category,
                        id: toCategoryId(response.category.id)
                    };

                    // Add new category to the list
                    allAvailableCategories.push(newCategory);

                    // Refresh displays
                    populateCurrentCategories();
                    populateAvailableCategories();

                    // Auto-select the newly created category
                    $('#availableCategories').val(String(newCategory.id)).trigger('change');

                    // Clear form
                    document.getElementById('newCategoryName').value = '';
                    document.getElementById('newCategoryDesc').value = '';
                    document.getElementById('categoriesErrors').style.display = 'none';

                    // Show success message
                    alert(i18n.categoryCreatedSuccess);
                } else {
                    showCategoryError(response.message || i18n.errorCreatingCategory);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorHtml = '<ul class="mb-0">';
                    for (let field in response.errors) {
                        errorHtml += '<li>' + response.errors[field] + '</li>';
                    }
                    errorHtml += '</ul>';
                    document.getElementById('categoriesErrors').innerHTML = errorHtml;
                } else {
                    showCategoryError(i18n.errorCreatingCategory);
                }
                document.getElementById('categoriesErrors').style.display = 'block';
            }
        });
    }

    function saveProductCategories() {
        const productId = document.getElementById('currentProductId').value;

        $.ajax({
            url: '<?= site_url('products') ?>/' + productId + '/categories/update',
            method: 'POST',
            dataType: 'json',
            data: {
                categories: currentProductCategories,
                [<?= json_encode(csrf_token()) ?>]: <?= json_encode(csrf_hash()) ?>
            },
            success: function(response) {
                if (response.success) {
                    $('#categoriesModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                showCategoryError(i18n.errorSavingCategories);
            }
        });
    }

    function showCategoryError(message) {
        const errorDiv = document.getElementById('categoriesErrors');
        errorDiv.innerHTML = '<strong>' + message + '</strong>';
        errorDiv.style.display = 'block';
    }

    function initAvailableCategoriesSelect2() {
        const $availableCategories = $('#availableCategories');
        if ($availableCategories.hasClass('select2-hidden-accessible')) {
            $availableCategories.select2('destroy');
        }

        $availableCategories.select2({
            theme: 'classic',
            width: '100%',
            placeholder: i18n.selectCategoryPlaceholder,
            allowClear: true,
        });
    }
</script>
<?= $this->endSection() ?>
