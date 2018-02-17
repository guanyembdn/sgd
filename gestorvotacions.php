<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Gestor de votacions</h1>

<p>
<ul>
<li><a href="afegirvotacio">Afegir una nova votació global</a></li>
<li><a href="votacsv">Descarregar informació sobre totes les votacions com a CSV</a></li>
<li><a href="vota_resultat_csv">Descarregar informació sobre els resultats de totes les votacions com a CSV</a></li>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

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
foreach($con->query("SELECT v.id, v.pregunta, IF(v.gid IS NULL,'🌐 Global',g.Nom) AS GrupNom, v.startdate, v.autotanca, v.closedate, v.tipus, DATEDIFF(v.closedate, CURDATE()) AS datediff FROM votacions AS v LEFT JOIN grups AS g ON v.gid IS NOT NULL AND v.gid=g.id") as $row){
	$tancada = $row["tipus"] == 0 || ($row["autotanca"] == 1 && $row["datediff"] < 0);
	?>

	<tr>
		<td><?=safe_escape($row["pregunta"])?></td>
		<td><?=$tancada ? "🔴 Tancada" : "🔵 Oberta"?></td>
		<td><?=safe_escape($row["GrupNom"])?></td>
		<td><?=safe_escape($row["startdate"])?></td>
		<td><?=safe_escape($row["closedate"])?></td>
		<td><a href="veurevotacio?id=<?=$row["id"]?>">Veure...</a></td>
	</tr>

	<?php
}
?>

</table>

<?php
require_once "internal/foot.php";
?>