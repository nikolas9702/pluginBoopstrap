<!DOCTYPE html>
<!--html lang="en" manifest="index.manifest"-->
<html></html>
<head >
    <script type="text/javascript" src="Librerias/jquery-ui-1.12.0.custom/images/jquery/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="Librerias/jquery-ui-1.12.0.custom/images/jquery/jquery-2.2.4.min.js"></script>
    <!--script type="text/javascript" src="../Librerias/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script-->
    <script type="text/javascript" src="libreriaJsNikolas.js"></script>
    <!--link rel="stylesheet" type="text/css" href="../Librerias/bootstrap-3.3.7/dist/css/bootstrap.min.css"-->
    <script type="text/javascript" src="Librerias/tether/js/tether.min.js" ></script>
    <link rel="stylesheet" type="text/css" href="Librerias/tether/js/tether.min.css">
    <script type="text/javascript" src="Librerias/bootstrap-4.0.0-alpha.5-dist/js/bootstrap.min.js" ></script>
    <link rel="stylesheet" type="text/css" href="Librerias/bootstrap-4.0.0-alpha.5-dist/css/bootstrap.min.css">
    <meta charset="UTF-8" content="no-cache">
    <title>nikolas</title>
    <script type="text/javascript">
        $(document).ready(()=> {
           $(".formulario").convertirBoostrapp4(correo); 
        });
    </script>
</head>
<body>
    
    <textarea class="formulario" >
        <?php 
            echo '{"input" : {"type":"text","class":"form-control","value":"nikolas","style":{"background": "#fff"}}}' ;
        ?>
    </textarea>
    <textarea class="formulario" >
        <?php 
            echo '{"textarea" : {"type":"text","value":"nikolas","style":{"background": "#fff" , "color": "red"}}}' ;
        ?>
    </textarea>
    <textarea class="formulario" >
        <?php 
            echo '{"input" : {"type":"password","value":"nikolas","style":{"background": "#fff" , "color": "red"}}}' ;
        ?>
    </textarea>
    <div id="correo" ></div>
    
    <!--textarea id="formulario" > 
        <?php echo '{
           "tipo":"valoress",
           "arreglo":
           {
           "valores":"jsonSegundo",
           "jsonui":"jsonesPraticos",
            "arreglo":
            {
            "valores":"jsontercero1",
            "jsonui":"jsonesPraticosTercero",
            "valores":"jsontercero2",
            "jsonui":"jsonesPraticosTercero1",
            "valores":"jsontercero3",
            "jsonui":"jsonesPraticosTercero2",
            "valores":"jsontercero4",
            "jsonui":"jsonesPraticosTercero3",
            "valores":"jsontercero5",
            "jsonui":"jsonesPraticosTercero4",
            "valores":"jsontercero6",
            "jsonui":"jsonesPraticosTercero5",
            "valores":"jsontercero7",
            "jsonui":"jsonesPraticosTercero6",
            "valores":"jsontercero8",
            "jsonui":"jsonesPraticosTercero7"
            }
           },
           "tips":"valoress11",
           "tipo234":"valoress22"
           }'; ?>
    </textarea-->

</body>
</html>