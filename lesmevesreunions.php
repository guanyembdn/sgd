<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Les meves reunions</h1>

<h2>Filtrar</h2>

<form id="reunions_filter" name="table_filter" data-table-id="reunions">
Nom: <input type="text" data-filter-id="0"><br>
El meu estat: <select data-filter-id="1">
	<option></option>
	<option>Cap</option>
	<option>Convocat</option>
	<option>Anir�</option>
	<option>He anat</option>
	<option>Excusa</option>
	</select><br>
Grup: <input type="text" data-filter-id="2">
</form>

<h2>Llista de reunions</h2>

<table id="reunions" class="sortable">
<tr>
<th>Nom</th>
<th>El meu estat</th>
<th>Grup</th>
<th>Data i hora</th>
<th>Veure...</th>
</tr>

<?php
$sth = $con->prepare("SELECT r.id, r.Nom, Date_format(r.Data, '%Y-%m-%d %H:%i') AS Data, rp.tipus, IF(r.gid IS NULL,'🌐 Global',g.Nom) AS GrupNom, TIMEDIFF(r.Data, NOW()) AS datediff FROM reunions AS r INNER JOIN reunions_persones AS rp ON rp.rid=r.id AND rp.uid=:uid LEFT JOIN grups AS g ON r.gid IS NOT NULL AND r.gid=g.id ORDER BY r.Data DESC");
$sth->bindParam(":uid", $_SESSION["uid"], PDO::PARAM_INT);
$sth->execute();
foreach($sth->fetchAll() as $row){
	$tancada = $row["datediff"] < 0;
	?>

	<tr>
		<td><?=safe_escape($row["Nom"])?></td>
		<td>
		<?php
		switch($row["tipus"]){
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
		<td><?=safe_escape($row["GrupNom"])?></td>
		<td><?=$tancada ? "<span class='span_temps_pasat'>←</span>" : "<span class='span_temps_futur'>→</span>"?><?=$row["Data"]?></td>
		<td><a href="veurereunio?id=<?=$row["id"]?>">Veure...</a></td>
	</tr>

	<?php
}
?>

</table>

<h2>Altres funcions</h2>

<p>
<ul>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

<?php
require_once "internal/foot.php";
?>