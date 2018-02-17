<?php
if(!isset($_SESSION["uid"]) || !isset($_SESSION["type"]) || ($_SESSION["type"] != 2 && $_SESSION["type"] != 3  && $_SESSION["type"] != 1)) die();
?>