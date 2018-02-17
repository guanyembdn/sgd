<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

$sth=$con->prepare("SELECT r.*, rp.tipus, TIMEDIFF(data, NOW()) AS datediff, Date_format(Data, '%Y-%m-%dT%H:%i') AS isodate FROM reunions AS r LEFT JOIN reunions_persones AS rp ON rp.rid = r.id AND rp.uid = ? WHERE r.id = ?");
$sth->bindParam(1, intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->bindParam(2, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$reunions_row = $sth->fetchAll(PDO::FETCH_ASSOC);
if(!isset($reunions_row[0])) die();
$gid = $reunions_row[0]["gid"];

$tancada = $reunions_row[0]["datediff"] < 0;
$convocat = $reunions_row[0]["tipus"] > 0; // He sigut convocat?

if(isset($gid) && $gid != null){ // reunió de grup
	$isadmin = chkgrouporglobaladmin($con, $gid);
	$rol = getgrouprole($con, $gid);
	if(!$isadmin && !$convocat) die(); // no som admins globals ni admins del grup ni hem sigut convocats
}else{ // reunió global
	$isadmin = isadmin();
	if(!$isadmin && !$convocat) die(); // no som admins globals ni admins del grup ni hem sigut convocats
}

$data_new = "";

if($isadmin){
	if(isset($_POST["nom"]) && !empty($_POST["nom"])){
		// Purificar HTML de campos de edición de texto enriquecido
		require_once "internal/HTMLPurifier/HTMLPurifier.auto.php";
		$htmlp_config = HTMLPurifier_Config::createDefault();
		$htmlp_config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
		$htmlp_config->set('HTML.Doctype', 'HTML 4.01 Transitional'); // replace with your doctype
		$htmlp_config->set('HTML.AllowedElements', array('p','br','strong','b','em','i','u','h1','h2','h3','h4','h5','h6','s','strike','sup','sub','blockquote','ol','ul','li','font','div'));
		$htmlp_config->set('HTML.AllowedAttributes', array('font.color', 'font.size', 'font.face'));
		$htmlp = new HTMLPurifier($htmlp_config);
		
		$ordre_clean = $htmlp->purify($_POST["ordre"]);
		$acta_clean = $htmlp->purify($_POST["acta"]);
		
		$sth=$con->prepare("UPDATE reunions SET Nom=:nom, Data=:data, Lloc=:lloc, Ordre=:ordre, Acta=:acta WHERE id=:id");
		
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$sth->bindParam(":nom", $_POST["nom"], PDO::PARAM_STR, 50);
		$sth->bindParam(":data", $_POST["data"], PDO::PARAM_INT);
		$sth->bindParam(":lloc", $_POST["lloc"], PDO::PARAM_STR);
		$sth->bindParam(":ordre", $ordre_clean, PDO::PARAM_STR);
		$sth->bindParam(":acta", $acta_clean, PDO::PARAM_STR);
		$sth->execute();
		
		$data_new = $_POST["data"];
		
		$sth=$con->prepare("SELECT *, TIMEDIFF(data, NOW()) AS datediff FROM reunions WHERE id = ?");
		$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
		$sth->execute();
		$reunions_row = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		if(isset($_POST["convocar-tothom"])){
			$sth = $con->prepare("INSERT INTO reunions_persones (uid, rid, tipus) SELECT p.id, ?, 1 FROM persones AS p ON DUPLICATE KEY UPDATE reunions_persones.id=reunions_persones.id");
			$sth->execute(array(intval($_GET["id"])));
		}else{
			if(isset($_POST["convocar-grup"])){
				$sth = $con->prepare("INSERT INTO reunions_persones (uid, rid, tipus) SELECT gp.idpersona, ?, 1 FROM grups_persones AS gp WHERE gp.idgrup = ? AND gp.rol > 0 ON DUPLICATE KEY UPDATE reunions_persones.id=reunions_persones.id");
				$sth->execute(array(intval($_GET["id"]), intval($gid)));
			}
			
			if(isset($_POST["nom_membre_afegir"])){
				$sqlstr = "INSERT INTO reunions_persones(uid, rid, tipus) VALUES";
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
		}

		if(isset($_POST["membres"])){
			$delids = array();

			foreach($_POST["membres"] as $membre){
				if(isset($membre["delete"]) && $membre["delete"]){
					$delids[] = $membre["id"];
				}else{
					foreach($membre as $k => $v){
						switch($k){
							case "tipus":
								$q = $con->prepare("UPDATE reunions_persones SET tipus=? WHERE id=?");
								$q->bindParam(1, intval($membre["tipus"]), PDO::PARAM_INT);
								$q->bindParam(2, $membre["id"], PDO::PARAM_INT);
								$q->execute();
								break;
						}
					}
				}
			}

			if(count($delids) > 0){
				$qs = "DELETE FROM reunions_persones WHERE id IN (" . str_repeat("?,", count($delids) - 1) . "?)";
				$q = $con->prepare($qs);

				$i = 1; foreach($delids as $k => $id){
					$q->bindValue($i, $id, PDO::PARAM_INT);
					$i++;
				}

				$q->execute();
			}
		}
	}
}else{
	$ltipus = $reunions_row[0]["tipus"];
	
	if(!$tancada && ($ltipus == 1 || $ltipus == 2 || $ltipus == 4)){
		if(isset($_POST["self_rsvp"])){
			if($_POST["self_rsvp"] == "no") $ltipus = 1;
			else if($_POST["self_rsvp"] == "yes") $ltipus = 2;
			else if($_POST["self_rsvp"] == "excusa") $ltipus = 4;
			
			$sth = $con->prepare("UPDATE reunions_persones SET tipus=:tipus WHERE rid=:rid AND uid=:uid");
			$sth->bindParam(":rid", intval($_GET["id"]), PDO::PARAM_INT);
			$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
			$sth->bindParam(":tipus", $ltipus, PDO::PARAM_INT);
			$sth->execute();
		}
	}
}
?>

<h1 class="clear">Veure reunió</h1>

<form method="post">
	<p><label>Nom: <input name="nom" type="text" value="<?=safe_escape($reunions_row[0]["Nom"])?>" required <?=$isadmin ? "" : "readonly"?>></label><br>
	<label>Data i hora: <input name="data" type="datetime-local" value="<?=$data_new == "" ? $reunions_row[0]["isodate"] : safe_escape($data_new)?>" required <?=$isadmin ? "" : "readonly"?>></label><br>
	<label>Lloc:<br><textarea name="lloc" rows="5" cols="38" required <?=$isadmin ? "" : "readonly"?>><?=safe_escape($reunions_row[0]["Lloc"])?></textarea></label><br>
	Ordre del dia:<br><textarea name="ordre" data-type="rte" rows="5" cols="38" <?=$isadmin ? "" : "readonly"?>><?=safe_escape($reunions_row[0]["Ordre"])?></textarea><br>
	Acta de la reunió:<br><textarea name="acta" data-type="rte" rows="5" cols="38" <?=$isadmin ? "" : "readonly"?>><?=safe_escape($reunions_row[0]["Acta"])?></textarea>
	<br>
	
	<?php
	if(!$isadmin){
		if(!$tancada && ($ltipus == 1 || $ltipus == 2 || $ltipus == 4)){
			?>
			
			<p>Has sigut convocat a aquesta reunió. Anirás?<br>
			<label><input type="radio" name="self_rsvp" value="no" <?=$ltipus == 1 ? "checked" : ""?>>No.</label><br>
			<label><input type="radio" name="self_rsvp" value="yes" <?=$ltipus == 2 ? "checked" : ""?>>Sí.</label></p>
			<label><input type="radio" name="self_rsvp" value="excusa" <?=$ltipus == 4 ? "checked" : ""?>>Excusar assistencia.</label></p>
			
			<?php
		}else if($tancada){
			?>
			
			<p>Aquesta reunió ja ha ocurrit.</p>
			
			<?php
		}
	}
	?>
	
	<input type="submit" value="Desa tot"></p>

<?php
if($isadmin){
?>

<h2>Llista de convocats</h2>


<table class="sortable">
<tr>
<th>Nom compte</th>
<th>Nom i cognoms</th>
<th>Alies Telegram</th>
<th>Email</th>
<th>Convocat</th>
<th>Assistirá</th>
<th>Ha assistit</th>
<th>Excusa</th>
<th>Eliminar?</th>
</tr>

<?php
$sth = $con->prepare("SELECT rp.id, p.nomusuari, p.Nom_i_Cognoms, p.Alies_Telegram, p.email, rp.tipus FROM persones AS p INNER JOIN reunions_persones AS rp ON rp.rid = ? AND p.id = rp.uid");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row){
	?>

	<tr>
		<input type="hidden" name="membres[<?=$row["id"]?>][id]" value="<?=$row["id"]?>"> 
		<td><?=safe_escape($row["nomusuari"])?></td>
		<td><?=safe_escape($row["Nom_i_Cognoms"])?></td>
		<td><a href="https://t.me/<?=safe_escape($row["Alies_Telegram"])?>" target="_blank"><?=safe_escape($row["Alies_Telegram"])?></a></td>
		<td><a href="mailto:<?=safe_escape($row["email"])?>" target="_blank"><?=safe_escape($row["email"])?></a></td>
		<td><label class="radio-conv-tipus"><input type="radio" name="membres[<?=$row["id"]?>][tipus]" value="1" <?=$row["tipus"] == 1 ? "checked" : ""?>></label></td>
		<td><label class="radio-conv-tipus"><input type="radio" name="membres[<?=$row["id"]?>][tipus]" value="2" <?=$row["tipus"] == 2 ? "checked" : ""?>></label></td>
		<td><label class="radio-conv-tipus"><input type="radio" name="membres[<?=$row["id"]?>][tipus]" value="3" <?=$row["tipus"] == 3 ? "checked" : ""?>></label></td>
		<td><label class="radio-conv-tipus"><input type="radio" name="membres[<?=$row["id"]?>][tipus]" value="4" <?=$row["tipus"] == 4 ? "checked" : ""?>></label></td>
		<?php if($isadmin) { ?> <td><label><input type="checkbox" name="membres[<?=$row["id"]?>][delete]"> Eliminar</label></td> <?php } ?>
	</tr>

<?php
}
?>

</table>
<br>
<input type="submit" value="Desa tots els canvis">

	<h3>Convocar membre(s) específics a la reunió</h3>
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
	<input type="submit" value="Desa tot"><br>
	
	<h3>Convocar membres en masa</h3>
	
	<?php
	if(isset($gid) && $gid != null) echo '<input name="convocar-grup" type="submit" value="Convoca a el grup i a membres específics, i desa tot"><br>Fes clic a el botó per convocar a la reunió a tots els membres del grup amb rol superior a "Pot veure".';
	else echo '<input name="convocar-tothom" type="submit" value="Convoca a tothom i desa tot"><br>Fes clic a el botó per convocar a la reunió a totes les persones registrades al sistema.';
	?>
	</form>

<?php
}
?>

</form>

<h2>Altres funcions</h2>

<ul>
<?php if(is_numeric($gid)) echo '<li><a href="editagrup?id=' . $gid .'">Anar al grup</a></li>'; ?>
<li><a href="lesmevesreunions">Anar a les meves reunions</a></li>
<li><a href="/">Anar a la pàgina principal</a></li>
</ul>

<?php
if($isadmin){
?>
	
<h2>Funcions d'administració</h2>
<ul>
<li><a href="eliminareunio?id=<?=intval($_GET["id"])?>">Elimina aquesta reunió</a></li>
<?php if(isadmin()) echo '<li><a href="gestorreunions">Gestor de reunions</a></li>'; ?>
</ul>
	
<?php
}

require_once "internal/foot.php";
?>