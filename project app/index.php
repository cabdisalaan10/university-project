<?php

// Sends visitors to the correct first page based on their login state.
require_once 'includes/config.php';
redirect(logged_in() ? 'dashboard.php' : 'login.php');
