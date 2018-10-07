<?php
/**
 * 'Active' flag switcher
 * NATO MILMED COE FPS Tier 1.
 * @start 2018-09-18 nr
 * @lastmod 2018-09-18 nr 18:24
 */

header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['id']) ) die('{"e":1931}'); // transfer.cmd.params.error.missing  | 'ID'

if( (int)$_REQUEST['id'] < 1 ) die('{"e":1930}'); // data.transfer.cmd.params.error.invalid

if( !isset($_REQUEST['a']) ) die('{"e":1931}'); // transfer.cmd.params.error.missing  | a: 0,1
if( !isset($_REQUEST['t']) ) die('{"e":1931}'); // transfer.cmd.params.error.missing  | t: type: req,pow,prj,br,pa

if( (int)$_REQUEST['a'] != 1 ) $_REQUEST['a'] = 0; // simplify

//
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRequestDAO.php' ) ;
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiProjectDAO.php' ) ;
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPOWDAO.php' ) ;
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPartnerDAO.php' ) ;
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBranchDAO.php' ) ;
 

//
 //
 switch( $_REQUEST['t'] )
 {
	case "req":
			$reqDAO = new \Nato\Coemed\CoeMedFiRequestDAO( $yc ) ;
			
			$reqDAO->clearMe() ;
			$reqDAO->setID( (int)$_REQUEST['id'] ) ;
			$reqDAO->setValue( 'Active', $_REQUEST['a'] ) ;
			$reqDAO->updateMe() ;
			
			echo '{"e":0,"id":'.(int)$_REQUEST['id'].',"a":'.(int)$_REQUEST['a'].',"t":"'.$_REQUEST['t'].'"}';
			
			break;

	case "pow":
			$powDAO = new \Nato\Coemed\CoeMedFiPOWDAO( $yc ) ;
			
			$powDAO->clearMe() ;
			$powDAO->setID( (int)$_REQUEST['id'] ) ;
			$powDAO->setValue( 'Active', $_REQUEST['a'] ) ;
			$powDAO->updateMe() ;
			
			echo '{"e":0,"id":'.(int)$_REQUEST['id'].',"a":'.(int)$_REQUEST['a'].',"t":"'.$_REQUEST['t'].'"}';
			
			break;

	case "prj":
			$prjDAO = new \Nato\Coemed\CoeMedFiProjectDAO( $yc ) ;
			
			$prjDAO->clearMe() ;
			$prjDAO->setID( (int)$_REQUEST['id'] ) ;
			$prjDAO->setValue( 'Active', $_REQUEST['a'] ) ;
			$prjDAO->updateMe() ;
			
			echo '{"e":0,"id":'.(int)$_REQUEST['id'].',"a":'.(int)$_REQUEST['a'].',"t":"'.$_REQUEST['t'].'"}';
			
			break;

	case "br":
			$branchDAO = new \Nato\Coemed\CoeMedFiBranchDAO( $yc ) ;
			
			$branchDAO->clearMe() ;
			$branchDAO->setID( (int)$_REQUEST['id'] ) ;
			$branchDAO->setValue( 'Active', $_REQUEST['a'] ) ;
			$branchDAO->updateMe() ;
			
			echo '{"e":0,"id":'.(int)$_REQUEST['id'].',"a":'.(int)$_REQUEST['a'].',"t":"'.$_REQUEST['t'].'"}';
			
			break;

	case "pa":
			$paDAO = new \Nato\Coemed\CoeMedFiPartnerDAO( $yc ) ;
			
			$paDAO->clearMe() ;
			$paDAO->setID( (int)$_REQUEST['id'] ) ;
			$paDAO->setValue( 'Active', $_REQUEST['a'] ) ;
			$paDAO->updateMe() ;
			
			echo '{"e":0,"id":'.(int)$_REQUEST['id'].',"a":'.(int)$_REQUEST['a'].',"t":"'.$_REQUEST['t'].'"}';
			
			break;

	default:
			//
			die('{"e":1930}'); // invalid
 }

//
exit(0);