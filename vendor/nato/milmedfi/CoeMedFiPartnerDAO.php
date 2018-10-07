<?php
/**
 * CoeMedFiPartnerDAO
 * 
 * NATO / CoeMed / Finance / PartnerDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-08-30 15:35
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiPartnerDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_partners'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a', 'Active' ); 		// TINYINT(1)
	 $m[] = array( 't', 'ItemTypeID' );		// INT | 328
	 //$m[] = array( 'scid', 'SubClassID' ); 	// INT
	 
	 $m[] = array( 'pid', 'ParentID' ); 	// INT | AuxID : ID in Aux sys. (ha mást nem, meta/kapcs. táblán keresztül)
	 
	 $m[] = array( 'xsys', 'AuxSysID' ); 	// INT | Auxiliary System or Type ID + 2018-08-30
	 
	 $m[] = array( 'cc', 'CountryCode' ); 	// INT | ISO 3166 ID#
	 $m[] = array( 'first8', 'First8' ); 	// CHAR(8)
	 
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
	 
	 $this->loadMap( CoeMedFiPartnerDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiPartnerDAO{}  
