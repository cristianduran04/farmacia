<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-cart-plus fa-fw"></i> &nbsp; Nueva venta a crédito</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        $check_empresa=$insLogin->seleccionarDatos("Normal","empresa LIMIT 1","*",0);

        if($check_empresa->rowCount()==1){
            $check_empresa=$check_empresa->fetch();
    ?>
    <div class="columns">
        <div class="column pb-6">
            <form class="pt-6 pb-6" id="sale-barcode-form" autocomplete="off">
                <div class="columns">
                    <div class="column is-one-quarter">
                        <button type="button" class="button is-link is-light js-modal-trigger" data-target="modal-js-product" ><i class="fas fa-search"></i> &nbsp; Buscar producto</button>
                    </div>
                    <div class="column">
                        <div class="field is-grouped">
                            <p class="control is-expanded">
                                <input class="input" type="text" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70"  autofocus="autofocus" placeholder="Código de barras" id="sale-barcode-input" >
                            </p>
                            <a class="control">
                                <button type="submit" class="button is-info">
                                    <i class="far fa-check-circle"></i> &nbsp; Agregar producto
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <?php
                if(isset($_SESSION['alerta_producto_agregado']) && $_SESSION['alerta_producto_agregado']!=""){
                    echo '
                    <div class="notification is-success is-light">
                      '.$_SESSION['alerta_producto_agregado'].'
                    </div>
                    ';
                    unset($_SESSION['alerta_producto_agregado']);
                }

                if(isset($_SESSION['venta_codigo_factura']) && $_SESSION['venta_codigo_factura']!=""){
            ?>
            <div class="notification is-info is-light mb-2 mt-2">
                <h4 class="has-text-centered has-text-weight-bold">Venta realizada</h4>
                <p class="has-text-centered mb-2">La venta se realizó con éxito. ¿Qué desea hacer a continuación? </p>
                <br>
                <div class="container">
                    <div class="columns">
                        <div class="column has-text-centered">
                            <button type="button" class="button is-link is-light" onclick="print_ticket('<?php echo APP_URL."app/pdf/ticket.php?code=".$_SESSION['venta_codigo_factura']; ?>')" >
                                <i class="fas fa-receipt fa-2x"></i> &nbsp;
                                Imprimir ticket de venta
                            </buttona>
                        </div>
                        <div class="column has-text-centered">
                            <button type="button" class="button is-link is-light" onclick="print_invoice('<?php echo APP_URL."app/pdf/invoice.php?code=".$_SESSION['venta_codigo_factura']; ?>')" >
                                <i class="fas fa-file-invoice-dollar fa-2x"></i> &nbsp;
                                Imprimir factura de venta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    unset($_SESSION['venta_codigo_factura']);
                }
            ?>
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th class="has-text-centered">#</th>
                            <th class="has-text-centered">Código de barras</th>
                            <th class="has-text-centered">Producto</th>
                            <th class="has-text-centered">Cant.</th>
                            <th class="has-text-centered">Precio</th>
                            <th class="has-text-centered">Subtotal</th>
                            <th class="has-text-centered">Actualizar</th>
                            <th class="has-text-centered">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(isset($_SESSION['datos_producto_venta']) && count($_SESSION['datos_producto_venta'])>=1){

                                $_SESSION['venta_total']=0;
                                $cc=1;

                                foreach($_SESSION['datos_producto_venta'] as $productos){
                        ?>
                        <tr class="has-text-centered" >
                            <td><?php echo $cc; ?></td>
                            <td><?php echo $productos['producto_codigo']; ?></td>
                            <td><?php echo $productos['venta_detalle_descripcion']; ?></td>
                            <td>
                                <div class="control">
                                    <input class="input sale_input-cant has-text-centered" value="<?php echo $productos['venta_detalle_cantidad']; ?>" id="sale_input_<?php echo str_replace(" ", "_", $productos['producto_codigo']); ?>" type="text" style="max-width: 80px;">
                                </div>
                            </td>
                            <td><?php echo MONEDA_SIMBOLO.number_format($productos['venta_detalle_precio_venta'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></td>
                            <td><?php echo MONEDA_SIMBOLO.number_format($productos['venta_detalle_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?></td>
                            <td>
                                <button type="button" class="button is-success is-rounded is-small" onclick="actualizar_cantidad('#sale_input_<?php echo str_replace(" ", "_", $productos['producto_codigo']); ?>','<?php echo $productos['producto_codigo']; ?>')" >
                                    <i class="fas fa-redo-alt fa-fw"></i>
                                </button>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off">

                                    <input type="hidden" name="producto_codigo" value="<?php echo $productos['producto_codigo']; ?>">
                                    <input type="hidden" name="modulo_venta" value="remover_producto">

                                    <button type="submit" class="button is-danger is-rounded is-small" title="Remover producto">
                                        <i class="fas fa-trash-restore fa-fw"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                                $cc++;
                                $_SESSION['venta_total']+=$productos['venta_detalle_total'];
                            }
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="4"></td>
                            <td class="has-text-weight-bold">
                                TOTAL
                            </td>
                            <td class="has-text-weight-bold">
                                <?php echo MONEDA_SIMBOLO.number_format($_SESSION['venta_total'],MONEDA_DECIMALES,MONEDA_SEPARADOR_DECIMAL,MONEDA_SEPARADOR_MILLAR)." ".MONEDA_NOMBRE; ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <?php
                            }else{
                                    $_SESSION['venta_total']=0;
                        ?>
                        <tr class="has-text-centered" >
                            <td colspan="8">
                                No hay productos agregados
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="column is-one-quarter">
            <h2 class="title has-text-centered">Datos de la venta a crédito</h2>
            <hr>

            <?php if($_SESSION['venta_total']>0){ ?>
            <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/ventaAjax.php" method="POST" autocomplete="off" name="formsale" >
                <input type="hidden" name="modulo_venta" value="registrar_venta_credito">
            <?php }else { ?>
            <form name="formsale">
            <?php } ?>

                <div class="control mb-5">
                    <label>Fecha</label>
                    <input class="input" type="date" value="<?php echo date("Y-m-d"); ?>" readonly >
                </div>

                <label>Caja de ventas <?php echo CAMPO_OBLIGATORIO; ?></label><br>
                <div class="select mb-5">
                    <select name="venta_caja">
                        <?php
                            $datos_cajas=$insLogin->seleccionarDatos("Normal","caja ORDER BY caja_nombre ASC","*",0);
                            $datos_cajas=$datos_cajas->fetchAll();
                            foreach($datos_cajas as $rows){
                                echo '<option value="'.$rows['caja_id'].'">'.$rows['caja_nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="control mb-5">
                    <label>Nombre del cliente <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="text" name="venta_cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,70}" maxlength="70" required >
                </div>

                <div class="control mb-5">
                    <label>Plazo de crédito (días) <?php echo CAMPO_OBLIGATORIO; ?></label>
                    <input class="input" type="number" name="plazo_credito" min="1" required >
                </div>

                <div class="control mb-5">
                    <label>Fecha de vencimiento</label>
                    <input class="input" type="date" name="fecha_vencimiento" value="<?php echo date("Y-m-d", strtotime("+30 days")); ?>" readonly >
                </div>

                <div class="control mb-5">
                    <label>Nota o comentario</label>
                    <textarea class="textarea" name="venta_comentario" maxlength="250" rows="3"></textarea>
                </div>

                <p class="has-text-centered">
                    <button type="submit" class="button is-info is-rounded">Registrar venta</button>
                </p>

            </form>
        </div>
    </div>
    <?php
        }else{
            include "./inc/empresa_config.php";
        }
    ?>
</div>
