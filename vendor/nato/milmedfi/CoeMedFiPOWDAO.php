<?php
/**
 * CoeMedFiPOWDAO
 * 
 * NATO / CoeMed / Finance / POWDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-07-11 12:49
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiPOWDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_pow'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a', 'Active' ); 		// TINYINT(1)
	 $m[] = array( 't', 'ItemTypeID' );		// INT | 328
	 $m[] = array( 'scid', 'SubClassID' ); 	// INT
	 
	 $m[] = array( 'yr', 'Year' );		 	// INT
	 $m[] = array( 'brid', 'BranchID' ); 	// INT | BranchID = Parent
	 
	 $m[] = array( 'cc', 'CountryCode' ); 	// INT | ISO 3166 ID#
	 $m[] = array( 'n', 'ShortName' ); 		// CHAR(8)
	 
	 $m[] = array( 'ts', 'TimeStamp' ); 	// TIMESTAMP
	 $m[] = array( 'jd', 'JsonData' ); 		// TEXT
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
	 
	 $this->loadMap( CoeMedFiPOWDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiPOWDAO{}  
