<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
                <div class="card-tools">
                    <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> <?= esc(lang('App.new_user')) ?>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="<?= site_url('users') ?>" class="mb-3">
                    <div class="form-row">
                        <div class="form-group col-md-5 mb-2">
                            <input type="text" name="q" class="form-control" value="<?= esc($search ?? '') ?>" placeholder="<?= esc(lang('App.search_users')) ?>">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <select name="role" class="form-control">
                                <option value=""><?= esc(lang('App.all_roles')) ?></option>
                                <?php foreach (($roles ?? []) as $role): ?>
                                    <option value="<?= esc($role['slug']) ?>" <?= ($roleFilter ?? '') === $role['slug'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            <button type="submit" class="btn btn-primary"><?= esc(lang('App.filter')) ?></button>
                            <a href="<?= site_url('users') ?>" class="btn btn-secondary"><?= esc(lang('App.clear')) ?></a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= esc(lang('App.name')) ?></th>
                                <th><?= esc(lang('App.email')) ?></th>
                                <th><?= esc(lang('App.role')) ?></th>
                                <th><?= esc(lang('App.created_at')) ?></th>
                                <th style="width: 170px;"><?= esc(lang('App.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rolesMap = []; ?>
                            <?php foreach (($roles ?? []) as $role): ?>
                                <?php $rolesMap[$role['slug']] = $role['name']; ?>
                            <?php endforeach; ?>
                            <?php if (! empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= (int) $user['id'] ?></td>
                                        <td><?= esc($user['name']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $user['role'] === 'admin' ? 'success' : 'warning' ?>">
                                                <?= esc($rolesMap[$user['role']] ?? $user['role']) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($user['created_at'] ?? '-') ?></td>
                                        <td>
                                            <a href="<?= site_url('users/' . $user['id'] . '/edit') ?>" class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= site_url('users/' . $user['id'] . '/delete') ?>" class="btn btn-xs btn-danger" onclick="return confirm('<?= esc(lang('App.confirm_delete_user'), 'js') ?>');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted"><?= esc(lang('App.no_users_found')) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
