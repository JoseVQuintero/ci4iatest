<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<?php if(session()->get('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->get('error')) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= site_url('login') ?>">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="email"><?= esc(lang('App.email')) ?></label>
        <input type="email" name="email" id="email" value="<?= old('email') ?>" class="form-control" required autofocus>
    </div>
    <div class="form-group">
        <label for="password"><?= esc(lang('App.password')) ?></label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block"><?= esc(lang('App.login')) ?></button>
</form>

<hr>
<a href="<?= site_url('auth/google') ?>" class="btn btn-auth-social btn-block">
    <i class="fab fa-google mr-2"></i> <?= esc(lang('App.continue_with_google')) ?>
</a>
<a href="<?= site_url('auth/github') ?>" class="btn btn-auth-social btn-block">
    <i class="fab fa-github mr-2"></i> <?= esc(lang('App.continue_with_github')) ?>
</a>

<hr>
<p class="text-center">
    <?= esc(lang('App.dont_have_account')) ?> <a href="<?= site_url('register') ?>"><?= esc(lang('App.register_here')) ?></a>
</p>

<div class="alert alert-info mt-3">
    <small>
        <strong><?= esc(lang('App.demo_account')) ?></strong><br>
        <?= esc(lang('App.email')) ?>: admin@example.com<br>
        <?= esc(lang('App.password')) ?>: password123
    </small>
</div>

<?= $this->endSection() ?>
