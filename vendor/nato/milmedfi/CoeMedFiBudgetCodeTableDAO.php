<?php
/**
 * CoeMedFiBudgetCodeTableDAO
 * 
 * NATO / CoeMed / Finance / BudgetCodeTableDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-09-12 12:14
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiBudgetCodeTableDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_bctable'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'bc', 'BudgetCode' ); // INT(11)
	 $m[] = array( 'yr', 'Yr' ); // TINYINT(2)
	 
	 $m[] = array( 'oest', 'OriginalEstimate' ); 	// DECIMAL(10,2)
	 $m[] = array( 'aest', 'AuthorisedEstimate' );	// DECIMAL(10,2) | Authorised: NATO English(EN) -but in US,CA english: authorized
	 
	 $m[] = array( 'expe', 'Expenditure' ); 		// DECIMAL(10,2)
	 $m[] = array( 'comm', 'Commitment' ); 			// DECIMAL(10,2)
	 
	 $m[] = array( 'avail', 'Available' ); 			// DECIMAL(10,2) -- Available Balance
	 $m[] = array( 'perc', 'Percent' ); 			// DECIMAL(10,2) -- Percent-of-Usage
	 
	 $m[] = array( 'curr', 'Currency' ); 	// INT(11) -- Currency ISO 4217
	 $m[] = array( 'ch', 'ChapterID' ); 	// DECIMAL(10,2) -- ParentID# 0,ChapterID
	 
	 $m[] = array( 'd', 'DirtyFlag' ); // Obsolete! TINYINT(1) // nem itt kezeljuk... hanem felso szinten (BcDefDAO)	 
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
	 
	 $this->loadMap( CoeMedFiBudgetCodeTableDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiBudgetCodeTableDAO{}  
