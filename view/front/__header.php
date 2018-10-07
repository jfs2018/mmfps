<?php
// __header
// # JazzFS 1v8+ :: (c) 2018 Jasmine Kft. (Budapest HUN)
//
?><!DOCTYPE html><html lang="en" dir="ltr"><head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 
 <meta name="generator" content="Jazz v<?php echo $WSVER; ?> App v<?php echo $ver_tier.'.'.$ver_mst; ?> (c) HU <?php echo date('Y') ; ?> JFS Kft. (Budapest HUN)">
 <meta name="Author" content="Copyright (C) <?php echo date('Y'); ?> Jasmine FS Kft. | In service of NATO MILMED CoE">
 <!-- meta name="description" content="Fiscal Planning Software | NATO MILMED COE" -->
 <!-- meta name="keywords" content="" --> 
 
 <?php
 // 
 echo '<base href="'.$arr_cfg['SITE_URL'].'/" >'."\n" ;
 //
 ?>
 
 <!-- -->
 <title><?php echo $arr_cfg['SITE_TITLE']; ?></title>
 
 <!-- -->
 <?php
 //<link rel="icon" href="< ? php echo $arr_cfg['SITE_DIR']; ? >/img/favicon.ico" type="image/vnd.microsoft.icon" />
 ?>
 <link rel="shortcut icon" href="<?php echo $arr_cfg['SITE_DIR']; ?>/img/favicon.ico" type="image/x-icon" />
 
 <!-- google-site-verification -->
 <!-- meta name="google-site-verification" content="***" / -->
 
 <!-- bootstrap:4.0 Beta __marad!! még!!__ -->
 <link href="<?php echo $arr_cfg['SITE_DIR']; ?>/css/bootstrap.min.css" rel="stylesheet">
 <!-- link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous" -->
 
 <!-- bootstrap:4.1 Beta -->
 <!-- link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous" -->
 <!-- 4.1-nél FORM INPUT GROUP: prepend és append... van némi változás... -->
 
 <!-- jazz-v1.8-css : www -->
 <!-- link href="https://www.yourbank.hu/css/jazz-v1.8.yas.css" rel="stylesheet" -->
 <!-- jazz-v1.8-css : local -->
 <link href="<?php echo $arr_cfg['SITE_DIR']; ?>/css/jazz-v1.8.yas.css" rel="stylesheet">
 
 <!-- font-awesome-4.7  +PLUS+ material-2.2 for animations etc. -->
 <link  href="<?php echo $arr_cfg['SITE_DIR']; ?>/css/font-awesome.min.css" rel="stylesheet">
 <!-- link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" -->

 <!-- zmdi -->
 <link href="<?php echo $arr_cfg['SITE_DIR']; ?>/css/material-design-iconic-font.min.css" rel="stylesheet">

 <!-- datepickers -->
 <link href="<?php echo $arr_cfg['SITE_DIR']; ?>/css/dpg-datepicker.css" rel="stylesheet">
 
 <!-- -->
 <link rel="dns-prefetch" href="//ajax.googleapis.com" />

 <?php
 //
 if( file_exists(doc_root().'/'.$arr_cfg['SITE_DIR'].'/view/'.$tmpl_dir.'/__css.php') ) 
 include('view/'.$tmpl_dir.'/__css.php'); // ! CSS extensions on demand ! // v1.8.7
 //
?>

<?php

 // Policy: load them from 'local'
 addJSHook( JSHOOK_LOAD_LIB, '<script src="'.$arr_cfg['SITE_DIR'].'/js/jquery.min.js"></script>' );
 //addJSHook( JSHOOK_LOAD_LIB, '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>' );
 //
 // Policy: load them from 'local'
 addJSHook( JSHOOK_LOAD_LIB, '<script src="'.$arr_cfg['SITE_DIR'].'/js/tether.min.js"></script>' );
 //addJSHook( JSHOOK_LOAD_LIB, '<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>' );
 //
 //addJSHook( JSHOOK_LOAD_LIB, '<script src="'.$arr_cfg['SITE_DIR'].'/js/tether.min.js"></script>' );
 //
 // Policy: load them from 'local'
 addJSHook( JSHOOK_LOAD_LIB, '<script src="'.$arr_cfg['SITE_DIR'].'/js/popper.min.js"></script>' );
 
 // 4.1:
 addJSHook( JSHOOK_LOAD_LIB, '<script src="'.$arr_cfg['SITE_DIR'].'/js/bootstrap.min.js"></script>' );
 
 //<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
 
 //
 addJSHook( JSHOOK_LOAD_LIB, '<script src="'.$arr_cfg['SITE_DIR'].'/js/dpg-bootstrap-datepicker.js"></script>' );
 
 //
 // !! v1.8 jQuery
 //addJSHook( JSHOOK_LOAD_LIB, '<script> (function(jQuery){})($); </script>' ); // Jazz v1.8 recommendation // solves some runtime errs
 //
 
 //
 ?>  

<!-- script src='https://www.google.com/recaptcha/api.js'></script --> 
</head>
