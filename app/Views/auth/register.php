<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<?php if(session()->get('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach(session()->get('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= site_url('register') ?>">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="name"><?= esc(lang('App.full_name')) ?></label>
        <input type="text" name="name" id="name" value="<?= old('name') ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email"><?= esc(lang('App.email')) ?></label>
        <input type="email" name="email" id="email" value="<?= old('email') ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password"><?= esc(lang('App.password')) ?></label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block"><?= esc(lang('App.register')) ?></button>
</form>

<hr>
<p class="text-center">
    <?= esc(lang('App.already_have_account')) ?> <a href="<?= site_url('login') ?>"><?= esc(lang('App.login_here')) ?></a>
</p>

<?= $this->endSection() ?>
