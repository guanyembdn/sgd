<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

$isadmin = false;
$gid = null;

if(isset($_GET["gid"])){
	$isadmin = chkgrouporglobaladmin($con, $_GET["gid"]);
	$gid = intval($_GET["gid"]);
}else{
	$isadmin = isadmin();
}

if($isadmin){
	if(isset($_POST["nom"]) && !empty($_POST["nom"])
		&& isset($_POST["data"]) && !empty($_POST["data"])
	&& isset($_POST["lloc"]) && !empty($_POST["lloc"]))
		{
		$sth=$con->prepare("INSERT INTO reunions (nom, gid, data, lloc) VALUES(?, ?, ?, ?)");
		if($sth->execute(array($_POST["nom"], $gid, $_POST["data"], $_POST["lloc"]))==True){
			$rid = $con->lastInsertId();
			header("Location: veurereunio?id=" . $rid);
		}
	}
?>

<h1 class="clear">Afegir una nova reunió</h1>

<?php
if($gid != null) echo "<p>Estás creant una reunió de àmbit <strong>grup</strong>.</p>";
else echo "<p><p>Estás creant una reunió de àmbit <strong>global</strong>.</p>";
?>

<form method="post">
	<p><label>Nom: <input name="nom" type="text" required></label><br>
	<label>Data i hora: <input name="data" type="datetime-local" required value="<?=date('Y-m-d\TH:i')?>"><br>
	<label>Lloc:<br><textarea name="lloc" rows="5" cols="38" required></textarea></label>
	<br>
	<input type="submit" value="Enviar"></p>
</form>

<h2>Altres funcions</h2>
<p>
<?php
if(isset($_GET["gid"])) echo '<a href="editagrup?id=' . intval($_GET["gid"]) . '">Tornar al grup</a><br>';
if(isadmin()) echo '<a href="gestorreunions">Anar al gestor de reunions (admin)</a>';
?>
</p>

<?php
}
require_once "internal/foot.php";
?>