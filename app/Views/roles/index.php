<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc($title) ?></h3>
                <div class="card-tools">
                    <a href="<?= site_url('roles/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> <?= esc(lang('App.new_role')) ?>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= esc(lang('App.name')) ?></th>
                                <th>Slug</th>
                                <th><?= esc(lang('App.description')) ?></th>
                                <th><?= esc(lang('App.users_count')) ?></th>
                                <th><?= esc(lang('App.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td><?= (int) $role['id'] ?></td>
                                        <td>
                                            <?= esc($role['name']) ?>
                                            <?php if ((int) ($role['is_system'] ?? 0) === 1): ?>
                                                <span class="badge badge-info ml-1"><?= esc(lang('App.system_role')) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($role['slug']) ?></td>
                                        <td><?= esc($role['description'] ?? '-') ?></td>
                                        <td><?= (int) ($role['user_count'] ?? 0) ?></td>
                                        <td>
                                            <a href="<?= site_url('roles/' . $role['id'] . '/edit') ?>" class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ((int) ($role['is_system'] ?? 0) !== 1): ?>
                                                <a href="<?= site_url('roles/' . $role['id'] . '/delete') ?>" class="btn btn-xs btn-danger" onclick="return confirm('<?= esc(lang('App.confirm_delete_role'), 'js') ?>');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted"><?= esc(lang('App.no_roles_found')) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
