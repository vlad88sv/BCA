<?php

// Genera un simple contenedor JavaScript
function JS($script){
    return "<script type='text/javascript'>".$script."</script>";
}

// Genera un simple contenedor JavaScript para JQuery ON DOM READY
function JS_onload($script){
    return "<script type='text/javascript'>$(document).ready(function(){".$script."});</script>";
}

// Genera un pequeño GROWL
function JS_growl($mensaje){
    return "$.jGrowl('".addslashes($mensaje)."', { sticky: true });";
}

function suerte($una, $dos){
    if (rand(0,1)) {
        return $una;
    } else {
        return $dos;
    }
}

function Truncar($cadena, $largo) {
    if (strlen($cadena) > $largo) {
        $cadena = substr($cadena,0,($largo -3));
            $cadena .= '...';
    }
    return $cadena;
}


function _F_form_cache($campo)
{
    if (!isset($_POST))
        return '';
    if (array_key_exists($campo, $_POST))
    {
        return $_POST[$campo];
    }
    else
    {
        return '';
    }
}

// http://www.linuxjournal.com/article/9585
function validcorreo($correo)
{
   $isValid = true;
   $atIndex = strrpos($correo, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($correo, $atIndex+1);
      $local = substr($correo, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}


function scaleImage($x,$y,$cx,$cy) {
    //Set the default NEW values to be the old, in case it doesn't even need scaling
    list($nx,$ny)=array($x,$y);

    //If image is generally smaller, don't even bother
    if ($x>=$cx || $y>=$cx) {

        //Work out ratios
        if ($x>0) $rx=$cx/$x;
        if ($y>0) $ry=$cy/$y;

        //Use the lowest ratio, to ensure we don't go over the wanted image size
        if ($rx>$ry) {
            $r=$ry;
        } else {
            $r=$rx;
        }

        //Calculate the new size based on the chosen ratio
        $nx=intval($x*$r);
        $ny=intval($y*$r);
    }

    //Return the results
    return array($nx,$ny);
}
function Imagen__Redimenzionar($Origen, $Ancho = 640, $Alto = 480)
{
    $im=new Imagick($Origen);

    $im->setImageColorspace(255);
    $im->setCompression(Imagick::COMPRESSION_JPEG);
    $im->setCompressionQuality(80);
    $im->setImageFormat('jpeg');

    list($newX,$newY)=scaleImage($im->getImageWidth(),$im->getImageHeight(),$Ancho,$Alto);
    $im->scaleImage($newX,$newY,true);
    return $im->writeImage($Origen);
}

/*
 * Imagen__CrearMiniatura()
 * Crea una versión reducida de la imagen en $Origen
*/
function Imagen__CrearMiniatura($Origen, $Destino, $Ancho = 100, $Alto = 100)
{
    $im=new Imagick($Origen);

    $im->setImageColorspace(255);
    $im->setCompression(Imagick::COMPRESSION_JPEG);
    $im->setCompressionQuality(80);
    $im->setImageFormat('jpeg');

    list($newX,$newY)=scaleImage($im->getImageWidth(),$im->getImageHeight(),$Ancho,$Alto);
    $im->thumbnailImage($newX,$newY,false);
    return $im->writeImage($Destino);
}

function SEO($URL,$prefijo='.html'){
    $URL = preg_replace("`\[.*\]`U","",$URL);
    $URL = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$URL);
    $URL = htmlentities($URL, ENT_COMPAT, 'utf-8');
    $URL = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $URL );
    $URL = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $URL);
    return strtolower(trim($URL, '-')).$prefijo;
}
// http://www.webcheatsheet.com/PHP/get_current_page_url.php
// Obtiene la URL actual, $stripArgs determina si eliminar la parte dinamica de la URL
function curPageURL($stripArgs=false) {
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 if ($stripArgs) {$pageURL = preg_replace("/\?.*/", "",$pageURL);}
 return $pageURL;
}

// Wrapper de envío de correo electrónico. HTML/utf-8
function correo($para, $asunto, $mensaje)
{
    $headers = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: '. PROY_NOMBRE .' <'. PROY_MAIL_POSTMASTER . ">\r\n";
    $mensaje = sprintf('<html><head><title>%s</title></head><body>%s</body>',PROY_NOMBRE,$mensaje);
    return mail($para,'=?UTF-8?B?'.base64_encode($asunto).'?=',$mensaje,$headers);
}

function correo_x_nivel($nivel, $asunto, $mensaje)
{
    $c = sprintf('SELECT correo FROM %s WHERE nivel=%s', db_prefijo.'usuarios', $nivel);
    $r = db_consultar($c);
    while ($f = mysql_fetch_array($r)) {
        correo($f['correo'],PROY_NOMBRE." - $asunto",$mensaje);
    }
}

function protegerme($solo_salir=false,$niveles_adicionales=array())
{
    if (usuario_cache('nivel') == NIVEL_administrador) return;
    if (!in_array(usuario_cache('nivel'),$niveles_adicionales))
    {
        if (!$solo_salir)
            header('Location: '. PROY_URL.'inicio?ref='.PROY_URL_ACTUAL_DINAMICA);
        ob_end_clean();
        exit;
    }
}

/*
 * Envia mensajes a la bandeja de mensajes de ID_empresa
 * $arrID_empresa = array(ID_empresa1,ID_empresa2,ID_empresaN...)
 * $mensajes[] = array(mensaje=$mensaje, tipo=['nota'|'info'|'advertencia'|'error'])
 */
function mensaje($arrID_empresa = array(), $mensajes = array())
{
    foreach($mensajes as $mensaje)
    {
        foreach($arrID_empresa as $ID_empresa)
        {
            $c = sprintf('INSERT INTO %s (ID_empresa, tipo, mensaje, leido, fecha) VALUES("%s", "%s", "%s", 0, NOW())', db_prefijo.'mensaje', $ID_empresa, $mensaje['tipo'], db_codex($mensaje['mensaje']));
            $r = db_consultar($c);
        }
    }
}
?>