<?php
/**
 * CoeMedFiSponsoringNationDAO
 * 
 * NATO / CoeMed / Finance / SponsoringNationDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-07-11 12:01
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiSponsoringNationDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_sn'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11) ISO 3166 ID#
	 
	 $m[] = array( 'a', 'Active' ); 		// TINYINT(1)
	 $m[] = array( 'ul', 'UseLeftover' );	// TINYINT(1) Use:1 Retain:0
	 
	 $m[] = array( 'sp3', 'SP3' ); 			// CHAR(4) NATO STANAG 1059 Country Code
	 $m[] = array( 'n', 'CountryName' ); 	// CHAR(24) Plain text
	 
	 $m[] = array( 'curr', 'Currency' ); 	// INT | ISO 4217
	 $m[] = array( 'amtnow', 'AmountNow' );	// DECIMAL(10,2)
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
	 
	 $this->loadMap( CoeMedFiSponsoringNationDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiSponsoringNationDAO{}  
