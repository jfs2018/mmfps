<?php
/**
 * Táblázatkezelő Komponens (c) 2018 JFS
 *
 * @start 2018-07-16 14:30:00 | table_base iHI
 * @lastmod 2018-10-08 17:02 nr
 */
 
//
if( !isset($wstoken) )die('Fatal runtime error: WS token failed');
if( $lvl <1 )die('Fatal runtime error: no GRANT to call this module.'); // <1 =die() tehát 'inside': MINIMUM logged in user kell legyen!
// 
	
	//
	$csi = array( 348=>'Ft', 826 => '&pound;', 840 => '$', 978=>'&euro;' ); // Currency Signs

	//
	$arr_error = null; // ! by default: NULL
	
	//
	$reqCacheDAO = null;	
	
	$arr_reqPres = array(); // array of reqID presence | ReqID jelenlét detektálás (exp,com)
	
	$sumof_exp_req = array(); // SUM() of Expenditures grouped by RequestID's  | [rq] => [ 0:amt,1:cur ]
	$items_exp_req = array(); // Matrix: array() Items by rqID and bcRowID's [rq] => [rwid] => [0:amt,1:cur]
	//
	
	//
	$num_exp_foot_amt = 0;
	$num_com_foot_amt = 0;
	
	//
	  $basic_currency_id  = 978; // 2018-09-10 | default: EUR
	  $basic_currency_txt = null; 
	
	  $jMetaDAO = $jazzEntityManager->get('JazzMeta') ;
	  
	  $jMetaDAO->clearMe();
	  $jMetaDAO->setItemTypeID( 899 ) ; // 'currency_of_booking'
	  $jMetaDAO->loadMe();
	  //
	  
	  if( (int)$jMetaDAO->getValue('MetaValue') >0 )
	  if( (int)$jMetaDAO->getValue('MetaValue') !=978 ){ $basic_currency_id = (int)$jMetaDAO->getValue('MetaValue') ; }
	  
	  //
	  $jCurrencyDAO = $jazzEntityManager->get('JazzISO4217DAO') ;
	  
	  $jCurrencyDAO->clearMe();
	  $jCurrencyDAO->setID( $basic_currency_id );
	  
	  if( $basic_currency_id!=978 ){
	   //
	   $jCurrencyDAO->loadMe();
	  
	   $basic_currency_txt = $jCurrencyDAO->getValue('Code');
	  }
	  else{
	   $jCurrencyDAO->setValue( 'Code', 'EUR' );
	   
	   $basic_currency_txt = "EUR"; // spare an SQL call :)
	  }
	//

//

	//DATE
	//
	if( !isset($fi_sel_yr) ){
	  if( isset($_SESSION["chg_yr"]) ){
		$fi_sel_yr = (int)$_SESSION["chg_yr"]-2000;
	  }
	  else
		$fi_sel_yr = (int)date('y') ;
	}
	
	//
	// BudgetCodeID alias 'bcid'
	//
	$bcid = 0 ; 
	
	if( isset($_REQUEST['id']) )if( ((int)$_REQUEST['id'] >700000) ) $bcid = (int)$_REQUEST['id'] ;
	//


	//
	//
	$bcDefsDAO = null;
	
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeDefDAO.php' ) ;
	
	$bcDefsDAO = new \Nato\Coemed\CoeMedFiBudgetCodeDefDAO( $yc ) ;
	//
//
?>

