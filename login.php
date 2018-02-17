<?php
require_once "internal/head.php";
require_once "internal/common.php";
?>

<div>
<?php
	$lfailed = false;
	
	if(isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])){
		if(validate_username($_POST['username'])){
			$sth = $con->prepare("SELECT COUNT(*) AS num FROM login_failed WHERE ip=? AND TIME_TO_SEC(TIMEDIFF(NOW(), date)) < 300");
			$sth->bindParam(1, $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR, 50);
			$sth->execute();
			$row_failed = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			if($row_failed[0]["num"] > 10){
				die("<p>⚠️ S'han introduit dades erronies desde la teva connexió a internet massa vegades en poc temps. Per seguretat, no podrás intentar iniciar sessió en els proxims 5 minuts.</p>");
			}
			
			$pass=$_POST['password'];
			$user=$_POST['username'];
			$sth=$con->prepare("SELECT id, contrasenya, tipus, canviatself FROM persones WHERE nomusuari=?");
			$sth->bindParam(1, $user, PDO::PARAM_STR, 50);
			$sth->execute();
			
			foreach($sth->fetchAll() as $row){
				$password=$row['contrasenya'];
				$type=$row['tipus'];
				$id=$row['id'];
			}
			
			if(isset($password) && !empty($password)){
				if(password_verify($pass, $password)){
					if($type == 0 || $type == 4){
						echo '<p>🛑 Aquest compte encara no ha sigut validat pels administradors.</p>';				
					}else{		
						$_SESSION["user"] = $user;
						$_SESSION["uid"] = $id;
						$_SESSION["type"] = $row["tipus"];
						$_SESSION["canviatself"] = $row["canviatself"];
						$_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
						$_SESSION["ua"] = $_SERVER["HTTP_USER_AGENT"];
						
						$sth = $con->prepare("UPDATE persones SET lastlogin=CURDATE() WHERE id=?");
						$sth->execute(array($id));
						
						if($_SESSION['type']=="1"){
							header('Location: /');
						}else{
							header('Location: /');
						}
					
					}	
				}else{
					echo '<p>⚠️ Nom de compte o contrasenya incorrectes.</p>
						<a href="login"><button>Tornar</button></a>';
						$lfailed = true;
				}
			}else{
				echo '<p>⚠️ Nom de compte o contrasenya incorrectes.</p>
					<a href="login"><button>Tornar</button></a>';
						$lfailed = true;
			}
		}else{
			echo '<p>⚠️ Nom de compte o contrasenya incorrectes.</p>
				<a href="login"><button>Tornar</button></a>';
						$lfailed = true;
		}
	}else{
		if(isset($_SESSION['user'])){
			header('Location: /');
		}else{
			?>
			<h1>Iniciar sessiò</h1>

			<form action="login" method="post">
				Nom de compte: <input type="text" name="username" autofocus required><br>
				Contrasenya: <input type="password" name="password" required><br>
				<input type="submit" value="➡">
			</form>
			<p class="clear">🛑 No disposes de compte? Utilitza el <a href="register">registre</a>.</p>
			<?php
		}
	}
	
	if($lfailed){
		$sth = $con->prepare("INSERT INTO login_failed (ip, date) VALUES(?, NOW())");
		$sth->execute(array($_SERVER["REMOTE_ADDR"]));
	}
?>
</div>

<?php
require_once "internal/foot.php";
?>