<?php
/**
 * CoeMedFiRequestDAO
 * 
 * NATO / CoeMed / Finance / RequestDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-10
 * @lastmod 2018-09-20 nr (20180910 ApprAmount 20180920 DirtyFlag)
 * 
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiRequestDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_req'); // map|table
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 
	 $m[] = array( 'a',  'Active' ); 		// TINYINT(1)
	 
	 $m[] = array( 'd',  'DirtyFlag' ); 		// TINYINT(1)
	 
	 $m[] = array( 'ci', 'Circle' );		// INT | 3,5,8 ügykör ("gyûjtõ")
	 $m[] = array( 'rq', 'ReqNum' );		// INT | 18 ...
	 $m[] = array( 's_rq', 'SubReqNum' );	// CHAR(4) e.g. 5-11A/2018
	 
	 $m[] = array( 'yr', 'Year' );			// INT | 18 ...
	 
	 $m[] = array( 'apramt', 'ApprAmount' );		// DECIMAL 10,2 Approved Amount
	 $m[] = array( 'rsdamt', 'ResidualAmount' );	// DECIMAL 10,2 Residual - calculated maradványösszeg
	 $m[] = array( 'sumexp', 'TotalExp' );			// DECIMAL 10,2 Total Expenditures - calculated SUM()
	 
	 $m[] = array( 'curr', 'Currency' ); 			// INT | ISO4217
	 
	 $m[] = array( 'n',  'ShortName' ); 	// INT
	 
	 $m[] = array( 'uid', 'UserID' ); 		// INT | uid: who stored this request (!not -eq to Branch:)) | for only evtlog purpose
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
	 
	 $this->loadMap( CoeMedFiRequestDAO::mapping() ) ;
	 
	 //
	}//#__construct()

	//	

}//#CoeMedFiRequestDAO{}
