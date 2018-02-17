<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(!isset($_GET["id"]) || empty($_GET["id"])){
	die();
}

if(isset($_POST["id"]) && !empty($_GET["id"])){
		$sth = $con->prepare("DELETE FROM persones WHERE id=?");
		$sth->execute(array(intval($_POST["id"])));
		?>
		
		<p>La compte ha sigut eliminada.<br>
		<a href="gestorusuaris">Anar al gestor de comptes</a></p>
		
		<?php
}else{
?>

<h1>Eliminació de compte</h1>

<p>Estàs segur/a?</p>
<form method="post">
<input type="hidden" name="id" value="<?=intval($_GET["id"])?>">

<h2>Altres opcions</h2>

<input type="submit" value="Eliminar compte">
</form>

<a href="editausuari?id=<?=intval($_GET["id"])?>">Tornar endarrera</a>

<?php
}

require_once "internal/foot.php";
?>