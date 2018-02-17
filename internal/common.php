<?php
define("EMAIL_NOREPLY", "");
define("EMAIL_FROM", "");

function phash($pass){
	return password_hash($pass, PASSWORD_BCRYPT);
}

function safe_escape($str){
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

function isadmin(){
	return isset($_SESSION["type"]) && $_SESSION["type"] === "1";
}

function getgrouprole($con, $gid){
	$sth = $con->prepare("SELECT gp.rol, g.Tipus FROM grups AS g INNER JOIN grups_persones AS gp ON gp.idpersona = :id AND gp.idgrup = g.id AND g.id=:gid GROUP BY g.id");
	$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
	$sth->bindParam(":gid", intval($gid), PDO::PARAM_INT);
	$sth->execute();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);
	if(isset($result[0])) return $result[0]["rol"];
	else return null;
}

function chkgroupadmin($con, $gid){
	$res = getgrouprole($con, $gid);
	if(isset($res) && $res == "2") return true;
}

function chkgrouporglobaladmin($con, $gid){
	if(isadmin()) return true;
	else return chkgroupadmin($con, $gid);
}

function validate_username($username){
	return preg_match("/^[A-Za-z0-9_-àèìòùáéíóúñç]+$/", $username);
}
?>