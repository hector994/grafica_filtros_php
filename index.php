<?php
include_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="stylesheet" href="estilo.css">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="post">
        <label for="">Totales anuales mayores que</label>
        <input type="text" name="totales" id="totales" value="<?php echo isset($_POST['totales'])?$_POST['totales']:"";?>">
        <?php 
        $anios= "SELECT DISTINCT year(fecha) as año 
        FROM encabezado_factura WHERE year(fecha) 
        BETWEEN 2013 and 2022 order by fecha ASC";
        $ejecutar = mysqli_query($pdo,$anios);
        while ($seleccionesAnios = mysqli_fetch_array($ejecutar)) {
            echo "<label>".$seleccionesAnios[0]."</label>";
            echo "<input type='checkbox' name='anios[]' value='$seleccionesAnios[0]' id=''>";
        }

        $yearSelect = isset($_POST['anios']) ? $_POST['anios']:"";

        if(empty($yearSelect)) 
        {
            echo("No has seleccionado los anios de tu interes!");
        } 
        else 
        {
            $N = count($yearSelect);

            echo("Tu seleccionaste $N anios(s): ");
            for($i=0; $i < $N; $i++)
            {
            echo($yearSelect[$i] . ", ");
            }
        }
        ?>
        <input type="submit" value="graficar">
    </form>
<figure class="highcharts-figure">
    <div id="container"></div>
   
</figure>
</body>
</html>
<script>
    Highcharts.chart('container', {

title: {
    text: 'Empresa XYZ',
    align: 'center'
},

subtitle: {
    text: 'Total de ventas anuales de los ultimos 10 años',
    align: 'center'
},

yAxis: {
    title: {
        text: 'Ventas en $'
    }
},

xAxis: {
    accessibility: {
        rangeDescription: 'Desde 2013 al 2022'
    }
},

legend: {
    layout: 'vertical',
    align: 'right',
    verticalAlign: 'middle'
},

plotOptions: {
    series: {
        label: {
            connectorAllowed: false
        },
        pointStart: 
        <?php
        $totales = isset($_POST['totales']) ? $_POST['totales']:"";
        $yearSelect = isset($_POST['anios']) ? $_POST['anios']:"";
        include_once 'conexion.php';
        if ($totales === "") {
            echo 2013;
        } else if (!empty($yearSelect)) {
            $consulta3 = "SELECT year(fecha) as año FROM detalle_factura INNER JOIN encabezado_factura ON detalle_factura.codigo = encabezado_factura.codigo GROUP BY year(fecha) HAVING SUM(venta) >= $totales ORDER by fecha ASC";
            $ejecutar3 = mysqli_query($pdo,$consulta3);
            while ($periodos =mysqli_fetch_array($ejecutar3)) {
                $yearZ = number_format($periodos['año'],0,'','');
                if(in_array($yearZ, $yearSelect)){
                    echo number_format($yearZ,0,'','');
                    break; 
                }
            }
        } else {
            $anio = "SELECT year(fecha) as año FROM detalle_factura INNER JOIN encabezado_factura ON detalle_factura.codigo = encabezado_factura.codigo GROUP BY year(fecha) HAVING SUM(venta) >= $totales ORDER by fecha ASC LIMIT 1";
            $ejecutar = mysqli_query($pdo,$anio);
            $starting=mysqli_fetch_array($ejecutar);
            echo $anio = $starting[0];
        }
        ?>

    }
},

series: [{
    name: 'Ventas anuales',
    data: [

        <?php
        $yearSelect = isset($_POST['anios']) ? $_POST['anios']:"";
        $totales = isset($_POST['totales']) ? $_POST['totales']:"";
        include_once 'conexion.php';

        if ($totales === "") {
            
            $consulta = "SELECT sum(venta) as venta, year(fecha) as año 
            FROM detalle_factura 
            INNER JOIN encabezado_factura ON detalle_factura.codigo = encabezado_factura.codigo 
            GROUP BY year(fecha) ORDER by fecha ASC";
            // ORDER BY año ASC";
            $ejecutar = mysqli_query($pdo,$consulta);
            while ($data=mysqli_fetch_array($ejecutar)) {
                $ventastotales = number_format($data[0],2,'.','');
                echo $ventastotales.",";
            }
        
        } else {
            
            $consulta = "SELECT sum(venta) as venta, year(fecha) as año 
            FROM detalle_factura 
            INNER JOIN encabezado_factura ON detalle_factura.codigo = encabezado_factura.codigo 
            GROUP BY year(fecha)
            HAVING SUM(venta) >= $totales ORDER by fecha ASC";
            //ORDER BY año ASC";

            $consulta2 = "SELECT year(fecha) as año FROM detalle_factura 
            INNER JOIN encabezado_factura ON detalle_factura.codigo = encabezado_factura.codigo 
            GROUP BY year(fecha) HAVING SUM(venta) >= $totales ORDER by fecha ASC";

            $ejecutar = mysqli_query($pdo,$consulta);
            $ejecutar2 = mysqli_query($pdo,$consulta2);
            
            while ($data=mysqli_fetch_array($ejecutar)) {
                
                $ventastotales = number_format($data['venta'],2,'.','');                
                $yearZ2 = number_format($data['año'],0,'','');

                if(empty($yearSelect)) 
                {
                    echo $ventastotales.",";
                } else {
                    while ($periodos = mysqli_fetch_array($ejecutar2)) {
                        $yearZ = number_format($periodos['año'],0,'','');
                        if(in_array($yearZ, $yearSelect)){
                            echo $ventastotales.","; 
                        }
                        break;
                    }

                }
            }
        
        } 

        ?>

    ]
}],

responsive: {
    rules: [{
        condition: {
            maxWidth: 500
        },
        chartOptions: {
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
            }
        }
    }]
}

});

</script>