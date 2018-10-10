<?php
/**
 * NATO MILMED COE FPS Tier 1.
 * BudgetCode__Line__Editor.php
 * @start 2018-08-19 nr
 * @lastmod 2018-10-10 nr 15:57
 *
 * ALTER TABLE `fi_natomm_723111` CHANGE `subj` `subj` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'evt venue dt pt trv';
 * ALTER TABLE `fi_natomm_723311` CHANGE `subj` `subj` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'evt venue dt pt';
 * 
 */


header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['cmd']) ) die('{"e":1926}'); // transfer.cmd.unk;

if( !isset($_REQUEST['bc']) )   die('{"e":1931}'); // data.transfer.cmd.params.error.missing | bc: BudgetCodeID
if( (int)$_REQUEST['bc'] <700000 ) die('{"e":1930}'); // invalid value

if( !isset($_REQUEST['yr']) ) die('{"e":1931}'); // missing; | Year (18)
if( (int)$_REQUEST['yr'] <10 ) die('{"e":1930}');


$booked_cur_txt = "EUR" ; // default 2018-09-10


//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeLineDataDAO.php' ) ;
	$bclineDAO = new \Nato\Coemed\CoeMedFiBudgetCodeLineDataDAO( $yc, (int)$_REQUEST['bc'] ) ; // itt derul ki h melyik BC szerinti tabla
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeLineMetaDAO.php' ) ;
	$bcMetaDAO = new \Nato\Coemed\CoeMedFiBudgetCodeLineMetaDAO( $yc ) ;
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeDefDAO.php' ) ; // kieg 20180915
	$bcDefDAO = new \Nato\Coemed\CoeMedFiBudgetCodeDefDAO( $yc ) ;
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeTableDAO.php' ) ; // kieg 20180925
	$bcTable = new \Nato\Coemed\CoeMedFiBudgetCodeTableDAO( $yc ) ;
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRequestDAO.php' ) ; // kieg 20180920	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRqCalcCacheDAO.php' ) ; // -"-
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPartnerDAO.php' ) ; // kieg 20181007
//

//print_r( $_REQUEST ) ;

