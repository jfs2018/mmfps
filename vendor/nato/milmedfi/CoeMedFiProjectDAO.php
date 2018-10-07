<?php
/**
 * CoeMedFiProjectDAO
 * 
 * NATO / CoeMed / Finance / ProjectDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-08-28
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiProjectDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_prj'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a', 'Active' ); 		// TINYINT(1)
	 $m[] = array( 't', 'ItemTypeID' );		// INT | 328
	 
	 $m[] = array( 'scid', 'SubClassID' ); 	// INT, e.g. #200,#201 for events...
	 
	 $m[] = array( 'yr', 'Year' ); 		// INT | Year '18'
	 
	 $m[] = array( 'brid', 'BranchID' ); 	// INT | BranchID
	 $m[] = array( 'pid', 'PowID' ); 		// INT | POW ID = Parent
	 
	 $m[] = array( 'evt', 'EventID' ); 		// INT | EREG EventID# (or 0)
	 
	 $m[] = array( 'cc', 'CountryCode' ); 	// INT | ISO 3166 ID#
	 $m[] = array( 'n', 'ShortName' ); 		// CHAR(24) plaintext
	 
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
	 
	 $this->loadMap( CoeMedFiProjectDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiProjectDAO{}  
