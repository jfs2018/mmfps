<?php
/**
 * List of Sponsoring Nations (c) 2018 Jasmine FS KFt.
 * @author nr
 * @start  2018-07-08
 * @lastmod 2018-10-08 nr
 //
 // -- érintett tábla '_natomm_sn' 
 // -- CoeMedFiSponsoringNationDAO
 //
 // -- Elsődleges DAO objektum: $spoNationDAO | és azért nem 'snDAO' mert az nem elég beszédes | nehogy összekeverjük másik objektummal! 
 //
 // SP3-at használunk | lásd: https://en.wikipedia.org/wiki/List_of_NATO_country_codes  (Vízi jelzésekkel! tengerek és óceánok)
 */
 
//
if( !isset($wstoken) )die('Fatal runtime error: WS token failed');
if( $lvl <1 )die('Fatal runtime error: no GRANT to call this module.'); // <1 =die() tehát 'inside': MINIMUM logged in user kell legyen!
//

	//
	if( !isset($fi_sel_yr) ){
	  if( isset($_SESSION["chg_yr"]) ){
		$fi_sel_yr = (int)$_SESSION["chg_yr"]-2000;
	  }
	  else
		$fi_sel_yr = (int)date('y') ;
	}

// Utána ACL-t ellenőrzünk:
// ................................. később megírjuk
// ...

//
if( !isset( $spoNationDAO ) ){
	//
	require_once(  $arr_cfg['DOC_ROOT'].'vendor/nato/milmedfi/CoeMedFiSponsoringNationDAO.php' ) ;
	
	$spoNationDAO = new \Nato\Coemed\CoeMedFiSponsoringNationDAO( $yc ) ;
	//
}

//
if( isset($_REQUEST['fn']) )
if( strcmp( $_REQUEST['fn'],"a" )==0 )
{
	//
	$id = 0;
	$ccc = 0;
	
	if(isset($_REQUEST['id'])) $id = (int)$_REQUEST['id'] ;
	if(isset($_REQUEST['cc'])) $ccc= (int)$_REQUEST['cc'] ;
	
	$ccDAO = $jazzEntityManager->get('JazzISO3166DAO') ;
	
	if( $ccc<=0 ){
			//
			$arr_error = array( 'Data error.',
							JAZZ_ITEMDEF_EVENT_MESSAGE_ERROR,
							'Please define a proper CountryID#',
							[ 'ccc' ], 
							1
							);
	}
	else
	{
	
	 $ccDAO->clearMe() ;
	 $ccDAO->setID( $ccc ) ;
	 $ccDAO->loadMe();
	
	 $spoNationDAO->clearMe();
	 
	 if( $id >0 ) $spoNationDAO->setID( $id ) ;
	 
	 $spoNationDAO->setValue('SP3', $ccDAO->getValue('SP3') ) ;
	 
	 $spoNationDAO->setValue('CC', (int)$ccDAO->getID() ) ;
	 
	 $spoNationDAO->setValue('CountryName', $ccDAO->getValue('Name') ) ;
	 
	 $spoNationDAO->setValue('Yr', $fi_sel_yr ) ;
	 
	 $spoNationDAO->setValue('UseLeftover', 0 ) ;
	 
	 $spoNationDAO->setValue('Active', (int)$_REQUEST['a'] ) ;
	
	 $spoNationDAO->saveMe() ;
			
			//
			$arr_error = array( 'Success.',
							JAZZ_ITEMDEF_EVENT_MESSAGE,
							'`Active` flag modified. CountryID# "'.$ccc.' aka '.$ccDAO->getValue('SP3'),
							[], 
							0
							);			
	
	}
	//
}
else
if( strcmp( $_REQUEST['fn'],"lof" )==0 )
if( isset($_REQUEST['id']) )
{
	//
	 $spoNationDAO->clearMe();
	 
	 $spoNationDAO->setID( (int)$_REQUEST['id'] ) ;
	 
	 $spoNationDAO->setValue('UseLeftover', (int)$_REQUEST['v'] ) ;
	 
	 $spoNationDAO->updateMe() ;
	// 
}




//
	//
		$lr_di_sn = new \Jazz\Core\JazzDAOList( $yc,
										 $spoNationDAO,
										 [
										 ],
										 [
										  ['SP3', 1]
										 ]
										);
		$lr_di_sn->distinct();
		
		$li_di_sn = $lr_di_sn->getRowsArray( ['CC','SP3','CountryName'] ) ; // never call 'ByID' if disctinct() is on!
	//

	//
	$lr_sn = new \Jazz\Core\JazzDAOList( $yc,
										 $spoNationDAO,
										 [
										  [ 'Yr', '=', $fi_sel_yr ],
										 ],
										 [ ['SP3',1] ]
										);

	$li_sn = $lr_sn->getRowsArrayByID( [ 'ID', 'CC', 'Active', 'UseLeftover', 'SP3', 'CountryName' ] ) ;
	
	//print_r( $li_sn ) ;
	
	//print_r( $li_di_sn ) ;
	
	//echo $ctr_spo = count( $li_di_sn ) ;
	
	//for( $i=0; $i < $ctr_spo ; $i++ )
	foreach( $li_di_sn as $i => $spo )
	{
		//$spo = $li_di_sn[ $i ] ;
	
		//echo '<br>' .$i. json_encode( $spo )."\n";
		
		
		foreach( $li_sn as $sn ){
		 if( $spo['CC']==$sn['CC'] )
		 {
		  $li_di_sn[ $i ]['ID'] = (int)$sn['ID'] ;
		  
		  $li_di_sn[ $i ]['Active'] = (int)$sn['Active'] ;
		  
		  $li_di_sn[ $i ]['UseLeftover'] = (int)$sn['UseLeftover'] ;
		
		  break;
		 }
		}
		
		//
		if( !isset( $li_di_sn[ $i ]['ID'] ) )
		{
		  $li_di_sn[ $i ]['ID'] = 0 ;
		  
		  $li_di_sn[ $i ]['Active'] = 0 ;
		  
		  $li_di_sn[ $i ]['UseLeftover'] = 0 ;
		}
		
		//
	}
	
	//exit(1);
	
	//
	// Note:   SP3 are NATO STANAG 1059 3 char codes
	//
	// érdekesség: a FIPS 10-4 nagyon hasonlít az ISO 3166-2 -re bár az STN 1059 inkább ezt (FIPS) használja (többségében azonos az ISO-val)
	//
	?>

	
