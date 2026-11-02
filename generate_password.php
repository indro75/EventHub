<?php
// Generate password hash for 'admin123'
echo "Password hash for 'admin123':<br>";
echo password_hash('admin123', PASSWORD_DEFAULT);
?>