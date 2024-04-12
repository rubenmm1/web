<?php
@session_start();
if (!isset($_SESSION['cod_usuario'])) {
    header("location: index.php");
}

include ("../conexion.php");
$codigo_maquina = filter_input(INPUT_POST, 'cod_maquina');

$codigo_cliente = filter_input(INPUT_POST, 'cod_cliente');

$codigo_incidencia = filter_input(INPUT_POST, 'cod_incidencia');

$codigo_revision = filter_input(INPUT_POST, 'cod_revision_inc');
// var_dump($codigo_cliente, $codigo_incidencia, $codigo_maquina, $codigo_revision);



/*
 * En el caso de que sea una modificacion se cargan los datos
 */

$fecha = date("Y-m-d");
$cont_fotos = 1;
$html_btn_annadir_foto = '<div class="col-6 d-flex justify-content-center align-items-center div_btn_camara p-5"><div class="col-md-auto btn_circular puntero" id="add_foto"><center><i class="fa fa-camera" aria-hidden="true"></i></center></div></div>';

// if (!empty($codigo_incidencia)) {// Modificar incidencia

    /*
     * Obtener la información de la incidencia
     */
    $sql = "select * from incidencias where id_revision=" . $codigo_revision;

    $resul = $conexion->query($sql);

    while ($fila = $resul->fetch_assoc()) {
        $descripcion = $fila['descripcion'];
        $fecha = $fila["fecha"];
        $codigo_incidencia = $fila["cod_incidencia"];
    }


    //////////// Obtengo las líneas de la incidencia
    $sqlLineas = "SELECT incidencias_lineas.id_articulo, incidencias_lineas.cantidad, incidencias_lineas.descripcion,articulos.nombre  from incidencias_lineas inner join articulos on cod_articulo=id_articulo where id_incidencia=".$codigo_incidencia;
    // var_dump($sqlLineas);

    $resulLineas = $conexion->query($sqlLineas);

    $array_lineas = array();
    if(!$conexion->error){
        


        while ($filaLinea=$resulLineas->fetch_assoc()) {
            $array_dato = array();
            $array_dato['id_articulo'] = $filaLinea['id_articulo'];
            $array_dato['descripcion'] = $filaLinea['descripcion'];
            $array_dato['cantidad'] = $filaLinea['cantidad'];
            $array_dato['nombre'] = $filaLinea['nombre'];
            
            array_push($array_lineas, $array_dato);
        }
        // var_dump($array_lineas);
        
    }
    /*
     * En el caso de tener imagenes, mostrar las imagenes     * 
     */

    $fotos_guardadas = "";

if (!$codigo_incidencia)
    $where = '';
else
    $where="where id_incidencia=" . $codigo_incidencia;

    $sql = "select * from incidencias_fotos " . $where;

    $resulF = $conexion->query($sql);
    while ($filaF = $resulF->fetch_assoc()) {

        $fotos_guardadas .= ' <div id="card-' . $cont_fotos . '" class="card col-md-6 col-sm-12 p-2"> 
                <div class=row>
                    <div class="col-6"><label for="id=txt_foto_' . $cont_fotos . '">Descripcion</label></div>
                    <div class="col-6 text-right btn_eliminar_foto puntero" nom_foto="' . $filaF['foto'] . '" cod_registro="' . $cont_fotos . '" cod_foto="' . $filaF['cod_incidencias_fotos'] . '" cod_incidencia="' . $codigo_incidencia . '" ><i class="fa fa-trash btn_eliminar puntero tx-danger" cod="4"></i></div>
                </div>                        
                <input type="text"  class="form-control" id=txt_foto_' . $cont_fotos . ' value="' . $filaF['descripcion_foto'] . '" />
                <img id="card-img-top_' . $cont_fotos . '" src="./fotos/incidencias/' . $codigo_incidencia . '/' . $filaF['foto'] . '">
                <input type="file" id=foto_' . $cont_fotos . '  class="fotos" val="' . $cont_fotos . '" data-max-file-size="3M" />               
            </div>';
        $cont_fotos++;
    }
