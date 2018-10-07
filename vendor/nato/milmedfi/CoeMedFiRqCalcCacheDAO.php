<?php
/**
 * CoeMedFiRequestCalculatorCacheDAO
 * 
 * NATO / CoeMed / Finance / RequestCalculatorCacheDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-09-12
 * @lastmod 2018-09-18 nr 16:00
 * 
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiRequestCalculatorCacheDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_req_ch'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a',  'Active' ); 		// TINYINT(1)
	 
	 $m[] = array( 'req',  'ReqID' ); 		// INT
	 $m[] = array( 'bcid',  'BCodeID' ); 	// INT
	 $m[] = array( 'yr',  'Yr' ); 			// TINYINT(2)  |  10..99
	 $m[] = array( 'bcrw',  'BCodeRowID' ); // INT
	 
	 $m[] = array( 'amt',  'Amount' ); // DECIMAL 12,2

	 $m[] = array( 'cur',  'Currency' ); // INT | ISO-4217 CurrencyID (UN)
	 //
	 return $m;
	 //
	}//#mapping()
	
	//
	//
	//
	public function __construct( $yc = null ){
	 //
	 
	 if( isset($yc) ){ $this->__yc = $yc; }
	 
	 $this->__dirty = 0;
	 
	 $this->loadMap( CoeMedFiRequestCalculatorCacheDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	

}//#CoeMedFiRequestCalculatorCacheDAO{}
