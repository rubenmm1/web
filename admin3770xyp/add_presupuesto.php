<?php
@session_start();
if (!isset ($_SESSION['cod_usuario'])) {
	header("location: index.php");
}

include ("../conexion.php");
include ("../functions.php");

//data: "cod_cliente="+cod_cliente+"&razon_social="+razon_social+
//"&nombre="+nombre+"&cif="+cif+"&direccion="+direccion+"&telefono="+telefono+
//"&email="+email+"&poblacion="+poblacion+"&provincia="+provincia+"&login="+login+"&pass="+pass+"&pass2="+pass2,

$var_cod_cliente = $_POST['codigo_cliente'];
$var_iva_cliente = $_POST['iva_cliente'];
$var_re_cliente = $_POST['re_cliente'];
$var_dto_tarifa = $_POST['dto_tarifa'];
$var_cod_serie = $_POST['cod_serie'];
$var_observaciones = htmlspecialchars($_POST['observaciones'], ENT_QUOTES | ENT_HTML401, 'UTF-8');


$codigo_presupuesto_enviado = $_POST['cod_presupuesto'];


if ($codigo_presupuesto_enviado == "0") {/// Nuevo presupuesto
	$NuevoPresupuesto = true;
	$accion = "creado";
} else {
	$NuevoPresupuesto = false;
	$accion = "actualizado";
}


if ($NuevoPresupuesto) {/// Nuevo presupuesto

	$codigo_presupuesto = obtener_series($var_cod_serie, "P");


} else { /// Modificar presupuesto

	$codigo_presupuesto = $codigo_presupuesto_enviado;

	$sql = "delete from presupuestos_lineas where id_presupuesto='" . $codigo_presupuesto . "'";
	$conexion->query($sql);

}


$articulos = json_decode($_POST['array_articulos']);
$i = 0;

$importeNeto = 0;
$importeConDesc = 0;

foreach ($articulos as $articulo) {
	//$i =>cod_articulo
	//[0]=>cantidad
	//[1]=>precio
	//[2]=>descripcion
	//[3]=>descuento linea

	if ($articulo[0] <> "") {
		//echo $i."=>".$articulo[0]."-".$articulo[1]."-".$articulo[2]."*";
		$subtotal = $articulo[1] * $articulo[0];
		$subtotal = $subtotal - ($subtotal * $articulo[3] / 100);
		$sql = "insert into presupuestos_lineas (id_presupuesto,id_articulo,cantidad,descripcion,precio_unitario,subtotal,desc_linea)
			values ('" . $codigo_presupuesto . "','" . $i . "'," . $articulo[0] . ",'" . $articulo[2] . "'," . $articulo[1] . ",$subtotal," . $articulo[3] . ")";
		$conexion->query($sql);
		$importeNeto = $importeNeto + $subtotal;
		//echo $sql."<br />";
	}

	$i++;
}

$dto = $var_dto_tarifa;// variable que contiene el % para descontar;
// echo $dto;
$importeConDesc = $importeNeto - ($importeNeto * $dto / 100);

if ($NuevoPresupuesto) {/// Nuevo presupuesto
	$sql = "insert into presupuestos (cod_presupuesto,fecha,id_cliente,importe_neto,importe_descuento,iva,observaciones,tarifa,cod_serie,re)
	values ('" . $codigo_presupuesto . "',NOW()," . $var_cod_cliente . "," . $importeNeto . "," . $importeConDesc . ",'" . $var_iva_cliente . "','" . $var_observaciones . "'," . $var_dto_tarifa . "," . $var_cod_serie . "," . $var_re_cliente . ")";
	// echo "<script>console.log('sql to insert new presupuesto');</script>";
} else {
	$sql = "Update presupuestos SET
	id_cliente=" . $var_cod_cliente . ",
	importe_neto=" . $importeNeto . ",
	importe_descuento=" . $importeConDesc . ",
	iva='" . $var_iva_cliente . "',
	observaciones='" . $var_observaciones . "',
	tarifa='" . $var_dto_tarifa . "'
	where cod_presupuesto='" . $codigo_presupuesto . "'";
}
$conexion->query($sql);

echo $sql;

// echo $codigo_presupuesto."#####".$accion;
