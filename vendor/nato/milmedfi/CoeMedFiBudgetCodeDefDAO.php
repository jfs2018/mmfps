<?php
/**
 * CoeMedFiBudgetCodeDefDAO
 * 
 * NATO / CoeMed / Finance / BudgetCodeDefDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-08 size 911 B :)
 * @lastmod 2018-09-14
 *
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiBudgetCodeDefDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_bcode_def'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a', 'Active' ); // INT(11)
	 
	 $m[] = array( 'ch', 'ChapterID' ); // INT

	 $m[] = array( 'd', 'DirtyFlag' ); // TINYINT(1) // itt jelezzuk kozpontilag hogy kalkulacios adat modosult!
	 
	 $m[] = array( 'jd', 'JsonData' ); 	// TINYTEXT
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
	 
	 $this->loadMap( CoeMedFiBudgetCodeDefDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	
	
}//#CoeMedFiBudgetCodeDefDAO{}  
