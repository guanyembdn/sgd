<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(!isset($_GET["id"]) || empty($_GET["id"])){
	die();
}

$sth=$con->prepare("SELECT * FROM reunions WHERE id = ?");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$reunions_row = $sth->fetchAll(PDO::FETCH_ASSOC);
if(!isset($reunions_row[0])) die();
$gid = $reunions_row[0]["gid"];

if(isset($gid) && $gid != null){ // reunió de grup
	$isadmin = chkgrouporglobaladmin($con, $gid);
	$rol = getgrouprole($con, $gid);
	if(!$isadmin && $rol != 1) die(); // no som admins globals ni admins del grup ni membres del grup
}else{ // reunió global
	$isadmin = isadmin();
	if(!$isadmin && ($_SESSION["type"] != 2 && $_SESSION["type"] != 3)) die(); // no som membres validats
}

if(!$isadmin) die();

if(isset($_POST["id"]) && !empty($_GET["id"])){
		$sth = $con->prepare("DELETE FROM reunions WHERE id=?");
		$sth->execute(array(intval($_POST["id"])));
		?>
		
		<p>La reunió ha sigut eliminada.<br>
		<ul>
		<?php if($isadmin) { ?> <li><a href="gestorreunions">Anar al gestor de reunions</a></li> <?php } ?>
		</ul>
		</p>
		
		<?php
}else{
?>

<h1>Eliminar aquesta reunió</h1>

<p>Estàs segur/a?</p>
<form method="post">
<input type="hidden" name="id" value="<?=intval($_GET["id"])?>">

<h2>Altres opcions</h2>

<input type="submit" value="Eliminar reunió">
</form>

<a href="veurereunio?id=<?=intval($_GET["id"])?>">Tornar endarrera</a>

<?php
}

require_once "internal/foot.php";
?>