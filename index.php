<?php
/**
 * index.php - Application Entry Point
 *
 * All requests are routed through this file.
 * Uses simple routing to load the appropriate controller action.
 */

session_start();

// Load the router which handles all page routing
require_once __DIR__ . '/routes.php';
