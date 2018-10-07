<?php
/**
 * JazzFS 1.8.7+ | REWRITTEN !! with DAOs | !! WARNING !! This is not recommended with old (1.2.3,1.2.4 users table!!)
 *
 * Ez a modul egyszerű profilszerkesztő (ehhez nincs Role + ACL szabályzás -> az az 1.2.5.11+ NATO)
 *
 * @src profile-edit.php
 *
 * @author Robert
 * 2018-01-11
 * @lastmod 2018-07-07
 */
//
//
if( !isset($wstoken) )die('Fatal runtime error: WS token failed');
if( $lvl <1 )die('Fatal runtime error: no GRANT to call this module.');
//
//
?>


<?php
//
	$userDAO = $jazzEntityManager->get('JazzUser'); 
//

//var_dump( $_REQUEST ) ;

if( isset($_REQUEST['fn']) )
if( $_REQUEST['fn']=='u' )
if( (int)$_REQUEST['uid']==$uid )
{
	//
	if( strcmp( $_REQUEST['p1'],$_REQUEST['p2'] )==0 ){
	
	  //
	  // OLD 1.2.4: $u_name = str_replace( '"',"", str_replace( "\\","\\\\",json_encode($_REQUEST['n']) ) ) ;
	  
	  //1.8+:
	  $u_name = jsonobject4SQL( lossSQLinjectionChars( $_REQUEST['n'] ) ) ;
	  
	  //
	  $salt12510 = md5( rand().rand() ) ; // v1.2.5-10+
	  $paas12510 = md5( $salt12510.$_REQUEST['p1'] ) ;
	  //
	  
	  //
	  //
		$userDAO->clearMe();
		
		$userDAO->setID( $uid ) ;
		
		$userDAO->setValue('PersonName', $u_name ) ;
		
		if( strlen($_REQUEST['p1'])>0 ){
		 //
		 $userDAO->setValue('Password', $paas12510 ) ;
		 $userDAO->setValue('Salt',     $salt12510 ) ;
		}
		
		$userDAO->updateMe();
	  //
	  //	  
	}
	else{
	  //
	  echo "<p class='m-1 p-2 fg-red-lt fontsiz10 fontbold'><span class='fa fa-warning fa-fw fontplus25'></span> Pass key and repeated key did not match.</p>";
	}

}//#UPDATE
else
die('Fatal runtime error: no GRANT to UPDATE accounts other than you are.');



//
 // Read Profile Data
 
 //
 $s=''; $r=null; $rw=null;

 //
 $s = " SELECT `id`,`a`,`lvl`,`name`,`email`,`jd` ".
      " FROM `".DBPFX."jaz1_user` ".
	  " WHERE `id`=". $uid ;
	  
 $r = $yc->query( $s );
 
 //var_dump( $r );
 
 if( $r )
 $rw=$r->fetch_array( MYSQLI_BOTH )  ;
 
 if( !is_array($rw) )
 die('Fatal SQL Query error: cannot read users profile data.');
 
 $u_name = json_decode( $rw['name'] ) ; // old-style 1.2.4: json_decode(  '"'.$rw['name'].'"' ) ;
 
//

//
?>
<div class="card m-1 p-1 col-12 col-md-9 col-xl-8 border-0">
<div class="card-body">

    <h3 class="card-title fontsiz12-i"><span class="fa fa-user fa-fw fg-tk-green fontsiz12-i"></span> My Profile </h3>
	
	<hr>
	
	<form method="POST" action="<?php echo $arr_cfg['SITE_DIR'] ; ?>/?r=my-profile">
	
		<input type="hidden" name="id" value="<?php echo $uid; ?>"> 
		<input type="hidden" name="fn" value="u"><!-- u:update -->
		<input type="hidden" name="uid" value="<?php echo $uid; ?>"> 
		
		<div class="row">
			<div class="col-sm-2 hide-on-xs">
				&nbsp;
			</div>
			<div class="col-sm-4">
			
				<div class="form-group">  
				<label class="label-form fontsiz10" id="id_n_p"><i class="fa fa-user fontsiz12-i fa-fw fg-tk-primary"></i> Real Name:</label>  
				<input type="text" tabindex="1" class="form-control fontsiz10" name="n" value="<?php echo $u_name ; ?>"> 
				</div>
				
			</div>
			<div class="col-sm-4">
			
				<div class="form-group">  
				<label class="label-form fontsiz10" id="id_n_p"><i class="fa fa-lock fontsiz11-i fa-1x fg-gray7"></i>&nbsp; Password:</label>  
				<input type="text" tabindex="11" class="form-control fontsiz10" name="p1" value=""> 
				</div>
				
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-2 hide-on-xs">
				&nbsp;
			</div>
			<div class="col-12 col-sm-4">
			
				<div class="form-group">  
				<label class="label-form fontsiz10" id="id_em_p"><i class="fa fa-envelope fontsiz11-i fa-fw fg-tk-primary"></i> Account:</label>  
				<input type="text" tabindex="2" class="form-control fontsiz10 bg-gray-lt6-i" readonly name="em" value="<?php echo $rw['email'] ; ?>"> 
				</div>
				
			</div>
			<div class="col-12 col-sm-4">
			
				<div class="form-group">  
				<label class="label-form fontsiz10" id="id_pass1"><i class="fa fa-lock fontsiz11-i fa-1x fg-gray7"></i>&nbsp; Password, re-type:</label>  
				<input type="text" tabindex="12" class="form-control fontsiz10" name="p2" value=""> 
				</div>
				
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-2 hide-on-xs">
				&nbsp;
			</div>
			<div class="col-12 col-sm-4">
			
			</div>
			
			<div class="col-12 col-sm-4 text-to-right">
			
			<button type="submit" class="btn btn-sm btn-success fontsiz10-i"> <span class="fa fa-save fa-fw"></span> <?php echo $arr_lang['SYS_BTN_SAVE'] ?></button> 
			
			</div>			
		</div>
	
	</form>

</div>
</div>
<?php 
 //var_dump( [ $uid, $lvl   ] ) ;
 //var_dump( $arr_cfg ) ;
?>