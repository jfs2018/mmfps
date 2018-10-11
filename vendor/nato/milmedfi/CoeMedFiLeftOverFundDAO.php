<?php
/**
 * CoeMedFiLeftOverFundDAO
 * 
 * NATO / CoeMed / Finance / CoeMedFiLeftOverFundDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-10-11
 * @lastmod 2018-10-11 10:41
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiLeftOverFundDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_the_lof'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11) ISO 3166 ID#
	 $m[] = array( 'a', 'Active' ); // TINYINT(1)
	 
	 $m[] = array( 'yr', 'Yr' ); // TINYINT Yr e.g. '18
	 $m[] = array( 'sn', 'SN' ); // INT(11) ISO 3166 ID# Sponsoring Nations ID (cc)
	 
	 $m[] = array( 'xid', 'MetaID' ); // INT(11)
	 
	 $m[] = array( 'plus', 'Carry' );	// DECIMAL(10,2) carry over starts the Year
	 $m[] = array( 'minus', 'Usage' );	// DECIMAL(10,2) usage do minus
	 
	 $m[] = array( 'curr', 'Currency' ); 	// INT | ISO 4217
	 
	 $m[] = array( 'uid', 'UserID' ); 		// INT | uid: who stored
	 $m[] = array( 'ts', 'TimeStamp' ); 	// TIMESTAMP
	 
	 $m[] = array( 'jd', 'JsonData' ); 		// TINYTEXT(255) Short! Notice
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
	 
	 $this->loadMap( CoeMedFiLeftOverFundDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiLeftOverFundDAO{}
	