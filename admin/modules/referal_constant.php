<?php
/**
 * Autor: erik gonzalez
 * email: erikhuboy@gmail.com
 * Date: 13/12/15
 */
define("EvolutionScript", 1);
require_once "../library.php";

if (ADMINLOGGED !== true)
{
    header("location: ./?view=login");
    exit();
}

if($input->p["actualizar"] == "do"){
    $membership_id = $input->p["membership_id"];
    unset($input->p["membership_id"]);
    unset($input->p["actualizar"]);
    $db->update("membership",$input->p," id=".$membership_id);
    serveranswer(1, "Ha sido actualizado");
}
else {
    serveranswer(0, "No se pudo actualizar");
}