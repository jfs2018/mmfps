<?php
// # Jazz 1v8+(c) 2018 Jasmine FS Kft.
//
// text.php | User mode (1.8 with JazzDAO)
//
// @lastmod 2018/06/21
//

// load Page:
//
 $pg = str_replace("'",'',$_REQUEST['item']);   $pg = str_replace(';','',$pg);   $pg = str_replace("\\",'',$pg);   $pg = str_replace('"','',$pg);
//
//
	$text = $jazzEntityManager->get('JazzText');

	//$text->setLanguage( $langcode );
	$text->setValue( 'LanguageID', $langisoID ) ;
	
	$text->setValue( 'ItemTypeID', JAZZ_ITEMDEF_TEXT ) ; // #16: Text and not Product etc. | mod 2018/06/21
	
	$text->setKey(  $pg  );
	
	$text->loadMe();
//

//
  echo "<div id=\"jazzfs_text_article\">\n";
	
	//
	if( ($text->getID()==0) || !( ((int)$text->getValue('Status')==1)||((int)$text->getValue('Status')==19) ) ){
	 //

	 $txt_noauth = file_get_contents( 'view/'.$tmpl_dir.'/warn_tpl_text_noauth.html' ) ;
	
	 // applyTwigTemplate
	 echo applyJazz1v8TemplateVars( array( 'home' => $arr_cfg['SITE_DIR']  ), 
									$txt_noauth 
									);
	 
	 //
	}
	//
	
	//
	$ti_ = $text->getTitle();
	
	if( strlen(trim(''.$ti_))>0 )
	echo '<h1 class="m0 p0 my-1 py-1 fg-green-dk text-justified  fontsiz19-i fontbold">'.$ti_.'</h1>' . "\n" ; // 2018/06/27 ide ez a szín kell
	//echo '<h1 class="m0 p0 my-1 py-1 fg-blue-dk3 text-justified  fontsiz16-i fontbold">'.$ti_.'</h1>' . "\n" ; // 04/09/18
	
	echo '<div class="fontsiz11 text-justified booktext">&nbsp;<br>';
	
    $tx_ = str_replace( '<h1>','<h1 class="m0 p0 my-1 py-1 fontsiz16-i fontbold">', $text->getContent() );
    echo str_replace( '&#34;','"', $tx_ );
	
	//print_r( json_decode( $text->getJsonData() ) ) ;
  
    echo '</div>';
    //
  

  echo "\n</div>\n<!-- #jazzfs_text_article -->\n";

//
?>
