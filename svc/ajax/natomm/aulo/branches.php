<?php
/**
 * NATO MILMED COE FPS Tier 1.
 * branches.php | produces a JSON list of #id, codename (of active! branches)
 * @start 2018-07-23 nr
 * @lastmod 2018-08-23 nr
 */

header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['cmd']) ) $_REQUEST['cmd']='list';

//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBranchDAO.php' ) ;
	$branchDAO = new \Nato\Coemed\CoeMedFiBranchDAO( $yc ) ;
//

//
switch( $_REQUEST['cmd'] ){

	//
	case 'list':
	
				$lr = new \Jazz\Core\JazzDAOList( $yc,
												$branchDAO,
												[
													['Active', '=', 1 ],
												], 
												[ 
													['ID',1] 
												]
											);
											
				$li = $lr->getRowsArray( [ 'ID', 'ShortName' ] ) ;
				
				die( '{"e":0,"c":'.count($li).',"list":'.json_encode($li).'}') ;
				//
				
				break;

	//
	case 'put':
	
				die('{"e":1927}');  // Wont Run Now... | used to inform caller don't try it this way :)

	//
	default:
				die('{"e":1926}'); // transfer.cmd.unk
		
}//#switch
