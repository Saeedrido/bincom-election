<!-- Polling Unit Result -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon" style="background:#FFFBEB;color:#F59E0B;"><i class="bi bi-bar-chart"></i></div>
        <div>
            <h1>Polling Unit Result</h1>
            <p>View election results for a selected polling unit</p>
        </div>
    </div>
</div>

<div class="breadcrumb">
    <a href="index.php">Home</a>
    <span class="sep">/</span>
    <span class="current">Polling Unit Result</span>
</div>

<!-- Selection Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="POST" action="index.php?page=polling-result" id="pollingForm">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="polling_unit_id" class="form-label">Polling Unit</label>
                    <select class="form-select" name="polling_unit_id" id="polling_unit_id" required>
                        <option value="">— Select Polling Unit —</option>
                        <?php foreach ($polling_units as $pu): ?>
                            <option value="<?= $pu['uniqueid'] ?>" <?= $selected_id == $pu['uniqueid'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pu['polling_unit_name'] ?? $pu['polling_unit_number']) ?>
                                (<?= htmlspecialchars($pu['polling_unit_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-eye"></i> View Result
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($polling_unit_detail !== null): ?>
    <!-- Polling Unit Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-info-circle me-1" style="color:var(--color-warning);"></i> Polling Unit Information</h5>
        </div>
        <div class="card-body">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-item-label">Name</span>
                    <span class="info-item-value"><?= htmlspecialchars($polling_unit_detail['polling_unit_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-item-label">Number</span>
                    <span class="info-item-value"><?= htmlspecialchars($polling_unit_detail['polling_unit_number'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-item-label">Ward</span>
                    <span class="info-item-value"><?= htmlspecialchars($polling_unit_detail['ward_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-item-label">LGA</span>
                    <span class="info-item-value"><?= htmlspecialchars($polling_unit_detail['lga_name'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="table-container">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong style="font-size:var(--font-size-sm);color:var(--color-heading);">Election Results</strong>
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Party</th>
                        <th style="text-align:right;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($results) > 0): ?>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($result['party_abbreviation']) ?></strong></td>
                                <td style="text-align:right;"><span class="badge badge-primary"><?= number_format($result['party_score']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h4>No results found</h4>
                                    <p>This polling unit has no recorded results.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Please select a valid polling unit.</div>
<?php endif; ?>
