<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Les meves votacions</h1>

<h2>Filtrar</h2>

<form id="votacions_filter" name="table_filter" data-table-id="votacions">
Nom: <input type="text" data-filter-id="0"><br>
Estat: <select data-filter-id="1">
	<option></option>
	<option>Oberta</option>
	<option>Tancada</option>
	</select><br>
Grup: <input type="text" data-filter-id="2">
</form>

<h2>Llista de votacions</h2>

<table id="votacions" class="sortable">
<tr>
<th>Pregunta</th>
<th>Estat</th>
<th>Grup</th>
<th>Data comença</th>
<th>Data tanca</th>
<th>Veure...</th>
</tr>

<?php
$sth = $con->prepare("SELECT v.id, v.pregunta, v.quipotvotar, IF(v.gid IS NULL,'🌐 Global',g.Nom) AS GrupNom, v.startdate, v.autotanca, v.closedate, v.tipus, DATEDIFF(v.closedate, CURDATE()) AS datediff FROM votacions AS v LEFT JOIN grups AS g ON v.gid IS NOT NULL AND v.gid=g.id LEFT JOIN grups_persones AS gp ON gp.idgrup=v.gid AND gp.idpersona = :uid WHERE (v.gid IS NULL OR (gp.rol = 1 OR gp.rol = 2))");
$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach($result as $row){
	$nomesmpd = $row["quipotvotar"] == 0;
	
	if(!$nomesmpd || $_SESSION["tipus_membre_partit"] == 2){
		$tancada = $row["tipus"] == 0 || ($row["autotanca"] == 1 && $row["datediff"] < 0);
		?>

		<tr>
			<td><?=safe_escape($row["pregunta"])?></td>
			<td><?=$tancada ? "🔴 Tancada" : "🔵 Oberta"?></td>
			<td><?=safe_escape($row["GrupNom"])?></td>
			<td><?=$row["startdate"]?></td>
			<td><?=$row["closedate"]?></td>
			<td><a href="veurevotacio?id=<?=$row["id"]?>">Veure...</a></td>
		</tr>

		<?php
	}
}
?>

</table>

<h2>Altres funcions</h2>

<p>
<ul>
<?php if(isadmin()) echo '<li><a href="afegirvotacio">Afegir votació global (admin)</a></li>';?>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

<?php
require_once "internal/foot.php";
?>