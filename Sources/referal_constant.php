<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 13/12/15
 * Time: 01:00 PM
 */

if (!defined("EvolutionScript"))
{
    exit("Hacking attempt...");
}

if ($_SESSION['logged'] != "yes") {
    header("location: ./");
    exit();
}

require SMARTYLOADER;

$tipo = $user_info["type"];
$cant_ref = $user_info["myrefs1"];
$back_refs = $user_info["backup_refs"];
$prox_crefs = $user_info["prox_crefs"];

$membership  = $db->fetchRow("SELECT * FROM membership WHERE id=".$tipo .";");

//nombres , ganancias y cantidades de la membresia
$nref = $db->fetchRow("SELECT nref0, nref1, nref2, nref3, nref4, nref5, nref6 FROM membership WHERE id=".$tipo .";");
$gref = $db->fetchRow("SELECT gref0, gref1, gref2, gref3, gref4, gref5, gref6 FROM membership WHERE id=".$tipo .";");
$cref = $db->fetchRow("SELECT cref0, cref1, cref2, cref3, cref4, cref5, cref6 FROM membership WHERE id=".$tipo .";");

$nivel_actual = retornar_nivel($cant_ref,$cref, $nref, $gref);

if($prox_crefs == 0 ){
    $prox_crefs = $cref["cref0"];
}

if(($nivel_actual["nivel"] + 1) < count($cref)) {
    $prox_ganancias = $gref["gref" . ($nivel_actual["nivel"] + 1)];
    $meta = $cref["cref".($nivel_actual["nivel"]+1)];
}else{
    $prox_ganancias = 0;
    $meta = $cref["cref".($nivel_actual["nivel"])];
}

$ganancias = $nivel_actual["gref"];
$nombre_nivel = $nivel_actual["nref"];
$reclamo = $user_info["reclamado"];


$smarty->assign("membresia",$membership["name"]);
$smarty->assign("cantidad",$cant_ref);
$smarty->assign("ganancias",$ganancias);
$smarty->assign("nivel_actual",$nombre_nivel);
$smarty->assign("reclamado",$reclamo);
$smarty->assign("prox_meta",$prox_crefs);
$smarty->assign("prox_ganancias",$prox_ganancias);
$smarty->assign("ganancias_totales",$total);
$smarty->assign("file_name", "referal_constant.tpl");
$smarty->display("account.tpl");


/**
 *
 * Retorna el nombre del nivel
 *
 * @param $cant_ref
 * @param $cref
 * @param $nref
 * @param $gref
 * @return mixed
 */
function nombre_del_nivel($cant_ref ,$cref,$nref,$gref){
  return  retornar_nivel($cant_ref,$cref,$nref,$gref )["nref"];
}


/**
 * Regresa el nivel en el que se encuentra el usuario actual
 * @param $cant_ref
 * @param $recursivo
 * @param $cref
 * @param $nref
 * @param $gref
 * @return array
 *
 */
  function retornar_nivel($cant_ref, $cref, $nref, $gref)
    {
        $nivel = null;
        for ($i = 1; $i <= count($cref); $i++) {

            if ($cant_ref >= $cref["cref" . ($i - 1)] && $cant_ref < $cref["cref" . $i] || $cref["cref" . $i] == 0 || $i > (count($cref) - 1)) {
                $nivel = array();
                $nivel["cref"] = $cref["cref" . ($i - 1)];
                $nivel["gref"] = $gref["gref" . ($i - 1)];
                $nivel["nref"] = $nref["nref" . ($i - 1)];
                $nivel["nivel"] = $i - 1;
                break;
            }

        }

        if ($cant_ref == 0 || $cant_ref < $cref["cref0"]) {
            $nivel = array();
            $nivel["cref"] = $cant_ref["cref0"];
            $nivel["gref"] = 0;
            $nivel["nref"] = "Ninguno";
            $nivel["nivel"] = -1;
        }
        return $nivel;
    }

exit();