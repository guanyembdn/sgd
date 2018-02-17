<?php
require_once "internal/head.php";

if(isset($_SESSION['user'])){
	session_destroy();
}

header("Location: /");
?>

<?php
require_once "internal/foot.php";
?>