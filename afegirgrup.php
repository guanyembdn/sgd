<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(isset($_POST["nomgrup"]) && !empty($_POST["nomgrup"])){
	$sth=$con->prepare("INSERT into grups (Nom) VALUES(?)");
	if($sth->execute(array($_POST["nomgrup"]))==True){
		header("Location: editagrup?id=" . $con->lastInsertId());
	}
}
?>

<h1 class="clear">Afegir un nou grup</h1>

<form method="post">
	Nom: <input type="text" name="nomgrup" required><br>
	<input type="submit" value="Enviar">
</form>

<h2>Altres funcions</h2>

<p><a href="gestorgrups">Tornar endarrera: gestor de grups</a></p>

<?php
require_once "internal/foot.php";
?>