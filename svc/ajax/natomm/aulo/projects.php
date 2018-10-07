<?php
/**
 * NATO MILMED COE FPS Tier 1.
 * projects.php | produces a JSON list of #id, shortname | & PUT by Modal window
 * @start 2018-07-24 nr
 * @lastmod 2018-08-28 nr
 */

header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['cmd']) ) $_REQUEST['cmd']='list';
//
if( !isset($_REQUEST['yr']) ) $_REQUEST['yr']=date('y');

//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiProjectDAO.php' ) ;
	$projectDAO = new \Nato\Coemed\CoeMedFiProjectDAO( $yc ) ;
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPOWDAO.php' ) ;
	$powDAO = new \Nato\Coemed\CoeMedFiPOWDAO( $yc ) ;	
//

//
switch( $_REQUEST['cmd'] ){

	//
	case 'list':
				
				$lr = new \Jazz\Core\JazzDAOList( $yc,
												$projectDAO,
												[
													['Active', '=', 1 ],
												], 
												[ 
													['ShortName',1] 
												]
											);
											
				$li = $lr->getRowsArray( [ 'ID', 'ShortName', 'EventID' ] ) ;
				
				die( '{"e":0,"c":'.count($li).',"list":'.json_encode($li).'}') ;
				//
				
				break;

	//
	case 'get':
	
				die('{"e":1927}');  // Wont Run Now... | used to inform caller don't try it this way :)

	//
	case 'new':
	case 'put':
				
				if( !isset($_REQUEST['yr']) ) die('{"e":1931,"p":"yr"}'); // missing 'yr'
				if( (int)$_REQUEST['yr'] <10 ) die('{"e":1930,"p":"yr"}'); // invalid value
				
				//if( !isset($_REQUEST['pow']) ) die('{"e":1931,"p":"pow"}'); // missing 'pow'
				//if( (int)$_REQUEST['pow'] <1 ) die('{"e":1930,"p":"pow"}'); // invalid value
				
				$pow  = 0 ;
				$brid = 0 ;
				
				if( isset($_REQUEST['pow']) )
				if( (int)$_REQUEST['pow'] >0 ){
				  //
				  $pow = (int)$_REQUEST['pow'] ;
				  
				  $powDAO->clearMe() ;
				  $powDAO->setID( $pow ) ;
				  
				  $powDAO->loadMe() ;
				  
				  $brid = (int)$powDAO->getValue('BranchID') ;
				}
				
				if( !isset($_REQUEST['prj']) ) die('{"e":1931,"p":"prj"}'); // missing 'prj'
				
				$prj  = lossSQLinjectionChars( trim($_REQUEST['prj']) ) ;
				
				if( strlen($prj) <1 ) die('{"e":1930,"p":"prj"}'); // invalid value
				
				$evt  = 0;
				
				if( isset($_REQUEST['evt']) ) if( (int)$_REQUEST['evt']>0 )  $evt = (int)$_REQUEST['evt'] ;
				
				$projectDAO->clearMe();
				
				$projectDAO->setValue( 'Active',1) ;
				
				$projectDAO->setValue( 'ShortName',$prj ) ;
				$projectDAO->setValue( 'EventID',(int)$evt ) ;
				
				$projectDAO->loadMe() ;
	
				//
				if( $projectDAO->getID() >0 ){
					//
					echo '{"e":1938,"id":'.(int)$projectDAO->getID().',"msg":"insert failed: data duplication"}';
				}
				else{
					
					//
					$projectDAO->setItemTypeID( 550 ) ; // Project
					$projectDAO->setValue( 'SubClassID', 201 ) ; // Event
					
					$projectDAO->setValue( 'PowID', $pow ) ;
					$projectDAO->setValue( 'BranchID', $brid ) ;
					
					$projectDAO->insertMe() ;
					
					$p_ID = (int)$projectDAO->getID() ;
					
					//
					$lr = new \Jazz\Core\JazzDAOList( $yc,
												$projectDAO,
												[
													['Active', '=', 1 ],
												], 
												[ 
													['ShortName',1] 
												]
											);
											
					$li = $lr->getRowsArray( [ 'ID', 'ShortName' ] ) ;
				
					$p_cbx = array();
					foreach( $li as $p ) $p_cbx[] = array( $p['ID'], $p['ShortName'] ) ;
					
					
					echo '{"e":0,"id":'.$p_ID.',"prj":"'.$prj.'","cbx":'.json_encode($p_cbx).'}' ;  // OK
					//
					//
				}
				
				break;

	//
	default:
				die('{"e":1926}'); // transfer.cmd.unk
		
}//#switch

die('');
