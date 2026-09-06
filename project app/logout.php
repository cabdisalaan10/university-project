<?php

// Clears the current user session and returns to the login screen.
require_once 'includes/config.php';
session_destroy();
session_start();
flash('You have signed out.');
redirect('login.php');
