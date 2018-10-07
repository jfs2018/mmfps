<?php
/**
 * NATO MILMED COE FPS Tier 1.
 * reqs.php | produces a JSON list of #id, shortname | & PUT by Modal window
 * @start 2018-07-23 nr
 * @mod 2018-09-11 nr
 * @lastmod 2018-10-02 nr
 */

header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['cmd']) ) $_REQUEST['cmd']='list';
//

//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRequestDAO.php' ) ;
	$reqDAO = new \Nato\Coemed\CoeMedFiRequestDAO( $yc ) ;
//

//
switch( $_REQUEST['cmd'] ){

	//
	case 'list':
		
				if( !isset($_REQUEST['yr']) ) $_REQUEST['yr']=date('y');
				
				$lr = new \Jazz\Core\JazzDAOList( $yc,
												$reqDAO,
												[
													['Active', '=', 1 ],
													['Year', '=', (int)$_REQUEST['yr'] ],
												], 
												[ 
													['Circle',1], ['ReqNum',1], ['SubReqNum',1], 
												]
											);
											
				$li = $lr->getRowsArray( [ 'ID', 'ShortName' ] ) ;
				
				die( '{"e":0,"c":'.count($li).',"list":'.json_encode($li).'}') ;
				//
				
				break;

	//
	case 'new':
	case 'put':
				
				if( !isset($_REQUEST['circ']) ) die('{"e":1931,"p":"circ"}'); // missing 'circ'
				if( (int)$_REQUEST['circ'] <1 ) die('{"e":1930,"p":"circ"}'); // invalid value
				
				if( !isset($_REQUEST['num']) ) die('{"e":1931,"p":"num"}'); // missing 'num'
				if( strlen( trim($_REQUEST['num']))<1 ) die('{"e":1930,"p":"num"}'); // invalid value
				
				if( !isset($_REQUEST['yr']) ) die('{"e":1931,"p":"yr"}'); // missing 'yr'
				if( (int)$_REQUEST['yr'] <10 ) die('{"e":1930,"p":"yr"}'); // invalid value
				
				if( !isset($_REQUEST['appr']) ) $_REQUEST['appr']=0.0; // 20181002
				if( (int)$_REQUEST['appr'] <0 ) die('{"e":1930,"p":"appr"}'); // invalid value
				
				$txt = null;
				if( isset($_REQUEST['txt']) ){ $txt = $_REQUEST['txt'] ; }
				else $txt="";
				
				$circ = (int)$_REQUEST['circ'] ;
				$rnum = (int)$_REQUEST['num'] ;
				$yr = (int)$_REQUEST['yr'] ;
				
				//
				 $cur = 978 ; // default EUR 2018-09-11
				
				 if( isset($_REQUEST['cur']) )
				 if( (int)$_REQUEST['cur'] >0 )
				 if( (int)$_REQUEST['cur'] != 978 ) $cur = (int)$_REQUEST['cur'] ;
				//
				
				$abc = ""; 
				if( isset($_REQUEST['abc']) ) $abc = substr( lossSQLinjectionChars( $_REQUEST['abc'] ), 0,1 ) ;
				
				$reqDAO->clearMe();
				
				$reqDAO->setValue( 'Active',1) ;
				$reqDAO->setValue( 'Circle',(int)$circ ) ;
				$reqDAO->setValue( 'ReqNum',(int)$rnum ) ;
				$reqDAO->setValue( 'SubReqNum',$abc ) ;
				$reqDAO->setValue( 'Year',(int)$yr ) ;
				
				$reqDAO->loadMe();
				
				if( $reqDAO->getID()>0 ){
					//
					echo '{"e":1938,"id":'.(int)$reqDAO->getID().',"msg":"insert failed: data duplication"}';
				}
				else{
					//
					$sna = $circ.'-'.$rnum.( strlen($abc)>0 ? $abc : '' ).'/20'.$yr ;
					$reqDAO->setValue( 'ShortName', $sna ) ;

					$v = $_REQUEST['appr'] ;

					if( strpos( $v,'.' )!==false ){
				     $v = str_replace( ',','', $v );
				     $v = str_replace( ' ','', $v );
					}
				  
				    if( strpos( $v,',' )!==false ){
				     $v = str_replace( ',','.', $v );
					 $v = str_replace( ' ','', $v );
					}
				  
					$appr = round( $v, 2 ) ;
					
					$reqDAO->setValue( 'ApprAmount', $appr ) ;
					$reqDAO->setValue( 'Currency', $cur ) ; // kieg 2018-09-11
					
					$reqDAO->setValue( 'JsonData', jsontext4SQL($txt) ) ;
					
					$reqDAO->setValue( 'UserID', $uid ) ;
					
					$reqDAO->insertMe() ;
					//
				
					$lr = new \Jazz\Core\JazzDAOList( $yc,
												$reqDAO,
												[
													['Active', '=', 1 ],
													['Year', '=', (int)$_REQUEST['yr'] ],
												], 
												[ 
													['Circle',1], ['ReqNum',1], ['SubReqNum',1], 
												]
											);
											
					$li = $lr->getRowsArrayByID( [ 'ID', 'ShortName' ] ) ;
					$r_cbx = array();
					foreach( $li as $r ) $r_cbx[] = array( $r['ID'], $r['ShortName'] ) ;
					
					//
	
					echo '{"e":0,"id":'.(int)$reqDAO->getID().',"req":"'.$sna.'","cbx":'.json_encode($r_cbx).'}' ;  // OK
				}
				
				break;
	//
	default:
				die('{"e":1926}'); // transfer.cmd.unk
		
}//#switch

die('');
