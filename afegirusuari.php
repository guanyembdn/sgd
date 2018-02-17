<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
?>

<h1 class="clear">Afegir una nova compte</h1>
<form action="register" method="post">
<input type="hidden" name="adminadd">
Nom de compte: <input type="text" name="username" required><br>
Contrasenya: <input type="password" name="password" required minlength="8" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Com a minim una minuscula, una majuscula i numeros o caracters especials"><br>
E-mail: <input type="email" name="email" required><br>
Tipus: <select name="tipus">
<option value="0"<?=$row["tipus"] == "0" ? " selected" : ""?>>Compte no verificada</option>
<option value="1"<?=$row["tipus"] == "1" ? " selected" : ""?>>Administrador/a</option>
<option value="2"<?=$row["tipus"] == "2" ? " selected" : ""?>>Compte verificada</option>
<option value="3"<?=$row["tipus"] == "3" ? " selected" : ""?> selected>Compte verificada completament</option>
<option value="4"<?=$row["tipus"] == "4" ? " selected" : ""?>>Desactivada</option>
</select></label><br>
<input type="submit" value="Afegir">
</form>

<h2>Altres funcions</h2>

<p><a href="gestorusuaris">Tornar endarrera: gestor de comptes</a></p>

<?php
require_once "internal/foot.php";
?>