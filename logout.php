<?php
setcookie("logged_in", '',time() -1);
header("Location: index.php");

?>