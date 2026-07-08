<!-- LGA Summed Result -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon page-header-icon-success"><i class="bi bi-pie-chart"></i></div>
        <div>
            <h1>LGA Summed Result</h1>
            <p>Total election results aggregated by Local Government Area</p>
        </div>
    </div>
</div>

<div class="breadcrumb-nav">
    <a href="index.php">Home</a>
    <span class="sep">/</span>
    <span class="current">LGA Summed Result</span>
</div>

<!-- Selection Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="POST" action="index.php?page=lga-result" id="lgaForm">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="lga_id" class="form-label">Local Government Area</label>
                    <select class="form-select" name="lga_id" id="lga_id" required>
                        <option value="">— Select LGA —</option>
                        <?php foreach ($lgas as $lga): ?>
                            <option value="<?= $lga['uniqueid'] ?>" <?= $selected_id == $lga['uniqueid'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lga['lga_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-calculator"></i> Calculate Result
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($lga_detail !== null): ?>
    <!-- LGA Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-info-circle me-1 text-success"></i> LGA Information</h5>
        </div>
        <div class="card-body">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-item-label">LGA Name</span>
                    <span class="info-item-value"><?= htmlspecialchars($lga_detail['lga_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-item-label">State</span>
                    <span class="info-item-value"><?= htmlspecialchars($lga_detail['state_name'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-item-label">Polling Units Counted</span>
                    <span class="info-item-value"><span class="badge badge-primary"><?= $total_polling_units ?></span></span>
                </div>
            </div>
            <p class="calc-note">
                <i class="bi bi-diagram-3"></i>
                Results calculated from <?= $total_polling_units ?> polling unit(s) via LGA &rarr; Ward &rarr; PU &rarr; Results
            </p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="table-container">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <strong style="font-size:var(--font-size-sm);color:var(--color-heading);">Summed Party Scores</strong>
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Party</th>
                        <th class="text-right">Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($summed_results) > 0): ?>
                        <?php $grandTotal = 0; ?>
                        <?php foreach ($summed_results as $result): ?>
                            <?php $grandTotal += $result['total_score']; ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($result['party_abbreviation']) ?></strong></td>
                                <td class="text-right"><span class="badge badge-success"><?= number_format($result['total_score']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h4>No results found</h4>
                                    <p>This LGA has no recorded results.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (isset($grandTotal) && $grandTotal > 0): ?>
                <tfoot>
                    <tr>
                        <th>Grand Total</th>
                        <th class="text-right"><?= number_format($grandTotal) ?></th>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Please select a valid LGA.</div>
<?php endif; ?>
