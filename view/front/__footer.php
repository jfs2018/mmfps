<?php
// __footer
// # JazzFS 1v8+ :: (c) 2017/2018 Jasmine Kft. (Budapest HUN)
//
// 0.1: Tier 1 Milestone1
// ...
// 0.6: Tier 1 Milestone6
// 1.1: élesre átadva... először [ez ilyen... gondolat]
//
?>

<footer class="overflow-hidden">

<div class="container">

<div class="row m-0 p-0">
<!-- div class="col">&nbsp;</div>
<div class="col-10"><hr></div>
<div class="col">&nbsp;</div -->
</div>

</div>
 
<!-- -->
<?php
//
  if( !isset( $userDAO ) ){ $userDAO = $jazzEntityManager->get('JazzUser') ; }
//

if( $lvl>0 ){
 //
 echo '<div class="container-fluid bg-oliv-v2 d-md-block hide-on-xs hide-on-sm">';
}
else{
 //
 echo '<div class="container-fluid bg-oliv-v2 px-5 d-md-block hide-on-xs hide-on-sm">';
 //echo '<div class="container-fluid bg-nato-v3 px-5 d-md-block hide-on-xs hide-on-sm">';
}
?>
 <p class="fg-white fontsiz10 py-1">

<?php if( $uid >0 ) { ?>
 
 <span class="fa fa-user fontsiz9-i fg-green-lt fa-fw"></span>
 <span class="fontsiz9">
 <?php
    $userDAO->clearMe() ; $userDAO->setID( $uid ) ; $userDAO->loadMe() ; 
	
	echo json_decode( $userDAO->getValue('PersonName') ) ;
 ?>
 </span>
 
 &nbsp; 
 &nbsp;
 
 |
 &nbsp;
<?php }/*else{ ?>
 
 
 <!-- span class="fontsiz12-i fg-white">&raquo;</span -->
 <a href="#top"><span class="fa fa-navicon fontplus15 fg-white-i fa-fw"></span></a> 
 
<?php } */ ?>
 
 <span class="fontsiz9">
 Fiscal Planning System v<?php echo ($ver_tier-1) ; ?>.<?php echo $ver_mst; ?>
 
 &nbsp; 
 &sdot; 
 &nbsp;
 
 JFS core <?php echo $WSVER; ?>
 </span>
  
 <span style="float:right;">&copy; 2018 NATO COEMED | MilDev HU</span>
  
  </p>
</div><!-- #container|show-on-md &upper -->
<div class="container-fluid  m-0 p-0 d-md-none">
 <p class="text-centered fg-nato-v3 fontsiz11 py-0">

<?php if( $uid >0 ) { ?>
 
 <span class="fa fa-user fontsiz9-i fg-green fa-fw"></span>
 <span class="fontsiz9">
 <?php
    $userDAO->clearMe() ; $userDAO->setID( $uid ) ; $userDAO->loadMe() ; 
	
	echo json_decode( $userDAO->getValue('PersonName') ) ;
 ?>
 <br>
<?php }/*else{ ?>
 
 
 <!-- span class="fontsiz12-i fg-white">&raquo;</span -->
 <a href="#top"><span class="fa fa-navicon fontplus15 fg-white-i fa-fw"></span></a> 
 
<?php }*/ ?>

 <span class="fontsiz9">
 Fiscal Planning System v<?php echo ($ver_tier-1) ; ?>.<?php echo $ver_mst; ?>
 
 &nbsp; 
 &sdot; 
 &nbsp;
 
 JFS core <?php echo $WSVER; ?>
 </span>
 
 </p>
</div>
<div class="container-fluid m-0 p-0 bg-oliv-v2 d-block d-sm-block d-md-none">
<div class="row m-0 p-0">
<div class="col m-0 p-0">
 <p class="text-centered fg-white fontsiz10 m-0 py-1 px-0">
 &copy; 2018 NATO COEMED | MilDev HU
 </p>
</div>
</div> 
</div><!-- #container|show-on-xs,show-on-sm -->
</footer><!-- #foot -->

<?php
//
 $js_footer='
<script>
jQuery(document).ready(function($){
 //
 $(".soonbeupld").on("click",function(){
  //
  alert("Soon it will be uploaded") ;
  //
 });
 //
}); 
</script>'."\n";

addJSHook(JSHOOK_LAST, $js_footer);
//
?>