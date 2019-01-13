<?php
#
#  Plantel to Wiki Converter
#  Convierte un plantel con el formato de soloascenso.com en un 
#  formato para Wikipedia-ES
#
#  (c) 2015 jmmuguerza
#
############################################################################
#
#  Based on:
#  HTML to Wiki Converter - tables
#  converts the HTML table tags into their wiki equivalents,
#  which were developed by Magnus Manske and are used in MediaWiki
#
#  Copyright (C) 2004 Borislav Manolov
#
#  This program is free software; you can redistribute it and/or
#  modify it under the terms of the GNU General Public License
#  as published by the Free Software Foundation; either version 2
#  of the License, or (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  Author: Borislav Manolov <b.manolov at gmail.com>
#          http://purl.org/NET/borislav
#############################################################################
#  Adopted to this mirror location and slightly enhanced ("otherhtml" stuff)
#  by Magnus Manske

error_reporting ( E_ALL ) ;
@set_time_limit ( 20*60 ) ; # Time limit 20min

include_once ( "php/wikiquery.php") ;

function getvalue($array, $key){
  return $array[$key];
}

function plantel2wiki($str, $nacion = true, $links = true, $edad = true) {

# @param $nacion    Show country of birth
# @param $links     Show internal links
# @param $edad    Show age

    $output = "{{Equipo de fútbol inicio|procedencia=no|edad=";
    if ( $edad ) 
	    $output .= "si";
    else
	    $output .= "no";
    $output .= "|fondo=grey|texto=black}}\n";
    
    $lines = explode("\n", $str);
//    $output .= $lines[0];
    array_shift( $lines );

    $dt = array_pop( $lines );
    while ( strpos($dt,"DT") === false )
	$dt = array_pop( $lines );
    
    $dt = array_pop( $lines );    
    $dtlast = trim( ucwords(mb_strtolower( getvalue( explode(',', $dt), 0), 'UTF-8' )) );
    $dtname = trim( ucwords(mb_strtolower( getvalue( explode(',', $dt), 1), 'UTF-8' )) );

    foreach ( $lines as $l ){
      if ( preg_match("/[A-Z][A-Z]+/",$l) ){
	if ( strlen($l) < 5 ){
		$position = substr( $l, 0, -1);
		if ( $nacion ) {
			$output .= "nac=Argentina|";
	    	}
		$output .= "name=[[$firstname $lastname]]";
	}
	else {
		$lastname = trim( ucwords(mb_strtolower( getvalue( explode(',', $l), 0), 'UTF-8' )) );
		$firstname = trim( ucwords(mb_strtolower( getvalue( explode(',', $l), 1) , 'UTF-8' )) );
		$output .= "{{Jugador de fútbol|";
	}
      }
      else {
	if ( preg_match("/[0-9][0-9].[0-9][0-9].[0-9][0-9][0-9][0-9]/",$l) ){
		$nacimiento = trim( str_ireplace ( "." , "|" , $l ) );
		if ( $edad && !strpos($nacimiento,"0000") ) {
                        $output .= "|edad={{edad|$nacimiento}}";
                }
		$output .= "|pos=$position}}\n";
	}
      }
    }
    $output .= "{{Equipo de fútbol fin|entrenador=";
    if ($nacion) {
	$output .= "{{ARG| }}";
    }
    $output .= "[[$dtname $dtlast]]}}";

    if (! $links){
	$output = str_ireplace ( "[[" , "" , $output );
	$output = str_ireplace ( "]]" , "" , $output );
    }
    return $output;
}

