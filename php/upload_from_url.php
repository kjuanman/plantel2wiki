<?PHP

error_reporting(E_ERROR|E_CORE_ERROR|E_ALL|E_COMPILE_ERROR);
ini_set('display_errors', 'On');

require_once( 'common.php' );
require_once( 'peachy/Init.php' );

//header('Content-type: text/plain; charset=utf-8');
header('Content-type: application/json; charset=utf-8');

function do_direct_upload ( $url , $new_name , $desc ) {
	global $project , $source , $tusc_user , $o , $peachy_error ;
	$language = 'commons' ;
	$cwd = getcwd() ;
	
	do {
		$temp_name = tempnam ( "/data/project/magnustools/tmp" , "f2c_" ) ;
		$temp = @fopen ( $temp_name , "w" ) ;
	} while ( $temp === false ) ;
	$temp_dir = $temp_name . "-dir" ;
	mkdir ( $temp_dir ) ;
	
	$short_file = str_replace ( " " , "_" , $new_name ) ;
	$short_file = str_replace ( ":" , "_" , $short_file ) ;
	$short_file = str_replace ( "/" , "_" , $short_file ) ;
	$short_file = str_replace ( "\\" , "_" , $short_file ) ;
	$short_file = str_replace ( "'" , "" , $short_file ) ;
	
	$newfile = $temp_dir . "/" . $short_file ;
	if ( !copy($url, $newfile) ) {
		$o['note'] = 'Download from source failed' ;
		rmdir ( $temp_dir ) ;
		unlink ( $temp_name ) ;
		return false ;
	}

//!!!!
	// duplicate check
	$size = filesize ( $newfile ) ;
	$sha1 = sha1_file ( $newfile ) ;
	$url = "http://$language.$project.org/w/api.php?action=query&generator=allimages&gailimit=1&gaiminsize=$size&gaimaxsize=$size&gaisha1=$sha1&prop=imageinfo&format=php" ;
	$d = unserialize ( file_get_contents ( $url ) ) ;
	if ( isset ( $d['query'] ) ) { // Does exist
		$d = array_shift ( $d['query']['pages'] ) ;
		$o['note'] = "Exists as <a target='_blank' href='http://commons.wikimedia.org/wiki/" . $d['title'] . "'>" . $d['title'] . "</a>." ;
		unlink ( $newfile ) ;
		rmdir ( $temp_dir ) ;
		unlink ( $temp_name ) ;
		return false ;
	}
//!!!!
	
	$desc = trim ( $desc ) ;

	$newname = $short_file ;
	$comment = "Transferred from $source by [[User:$tusc_user]]" ;

	$bot_name = "File Upload Bot (Magnus Manske)" ;
	$bot_pass = trim ( file_get_contents ( "/data/project/magnustools/fub_key.txt" ) ) ;
	
	$o['upload'] = uploadFileViaAPI ( $bot_name , $bot_pass , $newfile , $newname , $desc , $comment ) ;

	
/*	
	$peach = Peachy::newWiki( null , $bot_name , $bot_pass , 'https://commons.wikimedia.org/w/api.php' );
	$pi = $peach->initImage( $newname );
	$ret = $pi->upload ( $newfile , $desc , $comment ) ;
	$o['peachy'] = $peachy_error ;
*/
	// Cleanup
	unlink ( $newfile ) ;
	rmdir ( $temp_dir ) ;
	fclose ( $temp ) ;
	unlink ( $temp_name ) ;

	return true ;
}

# Prep
$tusc_user = get_request ( "tusc_user" , '' ) ;
$tusc_password = get_request ( "tusc_password" , '' ) ;
$project = "wikimedia" ;
$url = get_request ( 'url' , '' ) ;
$new_name = get_request ( 'new_name' , '' ) ;
$desc = get_request ( 'desc' , '' ) ;
$source = get_request ( 'source' , $url ) ;

$o = array() ;
$o['status'] = 'OK' ;

if ( !verify_tusc () ) {
	$o['status'] = "TUSC verification failed : $tusc_error" ;
	print json_encode ( $o ) ;
	exit () ;
}


if ( ! do_direct_upload ( $url , $new_name , $desc ) ) {
	$a = array() ;
/*	foreach ( $o['peachy'] AS $e ) {
		if ( stristr ( $e , ' error' ) ) $a[] = $e ;
	}*/
	if ( count ( $a ) == 0 ) $a[] = 'Unknown upload error' ;
	$o['status'] = implode ( "<br/>" , $a ) ;
}
//unset ( $o['peachy'] ) ;

print json_encode ( $o ) ;
myflush() ;
