<?php
session_start();
session_unset();
session_destroy();
?>
<script>
    localStorage.clear();
    window.location.href = "frigo.php";
</script>
