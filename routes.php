<?php

require_once __DIR__ . '/controllers/PollingController.php';

$controller = new PollingController();
$page = $_GET['page'] ?? 'home';

try {
    switch ($page) {
        case 'home':
        case '':
            $controller->home();
            break;

        case 'polling-result':
            $controller->pollingUnitResult();
            break;

        case 'lga-result':
            $controller->lgaResult();
            break;

        case 'add-result':
            $controller->addResult();
            break;

        case 'search':
            $controller->search();
            break;

        case 'get-wards':
            $controller->getWards();
            break;

        default:
            http_response_code(404);
            $pageTitle = '404 Not Found';
            include __DIR__ . '/includes/header.php';
            include __DIR__ . '/includes/navbar.php';
            echo '<div class="container mt-5">';
            echo '<div class="text-center">';
            echo '<h1 class="display-1 text-muted">404</h1>';
            echo '<p class="lead">Page not found.</p>';
            echo '<a href="index.php" class="btn btn-primary"><i class="bi bi-house-door-fill me-1"></i> Go Home</a>';
            echo '</div>';
            echo '</div>';
            include __DIR__ . '/includes/footer.php';
            break;
    }
} catch (PDOException $e) {
    $pageTitle = 'Database Error';
    include __DIR__ . '/includes/header.php';
    include __DIR__ . '/includes/navbar.php';
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger">';
    echo '<h4><i class="bi bi-database-slash me-1"></i> Database Connection Error</h4>';
    echo '<p>Could not connect to the database. Please check your configuration.</p>';
    echo '<hr>';
    echo '<p class="mb-0"><small>Ensure MySQL is running and the database exists.</small></p>';
    echo '</div>';
    echo '</div>';
    include __DIR__ . '/includes/footer.php';
} catch (Exception $e) {
    $pageTitle = 'Error';
    include __DIR__ . '/includes/header.php';
    include __DIR__ . '/includes/navbar.php';
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger">';
    echo '<h4><i class="bi bi-exclamation-triangle me-1"></i> Application Error</h4>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    echo '</div>';
    include __DIR__ . '/includes/footer.php';
}
