<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

$isadmin = false;
$gid = null;

if(isset($_GET["gid"])){
	$isadmin = chkgrouporglobaladmin($con, $_GET["gid"]);
	$gid = $_GET["gid"];
}else{
	$isadmin = isadmin();
}

if($isadmin){
	if(isset($_POST["envia"])){
		$para = "";

		if($gid != null){ // Enviament al grup
			if(isset($_POST["rolgrup"])){
				if(isadmin()){
					if(isset($_POST["tipus"]) && isset($_POST["paganquota"])){
						$sth = $con->prepare("SELECT p.id, p.email, p.tipus, p.Barri, q.uid AS quota_uid, gp.idpersona, gp.rol FROM persones AS p INNER JOIN grups_persones AS gp ON p.id=gp.idpersona AND gp.idgrup=? LEFT JOIN quotes AS q ON p.id=q.uid WHERE p.No_vol_emails=0");
						$sth->bindParam(1, $gid, PDO::PARAM_INT);
						$sth->execute();
					
						foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row){
							if(in_array($row["tipus"], $_POST["tipus"])){
								if(in_array($row["Barri"], $_POST["barris"])){
									if(in_array($row["rol"], $_POST["rolgrup"])){
										if($_POST["paganquota"] == 2 || ($_POST["paganquota"] == 0 && $row["quota_uid"] != null) || (($_POST["paganquota"] == 1 && $row["quota_uid"] == null))){
											$para .= $row["email"] . ", ";
										}
									}
								}
							}
						}
					}
				}else{
					$sth = $con->prepare("SELECT p.id, p.email, gp.idpersona, gp.rol FROM persones AS p INNER JOIN grups_persones AS gp ON p.id=gp.idpersona AND gp.idgrup=? WHERE p.No_vol_emails=0");
					$sth->bindParam(1, $gid, PDO::PARAM_INT);
					$sth->execute();
					
					foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row){
						if(in_array($row["rol"], $_POST["rolgrup"])){
							$para .= $row["email"] . ", ";
						}
					}
				}
			}
		}else{ // Enviament global
			if(isset($_POST["tipus"]) && isset($_POST["paganquota"])){
				foreach($con->query("SELECT p.email, p.tipus, p.Barri, q.uid AS quota_uid FROM persones AS p LEFT JOIN quotes AS q ON p.id=q.uid WHERE p.No_vol_emails=0") as $row){
					if(in_array($row["tipus"], $_POST["tipus"])){
						if(in_array($row["Barri"], $_POST["barris"])){
							if($_POST["paganquota"] == 2 || ($_POST["paganquota"] == 0 && $row["quota_uid"] != null) || (($_POST["paganquota"] == 1 && $row["quota_uid"] == null))){
								$para .= $row["email"] . ", ";
							}
						}
					}
				}
			}
		}
		
		$para =  rtrim($para, ', ');
		$mensaje = wordwrap($_POST["contingut"], 70, "\r\n", TRUE);
		$headers = "";
		
		if(isset($_POST["contingut_html"])){
			$headers .= "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
		}
		
		$headers .= "To: SGD <" . EMAIL_NOREPLY . ">\r\nFrom: " . EMAIL_FROM . " <" . EMAIL_NOREPLY . ">\r\nBcc: " . $para . "\r\n";
		mail(EMAIL_NOREPLY, $_POST["assumpte"], $mensaje, $headers);
	}
?>

<h1>Enviar e-mail</h1>

<?php
if($gid != null) echo "<p>Estás enviant el e-mail de àmbit <strong>grup</strong>.</p>";
else echo "<p>Estás enviant un e-mail de àmbit <strong>global</strong>.</p>";
?>

<form method="post">
<label>Assumpte:<br>
<input type="text" name="assumpte" required></label><br>
<label>Contingut:<br>
<textarea name="contingut" rows="5" cols="38" required></textarea></label><br>
<label>Contingut HTML?
<input type="checkbox" name="contingut_html"></label>

<?php
if(isadmin()){
?>
<h3>Enviar a les comptes que:</h3>
<label><input type="radio" name="paganquota" value="0"> Pagan quota</label><br>
<label><input type="radio" name="paganquota" value="1"> No pagan quota</label><br>
<label><input type="radio" name="paganquota" value="2" checked> Si/no pagen quota</label>

<h4>i son del tipus:</h4>
<label><input type="checkbox" name="tipus[]" value="0"> Compte no verificada</label><br>
<label><input type="checkbox" name="tipus[]" value="1" checked> Administrador/a</label><br>
<label><input type="checkbox" name="tipus[]" value="2"> Compte verificada</label> ⚠️ Compte amb això: els seus e-mails no estat verificats<br>
<label><input type="checkbox" name="tipus[]" value="3" checked> Compte verificada completament</label><br>
<label><input type="checkbox" name="tipus[]" value="4"> Desactivada</label>

<h4>i son dels barris:</h4>

<p><a name="invert_sel_btn" href="#a" data-target="barris[]">(Invertir selecció)</a></p>

<?php
$barris_res = $con->query("SELECT Barri FROM persones GROUP BY Barri");
foreach($barris_res->fetchAll(PDO::FETCH_ASSOC) AS $barris_row){
	echo '<label><input type="checkbox" name="barris[]" value="' . safe_escape($barris_row["Barri"]) . '" checked> ' . ($barris_row["Barri"] == "" ? "(Cap)" : safe_escape($barris_row["Barri"])) . '</label><br>';
}

} // cierra isadmin()
?>

<?php
if($gid != null){
?>
<h4>Enviar a els/les membres que tenen el rol en el grup:</h4>
<label><input type="checkbox" name="rolgrup[]" value="0"> Pot veure</label><br>
<label><input type="checkbox" name="rolgrup[]" value="1" checked> Usuari/a grup</label><br>
<label><input type="checkbox" name="rolgrup[]" value="2" checked> Administrador/a grup</label>
<?php
}
?>

<h4>Enviar</h4>

<input type="submit" name="envia" value="Enviar e-mail">
</form>

<h2>Altres funcions</h2>
<ul>
<?php
if(isset($_GET["gid"])) echo '<li><a href="editagrup?id=' . $_GET["gid"] . '">Tornar al grup</a></li>';
?>
<li><a href="/">Anar a la página principal</a></li>
</p>

<?php
}
require_once "internal/foot.php";
?>