function print_form ( $html, $row_delim , $oneline , $nacion , $links , $edad , $wiki ) {
  $oneline_yes = $oneline ? "selected=\"selected\"" : '' ;
  $oneline_no = $oneline ? '' : "selected=\"selected\"" ;
  $nacion_check = $nacion ? "checked=\"checked\"" : '' ;
  $links_check = $links ? "checked=\"checked\"" : '' ;
  $edad_check = $edad ? "checked=\"checked\"" : '' ;
  
  print "<form action=\"./plantel2wiki.php\"
      method=\"post\" class='form-inline'>
<fieldset>
  <legend>Plantel2Wiki Converter<br /></legend>

  <input type=\"hidden\" name=\"post\" value=\"1\" />
";
//  <label for=\"dashes\" accesskey=\"d\" title=\"accesskey 'D'\">
//    Number of <span class=\"accesskey\">d</span>ashes used for a row:</label>
//  <input type=\"text\" id=\"dashes\" name=\"dashes\"
//    value=\"$row_delim\"
//    size=\"3\" maxlength=\"2\" class='span1'
//    title=\"Enter here the number of dashes used for a row generation, accesskey 'D'\" />
//  &nbsp;
//  <label for=\"oneline\" accesskey=\"o\" title=\"accesskey 'O'\">
//    Use <span class=\"accesskey\">o</span>ne-line position of cells in a row:</label>
//
//  <select id=\"oneline\" name=\"oneline\" class='span1'
//    title=\"Use one-line position of cells in a row, accesskey 'O'\">
//    <option value=\"1\" $oneline_yes>yes</option>
//    <option value=\"0\" $oneline_no>no</option>
//  </select>
//	 <br >
print "
	<input type=\"checkbox\" name=\"nacion\" id=\"nacion\" $nacion_check />
	<label for=\"nacion\">Agregar nacionalidad (Argentina)</label>
 &nbsp; 
	<input type=\"checkbox\" name=\"edad\" id=\"edad\" $edad_check />
	<label for=\"edad\">Agregar edad</label>
 &nbsp; 
	<input type=\"checkbox\" name=\"links\" id=\"links\" $links_check />
	<label for=\"links\">Agregar enlaces internos de jugadores y DT</label>

  <br />

  <label for=\"html\" accesskey=\"p\" title=\"accesskey 'P'\">
    <span class=\"accesskey\">P</span>lantel:</label> <br />
  <textarea id=\"html\" name=\"html\" class=\"input\"
    title=\"Pegar el plantel aquí, tecla 'P'. Ej: 
 Nombre
Puesto
Fecha Nac
Apodo
DEL MORO, FRANCO
ARQ
00.00.0000
GIACONE, FEDERICO
ARQ
00.00.0000
MARCÓ, HERNÁN
ARQ
VOUILLOUD, GERMÁN
ARQ
ÁLFEREZ, LUCAS
DEF
00.00.0000
ALFONSO, FERNANDO
DEF
ALLENDE, FEDERICO SALVADOR
DEF
00.00.0000
BELTRAN, ALEXIS EZEQUIEL
DEF
BIDAL, DANTE MARTÍN
DEF
00.00.0000
CASTRO, MAURICIO
DEF
FRANCISCO, CRISTIAN
DEF
GONZÁLEZ, MANUEL ROQUE
DEF
16.03.1991
LOZANO, MAXIMILIANO
DEF
MARTÍNEZ, JUAN MANUEL
DEF
00.00.0000
MASOERO, LUCAS
DEF
00.00.0000
MONTIVEROS, MAXIMILIANO
DEF
00.00.0000
MORALES PÁEZ, MAXIMILIANO
DEF
OLMEDO, JORGE
DEF
00.00.0000
PEINADO, LUCIANO IGNACIO
DEF
ZELAYE, JOSÉ LUIS
DEF
BARROSO, PABLO
VOL
BODNARSKY, GUILLERMO
VOL
DEBUT, MATIAS
VOL
ESCUDERO, CARLOS EMMANUEL
VOL
00.00.0000
FARÍAS, FRANCO
VOL
FERNANDEZ, EZEQUIEL
VOL
FUNES, MATíAS
VOL
JOFRE, JESUS CRISTIAN
VOL
00.00.0000
La Joya
MÉNDEZ, DIEGO
VOL
00.00.0000
El Vietnamita
MORELO, FACUNDO
VOL
OLGUÍN, ARIEL
VOL
OSORIO, FERNANDO
VOL
ROLAN, PABLO
VOL
RUIZ, JUAN
VOL
SALINAS, NEYEN
VOL
00.00.0000
TORRES ZAFFORA, MISAEL
VOL
00.00.0000
URRUTI, TOMAS
VOL
ALDERETE, JONATHAN
DEL
00.00.0000
ALFONSO, CLAUDIO
DEL
AMAYA, FEDERICO
DEL
00.00.0000
CATARUOZZOLO, FEDERICO
DEL
00.00.0000
HONGN, IVO
DEL
00.00.0000
LUCERO, MANUEL
DEL
00.00.0000
La Cobra
MANSILLA, CRISTIAN
DEL
MIGNANI, NICOLAS
DEL
NIEVA, BRIAN
DEL
00.00.0000
ROSÓN, MIGUEL
DEL
ZARAGOZA, ALEJANDRO
DEL
00.00.0000
VILLAFAÑE, ANDRÉS
DT
00.00.0000\"
    rows=\"15\" cols=\"80\" style=\"width:100%\"
    onfocus=\"if (this.value=='Ingresar aquí el plantel.')
            this.value=''\"
    >$html</textarea><br />

  <label for=\"wiki\" accesskey=\"w\" title=\"accesskey 'W'\">

    <span class=\"accesskey\">W</span>iki markup:</label> <br />
  <textarea id=\"wiki\" name=\"wiki\" class=\"output\"
    title=\"Aquí irá el texto wiki, tecla 'W'\"
    rows=\"15\" cols=\"80\" style=\"width:100%\"
    >$wiki</textarea><br />

  <input type=\"submit\" value=\"Convertir\"
    accesskey=\"x\" title=\"Convierte el plantel, tecla 'X'\" />
  <input type=\"reset\" value=\"Borrar\"
    accesskey=\"r\" title=\"Borra el formulario, tecla 'R'\" />
</fieldset>
</form>" ;
}

$html = get_request ( 'html' , '' ) ;
$row_delim = get_request ( 'dashes' , 1 ) ;
$oneline = get_request ( 'oneline' , false ) ;
$nacion = isset ( $_REQUEST['nacion'] ) ;
$edad = isset ( $_REQUEST['edad'] ) ;
$links = isset ( $_REQUEST['links'] ) ;

if ( $html != '' ) {
  $wiki = plantel2wiki($html, $nacion, $linksi, $edad) ;
} else { # Default values
  $wiki = 'Aquí se mostrará el plantel en formato wiki luego de presionar en Convertir' ;
  $html = 'Ingresar aquí el plantel.'; 
  $nacion = 1 ;
  $edad = 1 ;
  $links = 1 ;
}

print get_common_header ( "plantel2wiki.php" , "Plantel2wiki" ) ;
print_form ( $html, $row_delim , $oneline , $nacion , $links , $edad , $wiki ) ;
print "Based on html2wiki script by Borislav Manolov & Magnus Manske, released under the <a href=\"http://www.gnu.org/licenses/gpl-3.0.html\">GPL v3</a>." ;
print get_common_footer() ;
