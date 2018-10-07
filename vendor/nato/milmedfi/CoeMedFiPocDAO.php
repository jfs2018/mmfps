<?php
/**
 * CoeMedFiPOCDAO
 * 
 * NATO / CoeMed / Finance / POCDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-07-08 17:35
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiPOCDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_poc'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'v', 'isValidNow' ); 	// TINYINT(1)
	 $m[] = array( 't', 'ItemTypeID' );		// INT | 311
	 $m[] = array( 'scid', 'SubClassID' ); 	// INT | 314:Primary/HQ, 313:Telephely(Premise)
	 $m[] = array( 'pid', 'PartnerID' ); 	// INT PartnerID#
	 
	 $m[] = array( 'cc', 'CountryCode' ); 	// INT | ISO 3166 ID#
	 $m[] = array( 'first8', 'First8' ); 	// CHAR(8)
	 
	 $m[] = array( 'ts', 'TimeStamp' ); 	// TIMESTAMP
	 
	 $m[] = array( 'jdn', 'JsonDataName' ); 	// TEXT
	 $m[] = array( 'jda', 'JsonDataAddress' );	// TEXT
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
	 
	 $this->loadMap( CoeMedFiPOCDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiPOCDAO{}  
