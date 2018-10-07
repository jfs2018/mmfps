<?php
// @lastmod 2018-07-25 18:14 iHI
// @lastmod 2018-08-03 10:14 nr

 if( !isset($_REQUEST['fn']) )
 {
	$_REQUEST['item'] = 'welcome';
	include( hook_it( 'text' ) );
 }

 if( strcmp( $_REQUEST['fn'], 'fi_sel_yr' )==0 )
 {
	
	echo '<p class="py-2 pl-4 fontsiz10 fg-black"> &nbsp;<br>Select a year: </p>';
	
	for( $_y=date('y'); $_y>=13; $_y-- )
	{
		echo '<p class="pl-5 fontsiz10 fg-black"> <a href="'.$arr_cfg['SITE_DIR'].'/?r=home&fn=fi_sel_yr&chg_yr='.(int)$_y.'" class="fontsiz10-i">20'.$_y.'</a></p>';
	}
 }
?>
