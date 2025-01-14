<?php
use function MongoDB\BSON\toJSON;

@session_start();
if (!isset($_SESSION['cod_usuario'])) {
    header("location: index.php");
}

include ("../conexion.php");
include ("../functions.php");

$var_cod_maquina = filter_input(INPUT_POST, "cod_maquina");
$var_cod_usuario = filter_input(INPUT_POST, "cod_usuario");
$var_descripcion = htmlspecialchars(filter_input(INPUT_POST, "descripcion"), ENT_QUOTES | ENT_HTML401, 'UTF-8');
$var_array_fotos = json_decode(filter_input(INPUT_POST, "fotos"));
$var_cod_revision = filter_input(INPUT_POST, "cod_revision");
$var_fecha = filter_input(INPUT_POST, "fecha");

if (empty($var_cod_revision)) {/// en el caso de ser una actualizacion, no se añade la revision, sino que se actualizará
    $var_cod_revision = obtener_Cod("revisiones");
    $sqlRevision = "INSERT INTO revisiones (cod_revision,id_maquina,id_usuario,fecha,descripcion) "
            . "values (" . $var_cod_revision . "," . $var_cod_maquina . "," . $var_cod_usuario . ",'" . $var_fecha . "','" . $var_descripcion . "');";
} else {
    $sqlRevision = "UPDATE revisiones set fecha='" . $var_fecha . "',descripcion='" . $var_descripcion . "' where cod_revision=" . $var_cod_revision;
}



/*
 * Elimino todos las fotos actuales de esta revision, para luego añadir todas. De esta manera evito el tener que comprobar que es una actualizacion
 */

$sqlDelete = "delete from revisiones_fotos where id_revision=" . $var_cod_revision;
$conexion->query($sqlDelete);
// var_dump(count($var_array_fotos));
$bandera_correcto = true;
if(count($var_array_fotos)>0){

    foreach ($var_array_fotos as $foto) {

        //[0]=>nombre_imagen
        //[1]=>texto_descriptivo_imagen
        if ($foto[0] <> "default_image.jpg" && $foto[1] <> "") {
    
            // Creacion del sql para insertar    
            $sql = "insert into revisiones_fotos (id_revision,foto,descripcion_foto) values (" . $var_cod_revision . ",'" . $foto[0] . "','" . $foto[1] . "')";
            
            // Mover la imagen de la carpeta temporal(temp)
    
            $path_fotos_maquina = "fotos/revisiones/" . $var_cod_revision;
            if (!file_exists($path_fotos_maquina)) {            
                mkdir($path_fotos_maquina, 0777, true);
            }
    
            /*
             * La manera de comprobar de que es una foto ya subido
             */
           
            if (file_exists("temp/" . $foto[0])) {
                copy("temp/" . $foto[0], $path_fotos_maquina . "/" . $foto[0]);
                unlink("temp/" . $foto[0]);
            }
    
            $conexion->query($sql);
        }else{
            echo "Asegúrese de introducir una descripción a las fotos.";
            $bandera_correcto = false;
            exit;
        }

        
        
    }

}

$conexion->query($sqlRevision);
echo 0;
?>