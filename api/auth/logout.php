<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Start a new session for the success message
session_start();
$_SESSION['success'] = 'Successfully logged out!';

// Redirect to homepage
header('Location: ../../index.php');
exit;
