<?php
// __topnav
// @lastmod 2018-09-18
//

if( $lvl >0 ){
?>
<!-- *size -->
<div class="container-fluid m-0 p-0 bg-oliv-v2">
 <div class="row nomnop">
 <div class="col nomnop">
 <p class="text-left fg-white fontsiz10 py-1 px-2 m-0">
 NATO MILMED COE &nbsp;&sdot;&nbsp; Fiscal Planning Software
 </p>
 </div>
 </div>
</div><!-- #container-fluid (on every media size) -->

<?php
	//
	if( !isset(	$arr_lang['SYS_LBL_MY_PROFILE'] ) ){ 
	 $arr_lang['SYS_LBL_MY_PROFILE']='My Profile' ;
	 
	 if( $langisoID==348 )$arr_lang['SYS_LBL_MY_PROFILE']='Fiókom';
	}
	
	$arr_lang['SYS_LBL_STRAIN_OF_DATA']='Key database';
	if( $langisoID==348 )$arr_lang['SYS_LBL_STRAIN_OF_DATA']='Törzsadatok';
	
	$arr_lang['SYS_LBL_MENUITEM_BUDGETMGNT']='Budget management';
	if( $langisoID==348 )$arr_lang['SYS_LBL_MENUITEM_BUDGETMGNT']='Költségvetés';
	
	$arr_lang['SYS_LBL_MENUITEM_FILE']='Special...';
	if( $langisoID==348 )$arr_lang['SYS_LBL_MENUITEM_FILE']='Főmenü';
	//
?>
<ul class="nav nav-tabs p-0">

  <li class="nav-item"> 
    <a class="nav-link fontsiz9 fg-oliv-dk2-i dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> <span class="fa fa-home fa-fw fontsiz10-i fg-green-dk2-i hide-on-xs"></span> <?php echo $arr_lang['SYS_LBL_MENUITEM_FILE']; ?> </a>
    <div class="dropdown-menu">

	  <!--
      <a class="dropdown-item fontsiz9 px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=home&fn=fi_sel_yr"><span class="fa fa-calendar fontsiz9-i fa-fw fg-green-dk2-i"></span> <?php echo $_SESSION["chg_yr"]; ?> &sdot; Select year...</a>  
	  <div class="dropdown-divider"></div>
	  
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=new-budget-code"><span class="fa fa-plus fa-fw fg-nato-i"></span> Add new Budget Code...</a>
      <div class="dropdown-divider"></div>
	  -->
	  
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=my-profile"><span class="fa fa-user fa-fw fg-oliv-i"></span> <?php echo $arr_lang['SYS_LBL_MY_PROFILE']; ?></a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?lang=en"><img src="<?php echo $arr_cfg['SITE_DIR']; ?>/img/us.png"> English</a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?lang=hu"><img src="<?php echo $arr_cfg['SITE_DIR']; ?>/img/hu.png"> Magyar</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?do=exit"><span class="fa fa-power-off fa-fw fg-red-dk-i"></span> <?php echo $arr_lang['SYS_BTN_EXIT']; ?></a>
    </div>	
  </li>
  
  <li class="nav-item"> 
    <a class="nav-link fontsiz9 fg-oliv-v2-i" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=budget-ctrl"><span class="fa fa-calculator fontsiz10-i fa-fw fg-tk-gray-dark-i"></span> <?php echo $arr_lang['SYS_LBL_MENUITEM_BUDGETMGNT']; ?></a> <!-- Költségvetés -->  
  </li>
  
  <?php /* // OLD 
  <li class="nav-item px-0 hide-on-xs hide-on-sm"> 
    <a class="nav-link fontsiz9 fg-oliv-v2-i" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=home&fn=fi_sel_yr"><span class="fa fa-calendar fontsiz9-i fa-fw fg-green-dk2-i"></span> <?php echo $_SESSION["chg_yr"]; ?> <span class="fa fa-angle-down fontsiz8-i"></span></a>  
  </li> */ ?>

  <li class="nav-item hide-on-xs hide-on-sm"> 
    <?php 
		$yrsel_link='?r=home&fn=fi_sel_yr' ; 
		if( strcmp( $_REQUEST['r'],'home' )!=0 ){ 
			//
			$yrsel_link='?r='.$_REQUEST['r'] ; 
			
			if( isset($_REQUEST['id']) ) $yrsel_link.='&id='.(int)$_REQUEST['id'] ;
		}
		
		$yrsel_full_link = site_url().site_dir().$yrsel_link;
		//
	?>
    <a class="nav-link fontsiz9 fg-oliv-v2-i dropdown-toggle" data-toggle="dropdown" href="<?php echo $arr_cfg['SITE_DIR'].$yrsel_link ?>" role="button" aria-haspopup="true" aria-expanded="false"> <span class="fa fa-calendar fg-green-dk2-i fa-fw"></span> <?php echo $_SESSION["chg_yr"]; ?> </a><!-- Year Selector -->
    <div class="dropdown-menu">
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $yrsel_full_link ?>&chg_yr=<?php echo (date('y') +1 ) ; ?>"><span class="fa fa-angle-right fontsiz9-i fg-green-dk2-i fa-fw"></span> <?php echo (date('Y') +1 ) ; ?> <span class="fontsiz8">(+1)</span></a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $yrsel_full_link ?>&chg_yr=<?php echo (date('y') ) ; ?>"><span class="fa fa-angle-right fontsiz9-i fg-oliv-dk2-i fa-fw"></span> <?php echo date('Y') ; ?></a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $yrsel_full_link ?>&chg_yr=<?php echo (date('y') -1 ) ; ?>"><span class="fa fa-angle-right fontsiz9-i fg-oliv-dk2-i fa-fw"></span> <?php echo (date('Y') -1 ) ; ?> <span class="fontsiz8">(-1)</span></a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $yrsel_full_link ?>&chg_yr=<?php echo (date('y') -2 ) ; ?>"><span class="fa fa-angle-right fontsiz9-i fg-oliv-dk2-i fa-fw"></span> <?php echo (date('Y') -2 ) ; ?> <span class="fontsiz8">(-2)</span></a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $yrsel_full_link ?>&chg_yr=<?php echo (date('y') -3 ) ; ?>"><span class="fa fa-angle-right fontsiz9-i fg-oliv-dk2-i fa-fw"></span> <?php echo (date('Y') -3 ) ; ?> <span class="fontsiz8">(-3)</span></a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $yrsel_full_link ?>&chg_yr=<?php echo (date('y') -4 ) ; ?>"><span class="fa fa-angle-right fontsiz9-i fg-oliv-dk2-i fa-fw"></span> <?php echo (date('Y') -4 ) ; ?> <span class="fontsiz8">(-4)</span></a>
    </div>	
  </li>

  <li class="nav-item"> 
    <a class="nav-link fontsiz9 fg-oliv-v2-i dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> <span class="fa fa-database fg-oliv-v2-i fa-fw hide-on-xs"></span> <?php echo $arr_lang['SYS_LBL_STRAIN_OF_DATA']; ?> </a><!-- Törzsadatok -->
    <div class="dropdown-menu">
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=list-of-reqs"><span class="fa fa-edit fa-fw"></span> &nbsp; Requests</a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=list-of-projects"><span class="fa fa-cog fa-fw"></span> &nbsp; Projects</a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=list-of-pows"><span class="fa fa-cogs fa-fw"></span> &nbsp; POWs</a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=list-of-branches"><span class="fa fa-institution fa-fw"></span> &nbsp; Branches</a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=list-of-sn"><span class="fa fa-flag fg-nato-i fa-fw"></span> &nbsp; SN</a>
      <a class="dropdown-item fontsiz9-i px-2" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?r=list-of-partners"><span class="fa fa-address-card-o fontsiz10-i fg-gray4-i fa-fw"></span> &nbsp; Partners</a>
    </div>	
  </li>

  
  <li class="nav-item hide-on-xs hide-on-sm"> 
    <a class="nav-link fontsiz9 fg-oliv-dk2-i" href="<?php echo $arr_cfg['SITE_DIR'] ?>/?do=exit"><span class="fa fa-power-off fa-fw"></span> <?php echo $arr_lang['SYS_BTN_EXIT']; ?></a>  
  </li>

  <?php /*
  <li class="nav-item">
    <a class="nav-link <?php echo ( (strcmp($_REQUEST['sh'],'gen')==0)||(strcmp($_REQUEST['sh'],'tm')==0) ? 'active' : '' ) ; ?> fontsiz10-i" href="<?php echo $arr_cfg['SITE_DIR'].'/?r=cong-edit&sh=tm&id='.(int)$_REQUEST['id']; ?>" id="a_nav_team" data-win="team"><span class="fa fa-random fa-fw fontsiz10-i fg-blue-sh"></span> Assignments</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ( strcmp($_REQUEST['sh'],'usr')==0 ? 'active' : '' ) ; ?> fontsiz10-i" href="<?php echo $arr_cfg['SITE_DIR'].'/?r=cong-edit&sh=usr&id='.(int)$_REQUEST['id']; ?>" id="a_nav_team" data-win="user"><span class="fa fa-user-plus fa-fw fontsiz10-i fg-blue-sh"></span> SysUsers</a>
  </li>
  */ ?>
</ul>

<?php
}
else{
?>

<div class="container-fluid bg-oliv-v2 fg-white px-5 d-md-block d-none d-sm-none">
 
   <div class="col m-0 p-0">
    <p class="m-0 p-0 py-3 fontsiz12">
    <a href="<?php echo site_dir(); ?>"><span class="fa fa-bar-chart fontplus20 fa-fw fg-white "></span></a>
	<b>
	<a href="<?php echo site_dir(); ?>" class="fg-white-i">
	NATO MILMED COE
	</a> 
	&nbsp;&sdot;&nbsp; 
	<a href="<?php echo site_dir(); ?>" class="fg-white-i">
	Fiscal Planning Software
	</a> 
	</b>
	</p>
   </div>
 
</div><!-- #container|show-on-md &upper -->
<div class="container-fluid m-0 p-0 bg-oliv-v2 fg-white d-block d-sm-block d-md-none">
 <div class="row nomnop">
 <div class="col nomnop">
 <p class="text-centered fg-white fontsiz10 py-1 px-0 m-0">
 NATO MILMED COE &nbsp;&sdot;&nbsp; Fiscal Planning Software
 </p>
 </div>
 </div>
</div><!-- #container|show-on-xs,show-on-sm -->

<?php
}//guest-view
?>
