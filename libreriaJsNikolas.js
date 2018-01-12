(function ($) {
    var mostrarMensaje = 0;
    $.fn.extend({
        validar: function () {
            this.each(function () {

            });
        },
        formatear: function () {
            this.each(function () {//obtiene todos los valores y se re corren 
                var $this = $(this);
                $this.focus(function () {//se valida que tenga foco el valor que se va validar 
                    $this.keypress(function () {//cada vez se de da click se valida el contenido 
                        console.log($(this).val());
                    });
                });
            });
        },
        convertirBoostrapp4: function (idElemento) {
            $(idElemento).html("");
            this.each(function () {
                console.log(this)
                var elemento = $(this);
                //elemento.css('display', 'none');
                //elemento.ProcesarJson(elemento.val());
                //elemento.procesamos();
                //$(this).val().fn.ProcesarJson();
                var parteCompuestas = EstilosCampos(elemento.val());
                $(idElemento).append(parteCompuestas[0]+ProcesarJson(elemento.val(), 0) + parteCompuestas[1]);
            });
        },
        validarConexion: function (ocultar) {//Se da un div para colocar el mensage
            if (ocultar == 1)
                mostrarMensaje = 1;
            var divMensaje = $("#div_mensaje_error");
            var conexion = navigator.onLine;
            if (divMensaje.length == 0)
            {
                $(this).html("<div id='div_mensaje_error' class='form-group alert'></div>");
            }


            if (conexion == false) {
                mostrarMensaje = 0
            }
            else if (mostrarMensaje == 1) {
                return false;
            }
            divMensaje.html("<a id='cerrar_mensaje_conexion' class='close' data-dismiss='alert' aria-label='close'>&times;</a>");
            if (conexion == false) {
                divMensaje.removeClass("alert-success");
                divMensaje.addClass("alert-danger");
                divMensaje.append("Sin Conexion");
            }
            else if (conexion == true) {
                divMensaje.removeClass("alert-danger");
                divMensaje.addClass("alert-success");
                divMensaje.append("Con Conexion");
            }
        },
        /*ProcesarJson: function (JSON) {
         console.log(JSON);
         var json = jQuery.parseJSON(JSON);
         $.map(json, function(item, index) {
         console.log("item"+item);
         if (typeof item == "object" ) {
         console.log(this);
         $(item).ProcesarJson(item);
         }
         console.log("index"+index);
         });
         },*/
        procesamos: function () {
            console.log("valores");
        }
    });
    function ProcesarJson(JSON, tipo) {
        var valor = "";
        var json = (tipo == 0) ? jQuery.parseJSON(JSON) : JSON;
        $.map(json, function (value, key) {
            console.log(value + " : " + key);
            if (typeof value == "object") {
                valor += ProcesarJson(value, 1);
            }
            else
            {
                valor += key + "='" + value + "' ";
            }
        });
        return valor;
    }
    function EstilosCampos(JSON) {
        var json = jQuery.parseJSON(JSON) ;
        var tipo ;
        var arregloPartes = {} ;
        $.map(json, function (value, key) {
            tipo = key;
        });
        
        switch (tipo)
        {
            case "input":
            case "number":
                arregloPartes[0] = "<"+tipo+" ";
                arregloPartes[1] = "/>";
                break;
            case "div":
            case "textarea":
                arregloPartes[0] = "<"+tipo+" ";
                arregloPartes[1] = "></"+tipo+">";
                break;
            default :
                break;
        }
        return arregloPartes;
    }
})(jQuery)