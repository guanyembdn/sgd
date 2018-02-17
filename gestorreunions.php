<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Gestor de reunions</h1>

<p>
<ul>
<li><a href="afegirreunio">Afegir una nova reunió global</a></li>
<li><a href="reuniocsv">Descarregar informació sobre totes les reunions com a CSV</a></li>
<li><a href="reunio_assistencia_csv">Descarregar informació sobre la assistencia a totes les reunions com a CSV</a></li>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

<h2>Filtrar</h2>

<form id="reunions_filter" name="table_filter" data-table-id="reunions">
Nom: <input type="text" data-filter-id="0"><br>
El meu estat: <select data-filter-id="1">
	<option></option>
	<option>Cap</option>
	<option>Convocat</option>
	<option>Aniré</option>
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
$sth = $con->prepare("SELECT r.id, rp.tipus, r.Nom, CHAR_LENGTH(r.Acta) AS ActaCharlen, Date_format(r.Data, '%Y-%m-%d %H:%i') AS Data, IF(r.gid IS NULL,'🌐 Global',g.Nom) AS GrupNom, TIMEDIFF(r.Data, NOW()) AS datediff FROM reunions AS r LEFT JOIN reunions_persones AS rp ON rp.rid=r.id AND rp.uid=:uid  LEFT JOIN grups AS g ON r.gid IS NOT NULL AND r.gid=g.id ORDER BY r.Data DESC");
$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->execute();
foreach($sth->fetchAll() as $row){
	$tancada = $row["datediff"] < 0;
	?>

	<tr>
		<td><?php if($tancada && !($row["ActaCharlen"] > 0)){echo '<span title="Ja ha passat i sense acta">⚠️</span>';}?> <?=safe_escape($row["Nom"])?></td>
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

<?php
require_once "internal/foot.php";
?>