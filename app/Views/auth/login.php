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
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= old('email') ?>" class="form-control" required autofocus>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Login</button>
</form>

<hr>
<a href="<?= site_url('auth/google') ?>" class="btn btn-danger btn-block">
    <i class="fab fa-google mr-2"></i> Continue with Google
</a>
<a href="<?= site_url('auth/github') ?>" class="btn btn-dark btn-block">
    <i class="fab fa-github mr-2"></i> Continue with GitHub
</a>

<hr>
<p class="text-center">
    Don't have an account? <a href="<?= site_url('register') ?>">Register here</a>
</p>

<div class="alert alert-info mt-3">
    <small>
        <strong>Demo Account:</strong><br>
        Email: admin@example.com<br>
        Password: password123
    </small>
</div>

<?= $this->endSection() ?>
