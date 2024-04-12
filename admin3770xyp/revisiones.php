<?php
@session_start();
if (!isset($_SESSION['cod_usuario'])) {
    header("location: index.php");
}

include ("../conexion.php");

/** @var $_POST */
$cod_maquina = filter_input(INPUT_POST, 'cod_maquina');
$cod_cliente = filter_input(INPUT_POST, 'cod_cliente');
?>
<!DOCTYPE html>
<html lang="es">
    <!-- Internal Data table css -->
    <link href="../assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="../assets/plugins/datatable/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/plugins/datatable/responsive.bootstrap5.css" rel="stylesheet" />
    <link href="../assets/plugins/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="../assets/plugins/datatable/responsive.dataTables.min.css" rel="stylesheet">
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
                            <div class="d-flex"><h4 class="content-title mb-0 my-auto">Clientes</a><span class="text-muted mt-1 tx-13 ms-2 mb-0">/ <span class='content-title mb-0 my-auto h5'>Máquinas asociadas</span> /  Revisiones </div>
                        </div>
                    </div>
                    <!-- breadcrumb -->

                    <div class="row">

                        <div class="col-md-3 col-xl-3 mt-5">
                        

                            <div class="card">                               
                                <div class="card-body">
                                    <?php
                                    $sql = 'select * from maquinas m
                                            inner join articulos a on a.cod_articulo=m.id_articulo
                                            where activa=1 and cod_maquina=' . $cod_maquina;

                                    //echo $sql;
                                    $result = $conexion->query($sql);
                                    if ($result->num_rows == 0) {// Usuario web no existente
                                        echo "No hay maquinas";
                                    } else {// existe el usuario web
                                        while ($fila1 = $result->fetch_assoc()) :
                                            echo "
                                                <font><strong>" . $fila1['nombre_maquina'] . "</strong></font><br>                                                                                                        
                                                <font class='small'>SN:<strong>" . $fila1['nserie'] . "</strong></font>
                                                <img src='../fotos/" . $fila1['id_articulo'] . "/" . $fila1['id_articulo'] . ".jpg' class='img-fluid' />
                                                <p>Revisión</p>
                                                <td class='text-left tx-bold'>" . $fila1['descripcion_maquina'] . "</td>";
                                        endWhile;
                                    }
                                    ?>
                                </div>                                
                            </div>
                        </div>

                        <div class="col-md-9 ">
                            <div class="row">
                                <div class="col-12 text-left pb-2">
                                    <div class="btn btn-success text-left" id="btn_annadir_rev">Añadir Revisión</div>
                                </div>
                                <form action="revisiones-gestion.php" method="post" id="form_rev_gestion">
                                    <input type="hidden" name="cod_maquina" value="<?php echo $cod_maquina; ?>">
                                    <input type="hidden" name="cod_revision" id="cod_revision" value="">                                    
                                    <input type="hidden" name="editar" id="editar" value="1">                                    
                                    <input type="hidden" name="cod_cliente" id="cod_cliente" value="<?php echo $cod_cliente; ?>">                                    
                                </form>
                                
                                 <form action="incidencias-gestion.php" method="post" id="form_inc_gestion">
                                    <input type="hidden" name="cod_maquina" value="<?php echo $cod_maquina; ?>">
                                    <input type="hidden" name="cod_revision_inc" id="cod_revision_inc" value="">                                    
                                    <input type="hidden" name="cod_cliente" id="cod_cliente" value="<?php echo $cod_cliente; ?>">                                    
                                 </form>
                                 
                            </div>

                            <div class="card"> 
                                <div class="card-header">
                                    <h3>Revisiones</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive hoverable-table">
                                        <table id="tabla_listado" class="table text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th class="">Descripcion</th>                                                    
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT r.*,m.*, DATE_FORMAT(r.fecha, '%d-%m-%Y') as fecha from revisiones r
                                                        inner join maquinas m on m.cod_maquina=r.id_maquina
                                                        where  id_maquina=" . $cod_maquina . " Order by fecha desc";

                                                $result2 = $conexion->query($sql);

                                                if ($result2->num_rows != 0) {// Usuario web no existente                                               
                                                    while ($fila1 = $result2->fetch_assoc()) :
                                                        // Sacar las incidencias de cada revision
                                                        $sqlInci='select count(*) as count from incidencias where id_revision='.$fila1['cod_revision'];
                                                        $resultInci = $conexion->query($sqlInci)->fetch_assoc();
                                                        // var_dump($resultInci);
                                                        if ($resultInci['count'] > 0)
                                                            $color = 'green';
                                                        else
                                                            $color = 'black';

                                                        echo "
                                                            <tr id='reg_" . $fila1['cod_maquina'] . "'>                                                            
                                                            <td class='text-left tx-bold'>" . $fila1['fecha'] . "</td>                                                            
                                                            <td class='text-left small'>" . $fila1['descripcion'] . "</td>                                                            
                                                            <td class='text-center wp-20'><i style='color:".$color."' class='icon ion-ios-list-box btn_ver_incidencias puntero' cod_rev=" . $fila1['cod_revision'] . "></i></td>";

                                                            if(isset($fila1['firma']) && trim($fila1['firma'])!='' ){
                                                                echo "<td class='text-center wp-20'><i class='fa fa-eye btn_ver_revision puntero' cod_rev=" . $fila1['cod_revision'] . "></i></td>";
                                                            }else{
                                                                echo "<td class='text-center wp-20'><i class='fa fa-edit btn_editar_revision puntero' cod_rev=" . $fila1['cod_revision'] . "></i></td>";
                                                            }
                                                        echo "</tr>";

                                                    endWhile;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group mb-0 mt-5 text-center">
                                        <div>
                                            <form action="clientes-gestion.php" method="post" ><!-- comment -->
                                                <input type="hidden" name="cod_maquina" id="cod_maquina" value='<?php echo $cod_maquina; ?>' />
                                                <input type="hidden" name="cod_cliente" id="cod_cliente" value='<?php echo $cod_cliente; ?>' />                                                                                                        
                                                <input type="submit" class="btn btn-secondary" value="Volver">                                                
                                            </form>
                                        </div>
                                    </div>                                     

                                </div><!-- card-body -->
                            </div><!-- card -->
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

        <!-- custom js -->
        <script src="../assets/js/custom.js"></script>

        <!-- Internal Data tables -->
        <script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
        <script src="../assets/plugins/datatable/datatables.min.js"></script>
        <script src="../assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
        <script src="../assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
        <script src="../assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
        <script src="../assets/plugins/datatable/js/jszip.min.js"></script>
        <script src="../assets/plugins/datatable/js/buttons.html5.min.js"></script>
        <script src="../assets/plugins/datatable/js/buttons.print.min.js"></script>
        <script src="../assets/plugins/datatable/js/buttons.colVis.min.js"></script>
        <script src="../assets/plugins/datatable/pdfmake/pdfmake.min.js"></script>
        <script src="../assets/plugins/datatable/pdfmake/vfs_fonts.js"></script>

        <script>
            $('#tabla_listado').DataTable({
                responsive: true,
                order: [[0, "desc"]],
                columnDefs: [
                    { "width": "65%", "targets": 1 },
                    
                ],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }
            });

            $("#btn_annadir_rev").on("click", function () {
                $("#form_rev_gestion").submit();
            });

            $(".btn_editar_revision").on("click", function () {
                cod_revision = $(this).attr("cod_rev");
                $("#cod_revision").val(cod_revision);
                $("#editar").val(1);
                $("#form_rev_gestion").submit();
            });

            $(".btn_ver_revision").on("click", function () {
                cod_revision = $(this).attr("cod_rev");
                $("#cod_revision").val(cod_revision);
                $("#editar").val(0);
                $("#form_rev_gestion").submit();
            });
            
            $(".btn_ver_incidencias").on("click", function () {
                cod_revision = $(this).attr("cod_rev");                
                $("#cod_revision_inc").val(cod_revision);
                $("#form_inc_gestion").submit();
            });
            
            

        </script>
    </body>
</html>