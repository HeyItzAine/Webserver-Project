<?php
session_start();
session_unset();
session_destroy();

// After session is cleared, redirect to index.html
header('Location: /index.html');
exit;
?>