<?php
//
 if( $bcid < 700000 ){
	//	
	//
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeTableDAO.php' ) ;
	
		$bctabDAO = new \Nato\Coemed\CoeMedFiBudgetCodeTableDAO( $yc ) ; // mod 2018-09-12 +fields: bc,yr | 'bc+yr' instd. 'id'
	//
	
	//
	//var_dump( $_REQUEST ) ;
	
	if( isset($_REQUEST['bcode']) )
	if( (int)$_REQUEST['bcode'] >700000 ){
	  //
	  $bcid = (int)$_REQUEST['bcode'] ;
	  
	  if( isset($_REQUEST['bname']) && (strlen(trim($_REQUEST['bname']))>0) ){
	    //
		
		//
		// a.
		//
		$bcDefsDAO->clearMe() ;
		
		$bcDefsDAO->setID( $bcid ) ;
		
		$bcDefsDAO->setValue('JsonData', jsonobject4SQL( array( 'n'=> mysqli_real_escape_string( $yc, $_REQUEST['bname'] ) ) ) ) ;
		
		$bcDefsDAO->insertMe() ;
		
		//
		// b.
		//
		 
		$chid = round( $bcid / 10000, 0 ).'0000';
		//
		$bctabDAO->clearMe() ;
		
		$bctabDAO->setValue( 'BudgetCode', $bcid ) ;
		$bctabDAO->setValue( 'Yr', $fi_sel_yr ) ;
		
		$bctabDAO->setValue('Currency', 978 ) ;
		$bctabDAO->setValue('ChapterID', (int)$chid ) ;
		
		$bctabDAO->insertMe() ;
		
		//
		// c.
		//

		$si1 = "
CREATE TABLE `fi_natomm_700000` (
  `id` int(11) NOT NULL,
  `a` tinyint(4) NOT NULL DEFAULT '1',
  `t` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'line inserted by',
  `yr` int(11) NOT NULL DEFAULT '0',
  `k` char(4) NOT NULL COMMENT 'key: est,com,exp',
  `dt` date NOT NULL COMMENT 'date for this line',
  `req` int(11) NOT NULL DEFAULT '0' COMMENT 'reqID',
  `prjid` int(11) NOT NULL DEFAULT '0' COMMENT 'ProjectCode (ex: remark)',
  `pow` int(11) NOT NULL DEFAULT '0',
  `brid` int(11) NOT NULL DEFAULT '0' COMMENT 'BranchID',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT 'PartnerID#',
  `pm` int(11) NOT NULL DEFAULT '0' COMMENT 'method',
  `invnum` char(32) NOT NULL COMMENT 'char(32)',
  `invamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `invcur` int(11) NOT NULL DEFAULT '978',
  `amt` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Booked',
  `cur` int(11) NOT NULL DEFAULT '978',
  `xrate` decimal(12,8) NOT NULL DEFAULT '1.00000000',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subj` CHAR(255) NOT NULL COMMENT 'subj. of procurement'
  
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Budget Code #700000';
";

		//
		$yc->query( str_replace( '700000', $bcid , $si1 ) ) ;

		//
		//
		$si2 = "
ALTER TABLE `fi_natomm_700000`
  ADD PRIMARY KEY (`id`),
  ADD KEY `a` (`a`),
  ADD KEY `yr` (`yr`),
  ADD KEY `t` (`t`),
  ADD KEY `pid` (`pid`),
  ADD KEY `pm` (`pm`),
  ADD KEY `cur` (`cur`),
  ADD KEY `brid` (`brid`),
  ADD KEY `pow` (`pow`),
  ADD KEY `req` (`req`),
  ADD KEY `prjid` (`prjid`);
";

		//
		$yc->query( str_replace( '_700000', '_'.$bcid , $si2 ) ) ;


		//
		//
		$si3=" ALTER TABLE `fi_natomm_700000` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT; ";

		//
		$yc->query( str_replace( '_700000', '_'.$bcid , $si3 ) ) ;
		
		//
		//
		$arr_error = array( 'Success.',
							JAZZ_ITEMDEF_EVENT_MESSAGE,
							'BudgetCode #'.$bcid.' added and configuration have set.',
							[], 
							0
							);
		
		
		// d.
		//
		$_REQUEST['bcode']='';
		$_REQUEST['bname']='';
		//
	  }
	  else{
		//
		$arr_error = array( 'Data check failed.',
							JAZZ_ITEMDEF_EVENT_MESSAGE_ERROR,
							'Please determine a name for BudgetCode #'.$bcid,
							[ 'bcode' ], 
							2
							);
	  }
	  //
	}
	else
	{
		//
		$arr_error = array( 'Data check failed.',
							JAZZ_ITEMDEF_EVENT_MESSAGE_ERROR,
							'Invalid BudgetCode (<700000)',
							[ 'bcode' ], 
							1
							);
		//
		$_REQUEST['bcode']='';
	}	
	//
	
	
	//
	// ... =============================== A NYITÓKÉP:   TÁBLÁZAT   =============================== ... 
	
	
	//
	$lr_bcDefs = new \Jazz\Core\JazzDAOList( $yc,
										 $bcDefsDAO,
										 [ [ 'Active', '=', 1 ] ],
										 [ ['ID',1] ]
										);

	$li_bcDefs = $lr_bcDefs->getRowsArrayByID( [ 'ID', 'DirtyFlag', 'JsonData' ] ) ;
	
	

	//
	$lr_bcTable = new \Jazz\Core\JazzDAOList( $yc,
										 $bctabDAO,
										 [
										  ['Yr', '=', $fi_sel_yr]
										 ],
										 [ ['BudgetCode',1] ]
										);
	
	$li_bcTable = $lr_bcTable->getRowsArrayByID(  [ 'ID', 'BudgetCode', 'OriginalEstimate', 'AuthorisedEstimate', 
													'Expenditure', 'Commitment', 'DirtyFlag', 
													'Currency', 'ChapterID' ] ) ;
	
	//echo 'li_bcTable: '.$fi_sel_yr.json_encode( $li_bcTable );	//	exit(0);
	//

	$mtx_bcTable = array();
	$w_arr = null;
	foreach( $li_bcTable as $bc )
	{
	 //
	 $w_arr = array();
	 
	 $w_arr['dbID'] = $bc['ID']; // !! és ez nem azonos a BCodeID-vel !!
	 
	 $w_arr['orig'] = $bc['OriginalEstimate'];
	 $w_arr['auth'] = $bc['AuthorisedEstimate'];
	 $w_arr['exp'] = $bc['Expenditure'];
	 $w_arr['com'] = $bc['Commitment'];
	 $w_arr['blnc'] = 0.0;
	 $w_arr['perc'] = 0.0;
	 $w_arr['d'] = (int)$bc['DirtyFlag'];
	 
	 $mtx_bcTable[ $bc['BudgetCode'] ] = $w_arr;
	 
	 $w_arr = null;
	 //
	}
	
	//print_r( $mtx_bcTable ) ;
	//die('');
	
	if( !isset($_REQUEST['bcode']) ) 	$_REQUEST['bcode']='';
	if( !isset($_REQUEST['bname']) ) 	$_REQUEST['bname'] ='';
	//
	
?>
<div class="card m-auto p-1 col-12 col-md-10 col-xl-10 border-0">
	<div class="card-body m-0 p-0">

		<p class="col-12 col-sm-12 col-lg-8 fontsiz12 fontbold fg-oliv-v2">
			<span class="fa fa-bars fa-fw"></span> Budget Codes
			
			<span class="pl-5 fontsiz9">
			 <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="fontnormal-i fontsiz9-i"><span class="fa fa-refresh fa-fw fontsiz11-i"></span> refresh calculation</a>
			 &nbsp;
			 &nbsp;
			 &nbsp;
			</span>
			
			<button class="btn btn-xs jazz-btn-cyan fontsiz10-i px-2 btn-win-open float-right" data-id="new_bc">
				<span class="fa fa-plus fontsiz8-i fa-1x"></span> <?php echo $arr_lang['SYS_BTN_NEW']?>... 
			</button>
			
		</p>
				
		<?php
		//
			// Hiba - ha van - megjelenítése
			//
			display_jazz_alert_box( $arr_error ) ; // lásd @doc JAP 6.5.3  @src __commonfsurf.php (common-front-surface functions)
		//
		?>	
		
		<div class="row m-0 mb-5 bg-gray-lt6 col-12 col-sm-12 col-lg-8 border border-radius-xs border-jazz-oliv-lt-i display-none" id="win_new_bc">
			<div class="col m-0 mb-4 p-1">
				<p class="my-2 p-0 fontsiz10 fg-oliv-lt">
					<span class="fa fa-plus fontsiz8-i fa-1x"></span>
						Add new Budget Code
					<span class="fa fa-times fontsiz8-i fg-gray7 p-1 btn-win-close mousexhand float-right" data-id="new_bc"></span>
				</p>
				
				<hr>
				
				<form class="form" role="form" id="frm_new_bc" method="POST" action="<?php echo site_url().site_dir() ; ?>?r=budget-ctrl">
					<input type="hidden" name="new" value="1">
					
					  <div class="form-group row">
						<div class="col-3 fontsiz9-i fontbold">Code:</div>
						<div class="col-2">
						 <input type="text" name="bcode" id="id_new_bcode" class="form-control form-control-sm" value="<?php echo $_REQUEST['bcode'] ; ?>">
						</div>
					  </div>
					
					  <div class="form-group row">
						<div class="col-3 fontsiz9-i fontbold">Name:</div>
						<div class="col-9">
						 <input type="text" name="bname" id="id_new_bname" class="form-control form-control-sm" value="<?php echo $_REQUEST['bname'] ; ?>">
						</div>
					  </div>
					  
					  <div class="row">
						<div class="col text-right">
							<button class="btn btn-xs jazz-btn-success">
								<span class="fa fa-check fontsiz10-i"></span>
								&nbsp;
								<?php echo $arr_lang['SYS_BTN_SAVE']?>
							</button>
						</div>
					  </div>
				</form>
			</div>
		</div>
		
		<!-- hr -->
		
		<!-- br -->

		<!-- ReCalculation if any DirtyFlag is on -->
		<?php
		
		/*** BudgetCode Line Data DAO ***/
		if( !isset( $bcdataDAO ) ){
		 //
		 require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeLineDataDAO.php' ) ;
		
		 $bcdataDAO = null; // new \Nato\Coemed\CoeMedFiBudgetCodeLineDataDAO( $yc, 0 ) ; // !! related on bcid :)
		 //
		}
		
		//
		//
		$lr_bcdata = null; 
		$mtx_sums = array( 904 => 0, 905 => 0, 906 => 0, 907 => 0, 908 => 0, ) ;
		
		$bcid = 0;
		
		//
		 // old: foreach($li_bcDefs as $def) 
		 //      if( $def['DirtyFlag']==1 )
		//       { ... $bcid = (int)$def['ID'] ; ...
		
		
		//
		foreach($mtx_bcTable as $bcid => $mtxRow)
		if( $mtxRow['d'] ==1 )
		{
		 //
		 $bcdataDAO = new \Nato\Coemed\CoeMedFiBudgetCodeLineDataDAO( $yc, $bcid ) ;
		 
		 $mtx_sums[0] = (int)$bcid;
		 
		 //
		 // 904. est
		 $lr_bcdata = new \Jazz\Core\JazzDAOList( $yc,
										 $bcdataDAO,
										 [
										  [ 'Active', '=', 1 ],
										  [ 'Year', '=', $fi_sel_yr ],
										  [ 'ItemTypeID', '=', 904 ]
										 ]
										);

		 $mtx_sums[904] = $lr_bcdata->sum( 'BookedAmount' ) ;
		
		 //
		 // 905. mod
		 $lr_bcdata = null;
		 $lr_bcdata = new \Jazz\Core\JazzDAOList( $yc,
										 $bcdataDAO,
										 [
										  [ 'Active', '=', 1 ],
										  [ 'Year', '=', $fi_sel_yr ],
										  [ 'ItemTypeID', '=', 905 ]
										 ]
										);

		 $mtx_sums[905] = $lr_bcdata->sum( 'BookedAmount' ) ;
		
		 //
		 // 906. exp
		 $lr_bcdata = null;
		 $lr_bcdata = new \Jazz\Core\JazzDAOList( $yc,
										 $bcdataDAO,
										 [
										  [ 'Active', '=', 1 ],
										  [ 'Year', '=', $fi_sel_yr ],
										  [ 'ItemTypeID', '=', 906 ]
										 ]
										);

		 $mtx_sums[906] = $lr_bcdata->sum( 'BookedAmount' ) ;
		
		 //
		 // 907. com
		 $lr_bcdata = null;
		 $lr_bcdata = new \Jazz\Core\JazzDAOList( $yc,
										 $bcdataDAO,
										 [
										  [ 'Active', '=', 1 ],
										  [ 'Year', '=', $fi_sel_yr ],
										  [ 'ItemTypeID', '=', 907 ]
										 ]
										);

		 $mtx_sums[907] = $lr_bcdata->sum( 'BookedAmount' ) ;
		
		 //
		 // 908. rev
		 $lr_bcdata = null;
		 $lr_bcdata = new \Jazz\Core\JazzDAOList( $yc,
										 $bcdataDAO,
										 [
										  [ 'Active', '=', 1 ],
										  [ 'Year', '=', $fi_sel_yr ],
										  [ 'ItemTypeID', '=', 908 ]
										 ]
										);

		 $mtx_sums[908] = $lr_bcdata->sum( 'BookedAmount' ) ;
		 
		 //
		 if( !isset($mtx_bcTable[ $bcid ]) ){
		  //
		  $mtx_bcTable[ $bcid ] = array();
		  
		  $mtx_bcTable[ $bcid ]['dbID'] = 0 ;
		  //
		 }
		 
		 $mtx_bcTable[ $bcid ]['orig'] = $mtx_sums[904] ;
		 $mtx_bcTable[ $bcid ]['auth'] = $mtx_sums[904] + $mtx_sums[905] ; // = orig + mods
		 $mtx_bcTable[ $bcid ]['exp'] = $mtx_sums[906] ;
		 $mtx_bcTable[ $bcid ]['com'] = $mtx_sums[907] ;
		 $mtx_bcTable[ $bcid ]['rev'] = $mtx_sums[908] ;
		 
		 //print_r( $mtx_sums ) ;
		 
		 //
		 $bctabDAO->clearMe() ;
		 $bctabDAO->setID( $mtx_bcTable[ $bcid ]['dbID'] ) ;
		 
		 $bctabDAO->setValue( 'BudgetCode', $bcid ) ;
		 
		 $bctabDAO->setValue( 'OriginalEstimate', $mtx_sums[904] ) ;
		 $bctabDAO->setValue( 'AuthorisedEstimate', $mtx_sums[904] + $mtx_sums[905] ) ;
		 
		 $bctabDAO->setValue( 'Expenditure', ( $mtx_sums[908] >0 ? $mtx_sums[908] : $mtx_sums[906] ) ) ;
		 
		 $bctabDAO->setValue( 'Commitment', $mtx_sums[907] ) ;
		 
		 $bctabDAO->setValue( 'DirtyFlag', 0 ) ; // !!
		 
		 $bctabDAO->setValue( 'ChapterID', substr( $bcid,0,5 ).'0' ) ;
		 
		 $bctabDAO->saveMe() ; 
		 // !! a saveMe() _ELŐTT_ hasznos a loadMe() ha nem vagyunk a dologban biztosak! ID-t mindig kell adni. az dönti el hogy INSERT-e?
		 
		 //
		 //
		  //$bcDefsDAO->clearMe() ;
		  //$bcDefsDAO->setID( $bcid ) ;
		  //$bcDefsDAO->setValue( 'DirtyFlag', 0 ) ; // obso.! 20181002
		  //$bcDefsDAO->updateMe() ;
		 //
		 		 
		 $bcdataDAO = null; // BC Data DAO-t is dobjuk el 
		 //
		 
		}
		//
		$bcid = 0 ;
		//
		?>
		
		
		<!-- TABLE ROWS | from v0.4+ -->
		<table class="table fontsiz10">
		<thead>
		 <th class="text-centered fontsiz9-i"> Code<br>&nbsp; </th>
		 <th class="text-centered fontsiz9-i"> Chapter/Item/Sub-item<br>title </th>
		 <th class="text-centered fontsiz9-i"> Original<br>Estimate </th>
		 <th class="text-centered fontsiz9-i"> Auth.<br>Estimate </th>
		 <th class="text-centered fontsiz9-i pb-2"> Expenditure </th>
		 <th class="text-centered fontsiz9-i pb-2"> Commitment </th>
		 <th class="text-centered fontsiz9-i"> Available<br>balance </th>
		 <th class="text-centered fontsiz9-i"> <br>% of usage </th>
		 <th class="text-right fontnormal"> <br>15% </th>		 
		</thead>
		<tbody>
	<?php
		$csi_this = $csi[ $basic_currency_id ] ;
		
		$tr = null;
		
		foreach($li_bcDefs as $def)
		{
		 //
		 //
		 $td_id = '<td class="';
		
		 if( substr($def['ID'],2,4)=="0000" ){ $td_id.= ' fg-red-v2 fontbold'; }
		 else if( substr($def['ID'],3,3)=="000" ){ $td_id.= ' fg-gray3 fontbold'; }
		 else if( substr($def['ID'],5,1)=="0" ){ $td_id.= ' fontitalic'; }
		 
		 $td_id.=' text-centered">';
		 //
		 if( strcmp( substr( $def['ID'],5,1) ,"0" )==0 ){
		  //
		   $td_id.= $def['ID'].' </td>' ;
		 }
		 else
		   $td_id.= '<a href="'.site_url().site_dir().'?r=budget-ctrl&id='.$def['ID'].'" class="fg-nato-dk">'.$def['ID'].'</a> </td>' ; 
		 
		 //
		 //
		 $td_name = '<td class="';
		
		 if( substr($def['ID'],2,4)=="0000" ){ $td_name.= ' fg-red-v2 fontbold'; }
		 else if( substr($def['ID'],3,3)=="000" ){ $td_name.= ' fg-gray3 fontbold'; }
		 else if( substr($def['ID'],5,1)=="0" ){ $td_name.= ' fontitalic'; }
		 
		 $td_name.=' pl-3">';
		 //
			$ja = null;
			$ja = json_decode( $def['JsonData'] );
					
			$href_txt = $ja;
					
			if( isset($ja) && isset($ja->{'n'}) ){ $href_txt = $ja->{'n'} ; }
		 
		 //
		 if( strcmp( substr( $def['ID'],5,1) ,"0" )==0 ){
		  //
		   $td_name.= $href_txt.' </td>' ;
		 }
		 else
		   $td_name.= '<a href="'.site_url().site_dir().'?r=budget-ctrl&id='.$def['ID'].'" class="fg-nato-dk">'.$href_txt.'</a> </td>' ; 
		 //
		 
		 //
		 //
		 $td_orig = '<td class="text-right fontsiz10">'.number_format( $mtx_bcTable[ $def['ID'] ]['orig'],2, ',',' ' ).' '.$csi_this.'</td>';
		 
		 //
		 //
		 $td_auth = '<td class="text-right fontsiz10">'.number_format( $mtx_bcTable[ $def['ID'] ]['auth'],2, ',',' ' ).' '.$csi_this.'</td>';
		 
		 //
		 //
		 $td_exp = '<td class="text-right fontsiz10">'.number_format( $mtx_bcTable[ $def['ID'] ]['exp'],2, ',',' ' ).' '.$csi_this.'</td>';
		 
		 //
		 //
		 $td_com = '<td class="text-right fontsiz10">'.number_format( $mtx_bcTable[ $def['ID'] ]['com'],2, ',',' ' ).' '.$csi_this.'</td>';
		 
		 //
		 //
		 $_avail = $mtx_bcTable[ $def['ID'] ]['auth'] - $mtx_bcTable[ $def['ID'] ]['exp'] - $mtx_bcTable[ $def['ID'] ]['com'] ;
		 
		 $td_avail = '<td class="text-right fontsiz10">'.number_format( $_avail,2, ',',' ' ).'</td>';
		 
		 //
		 //
		 $_perc = 0.0;
		 if( $mtx_bcTable[ $def['ID'] ]['auth'] >0 ){
		  //
		  
		  $_perc = ( $mtx_bcTable[ $def['ID'] ]['exp'] + $mtx_bcTable[ $def['ID'] ]['com'] ) / $mtx_bcTable[ $def['ID'] ]['auth'] ;
		  
		 }
		 
		 $td_perc = '<td class="text-right fontsiz10">'.number_format( $_perc*100 ,2, ',',' ' ).'</td>';
		 
		 //
		 //
		 $_amt15 = 0.0;
		 //if( $mtx_bcTable[ $def['ID'] ]['auth'] >0 ) $_amt15 = $mtx_bcTable[ $def['ID'] ]['auth'] * 0.15 ;
		 
		 $td_15 = '<td></td>'; //...
		 //$td_15 = '<td class="text-right fontsiz9">'.number_format( $_amt15 ,2, ',',' ' ).'</td>'; // csak a Chapter-ek eseteben
		
		 //
		 $tr = '<tr>'.$td_id.$td_name.$td_orig.$td_auth.$td_exp.$td_com.$td_avail.$td_perc.( $_amt15>0.0 ? $td_15 : "" ).'</tr>'."\n";
		
		 echo $tr;
		}
	?>
		</tbody>
		</table>
		
<?php 		
/*
		<!-- GRID HEAD | up to v0.3 -->
		//#FOREACH $li_bcDefs | Grid | been Obsolete from v0.4		
*/		
	?>	
		
	</div>
</div>
<br>	
	
<?php	
	/*	
	*/
 }
 else
 {
	//
	// id >700000 | ... TEHÁT AMIKOR VAN RENDES BUDGET CODE azonosítónk :)
	//
 
	/*** BudgetCode Line Definition DAO ***/
	if( !isset( $bcDefsDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeDefDAO.php' ) ;
		$bcDefsDAO = new \Nato\Coemed\CoeMedFiBudgetCodeDefDAO( $yc ) ;
	}
	
	//
	// detektáljuk h küldtek-e módosítást? 2018-07-26 18:20 nr
	
	
	if( isset($_REQUEST['mod_bc_def']) )
	{
	
		$bcDefsDAO->setID( $bcid ) ;
		$bcDefsDAO->loadMe() ;
		
		$j = json_decode( $bcDefsDAO->getJsonData(), true ) ;
	
		$j['n'] = $_REQUEST['bc_def_n'] ; // name
		$j['x'] = trim( $_REQUEST['bc_def_x'] ) ; // extensions
		
		//
		$j['hide']=array();
		
		if( isset($_REQUEST['show_prj']) && ((int)$_REQUEST['show_prj'] ==0) ) $j['hide'][]='prj'; // 20180924
		
		if( isset($_REQUEST['show_pow']) && ((int)$_REQUEST['show_pow'] ==0) ) $j['hide'][]='pow';
		
		if( isset($_REQUEST['show_br']) && ((int)$_REQUEST['show_br'] ==0) ) $j['hide'][]='br';
		
	    //
		$bcDefsDAO->setValue('JsonData', jsonobject4SQL($j) ) ;
		
		$bcDefsDAO->updateMe() ;
	
		unset($_REQUEST['mod_bc_def']); // ...
		
	}
	
	//
	//
	
	/*** BudgetCode Line Data DAO ***/
	if( !isset( $bcdataDAO ) ){
		//
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeLineDataDAO.php' ) ;
		
		$bcdataDAO = new \Nato\Coemed\CoeMedFiBudgetCodeLineDataDAO( $yc, $bcid ) ; // !! related on bcid :)
		//
	}
	
	/*** BudgetCode Line Meta DAO ***/
	if( !isset( $bcmetaDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBudgetCodeLineMetaDAO.php' ) ;
		$bcmetaDAO = new \Nato\Coemed\CoeMedFiBudgetCodeLineMetaDAO( $yc ) ;
	}

	
	/*** REQ DAO ***/
	if( !isset( $reqDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRequestDAO.php' ) ;
		$reqDAO = new \Nato\Coemed\CoeMedFiRequestDAO( $yc ) ;
	}
	/*** PROJ DAO ***/
	if( !isset( $projectDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiProjectDAO.php' ) ;
		$projectDAO = new \Nato\Coemed\CoeMedFiProjectDAO( $yc ) ;
	}
	/*** POW DAO ***/
	if( !isset( $powDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPOWDAO.php' ) ;
		$powDAO = new \Nato\Coemed\CoeMedFiPOWDAO( $yc ) ;
	}
	/*** BRANCH DAO ***/
	if( !isset( $branchDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiBranchDAO.php' ) ;
		$branchDAO = new \Nato\Coemed\CoeMedFiBranchDAO( $yc ) ;
	}

	/*** Partners DAO ***/
	if( !isset( $partnerDAO ) ){
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiPartnerDAO.php' ) ;
		$partnerDAO = new \Nato\Coemed\CoeMedFiPartnerDAO( $yc ) ;
	}
	
	
	/*** REQ Lister ***/
	$lr_reqs = new \Jazz\Core\JazzDAOList( $yc,
												$reqDAO,
												[
													['Active', '=', 1],
													// ['Year', '=', $fi_sel_yr ],
												], 
												[ 
													['ShortName',1] 
												]
											);
											
	$li_reqs = $lr_reqs->getRowsArrayByID( [ 'ID', 'ShortName' ] ) ;
	
	// Disabled Requests: 20181002
	$lr_disa_reqs = new \Jazz\Core\JazzDAOList( $yc, $reqDAO, [ ['Active', '=', 0] ] );
											
	$li_disa_reqs = $lr_disa_reqs->getRowsArrayByID( [ 'ID','ShortName' ] ) ;
	
	
	/*** PROJ Lister ***/
	$lr_projects = new \Jazz\Core\JazzDAOList( $yc,
												$projectDAO,
												[
													['Active', '=', 1],
												], 
												[ 
													['ShortName',1] 
												]
											);
	$li_projs = $lr_projects->getRowsArrayByID( [ 'ID', 'ShortName' ] ) ;
	
	/*** POW Lister ***/
	$lr_pows = new \Jazz\Core\JazzDAOList( $yc,
										   $powDAO,
										   [
										    [ 'Year', '=', (int)$fi_sel_yr ]
										   ], 
										   [ ['ShortName',1] ]
										);								
	$li_pows = $lr_pows->getRowsArrayByID( [ 'ID', 'ShortName', 'JsonData' ] ) ;
	
	/*** BRANCH Lister  ***/
	$lr_branches = new \Jazz\Core\JazzDAOList( $yc,
										 $branchDAO,
										 [
										  [ 'Active', '=', 1 ]
										 ],
										 [ ['ShortName',1] ]
										);
	$li_branches = $lr_branches->getRowsArrayByID( [ 'ID', 'ShortName', 'JsonData' ] ) ;
	
	
	/*** partnerDAO Lister  ***/
	$prtnType = 328; // Supplier/Contractor
	if( ($_REQUEST['id'] >740000) && ($_REQUEST['id'] <750000) ) $prtnType = 330 ; // Customer
	
	$lr_partners = new \Jazz\Core\JazzDAOList( $yc,
										 $partnerDAO, // nr 2018-07-18
										 [
										  [ 'Active', '=', 1 ],
										  [ 'ItemTypeID', '=', $prtnType ]
										 ],
										 [ ['First8',1] ]
										);
	$li_partners = $lr_partners->getRowsArrayByID( [ 'ID', 'ItemTypeID', 'JsonData' ] ) ;
	
	
	//
	
	/*** curncyDAO Lister  ***/
	$lr_crncy = new \Jazz\Core\JazzDAOList( $yc,
										 $curncyDAO = $jazzEntityManager->get('JazzISO4217DAO'), // nr 2018-07-18
										 [
										  /* [ 'ID', 'NOT IN', [348,826,840,978] ] */
										 ],
										 [ ['Code',1] ]
										);
	$li_currency = $lr_crncy->getRowsArrayByID( [ 'ID', 'Code' ] ) ;
	
	$cbx_opts_crncy = "";
	foreach( $li_currency as $cr ){
	 //
	 $cbx_opts_crncy.='<option value=\"'.$cr['ID'].'\">'.$cr['Code'].'</option>';
	}
	
	//
	
	/*** countryDAO Lister  ***/
	$lr_country = new \Jazz\Core\JazzDAOList( $yc, $iso3166DAO = $jazzEntityManager->get('JazzISO3166DAO'), // nr 2018-08-28
					[
					 [ 'ID', '<', 1000 ] 
					],
					[ ['Name',1] ]
					) ;
					 
	$li_country = $lr_country->getRowsArrayByID( [ 'ID','SP3','Name' ] ) ;
	
	
	//
	// DMS: na majd ehhez is kellenek Bean-ek (DAO-k) de most még gyorsan sima SELECT :/ ...	
	//
	$s = " SELECT `id` FROM `".DBPFX."natomm_dms_meta` WHERE `a`=1 AND `t`=642 ORDER BY `id` ASC "; // Aktív Ügykörök
	$rw= null;	
	$r = $yc->query( $s ) ;
	
	$arr_circ = array();
	
	if( $r )
	while( $rw = $r->fetch_array() ){ $arr_circ[] = $rw[0] ; }
	
	$s = null; $r = null; $rw = null; 
	//
	//
	//print_r( $cbx_opts_crncy ) ;
	//
?>
<?php
	//
	$popup_req_id = display_jazz_popup_box_create( '<span class="fg-oliv-i fontbold">Add a new Request</span>', 1 ); // 1:middle size
	?>
	<div class="container-fluid">
					<div class="form" role="form" id="frm_popnew_req">
						<input type="hidden" name="new" value="1">
						<div class="row">
							<div class="col-12 py-1 fontsiz9 fontbold">
								Number:
							</div>
							<div class="col-12">
								<select name="circ" id="id_popnew_req_cbx_circ" class="fontsiz10 p-0 m-0" style="width:40px;">
								<?php
								foreach( $arr_circ as $__ci )
								echo '<option value="'.$__ci.'">'.$__ci.'</option>';
								?>
								</select>

									<span class="fontsiz10">&dash;</span>
									
									<input type="text" name="num" id="id_popnew_req_txt_num" value="" class="fontsiz10 p-0 mr-1" style="width:43px">
									
									<select name="abc" id="id_popnew_req_txt_abc" class="fontsiz10 p-0 m-0" style="width:37px;">
									 <option value=""></option>
							 <?php
							    for( $_ch=ord("A"); $_ch<=ord("Z"); $_ch++ ){
								 //
								 echo '<option value="'.chr($_ch).'" >'.chr($_ch).'</option>';
								 //								 
								}
							 ?>
									</select>

									<span class="fontsiz10">&nbsp;/&nbsp;</span>
									
									<select name="yr"  id="id_popnew_req_cbx_yr" class="fontsiz10 p-0 m-0" style="width:60px;">
									 <?php 
							 for( $_y=date('y'); $_y>=10; $_y-- )
							 {
								echo '<option value="'.$_y.'" '.($fi_sel_yr==$_y ? "selected" : "").'>'.( $_y+2000 ).'</option>';
									 } 
									 ?>
									</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-12 py-1 fontsiz9 fontbold">
								Title:
							</div>
							<div class="col-12">
								<textarea name="txt" rows="1" class="w-100 fontsiz10" id="id_popnew_req_txt_txt"></textarea>
							</div>
						</div>
						
						<div class="row">
							<div class="col-12 py-1 fontsiz9 fontbold">
								Commitment:
							</div>
							<div class="col-2">
								<input type="number" step="any" min="0" name="comm" id="id_popnew_req_txt_comm" value="" class="fontsiz10 p-0 m-0" style="width:73px">
							</div>
							<div class="col-2">
								<select name="cur" class="fontsiz9 px-1" id="id_popnew_req_cbx_cur">
								<option value="978">-</option>
								<?php echo str_replace( '\"','"', $cbx_opts_crncy ) ; ?>
								</select>
							</div>
							<div class="col-8 text-to-right">
							  <button class="btn btn-xs jazz-btn-success popup-req-save">
								<span class="fa fa-check"></span> Save
							  </button>
							</div>
						</div>
					</div>	
	</div>
	<?php
	display_jazz_popup_box_closure( $popup_req_id ) ;
	//
	
	//
	$popup_prj_id = display_jazz_popup_box_create( '<span class="fg-oliv-i fontbold">Add a new Project</span>', 0 );
	?>
	<div class="container-fluid">
					<div class="form" role="form" id="frm_popnew_pa">
						<input type="hidden" name="new" value="1">
						<div class="row">
							<div class="col-12 py-1 fontsiz9 fontbold">
								Project Name:
							</div>
							<div class="col-12">
									
								<input type="text" name="prj" id="id_popnew_prj_txt_prj" value="" class="w-100 fontsiz10 p-0 mr-1" >
								
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-6 py-1 fontsiz9 fontbold">
								POW:
							</div>
							<div class="col-6 py-1 fontsiz9 fontbold">
								<select name="pow" id="id_popnew_prj_cbx_pow" class="fontsiz9-i p-0 m-0 w-100">
								<option value="0">-</option>
								<?php
								foreach( $li_pows as $pow )
								{
								echo '<option value="'.$pow['ID'].'">'.$pow['ShortName'].'</option>'."\n";
								}
								?>
								</select>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-6 py-1 fontsiz9 fontbold">
								ERM EventID# :
							</div>
							<div class="col-6 py-1 fontsiz9 fontbold">
									
								<input type="text" name="evt" id="id_popnew_prj_txt_evt" value="0" class="w-25 fontsiz10 p-0 mr-1 float-right" >
								
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-12 text-to-right">
							  <button class="btn btn-xs jazz-btn-success popup-prj-save">
								<span class="fa fa-check"></span> Save
							  </button>
							</div>
						</div>
					</div>	
	</div>
	<?php
	display_jazz_popup_box_closure( $popup_prj_id ) ;
	//
	
	//
	$popup_pow_id = display_jazz_popup_box_create( '<span class="fg-oliv-i fontbold">Add a new POW</span>', 0 );
	?>
	<div class="container-fluid">
	...
	</div>
	<?php
	display_jazz_popup_box_closure( $popup_pow_id ) ;
	//

	//
	$popup_br_id = display_jazz_popup_box_create( '<span class="fg-oliv-i fontbold">Add a new Branch</span>', 0 );
	?>
	<div class="container-fluid">
	...
	</div>
	<?php
	display_jazz_popup_box_closure( $popup_br_id ) ;
	//

	//
	$popup_pa_id = display_jazz_popup_box_create( '<span class="fg-oliv-i fontbold">Add a new Partner</span>', 0 );
	?>
	<div class="container-fluid">
					<div class="form" role="form" id="frm_popnew_pa">
						<input type="hidden" name="new" value="1">
						<div class="row">
							<div class="col-12 py-1 fontsiz9 fontbold">
								Partner Name:
							</div>
							<div class="col-12">
									
								<input type="text" name="pname" id="id_popnew_pa_txt_pname" value="" class="w-100 fontsiz10 p-0 mr-1" >
								
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-12 py-1 fontsiz9 fontbold">
								Country:
							</div>
							<div class="col-12 py-1 fontsiz9 fontbold">
								<select name="cc" id="id_popnew_pa_cbx_cc" class="fontsiz9-i p-0 m-0 w-100">
							<?php
							foreach( $li_country as $cc )
							{
							 echo '<option value="'.$cc['ID'].'" '.( $cc['ID']==348 ? ' selected="selected"' : '' ).'>'.$cc['Name'].' - '.$cc['SP3'].'</option>'."\n";
							}
							?>
								</select>
							</div>
							<div class="col-12 text-to-right">
							  <button class="btn btn-xs jazz-btn-success popup-pa-save">
								<span class="fa fa-check"></span> Save
							  </button>
							</div>
						</div>
					</div>	
	</div>
	<?php
	display_jazz_popup_box_closure( $popup_pa_id ) ;
	//
	
?>
	<div class="row m-0 p-0 fontsiz9">
	<div class="col-12">
	<?php
	//
	  $bcDefsDAO->setID( $bcid ) ;
	  $bcDefsDAO->loadMe() ;
	  
	  $bcConfig = json_decode( $bcDefsDAO->getJsonData() ) ; // na ezt nézegetjük majd
	  
	  //
	  $hidden_cols = null;
	  
	  if( isset( $bcConfig->hide ) ) if( is_array( $bcConfig->hide ) ) $hidden_cols = $bcConfig->hide ;
	  
	  if( !isset($hidden_cols) ) $hidden_cols=array(); // empty but Array type! // bugfix 2018-09-16
	  
	  //
	  $plus_cols = array(); // 20180924
	  
	  if( isset( $bcConfig->plus ) )
	  foreach( $bcConfig->plus as $k => $v ){
	    //
		$plus_cols[] = array( $k, $v ) ;
		//
	  }
	  //print_r( $plus_cols ) ;
	//
	?>
		<div class="row pt-2 fontsiz10">
			<div class="col-3 fg-brown">
			<h4 class="fontsiz10-i m-0 p-0 fg-brown"><u>Budget Code</u>:
			 <span class="pl-3 fg-black fontsiz9-i fontbold">
			 <?php
				if($fi_sel_yr!=(int)date('y')) { echo $bcid.$fi_sel_yr; }
				else{ echo $bcid; }
			 ?>
			 </span>
			 &nbsp;
			 <!-- a href="<?php echo site_url().site_dir(); ?>?r=budget-ctrl&id=<?php echo $bcid; ?>"><span class="fa fa-refresh"></span></a -->
			</h4>
			</div>
			
			<div class="col-9 fontsiz9 text_bc_def">
				<img src="<?php echo $arr_cfg['SITE_DIR']?>/img/fa/fa-pencil-8-blue-lt3.png" class="pb-1  mousexhand modifyBudgetPencil">
				<span class="fontbold"><?php echo $bcConfig->{'n'}; ?></span>
				<br>
				<?php if( isset($bcConfig->{'x'}) ) echo $bcConfig->{'x'}; ?>
			</div>
			
			<div class="col-9 fontsiz9 edit_bc_def display-none">
				<form method="POST">
					<input type="hidden" name="mod_bc_def" value="1">
					<div class="form-row pb-1">
					<input type="text" name="bc_def_n" class="form-control form-control-sm fontsiz9-i fontbold" value="<?php echo $bcConfig->{'n'}; ?>">
					</div>
					<div class="form-row pb-1">
					<textarea name="bc_def_x" rows="2" class="form-control form-control-sm fontsiz10-i"><?php if( isset($bcConfig->{'x'}) ) echo $bcConfig->{'x'}; ?></textarea>
					</div>
					<div class="form-row">
					<hr>
					<p class="text-to-left w-100">
					<!-- span class="fontsiz9-i fontbold">hide:</span -->
					<?php
					//
					if( in_array( 'prj' ,$hidden_cols ) )
					{
					 echo '<span class="btn btn-mini jazz-btn-gray-lt px-2" id="btn_hide_prj"><span class="fa fa-times fontsiz8" id="sp_hide_prj"></span> prj</span> ';
					 echo '<input type="hidden" name="show_prj" id="chk_show_prj" value="0">'; // SHOW: 0 and 1: true if NOT HIDDEN...
					}
					else
					{
					 echo '<span class="btn btn-mini jazz-btn-gray-lt px-2" id="btn_hide_prj"><span class="fa fa-check fg-green fontsiz9" id="sp_hide_prj"></span> prj</span> ';					 
					 echo '<input type="hidden" name="show_prj" id="chk_show_prj" value="1">';
					}
					
					if( in_array( 'pow' ,$hidden_cols ) )
					{
					 echo '<span class="btn btn-mini jazz-btn-gray-lt px-2" id="btn_hide_pow"><span class="fa fa-times fontsiz8" id="sp_hide_pow"></span> pow</span> ';
					 echo '<input type="hidden" name="show_pow" id="chk_show_pow" value="0">';
					}
					else
					{
					 echo '<span class="btn btn-mini jazz-btn-gray-lt px-2" id="btn_hide_pow"><span class="fa fa-check fg-green fontsiz9" id="sp_hide_pow"></span> pow</span> ';					 
					 echo '<input type="hidden" name="show_pow" id="chk_show_pow" value="1">';
					}
					
					if( in_array( 'br' ,$hidden_cols ) )
					{
					 echo '<span class="btn btn-xs jazz-btn-gray-lt" id="btn_hide_br"><span class="fa fa-times fontsiz8" id="sp_hide_br"></span> br</span> ';
					 echo '<input type="hidden" name="show_br" id="chk_show_br" value="0">';
					}
					else
					{
					 echo '<span class="btn btn-xs jazz-btn-gray-lt" id="btn_hide_br"><span class="fa fa-check fg-green fontsiz9" id="sp_hide_br"></span> br</span> ';
					 echo '<input type="hidden" name="show_br" id="chk_show_br" value="1">';
					}
					?>
					<!-- <br> <span class="fontsiz9-i fontbold">plus:</span> -->
					</p>
					</div>
					<div class="form-row">
					<!-- a href="<?php echo $arr_cfg['SITE_DIR']?>/?r=budget-ctrl&id=<?php echo $_REQUEST["id"]; ?>" class="btn btn-xs jazz-btn-gray-lt px-2 fontsiz9">
						<span class="fa fa-times fontsiz9-i"></span>
						<?php 
							if(!isset($arr_lang['SYS_BTN_CANCEL'])) $arr_lang['SYS_BTN_CANCEL']='Cancel';
							
							echo $arr_lang['SYS_BTN_CANCEL'] 
						?>
					</a -->
					<button class="btn btn-xs jazz-btn-success p-0">
						<img src="<?php echo site_dir() ?>img/fa/fa-check-white-bg-green-lt.png" class="pl-1 mb-1">
						<span class="p-1"><?php echo $arr_lang['SYS_BTN_SAVE']?></span>
					</button>
					&nbsp;
					<span class="btn btn-xs jazz-btn-gray-lt p-0 fontsiz9 mousexhand modifyBudgetPencil"> 
						<img src="<?php echo site_dir() ?>img/fa/fa-times-8.png" class="pl-1 mb-1">
						<span class="p-1"><?php echo $arr_lang['SYS_BTN_CANCEL']; ?></span>
					</span>
					</div>
				</form>
			</div>
			
		</div>
<?php
//
$js_hidecols = '<script>
//
jQuery(document).ready(function($){

 var v_show_pow = Number( $("#chk_show_pow").val() ) ; if( isNaN(v_show_pow) ) v_show_pow = 0;
 var v_show_prj = Number( $("#chk_show_prj").val() ) ; if( isNaN(v_show_prj) ) v_show_prj = 0;
 var v_show_br  = Number( $("#chk_show_br").val() ) ;  if( isNaN(v_show_br) ) v_show_br = 0;

 $("#btn_hide_pow").on("click",function(){
  if( v_show_pow==0 ){ 
   v_show_pow=1; 
   $("#chk_show_pow").val(1);    
   $("#sp_hide_pow").removeClass("fa-times");
   $("#sp_hide_pow").addClass("fa-check");
   $("#sp_hide_pow").addClass("fg-green");
  }else{ 
   v_show_pow=0; 
   $("#chk_show_pow").val(0); 
   $("#sp_hide_pow").removeClass("fa-check");
   $("#sp_hide_pow").removeClass("fg-green");
   $("#sp_hide_pow").addClass("fa-times");
  }
 });
 $("#btn_hide_prj").on("click",function(){
  if( v_show_prj==0 ){ 
   v_show_prj=1; 
   $("#chk_show_prj").val(1); 
   $("#sp_hide_prj").removeClass("fa-times");
   $("#sp_hide_prj").addClass("fa-check");
   $("#sp_hide_prj").addClass("fg-green");
  }else{ 
   v_show_prj=0; 
   $("#chk_show_prj").val(0); 
   $("#sp_hide_prj").removeClass("fa-check");
   $("#sp_hide_prj").removeClass("fg-green");
   $("#sp_hide_prj").addClass("fa-times");
  }
 });
 $("#btn_hide_br").on("click",function(){
  if( v_show_br	==0 ){ 
   v_show_br=1; 
   $("#chk_show_br").val(1); 
   $("#sp_hide_br").removeClass("fa-times");
   $("#sp_hide_br").addClass("fa-check");
   $("#sp_hide_br").addClass("fg-green");
  }else{ 
   v_show_br=0; 
   $("#chk_show_br").val(0); 
   $("#sp_hide_br").removeClass("fa-check");
   $("#sp_hide_br").removeClass("fg-green");
   $("#sp_hide_br").addClass("fa-times");
  }
 });
 
});
</script>
';

addJSHook(JSHOOK_LAST, $js_hidecols);
//
?>
	<div class="clearfix"> <br> </div>
	
<?php
	//
	$hide_if_74 = false; 
	$hide_74_css = "";
	$wid_est_just = 312 ;
	
	if( (int)$_REQUEST['id']>740000 ){ $hide_if_74 = true ; $hide_74_css = "display-none"; $wid_est_just = 555; }
	//
?>		
		<div class="table-responsive">
		    <p id="est_error_txt" class="fg-red-lt fontsiz9-i my-0 py-0"></p>
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th scope="col">&nbsp;</th>
						<th class="text-right" scope="col">Original estimate</th>
						<th class="text-center" scope="col">Modification</th>
						<th class="text-center" scope="col" width="<?php echo $wid_est_just; ?>" min-width="<?php echo $wid_est_just; ?>">Justification of the modification</th>
						<th class="text-center" scope="col">Authorized estimate</th>
						<th class="text-center" scope="col"><?php echo ( $hide_if_74 ? "Income" : "Expenditure" ) ; ?></th>
						<th class="text-center <?php echo $hide_74_css; ?>" scope="col">Commitment</th>
						<th class="text-center <?php echo $hide_74_css; ?>" scope="col">Available balance</th>
					</tr>
				</thead>
				<tbody> 
<?php
	//
	//
	
		$lr_est_bcode = new \Jazz\Core\JazzDAOList( $yc,
												$bcdataDAO,
												[ 
												 [ 'Year', '=', (int)$fi_sel_yr ],
												 [ 'Active', '=', 1 ], /* EST Table items 1:active */
												 [ 'ItemTypeID', 'IN', [ 904,905,906,907,908 ] ] /* est,mod exp||com||rev */
												], 
												[ ['ItemTypeID',1], ['ID',1] ]
											   );
											   
	    $rows_bc_estmod = $lr_est_bcode->getRowsArrayByID( [ 'ID', 'ItemTypeID', 'BookedAmount', 'Subject' ] ) ;
	//
	
	// 
	$est_orig= 0 ; $est_orig_set = 0;
	
	$est_act = 0 ; // actual : est - sum(mod)
	$est_mod = 0 ;
	
	$est_exp = 0 ;
	$est_com = 0 ;
	$est_rev = 0 ;
	
	$jsvar_balance_items = array( 'exp' => 0, 
								  'com' => 0,
								  'rev' => 0,
								  'orig' => 0,
								  'eact' => 0,
								  'emod' => 0,
								  'blnc' => 0,
								  'mods' => array(),
								 );
	
	//
	foreach( $rows_bc_estmod as $balance_line )
	{
		//
		if( $balance_line['ItemTypeID']==904 ){ // 'est'
		  //
		  $subj = "";
		  $jd = json_decode( $balance_line['Subject'] ) ;
		  if( is_object($jd) && isset($jd->{'n'}) ){ $subj = $jd->{'n'} ; }
		  else
		  if( is_string($jd) && (strlen($jd)>0) ) 	 $subj = $jd ;
		  
		  //
		  echo '<tr>
				<td>&nbsp;</td>
				<td class="text-right" id="td_bc_estorigmod" undere="0" width="160" data-rid="'.$balance_line['ID'].'" data-amt="'.round( $balance_line['BookedAmount'], 2 ).'" xcol-txt="'.number_format( $balance_line['BookedAmount'], 2, ',',' ' ).'" style="width:160px;">'.number_format( $balance_line['BookedAmount'], 2, ',',' ' ).' '.$basic_currency_txt.'</td>
				<td class="text-center"></td>
				<td class="text-left xcol-jtxt px-1" rid="'.$balance_line['ID'].'" id="td_bc_'.$balance_line['ID'].'" undere="0" xest-t="auth" xcol-txt="'.$subj.'">'.$subj.'</td>
				<td class="text-right"><span id="sp_est_authorig">'.number_format( $balance_line['BookedAmount'], 2, ',',' ' ).'</span> '.$basic_currency_txt.'</td>
				<td class="text-center"></td>
				<td class="text-center '.$hide_74_css.'"></td>
				<td class="text-center '.$hide_74_css.'"></td>
			</tr>';
		  
		  //
		  $jsvar_balance_items[ 'orig' ] = round( $balance_line['BookedAmount'], 2 ) ;
		  
		  //
		  $est_orig_set = 1 ;
		  
		  $est_orig= round( $balance_line['BookedAmount'], 2 ) ;
		  $est_act = $est_orig ;
		  //	
		}
		else
		if( $balance_line['ItemTypeID']==905 ){ // 'mod'
		  //		  
		  $est_act += round( $balance_line['BookedAmount'], 2 ) ;
		  $est_mod += round( $balance_line['BookedAmount'], 2 ) ;
		  
		  $subj = "";
		  $jd = json_decode( $balance_line['Subject'] ) ;
		  if( is_object($jd) && isset($jd->{'n'}) ){ $subj = $jd->{'n'} ; }
		  else
		  if( is_string($jd) && (strlen($jd)>0) ) 	 $subj = $jd ;
		  
		  echo '<tr>
				<td>&nbsp;</td>
				<td class="text-right" width="160" style="width:160px;"> </td>
				<td class="text-right xcol-emod px-1" rid="'.$balance_line['ID'].'" id="td_emod_'.$balance_line['ID'].'" data-amt="'.round( $balance_line['BookedAmount'],2 ).'" undere="0" xcol-txt="'.number_format( $balance_line['BookedAmount'], 2, ',',' ' ).' '.$basic_currency_txt.'">'.number_format( $balance_line['BookedAmount'], 2, ',',' ' ).' '.$basic_currency_txt.'</td>
				<td class="text-left xcol-jtxt px-1" rid="'.$balance_line['ID'].'" id="td_bc_'.$balance_line['ID'].'" undere="0" xest-t="est" xcol-txt="'.$subj.'">'.$subj.'</td>
				<td class="text-right">'.number_format( $est_act, 2, ',',' ' ).' '.$basic_currency_txt.'</td>
				<td class="text-center"></td>
				<td class="text-center '.$hide_74_css.'"></td>
				<td class="text-center '.$hide_74_css.'"></td>
			</tr>';		  
			//
			// <input type="text" name="estmod_'.$balance_line['ID'].'_txt" name="estmod_'.$balance_line['ID'].'_txt" data-rid="'.$balance_line['ID'].'" value="'.$subj.'" class="border-0 fontsiz9 w-100">
		    //
		  
		  
		  //
		  $jsvar_balance_items['mods'][ $balance_line['ID'] ] = round( $balance_line['BookedAmount'], 2 ) ;
		  
		  //	
		}
		else
		if( $balance_line['ItemTypeID']==906 ){ // 'exp'
		  //		  
		  $est_exp += round( $balance_line['BookedAmount'], 2 ) ;
		}
		else
		if( $balance_line['ItemTypeID']==907 ){ // 'com'
		  //		  
		  $est_com += round( $balance_line['BookedAmount'], 2 ) ;
		}
		else
		if( $balance_line['ItemTypeID']==908 ){ // 'rev'
		  //		  
		  $est_rev += round( $balance_line['BookedAmount'], 2 ) ;
		}
	}
	
	//
	// PLUS ROW
	//
	
	  if( $est_orig_set==0 ){
		//
		// pick up the Est_Orig amount
		//
		echo '<tr>
				<td>&nbsp;</td>
				<td class="px-1 text-to-right" id="td_bc_estorig" undere="0" width="160" style="width:160px;"><input type="text" name="id_bc_estorig" id="id_bc_estorig" value="" width="74" class="m-0 p-0 border-0 text-right fontsiz9" style="width:74px;"> <span id="span_bc_estorig" style="padding-right:2px;"></span></td>
				<td> </td>
				<td> </td>
				<td> </td>
				<td> </td>
				<td class="'.$hide_74_css.'"> </td>
				<td class="'.$hide_74_css.'"> </td>
			</tr>';
		//
	  }
	  else{
		//
		// pop the Plus Line for 'mod' items:
		//
		echo '<tr>
				<td>&nbsp;</td>
				<td> </td>
				<td class="p-0 text-to-right" id="td_bc_estmod" width="160" style="width:160px;"><input type="text" name="id_bc_estmod" id="id_bc_estmod" value="" width="74" class="m-0 p-0 border-0 text-right fontsiz9" style="width:74px;"> <span id="span_bc_estmod" style="padding-right:2px;"></span></td>
				<td> </td>
				<td> </td>
				<td> </td>
				<td class="'.$hide_74_css.'"> </td>
				<td class="'.$hide_74_css.'"> </td>
			</tr>';
	  }
	//
?>
				</tbody>
				<tfoot>
					<tr>
						<th class="text-center">Actual:</th>
						<th class="text-right"><span id="sp_est_orig"><?php echo number_format( $est_orig, 2, ',',' ' ) ; ?></span> <?php echo $basic_currency_txt; ?></th>
						<th class="text-right"><span id="sp_est_mod"><?php echo number_format( $est_mod, 2, ',',' ' ) ; ?></span> <?php echo $basic_currency_txt; ?></th>
						<th class="text-right"></th>
						<th class="text-right"><span id="sp_est_act"><?php echo number_format( $est_act, 2, ',',' ' ) ; ?></span> <?php echo $basic_currency_txt; ?></th>
						<th class="text-right"><span id="sp_est_exp"><?php echo number_format( ( $hide_if_74 ? $est_rev : $est_exp ), 2, ',',' ' ) ; ?></span> <?php echo $basic_currency_txt; ?></th>
						<th class="text-right <?php echo $hide_74_css; ?>"><span id="sp_est_com"><?php echo number_format( $est_com, 2, ',',' ' ) ; ?></span> <?php echo $basic_currency_txt; ?></th>
						<th class="text-right <?php echo $hide_74_css; ?>"><span id="sp_total_blnc"><?php 
						  //
						  $total_lines_calculation = $est_act -$est_exp -$est_com ;
						  
						  echo number_format( $total_lines_calculation, 2, ',',' ' ) ; 
						  //
						 ?></span>
						<?php echo $basic_currency_txt; ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		
		<?php
		  //
		  $jsvar_balance_items[ 'exp' ] = round( $est_exp, 2 ) ;
		  $jsvar_balance_items[ 'com' ] = round( $est_com, 2 ) ;
		  $jsvar_balance_items[ 'rev' ] = round( $est_rev, 2 ) ;
		  //
		  $jsvar_balance_items[ 'orig' ] = round( $est_orig, 2 ) ;
		  $jsvar_balance_items[ 'emod' ] = round( $est_mod, 2 ) ;
		  $jsvar_balance_items[ 'eact' ] = round( $est_act, 2 ) ;
		  //
		  $jsvar_balance_items[ 'blnc' ] = round( $total_lines_calculation, 2 ) ; // the calculated balance
		  //
		?>
		
		
	<div class="clearfix"> <br> </div>
	<?php
	 //
	 $genJS_Array_expHead = array() ;
	 //
	?>
	
		<div class="table-responsive">
			<h4 class="fontsiz10-i my-3 py-2 fg-brown"><span class="fontunderline">Actual 
			<?php echo ( (int)$_REQUEST['id']>740000 ? 'revenues' : 'expenditures' ) ; ?></span>
			 &nbsp;
			 <a href="<?php echo site_url().site_dir(); ?>?r=budget-ctrl&id=<?php echo $bcid; ?>"><span class="fa fa-refresh"></span></a>
			</h4>
			<table id="tbl_expend" class="table table-bordered table-hover">
				<thead>	
					<tr>
						<?php 
						 $cols=0; 
						 $genJS_Array_expHead[ 0 ]='id';
						?>
						<th class="text-center">&nbsp;</th> 
						<?php $cols++; ?>
						<?php
							//
							// $hidden_cols = array(  ); // FENTEBB olvassuk ki! a bcDefsDAO-ból
							
							//
							$shorten_subj = false;
							
							$this_bc_is_rev = false;
							
							if( count($plus_cols)>0 ){ $shorten_subj = true; }
							else
							if( ((int)$_REQUEST['id']>740000) && ((int)$_REQUEST['id']<750000) )
							{ 
							 $this_bc_is_rev = true;
							 
							 $shorten_subj = true; 
							}
							//
						?>
						<th class="text-center" <?php echo ( $shorten_subj ? ' width="155"' : ' width="262" style="max-width:262px;"' ) ; ?> ><?php 
						//
						$lbl_plus_header = array( "event" => "Event",
												  "venue" => "Venue",
												  "evdt"  => "Date",
												) ;
						//
						if( $_REQUEST['id']==723111 ){ // Participant's TDYs (general)
						  //
						  $lbl_plus_header['ptcp'] = "Participant(s)";
						  $lbl_plus_header['trv']  = "Travel";
						}
						else
						if( $_REQUEST['id']==723311 ){ // Participant's taxi
						  //
						  $lbl_plus_header['ptcp'] = "Participant(s)";
						}
						
						//						
						//
						  $trv_field = false;
						  
						  // !!
						  if( $this_bc_is_rev ) $trv_field = true ; // 74 Chapternél nincs Req	20181004
						//
						
						//
						if( count($plus_cols)>0 ){
						  //
						  echo 'Event<br>&nbsp;</th>';
						  
						  foreach( $plus_cols as $plus_col ){ // v.20180924
							  //
							  $genJS_Array_expHead[ $cols ] = $plus_col[0]; // 'key'
							  $cols++;
							  
							  //
							  if( strcmp( $plus_col[0], 'trv' )==0 ) $trv_field = true ; // 20181002

							  //
							  if( strcmp( $plus_col[0],'event')!=0 )
							  {
							    echo '<th class="text-center">'.$lbl_plus_header[ $plus_col[0] ].'<br>&nbsp;</th>';
							  }
						  }
						  //
						}
						else
						{
						echo ( $this_bc_is_rev ? 'Description' : 'Subject of the procurement' ) ; ?><br>&nbsp;</th> 
						<?php 
							  $genJS_Array_expHead[ $cols ]='subj';
							  $cols++; 
						}
						?>
						
						
						<?php if( !$trv_field) { ?>
						
						<th class="text-center" width="94">Request No.<br>
						<span class="btn btn-xs btn-light border-gray-lt-i fontbold fontsiz8-i fg-oliv-lt-i m-0 mousexhand" style="padding:1px 4px !important;" data-toggle="modal" data-target="#popup_<?php echo $popup_req_id; ?>"><img src="<?php echo site_dir() ?>img/fa/fa-plus-6-oliv-lt.png" style="padding-bottom:3px;"> New</span>
						&nbsp;</th>
						
						<?php 
							  $genJS_Array_expHead[ $cols ]='req';
							  $cols++; 
						}	  
						?>
						
						<?php
						if( !in_array( 'prj', $hidden_cols ) )
						{
						    echo
							 '<th class="text-center">Project<br> <span class="btn btn-xs btn-light border-gray-lt-i fontbold fontsiz8-i fg-oliv-lt-i m-0 mousexhand" style="padding:1px 4px !important;" data-toggle="modal" data-target="#popup_'.$popup_prj_id.'"><img src="'.site_dir().'img/fa/fa-plus-6-oliv-lt.png" style="padding-bottom:3px;"> New</span>
							  </th>'; 
							//
							$genJS_Array_expHead[ $cols ]='prj';
							$cols++;
						}
						?>
						<?php
						if( !in_array( 'pow', $hidden_cols ) )
						{
						    echo
							 '<th class="text-center">POW<br>&nbsp;</th>';
							//
							$genJS_Array_expHead[ $cols ]='pow';
							$cols++;
						}
						?>
						<?php
						if( !in_array( 'br', $hidden_cols ) )
						{
						    echo
							 '<th class="text-center">Branch<br>&nbsp;</th>';
							//
							$genJS_Array_expHead[ $cols ]='br';
							$cols++;
						}
						?>
						<th class="text-center">Method of <br>payment</th>
						<?php 
							  $genJS_Array_expHead[ $cols ]='pm';
							  $cols++; 
						?>
						<th class="text-center">Invoice /receipt/<br> number</th>
						<?php 
							  $genJS_Array_expHead[ $cols ]='invo';
							  $cols++; 
						?>
						<th class="text-center" width="90">Date of<br>payment</th>
						<?php 
							  $genJS_Array_expHead[ $cols ]='date';
							  $cols++; 
						?>
						
						<?php if( $this_bc_is_rev ){ ?>
						<th class="text-center"> Payer <br> 
						&nbsp;</th>
						<?php }else{ ?>
						<th class="text-center">Supplier or contractor<br>
						<span class="btn btn-xs btn-light border-gray-lt-i fontbold fontsiz8-i fg-oliv-lt-i m-0 mousexhand" style="padding:1px 4px !important;" data-toggle="modal" data-target="#popup_<?php echo $popup_pa_id; ?>"><img src="<?php echo site_dir() ?>img/fa/fa-plus-6-oliv-lt.png" style="padding-bottom:3px;"> New</span>
						&nbsp;</th>
						<?php 
							  }
							  
							  //
							  $genJS_Array_expHead[ $cols ]='pa';
							  $cols++; 
						?>
						
						
						
						<th width="128" class="text-center">Amount<br> of the invoice</th>
						<?php 
							  $genJS_Array_expHead[ $cols ]='ia';
							  $cols++; 
						?>
						<th width="96" class="text-center">Exchange <br>rate&nbsp; </th>
						<?php 
							  $genJS_Array_expHead[ $cols ]='xr';
							  $cols++; 
						?>
						<th width="128" class="text-center">Booked amount<br>(<?php echo $basic_currency_txt; ?>)</th>
						<?php 
							  $genJS_Array_expHead[ $cols ]='ba';
							  $cols++; 
						?>
					</tr> 
				</thead> 
				<tbody> 
<?php
	//
	//
	$exp_table_colsnum = 3; // Expenditure Table columns number | begin: #3
	
	// itt lesznek biz.esetekben még + mezők főleg a 73as Chapternél
	
	//
	$exp_req_column = $exp_table_colsnum-1 ; // 0-tól indel!
	
	if( $trv_field) 
	{ 
	  $exp_req_column = 0 ;  // !! ilyenkor ne! 20181004
	  $exp_table_colsnum-- ;
	}
	//
	
	
	//
	$jsvar_barr = null; // builder array
	
	// REQ:
	//
		$genjs_arr_t_req = array(); // 2018-08-25
	
		//
		if( !$trv_field)
		foreach($li_reqs as $rq)
		{
			$nam_ = null;			
			$nam_ = str_replace( '"', '', $rq['ShortName'] ); 
			$nam_ = str_replace( "'", "", $nam_ ); 
			$nam_ = str_replace( "\\", '', $nam_ ); 

			// MÁR NEM ÍGY: ... $jsvar_barr[] = '<option value=\"'.$rq['ID'].'\">'.$nam_.'</option>'; // escape-elni a " jelet!
			
			$genjs_arr_t_req[] = array( $rq['ID'], $nam_ ) ; // 2018-08-25		
		}
		//
	
	// PROJ:
	//
		$genjs_arr_t_prj = array(); // 2018-08-25
	
		//
		foreach($li_projs as $proj)
		{
			$nam_ = null;			
			$nam_ = str_replace( '"', '', $proj['ShortName'] ); 
			$nam_ = str_replace( "'", "", $nam_ ); $nam_ = str_replace( "\\", '', $nam_ ); 
			
			$genjs_arr_t_prj[] = array( $proj['ID'], $nam_ ) ; // 2018-08-25		
		}
		//
	
	// POW:
	//
		$genjs_arr_t_pow = array(); // 2018-08-25
	
		//
		foreach($li_pows as $pows)
		{
			$nam_ = null;			
			$nam_ = str_replace( '"', '', $pows['ShortName'] ); 
			$nam_ = str_replace( "'", "", $nam_ ); $nam_ = str_replace( "\\", '', $nam_ ); 

			$genjs_arr_t_pow[] = array( $pows['ID'], $nam_ ) ; // 2018-08-25
		}
		//
	
	// BRANCHES:
	//
		$genjs_arr_t_br = array(); // 2018-08-25

		//
		foreach($li_branches as $br)
		{
			$nam_ = null;			
			$nam_ = str_replace( '"', '', $br['ShortName'] ); 
			$nam_ = str_replace( "'", "", $nam_ ); $nam_ = str_replace( "\\", '', $nam_ ); 

			$genjs_arr_t_br[] = array( $br['ID'], $nam_ ) ; // 2018-08-25
		}
		//
		
	
	// PARTNERS:
	//
		$genjs_arr_t_pa = array(); // 2018-08-25
	
		//
		foreach($li_partners as $pa)
		{
			$o_ = null; $pana = null; // object, partner_name
			$o_ = json_decode( $pa['JsonData'] ) ;
			
			$pana = null;
			$pana = ( is_object($o_) && isset($o_->{'n'}) ? $o_->{'n'} : "" ) ;
			
			$pana = str_replace( '"', '', $pana ); $pana = str_replace( "'", "", $pana ); $pana = str_replace( "\\", '', $pana ); 
			
			$genjs_arr_t_pa[] = array( $pa['ID'], $pana ) ; // 2018-08-25
		}
		//
	
	
	$jsvar_barr = null;
	//
	
	
	//
	$exp_prj_column=0 ;
	
	if( !in_array( 'prj', $hidden_cols ) )
	{
	  //
	  $exp_prj_column = $exp_table_colsnum;
	  
	  $exp_table_colsnum++;
	}
	
	
	//
	$exp_pow_column=0 ;
	
	if( !in_array( 'pow', $hidden_cols ) )
	{
	  //
	  $exp_pow_column = $exp_table_colsnum;
	  
	  $exp_table_colsnum++;
	}
	
	//
	$exp_br_column = 0;
	
	if( !in_array( 'br', $hidden_cols ) )
	{
	  //
	  $exp_br_column = $exp_table_colsnum;
	  
	  $exp_table_colsnum++;
	}
		
	//
	$arr_pmeth = array( 95 => 'cash',
						97 => 'transfer',
						98 => 'card/web',
						99 => 'LoF',
					  );
	
	//
	$jsvar_cbx_pmeth='class=\"combo-w100\"><option value=\"0\"></option><option value=\"95\">cash</option><option value=\"97\">transfer</option><option value=\"98\">card/web</option><option value=\"99\">LoF</option></select>'; // PaymentMethode
		
	$exp_pmeth_column = $exp_table_colsnum;
		
	$exp_table_colsnum++ ;
	//
	
	$exp_invo_column  = $exp_pmeth_column+1;
	$exp_date_column  = $exp_pmeth_column+2;
	
	$exp_table_colsnum+=2 ;
	//
		
	$exp_pa_column = $exp_table_colsnum; // Partners
		
	$exp_table_colsnum++ ;
	//
				
		//
		$exp_table_colsnum+=3;  // 3 záró mező: összegek
	//
	//
	
	
	
	//
	//
	// KIOLVASSUK a BudgetCode sorait!!
	//
		
		//
		// flags, old: /* 0:draft 1:active -1:hidden/removed */ ||| az 'exp' "draft"-ja az a 'comm' szóval ami =1 csak azt kezeljük!
		//
		//
		$typs = null;  /* 906 expenditures, 907 commitments, 908 revenues */
		
		if( (int)$_REQUEST['id'] >750000 ){ 
			//
			$typs = array( 906,908 ) ;
		}
		else
		if( (int)$_REQUEST['id'] >740000 ){
			//
			$typs = array( 908 ) ;
		}
		else
		if( (int)$_REQUEST['id'] <740000 ){ // Chapter 71,72,73
			//
			$typs = array( 906,907 ) ;
		}
		//
		
		//
		$lr_bcode = new \Jazz\Core\JazzDAOList( $yc,
												$bcdataDAO,
												[ 
												 [ 'Year', '=', (int)$fi_sel_yr ],
												 [ 'Active', '=', 1 ], 
												 [ 'ItemTypeID', 'IN', $typs ]
												], 
												[ ['ID',1] ]
											   );
											   
	    $rows_bc_expcom = $lr_bcode->getRowsArrayByID( [ 'ID', 'ItemTypeID', 'Date', 'RequestID', 'ProjectID', 'POWID', 'BranchID', 'PartnerID', 'PaymentMethode', 'InvoiceNumber', 'InvoiceAmount', 'InvoiceCurrency', 'BookedAmount', 'BookedCurrency', 'XRate', 'InsertedByUID', 'TimeStamp', 'Subject' ] ) ;
	
	//print_r( $rows_bc_expcom ) ;
	//
	
	
	//
	// rows - expenditures:
	//	
	$i=1;
	
	$genJS_Array_expMatrix = array() ; // 2018-08-24
	$_genJS = null;
	
	$ity_exprev = 906; // `exp`
	if( (int)$_REQUEST['id'] >740000 ) $ity_exprev = 908; // `rev`
	
	//
	$arr_reqPres[] = 0 ; // a 'nincs beállítva' tehát #0 is legyen eleme
	
	$sumof_exp_req[ 0 ] = array( 0,$basic_currency_id ) ; // Matrix: req by presence | y-index: bcrwID array( [0] amt [1] cur )
	$items_exp_req[ 0 ] = array( 0=> array( 0,$basic_currency_id ) ) ; // Matrix: req by presence | y-index: bcrwID
	
	$genjs_calc_exprev = array() ; // rows by rid
	$genjs_calc_exprev[0] = array( 0,0, 0.0, 1.0, 0.0 ) ;// idx:0=rq,ia,icur,xr,ba
	//
	
	$_genJS = null;
	$plus_colvals = null;
	
	foreach( $rows_bc_expcom as $row_e )
	if( $row_e['ItemTypeID']==$ity_exprev )
	{
		//
		    $_genJS = null;
			$_genJS = array() ;
			
			$_genJS['id'] = (int)$row_e['ID'] ; // 'as is it' | kivételesen: irregular!
		
		//
			$subj_ = json_decode( $row_e['Subject'] ) ;
			
			$_genJS['subj'] = $subj_ ; // by default: sima String 20180923
			
			$plus_colvals = null;
			$plus_colvals = array();
			
			if( is_object($subj_) )
			foreach( $subj_ as $k => $v ){
			 //
			 $plus_colvals[$k] = $v ;
			 
			 if( $k=='event' ){ 
			  //
			  $subj_ = $v ;
			  
			  $_genJS['subj'] = $v ;
			 } 
			 //
			}
			
			//if( count($plus_colvals)>0 ) 
			//print_r( $plus_colvals ) ; 
			//exit(0);
		
			//
			$req_ = null;
			
			if( $row_e['RequestID'] >0 )
			{
			
				//
				$_genJS['req'] = array( 'id' => $row_e['RequestID'], 'a'=>1, 'v' => null ) ; // Object( id,active,value ) | type='req'
				
				if( isset( $li_disa_reqs[ $row_e['RequestID'] ] ) ){  // 20181002
				 //
				 $_genJS['req']['a'] = 0 ;
				 
				 $req_ = $li_disa_reqs[ $row_e['RequestID'] ]['ShortName'] ;
				 //
				}
				else
				{
				 $req_ = $li_reqs[ $row_e['RequestID'] ]['ShortName'] ;
				}
				
				$_genJS['req']['v'] = $req_ ;
				
				
				//
				// plus doing 2018-09-12:
				//
				if( !in_array( (int)$row_e['RequestID'], $arr_reqPres ) ){ $arr_reqPres[] = (int)$row_e['RequestID']; } // gyűjtsük!
				
				if( !isset( $sumof_exp_req[ (int)$row_e['RequestID'] ] ) ){ 
				 //
				 //	20181002: az EXP REQ-t 'base_cur'-ban kell könyvelni!!!
				 $sumof_exp_req[ (int)$row_e['RequestID'] ] = array( 0, (int)$row_e['BookedCurrency'] ) ; // kialakitjuk a helyet 
				 
				 $items_exp_req[ (int)$row_e['RequestID'] ] = array();  // itt csak feszket rakunk!! ertekeket majd _in place_
				 //
				} // számoljunk!
				//
				//				
			}
			else{
				//
				$_genJS['req'] = array( 'id' => 0, 'a'=>1, 'v' => "" ) ;
			}
			
			//print_r( $_genJS['req'] ) ;
		
			//
			$prj_ = null;
			
			if( ($row_e['ProjectID'] >0) && isset( $li_projs[ $row_e['ProjectID'] ] ) ){
				//
				$prj_ = $li_projs[ $row_e['ProjectID'] ]['ShortName'] ;
				
				$_genJS['prj'] = array( 'id' => $row_e['ProjectID'], 'v' => $prj_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['prj'] = array( 'id' => 0, 'v' => "" ) ;
			}
			
			//
			$pow_ = null;
			
			if( ($row_e['POWID'] >0) && isset( $li_pows[ $row_e['POWID'] ] ) ){
				//
				$pow_ = $li_pows[ $row_e['POWID'] ]['ShortName'] ;
				
				$_genJS['pow'] = array( 'id' => $row_e['POWID'], 'v' => $pow_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['pow'] = array( 'id' => 0, 'v' => "" ) ;
			}
		
			//
			$branch_ = null;
			
			if( ($row_e['BranchID'] >0) && isset( $li_branches[ $row_e['BranchID'] ] ) ){
				//
				$branch_ = $li_branches[ $row_e['BranchID'] ]['ShortName'] ;
				
				$_genJS['br'] = array( 'id' => $row_e['BranchID'], 'v' => $branch_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['br'] = array( 'id' => 0, 'v' => "" ) ;
			}
		
			//
			$pa_ = null;

			if( ($row_e['PartnerID'] >0) && isset( $li_partners[ $row_e['PartnerID'] ] ) ){
				//
						$o_ = null;
						$o_ = json_decode( $li_partners[ $row_e['PartnerID'] ]['JsonData'] ) ;
						
						if( is_object($o_) && isset($o_->{'n'}) )
						{
							$pa_ = $o_->{'n'} ; 
						}
				
						$_genJS['pa'] = array( 'id' => $row_e['PartnerID'], 't' => (int)$li_partners[ $row_e['PartnerID'] ]['ItemTypeID'], 'v' => $pa_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['pa'] = array( 'id' => 0, 't'=>0, 'v' => "" ) ;
			}


		
		//
		//
		echo '<tr id="tr'.$row_e['ID'].'" data-id="'.$row_e['ID'].'"> 
				<td xrow-t="'.$ity_exprev.'" xcol-rid="'.$row_e['ID'].'" xcol-t="id" xcol-id="'.$row_e['ID'].'" undere="0" class="xcol-num mousexhand text-right">&nbsp;'.$i++.'&nbsp;</td>
				';
				
		
		$pv = null; // plus_col_val
		if( count($plus_cols)>0 ){
		 foreach( $plus_cols as $pcol ){ // 20180924
		  //
		  $pv = ( isset( $plus_colvals[ $pcol[0] ] ) ? $plus_colvals[ $pcol[0] ] : "" ) ;
		  
		  echo '<td id="'.$pcol[0].'_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="'.$pcol[0].'" xcol-id="'.$row_e['ID'].'" undere="0" class="xcol-exp '.( $pcol[0]=='event' ? "" : "text-center" ).'" xcol-txt="'.$pv.'">'.$pv.'</td>';
		 }
		}
		else
		  echo '<td id="subj_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="subj" xcol-id="'.$row_e['ID'].'" undere="0" class="xcol-exp" xcol-txt="'.$subj_.'">'.$subj_.'</td>';
		
		//
		  if( !$trv_field )
		  echo '	<td id="req_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" '.( $_genJS['req']['a']==1 ? 'xcol-t="req"' : '' ).' xcol-id="'.$_genJS['req']['id'].'" undere="0" class="xcol-exp text-center px-0">'.( isset($req_) ? $req_ : "" ).'</td>';
		//
		
		if( !in_array( 'prj', $hidden_cols ) ){
		  echo '<td id="prj_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="prj" xcol-id="'.$_genJS['prj']['id'].'" undere="0" class="xcol-exp text-center px-1">'.$prj_.'</td> ';
		}
				
		if( !in_array( 'pow', $hidden_cols ) ){
		  echo '<td id="pow_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="pow" xcol-id="'.$_genJS['pow']['id'].'" undere="0" class="xcol-exp text-center px-0">'.$pow_.'</td> ';
		}
				
		if( !in_array( 'br', $hidden_cols ) ){
		  echo '<td id="br_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="br" xcol-id="'.$_genJS['br']['id'].'" undere="0" class="xcol-exp text-center px-1">'.$branch_.'</td> ';
		}
			
		//
			//
			$inv_amt_ = null;
			
			if( $row_e['InvoiceCurrency'] == 348 ){ // HUF: nincs cent
			  //
			  $inv_amt_ = number_format( $row_e['InvoiceAmount'],0, '', ' ' ) ;
			}
			else
			{
			  //
			  $inv_amt_ = number_format( $row_e['InvoiceAmount'],2, ',', ' ' ) ;
			}
			
			//
			//
			if( $row_e['PaymentMethode'] ==0 ) $row_e['PaymentMethode']=97; // Bank Transfer
			
			$_genJS['pm'] = array( 'id' => $row_e['PaymentMethode'], 'v' => $arr_pmeth[ $row_e['PaymentMethode'] ] ) ;

			$_genJS['invo'] = $row_e['InvoiceNumber'] ; // as it is
			
			$_genJS['date'] = $row_e['Date'] ; // as it is | dateFormat...
			
			$_genJS['ia'] = array( 'c' => $row_e['InvoiceCurrency'], 'a'=>$row_e['InvoiceAmount'], 'v' => $inv_amt_ ) ;
			
			$_genJS['xr'] = array( 'a'=>$row_e['XRate'], 'v'=>number_format( $row_e['XRate'],2, ',', ' ' ) ) ;

			$_genJS['ba'] = array( 'c' => 978, 'a'=>$row_e['BookedAmount'], 'v' => number_format( $row_e['BookedAmount'],2, ',', ' ' ) ) ; // fix: 978

			
		//
		// $genjs_calc_exprev[0] = array( idx:0=rq,ia,icur,xr,ba
		
		$genjs_calc_exprev[ $row_e['ID'] ] = array( (int)$_genJS['req']['id'], 
													round( $row_e['InvoiceAmount'],2) , 
													(int)$row_e['InvoiceCurrency'] ,
													round( $row_e['XRate'],2) , 
													round( $row_e['BookedAmount'],2) , 
													) ;
		
		//
		//
		echo '		
				<td id="pm_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="pm" xcol-id="'.$_genJS['pm']['id'].'" undere="0" class="xcol-exp px-0 text-center">'.$arr_pmeth[ $row_e['PaymentMethode'] ].'</td>
				<td id="invo_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="invo" xcol-id="0" undere="0" class="xcol-exp text-center px-0" xcol-txt="'.$row_e['InvoiceNumber'].'">'.$row_e['InvoiceNumber'].'</td>
				<td id="date_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="date" xcol-id="0" undere="0" class="xcol-exp text-center px-0" xcol-txt="'.$row_e['Date'].'">'.$row_e['Date'].'</td>' ;
		
		if( ($bcid >740000)&&($bcid <750000) ){
		 //
		 echo ' <td id="pa_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="cust" xcol-id="'.$_genJS['pa']['id'].'" undere="0" class="xcol-exp '.( $this_bc_is_rev ? 'text-left px-1':'text-center px-0' ).'" xcol-txt="'.$pa_.'">'.$pa_.'</td>';
		}
		else
		 echo '		
				<td id="pa_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="pa" xcol-id="'.$_genJS['pa']['id'].'" undere="0" class="xcol-exp text-center px-0">'.$pa_.'</td>';
		
		echo '		
				<td id="ia_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="ia" xcol-id="0" xcol-a="'.round( $_genJS['ia']['a'],2 ).'" xcol-c="'.$row_e['InvoiceCurrency'].'" undere="0" class="xcol-exp text-right pr-1" xcol-txt="'.$inv_amt_ .' '.$currency_names[ $row_e['InvoiceCurrency'] ].'">'. $inv_amt_ .' '.$currency_names[ $row_e['InvoiceCurrency'] ].'</td>
				<td id="xr_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="xr" xcol-id="0" xcol-a="'.round( $_genJS['xr']['a'],8 ).'" undere="0" class="xcol-exp text-right pr-1" xcol-txt="'.number_format( $row_e['XRate'],2, ',', ' ' ).'">'.number_format( $row_e['XRate'],2, ',', ' ' ).'</td>
				<td id="ba_'.$row_e['ID'].'" xcol-rid="'.$row_e['ID'].'" xcol-t="ba" xcol-id="0" xcol-a="'.round( $_genJS['ba']['a'],2 ).'" xcol-c="978" undere="0" class="xcol-exp text-right pr-1" xcol-txt="'.number_format( $row_e['BookedAmount'],2, ',', ' ' ).' '.$basic_currency_txt.'">'.number_format( $row_e['BookedAmount'],2, ',', ' ' ).' '.$basic_currency_txt.'</td>
			</tr>';
			
			
			//
			//
			// !! a BookedAmount alapjan: 20181002
			 //
			 $sumof_exp_req[ (int)$row_e['RequestID'] ][0] = $sumof_exp_req[ (int)$row_e['RequestID'] ][0] + round( $_genJS['ba']['a'],2 ) ;
			
			 $items_exp_req[ (int)$row_e['RequestID'] ][ $row_e['ID'] ] = array( round( $_genJS['ba']['a'],2 ), $basic_currency_id ) ;
			 //
			//
			
		//
		//
		$genJS_Array_expMatrix[] = $_genJS ;
		
		print_r( $_genJS ) ;
			
		$_genJS = null;
		
		//
		// Calculation:
		//
		$num_exp_foot_amt += round( $row_e['BookedAmount'], 2 ) ;
		
		//
		
	}//#FOREACH : read data
	
	
	
	//
	$exp_rowsnum = count( $genJS_Array_expMatrix ) ;
	//
	
	
	//
	echo '<tr id="exp_the_plus_row">';
	
	for( $jj=0; $jj<$exp_table_colsnum; $jj++ ){
	
	 if( $jj==1 ){ 
		echo '<td id="exp_pluscol_1"><input type="text" name="exp_pluscol_title" id="exp_pluscol_title" width=100% border=0 style="margin:0px;padding:1px;width:100%;min-width:180px;border:none;font-size:9pt !important;font-family:Sans-serif;"> </td>';
	 }
	 else
		echo '<td> </td>';
	
	}//#FOREACH : the plus row
	echo '</tr>';
	//
?>
				</tbody> 
				<tfoot>
					<tr>
						<th colspan="<?php echo (int)$cols -1; ?>" class="text-center">Together
						 <span class="float-right">
						 <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="fontnormal-i fontsiz9-i"><span class="fa fa-refresh fa-fw fontsiz9-i"></span></a>
						 &nbsp;
						 </span>
						</th>
						<th class="text-right" id="exp_foot"><span id="exp_foot_amt"><?php echo number_format( $num_exp_foot_amt,2,',',' ') ; ?></span> <span id="exp_foot_cur"><?php echo $basic_currency_txt; ?></span></th>
					</tr>
				</tfoot>
			</table> 
			<?php //echo $exp_table_colsnum; ?>
			<input type="hidden" name="num_exp_foot_amt" id="num_exp_foot_amt" value="<?php echo round( $num_exp_foot_amt,2 ) ; ?>">
		</div>
		
	<?php
	  //
	  //print_r( $genJS_Array_expMatrix ) ;
	  
	  //var genjs_exp_ref = null; // document.getElementById("tbl_genjs_exp").getElementsByTagName("tbody")[0];
		
$js_after_exp_table = '
<script>

var xls_exp_Array  = new Array( '.$exp_rowsnum.' );
var xls_comm_Array = null; // new Array( com_rowsnum );
var xls_est_Array  = new Array( 1 );

var genjs_exp_ref = null; // ...(tbody)[0]

var genjs_exp_rowsnum = '.$exp_rowsnum."; \n".'
var genjs_exp_colsnum = '.count( $genJS_Array_expHead )."; \n".'

var genjs_exp_row = null;

var genjs_exp_cols = null;  // [ '. json_encode( $genJS_Array_expHead ) .' ] ;

var gen_a = null;

var idsof_req     = '.json_encode( $arr_reqPres ).' ;
var sumof_exp_req = '.json_encode( $sumof_exp_req ).' ;
var items_exp_req = '.json_encode( $items_exp_req ).' ;


</script>
';
//
	addJSHook( JSHOOK_LAST , $js_after_exp_table ) ;
//	
?>	
	
	<div class="clearfix"> </div>
	
<?php		
//
	//
		// Aktualizáljuk / összeállítjuk a ReqCalculatorCache táblát! 
		// és az alapján kifrissítjük a BCTABLE áttekintés (review) táblát is!
		// és esetleg a commitment értékeket is javítjuk a ReqCalculatorCache alapján
		// 
//
	//
		require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiRqCalcCacheDAO.php' ) ;
		$reqCacheDAO = new \Nato\Coemed\CoeMedFiRequestCalculatorCacheDAO( $yc ) ;
	//
	
		$lr_rq_cache = null;
		
		foreach( $arr_reqPres as $rq ){
		  //
		  $lr_rq_cache = new \Jazz\Core\JazzDAOList( $yc,
												$reqCacheDAO,
												[ 
												 [ 'Active', '=', 1 ],
												 [ 'BCodeID', '=', (int)$_REQUEST['id'] ],
												 [ 'ReqID', '=', $rq ],
												 /* [ 'Currency' mezőre amúgy szűrhetnénk DE az kötelezően azonos kell legyen a Req devizanemével */
												 /* [ 'Year', '=', (int)$fi_sel_yr ], // Year mező nincs! mert nem így számít Req esetén... */
												]
											   );
											   
		  //var_dump( [ $rq, $lr_rq_cache->sum( 'Amount' ) ] ) ;
		  //
		}

		
	//	
	
		//
		// ++ .................... THE LIST OF COMMITMENTS If Any ....................
		//
		
		//
		$genJS_Array_commHead = array(); // 2018-08-26
		
		$genJS_Array_commMatrix = null; // bugfix 2018-08-27
		
		$items_com_req = array( 0 => array( 0,0,$basic_currency_id,0 ) ) ; // 2018-09-12, mod-09-13
		$idsof_com_req = array( 0 ) ; // 2018-09-13 !!with default reqID=0

		$genjs_calc_comm = array(); // 2018-09-16
		$genjs_calc_comm[0] = array( 0,0,0,1.0,0 ) ;
		
		$com_rowsnum = 0;
		//

//
//		
if( (int)$_REQUEST['id'] <740000 )
{
	//
										// -- REVENUE esetén nincs `com` (költési elkötelezettség!) 2018-08-27	
	//
	?>
		<br>
		<div class="table-responsive">
			<h4 class="fontsiz10-i my-3 py-2 fg-brown"><span class="fontunderline">Commitments</span>
			 &nbsp;
			 <a href="<?php echo site_url().site_dir(); ?>?r=budget-ctrl&id=<?php echo $bcid; ?>"><span class="fa fa-refresh"></span></a>
			</h4>
			<table id="tbl_commit" class="table table-bordered table-hover">
				<thead>	
					<tr>
					<?php	
					//
					$genJS_Array_commHead[] = "id"; // 2018-08-26
					$genJS_Array_commHead[] = "subj";
					$genJS_Array_commHead[] = "req";
					
					$ppb = array();
					$emptyNum=0;
					if( !in_array( 'prj', $hidden_cols ) ){ $genJS_Array_commHead[] = "prj"; $ppb[]='prj'; }
					if( !in_array( 'pow', $hidden_cols ) ){ $genJS_Array_commHead[] = "pow"; $ppb[]='pow'; }
					if( !in_array( 'br', $hidden_cols ) ){ $genJS_Array_commHead[] = "br"; $ppb[]='br'; }
					
					if( count($ppb)<3 ){ $ppb[]='foo_'.$emptyNum; $genJS_Array_commHead[] = 'foo'.$emptyNum++; }
					if( count($ppb)<3 ){ $ppb[]='foo_'.$emptyNum; $genJS_Array_commHead[] = 'foo'.$emptyNum++; }
		
					$genJS_Array_commHead[] = 'foo'.$emptyNum++;
					$genJS_Array_commHead[] = 'foo'.$emptyNum++;
		
					$genJS_Array_commHead[] = "date";
					$genJS_Array_commHead[] = "pa";
					$genJS_Array_commHead[] = "ia";
					$genJS_Array_commHead[] = "xr";
					$genJS_Array_commHead[] = "ba";
					//
					?>

						<th class="text-center">&nbsp;</th> 
						<th width="262" class="text-center" style="max-width:262px;">Subject of the commitment<br>&nbsp;</th> 
						<th class="text-center">Request No.<br> 
						<span class="btn btn-xs btn-light border-gray-lt-i fontbold fontsiz8-i fg-oliv-lt-i mousexhand m-0" style="padding:1px 4px !important;" data-toggle="modal" data-target="#popup_<?php echo $popup_req_id; ?>"><img src="<?php echo site_dir() ;?>img/fa/fa-plus-6-oliv-lt.png" style="padding-bottom:3px;"> New</span>
						&nbsp; </th> 
						<?php
						if( !in_array( 'prj', $hidden_cols ) ){ ?>
						<th class="text-center">Project<br>
						<span class="btn btn-xs btn-light border-gray-lt-i fontbold fontsiz8-i fg-oliv-lt-i mousexhand m-0" style="padding:1px 4px !important;" data-toggle="modal" data-target="#popup_<?php echo $popup_prj_id; ?>"><img src="<?php echo site_dir() ;?>img/fa/fa-plus-6-oliv-lt.png" style="padding-bottom:3px;"> New</span>
						</th> 
						<?php
						}
						if( !in_array( 'pow', $hidden_cols ) ){ ?>
						<th class="text-center">POW <br>&nbsp; </th> 
						<?php
						}
						if( !in_array( 'br', $hidden_cols ) ){ ?>
						<th class="text-center">Branch <br>&nbsp; </th> 
						<?php
						}
						for( $thi=0; $thi<$emptyNum; $thi++ ) echo '<th width="18">&nbsp; </th>'; 
						?> 
						<th class="text-center" width="100">Date <br>&nbsp; </th> 
						
						<th class="text-center" width="160" style="width:160px;">Supplier or contractor<br>
						<span class="btn btn-xs btn-light border-gray-lt-i fontbold fontsiz8-i fg-oliv-lt-i m-0 mousexhand" style="padding:1px 4px !important;" data-toggle="modal" data-target="#popup_<?php echo $popup_pa_id; ?>"><img src="<?php echo site_dir() ?>img/fa/fa-plus-6-oliv-lt.png" style="padding-bottom:3px;"> New</span>
						&nbsp;</th>
						
						<th class="text-right" width="145" style="width:145px;max-width:145px !important;">Amount of the<br> commitment</th> 
						<th class="text-right" width="96" style="max-width:96px !important;">Exchange <br>rate&nbsp; </th> 
						<th class="text-right" width="140" style="max-width:140px !important;">Committed<br>(orig. - exp. <?php echo $basic_currency_txt; ?>)</th> 
					</tr> 
				</thead> 
				<tbody> 
<?php
	
	//
	$comm_table_colsnum = 0;
	
	//
	// rows - commitments:
	//	
	$i=1;
	
	//$genJS_Array_commHead = array(); // up!! 2018-08-26
	$genJS_Array_commMatrix = array() ; // 2018-08-24
	$_genJS = null;	
		
	
	//
	foreach( $rows_bc_expcom as $row_c )
	if( $row_c['ItemTypeID']==907 )
	{
		//
			$_genJS = array() ;
			
			$_genJS['id'] = (int)$row_c['ID'] ; // 'as is it' | kivételesen: irregular!
		
		
		//
			$subj_ = json_decode( $row_c['Subject'] ) ;
			
			$_genJS['subj'] = $subj_ ; // 'as is it' | kivételesen: irregular!			
			
			//
			$req_ = null;
			
			if( ($row_c['RequestID'] >0) && isset( $li_reqs[ $row_c['RequestID'] ] ) ){ // Transformed at 2018-08-24
				//
				$req_ = $li_reqs[ $row_c['RequestID'] ]['ShortName'] ;
				
				$_genJS['req'] = array( 'id' => $row_c['RequestID'], 'v' => $req_ ) ; // Object( id,value ) | type='req'
				
				//
				// plus doing 2018-09-12:
				//
				if( !isset( $items_com_req[ (int)$row_c['RequestID'] ] ) ){ // gyűjtsük! össze a 'comm'-okat:
				  //
				  $items_com_req[ (int)$row_c['RequestID'] ] = array( 0,0,0 ); // 0: rowID, 1:amt, 2:cur
				}
				
				if( !in_array( (int)$row_c['RequestID'], $idsof_com_req ) ){ $idsof_com_req[] = (int)$row_c['RequestID'] ; }
				//				
			}
			else{
				//
				$_genJS['req'] = array( 'id' => 0, 'v' => "" ) ;
			}
			
			//
			$prj_ = null;
			
			if( ($row_c['ProjectID'] >0) && isset( $li_projs[ $row_c['ProjectID'] ] ) ){
				//
				$prj_ = $li_projs[ $row_c['ProjectID'] ]['ShortName'] ;
				
				$_genJS['prj'] = array( 'id' => $row_c['ProjectID'], 'v' => $prj_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['prj'] = array( 'id' => 0, 'v' => "" ) ;
			}
			
			//
			$pow_ = null;
			
			if( ($row_c['POWID'] >0) && isset( $li_pows[ $row_c['POWID'] ] ) ){
				//
				$pow_ = $li_pows[ $row_c['POWID'] ]['ShortName'] ;
				
				$_genJS['pow'] = array( 'id' => $row_c['POWID'], 'v' => $pow_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['pow'] = array( 'id' => 0, 'v' => "" ) ;
			}
		
			//
			$branch_ = null;
			
			if( ($row_c['BranchID'] >0) && isset( $li_branches[ $row_c['BranchID'] ] ) ){
				//
				$branch_ = $li_branches[ $row_c['BranchID'] ]['ShortName'] ;
				
				$_genJS['br'] = array( 'id' => $row_c['BranchID'], 'v' => $branch_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['br'] = array( 'id' => 0, 'v' => "" ) ;
			}
		
			//
			$pa_ = null;

			if( ($row_c['PartnerID'] >0) && isset( $li_partners[ $row_c['PartnerID'] ] ) ){
				//
						$o_ = null;
						$o_ = json_decode( $li_partners[ $row_c['PartnerID'] ]['JsonData'] ) ;
						
						if( is_object($o_) && isset($o_->{'n'}) )
						{
							$pa_ = $o_->{'n'} ; 
						}
				
						$_genJS['pa'] = array( 'id' => $row_c['PartnerID'], 'v' => $pa_ ) ; // Object( id,value ) | type='prj'
				//				
			}
			else{
				//
				$_genJS['pa'] = array( 'id' => 0, 'v' => "" ) ;
			}
			
		//
		//
		//$comm_table_colsnum=3; // 2018-08-26 már máshogy számoljuk
		
		echo '<tr id="tr'.$row_c['ID'].'" data-id="'.$row_c['ID'].'"> 
				<td xrow-t="907" xcol-rid="'.$row_c['ID'].'" xcol-t="id" xcol-id="'.$row_c['ID'].'" undere="0" class="xcol-num mousexhand text-right">&nbsp;'.$i++.'&nbsp;</td> 
				
				<td id="subj_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="subj" xcol-id="'.$row_c['ID'].'" undere="0" class="xcol-comm" xcol-txt="'.$subj_.'">'.$subj_.'</td>
				
				<td id="req_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="req" xcol-id="'.$_genJS['req']['id'].'" undere="0" class="xcol-comm text-center px-0">'.( isset($req_) ? $req_ : "" ).'</td>
				';

		//
		if( !in_array( 'prj', $hidden_cols ) ){
		  echo '<td id="prj_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="prj" xcol-id="'.$_genJS['prj']['id'].'" undere="0" class="xcol-comm text-center px-0">'.$prj_.'</td>
		  ';
		}
		// else // 2018-08-26 ! Az 'else' ág már nem úgy van mint az Expenditures -nél .. itt a 4 foo-t rakjuk majd ki
			 
				
		//		
		if( !in_array( 'pow', $hidden_cols ) ){
		  echo '<td id="pow_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="pow" xcol-id="'.$_genJS['pow']['id'].'" undere="0" class="xcol-comm text-center px-0">'.$pow_.'</td>
		  ';
		}
				
		//
		if( !in_array( 'br', $hidden_cols ) ){
		  echo '<td id="br_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="br" xcol-id="'.$_genJS['br']['id'].'" undere="0" class="xcol-comm text-center px-0">'.$branch_.'</td>
		  ';
		}
		
		
		//
		// The 'foo' PlaceHolder columns:
		
		for( $thi=0; $thi<$emptyNum; $thi++ ) 
		{
		 echo '<td xcol-rid="'.$row_c['ID'].'" xcol-t="foo'.$thi.'" xcol-id="0" undere="1" class="foo'.$thi.'">&nbsp; </td>'; 
		}
		
		//
		//
			//
			$inv_amt_ = null;
			
			//
			// 'ia' a req-nél.. na ez lesz itt az alap vagyis az Original_Commitment_Amount   | 2018-09-12
			//
			// -->> és EZT fogjuk csökkenteni
			
			//
			$amt_orig = round( $row_c['InvoiceAmount'],2 ) ;  // Original_Commitment_Amount !!!!
			
			$items_com_req[ $row_c['RequestID'] ][0] = (int)$row_c['ID'] ;
			$items_com_req[ $row_c['RequestID'] ][1] = $amt_orig ;
			$items_com_req[ $row_c['RequestID'] ][2] = (int)$row_c['InvoiceCurrency'] ;
			//
		
			//
			
			//if( $row_c['RequestID'] >0 ) /* -- a '0'-ásokat is össze kell adni... */
			if( isset( $sumof_exp_req[ $row_c['RequestID'] ] ) )
			{
			  // .......................................................... ha van ilyen reqID 
			
			  if( $sumof_exp_req[ $row_c['RequestID'] ][0] >0.0 ){
				//
				if( $row_c['InvoiceCurrency'] == $sumof_exp_req[ $row_c['RequestID'] ][1] ){ // neki az 1-es indexe a devizanem
				  //
					$_tmp_ia = $row_c['InvoiceAmount']  -  $sumof_exp_req[ $row_c['RequestID'] ][0] ;
					
					//$row_c['BookedAmount'] = 0.0;
					
					if( $row_c['XRate'] >0.0 )
					if( $row_c['XRate'] >2.0 ){
					  $row_c['BookedAmount'] = round( $_tmp_ia / $row_c['XRate'] , 2 ) ; // bugfix 20180925
					}
					else
					  $row_c['BookedAmount'] = round( $_tmp_ia * $row_c['XRate'] , 2 ) ; // bugfix 20180925
					
				  //
				}
				//
			  }
			  //
			}
			
			
			//
			if( $row_c['InvoiceCurrency'] == 348 ){ // HUF: nincs cent
			  //
			  $inv_amt_ = number_format( $row_c['InvoiceAmount'],0, '', ' ' ) ;
			}
			else
			{
			  //
			  $inv_amt_ = number_format( $row_c['InvoiceAmount'],2, ',', ' ' ) ;
			}
			//
		//
			
		//
		//$comm_table_colsnum+=5; // +7 lenne de itt nincs PaymentMethode és InvoiceNumber
		
		//
		$comm_req_column = 2;
		$comm_prj_column = 3;
		$comm_pow_column = 4;
		$comm_br_column  = 5; // egyes esetekben (BC) kiüthető 0ra amikor nem töltik ki...
		
		$comm_date_column= 6;	
		$comm_pa_column  = 7;
		
		//
			$_genJS['date'] = $row_c['Date'] ; // as it is | dateFormat...
			
			$_genJS['ia'] = array( 'c' => $row_c['InvoiceCurrency'], 'a'=>$row_c['InvoiceAmount'], 'v' => $inv_amt_ ) ;
			
			$_genJS['xr'] = array( 'a'=>$row_c['XRate'], 'v'=>number_format( $row_c['XRate'],2, ',', ' ' ) ) ;

			$_genJS['ba'] = array( 'c' => 978, 'a'=>$row_c['BookedAmount'], 'v' => number_format( $row_c['BookedAmount'],2, ',', ' ' ) ) ; // fix: 978

		//
		
		$genjs_calc_comm[ (int)$row_c['ID'] ] = array(  (int)$_genJS['req']['id'], 
														round( $row_c['InvoiceAmount'],2) , 
														(int)$row_c['InvoiceCurrency'] ,
														round( $row_c['XRate'],2) , 
														round( $row_c['BookedAmount'],2) , 
													) ;
			
		//
		echo '		
				<td id="date_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="date" xcol-id="'.$row_c['ID'].'" undere="0" class="xcol-comm text-center px-0" xcol-txt="'.$row_c['Date'].'">'.$row_c['Date'].'</td>
				<td id="pa_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="pa" xcol-id="'.$_genJS['pa']['id'].'" undere="0" class="xcol-comm text-center px-0">'.$pa_.'</td>
				
				<td id="ia_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="ia" xcol-id="0" xcol-a="'.round( $amt_orig,2 ).'" xcol-c="'.$row_c['InvoiceCurrency'].'" xcol-req="'.$row_c['RequestID'].'" undere="0" class="xcol-comm text-right pr-1" xcol-txt="'.$inv_amt_ .' '.$currency_names[ $row_c['InvoiceCurrency'] ].'">'. $inv_amt_ .' '.$currency_names[ $row_c['InvoiceCurrency'] ].'</td>
				<td id="xr_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="xr" xcol-id="0" xcol-a="'.round( $_genJS['xr']['a'],8 ).'" undere="0" class="xcol-comm text-right pr-1" xcol-txt="'.number_format( $row_c['XRate'],2, ',', ' ' ).'">'.number_format( $row_c['XRate'],2, ',', ' ' ).'</td>
				<td id="ba_'.$row_c['ID'].'" xcol-rid="'.$row_c['ID'].'" xcol-t="ba" xcol-id="0" xcol-a="'.round( $_genJS['ba']['a'],2 ).'" xcol-c="978" undere="0" class="xcol-comm text-right pr-1" xcol-txt="'.number_format( $row_c['BookedAmount'],2, ',', ' ' ).' '.$basic_currency_txt.'">'.number_format( $row_c['BookedAmount'],2, ',', ' ' ).' '.$basic_currency_txt.'</td>
			</tr>';		
		
		//
		//
		$genJS_Array_commMatrix[] = $_genJS ;
			
		$_genJS = null;		
				
		//
		// Calculation:
		//
		$num_com_foot_amt += round( $row_c['BookedAmount'], 2 ) ;
					
		//
		
	}//#FOREACH : read data
	
	//
	$com_rowsnum = count( $genJS_Array_commMatrix ) ;
	
	$comm_table_colsnum = count( $genJS_Array_commHead ) ;	
	//
	
	//
	echo '<tr id="comm_the_plus_row">';
	
	for( $jj=0; $jj<$comm_table_colsnum; $jj++ ){
	
	 if( $jj==1 ){ 
		echo '<td id="comm_pluscol_1"><input type="text" name="comm_pluscol_title" id="comm_pluscol_title" width=100% border=0 style="margin:0px;padding:1px;width:100%;min-width:180px;border:none;font-size:9pt !important;font-family:Sans-serif;"> </td>';
	 }
	 else
		echo '<td> </td>';
	
	}//#FOREACH : the plus row
	echo '</tr>';
	//
?>					
				</tbody>
				<tfoot>
					<tr>
						<th colspan="<?php echo (int)$comm_table_colsnum -1; ?>" class="text-center">Together
						 <span class="float-right">
						 <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="fontnormal-i fontsiz9-i"><span class="fa fa-refresh fa-fw fontsiz9-i"></span></a>
						 &nbsp;
						 </span>
						</th>						
						<th class="text-right" id="com_foot"><span id="com_foot_amt"><?php echo number_format( $num_com_foot_amt,2,',',' ') ; ?></span> <span id="com_foot_cur"><?php echo $basic_currency_txt; ?></span></th>
					</tr>
				</tfoot>
			</table>
			<input type="hidden" name="num_com_foot_amt" id="num_com_foot_amt" value="<?php echo round( $num_com_foot_amt,2 ) ; ?>">
		</div>
		
<?php
	//print_r( $genJS_Array_commHead ) ;
	
	//
	//print_r( $items_com_req ) ; 
	
	
}//#COMMITMENTS: 71,72,73
	
?>
	
	<div class="clearfix"> </div>
		
  </div>
</div>
		
<?php
//

	//
	$genjs_cbx_arr_pm = array(); // 2018-08-25
	
	foreach( $arr_pmeth as $k => $v ) $genjs_cbx_arr_pm[] = array( $k,$v ) ;
	//

//
// ÁTHELYEZVE IDE: 201809287
//
$js_budget_head_vars ='
<script>
var site_url = "'.site_url().'";
var site_dir = "'.site_dir().'";

var fi_sel_yr = '.$fi_sel_yr.';
var bcid = '.(int)$_REQUEST['id'].';

//
var base_cur_id = '.$basic_currency_id.'; 
var base_cur = "'.$basic_currency_txt.'";

//
var genjs_sumof_exp = 0 ;

var genjs_calc_exprev = '.json_encode( $genjs_calc_exprev ).' ; // 2018-09-16

for( var i in genjs_calc_exprev )
{
	if( !( genjs_calc_exprev[i]===undefined) && !( genjs_calc_exprev[i][4]===undefined) )
	{
		genjs_sumof_exp += Number( genjs_calc_exprev[i][4] ) ; // `ba` numerical value
	}
}

//
var jsvar_balance_items = '.json_encode( $jsvar_balance_items ).';

var num_exp_foot_amt_ = '.round( $num_exp_foot_amt,2 ).' ;
var num_com_foot_amt_ = '.round( $num_com_foot_amt,2 ).' ;
	
var last_exp_rownum  = '.$exp_rowsnum.' ;
var last_comm_rownum = '.$com_rowsnum.' ;

var edit_exp_rownum  = 0;
var edit_comm_rownum = 0;

var genjs_arr_cbx = { "req": '.json_encode( $genjs_arr_t_req ).', "br": '.json_encode( $genjs_arr_t_br ).', "pow": '.json_encode( $genjs_arr_t_pow ).',  "prj": '.json_encode( $genjs_arr_t_prj ).', "pa": '.json_encode( $genjs_arr_t_pa ).', "pm": '.json_encode( $genjs_cbx_arr_pm ).' } ;


var req_modal_btn = "<span class=\"fa fa-plus fa-1x fontsiz7-i fg-blue-dk border border-gray-lt-i mousexhand\" data-toggle=\"modal\" data-target=\"#popup_'. $popup_req_id.'\" style=\"padding:1px 2px !important;\"></span>";


//
var xls_comm_Array = new Array( '.$com_rowsnum.' );

var genjs_comm_ref = null; // document.getElementById("tbl_genjs_exp").getElementsByTagName("tbody")[0];

var genjs_comm_rowsnum = '.$com_rowsnum."; \n".'
var genjs_comm_colsnum = '.count( $genJS_Array_commHead )."; \n".'

var genjs_comm_row = null;

var genjs_comm_cols = null;  //var genjs_comm_cols = new Array( '. count( $genJS_Array_commHead ) .' ) ;

    gen_a = null; // defined upper: "exp" scripts
	
var items_com_req = '.json_encode( $items_com_req ).' ; // 2018-09-12	

var idsof_com_req = '.json_encode( $idsof_com_req ).' ; // 2018-09-13	

// 2018-09-16
var genjs_sumof_com = 0 ;

var genjs_calc_comm = '.json_encode( $genjs_calc_comm ).' ; // 2018-09-16

for( var i in genjs_calc_comm )
{
	if( !( genjs_calc_comm[i][4]===undefined) )
	{
		genjs_sumof_com += Number( genjs_calc_comm[i][4] ) ; // `ba` numerical value
	}
}
</script>
';

	addJSHook(JSHOOK_LAST, $js_budget_head_vars);
	//
?>
  

<?php	  
		//
		include('bctable-control-js.php');
		//
		
	  //	

 } //# IF id>700000
?>