// }
?>
<!DOCTYPE html>
<html lang="es">
    <!--- Internal Select2 css-->
    <link href="../assets/plugins/select2/css/select2.min.css" rel="stylesheet">

    <!---Internal Fileupload css-->
    <link href="../assets/plugins/fileuploads/css/fileupload.css" rel="stylesheet" type="text/css"/>
    <?php
    include("head.php");
    ?>
    <body class="main-body">
        <!-- Loader -->
        <?php
        include("loader.php");
        ?>
        <!-- /Loader -->

        <!-- Page -->
        <div class="page">

            <!-- main-header opened -->
            <?php
            include("main-header.php");
            ?>
            <!-- /main-header -->

            <!-- centerlogo-header opened -->
            <?php
            include("centerlogo-header.php");
            ?>
            <!-- /centerlogo-header closed -->

            <!--Horizontal-main -->
            <?php
            include("horizontal-main.php");
            ?>
            <!--Horizontal-main -->

            <!-- main-content opened -->
            <div class="main-content horizontal-content">

                <!-- container opened -->
                <div class="container">

                    <!-- breadcrumb -->
                    <div class="breadcrumb-header justify-content-between">
                        <div class="my-auto">                            
                            <div class="d-flex"><h4 class="content-title mb-0 my-auto">Clientes</a><span class="text-muted mt-1 tx-13 ms-2 mb-0">/ <span class='content-title mb-0 my-auto h5'>Maquinas asociadas</span> /  <span class='content-title mb-0 my-auto h5'>Revisiones</span> / Incidencia</div>
                                        </div>
                                        </div>
                                        <!-- breadcrumb -->

                                        <div class="row  ">
                                            <div class="col-lg-8 col-8-12 col-md-8 col-sm-12 mx-auto">
                                                <div class="card  box-shadow-0">

                                                    <div class="card-header"></div>

                                                    <div class="card-body pt-0">
                                                        <form class="form-horizontal" >                                        
                                                            <div class="form-group">
                                                                <label class="form-label" for="fecha">Fecha</label>
                                                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha; ?>">
                                                                <label class="form-label" for="descripcion">Descripcion</label>
                                                                <textarea class="form-control" name="descripcion" placeholder="Descripcion" id="descripcion" rows="3"><?php echo $descripcion; ?></textarea>
                                                                <input type ="hidden" id='cod_incidencia' name="cod_incidencia" value="<?php echo $codigo_incidencia; ?>">
                                                            </div>
                                                        </form>
                                                        <hr>
                                                        <div class="form-group mb-0 mt-3 ">
                                                            <label class="form-label h3">Articulos</label>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="txt_articulo">Articulo</label>                                                                    
                                                                    <select id="txt_articulo" class="form-control" >
                                                                        <option></option>
                                                                        <?php
                                                                        $sql = "select * from articulos where tipo=2 order by nombre";
                                                                        $resultAr = $conexion->query($sql);

                                                                        while ($filaAr = $resultAr->fetch_assoc()) {
                                                                            echo "<option value='" . $filaAr['cod_articulo'] . "'>" . $filaAr['nombre'] . "</option>";
                                                                        }
                                                                        ?>                                                                        
                                                                    </select>

                                                                </div>
                                                                
                                                                <div class="col-md-3">
                                                                    <label for="txt_cantidad">Cantidad</label>
                                                                    <input type="number" id="txt_cantidad" class="form-control">

                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label for="txt_cantidad"><br></label>
                                                                    <div class="btn btn-success" id="btn_annadir_articulo">Añadir</div>
                                                                    <input type='hidden' id='cod_reg_mod'>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="txt_descripcion_linea">Descripcion</label>
                                                                    <textarea class="form-control" name="txt_descripcion_linea" placeholder="Descripcion" id="txt_descripcion_linea" rows="3"></textarea>

                                                                </div>

                                                                <div class="col-12 pt-3">
                                                                    <div class="table-responsive hoverable-table">

                                                                        <table id="tabla_listado" class="table text-md-nowrap">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th data-orderable="false" >Articulo</th>                                                                                    
                                                                                    <th data-orderable="false">Cantidad</th>                                                                                    
                                                                                    <th data-orderable="false">Descripcion</th>         
                                                                                    <th data-orderable="false"></th>
                                                                                    <th data-orderable="false"></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="body_table">                 
                                                                                <!-- PINTO LAS LINEAS DE LA INCIDENCIA -->
                                                                                <?php
                                                                                
                                                                                foreach ($array_lineas as $linea) {
                                                                               
                                                                                    echo "<tr>
                                                                                    <td>" . $linea['nombre'] . "</td>
                                                                                    <td>" . $linea['cantidad'] . "</td>
                                                                                    <td>" . $linea['descripcion'] . "</td>
                                                                                    <td class='btn_modificar' desc = '". $linea['descripcion'] ."' cod_articulo = '".$linea['id_articulo']."' nombre_articulo = '".$linea['nombre']."' cantidad='".$linea['cantidad']."'><i class='fa fa-edit'></i></td>
                                                                                    <td class='btn_borrar'><i class='far fa-trash-alt'></i></td></tr>";
                                                                                    
                                                                                }
                                                                                ?>                                                               
                                                                            </tbody>
                                                                        </table>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>
                                                        <hr>
                                                        <div class="form-group mb-0 mt-3">
                                                            <label class="form-label h3">Fotos</label>

                                                            <div class="row" id="capa_imagenes"><!-- capa donde se irán cargando las fotos a subir -->
                                                                <?php echo $fotos_guardadas; ?>
                                                                <?php echo $html_btn_annadir_foto; ?>
                                                            </div>

                                                        </div>


                                                        <div class="form-group mb-0 mt-5 text-center">

                                                            <div>
                                                                <form action="revisiones.php" method="post" ><!-- comment -->
                                                                    <input type="hidden" name="cod_maquina" id="cod_maquina" value='<?php echo $codigo_maquina; ?>' />
                                                                    <input type="hidden" name="cod_cliente" id="cod_cliente" value='<?php echo $codigo_cliente; ?>' />                                                        
                                                                    <div id="btn-guardar" class="btn btn-primary">Guardar</div>
                                                                    <input type="submit" id="btn_volver" class="btn btn-secondary" value="Volver">
                                                                    <div id='msn'></div>
                                                                </form>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>                                            

                                        </div>


                                        </div>
                                        <!-- Container closed -->
                                        </div>
                                        <!-- main-content closed -->

                                        <!-- Sidebar-right-->
                                        <?php
