<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

$sth=$con->prepare("SELECT *, DATEDIFF(closedate, CURDATE()) AS datediff FROM votacions WHERE id = ?");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$votacions_row = $sth->fetchAll(PDO::FETCH_ASSOC);
if(!isset($votacions_row[0])) die();
$gid = $votacions_row[0]["gid"];

$tancada = $votacions_row[0]["tipus"] == 0 || ($votacions_row[0]["autotanca"] == 1 && $votacions_row[0]["datediff"] < 0);
$resultatsquan = $votacions_row[0]["resultatsquan"];
$secreta = $votacions_row[0]["secreta"] == 1;
$nomesmpd = $votacions_row[0]["quipotvotar"] == 0;
$countresp = $votacions_row[0]["cumulatiu_max_vots"];

if(isset($gid) && $gid != null){ // votació de grup
	$isadmin = chkgrouporglobaladmin($con, $gid);
	$rol = getgrouprole($con, $gid);
	if(!$isadmin && $rol != 1) die(); // no som admins globals ni admins del grup ni membres del grup
}else{ // votació global
	$isadmin = isadmin();
}

if(!$isadmin && $nomesmpd && $_SESSION["tipus_membre_partit"] != 2) die();

$havotat = false;
$sth=$con->prepare("SELECT * FROM vots WHERE uid = ? AND vid = ? LIMIT 1");
$sth->bindParam(1, intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->bindParam(2, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$vots_row = $sth->fetchAll(PDO::FETCH_ASSOC);
if(isset($vots_row[0])) $havotat = true;

$sth=$con->prepare("SELECT * FROM votacions_respostes WHERE vid = ?");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$respostes_row = $sth->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="clear">Veure votació</h1>

<p>Pregunta: <strong><?=safe_escape($votacions_row[0]["pregunta"])?></strong></p>

<?php
if($havotat || $tancada){
	if($tancada || $resultatsquan == 0){
	?>
	
	<p>Els resultats de la votació:</p>
	
	<p>
	<?php
	foreach($respostes_row as $r){
		?>
		
		<strong>Resposta <?=$r["id_local"]?></strong>: <i><?=safe_escape($r["resposta"])?></i>. <?=$r["vots"]?> vot(s).
		
		<?php
		if(!$secreta){
			$sth = $con->prepare("SELECT p.nomusuari, p.Nom_i_Cognoms FROM persones AS p INNER JOIN vots AS v ON v.uid = p.id AND v.vid = :vid AND v.rid_local = :rid_local");
			$sth->bindParam(":vid", intval($_GET["id"]), PDO::PARAM_INT);
			$sth->bindParam(":rid_local", intval($r["id_local"]), PDO::PARAM_INT);
			$sth->execute();
			$noms_votants = "";
			
			foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row_votants){
				$noms_votants .= safe_escape($row_votants["nomusuari"]) . " (" . safe_escape($row_votants["Nom_i_Cognoms"]) . "), ";
			}
			
			echo rtrim('Votants: ' . $noms_votants, ', ');
		}
		
		echo '<br>';
	}
	?>
	</p>
	
	<?php
	}else{
		?>
		
		<p>Ja has votat. Els resultats serán publicats quan tanqui la votació.</p>
		
		<?php
	}
}else{
	if(isset($_POST["votar"]) || isset($_POST["perdrevot"])){
		if($secreta || isset($_POST["perdrevot"])){ // Si es secreta pero tenim que registrar que el/la usuari/a ha votat pero no registrem el seu vot concret, o si el/la usuari/a ha decidit perdre el vot registrem un vot sense resposta
			$sth=$con->prepare("INSERT INTO vots (uid, vid) VALUES(?, ?)");
			$sth->execute(array(intval($_SESSION["uid"]), intval($_GET["id"])));
		}
		
		if(isset($_POST["votar"])){
			if($votacions_row[0]["tipusvot"] == 1){
				foreach($_POST["r_check"] AS $k => $v){
					$sth=$con->prepare("UPDATE votacions_respostes SET vots=vots+1 WHERE vid = ? AND id_local = ?");
					$sth->execute(array(intval($_GET["id"]), intval($k)));
					
					if(!$secreta){
						$sth=$con->prepare("INSERT INTO vots (uid, vid, rid_local) VALUES(?, ?, ?)");
						$sth->execute(array(intval($_SESSION["uid"]), intval($_GET["id"]), intval($k)));
					}
				}
			}else if($votacions_row[0]["tipusvot"] == 0){
				if(isset($_POST["r_radio"])){
					$sth=$con->prepare("UPDATE votacions_respostes SET vots=vots+1 WHERE vid = ? AND id_local = ?");
					$sth->execute(array(intval($_GET["id"]), intval($_POST["r_radio"])));
					
					if(!$secreta){
						$sth=$con->prepare("INSERT INTO vots (uid, vid, rid_local) VALUES(?, ?, ?)");
						$sth->execute(array(intval($_SESSION["uid"]), intval($_GET["id"]), intval($_POST["r_radio"])));
					}
				}
			}else if($votacions_row[0]["tipusvot"] == 2){ // Cumulatiu
				$numvots = 0;
				
				foreach($_POST["r_text"] AS $k => $v){
					if($numvots < $countresp && ($numvots + intval($v)) <= $countresp){
						$sth=$con->prepare("UPDATE votacions_respostes SET vots=vots+? WHERE vid = ? AND id_local = ?");
						$sth->execute(array(intval($v), intval($_GET["id"]), intval($k)));
						
						if(!$secreta){
							for($count_multi = 0; $count_multi < intval($v); $count_multi++){
								$sth=$con->prepare("INSERT INTO vots (uid, vid, rid_local) VALUES(?, ?, ?)");
								$sth->execute(array(intval($_SESSION["uid"]), intval($_GET["id"]), intval($k)));
							}
						}
						
						$numvots += intval($v);
					}
				}
			}
			
			?>
			
			<p>Has votat! <?php if($resultatsquan == 0) echo 'Ara pots <a href="">veure els resultats.</a>'; ?> </p>
			
			<?php
		}else{
			?>
			
			<p>Has decidit perdre el teu vot per poder veure els resultats. Ara pots <a href="">veure els resultats.</a></p>

			<?php
		}
	}else{
	?>
	
	<p><?php if(!$secreta) echo '⚠️ Aquesta votació <strong>no</strong> és secreta! Tothom podrá veure el que has votat.<br>'; ?>
	Pots votar en aquesta votació.<br>
	<?php if($votacions_row[0]["tipusvot"] == 2) echo '<br>Aixó es una votació de tipus cumulativa.<br>Tens un total de <strong>' . $countresp . '</strong> vots per assignar.<br>Introdueix quants vots assignas a cada opció.</p>';?></p></p>
	<form method="post">
	<?php
	if($votacions_row[0]["tipusvot"] == 1) $inputtype = "checkbox";
	else if($votacions_row[0]["tipusvot"] == 0) $inputtype = "radio";
	else if($votacions_row[0]["tipusvot"] == 2) $inputtype = "text";
	
	foreach($respostes_row as $r){
		?>
		
		<label><input type="<?=$inputtype?>" name="r<?php switch($inputtype){ case "radio": echo "_radio"; break; case "checkbox": echo "_check[" . $r["id_local"] . "]"; break; case "text":  echo "_text[" . $r["id_local"] . "]"; break; }?>" value="<?php switch($inputtype){ case "text": echo ""; break; default: echo $r["id_local"]; break; }?>" <?php switch($inputtype){ case "text": echo 'class="vot_cumulatiu_input"'; break; } ?>><strong>Resposta <?=$r["id_local"]?></strong>: <i><?=safe_escape($r["resposta"])?></i>.</label><br>
		
		<?php
	}
	?>
	<input type="submit" name="votar" value="Vot <?=$secreta ? "secret" : "públic"?>"><br>
	<?php if($resultatsquan == 0) echo '<input type="submit" name="perdrevot" value="Perdre el meu vot i veure resultats">';
		else echo '<i>Els resultats serán publicats una vegada tancada la votació.</i>'; ?>
	</form>
	
	<?php
	}
}
?>

<h2>Altres funcions</h2>

<ul>
<?php if(is_numeric($gid)) echo '<li><a href="editagrup?id=' . $gid .'">Anar al grup</a></li>'; ?>
<li><a href="lesmevesvotacions">Anar a les meves votacions</a></li>
<li><a href="/">Anar a la pàgina principal</a></li>
</ul>

<?php
if($isadmin){
?>
	
<h2>Funcions d'administració</h2>
<ul>
<li><a href="tancavotacio?id=<?=intval($_GET["id"])?>">Tanca aquesta votació</a></li>
<li><a href="eliminavotacio?id=<?=intval($_GET["id"])?>">Elimina aquesta votació</a></li>
<li><a href="gestorvotacions">Gestor de votacions</a></li>
</ul>
	
<?php
}

require_once "internal/foot.php";
?>