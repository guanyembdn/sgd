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
	if(isset($_POST["pregunta"]) && !empty($_POST["pregunta"])
		&& isset($_POST["respostes"]) && !empty($_POST["respostes"]))
		{
		$sth=$con->prepare("INSERT INTO votacions (pregunta, gid, startdate, tipus, tipusvot, autotanca, resultatsquan, closedate, secreta, quipotvotar, cumulatiu_max_vots) VALUES(?, ?, CURDATE(), 1, ?, ?, ?, ?, ?, ?, ?)");
		$closedate = null;
		if(isset($_POST["closedate"]) && !empty($_POST["closedate"])) $closedate = $_POST["closedate"];
		if($sth->execute(array($_POST["pregunta"], $gid, intval($_POST["tipusvot"]), intval($_POST["autotanca"]), intval($_POST["resultatsquan"]), $closedate, intval($_POST["secreta"]), intval($_POST["quipotvotar"]), intval($_POST["cumulatiu_max_vots"])))==True){
			$vid = $con->lastInsertId();
			
			$respostes = explode("\r\n", $_POST["respostes"]);
			
			$id_local = 1;
			
			$sqlstr = "INSERT INTO votacions_respostes (id_local, vid, resposta, vots) VALUES";
			$arr_execute = array();
			
			foreach($respostes as $resposta){
				if(!empty(trim($resposta))){
					$sqlstr .= "(?, ?, ?, 0), ";
					array_push($arr_execute, $id_local, $vid, trim($resposta));
					$id_local++;
				}
			}
			
			if($id_local > 1){
				$sqlstr = rtrim($sqlstr, ', ');
				$sth = $con->prepare($sqlstr);
				$sth->execute($arr_execute);
			}
			
			header("Location: veurevotacio?id=" . $vid);
		}
	}
?>

<h1 class="clear">Afegir una nova votació</h1>

<?php
if($gid != null) echo "<p>Estás creant una votació de àmbit <strong>grup</strong>.</p>";
else echo "<p><p>Estás creant una votació de àmbit <strong>global</strong>.</p>";
?>

<form method="post">
	<p>Pregunta de la votació:<br><textarea name="pregunta" rows="5" cols="38" required></textarea><br>
	Tipus vot:<br>
	<label><input type="radio" name="tipusvot" value="0" checked> Selecció única resposta</label><br>
	<label><input type="radio" name="tipusvot" value="1"> Selecció múltiples respostes</label><br>
	<label><input type="radio" name="tipusvot" value="2"> Cumulatiu</label>
	</p>
	<p><input type="text" name="cumulatiu_max_vots" value="5" class="vot_cumulatiu_input"> Cumulatiu: Max vots per persona</p>
	<p>Respostes que pot donar el usuari (una per línia):<br><textarea name="respostes" rows="5" cols="80" class="respostes_multiline" required></textarea></p>
	La votació es tanca automáticament a una data?
	<select name="autotanca">
	<option value="0" selected>No</option>
	<option value="1">Sí</option>
	</select>
	<br>
	Data que es tanca la votació: <input type="date" name="closedate"><br>
	Com serán publicats els resultats?
	<select name="resultatsquan">
	<option value="0" selected>Sempre visibles</option>
	<option value="1">Només visibles després de tancar la votació</option>
	</select>
	<br>
	Votació secreta?
	<select name="secreta">
	<option value="1" selected>Secreta</option>
	<option value="0">Tothom pot veure qui ha votat quina cosa</option>
	</select>
	<br>
	Qui pot veure la votació?
	<select name="quipotvotar">
	<option value="1" selected>Tothom</option>
	<option value="0">Només membres ple dret (i admins del grup)</option>
	</select>
	<br>
	<input type="submit" value="Enviar">
</form>

<h2>Altres funcions</h2>
<p>
<?php
if(isset($_GET["gid"])) echo '<a href="editagrup?id=' . intval($_GET["gid"]) . '">Tornar al grup</a><br>';
if(isadmin()) echo '<a href="gestorvotacions">Anar al gestor de votacions (admin)</a>';
?>
</p>

<?php
}
require_once "internal/foot.php";
?>