<div class="card m-auto p-1 col-12 col-sm-11 col-lg-9 border-0">
<div class="card-body m-0 p-0">


	<p class="fontsiz12 fontbold fg-oliv-v2">
	<span class="fa fa-flag fa-fw fg-nato-i"></span>
	Sponsoring Nations - 20<?php echo $fi_sel_yr; ?>
	</p>
	
	<!-- hr -->
	
	<!-- br -->
	
	<!-- GRID HEAD -->
	<div class="row col-12 col-sm-12 col-lg-8 m-0 p-1 border border-top-0 border-left-0 border-right-0 border-gray-192-i">
	 <div class="col-2 fontsiz8 fontbold text-centered">
	 #
	 </div>
	 <div class="col-2 col-md-1 fontsiz9 text-centered">
	 <span class="fa fa-check-square-o fa-fw fontsiz10-i"></span>
	 </div>
	 <div class="col-2 fontsiz9 fontbold text-centered">
	 LoF / Ret.
	 </div>
	 <div class="col-3 fontsiz9 fontbold text-centered">
	 Country
	 </div>
	 <div class="col-3 fontsiz9 fontbold text-centered">
	 
	 </div>
	 
	</div>

	<!-- GRID ROWS -->
	<?php
	
	foreach( $li_di_sn as $sn ){
	
	?>
	<div class="row col-12 col-sm-12 col-lg-8 m-0 p-1 pt-2 border border-top-0 border-left-0 border-right-0 border-jazz-steel-i">
	 <div class="col-2 fontsiz9 text-centered">
	 <?php echo $sn['CC'] ; ?>
	 </div>
	 <div class="col-2 col-md-1 fontsiz9 text-centered">
	 <?php
	 //
	  $data_a = 0; // attr
	  
	  $data_id = (int)$sn['ID'] ;
	  
	  $data_cc = (int)$sn['CC'] ;
	  
	  //
	  if( $sn['Active'] ==1 )
	  { 
	    $data_a = 1;
	  
	    echo '<span class="fa fa-check fa-fw fg-green fontsiz10-i mousexhand chg_act" data-id="'.$data_id.'" data-cc="'.$data_cc.'" data-a="1"></span>'; 
		//
	  }
	  else
	  { 
	    echo '<span class="fa fa-times fa-fw fg-gray fontsiz10-i mousexhand chg_act" data-id="'.$data_id.'" data-cc="'.$data_cc.'" data-a="0"></span>'; 
		//
	  }
	 //
	 ?>
	 </div>
	 <div class="col-2 fontsiz9 fontbold text-centered">
	 <?php
	 if( $sn['Active'] ==1 )
	 if( $sn['UseLeftover'] ==0 )
	 {
	    // Retain:
		echo '<span class="fa fa-refresh fa-fw fg-blue-lt3 fontsiz10-i mousexhand chg_lof" data-id="'.$data_id.'" data-v="0" data-a="'.$data_a.'" title="Retain"></span>'; 
	 }
	 else
	 {
		echo '<span class="fa fa-minus-circle fa-fw fg-green-lt2 fontsiz9-i mousexhand chg_lof" data-id="'.$data_id.'" data-v="1" data-a="'.$data_a.'" title="LoF"></span>'; 
		// 'minus' sign = LeftOver
	 }
	 ?>
	 </div>
	 <div class="col-3 fontsiz9 fontbold text-centered">
	 <?php echo $sn['SP3'] ; ?>
	 </div>
	 <div class="col-3 fontsiz10">	 
	 <?php echo $sn['CountryName'] ; ?>
	 </div>
	 
	</div>
	<?php
	}//#FOREACH $li_sn
	?>
	
	
</div>
</div>

<br>

<?php
 //
 $js_lof ='
<script>
var site_url = "'.site_url().'";
var site_dir = "'.site_dir().'";

var fi_sel_yr = '.$fi_sel_yr.';

jQuery(document).ready(function($){


 $(".chg_act").on("click",function(){
  
  var id = Number( $(this).attr("data-id") ) ; if( isNaN( id ) ) id=0;
  
  var cc = Number( $(this).attr("data-cc") ) ; if( isNaN( cc ) ) cc=0;
  
  var a = Number( $(this).attr("data-a") ) ; if( isNaN( a ) ) a=0;
  
      a = 1 - a; // megforditjuk :)
  
  location.href = site_url+site_dir+"?r='.$_REQUEST['r'].'&fn=a&id="+id+"&cc="+cc+"&a="+a ;
  
 });


 $(".chg_lof").on("click",function(){
  
  var id = Number( $(this).attr("data-id") ) ; if( isNaN( id ) ) id=0;
  
  var v = Number( $(this).attr("data-v") ) ; if( isNaN( v ) ) v=0;
  
      v = 1 - v; // megforditjuk :)
  
  location.href = site_url+site_dir+"?r='.$_REQUEST['r'].'&fn=lof&id="+id+"&v="+v ;
  
 });
 
 
});
</script>
 ';
 
 addJSHook( JSHOOK_LAST, $js_lof );
 //
?>