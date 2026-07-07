<?php

require_once __DIR__ . '/../models/PollingModel.php';

class PollingController
{
    private PollingModel $model;

    public function __construct()
    {
        $this->model = new PollingModel();
    }

    // Dashboard — show summary counts
    public function home(): void
    {
        $totalPollingUnits = $this->model->getTotalPollingUnitCount();
        $lgas = $this->model->getAllLgas();
        $parties = $this->model->getAllParties();

        $this->render('home', [
            'total_polling_units' => $totalPollingUnits,
            'total_lgas'          => count($lgas),
            'total_parties'       => count($parties),
            'lgas'                => $lgas,
        ]);
    }

    // Question 1 — Pick a polling unit and see its results
    public function pollingUnitResult(): void
    {
        $pollingUnits = $this->model->getAllPollingUnits();
        $pollingUnitDetails = null;
        $results = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['polling_unit_id'])) {
            $pollingUnitId = (int)$_POST['polling_unit_id'];
            if ($pollingUnitId > 0) {
                $pollingUnitDetails = $this->model->getPollingUnitDetails($pollingUnitId);
                $results = $this->model->getPollingUnitResults($pollingUnitId);
            }
        }

        $this->render('polling-result', [
            'polling_units'       => $pollingUnits,
            'polling_unit_detail' => $pollingUnitDetails,
            'results'             => $results,
            'selected_id'         => $_POST['polling_unit_id'] ?? '',
        ]);
    }

    // Question 2 — Sum up all party scores across an entire LGA
    // We chain LGA → Ward → PU → announced_pu_results ourselves
    // instead of relying on the pre-aggregated announced_lga_results table.
    public function lgaResult(): void
    {
        $lgas = $this->model->getAllLgas();
        $summedResults = [];
        $lgaDetails = null;
        $totalPollingUnits = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lga_id'])) {
            $lgaId = (int)$_POST['lga_id'];
            if ($lgaId > 0) {
                $lgaDetails = $this->model->getLgaDetails($lgaId);
                $summedResults = $this->model->getLgaSummedResults($lgaId);
                $totalPollingUnits = $this->model->getPollingUnitCountByLga($lgaId);
            }
        }

        $this->render('lga-result', [
            'lgas'                => $lgas,
            'lga_detail'          => $lgaDetails,
            'summed_results'      => $summedResults,
            'total_polling_units' => $totalPollingUnits,
            'selected_id'         => $_POST['lga_id'] ?? '',
        ]);
    }

    // Question 3 — Add a brand new polling unit along with its results
    // Everything runs inside a DB transaction so it's all-or-nothing.
    public function addResult(): void
    {
        $lgas = $this->model->getAllLgas();
        $states = $this->model->getAllStates();
        $parties = $this->model->getAllParties();
        $success = null;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAddResult();

            if (empty($errors)) {
                try {
                    $db = Database::getInstance();
                    $db->beginTransaction();

                    $pollingUnitId = $this->model->insertPollingUnit([
                        'polling_unit_id'          => time(),
                        'ward_id'                  => (int)$_POST['ward_id'],
                        'lga_id'                   => (int)$_POST['lga_id'],
                        'state_id'                 => (int)$_POST['state_id'],
                        'polling_unit_number'      => trim($_POST['polling_unit_number']),
                        'polling_unit_name'        => trim($_POST['polling_unit_name']),
                        'polling_unit_description' => trim($_POST['polling_unit_description'] ?? ''),
                        'entered_by_user'          => 'admin',
                    ]);

                    $scores = [];
                    foreach ($parties as $party) {
                        $abbr = $party['partyid'];
                        if (isset($_POST['score_' . $abbr]) && $_POST['score_' . $abbr] !== '') {
                            $scores[$abbr] = (int)$_POST['score_' . $abbr];
                        }
                    }

                    $this->model->insertPollingUnitResults($pollingUnitId, $scores);

                    $db->commit();
                    $success = 'Polling unit and results added successfully!';
                    $_POST = [];

                } catch (Exception $e) {
                    $db->rollback();
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }

        $this->render('add-result', [
            'lgas'    => $lgas,
            'states'  => $states,
            'parties' => $parties,
            'success' => $success,
            'errors'  => $errors,
        ]);
    }

    // AJAX — search polling units
    public function search(): void
    {
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 1) {
            echo json_encode([]);
            return;
        }
        echo json_encode($this->model->searchPollingUnits($query));
    }

    // AJAX — load wards when an LGA is selected
    public function getWards(): void
    {
        header('Content-Type: application/json');
        $lgaId = (int)($_GET['lga_id'] ?? 0);
        if ($lgaId <= 0) {
            echo json_encode([]);
            return;
        }
        echo json_encode($this->model->getWardsByLga($lgaId));
    }

    // Make sure the form data is valid before we touch the database
    private function validateAddResult(): array
    {
        $errors = [];

        if (empty(trim($_POST['polling_unit_name'] ?? ''))) {
            $errors[] = 'Polling Unit Name is required.';
        }

        if (empty(trim($_POST['polling_unit_number'] ?? ''))) {
            $errors[] = 'Polling Unit Number is required.';
        } elseif ($this->model->pollingUnitNumberExists(trim($_POST['polling_unit_number']))) {
            $errors[] = 'Polling Unit Number already exists.';
        }

        if (empty($_POST['ward_id']) || (int)$_POST['ward_id'] <= 0) {
            $errors[] = 'Please select a Ward.';
        }

        if (empty($_POST['lga_id']) || (int)$_POST['lga_id'] <= 0) {
            $errors[] = 'Please select an LGA.';
        }

        if (empty($_POST['state_id']) || (int)$_POST['state_id'] <= 0) {
            $errors[] = 'Please select a State.';
        }

        $parties = $this->model->getAllParties();
        $hasScore = false;
        foreach ($parties as $party) {
            $abbr = $party['partyid'];
            if (isset($_POST['score_' . $abbr]) && $_POST['score_' . $abbr] !== '') {
                if (!ctype_digit($_POST['score_' . $abbr]) && !is_int($_POST['score_' . $abbr])) {
                    $errors[] = "Score for {$abbr} must be a valid number.";
                } elseif ((int)$_POST['score_' . $abbr] < 0) {
                    $errors[] = "Score for {$abbr} cannot be negative.";
                } else {
                    $hasScore = true;
                }
            }
        }

        if (!$hasScore) {
            $errors[] = 'Please enter at least one party score.';
        }

        return $errors;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);

        $pageTitle = match ($view) {
            'home'           => 'Dashboard',
            'polling-result' => 'Polling Unit Result',
            'lga-result'     => 'LGA Summed Result',
            'add-result'     => 'Add New Result',
            default          => 'Bincom Election',
        };

        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        include __DIR__ . '/../includes/header.php';
        include __DIR__ . '/../includes/navbar.php';

        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo '<div class="container mt-5"><div class="alert alert-danger">View not found: ' . htmlspecialchars($view) . '</div></div>';
        }

        include __DIR__ . '/../includes/footer.php';
    }
}