//include("sidebar.php");
                                        ?>
                                        <!--/Sidebar-right-->

                                        <!-- Footer opened -->
                                        <?php
                                        include("footer.php");
                                        ?>
                                        <!-- Footer closed -->

                                        </div>
                                        <!-- End Page -->

                                        <!-- Back-to-top -->
                                        <a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>

                                        <!-- JQuery min js -->
                                        <script src="../assets/plugins/jquery/jquery.min.js"></script>

                                        <!-- Bootstrap Bundle js -->
                                        <script src="../assets/plugins/bootstrap/js/popper.min.js"></script>
                                        <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

                                        <!-- Ionicons js -->
                                        <script src="../assets/plugins/ionicons/ionicons.js"></script>

                                        <!-- Moment js -->
                                        <script src="../assets/plugins/moment/moment.js"></script>

                                        <!-- P-scroll js -->
                                        <script src="../assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
                                        <script src="../assets/plugins/perfect-scrollbar/p-scroll.js"></script>

                                        <!-- Rating js-->
                                        <script src="../assets/plugins/rating/jquery.rating-stars.js"></script>
                                        <script src="../assets/plugins/rating/jquery.barrating.js"></script>

                                        <!-- Custom Scroll bar Js-->
                                        <script src="../assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

                                        <!-- Horizontalmenu js-->
                                        <script src="../assets/plugins/horizontal-menu/horizontal-menu-2/horizontal-menu.js"></script>

                                        <!-- Sticky js -->
                                        <script src="../assets/js/sticky.js"></script>

                                        <!-- Right-sidebar js -->
                                        <script src="../assets/plugins/sidebar/sidebar.js"></script>
                                        <script src="../assets/plugins/sidebar/sidebar-custom.js"></script>

                                        <!-- eva-icons js -->
                                        <script src="../assets/js/eva-icons.min.js"></script>

                                        <!--Internal Fileuploads js-->
                                        <script src="../assets/plugins/fileuploads/js/fileupload.js"></script>
                                        <script src="../assets/plugins/fileuploads/js/file-upload.js"></script>

                                        <!--Internal Fancy uploader js-->
                                        <script src="../assets/plugins/fancyuploder/jquery.ui.widget.js"></script>
                                        <script src="../assets/plugins/fancyuploder/jquery.fileupload.js"></script>
                                        <script src="../assets/plugins/fancyuploder/jquery.iframe-transport.js"></script>
                                        <script src="../assets/plugins/fancyuploder/jquery.fancy-fileupload.js"></script>
                                        <script src="../assets/plugins/fancyuploder/fancy-uploader.js"></script>


                                        <!-- custom js -->
                                        <script src="../assets/js/custom.js"></script>

                                        <script>

                                            var cont_fotos = <?php echo $cont_fotos; ?>;
                                            var arrayarticulos = [];
                                            var reg = 0; ///variable que contiene el indice del listado de los productos a presupuestar.

                                            $(document).ready(function () {
                                                // var tabla_articulos = $('#body_table');

                                                // tabla_articulos.append('<tr><td>ewceccewrcewcc</td></tr>')

                                            });
                                            
                                            
                                            
                                            
                                            $("#btn-guardar").on("click", function () {
                                                
                                                
                                                arrayfotos = [];
                                                descripcion = $("#descripcion").val();
                                                cod_revision = <?php echo $codigo_revision;?>;
                                                cod_maquina =<?php echo $codigo_maquina; ?>;
                                                cod_usuario =<?php echo $_SESSION['cod_usuario']; ?>;
                                                cod_cliente = <?php echo $codigo_cliente;?>
                                                
                                                cod_incidencia = $("#cod_incidencia").val();


                                                // for (i = 1; i < cont_fotos; i++) {

                                                //     /*obj_img = "#foto_" + i;*/
                                                //     obj_img = "#card-img-top_" + i;
                                                //     obj_txt = "#txt_foto_" + i;

                                                //     /*ruta_foto_aux = $(obj_img).val();*/
                                                //     ruta_foto_aux = $(obj_img).attr("src");
                                                //     array_nom_foto = ruta_foto_aux.split("/");
                                                //     arrayaux = [array_nom_foto[array_nom_foto.length - 1], $(obj_txt).val()];
                                                //     arrayfotos.push(arrayaux);

                                                // }

                                                // Obtengo las imagenes
                                                var cont_imagenes =$('#capa_imagenes');
                                                var imagenes = cont_imagenes.children().toArray();

                                                imagenes.forEach((imagen,index) => {
                                                    if(index!=imagenes.length-1){
                                                        // console.log(imagen);
                                                        // ruta de la imagen
                                                        let ruta_aux =  $('#card-img-top_'+(index+1)).attr("src");
                                                        let array_ruta = ruta_aux.split('/');
                                                        // Descripcion de la imagen
                                                        let descripcion = $('txt_foto_'+(index+1)).val();
                                                        let array_aux=[array_ruta[array_ruta.length-1],descripcion];
                                                        arrayfotos.push(array_aux);

                                                    }

                                                });
                                                

                                                // Obtengo los articulos
                                                var tabla= $('#body_table');
                                                var filasArt=tabla.children().toArray();

                                                arrayarticulos=[];

                                                filasArt.forEach(element => {
                                                    arrayarticulos.push([element.children[3].attributes.getNamedItem('cod_articulo').value,element.children[1].innerHTML,element.children[2].innerHTML]);

                                                });

                                                //console.log(arrayaux);
                                                var valParam = JSON.stringify(arrayfotos);
                                                var valParamArticulos = JSON.stringify(arrayarticulos);
                                                // console.log(arrayarticulos)

                                                // console.log("Guardar--------------")
                                                // console.log(valParam);
                                                // console.log(valParamArticulos)
                                                
                                                $.ajax({
                                                    url: 'add_incidencia_jquery.php',
                                                    type: 'post',
                                                    data: "id_revision=" + cod_revision + "&cod_cliente=" + cod_cliente + "&descripcion=" + descripcion + "&fotos=" + valParam + "&articulos=" + valParamArticulos,
                                                    beforeSend: function () {
                                                        $("#msn").html('<div class="text-center col-md-12 m-5"><div class="spinner-border avatar-lg text-primary m-2" role="status"></div></div>');
                                                    },
                                                    success: function (response) {
                                                        // $("#msn").html(response);
                                                       window.location.href = "incidencias.php";
                                                        $("#btn_volver").trigger("click");
                                                        // console.log(response);
                                                    }
                                                });
                                                

                                            });

                                            $(document).on('change', '.fotos', function () {
                                                var id = $(this).attr("val");
                                                var formData = new FormData();
                                                var files = $(this)[0].files[0];
                                                // console.log(files)
                                                formData.append('file', files);
                                                $.ajax({
                                                    url: 'upload.php',
                                                    type: 'post',
                                                    data: formData,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function (response) {
                                                        if (response != 0) {
                                                            // console.log(response);
                                                            var cont_imagenes =$('#capa_imagenes').children().toArray().length-1;
                                                            // console.log(cont_imagenes);
                                                            $("#card-img-top_" + cont_imagenes).attr("src", response);
                                                        } else {
                                                            alert('Formato de imagen incorrecto.');
                                                        }
                                                    }
                                                });
                                                return false;
                                            });


                                            $(document).on('click', '#add_foto', function () {
                                                var cont_imagenes =$('#capa_imagenes').children().toArray().length;
                                                $(".div_btn_camara").remove();
                                                $('#capa_imagenes').append('<div id="card-' + cont_imagenes + '" class="col-md-6 col-sm-12 card p-1"> \n\
                                                                                <div class=row>\n\
                                                                                        <div class="col-6"><label for="id=txt_foto_' + cont_imagenes + '">Descripcion</label></div>\n\
                                                                                        <div class="col-6 text-right btn_eliminar_foto puntero" nom_foto="" cod_incidencia="" cod_registro="' + cont_imagenes + '" cod_foto="" ><i class="fa fa-trash btn_eliminar puntero tx-danger" cod="4"></i></div>\n\
                                                                                    </div>\n\
                                                                                    <input type="text"  class="form-control" id=txt_foto_' + cont_imagenes + ' />\n\
                                                                                    <img id="card-img-top_' + cont_imagenes + '" src="../assets/img/default_image.jpg">\n\
                                                                                    <input type="file" id=foto_' + cont_imagenes + '  class="fotos" val="' + cont_imagenes + '" data-max-file-size="3M" />\n\
                                                                                </div><?php echo $html_btn_annadir_foto; ?>');

                                                cont_fotos++;
                                            });

                                            $(document).on('click', '.btn_eliminar_foto', function () {

                                                cod_registro = $(this).attr("cod_registro");
                                                cod_foto = $(this).attr("cod_foto");

                                                cod_incidencia = $(this).attr("cod_incidencia");


                                                if (confirm("Se eliminará definitivamente la foto, deseas eliminar?")) {

                                                    if (cod_foto === "") {// ficha nueva

                                                        /*
                                                         * Obtengo el nombre del fichero subido. 
                                                         */

                                                        obj_img = "#card-img-top_" + cod_registro;
                                                        ruta_foto_aux = $(obj_img).attr("src");
                                                        array_nom_foto = ruta_foto_aux.split("/");
                                                        nom_foto = array_nom_foto[array_nom_foto.length - 1];


                                                    } else {// ficha cargada de las fotos
                                                        nom_foto = $(this).attr("nom_foto");
                                                    }

                                                    $("#card-" + cod_registro).remove();

                                                }

                                            });

                                            $("#btn_annadir_articulo").on("click", function () {
                                               
                                                txt_articulo = $("#txt_articulo option:selected").text();
                                                cod_articulo = $("#txt_articulo").val();
                                                txt_cantidad = $("#txt_cantidad").val();
                                                cod_reg_mod = $("#cod_reg_mod").val();
                                                descripcion = $("#txt_descripcion_linea").val();
                                                
                                               
                                                if (cod_articulo == "" || txt_cantidad == "") {
                                                    alert("Debes seleccionar un articulo e indicar una cantidad")

                                                } else {
                                                    
                                                    arrayarticuloadd= [cod_articulo,txt_cantidad,txt_articulo,descripcion];
                                                    if (cod_reg_mod!=""){
                                                        arrayarticulos.splice(cod_reg_mod,1,arrayarticuloadd);
                                                        $("#reg_"+cod_reg_mod).html("<td>" + txt_articulo + "</td><td>" + txt_cantidad + "</td><td>" + descripcion + "</td><td class='btn_modificar' desc = '"+ descripcion +"' cod_articulo = '"+cod_articulo+"' nombre_articulo = '"+txt_articulo+"' cod_reg ="+cod_reg_mod+" cantidad='"+txt_cantidad+"'><i class='fa fa-edit'></i></td><td class='btn_borrar' cod_reg ="+cod_reg_mod+"><i class='far fa-trash-alt'></i></td>");
                                                    }else{
                                                        arrayarticulos.push(arrayarticuloadd);
                                                        $("#body_table").append("<tr id='reg_"+reg+"'><td>" + txt_articulo + "</td><td>" + txt_cantidad + "</td><td>" + descripcion + "</td><td class='btn_modificar' desc = '"+ descripcion +"' cod_articulo = '"+cod_articulo+"' nombre_articulo = '"+txt_articulo+"' cod_reg ="+reg+" cantidad='"+txt_cantidad+"'><i class='fa fa-edit'></i></td><td class='btn_borrar' cod_reg ="+reg+"><i class='far fa-trash-alt'></i></td></tr>");                                                    
                                                        reg++;
                                                    }
                                                    
                                                    
                                                    $("#txt_articulo").val("");
                                                    $("#txt_cantidad").val("");
                                                    $("#cod_reg_mod").val("");
                                                    $("#txt_descripcion_linea").val("");
                                                    console.log(arrayarticulos);                                             
                                                    
                                                }
                                                

                                            });
                                            
                                            $(document).on('click', '.btn_borrar', function (evt) {
                                                evt.target.parentNode.parentNode.remove();
                                                
                                            })
                                            
                                            $(document).on('click', '.btn_modificar', function () {
                                                
                                                codigo_reg = $(this).attr("cod_reg");
                                                codigo_articulo = $(this).attr("cod_articulo");
                                                nombre_articulo = $(this).attr("nombre_articulo");
                                                cantidad = $(this).attr("cantidad");
                                                descripcion = $(this).attr("desc");
                                                
                                                $("#txt_articulo option[value="+ codigo_articulo +"]").attr("selected","selected");
                                                $("#txt_articulo").val(codigo_articulo);
                                                                                                                                             
                                                
                                                $("#txt_cantidad").val(cantidad);
                                                $("#cod_reg_mod").val(codigo_reg);
                                                $("#txt_descripcion_linea").val(descripcion);
                                                
                                                console.log(arrayarticulos);
                                                
                                            })
                                            
                                            $("#txt_articulo").on("change",function(){
                                                cantidad = $("#txt_cantidad").val();
                                                if(cantidad==''){
                                                    $("#txt_cantidad").val('1');
                                                }
                                            })
                                            
                                        </script>
                                        </body>
                                        </html>
