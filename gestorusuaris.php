<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Gestor de comptes</h1>

<ul>
<li><a href="afegirusuari">Afegir un nou compte</a></li>
<li><a href="comptescsv">Descarregar informació sobre totes les comptes com a CSV</a></li>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>

<h2>Filtrar</h2>
<form id="comptes_filter" name="table_filter" data-table-id="comptes">
Nom compte: <input type="text" data-filter-id="0"><br>
Nom i cognoms: <input type="text" data-filter-id="1"><br>
Tipus compte:
<select data-filter-id="2">
	<option></option>
	<option>Compte no verificada</option>
	<option>Administrador/a</option>
	<option>Compte verificada</option>
	<option>Compte verificada completament</option>
	<option>Desactivada</option>
</select><br>
Tipus membre partit:
<select data-filter-id="3">
	<option></option>
	<option>No és</option>
	<option>Simpatitzant</option>
	<option>Membre Ple Dret</option>
</select><br>
Alies Telegram: <input type="text" data-filter-id="4"><br>
Barri: <input type="text" data-filter-id="5" list="barris"><br>
Paga quota:
<select data-filter-id="6">
	<option></option>
	<option>No</option>
	<option>Sí</option>
</select>

<datalist id="barris">
		<?php
			foreach($con->query("SELECT Barri FROM persones GROUP BY Barri") as $rowbarri){
				echo "<option value=\"" . safe_escape($rowbarri["Barri"]) . "\">";
			}
		?>
		</datalist>
</form>

<h2>Llista de comptes</h2>

<table id="comptes" class="sortable">
<tr>
<th>Nom compte</th>
<th>Nom i Cognoms</th>
<th>Tipus compte</th>
<th>Tipus membre partit</th>
<th>Alies Telegram</th>
<th>Barri</th>
<th>Paga quota</th>
<th>Últim accés</th>
<th>Editar...</th>
</tr>

<?php
foreach($con->query("SELECT p.id, p.nomusuari, p.Nom_i_Cognoms, p.Tipus_Membre_Partit, p.Alies_Telegram, p.tipus, p.Barri, IF(q.uid IS NOT NULL,'Sí','No') AS pq, p.lastlogin FROM persones AS p LEFT JOIN quotes AS q ON q.uid=p.id ORDER BY lastlogin DESC") as $row){
	?>

	<tr>
		<td><?=safe_escape($row["nomusuari"])?></td>
		<td><?=safe_escape($row["Nom_i_Cognoms"])?></td>
		<td>
			<?php
				switch($row["tipus"]){
					case "0":
						echo "Compte no verificada";
						break;
					case "1":
						echo "Administrador/a";
						break;
					case "2":
						echo "Compte verificada";
						break;
					case "3":
						echo "Compte verificada completament";
						break;
					case "4":
						echo "Desactivada";
						break;
				}
			?>
			</td>
			<td>
			<?php
				switch($row["Tipus_Membre_Partit"]){
					case "0":
						echo "No és";
						break;
					case "1":
						echo "Simpatitzant";
						break;
					case "2":
						echo "Membre Ple Dret";
						break;
				}
			?>
			</td>
			<td><a href="https://t.me/<?=safe_escape($row["Alies_Telegram"])?>" target="_blank"><?=safe_escape($row["Alies_Telegram"])?></a></td>
			<td><?=safe_escape($row["Barri"])?></td>
			<td><?=safe_escape($row["pq"])?></td>
			<td><?=$row["lastlogin"]?></td>
		<td><a href="editausuari?id=<?=$row["id"]?>">Editar...</a></td>
	</tr>

	<?php
}
?>

</table>

<?php
require_once "internal/foot.php";
?>