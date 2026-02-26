<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-8 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
            </div>
            <form method="post" action="<?= site_url('roles/store') ?>">
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
                        <label for="name"><?= esc(lang('App.role_name')) ?></label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control" value="<?= old('slug') ?>" required>
                        <small class="text-muted">Ejemplo: ventas, soporte, editor</small>
                    </div>
                    <div class="form-group">
                        <label for="description"><?= esc(lang('App.description')) ?></label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label><?= esc(lang('App.role_modules')) ?></label>
                        <div class="border rounded p-2">
                            <?php foreach (($modules ?? []) as $moduleKey => $module): ?>
                                <div class="form-check mb-1">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="modules[]"
                                        value="<?= esc($moduleKey) ?>"
                                        id="role_module_<?= esc($moduleKey) ?>"
                                        <?= $moduleKey === 'dashboard' ? 'checked disabled' : '' ?>
                                    >
                                    <?php if ($moduleKey === 'dashboard'): ?>
                                        <input type="hidden" name="modules[]" value="dashboard">
                                    <?php endif; ?>
                                    <label class="form-check-label" for="role_module_<?= esc($moduleKey) ?>">
                                        <?= esc(lang((string) $module['label'])) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><?= esc(lang('App.save_role')) ?></button>
                    <a href="<?= site_url('roles') ?>" class="btn btn-secondary"><?= esc(lang('App.cancel')) ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
