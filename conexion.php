<?php
$hostname = "localhost";  
$username = "root"; 
$password = "";
$database_name = "db_ventas"; 

try {
  
    $pdo = new mysqli($hostname,$username,$password,$database_name);

    if($pdo)
    {
        echo "";
    }else
    {
        echo "Fallo la conexion";
    }

}catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>