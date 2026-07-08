<!-- Add New Result -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-plus-square"></i></div>
        <div>
            <h1>Add New Polling Unit Result</h1>
            <p>Create a new polling unit and record election results</p>
        </div>
    </div>
</div>

<div class="breadcrumb-nav">
    <a href="index.php">Home</a>
    <span class="sep">/</span>
    <span class="current">Add New Result</span>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible">
        <i class="bi bi-check-circle"></i>
        <span><?= htmlspecialchars($success) ?></span>
        <button class="btn-close" data-bs-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger alert-dismissible">
        <i class="bi bi-exclamation-triangle"></i>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin:4px 0 0;padding-left:18px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button class="btn-close" data-bs-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?page=add-result" id="addResultForm">

    <!-- Polling Unit Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-geo-alt me-1 text-primary"></i> Polling Unit Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="polling_unit_name" class="form-label">Polling Unit Name <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" class="form-control" id="polling_unit_name" name="polling_unit_name"
                           value="<?= htmlspecialchars($_POST['polling_unit_name'] ?? '') ?>"
                           placeholder="e.g. Agodi Primary School Hall" required>
                    <div class="invalid-feedback">Please enter the polling unit name.</div>
                </div>
                <div class="col-md-6">
                    <label for="polling_unit_number" class="form-label">Polling Unit Number <span style="color:var(--color-danger);">*</span></label>
                    <input type="text" class="form-control" id="polling_unit_number" name="polling_unit_number"
                           value="<?= htmlspecialchars($_POST['polling_unit_number'] ?? '') ?>"
                           placeholder="e.g. PU008" required>
                    <div class="invalid-feedback">Please enter a unique polling unit number.</div>
                </div>
                <div class="col-md-4">
                    <label for="state_id" class="form-label">State <span style="color:var(--color-danger);">*</span></label>
                    <select class="form-select" name="state_id" id="state_id" required>
                        <option value="">— Select State —</option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?= $state['state_id'] ?>" <?= ($_POST['state_id'] ?? '') == $state['state_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($state['state_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="lga_id" class="form-label">LGA <span style="color:var(--color-danger);">*</span></label>
                    <select class="form-select" name="lga_id" id="lga_id" required>
                        <option value="">— Select LGA —</option>
                        <?php foreach ($lgas as $lga): ?>
                            <option value="<?= $lga['uniqueid'] ?>" <?= ($_POST['lga_id'] ?? '') == $lga['uniqueid'] ? 'selected' : '' ?>
                                    data-state-id="<?= $lga['state_id'] ?? '' ?>">
                                <?= htmlspecialchars($lga['lga_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ward_id" class="form-label">Ward <span style="color:var(--color-danger);">*</span></label>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <select class="form-select" name="ward_id" id="ward_id" required style="flex:1;">
                            <option value="">— Select Ward —</option>
                        </select>
                        <div class="spinner" id="wardLoader" style="display:none;"></div>
                    </div>
                </div>
                <div class="col-12">
                    <label for="polling_unit_description" class="form-label">Description</label>
                    <textarea class="form-control" id="polling_unit_description" name="polling_unit_description"
                              rows="2" placeholder="Optional description"><?= htmlspecialchars($_POST['polling_unit_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Election Results -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-table me-1 text-primary"></i> Election Results</h5>
        </div>
        <div class="card-body">
            <p class="calc-note" style="margin-bottom:var(--space-3);">
                <i class="bi bi-info-circle me-1"></i> Enter the score for each party. Only numeric values are accepted.
            </p>
            <div class="table-container" style="border:none;border-radius:0;box-shadow:none;">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:50%;">Party</th>
                                <th style="width:50%;">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parties as $party): ?>
                                <tr>
                                    <td>
                                        <div class="party-name-cell">
                                            <strong><?= htmlspecialchars($party['partyid']) ?></strong>
                                            <span class="party-full-name"><?= htmlspecialchars($party['party_name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number"
                                               class="form-control party-score party-score-input"
                                               name="score_<?= htmlspecialchars($party['partyid']) ?>"
                                               id="score_<?= htmlspecialchars($party['partyid']) ?>"
                                               value="<?= htmlspecialchars($_POST['score_' . $party['partyid']] ?? '') ?>"
                                               min="0"
                                               placeholder="Enter score">
                                        <div class="invalid-feedback">Please enter a valid numeric score.</div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-3 justify-content-end mb-5">
        <button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
            <i class="bi bi-save"></i> Submit Result
        </button>
    </div>
</form>
