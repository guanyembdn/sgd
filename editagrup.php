<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

$isadmin = false;
$socmembre = false;

if(!isset($_GET["id"]) || empty($_GET["id"])) die();

$privat = true;
$sth = $con->prepare("SELECT Tipus FROM grups WHERE id = :gid");
$sth->bindParam(":gid", intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

if(!isset($result[0])) die();

if($result[0]["Tipus"] == 1 || $result[0]["Tipus"] == 2) $privat = false;
$public = ($result[0]["Tipus"] == 2);

if(isset($_SESSION["type"]) && $_SESSION["type"] === "1"){
	$isadmin = true;
}

$sth = $con->prepare("SELECT gp.rol, g.Tipus FROM grups AS g INNER JOIN grups_persones AS gp ON gp.idpersona = :id AND gp.idgrup = g.id AND g.id=:gid GROUP BY g.id");
$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->bindParam(":gid", intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$socmembre = isset($result[0]["rol"]);

if(!$isadmin){
	if(isset($result[0]["rol"]) && $result[0]["rol"] == "2") $isadmin = true;
	else if($privat && !isset($result[0]["rol"])) die(); // es privat i no tenim rol
}
	
if($isadmin){
	if(isset($_POST["nomgrup"]) && !empty($_POST["nomgrup"])){
		$sth=$con->prepare("UPDATE grups SET Nom=:nomgrup, Tipus=:tipus, Llista_Mail=:llista_mail, Lloc_Reunions=:lloc_reunions WHERE id=:id");
		
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$sth->bindParam(":nomgrup", $_POST["nomgrup"], PDO::PARAM_STR, 50);
		$sth->bindParam(":tipus", $_POST["tipus"], PDO::PARAM_INT);
		$sth->bindParam(":llista_mail", $_POST["llista_mail"], PDO::PARAM_STR, 200);
		$sth->bindParam(":lloc_reunions", $_POST["lloc_reunions"], PDO::PARAM_STR, 200);
		$sth->execute();
		
		if(isset($_POST["grup_telegram"])){
			$grup_telegram = $_POST["grup_telegram"];
			
			if(strlen($grup_telegram) > 13){
				if(substr($grup_telegram, 0, 7) == "http://"){
					$grup_telegram = "https://" . substr($grup_telegram, 7);
				}
				
				if(substr($grup_telegram, 0, 13) == "https://t.me/"){
					$sth=$con->prepare("UPDATE grups SET Grup_Telegram=:grup_telegram WHERE id=:id");
					$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
					$sth->bindParam(":grup_telegram", $grup_telegram, PDO::PARAM_STR, 200);
					$sth->execute();
				}
			}else{
					$sth=$con->prepare("UPDATE grups SET Grup_Telegram='' WHERE id=:id");
					$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
					$sth->execute();
			}
		}
	}

	if(isset($_POST["nom_membre_afegir"])){
		$sqlstr = "INSERT INTO grups_persones(idpersona, idgrup, rol) VALUES";
		$arr_execute = array();
		$i = 0;
		
		foreach($_POST["nom_membre_afegir"] as $x){
			if(!empty(trim($x))){
				$sqlstr .= "((SELECT id FROM persones WHERE nomusuari=?), ?, 1), ";
				array_push($arr_execute, $x, intval($_GET["id"]));
				$i++;
			}
		}
		
		if($i > 0){
			$sqlstr = rtrim($sqlstr, ', ');
			$sth = $con->prepare($sqlstr);
			$sth->execute($arr_execute);
		}
	}

	if(isset($_POST["membres"])){
		$delids = array();

		foreach($_POST["membres"] as $membre){
			if(isset($membre["delete"]) && $membre["delete"]){
				$delids[] = $membre["id"];
			}else{
				foreach($membre as $k => $v){
					switch($k){
						case "rol":
							$q = $con->prepare("UPDATE grups_persones SET rol=? WHERE id=?");
							$q->bindParam(1, intval($membre["rol"]), PDO::PARAM_INT);
							$q->bindParam(2, $membre["id"], PDO::PARAM_INT);
							$q->execute();
							break;
					}
				}
			}
		}

		if(count($delids) > 0){
			$qs = "DELETE FROM grups_persones WHERE id IN (" . str_repeat("?,", count($delids) - 1) . "?)";
			$q = $con->prepare($qs);

			$i = 1; foreach($delids as $k => $id){
				$q->bindValue($i, $id, PDO::PARAM_INT);
				$i++;
			}

			$q->execute();
		}
	}
}

if(!$socmembre && $public && isset($_POST["unirse"])){
	$sth=$con->prepare("INSERT INTO grups_persones(idpersona, idgrup, rol) VALUES(:uid, :idgrup, 1)"); // Rol usuari/a
	$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
	$sth->bindParam(":idgrup", intval($_GET["id"]), PDO::PARAM_INT);
	$sth->execute();
}

$sth=$con->prepare("SELECT * FROM grups WHERE id=?");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$row = $sth->fetch(PDO::FETCH_ASSOC);
?>

<h1 class="clear">Edició de grup</h1>
    <form method="post">
		<p class="clear">
		<label>Nom de grup:
		<input type="text" name="nomgrup" value="<?=safe_escape($row["Nom"])?>" <?=$isadmin ? "" : "readonly"?>></label><br>
		<label>Tipus grup:
		<select name="tipus" <?=$isadmin ? "" : "disabled"?>>
			<option value="0"<?=$row["Tipus"] == "0" ? " selected" : ""?>>Privat</option>
			<option value="1"<?=$row["Tipus"] == "1" ? " selected" : ""?>>Semipúblic</option>
			<option value="2"<?=$row["Tipus"] == "2" ? " selected" : ""?>>Públic</option>
		</select></label><br>
		<label>Link Grup Telegram<?php if($row["Grup_Telegram"] != "") echo ' (<a href="' . safe_escape($row["Grup_Telegram"]) . '" target="_blank">link</a>)'; ?>:
		<input type="text" name="grup_telegram" value="<?=safe_escape($row["Grup_Telegram"])?>" <?=$isadmin ? "" : "readonly"?> maxlength="200" title="Només pot contenir lletres ('A-Z'), nombres ('0-9'), '-' i '_'"></label><br>
		<label>Llista Mail:
		<input type="text" name="llista_mail" value="<?=safe_escape($row["Llista_Mail"])?>" <?=$isadmin ? "" : "readonly"?> maxlength="200"></label><br>
		<label>Lloc Reunions:<br>
		<textarea name="lloc_reunions" rows="3" cols="38" <?=$isadmin ? "" : "readonly"?>><?=safe_escape($row["Lloc_Reunions"])?></textarea></label><br>
		<?php if($isadmin) { ?> <input type="submit" value="Desa tots els canvis"> <?php } ?>
		<?php if(!$socmembre && $public && !isset($_POST["unirse"])) { ?> <br><input type="submit" name="unirse" value="Unir-se al grup<?=$isadmin ? " i desa tots els canvis" : ""?>"> <?php } ?>
	
	<p><strong>Semipúblic</strong> vol dir que tothom pot veure el grup, pero el/la usuari/a no es pot afegir a si mateix/a al grup.</p>

<?php
if($socmembre || $public || $isadmin){
?>

<h2>Membres del grup</h2>

<table class="sortable">
<tr>
<th>Nom compte</th>
<th>Alies Telegram</th>
<th>Permisos en el grup</th>
<?php if($isadmin) { ?> <th>Eliminar</th> <?php } ?>
</tr>

<?php
$sth = $con->prepare("SELECT gp.id, p.nomusuari, p.Nom_i_Cognoms, gp.rol, p.Alies_Telegram FROM persones AS p INNER JOIN grups_persones AS gp ON gp.idgrup = ? AND p.id = gp.idpersona");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row){
	?>

	<tr>
		<input type="hidden" name="membres[<?=$row["id"]?>][id]" value="<?=$row["id"]?>"> 
		<td><?=safe_escape($row["nomusuari"])?></td>
		<td><a href="https://t.me/<?=safe_escape($row["Alies_Telegram"])?>" target="_blank"><?=safe_escape($row["Alies_Telegram"])?></a></td>
		<td>
			<select  <?=$isadmin ? "" : "disabled"?> name="membres[<?=$row["id"]?>][rol]">
				<option value="0"<?=$row["rol"] == "0" ? " selected" : ""?>>Pot veure</option>
				<option value="1"<?=$row["rol"] == "1" ? " selected" : ""?>>Usuari/a grup</option>
				<option value="2"<?=$row["rol"] == "2" ? " selected" : ""?>>Administrador/a grup</option>
			</select>
			</td>
		<?php if($isadmin) { ?> <td><label><input type="checkbox" name="membres[<?=$row["id"]?>][delete]"> Eliminar</label></td> <?php } ?>
	</tr>

	<?php
}
?>

</table>

<?php
}
?>

<?php if($isadmin) { ?> <br><input type="submit" value="Desa tots els canvis"> <?php } ?>

<h3 class="clear">Reunions del grup</h3>

<table class="sortable">
<tr>
<th>Nom</th>
<th>El meu estat</th>
<th>Data i hora</th>
<th>Veure...</th>
</tr>

<?php
$sth = $con->prepare("SELECT r.id, r.Nom, Date_format(r.Data, '%Y-%m-%d %H:%i') AS Data, rp.tipus, IF(r.gid IS NULL,'🌐 Global',g.Nom) AS GrupNom, TIMEDIFF(r.Data, NOW()) AS datediff FROM reunions AS r LEFT JOIN reunions_persones AS rp ON rp.rid=r.id AND rp.uid=:uid INNER JOIN grups AS g ON r.gid IS NOT NULL AND r.gid=g.id AND r.gid=:mygid");
$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->bindParam(":mygid", intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();

foreach($sth->fetchAll() as $reunio_row){
	$tancada = $reunio_row["datediff"] < 0;
	if($isadmin || (isset($reunio_row["tipus"]) && is_numeric($reunio_row["tipus"]) && intval($reunio_row["tipus"]) >= 1)){
	?>

	<tr>
		<td><?=safe_escape($reunio_row["Nom"])?></td>
		<td>
		<?php
		switch($reunio_row["tipus"]){
					case "1":
						echo "Convocat";
						break;
					case "2":
						echo "Aniré";
						break;
					case "3":
						echo "He anat";
						break;
					case "4":
						echo "Excusa";
						break;
					default:
						echo "Cap";
						break;
		}
		?>
		</td>
		<td><?=$tancada ? "<span class='span_temps_pasat'>←</span>" : "<span class='span_temps_futur'>→</span>"?><?=$reunio_row["Data"]?></td>
		<td><a href="veurereunio?id=<?=$reunio_row["id"]?>">Veure...</a></td>
	</tr>

	<?php
	}
}
?>

</table>

<h3 class="clear">Votacions del grup</h3>

<table class="sortable">
<tr>
<th>Pregunta</th>
<th>Estat</th>
<th>Data comença</th>
<th>Data tanca</th>
<th>Veure...</th>
</tr>

<?php
$sth = $con->prepare("SELECT v.id, v.pregunta, v.quipotvotar, IF(v.gid IS NULL,'🌐 Global',g.Nom) AS GrupNom, v.startdate, v.autotanca, v.closedate, v.tipus, DATEDIFF(v.closedate, CURDATE()) AS datediff FROM votacions AS v INNER JOIN grups AS g ON v.gid IS NOT NULL AND v.gid=g.id AND g.id=:mygid");
$sth->bindParam(":mygid", intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach($result as $votacions_row){
	$nomesmpd = $votacions_row["quipotvotar"] == 0;
	
	if($isadmin || !$nomesmpd || $_SESSION["tipus_membre_partit"] == 2){
		$tancada = $votacions_row["tipus"] == 0 || ($votacions_row["autotanca"] == 1 && $votacions_row["datediff"] < 0);
		?>

		<tr>
			<td><?=safe_escape($votacions_row["pregunta"])?></td>
			<td><?=$tancada ? "🔴 Tancada" : "🔵 Oberta"?></td>
			<td><?=$votacions_row["startdate"]?></td>
			<td><?=$votacions_row["closedate"]?></td>
			<td><a href="veurevotacio?id=<?=$votacions_row["id"]?>">Veure...</a></td>
		</tr>

		<?php
	}
}
?>

</table>

<?php
if($isadmin){
?>

	<h3>Afegir membre al grup</h3>
	<p>Nom(s) de compte(s):
	
	<table id="afegir_form" data-no-minimize="no-minimize">
	<input type="text" name="nom_membre_afegir[0]" id="txtbox0" list="membres_list_noms">
	</table>
	<datalist id="membres_list_noms">
		<?php
			foreach($con->query("SELECT nomusuari, Nom_i_Cognoms FROM persones") as $rownoms){
				echo "<option value=\"" . safe_escape($rownoms["nomusuari"]) . "\" label=\"" . safe_escape($rownoms["nomusuari"]) . " (" . safe_escape($rownoms["Nom_i_Cognoms"]) . ")\">";
			}
		?>
	</datalist><br>
	<input type="submit" value="Desa tots els canvis">
	</form>
	
	<h2>Tasques de grup</h2>
	
	<p>
		<ul>
			<li><a href="afegirvotacio?gid=<?=intval($_GET["id"])?>">Afegir votació a aquest grup</a></li>
			<li><a href="afegirreunio?gid=<?=intval($_GET["id"])?>">Afegir reunió a aquest grup</a></li>
			<li><a href="email?gid=<?=intval($_GET["id"])?>">Enviar e-mail al grup</a></li>
		</ul>
	</p>
	
<?php
}
?>

<h2>Altres opcions</h2>
<p>
	<ul>
		<?php
		if($isadmin){
		?>
			<li><a href="eliminargrup?id=<?=intval($_GET["id"])?>">Eliminar aquest grup</a></li>
		<?php
		}
		?>
		<li><a href="editameugrups">Els meu grups</a></li>
		<?php if($_SESSION["type"] == 1){ ?> <li><a href="gestorgrups">Gestor de grups (admin)</li> <?php } ?>
	</ul>
</p>

<?php
require_once "internal/foot.php";
?>