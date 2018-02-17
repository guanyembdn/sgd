<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(!isset($_GET["id"]) || empty($_GET["id"])){
	die();
}

$sth=$con->prepare("SELECT * FROM votacions WHERE id = ?");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$votacions_row = $sth->fetchAll(PDO::FETCH_ASSOC);
if(!isset($votacions_row[0])) die();
$gid = $votacions_row[0]["gid"];

if(isset($gid) && $gid != null){ // votació de grup
	$isadmin = chkgrouporglobaladmin($con, $gid);
	$rol = getgrouprole($con, $gid);
	if(!$isadmin && $rol != 1) die(); // no som admins globals ni admins del grup ni membres del grup
}else{ // votació global
	$isadmin = isadmin();
	if(!$isadmin && ($_SESSION["type"] != 2 && $_SESSION["type"] != 3)) die(); // no som membres validats
}

if(!$isadmin) die();

if(isset($_POST["id"]) && !empty($_GET["id"])){
		$sth = $con->prepare("UPDATE votacions SET tipus=0, closedate=CURDATE() WHERE id=?");
		$sth->execute(array(intval($_POST["id"])));
		?>
		
		<p>La votació ha sigut tancada.<br>
		<ul>
		<li><a href="veurevotacio?id=<?=intval($_GET["id"])?>">Anar a la votació</a></li>
		<li><a href="lesmevesvotacions">Anar a les meves votacions</a></li>
		<?php if($isadmin) { ?> <li><a href="gestorvotacions">Anar al gestor de votacions</a></li> <?php } ?>
		</ul>
		</p>
		
		<?php
}else{
?>

<h1>Tancar aquesta votació</h1>

<p>Estàs segur/a?</p>
<form method="post">
<input type="hidden" name="id" value="<?=intval($_GET["id"])?>">

<h2>Altres opcions</h2>

<input type="submit" value="Tancar votació">
</form>

<a href="veurevotacio?id=<?=intval($_GET["id"])?>">Tornar endarrera</a>

<?php
}

require_once "internal/foot.php";
?>