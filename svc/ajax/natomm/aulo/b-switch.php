<?php
/**
 * 'BudgetCode / Row' switcher (a=0,1)
 * NATO MILMED COE FPS Tier 1.
 * @start 2018-09-19 nr
 * @lastmod 2018-09-19 nr 10:10
 */

header('Access-Control-Allow-Origin: *'); 

if( !isset($wstoken) )die('{"e":1904}'); // connection.port-error
if( $lvl <1 )die('{"e":1905}'); // connection.auth-error

//
if( !isset($_REQUEST['bc']) ) die('{"e":1931}'); // transfer.cmd.params.error.missing  | 'ID' as BCodeID

if( (int)$_REQUEST['bc'] < 1 ) die('{"e":1930}'); // data.transfer.cmd.params.error.invalid

//
if( !isset($_REQUEST['r']) ) die('{"e":1931}'); // transfer.cmd.params.error.missing  | 'RowID' as BCodeRowID

if( (int)$_REQUEST['r'] < 1 ) die('{"e":1930}'); // data.transfer.cmd.params.error.invalid

if( !isset($_REQUEST['a']) ) die('{"e":1931}'); // transfer.cmd.params.error.missing  | a: 0,1

if( (int)$_REQUEST['a'] != 1 ) $_REQUEST['a'] = 0; // simplify

//
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeLineDataDAO.php' ) ;
 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRqCalcCacheDAO.php' ) ;
 
 
 //
 $bcdataDAO = new \Nato\Coemed\CoeMedFiBudgetCodeLineDataDAO( $yc, (int)$_REQUEST['bc'] ) ; // igen ez ilyen :)
 
 $bcdataDAO->clearMe() ;
 $bcdataDAO->setID( (int)$_REQUEST['r'] ) ;
 $bcdataDAO->setValue('Active', (int)$_REQUEST['a'] ) ;
 $bcdataDAO->updateMe() ;
 
 
//
 // In The End: -- a RequestCache-bõl is ki kell törölni!
 
 //
  //
  
  $rchDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;
 
  $rchDAO->clearMe() ;
  $rchDAO->setValue('BCodeID', (int)$_REQUEST['bc'] ) ;
  $rchDAO->setValue('BCodeRowID', (int)$_REQUEST['r'] ) ;
  $rchDAO->loadMe() ;
 
  $rchDAO->setValue('Active',0 ) ;      // clean all the fields!
  $rchDAO->setValue('BCodeID', 0 ) ;
  $rchDAO->setValue('BCodeRowID', 0 ) ;
  $rchDAO->setValue('ReqID', 0 ) ;
  $rchDAO->setValue('Year', 0 ) ;
  $rchDAO->setValue('Amount', 0 ) ;
  $rchDAO->setValue('Currency', 0 ) ;
  $rchDAO->updateMe() ;
 
  //
 //
 
 //
 echo '{"e":0,"bc":'.(int)$_REQUEST['bc'].',"r":'.(int)$_REQUEST['r'].',"a":'.(int)$_REQUEST['a'].'}';
 
 exit(0);
 //
 