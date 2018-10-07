<?php
/**
 * CoeMedFiBranchDAO
 * 
 * NATO / CoeMed / Finance / BranchDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-09
 * @lastmod 2018-07-09 12:49
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiBranchDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_branch'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a', 'Active' ); 		// TINYINT(1)
	 $m[] = array( 't', 'ItemTypeID' );		// INT | általában fix #772 ('Szervezeti Egység')
	
	 //$m[] = array( 'scid', 'SubClassID' ); // INT
	 //$m[] = array( 'pid', 'BranchID' ); 	 // INT | sztem ezeket itt nem használjuk
	 
	 $m[] = array( 'cc', 'CountryCode' ); 	// INT | ISO 3166 ID# -> na ez fontos hogy az egység melyik országban van!
	 
	 $m[] = array( 'n', 'ShortName' ); 		// CHAR(8) pl. SB TRB DIR...
	 
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
	 
	 $this->loadMap( CoeMedFiBranchDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiBranchDAO{}  
	
