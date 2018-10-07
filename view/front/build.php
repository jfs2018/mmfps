<?php
/**
// # JazzFS 1v8+ :: (c) 2017 | 2018 Jasmine Kft. (Budapest HUN)
// @lastmod 2018-09-30 nr
*/
if( !isset($wstoken) )die('Fatal runtime error: JazzS token failed');
//
if( !isset($arr_cfg['SITE_DIR']) ){ $arr_cfg['SITE_DIR']=''; } // a fix for earlier version (prior to 1.2.4) of "view" layer
//
$ver_tier = 1 ; // APPLICATIONs VERSION: Tier #1
$ver_mst  = 5 ; // Milestone 1:'18 Jun 2:Jul 3:Aug 4:Sep 5:Oct 6:Nov 7:'18 Dec
//
//
	//
	// Preprocessing -- APPLICATION RELATED VARIABLES --
	//
	
	//
	if(!isset($_SESSION["chg_yr"]))
	{
		$_SESSION["chg_yr"]=(int)date('y')+2000;
	}
	else
	{
		if(isset($_REQUEST['chg_yr']))
		{
			$_SESSION["chg_yr"]=(int)$_REQUEST['chg_yr']+2000;
		}
		else
		{
			$_SESSION["chg_yr"]=$_SESSION["chg_yr"];
		}
	}
	
	$fi_sel_yr = (int)$_SESSION["chg_yr"]-2000;
	
	$currency_signs = array( 348=>'Ft', 
							 826 => '&pound;', 
							 840 => '$', 
							 978=>'&euro;' 
							); 				// Currency Signs .. 'memcache'
	
	$currency_names = array( 348 => 'HUF', 
							 826 => 'GBP', 
							 840 => 'USD', 
							 978 => 'EUR' 
							); 				// Currency Names .. 'memcache'
	//	
		
	//
	// #END-OF- Preprocessing -- APPLICATION RELATED VARIABLES --
	//

//
//
 include('view/'.$tmpl_dir.'/__header.php');
 
 //
 if( file_exists(doc_root().'/'.$arr_cfg['SITE_DIR'].'/view/'.$tmpl_dir.'/__commonfctrl_lib.php') ) 
 include('view/'.$tmpl_dir.'/__commonfctrl_lib.php');
 
 if( file_exists(doc_root().'/'.$arr_cfg['SITE_DIR'].'/view/'.$tmpl_dir.'/__commonfsurf_lib.php') ) 
 include('view/'.$tmpl_dir.'/__commonfsurf_lib.php');
 
//
?>
<body>
<a name="top" id="top"></a>
<style>
.table-row:hover{ background-color:rgb(228,255,238) ; }
</style>
<?php
//
 include('view/'.$tmpl_dir.'/__topnav.php');
//
?>

<?php
 //
  
?>

 <div class="container-fluid overflow-hidden m-0 p-0 py-1">
 
 <?php /*
 <div class="card">
 <div class="card-body">
 */ ?>
 <div class="row m-0 p-0 border-0">

  <!-- div class="col-xl-1"> &nbsp; </div --> <!-- this is: BS 4 Beta !! -->
 
  <div class="col-12 m-0 p-1 border-0">
   <div id="id_the_center" style="min-height: 280px;" class="pb-1">
<?php
 //
  
  // a.
  //
 
  //
  if( strcmp($_REQUEST['r'],'login')==0 ) 
  if( $lvl>0 )
  { 
   $_REQUEST['r']='home';
   //
   $_REQUEST['do']='home'; // against jokes...
  }

  
  // b.
  //
  
  if( (strpos( $route_file,'.php' )>0)&&( !file_exists( doc_root().'/'.$arr_cfg['SITE_DIR'].'/view/'.$tmpl_dir.'/'.$route_file ) ) ){
	//
	$txt_noauth = file_get_contents( 'view/'.$tmpl_dir.'/warn_tpl_view_noauth.html' ) ;
	
	// applyTwigTemplate
	echo applyJazz1v8TemplateVars(  array( 'home' => $arr_cfg['SITE_DIR']  ), 
									$txt_noauth 
									);
	//
  }
 
 
  // c.     
  //
  
   //
    //
	if( strcmp($_REQUEST['r'],'home')==0 ){
	 //
	 
	 if( $lvl>0 ){
		//
		//include( 'home.php' );
		
		include( hook_it( 'budget-ctrl' ) ) ;  // __ on demand __ :-) mod. 2018-09-18
		
		//
	 }
	 else{
		//
		include( hook_it('login') ) ;
	 }
	 
	 //
	}
	else
     include( hook_it( $route_file ) );
    //
   //
  //
 
  

 //
?>
   </div>
  </div>
 
  <!-- div class="col-xl-1"> &nbsp; </div--> <!-- this is: BS 4 Beta !! -->
 </div>

</div><!-- #body container -->
<?php
//
//
$js_tmp_front_build = '<script>

jQuery(document).ready(function($){
 //
 
 $(".btn-href").on("click",function(){
  //
  location.href = this.getAttribute("href") ;
  //
 });

 //
 // 
 $(".btn-win-open").on("click",function(){ // JFS 1.8.3+
  //
  $("#win_"+this.getAttribute("data-id")).toggle() ;
  //
 });

 //
 // 
 $(".btn-win-close").on("click",function(){ // JFS 1.8.6+
  //
  $("#win_"+this.getAttribute("data-id")).hide() ;
  //
 });
 //
 
 '.( isset($_REQUEST['js-jump-to-id']) ? "\n document.getElementById('".$_REQUEST['js-jump-to-id']."').scrollIntoView(); \n" : "" ).'
 //
});

</script>';
//

//
 include('view/'.$tmpl_dir.'/__footer.php');
//


//
 addJSHook(JSHOOK_LAST, $js_tmp_front_build);
//

//
 appJSHook();
//
?>

</body>
</html>