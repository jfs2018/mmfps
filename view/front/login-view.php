<?php
// login-view.php JazzFS 1.8.1+
//
if( !isset($wstoken) )die('Fatal runtime error: JazzFS token failed');
//

//echo json_encode($_REQUEST);

if( (isset($_REQUEST['do'])) && ( $_REQUEST['do']=="pass_rem" ) ){
?><div class="row">
 <div class="col-12 col-md-6">
 
 </div><!-- pass.rem -->
 <div class="col-12 col-md-6">
 &nbsp;
 </div>
</div><?php
}
else{
?><p> </p>
 <div class="row">
 
  <div class="col-md-4"> &nbsp; </div>
 
  <div class="col-12 col-md-4">
  <div class="card w-100 border-0">
   <div class="card-body pb-0">
   <p class="card-title pb-2 fontsiz11-i fontbold fg-tk-dark-gray border border-top-0 border-left-0 border-right-0">
   <?php echo $arr_lang['SYS_LBL_SIGNIN'] ; ?>
   </p>
   <form method="POST" action="<?php echo $arr_cfg['SITE_DIR']; ?>/?do=login">
    <input type="hidden" name="fn" value="login" />
    <fieldset class="form-group">
    <div class="input-group">
     <span class="input-group-addon fontsiz10" id="id_form_login_lbl1"><img src="<?php echo $arr_cfg['SITE_DIR']; ?>/img/fa/fa-user.png" style="width:16px !important;"></span>
     <input type="text" name="email" value="" class="form-control form-control-sm fontsiz10-i" data-validate="isEmail" aria-describedby="id_form_login_lbl1">
    </div>
    </fieldset>
    <fieldset class="form-group">
    <div class="input-group">
     <span class="input-group-addon fontsiz10" id="id_form_login_lbl2"><img src="<?php echo $arr_cfg['SITE_DIR']; ?>/img/fa/fa-lock.png" style="width:16px !important;"></span>
     <input type="password" name="pass" value="" class="form-control form-control-sm fontsiz10-i" aria-describedby="id_form_login_lbl2">
    </div>
    </fieldset>    
    <fieldset class="form-group">
     <button type="submit" class="btn btn-sm jazz-btn-cyan fontsiz10-i"><span class="fa fa-sign-in"></span> <?php echo $arr_lang['SYS_BTN_LOGIN'] ; ?></button>
     <!-- a href="#pass_rem" class="btn btn-link btn-sm fontsiz10" style="float:right;">Jelszó emlékeztető</a -->
    </fieldset>
   </form>
   </div>
  </div>
  </div>
  
  <?php /*
  <div class="col-md-3">
   &nbsp;
  </div> 
  
  <div class="col-md-4">
  <div class="card w-100 border-0">
   <div class="card-body">
    <!-- Opening hours -->
    <p class="card-title pb-2 fontsiz11-i fontbold fg-tk-dark-gray border border-top-0 border-left-0 border-right-0">
	Memo panel
    </p>
    <p class="card-text w-75 m-auto fontsiz9-i fg-nato-dk">
	...
    </p>
   </div>
  </div> 
  </div>
  */ ?>
  
 </div><!-- #user.login | row -->


</div>

<?php }#IF !pass_rem THEN Login Screen ?>
