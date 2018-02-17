<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(!isset($_GET["id"]) || empty($_GET["id"])){
	die();
}

$isadmin = false;

if(isset($_SESSION["type"]) && $_SESSION["type"] === "1"){
	$isadmin = true;
}

if(!$isadmin){
	$sth = $con->prepare("SELECT gp.rol, g.Tipus FROM grups AS g INNER JOIN grups_persones AS gp ON gp.idpersona = :id AND gp.idgrup = g.id AND g.id=:gid GROUP BY g.id");
	$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
	$sth->bindParam(":gid", intval($_GET["id"]), PDO::PARAM_INT);
	$sth->execute();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);

	if(isset($result[0]["rol"]) && $result[0]["rol"] == "2") $isadmin = true;
}

if($isadmin){
	if(isset($_POST["id"]) && !empty($_GET["id"])){
			$sth = $con->prepare("DELETE FROM grups WHERE id=?");
			$sth->execute(array(intval($_POST["id"])));
			?>
			
			<p>El grup ha segut eliminat.
			<ul>
			<?php
			if(!isadmin()) echo '<li><a href="editameugrups">Anar a els meus grups</a></li>';
			else echo '<li><a href="gestorgrups">Anar al gestor de grups</a></li>';
			?>
			</ul></p>
			
			<?php
				}else{
			?>

				<h1>Eliminació de grup</h1>

				<p>Estàs segur/a?</p>
				<form method="post">
				<input type="hidden" name="id" value="<?=intval($_GET["id"])?>">

				<h2>Altres opcions</h2>

				<input type="submit" value="Eliminar grup">
				</form>

				<a href="editagrup?id=<?=intval($_GET["id"])?>">Tornar endarrera</a>

				<?php
	}
}

require_once "internal/foot.php";
?>