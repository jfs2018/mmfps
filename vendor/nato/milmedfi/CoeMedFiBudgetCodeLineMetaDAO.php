<?php
/**
 * CoeMedFiBudgetCodeLineMetaDAO
 * 
 * NATO / CoeMed / Finance / BudgetCodeLineMetaDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08
 * @lastmod 2018-09-10 11:40 | _bcode_meta lesz a TableName
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiBudgetCodeLineMetaDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_bcode_meta'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'bc', 'BudgetCode' ); 	// INT(11)
	 $m[] = array( 'bl', 'BudgetLineID' );	// INT(11) | #ID of a BudgetCode row ('budget line')
	 
	 $m[] = array( 'typ', 'ItemTypeID' ); 	// INT(11) | Assigned Item
	 $m[] = array( 'k', 'ItemID' ); 		// INT(11)
	 
	 $m[] = array( 'v', 'JsonData' ); 		// TINYTEXT ! max of 255 excluding " chars!
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
	 
	 $this->loadMap( CoeMedFiBudgetCodeLineMetaDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiBudgetCodeLineMetaDAO{}  
