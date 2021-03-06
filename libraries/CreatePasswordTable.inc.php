<?php

// Prevent to be loaded directly
if (!isset($allowed_ops)) {
    die("ERROR");
}

require_once "./setup/config.php";
require_once "./libraries/Locale.inc.php";
require_once "./libraries/MYSQLConnection.inc.php";
require_once "./libraries/Functions.inc.php";

$create_q = 'CREATE TABLE ResetPassword ( '
        . 'main_id INT NOT NULL AUTO_INCREMENT, '
        . 'uid VARCHAR(256) NOT NULL, '
        . 'mail VARCHAR(256) NOT NULL, '
        . 'token VARCHAR(256) NOT NULL, '
        . 'description VARCHAR(256) NOT NULL, '
        . 'PRIMARY KEY(main_id))';

$create_r = AssistedMYSQLQuery($create_q);

?>