<?php
 // 
 // ! THIS.CSS extensions on demand ! | v1.8.7+
 
 if( file_exists(doc_root().'/'.$arr_cfg['SITE_DIR'].'/view/'.$tmpl_dir.'/this.css') ) {
 
  // dinamic way:
  //echo '<link href="'.$arr_cfg['SITE_DIR'].'/?api&css=../view/'.$tmpl_dir.'/this.css&v='.date('ymdHis').'" rel="stylesheet">'."\n" ; 
 
  // static way:
  echo '<link href="'.$arr_cfg['SITE_DIR'].'/view/'.$tmpl_dir.'/this.css" rel="stylesheet">'."\n" ; 
 
 }
 //
?>
<style>

</style>
