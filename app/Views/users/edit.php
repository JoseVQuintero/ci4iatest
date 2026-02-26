<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
            </div>
            <form method="post" action="<?= site_url('users/' . $user['id'] . '/update') ?>">
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
                        <label for="name"><?= esc(lang('App.full_name')) ?></label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= old('name', $user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><?= esc(lang('App.email')) ?></label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><?= esc(lang('App.password')) ?> (<?= esc(lang('App.optional')) ?>)</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="role"><?= esc(lang('App.role')) ?></label>
                        <select id="role" name="role" class="form-control" required>
                            <?php foreach (($roles ?? []) as $role): ?>
                                <option value="<?= esc($role['slug']) ?>" <?= old('role', $user['role']) === $role['slug'] ? 'selected' : '' ?>>
                                    <?= esc($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= esc(lang('App.visible_modules')) ?></label>
                        <div class="border rounded p-2">
                            <?php foreach (($modules ?? []) as $moduleKey => $module): ?>
                                <div class="form-check mb-1">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="modules[]"
                                        value="<?= esc($moduleKey) ?>"
                                        id="module_<?= esc($moduleKey) ?>"
                                        <?= in_array($moduleKey, (array) ($selectedModules ?? []), true) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="module_<?= esc($moduleKey) ?>">
                                        <?= esc(lang((string) $module['label'])) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted"><?= esc(lang('App.user_modules_note')) ?></small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><?= esc(lang('App.update_user')) ?></button>
                    <a href="<?= site_url('users') ?>" class="btn btn-secondary"><?= esc(lang('App.cancel')) ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
