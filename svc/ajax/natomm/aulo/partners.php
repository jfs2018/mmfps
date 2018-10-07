<?php
/**
 * partners.php | produces a JSON list of #id, shortname | & PUT by Modal window
 * NATO MILMED COE FPS Tier 1.
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
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPartnerDAO.php' ) ;
	$partnerDAO = new \Nato\Coemed\CoeMedFiPartnerDAO( $yc ) ;
	
	//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPocDAO.php' ) ;
	$pocDAO = new \Nato\Coemed\CoeMedFiPocDAO( $yc ) ;
	
//

//
switch( $_REQUEST['cmd'] ){

	//
	case 'list':
				
				$lr = new \Jazz\Core\JazzDAOList( $yc,
												$partnerDAO,
												[
													['Active', '=', 1 ],
													['ItemTypeID', '=', 328 ],
												], 
												[ 
													['First8',1] 
												]
											);
											
				$li = $lr->getRowsArray( [ 'ID', 'JsonData' ] ) ;
				
				$names = array();
				$j=null;
				foreach( $li as $p ){ 
					//
					$j = json_decode( $li['JsonData'],true ) ;
					
					if( is_array($j) && isset($j['n']) ) $names[] = array( 'ID'=>(int)$p['ID'], 'n'=>$j['n'] ) ;
					
					$j = null;
				}
				
				die( '{"e":0,"c":'.count($names).',"list":'.json_encode($names).'}') ;
				//
				
				break;

	//
	case 'get':
	
				die('{"e":1927}');  // Wont Run Now... | used to inform caller don't try it this way :)

	//
	case 'new':
	case 'put':
				
				if( !isset($_REQUEST['cc']) ) die('{"e":1931,"p":"cc"}'); // missing 'circ'
				if( (int)$_REQUEST['cc'] <1 ) die('{"e":1930,"p":"cc"}'); // invalid value
				if( (int)$_REQUEST['cc'] >=1000 ) die('{"e":1930,"p":"cc"}'); // invalid value
				
				if( !isset($_REQUEST['pname']) ) die('{"e":1931,"p":"pname"}'); // missing 'num'
				
				$pname = ""; 
				if( isset($_REQUEST['pname']) ) $pname = lossSQLinjectionChars( $_REQUEST['pname'] ) ;

				if( strlen( trim($pname))<1 ) die('{"e":1930,"p":"pname"}'); // invalid value
				
				$partnerDAO->clearMe();
				
				$partnerDAO->setValue( 'Active',1) ;
				$partnerDAO->setValue( 'CountryCode',(int)$_REQUEST['cc'] ) ;
				$partnerDAO->setValue( 'ReqNum',(int)$rnum ) ;
				
				$partnerDAO->setValue( 'First8', substr( lossSQLinjectionChars( $pname ) ,0,8 ) ) ;
				
				$partnerDAO->setValue( 'JsonData', jsonobject4SQL( array( 'n' => $_REQUEST['pname'] ) ) ) ;
				
				$partnerDAO->loadMe();
				
				$p_ID = (int) $partnerDAO->getID() ;
				
				if( $p_ID >0 ){
					//
					echo '{"e":1938,"id":'.(int)$partnerDAO->getID().',"msg":"insert failed: data duplication"}';
				}
				else{
					//
					//
					$partnerDAO->insertMe() ;
				
					$p_ID = (int) $partnerDAO->getID() ;
				
					//
					$pocDAO->clearMe();
		
					$pocDAO->setValue( 'SubClassID', 314 ) ; // HQ
		
					$pocDAO->setValue( 'PartnerID', $p_ID ) ;
		
					$pocDAO->setValue( 'isValidNow', 1 ) ;
		
					$pocDAO->setValue( 'CountryCode', (int) $_REQUEST['cc'] ) ;
		
					$pocDAO->setValue( 'JsonDataName', jsonobject4SQL( array( 'n' => $pname ) ) ) ;
		
					$pocDAO->insertMe() ;
					//

					//
					$lr = new \Jazz\Core\JazzDAOList( $yc,
												$partnerDAO,
												[
													['Active', '=', 1 ],
													['ItemTypeID', 'IN', array( 325,328,329 ) ]
												], 
												[ 
													['First8',1]
												]
											);
											
					$li = $lr->getRowsArrayByID( [ 'ID', 'JsonData' ] ) ;
					$pa_cbx = array();
					foreach( $li as $pa ) $pa_cbx[] = array( $pa['ID'], json_decode( $pa['JsonData'] )->{'n'} ) ;
					
					
					echo '{"e":0,"id":'.$p_ID.',"pname":"'.$pname.'","cbx":'.json_encode($pa_cbx).'}' ;  // OK
					//
					//
				}
				
				break;
	//
	default:
				die('{"e":1926}'); // transfer.cmd.unk
		
}//#switch
die('');