<?php
require_once "internal/sess.php";
require_once "internal/common.php";
?>

<!DOCTYPE html>

<html>
	<head>
		<title>SGD</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
		<meta charset="UTF-8">
		<script src="js/common.js"></script>
		<script src="js/sorttable.js"></script>
		
		<?php
		switch($_SESSION["css"]){
			case 0:
				echo '<link href="css/style-simple.css" rel="stylesheet" type="text/css">';
				break;
			case 1:
				echo '<link href="css/style-moderne.css" rel="stylesheet" type="text/css">';
				break;
		}
		?>
		
	</head>
	<body>
		<div class="banner">
			<a href="/" tabindex="-1"><button class="w3-btn headcolor home-btn">SGD</button></a>
			<div class="menu">
				<div class="menucont">
					<?php
						if(isset($_SESSION['user'])){
							?>
							
							<a href="/logout" tabindex="-1"><button class="w3-btn w3-blue">Tancar sessió</button></a>
							🔵 Sessió iniciada com: <span class="username_label"><?=safe_escape($_SESSION["user"])?></span>.
							
							<nav>
							<ul>
							<li><a href="ajuda">Ajuda</a></li>
							<li><a class="disabled-link">Usuari/a</a>
							<ul>
							<li><a href="editameusuari">Editar el meu compte</a></li>
							<li><a href="editameupagaments">Gestió meus pagaments</a></li>
							<?php
							if($_SESSION["type"] == 3 || $_SESSION["type"] == 1){
							?>
							
							<li><a href="editameugrups">Els meus grups</a></li>
							<li><a href="lesmevesreunions">Les meves reunions</a></li>
							<li><a href="lesmevesvotacions">Les meves votacions</a></li>
							
							<?php
							}
							?>
							
							</ul>
							</li>
							
							<?php
							if($_SESSION["type"] == 1){
							?>
							
							<li><a class="disabled-link">Administrador/a</a>
							<ul>
							<li><a href="gestorusuaris">Gestor de comptes</a></li>
							<li><a href="gestorgrups">Gestor de grups</a></li>
							<li><a href="gestorvotacions">Gestor de votacions</a></li>
							<li><a href="gestorreunions">Gestor de reunions</a></li>
							<li><a href="administraciopagaments">Administració de pagaments</a></li>
							<li><a href="email">Enviar e-mail global</a></li>
							</ul>
							</li>
							
							<?php
							}
							?>
							</ul>
							</nav>
							
							<?php
						}else{
							?>
							
							<a href="/login" tabindex="-1"><button class="w3-btn w3-green">Iniciar sessió</button></a>
							
							<?php
						}
					?>
				</div>
			</div>		
		</div>
		
		<div class="clear"></div>