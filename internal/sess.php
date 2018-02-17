<?php
require_once "internal/config.php";
session_set_cookie_params(900,"/","",TRUE,TRUE); // session lifetime (seconds)
session_start();
setcookie(session_name(),session_id(),time()+900,"/","",TRUE,TRUE); // aumentar lifetime cada vez que se carga la página
$_SESSION["css"] = 1;
  
if(isset($_SESSION["type"])){
	$sth=$con->prepare("SELECT nomusuari, tipus, css, Tipus_Membre_Partit FROM persones WHERE id=?");
	$sth->bindParam(1, $_SESSION["uid"], PDO::PARAM_INT);
	$sth->execute();
	
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(!isset($row[0])){
		header("Location: /");
		session_destroy();
		die();
	}
	
	$_SESSION["type"]=$row[0]['tipus'];
	$_SESSION["user"]=$row[0]['nomusuari'];
	$_SESSION["css"]=intval($row[0]["css"]);
	$_SESSION["tipus_membre_partit"]=intval($row[0]["Tipus_Membre_Partit"]);
	
	if($_SESSION["type"] == 0 ||
	$_SESSION["type"] == 4 ||
	$_SESSION["ip"] != $_SERVER["REMOTE_ADDR"] ||
	$_SESSION["ua"] != $_SERVER["HTTP_USER_AGENT"]){
		header("Location: /");
		session_destroy();
		die();
	}
}
?>