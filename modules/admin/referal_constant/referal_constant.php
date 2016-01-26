<?php
/**
 * Autor: erik gonzalez
 * email: erikhuboy@gmail.com
 * Date: 13/12/15
 */

if (!defined("EvolutionScript"))
{
    exit("Hacking attempt...");
}


$verificar = $db->fetchOne("SELECT COUNT(*) AS NUM FROM addon WHERE name='referal_constant'");

if($verificar == 0){

    if($_GET["instalar"] == "do" ){
        $nombre_addon = array("name" => "referal_constant");
        $db->insert("addon" ,$nombre_addon);
        $db->query("ALTER TABLE members ADD reclamado CHAR(1) DEFAULT 'N'; ");
        $db->query("ALTER TABLE members ADD backup_refs INTEGER DEFAULT 0; ");
        $db->query("ALTER TABLE members ADD prox_crefs INTEGER DEFAULT 0; ");
        for($i = 0 ; $i < 21 ; $i++){
            if($i < 7) {
                //ganancia del referido
                $db->query("ALTER TABLE membership ADD "."gref".$i." INTEGER DEFAULT 0 ;");
            }else if($i >= 7 && $i <14){
                //nombre del referido
                $db->query("ALTER TABLE membership ADD "."nref".($i-7)." VARCHAR(20) DEFAULT 'Ninguno';");
            }else if($i >= 14){
                //cantidad por nivel
                $db->query("ALTER TABLE membership ADD "."cref".($i-14)." INTEGER DEFAULT 0 ;");
            }
        }
        unset($campos_settings);
        echo("<script>location.href=\"?view=addon_modules&module=referal_constant\"</script>");
        exit();

    }

    echo("Bienvenido a la instalacion de mis referidos, para iniciar la instalacion presione el boton instalar");
    echo("<input type=\"button\" value=\"Instalar\" onclick=\"location.href='?view=addon_modules&module=referal_constant&instalar=do'\" />");
    return 1;
}

if($_GET["remover"] == "do"){
    $db->delete("addon" , 'name="referal_constant"');
    for($i = 0 ; $i < 21 ; $i++){
        if($i < 7) {
            //level del referido
            $db->query("ALTER TABLE membership DROP COLUMN " .('gref'.$i).";" );
        }else if($i >= 7 && $i <14){
            //nombre del referido
            $db->query("ALTER TABLE membership DROP COLUMN " .('nref'.($i-7)).";" );
        }else if($i >= 14){
            //cantidad por nivel
            $db->query("ALTER TABLE membership DROP COLUMN " .('cref'.($i-14)).";" );
        }
    }
    $db->query("ALTER TABLE members DROP COLUMN reclamado;");
    $db->query("ALTER TABLE members DROP COLUMN backup_refs;");
    $db->query("ALTER TABLE members DROP COLUMN prox_crefs;");
    echo("<script>location.href=\"?view=addon_modules&module=referal_constant\"</script>");
    exit();
}



echo("<div class=\"info_box\"> <strong>MIS REFERIDOS</strong> el modulo ya esta instalado en el sitio web , si no desea continuar puede desinstalarlo presionando el siguiente boton <input type=\"button\"  value= \"REMOVER\" onclick=\"location.href='?view=addon_modules&module=referal_constant&remover=do'\" /></div>");
$listaref ;

for($i = 0 ; $i < 7 ; $i++){
    $listaref .= ", gref".$i.", cref".$i .", nref".$i;
}

$membershiplist = $db->query("SELECT id, name".$listaref." FROM membership ORDER BY price ASC ; ");
unset($listaref);

?>