//
switch( $_REQUEST['cmd'] ){

	//
	case 'new':
	case 'put':
				
				if( !isset($_REQUEST['t']) )    
				die('{"e":1931,"p":"t"}'); // missing | ItemTypeID: 906 | 907 | 908
				
				if( !( ((int)$_REQUEST['t']==904) || ((int)$_REQUEST['t']==905) ||
					   ((int)$_REQUEST['t']==906) || ((int)$_REQUEST['t']==907) || ((int)$_REQUEST['t']==908) ) )  
				die('{"e":1930,"p":"t"}'); // param. is invalid
				
				//
				//$bcDefDAO->clearMe();
				//$bcDefDAO->setValue( 'ID', (int)$_REQUEST['bc'] ); // kieg 20180915
				//$bcDefDAO->setValue( 'DirtyFlag', 1 ); // obsolete 20181002
				//$bcDefDAO->updateMe();				
				//
				
				//
				$bcTable->clearMe(); // 20180925
				$bcTable->setValue( 'BudgetCode', (int)$_REQUEST['bc'] );
				$bcTable->setValue( 'Yr', (int)$_REQUEST['yr'] );
				$bcTable->loadMe();
				
				$bctID = (int)$bcTable->getID() ;
				
				$bcTable->clearMe() ;
				$bcTable->setValue( 'ID', $bctID );
				$bcTable->setValue( 'DirtyFlag', 1 );
				$bcTable->setValue( 'BudgetCode', (int)$_REQUEST['bc'] );
				$bcTable->setValue( 'Yr', (int)$_REQUEST['yr'] );
				$bcTable->saveMe(); // ................ ezzel kiderült h volt-e?
				//
				
				//
				$bclineDAO->clearMe();
				
				$bclineDAO->setItemTypeID( (int)$_REQUEST['t'] ) ;
				
				if( (int)$_REQUEST['t']==904 ) $bclineDAO->setValue( 'Key', 'est' ) ;
				else
				if( (int)$_REQUEST['t']==905 ) $bclineDAO->setValue( 'Key', 'mod' ) ;
				else
				if( (int)$_REQUEST['t']==906 ) $bclineDAO->setValue( 'Key', 'exp' ) ;
				else
				if( (int)$_REQUEST['t']==907 ) $bclineDAO->setValue( 'Key', 'com' ) ;
				else
				if( (int)$_REQUEST['t']==908 ) $bclineDAO->setValue( 'Key', 'rev' ) ;
				
				
				$bclineDAO->setValue('Year', (int)$_REQUEST['yr'] ) ;
				
				
				if( ((int)$_REQUEST['t']==904 ) || ((int)$_REQUEST['t']==905 ) ){
				  //
				  $bclineDAO->setValue('BookedAmount', round( $_REQUEST['amt'],2 ) ) ;
				}
				
				//
				if( isset($_REQUEST['cur']) )
				if( ( (int)$_REQUEST['cur']>0 )&&( (int)$_REQUEST['cur']!=978 ) )
				  { 
				   $bclineDAO->setValue('BookedCurrency', (int)$_REQUEST['cur'] ) ; // 2018-09-10
				   $bclineDAO->setValue('InvoiceCurrency', (int)$_REQUEST['cur'] ) ; // 2018-09-10
				  }
				
				//
				if( isset($_REQUEST['subj']) ){
				  $bclineDAO->setValue('Subject', json_encode( $_REQUEST['subj'] ) ) ;
				}
				else
				  $bclineDAO->setValue('Subject', '""' ) ; // empty string
				
				//
				$bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				//
				$bclineDAO->insertMe();
				
				
				echo '{"e":0,"id":'.(int)$bclineDAO->getID().'}' ;
				
				break;
				//

	//
	case 'mod':
				if( !isset($_REQUEST['rid']) )  die('{"e":1931}'); // missing !! RowID
				
				if( !isset($_REQUEST['t']) )  die('{"e":1931}'); // missing !! type
				
				//
				//$bcDefDAO->clearMe();
				//$bcDefDAO->setValue( 'ID', (int)$_REQUEST['bc'] ); // kieg 20180915
				//$bcDefDAO->setValue( 'DirtyFlag', 1 ); // Obsolete 20181002
				//$bcDefDAO->updateMe();				
				//
								
				//
				$bcTable->clearMe(); // 20180925
				$bcTable->setValue( 'BudgetCode', (int)$_REQUEST['bc'] );
				$bcTable->setValue( 'Yr', (int)$_REQUEST['yr'] );
				$bcTable->loadMe();
				
				$bctID = (int)$bcTable->getID() ;
				
				$bcTable->clearMe() ;
				$bcTable->setValue( 'ID', $bctID );
				$bcTable->setValue( 'DirtyFlag', 1 );
				$bcTable->setValue( 'BudgetCode', (int)$_REQUEST['bc'] );
				$bcTable->setValue( 'Yr', (int)$_REQUEST['yr'] );
				$bcTable->saveMe(); // ................ ezzel kiderült h volt-e?
				//
				
				//
				if( strcmp( $_REQUEST['t'],"904")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $v = $_REQUEST['amt'] ;
				  
				  if( strpos( $v,'.' )!==false ){
				   $v = str_replace( ',','', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  if( strpos( $v,',' )!==false ){
				   $v = str_replace( ',','.', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  $amt = round( $v, 2 ) ;
				
				  $bclineDAO->setValue('BookedAmount', $amt ) ;
				  
				  if( isset($_REQUEST['cur']) )
				  if( ( (int)$_REQUEST['cur']>0 )&&( (int)$_REQUEST['cur']!=978 ) )
				  $bclineDAO->setValue('BookedCurrency', (int)$_REQUEST['cur'] ) ; // 2018-09-10
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"904","amt":'.$amt.',"rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"905")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $v = $_REQUEST['amt'] ;
				  
				  if( strpos( $v,'.' )!==false ){
				   $v = str_replace( ',','', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  if( strpos( $v,',' )!==false ){
				   $v = str_replace( ',','.', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  $amt = round( $v, 2 ) ;
				
				  $bclineDAO->setValue('BookedAmount', $amt ) ;
				  
				  if( isset($_REQUEST['cur']) )
				  if( ( (int)$_REQUEST['cur']>0 )&&( (int)$_REQUEST['cur']!=978 ) )
				  $bclineDAO->setValue('BookedCurrency', (int)$_REQUEST['cur'] ) ; // 2018-09-10
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"905","amt":'.$amt.',"rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"jtxt")==0 ){ // 20180926
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  
				  $bclineDAO->setValue('Subject', jsontext4SQL( trim($_REQUEST['v']) ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"jtxt","v":"'. trim($_REQUEST['v']).'","rid":'.(int)$_REQUEST['rid'].'}' ;

				}
				else
				if( strcmp( $_REQUEST['t'],"subj")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('Subject', jsontext4SQL( trim($_REQUEST['v']) ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"subj","v":"'.$_REQUEST['v'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"event")==0 ){ // 20180924
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $ja = json_decode( $bclineDAO->getValue('Subject'), true ) ;
				  
				  if( !isset($ja) || !is_array($ja) ) $ja = array( 'event'=>"", 'venue'=>"", 'evdt'=>"" );
				  
				  $ja['event'] = substr( trim($_REQUEST['v']),0,128 ) ; // max-of 128
				  
				  $bclineDAO->setValue('Subject', jsonobject4SQL( $ja ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"event","v":"'.$ja['event'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"venue")==0 ){ // 20180924
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $ja = json_decode( $bclineDAO->getValue('Subject'), true ) ;
				  
				  if( !isset($ja) || !is_array($ja) ) $ja = array( 'event'=>"", 'venue'=>"", 'evdt'=>"" );
				  
				  $ja['venue'] = substr( trim($_REQUEST['v']),0,48 ) ; // max-of 48
				  
				  $bclineDAO->setValue('Subject', jsonobject4SQL( $ja ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"venue","v":"'.$ja['venue'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"evdt")==0 ){ // 20180924
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $ja = json_decode( $bclineDAO->getValue('Subject'), true ) ;
				  
				  if( !isset($ja) || !is_array($ja) ) $ja = array( 'event'=>"", 'venue'=>"", 'evdt'=>"" );
				  
				  $ja['evdt'] = substr( trim($_REQUEST['v']),0,32 ) ; // max-of 32
				  
				  $bclineDAO->setValue('Subject', jsonobject4SQL( $ja ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"evdt","v":"'.$ja['evdt'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"ptcp")==0 ){ // 20181002
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $ja = json_decode( $bclineDAO->getValue('Subject'), true ) ;
				  
				  if( !isset($ja) || !is_array($ja) ) $ja = array( 'event'=>"", 'venue'=>"", 'evdt'=>"", 'trv'=>"" );
				  
				  $ja['ptcp'] = substr( trim($_REQUEST['v']),0,255) ; // max-of 255 -> TEXT(4096) ALTERED BC TABLE!
				  
				  $bclineDAO->setValue('Subject', jsonobject4SQL( $ja ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"ptcp","v":"'.$ja['ptcp'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"trv")==0 ){ // 20181002 'Travel on Duty': TDY; but as plain Text: TRV
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $ja = json_decode( $bclineDAO->getValue('Subject'), true ) ;
				  
				  if( !isset($ja) || !is_array($ja) ) $ja = array( 'event'=>"", 'venue'=>"", 'evdt'=>"", 'ptcp'=>"" );
				  
				  $ja['trv'] = substr($_REQUEST['v'],0,255) ; // 255!
				  
				  $bclineDAO->setValue('Subject', jsonobject4SQL( $ja ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"trv","v":"'.$ja['trv'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"invo")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('InvoiceNumber', lossSQLinjectionChars( $_REQUEST['v'] ) ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"invo","v":"'.$_REQUEST['v'].'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"date")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $da = lossSQLinjectionChars( $_REQUEST['v'] ) ;
				  
				  if( strpos( $da,'/' )!==false )
				  {
				   $a = explode( '/',$da ) ; // kieg. 20181010
				   
				   $da = $a[2].'-'.$a[1].'-'.$a[0] ; // 23/06/1980 = 1980-06-23
				  }
				  
				  // error handle:
				  if( strlen($da)<10 ){ $da = date('Y-m-d'); }
				  else
				  if( substr($da,0,4)=="0000" ){ $da = date('Y-m-d'); }
				  else
				  if( substr($da,5,2)=="00" ){ $da = date('Y-m-d'); }
				  else
				  if( substr($da,8,2)=="00" ){ $da = date('Y-m-d'); }
				  else
				  if( strpos($da,'-')===false ){ $da = date('Y-m-d'); }
				
				  $bclineDAO->setValue('Date', $da ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"date","v":"'.$da.'","rid":'.(int)$_REQUEST['rid'].'}' ;
				
				}
				else
				if( strcmp( $_REQUEST['t'],"req")==0 ){ // mod. 20180920
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $oldID = (int)$bclineDAO->getValue('RequestID') ; // old_reqID
				  
				  $reqID = (int)$_REQUEST['v'] ;
				  
				  $typ   = (int)$bclineDAO->getItemTypeID() ; // 20181002
				
				  $bclineDAO->setValue('RequestID', $reqID ) ;
				
				  $bclineDAO->updateMe();
				  
				  //
				  $reqDAO = new \Nato\Coemed\CoeMedFiRequestDAO( $yc ) ;
				  
				  $reqDAO->clearMe();
				  $reqDAO->setID( $reqID );
				  $reqDAO->setValue( 'DirtyFlag',1 ); // ezt is 1-re! :)
				  $reqDAO->updateMe();
				  
				  $reqDAO->clearMe();
				  $reqDAO->setID( $oldID);
				  $reqDAO->setValue( 'DirtyFlag',1 ); // + ezt is :)
				  $reqDAO->updateMe();
				  
				  $reqChID = 0;
				  
				  if( $typ==906 ){ // !! csak ha 906:exp
				   //
				   $reqChDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;
				  
				   $reqChDAO->clearMe() ;
				   $reqChDAO->setValue('BCodeID', (int)$_REQUEST['bc'] );
				   $reqChDAO->setValue('BCodeRowID', (int)$_REQUEST['rid'] );
				   $reqChDAO->loadMe() ;
				   
				   $reqChID = (int)$reqChDAO->getID();
				   
				   $reqChDAO->setValue('ReqID',    $reqID ); // az ÚJRA átváltani!
				  
				   $reqChDAO->saveMe() ; // !! :)
				  }
				  //
				
				  echo '{"e":0,"t":"req","rid":'.(int)$_REQUEST['rid'].',"req":"'.$reqID.'","rch":'.$reqChID.'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"pow")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('POWID', (int)$_REQUEST['v'] ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"pow","v":'.(int)$_REQUEST['v'].',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"br")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('BranchID', (int)$_REQUEST['v'] ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"br","v":'.(int)$_REQUEST['v'].',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"prj")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('ProjectID', (int)$_REQUEST['v'] ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"prj","v":'.(int)$_REQUEST['v'].',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"pa")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('PartnerID', (int)$_REQUEST['v'] ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"pa","v":'.(int)$_REQUEST['v'].',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"cust")==0 ){ // Customer: 20181007
				
				  //
				  $custoID = 0; 
				  
				  $custoDAO =  new \Nato\Coemed\CoeMedFiPartnerDAO( $yc ) ;
				  $custoDAO->clearMe() ;
				  
				  $custoDAO->setItemTypeID( 330 ) ; // Customer
				  
				  $custoDAO->setValue('CountryCode', 348 ); 
				  if( isset($_REQUEST['cc']) ) 
				  if( (int)$_REQUEST['cc'] >0 )
				  $custoDAO->setValue('CountryCode', (int)$_REQUEST['cc'] ); 
				  
				  $custoDAO->setValue('JsonData', jsonobject4SQL( array( "n" => $_REQUEST['v'] ) ) ) ; // megkeressük
				  
				  $custoDAO->loadMe() ;
				  
				  $custoID = (int)$custoDAO->getID() ;
				  
				  if( $custoID ==0 )
				  {
				   $custoDAO->setID("") ;
				   $custoDAO->setValue("Active", 1) ;
				   $custoDAO->insertMe() ;
				   $custoID = (int)$custoDAO->getID() ; // 20181008
				  }
				  //
				  
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  $bclineDAO->setValue('PartnerID', $custoID ) ; // :)
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"cust","v":"'.$_REQUEST['v'].'","id":'.(int)$custoID.',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"pm")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				
				  if( (int)$_REQUEST['v'] < 95 ) $_REQUEST['v'] = 97 ; // bank transfer
				  if( (int)$_REQUEST['v'] > 99 ) $_REQUEST['v'] = 97 ; // 
				
				  $bclineDAO->setValue('PaymentMethode', (int)$_REQUEST['v'] ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //
				
				  echo '{"e":0,"t":"pm","v":'.(int)$_REQUEST['v'].',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"xr")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $reqID = (int)$bclineDAO->getValue('RequestID') ; // kieg. 2018-09-19
				  
				  $ia = round( $bclineDAO->getValue('InvoiceAmount'), 2 ) ; // kieg. 2018-09-13
				  
				  $ba = round( $bclineDAO->getValue('BookedAmount'), 2 ) ; // by default ...
				  
				  $ccc= (int)$bclineDAO->getValue('BookedCurrency') ; // ba: miben lesz a Booking
				  
				  //
				  $ccn = null;
				  if( $ccc==978 ){ $ccn="EUR"; }
				  else
				  if( $ccc==348 ){ $ccn="HUF"; }
				  else
				  if( $ccc==840 ){ $ccn="USD"; }
				  else
				  if( $ccc==826 ){ $ccn="GBP"; }
				  else
				   $ccn=""; // muszaj lesz lekerdezni... vagy inkabb kapja meg parameterben
				  
				  //
				  $v = $_REQUEST['v'] ;
				  
				  if( strpos( $v,'.' )!==false ){
				   $v = str_replace( ',','', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  if( strpos( $v,',' )!==false ){
				   $v = str_replace( ',','.', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  $xr = round( $v, 8 ) ;  // 8 értékű!! (bár csak 2-t jelenítünk meg 'html')
				
				  $bclineDAO->setValue('XRate', $xr ) ;
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				  //
				  
			      $sum_of_exp = 0;
				  //
				  //

				  
				  //
				  // 20181002 rch_amt
				  
				  $rch_amt = $ia ;
				  //
				  if( $xr >0.0 ){
				   if( $xr >2.0 ){ $rch_amt = round( $ia / $xr, 2 )  ; } // HUF->EUR váltás (pl. 314,77)
				   else{           $rch_amt = round( $ia * $xr, 2 )  ; } // USD->EUR váltás (pl. 0,88)
				  }
				  
				  //
				  $reqChDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;

				  $reqChID = 0 ;
				  
				  //
				  if( ( $bclineDAO->getItemTypeID()==906 ) || ( $bclineDAO->getItemTypeID()==908 ) )
				  {
				     $reqChDAO->clearMe() ;
				     $reqChDAO->setValue('BCodeID', (int)$_REQUEST['bc'] );
				     $reqChDAO->setValue('BCodeRowID', (int)$_REQUEST['rid'] );
				     $reqChDAO->loadMe() ;
				   
				     $reqChID = (int)$reqChDAO->getID();
				   
				     $reqChDAO->setValue('ReqID',    $reqID );
				     $reqChDAO->setValue('Amount',   $rch_amt );
				     $reqChDAO->setValue('Currency', $bclineDAO->getValue('BookedCurrency') );
				  
				     $reqChDAO->saveMe() ; // !! :)
				   //
				  }
				  else
				  if( $bclineDAO->getItemTypeID()==907 )
				  { // ........................................... 20180925 :
					
					//
					$lr_soe = new \Jazz\Core\JazzDAOList( $yc, $reqChDAO, [ ['ReqID','=',(int)$reqID ], 
																		    ['Yr','=',(int)$_REQUEST['yr'] ], 
																			['BCodeID','=',(int)$_REQUEST['bc'] ],   ] 
														) ;
				  
					$sum_of_exp = $lr_soe->sum('Amount') ; // ReqCache | _here_ this fld name is: 'Amount' and NOT! InvoiceAmount or BookedAmount
					//
				  }
				  
				  //
				  if( $xr >0.0 ){
				   if( $xr >2.0 ){ $ba = round( ( $ia - $sum_of_exp ) / $xr ,2 )  ; } // HUF->EUR váltás (pl. 314,77)
				   else{           $ba = round( ( $ia - $sum_of_exp ) * $xr ,2 )  ; } // USD->EUR váltás (pl. 0,88)
				  }
				  else{ $ba = round( $ia - $sum_of_exp,2 ) ; }
				  
				  //
				  $bclineDAO->setValue('BookedAmount', $ba ) ; // !!
				  
				  $bclineDAO->updateMe();
				  //
				  
				  //
				  $money = null; 
				  $tizedek=2;
				  if( $ccc==348 )
				   $tizedek = 0;
				  
				  //
				  $money = number_format( $ba,$tizedek,',',' ' ).' '.$ccn ;				   
				  //
				  
				  //
				  echo '{"e":0,"t":"xr","v":'.$xr.',"ba":'.$ba.',"bastr":"'.$money.'","req":'.$reqID.',"rch":'.$reqChID.',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"ba")==0 ){
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $reqID = (int)$bclineDAO->getValue('RequestID') ; // kieg. 2018-09-13
				  
				  //
				  $reqDAO = new \Nato\Coemed\CoeMedFiRequestDAO( $yc ) ;
				  
				  //
				  $reqChDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;

				  $reqChID = 0 ;
				  
				  //
				  if( !isset($_REQUEST['cur']) ) $_REQUEST['cur'] = 978; // bugfix 20180925
				
				  $v = $_REQUEST['v'] ;
				  
				  if( strpos( $v,'.' )!==false ){
				   $v = str_replace( ',','', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  if( strpos( $v,',' )!==false ){
				   $v = str_replace( ',','.', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  $amt = round( $v, 2 ) ;
				
				  $bclineDAO->setValue('BookedAmount', $amt ) ;
				  
				  $bclineDAO->setValue('BookedCurrency', (int)$_REQUEST['cur'] ) ; // 2018-09-10

				  //
				  
				  if( ($bclineDAO->getItemTypeID() ==906) || ($bclineDAO->getItemTypeID() ==908) )
				  if(  $reqID >0  )
				  {
				    //
					$reqDAO->clearMe();
					$reqDAO->setID( $reqID );
					$reqDAO->setValue( 'DirtyFlag',1 ); // ezt is
					$reqDAO->updateMe();

				    //
				     $reqChDAO->clearMe() ;
				     $reqChDAO->setValue('BCodeID', (int)$_REQUEST['bc'] );
				     $reqChDAO->setValue('BCodeRowID', (int)$_REQUEST['rid'] );
				     $reqChDAO->loadMe() ;
				   
				     $reqChID = (int)$reqChDAO->getID();
				   
				     $reqChDAO->setValue('ReqID',    $reqID );
				     $reqChDAO->setValue('Amount',   $amt );
				     $reqChDAO->setValue('Currency', $bclineDAO->getValue('BookedCurrency') );
				  
				     $reqChDAO->saveMe() ; // !! :)
				  }
				  

				  //				  
				  $xr = round( $bclineDAO->setValue('XRate'), 8 ) ;
				  
				  //
				   $booked_cur_txt = "EUR";
				   
				   if( (int)$_REQUEST['cur']== 348 ){ $booked_cur_txt = "HUF"; }
				   else
				   if( (int)$_REQUEST['cur']== 840 ){ $booked_cur_txt = "USD"; }
				  //
				
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  //

				  echo '{"e":0,"t":"ba","xr":'.$xr.',"v":'.$amt.',"str":"'.number_format($amt,2,',',' ').' '.$booked_cur_txt.'","req":'.$reqID.',"rch":'.$reqChID.',"rid":'.(int)$_REQUEST['rid'].'}' ;
				}
				else
				if( strcmp( $_REQUEST['t'],"ia")==0 ){
				
				  //
				  if( !isset($_REQUEST['c']) ) die('{"e":1931,"p":"c","t":"ia"}');
				  else
				  if( (int)$_REQUEST['c']<0 ) die('{"e":1930,"p":"c","t":"ia"}');
				  
				  if( !isset($_REQUEST['ccn']) ) die('{"e":1931,"p":"ccn","t":"ia"}');
				  else{
				  if( strlen($_REQUEST['ccn'])<3 ) die('{"e":1930,"p":"ccn","t":"ia"}');
				  if( strpos($_REQUEST['ccn'],'"')!==false ) die('{"e":1930,"p":"ccn","t":"ia"}');
				  }
				  
				  if( !isset($_REQUEST['cur']) ) $_REQUEST['cur'] = 978; // bugfix 20180925
				
				  //
				  $bclineDAO->clearMe();
				  $bclineDAO->setID( (int)$_REQUEST['rid'] ) ;
				  $bclineDAO->loadMe();
				  
				  $reqID = (int)$bclineDAO->getValue('RequestID') ; // kieg. 2018-09-13
				  
				  $ba = 0; // ! 20180925
				
				  $ccc = (int)$_REQUEST['c'];
				  $bclineDAO->setValue('InvoiceCurrency', 978 ) ;
				  $bclineDAO->setValue('BookedCurrency',  978 ) ;
				  
				  if( $ccc!=978 )
				  { 
				   $bclineDAO->setValue('InvoiceCurrency', $ccc ) ; // 20180925
				  }
				  
				  if( (int)$_REQUEST['cur'] !=978 )
				  { 
				   $bclineDAO->setValue('BookedCurrency', (int)$_REQUEST['cur'] ) ; // 20180925
				  }
				  
				  $ccn = $_REQUEST['ccn'];
				
				  $v = $_REQUEST['v'] ;
				  
				  if( strpos( $v,'.' )!==false ){
				   $v = str_replace( ',','', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  if( strpos( $v,',' )!==false ){
				   $v = str_replace( ',','.', $v );
				   $v = str_replace( ' ','', $v );
				  }
				  
				  $ia = round( $v, 2 ) ;
				  $ia_orig = $ia ;
				
				  $bclineDAO->setValue('InvoiceAmount', $ia_orig ) ;
				  
				  //
				  $bcur = (int)$bclineDAO->getValue('BookedCurrency') ;
				  
				  //
				  $xr = round( $bclineDAO->getValue('XRate'),8 ) ;  // 8 értékű!!
				  
				  //
				  $sum_of_exp = 0 ; // by reqID 20180920
				  //				  

				  
				  //
				  //
					$reqDAO = new \Nato\Coemed\CoeMedFiRequestDAO( $yc ) ;
				    $reqChDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;

				    $reqChID = 0 ;
				  
				  //
				  $rch_amt = $ia_orig;
				  
				  if( $xr >0.0 ){
				   if( $xr >2.0 ){ $rch_amt = round( $ia_orig / $xr,2 )  ; } // HUF->EUR
				   else{           $rch_amt = round( $ia_orig * $xr,2 )  ; } // USD->EUR
				  }
				  
				  
				  //
				  if( ($bclineDAO->getItemTypeID() ==906) || ($bclineDAO->getItemTypeID() ==908) )
				  {
				    //
					$reqDAO->clearMe();
					$reqDAO->setID( $reqID );
					$reqDAO->setValue( 'DirtyFlag',1 ); // ezt is
					$reqDAO->updateMe();
				  
					//
					$reqChDAO->clearMe() ;
					$reqChDAO->setValue('BCodeID', (int)$_REQUEST['bc'] );
					$reqChDAO->setValue('BCodeRowID', (int)$_REQUEST['rid'] );
					$reqChDAO->loadMe() ;
				  
					$reqChDAO->setValue('ReqID',    $reqID ); // set it! fix it! 
					$reqChDAO->setValue('Amount',   round( $rch_amt, 2) );
					$reqChDAO->setValue('Currency', $bclineDAO->getValue('BookedCurrency') );
				  
					$reqChDAO->saveMe() ; // !! :)  Csak az EXP és REV kerulhet be! a COM nem kerulhet be!!!!
					//
				  }
				  
				  //

				  //kieg.2018-09-16
				  //			  
				   $booked_cur_txt = "EUR";
				   
				   if( $bcur== 348 ){ $booked_cur_txt = "HUF"; }
				   else
				   if( $bcur== 840 ){ $booked_cur_txt = "USD"; }
				  //
				  
				  //
				  if( $bclineDAO->getItemTypeID()==907 )
				  { // ........................................... 20180925 :
				  
				    $reqChDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;

				    //
					$lr_soe = new \Jazz\Core\JazzDAOList( $yc, $reqChDAO, [ ['ReqID','=',(int)$reqID ], 
																		    ['Yr','=',(int)$_REQUEST['yr'] ], 
																			['BCodeID','=',(int)$_REQUEST['bc'] ],   ] 
														) ;
				  
					$sum_of_exp = $lr_soe->sum('Amount') ; // ReqCache | _here_ this fld name is: 'Amount' and NOT! InvoiceAmount or BookedAmount
					//
				  }
				  
				  
				  //
				  //
				  $ia = $ia_orig - $sum_of_exp ;
				  
				  //
				  if( $xr >0.0 ){
				   if( $xr >2.0 ){ $ba = round( $ia / $xr,2 )  ; } // HUF->EUR váltás (pl. 314,77)
				   else{           $ba = round( $ia * $xr,2 )  ; } // USD->EUR váltás (pl. 0,88)
				  }
				  else{ $ba = round( $ia ,2 ) ; }				  
				  
				  $bclineDAO->setValue('BookedAmount', $ba ) ; // missed... | fixed 20180918
				  
				  				  
				  //
				  //
				  $money = null; 
				  $tizedek=2;
				  if( $ccc==348 )
				   $tizedek = 0;
				   
				  $money = number_format( $ia_orig ,$tizedek,',',' ').' '.$ccn ; // 20181002: ia_orig megy vissza!
				  
				  //
				  $bastr = number_format($ba,2,',',' ').' '.$booked_cur_txt ;
				  
				  //
				  $bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				  $bclineDAO->updateMe();
				  
				  //--
				  
				  echo '{"e":0,"t":"ia","rid":'.(int)$_REQUEST['rid'].',"xr":'.$xr.',"ia":'.$ia_orig.',"v":'.$ia.',"str":"'.$money.'","c":'.$ccc.',"ccn":"'.$ccn.'","ba":'.$ba.',"bastr":"'.$bastr.'","req":'.$reqID.',"soe":'.round($sum_of_exp,2).'}' ;
				}

				
				break;
				//
				//#Mod
	//
	case 'del':
				if( !isset($_REQUEST['id']) ) die('{"e":1931}'); // ID | (INT) >0
				
				$bclineDAO->clearMe();
				$bclineDAO->setID( $_REQUEST['id'] );
				$bclineDAO->setValue('Active',    0);
				//$bclineDAO->setValue('ItemTypeID',0); // this way :O | 'key' field can store original itemType
				
				$bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				$bclineDAO->updateMe();

				echo '{"e":0,"r":1937}' ;  // data.transfer.item.processing.done
				
				break;
	//
	case 'hide':
				if( !isset($_REQUEST['id']) ) die('{"e":1931}'); // ID | (INT) >0
				
				$bclineDAO->clearMe();
				$bclineDAO->setID( $_REQUEST['id'] );
				
				$bclineDAO->setValue('Active',    14); // HIDDEN
				$bclineDAO->setValue('InsertedByUID', $uid ) ; // !!
				
				$bclineDAO->updateMe();

				echo '{"e":0,"r":1937}' ;  // data.transfer.item.processing.done

				break;
	//
	default:
				echo '{"e":1926}' ; // transfer.cmd.unk
		
}//#switch

//
exit(0); 
//