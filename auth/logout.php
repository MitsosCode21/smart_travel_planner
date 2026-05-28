<?php
session_start();
session_destroy(); // Σβήνει όλα τα δεδομένα του χρήστη από τη μνήμη
header("Location: ../index.php"); // Τον γυρνάει στην αρχική σελίδα
exit();
?>