<div style="text-align:center;"><h1>MIS REFERIDOS</h1></div>
<?php  while($membership = $db->fetch_array($membershiplist)) { ?>
    <h2> <?php echo($membership['name']); ?></h2>
    <form id="<?php echo("membership".strtolower($membership['id']));?>">
        <table width="100%" class="widget-tbl" >

            <tr><th></th><th>Nombre</th> <th>Cantidad</th> <th>Ganancia</th></tr>

            <?php for ($i = 0; $i < 7; $i++) { ?>
                <tr><td><?php echo(1+$i); ?></td><td style="padding-right:15px;"><input type="text" name="<?php echo('nref'.$i);?>" value="<?php echo($membership["nref".$i]);?>" style="width:100%;"/></td><td style="padding-right:15px;"><input type="text" name="<?php echo('cref'.$i);?>" value="<?php echo($membership["cref".$i]);?>" onkeypress="return filtroNumerico(event)" style="width:100%;"/></td><td style="padding-right:15px;"><input type="text" name="<?php echo('gref'.$i);?>" value="<?php echo($membership["gref".$i]);?>"  onkeypress="return filtroNumerico(event)" style="width:100%;"/></td></tr>
            <?php }?>
            <input type="hidden" name="membership_id" value="<?php echo($membership["id"]); ?>" />
            <input type="hidden" name="actualizar" value="do" />
            <tr style="text-align:center;"><td colspan="4"><input type="submit" value="Guardar"/></td></tr>

        </table>
    </form>
<?php } ?>

<script type="text/javascript">

    $("document").ready(function(){
        var formularios = $("form");
        for(var i = 1 ; i < formularios.size() ; i++){
            postClick(formularios.get(i).id);
        }
    });

    //realiza la peticion al servidor
    function postClick(id){
      $("#"+id).submit(function(e){
          e.preventDefault();
          httplocal = location.protocol+"//"+location.host+location.pathname+"modules/referal_constant.php";
          var mensaje;
          //if((mensaje = filtroCampos($("#"+id).serialize()))!="pasaelif"){alert(mensaje); return false;};

          var solicitud  = $("#"+id).serialize();

          /*AQUI QUEDE */

          $("#"+id).l2block();

          $.post(httplocal,$("#"+id).serialize()).done(function(data){

              data = JSON.parse(data);

              if(data.status == 0){
                  $("#"+id).l2error(data.msg);
              }
              else if(data.status == 1) {
                  $("#" + id).l2success(data.msg);
              }

              $("#"+id).l2unblock();

          }).fail(function(){
              $("#"+id).l2error("Error del servidor");
              $("#"+id).l2unblock();
          });

          return false;
      });
    }

    /*Funcion que sirve para comprobar los campos del formulario*/
    function filtroCampos(tabla){
        var objeto = JSON.parse(convertirJson(tabla));
        var mensaje = "pasaelif";
        var ultimocref = 0;
        var ultimofila = 0;

        $.each(objeto,function(llave , valor){
            if(llave.substr(0,4) == "cref"){
                var cantidad =  parseInt(valor);

                if(ultimocref > cantidad){
                   mensaje = "La fila " +ultimofila +"  no puede ser mayor a la fila " + llave.substr(4) + "\n en la columna Cantidad";
                }
                ultimocref = cantidad;
                ultimofila = llave.substr(4);
            }

            if(valor.trim() == ""){
                var subtitulo = "";
               switch(llave.substr(0,1)){
                   case 'n':
                       subtitulo = " Nombre";
                       break;
                   case 'g':
                       subtitulo = " Ganancias";
                       break;
                   case 'c':
                       subtitulo = " Cantidad";
                       break;
               }
                mensaje = "La fila " + (parseInt(llave.substr(4))+1) + " de " + subtitulo + " esta vacia";
            }
        });
        return mensaje;
    }

    //convierte las peticiones en formato json
    function convertirJson(tabla){
        var arreglo = tabla.split("&");
        var json = ' { ';
        for(i in arreglo){
            var par = arreglo[i].split("=");
            var llave = par[0];
            var valor = par[1];
            json += '"'+llave+'":"' + valor + ( i != arreglo.length-1 ? '",': '"}') ;
        }
        return json;
    }

    function filtroNumerico(e){
        return  e.charCode >= 58 && e.charCode <= 252 || e.charCode == 32? false : true   ;
    }
</script>