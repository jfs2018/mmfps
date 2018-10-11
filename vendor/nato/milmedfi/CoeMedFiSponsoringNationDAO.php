<?php
/**
 * CoeMedFiSponsoringNationDAO
 * 
 * NATO / CoeMed / Finance / SponsoringNationDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-10-11 14:41
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
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'yr', 'Yr' ); // TINYINT Yr e.g. '18
	 $m[] = array( 'cc', 'CC' ); // INT(11) ISO 3166 ID#
	 
	 $m[] = array( 'a', 'Active' ); 		// TINYINT(1) | Active in 'this' year?
	 
	 $m[] = array( 'ul', 'UseLeftover' );	// TINYINT(1) Use:1 Retain:0
	 
	 $m[] = array( 'sp3', 'SP3' ); 			// CHAR(4) NATO STANAG 1059 Country Code
	 $m[] = array( 'n', 'CountryName' ); 	// CHAR(24) Plain text
	 
	 $m[] = array( 'curr', 'Currency' ); 	// INT | ISO 4217
	 
	 $m[] = array( 'cf1a', 'Call1Amount' );	// DECIMAL(10,2) Call for Fund part 1
	 $m[] = array( 'rf1a', 'Received1Amount' );	// DECIMAL(10,2) Received Fund part 1
	 $m[] = array( 'cf2a', 'Call2Amount' );	// DECIMAL(10,2) 
	 $m[] = array( 'rf2a', 'Received2Amount' );	// DECIMAL(10,2)
	 
	 $m[] = array( 'cf1d', 'Call1Date' );	// DATE
	 $m[] = array( 'rf1d', 'Received1Date' ); 
	 $m[] = array( 'cf2d', 'Call2Date' ); 
	 $m[] = array( 'rf2d', 'Received2Date' ); 
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
