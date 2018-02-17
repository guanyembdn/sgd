<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Gestor de grups</h1>

<p>
<ul>
<li><a href="afegirgrup">Afegir un nou grup</a></li>
<li><a href="grupscsv">Descarregar informació sobre tots els grups com a CSV</a></li>
<li><a href="grups_membres_csv">Descarregar informació sobre els membres de tots els grups com a CSV</a></li>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

<h2>Llista de grups</h2>

<table class="sortable">
<tr>
<th>Nom grup</th>
<th>Tipus grup</th>
<th>Editar...</th>
</tr>

<?php
foreach($con->query("SELECT id, Nom, Tipus FROM grups") as $row){
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
		<td><a href="editagrup?id=<?=$row["id"]?>">Editar...</a></td>
	</tr>

	<?php
}
?>

</table>

<p><strong>Semipúblic</strong> vol dir que tothom pot veure el grup, pero el/la usuari/a no es pot afegir a si mateix/a al grup.</p>

<?php
require_once "internal/foot.php";
?>