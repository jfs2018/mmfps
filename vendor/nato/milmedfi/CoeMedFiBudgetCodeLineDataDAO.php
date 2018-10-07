<?php
/**
 * CoeMedFiBudgetCodeLineDataDAO
 * 
 * NATO / CoeMed / Finance / BudgetCodeLineDataDAO (c) 2018... Jasmine FS Kft.
 *
 * @author Robert
 * @start   2018-07-18
 * @lastmod 2018-07-18 16:08
 * 
 */

namespace Nato\Coemed;

use \Jazz\Core\JazzCoreDAO;

class CoeMedFiBudgetCodeLineDataDAO extends \Jazz\Core\JazzCoreDAO{
	

	//
	//
	public static function mapping(){
	 //
	 $m = array('#__natomm_'); // map|table _ + NUMERICAL budget code
	 //
	 $m[] = array( 'id', 'ID' ); // INT(11)
	 $m[] = array( 'a', 'Active' ); // INT(11)
	 $m[] = array( 't', 'ItemTypeID' ); // INT(11)
	 $m[] = array( 'k', 'Key' ); // char(3) helper text 

	 $m[] = array( 'yr', 'Year' ); // INT | kis year: csak '18' nem 2018

	 $m[] = array( 'dt', 'Date' ); // DATE

	 $m[] = array( 'req', 'RequestID' ); // INT(11)
	 $m[] = array( 'prjid', 'ProjectID' ); // INT(11)
	 $m[] = array( 'pow', 'POWID' ); // INT(11)
	 $m[] = array( 'brid', 'BranchID' ); // INT(11)
	 $m[] = array( 'pid', 'PartnerID' ); // INT(11)

	 $m[] = array( 'pm', 'PaymentMethode' ); // INT(11)
	 
	 $m[] = array( 'invnum', 'InvoiceNumber' ); // CHAR(32)
	 $m[] = array( 'invamt', 'InvoiceAmount' ); // DECIMAL(12,2)
	 $m[] = array( 'invcur', 'InvoiceCurrency' ); // INT
	 
	 $m[] = array( 'amt', 'BookedAmount' ); // DECIMAL(12,2)
	 $m[] = array( 'cur', 'BookedCurrency' ); // INT
	 
	 $m[] = array( 'xrate', 'XRate' ); // DECIMAL(12,8) eXchange Rate
	 
	 $m[] = array( 'uid', 'InsertedByUID' ); // INT(11) | INSERTED BY
	 $m[] = array( 'ts', 'TimeStamp' ); // 
	 
	 $m[] = array( 'subj', 'Subject' ); 		// CHAR(128) -- with JSON endoded " and \u.... !! recommended length: max 80
	 //
	 return $m;
	 //
	}//#mapping()
	
	
	//
	//
	//
	public function __construct( $yc = null, $ptfix = "700000" ){
	 //
	 
	 if( isset($yc) ){ $this->__yc = $yc; }
	 
	 $this->__dirty = 0;
	 
	 //print_r( $ptfix ) ;
	 
	 $this->loadMap( CoeMedFiBudgetCodeLineDataDAO::mapping(), $ptfix ) ;
	 
	 //
	}//#__construct()

	//	

}//#CoeMedFiBudgetCodeLineDataDAO{}
