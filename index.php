<?php
require_once "internal/head.php";
?>

<div class="indexbox">
<h1>Sistema de gestió de dades (SGD)</h1>
<?php
if(isset($_SESSION["type"])){
	?>
	
	<div class="w3-container headcolor">
	<h2>Informació</h2>
	</div>
	<ul>
	<li><a href="ajuda">Ajuda</a>. Llegeix aquí com utilitzar aquest sistema.</li>
	</ul>
	
	<div class="w3-container w3-green">
	<h2>Usuari/a</h2>
	</div>
	
	<p>Has accedit al sistema com usuari/a. Pots accedir a les funcions:</p>
		<ul>
			<li><a href="editameusuari">Editar el meu compte</a> <?php if(!$_SESSION["canviatself"]) echo '<span class="smallwarning">⚠️ Si us plau, utilitza aquesta pàgina per introduir la teva informació!</span>'; ?></li>
			<li><a href="editameupagaments">Gestió meus pagaments</a></li>
			
			<?php
			if($_SESSION["type"] == 3 || $_SESSION["type"] == 1){
			?>
				<li><a href="editameugrups">Els meus grups</a></li>
				<li><a href="lesmevesreunions">Les meves reunions</a></li>
				<li><a href="lesmevesvotacions">Les meves votacions</a></li>
				</ul>
			<?php
			}else{
			?>
				</ul>
				<div class="warning">⚠️ El teu compte no ha sigut validada pels administradors! Quan sigui validada, podrás accedir a més funcions. Per solicitar la validació, et pots posar en contacte amb els administradors.</div>
				
			<?php
			}
			?>
	
	<?php
	if($_SESSION["type"] == 1){
		?>
		
		
		<div class="w3-container w3-orange">
		<h2>Administrador/a</h2>
		</div>
		<p>El teu compte te funcionalitat de administrador/a. Pots accedir a les funcions d'administrador/a:</p>
			<ul>
				<li><a href="gestorusuaris">Gestor de comptes</a></li>
				<li><a href="gestorgrups">Gestor de grups</a></li>
				<li><a href="gestorvotacions">Gestor de votacions</a></li>
				<li><a href="gestorreunions">Gestor de reunions</a></li>
				<li><a href="administraciopagaments">Administració de pagaments</a></li>
				<li><a href="email">Enviar e-mail global</a></li>
			</ul>
		
		<?php
	}
}
?>
</div>

<?php
require_once "internal/foot.php";
?>