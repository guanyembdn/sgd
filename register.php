<?php
require_once "internal/head.php";
require_once "internal/common.php";
?>

<?php
	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email'])) {
		if(strpos($_POST['username'], " ")==False&&strlen($_POST['username'])>=1&&validate_username($_POST['username'])&&strlen($_POST['password'])>=6&&filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			$pass=phash($_POST['password']);
			$user=$_POST['username'];
			$email=$_POST['email'];
			$tipus_nou = 2;
			if(isadmin() && isset($_POST["adminadd"]) && isset($_POST["tipus"])){
				$tipus_nou = $_POST["tipus"];
			}
			
			$sth=$con->prepare("INSERT into persones (nomusuari, contrasenya, email, tipus) VALUES(?, ?, ?, ?)");
			if($sth->execute(array($user, $pass, $email, $tipus_nou))==True){
				if(isadmin() && isset($_POST["adminadd"])){
					header("Location: editausuari?id=" . $con->lastInsertId());
				}else{
					?>
					<div class="clear">
						<p>ğŸ”µ La compte ha sigut creada i es pot accedir.</p>
						<a href="/"><button>Anar a la pÃ gina principal</button></a>
					</div>
					<?php
				}
			}else{
			?>
			<div class="clear">
				<p>âš ï¸ Error: La compte no ha sigut creada. Prova amb un altre nom.</p>
				<a href="register"><button>Tornar</button></a>
			</div>
			<?php
			}
		}else{
			?>
			<div class="clear">
				<p>âš ï¸ El nom de compte contÃ© carÃ cters no permesos o Ã©s massa curt, o la contrasenya es massa curta, o el email no es valid.</p>
				<a href="register"><button>Tornar</button></a>
			</div>
			<?php
		}
	}else{
		?>
		<div class="clear">
		<h1>Registre de compte</h1>
		<form action="register" method="post">
			Nom de compte: <input type="text" name="username" required pattern="^[A-Za-z0-9_-àèìòùáéíóúñ]+$" title="NomÃ©s pot contenir lletres ('A-Z'), nombres ('0-9'), '-' i '_'"><br>
			Contrasenya: <input type="password" name="password" minlength="8" required pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Com a minim una minuscula, una majuscula i numeros o caracters especials"><br>
			E-mail: <input type="email" name="email" required><br>
			<input type="submit" value="â¡">
		</form>
		<p  class="clear">ğŸ”µ Ja tens compte? Utilitza l'<a href="login">inici de sessiÃ³</a>.</p>
<?php
	}
?>

<?php
require_once "internal/foot.php";
?>