<?php
/**
 * NATO MILMED COE FPS Tier 1.
 * pows.php | produces a JSON list of #id, shortname | & PUT by Modal window
 * @start 2018-07-23 nr
 * @lastmod 2018-08-23 nr
 */

header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['cmd']) ) $_REQUEST['cmd']='list';
//
if( !isset($_REQUEST['yr']) ) $_REQUEST['yr']=date('y');

//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPOWDAO.php' ) ;
	$powDAO = new \Nato\Coemed\CoeMedFiPOWDAO( $yc ) ;
//

//
switch( $_REQUEST['cmd'] ){

	//
	case 'list':
				
				$lr = new \Jazz\Core\JazzDAOList( $yc,
												$powDAO,
												[
													['Active', '=', 1 ],
													['Year', '=', (int)$_REQUEST['yr'] ],
												], 
												[ 
													['ShortName',1] 
												]
											);
											
				$li = $lr->getRowsArray( [ 'ID', 'ShortName' ] ) ;
				
				die( '{"e":0,"c":'.count($li).',"list":'.json_encode($li).'}') ;
				//
				
				break;

	//
	case 'get':
	
				die('{"e":1927}');  // Wont Run Now... | used to inform caller don't try it this way :)

	//
	case 'new':
	case 'put':
	
				die('{"e":1927}');  // Wont Run Now... | used to inform caller don't try it this way :)

	//
	default:
				die('{"e":1926}'); // transfer.cmd.unk
		
}//#switch
