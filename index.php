<!DOCTYPE html>
<!--html lang="en" manifest="index.manifest"-->
<html>
<head >
	<script type="text/javascript" src="../Librerias/jquery-ui-1.12.0.custom/images/jquery/jquery-2.2.4.min.js"></script>
	<!--script type="text/javascript" src="../Librerias/bootstrap-3.3.7/dist/js/bootstrap.min.js"></script-->
	<script type="text/javascript" src="../libreriaJsNikolas.js"></script>
	<!--link rel="stylesheet" type="text/css" href="../Librerias/bootstrap-3.3.7/dist/css/bootstrap.min.css"-->
	<script type="text/javascript" src="../Librerias/tether/js/tether.min.js" ></script>
	<link rel="stylesheet" type="text/css" href="../Librerias/tether/js/tether.min.css">
	<script type="text/javascript" src="../Librerias/bootstrap-4.0.0-alpha.5-dist/js/bootstrap.min.js" ></script>
	<link rel="stylesheet" type="text/css" href="../Librerias/bootstrap-4.0.0-alpha.5-dist/css/bootstrap.min.css">
  <script type="text/javascript" src="//rf.revolvermaps.com/0/0/8.js?i=52q7fj4fiij&amp;m=7&amp;s=220&amp;c=ff0000&amp;cr1=ffffff&amp;f=arial&amp;l=33&amp;bv=0&amp;rs=100&amp;as=100" async="async"></script>
	<meta charset="UTF-8" content="no-cache">
	<title>nikolas</title>
	<script type="text/javascript">
		$(document).ready(()=>{
			var mostrarMensaje = 0; 
			/*$(".formulario").click((i)=>{
				console.log(this);
				$(this).valida();
				$(i).valida();
			});*/
			//$(".formulario").formatear();
			$(".formulario").convertirBoostrapp4();

			function probarInternet () {
				$("#error").validarConexion();
			}
			setInterval(probarInternet,1000);
			$("#cerrar_mensaje_conexion").click(()=>{
				$("#error").validarConexion(1);
			});
		});


	</script>
</head>
<body>

<div id="error" >hola nikolas </div>


<form>
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control formulario " id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control formulario " id="exampleInputPassword1" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="exampleSelect1">Example select</label>
    <select class="form-control formulario " id="exampleSelect1">
      <option>1</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
    </select>
  </div>
  <div class="form-group">
    <label for="exampleSelect2">Example multiple select</label>
    <select multiple class="form-control formulario " id="exampleSelect2">
      <option>1</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
    </select>
  </div>
  <div class="form-group">
    <label for="exampleTextarea">Example textarea</label>
    <textarea class="form-control formulario " id="exampleTextarea" rows="3"></textarea>
  </div>
  <div class="form-group">
    <label for="exampleInputFile">File input</label>
    <input type="file" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
    <small id="fileHelp" class="form-text text-muted">This is some placeholder block-level help text for the above input. It's a bit lighter and easily wraps to a new line.</small>
  </div>
  <fieldset class="form-group">
    <legend>Radio buttons</legend>
    <div class="form-check">
      <label class="form-check-label">
        <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios1" value="option1" checked>
        Option one is this and that&mdash;be sure to include why it's great
      </label>
    </div>
    <div class="form-check">
    <label class="form-check-label">
        <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios2" value="option2">
        Option two can be something else and selecting it will deselect option one
      </label>
    </div>
    <div class="form-check disabled">
    <label class="form-check-label">
        <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios3" value="option3" disabled>
        Option three is disabled
      </label>
    </div>
  </fieldset>
  <div class="form-check">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input">
      Check me out
    </label>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
</body>
</html>