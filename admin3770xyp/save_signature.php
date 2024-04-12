<?php
require_once('../conexion.php');
// Store the signature img on folder
$data = file_get_contents("php://input");
$data = json_decode($data, true);

$filteredData = substr($data['url'], strpos($data['url'], ",") + 1);
$decodedData = base64_decode($filteredData);

$fp = fopen('../firmas/revisiones/' . $data['name'], 'wb');
$ok = fwrite($fp, $decodedData);
fclose($fp);

// Update database
$date = new DateTime();
$sql = "UPDATE revisiones set firma = '" . $data['name'] . "', fecha_firma = '" . $date->format('Y-m-d H:i:s') . "' where cod_revision = " . $data['cod_rev'];
$conexion->query($sql);
var_dump($sql);

if (!$conexion->error)
    echo "success";
return;