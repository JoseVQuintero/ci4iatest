<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-3">
    <div class="col-12">
        <p class="mb-0"><?= esc(lang('App.product_summary_of', [session()->get('user_name')])) ?></p>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= esc($stats['total_products']) ?></h3>
                <p><?= esc(lang('App.products')) ?></p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>$<?= number_format((float) $stats['avg_price'], 2) ?></h3>
                <p><?= esc(lang('App.average_price')) ?></p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= esc($stats['products_with_offer']) ?></h3>
                <p><?= esc(lang('App.with_offer')) ?></p>
            </div>
            <div class="icon"><i class="fas fa-tags"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= esc($stats['stock_out']) ?></h3>
                <p><?= esc(lang('App.out_of_stock')) ?></p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc(lang('App.products_by_category')) ?></h3>
            </div>
            <div class="card-body">
                <canvas id="categoriesChart" height="240"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc(lang('App.products_by_brand')) ?></h3>
            </div>
            <div class="card-body">
                <canvas id="brandsChart" height="240"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc(lang('App.price_ranges')) ?></h3>
            </div>
            <div class="card-body">
                <canvas id="pricesChart" height="240"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc(lang('App.product_status')) ?></h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="240"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const categoryRows = <?= json_encode($category_rows ?? []) ?>;
    const brandRows = <?= json_encode($brand_rows ?? []) ?>;
    const priceRangeRows = <?= json_encode($price_range_rows ?? []) ?>;
    const stats = <?= json_encode($stats ?? []) ?>;
    const i18n = {
        active: <?= json_encode(lang('App.active')) ?>,
        inactive: <?= json_encode(lang('App.inactive')) ?>,
        outOfStock: <?= json_encode(lang('App.out_of_stock')) ?>
    };

    function buildBarChart(canvasId, labels, values, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: color,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    buildBarChart(
        'categoriesChart',
        categoryRows.map(item => item.category_name),
        categoryRows.map(item => Number(item.total)),
        '#1f5db9'
    );

    buildBarChart(
        'brandsChart',
        brandRows.map(item => item.brand_name),
        brandRows.map(item => Number(item.total)),
        '#2f6fcb'
    );

    buildBarChart(
        'pricesChart',
        priceRangeRows.map(item => item.price_range),
        priceRangeRows.map(item => Number(item.total)),
        '#5b8fdb'
    );

    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: [i18n.active, i18n.inactive, i18n.outOfStock],
                datasets: [{
                    data: [
                        Number(stats.active_products || 0),
                        Number(stats.inactive_products || 0),
                        Number(stats.stock_out || 0)
                    ],
                    backgroundColor: ['#2f6fcb', '#789ccf', '#1f5db9']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
</script>
<?= $this->endSection() ?>
