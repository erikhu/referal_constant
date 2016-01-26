<div class="widget-main-title">Mi escala de referidos</div>
<div>
{if $reclamado == "Y"}
    <div class="info_box">
        Felicidades has reclamado {$ganancias}
    </div>
{/if}

<table style="width:100%">
    <tr><td colspan="3" style="text-align: center;"><h1>{$membresia}</h1></td></tr>
    <tr><th >Nivel actual</th><th>Cantidad de referidos</th><th>Proximas ganancias</th></tr>
    <tr><td style="text-align: center;">{$nivel_actual}</td><td style="text-align: center;">{$cantidad}</td><td style="text-align: center;">{$prox_ganancias}</td></tr>
    <tr><th >Ganancia anterior</th><th>Estado</th><th>Referidos para reclamar <br/> el proximo premio</th></tr>
    <tr><td style="text-align: center;">{$ganancias}</td><td style="text-align: center;">{if $reclamado == "N"}Espera{else}Reclamado{/if}</td><td style="text-align: center;">{$prox_meta}</td></tr>
</table>

</div>