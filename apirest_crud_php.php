<?php

$host="localhost";
$usuario="root";
$password="";
$basededatos="api";


$conexion = new mysqli($host, $usuario, $password, $basededatos);

if($conexion->connect_error){
    die("Error al establecer la conexión". $conexion->connect_error);
}


header("Content-Type: application/json");
$metodo=$_SERVER['REQUEST_METHOD'];
// print_r($metodo);

$path = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';
$buscarId = explode('/', $path);

$id= ($path!='/') ? end($buscarId):null;


switch ($metodo){
    //para usuarios
    //consulta de tipo SELECT
    case 'GET':
        consulta($conexion, $id);
        break;
    //consulta de tipo INSERT
    case 'POST':
        insertar($conexion);
        break;
    //consulta de tipo UPDATE
    case 'PUT':
        actualizar($conexion, $id);
        break;
    //consulta de tipo DELETE
    case 'DELETE':
        borrar($conexion, $id);
        break;
    default:
        echo "Método no permitido";
        break;
}

function consulta($conexion, $id){
    //si el id es nulo mostrará todos los usuarios y si no lo es, mostrará solo el usuario con ese id
    $sql = ($id==null) ? "SELECT * FROM usuarios" : "SELECT * FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        $dato = array();
        while($fila = $resultado->fetch_assoc()){
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
}

function insertar($conexion){

    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conexion->query($sql);

    if($resultado){
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else{
        echo json_encode(array('error'=>'Error al crear usuario'));
    }

}

function borrar($conexion, $id){

    $sql = "DELETE FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        echo json_encode(array('mensaje'=>'Usuario eliminado'));
    } else{
        echo json_encode(array('error'=>'Error al eliminar usuario'));
    }

}

function actualizar($conexion, $id){

    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion->query($sql);

    if($resultado){
        echo json_encode(array('mensaje'=>'Usuario actualizado'));
    } else{
        echo json_encode(array('error'=>'Error al actualizar usuario'));
    }
}


?>