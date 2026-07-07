<!-- Dashboard -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-speedometer2"></i></div>
        <div>
            <h1>Dashboard</h1>
            <p>Election management system overview</p>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon blue"><i class="bi bi-geo-alt"></i></div>
            <div class="stat-card-body">
                <h3><?= number_format($total_polling_units) ?></h3>
                <p>Total Polling Units</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon green"><i class="bi bi-building"></i></div>
            <div class="stat-card-body">
                <h3><?= number_format($total_lgas) ?></h3>
                <p>Local Government Areas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-icon amber"><i class="bi bi-flag"></i></div>
            <div class="stat-card-body">
                <h3><?= number_format($total_parties) ?></h3>
                <p>Political Parties</p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Cards -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="nav-card">
            <div class="nav-card-icon amber"><i class="bi bi-bar-chart"></i></div>
            <h5>Polling Unit Result</h5>
            <p>View election results for a specific polling unit.</p>
            <a href="index.php?page=polling-result" class="btn btn-warning">View Results</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="nav-card">
            <div class="nav-card-icon green"><i class="bi bi-pie-chart"></i></div>
            <h5>LGA Summed Result</h5>
            <p>View total results summed across an entire LGA.</p>
            <a href="index.php?page=lga-result" class="btn btn-success">View Summary</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="nav-card">
            <div class="nav-card-icon blue"><i class="bi bi-plus-square"></i></div>
            <h5>Add Election Result</h5>
            <p>Add a new polling unit with election results.</p>
            <a href="index.php?page=add-result" class="btn btn-primary">Add New</a>
        </div>
    </div>
</div>
