<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Els meus grups</h1>

<table class="sortable">
<tr>
<th>Nom grup</th>
<th>Tipus grup</th>
<th>El meu rol</th>
<th>Veure</th>
</tr>

<?php
$sth = $con->prepare("SELECT g.id, g.Nom, g.Tipus, gp.rol FROM grups AS g LEFT JOIN grups_persones AS gp ON gp.idpersona = :id AND gp.idgrup = g.id ORDER BY gp.rol DESC");
$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$altres = array();

foreach($result as $row){
	if(isset($row["rol"])){
		?>

		<tr>
			<td><?=safe_escape($row["Nom"])?></td>
			<td>
				<?php
					switch($row["Tipus"]){
						case "0":
							echo "Privat";
							break;
						case "1":
							echo "Semipúblic";
							break;
						case "2":
							echo "Públic";
							break;
					}
				?>
				</td>
			<td>
				<?php
					switch($row["rol"]){
						case "0":
							echo "Puc veure";
							break;
						case "1":
							echo "Usuari/a grup";
							break;
						case "2":
							echo "Administrador/a grup";
							break;
						default:
							echo "Cap";
							break;
					}
				?>
				</td>
			<td><a href="editagrup?id=<?=$row["id"]?>">Veure</a></td>
		</tr>

		<?php
	}else if($row["Tipus"] != 0){
		array_push($altres, $row);
	}
}
?>

</table>

<h2 class="clear">Llista de altres grups</h2>

<table class="sortable">
<tr>
<th>Nom grup</th>
<th>Tipus grup</th>
<th>Veure</th>
</tr>

<?php
foreach($altres as $row){
	?>
	
	<tr>
			<td><?=safe_escape($row["Nom"])?></td>
			<td>
				<?php
					switch($row["Tipus"]){
						case "0":
							echo "Privat";
							break;
						case "1":
							echo "Semipúblic";
							break;
						case "2":
							echo "Públic";
							break;
					}
				?>
				</td>
			<td><a href="editagrup?id=<?=$row["id"]?>">Veure</a></td>
	
	<?php
}
?>

</table>

<p><strong>Semipúblic</strong> vol dir que tothom pot veure el grup, pero el/la usuari/a no es pot afegir a si mateix/a al grup.</p>

<h2>Altres opcions</h2>
<a href="/">Endarrera: pàgina principal</a>

<?php
require_once "internal/foot.php";
?>