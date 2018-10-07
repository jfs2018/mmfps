<?php
/**
// # Jazz 1v8+ :: (c) 2008..2018 Jasmine Kft. (Budapest HUN)
//
// @author rob v1.1.00-01 2016-03-21 Tavasz elso napja
// @v1.8+ | 01/11/2018
// @lastmod rob v1.8-02 | 02/20/2018
*/

//
//
$uid=0; // userID
$lvl=0; // Profile Level
$usrn=''; // UserNameAsPerson

$wstoken=null;

//
//
  include('def/yas.1st.php');
  //
  include('def/yas.cfg.php');
//
//
		//
		if( FORCE_HTTPS )
		if( !isset($_SERVER['HTTPS']) ){ header( 'Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ; exit(0); }
		//		

//		
$langcode=DEF_LANG; // default language: HU

//
include('index/context.php');
//

		//
		// bugfix 02/20/18:
		if( strcmp( site_url(), $arr_cfg['SITE_URL'] ) !=0 ){ header( 'Location: '.$arr_cfg['SITE_URL'].'/'.$arr_cfg['SITE_DIR'] ) ; exit(0); }
		//

//
//$rob 20170227 ujraszervezve innen ... az 'svc' es a 'plg' elobb legyen checkolva mint a template feloldva:
//

 //
 $arr_plugins=array(); // v1.8.0-03 : move this block _before_ 'svc' argument check

 include('index/plugins.php');

 //:v1.8
 foreach( $arr_plugins as $plix => $p )
 if( file_exists('plg/'.$p[2].'/index.php') ){ 
  //
   // plix: bugfix 01/22/18
   // 
   if( DEF_CXLG ) 
   file_put_contents( getcwd().'/logs/dbg-'.date('Y-m-d').'.log',
                    'DBG '.date('Y-m-d H:i:s').' arr_plugins['.$plix.'] | Loading plugin SQL ID#'.$p[0].' [plg/'.$p[2].'/index.php] '."\n", //
                    FILE_APPEND );
   //
   include('plg/'.$p[2].'/index.php'); 
  //
 }//#v1.8

//
if( isset($_REQUEST['svc']) ){ // ha webSerViCe-ként hívják meg  |  v1.2.3-
 //
 include('index/router.php');

 $svc_='';
 if( isset($arr_hook[ $_REQUEST['svc'] ]) ){
  //
  $svc_ = $arr_hook[ $_REQUEST['svc'] ];
  
  //
  if( strpos( $svc_,'plg/' )===false ){ // v1.8: plugins::services @lastmod 01/19/2018
   //
   include( 'svc/'.$svc_ );
  }
  else
   include( $svc_ );
  //
 }
 
 exit(0);
 
 //#SVC
}
else
if( isset($_REQUEST['lib']) ){ // @bende 06/14/2017 javaslatára ez az IF ág rendbetéve:
 //
 include('index/router.php');

 $libpath_='';
 if( isset($arr_hook[ $_REQUEST['lib'] ]) ){
  //
  $libpath_ = $arr_hook[ $_REQUEST['lib'] ];
  
  //
  include( 'lib/'.$libpath_ ); 
  //
 }
 
 exit(0);
 
 //
 //
}

//#rob 20170227 ujraszervezve innen:
//
//
 $tmpl_dir=DEF_TMPL; // default_view
 //
 $v='TPL_USER'; 
 if( $lvl >= 4 ){  $tmpl_dir=DEF_TPLA;  $v='TPL_MNGR';  }
 //
 if( isset($arr_cfg[ $v ]) ){ if( $arr_cfg[ $v ]!='' ){  $tmpl_dir = $arr_cfg[ $v ];  } }
 //
 // USER:ROLE:tmpl ? 04/04/2017
 if( isset($arr_cfg[ 'CFG_ROLE' ]) )
 if( is_array($arr_cfg[ 'CFG_ROLE' ]) )
 if( isset($arr_cfg[ 'CFG_ROLE' ][ 'tmpl' ]) ){
  //
  if( $arr_cfg[ 'CFG_ROLE' ][ 'tmpl' ] != '' )
  $tmpl_dir = $arr_cfg[ 'CFG_ROLE' ][ 'tmpl' ];
  //
  // érdemes lenne egy if_exist() futtatni...
  //
 }
//

//
 $route_file='home.php'; // ide jött át 1.2.1-re

if( !isset($_REQUEST['svc']) && !isset($_REQUEST['lib']) ){ // ELSE: default... vagyis ha nem LIB-ként kell futtatni:

 //
 if( isset($_REQUEST['do']) ){ include('index/do.php'); }

 //
// $route_file='home.php'; // - nem ide hanem föntre -
 include('index/router.php'); // v1.2.1: mindig.. nem csak ha isset(r)

 //
 //
 include('index/jshook.php'); // ehhez kell a plugins_dirpath es a template_dirpath

 
 
 //
 // 
   if( DEF_CXLG ) 
   file_put_contents( getcwd().'/logs/dbg-'.date('Y-m-d').'.log',
                    'DBG '.date('Y-m-d H:i:s').' index.php | Loading builder [view/'.$tmpl_dir.'/build.php] '."\n", //
                    FILE_APPEND );
 //
 //

 //
 //
 if( file_exists( 'view/'.$tmpl_dir.'/build.php' ) ){
  //
  include('view/'.$tmpl_dir.'/build.php');
 }
 else{
   // 
   if( DEF_CXLG ) 
   file_put_contents( getcwd().'/logs/dbg-'.date('Y-m-d').'.log',
                    'DBG '.date('Y-m-d H:i:s').' index.php | FATAL: Cannot find TMPL_DIR/build.php '."\n", //
                    FILE_APPEND );
   //
   die('FATAL: Cannot find TMPL_DIR/build.php');
   //
 }
 
}//#works-as-NON-LIB

//
mysqli_close($yc);
//
?>
