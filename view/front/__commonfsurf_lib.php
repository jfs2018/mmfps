<?php
// v1.8.7+
// Common - FRONT-END - Surface Library (c) 2018 Jasmine Kft. 
//
// @start   2018-07-03 nr
// @lastmod 2018-07-10 nr
// @mod 2018-07-09 nr +display_jazz_alert_box()
//

//
// @doc JAP 6.5.3 fejezet
//
function display_jazz_alert_box( $arr_error = null ){
	//
	//note that:
	//#define( 'JAZZ_ITEMDEF_EVENT_MESSAGE',  6 ); // Message, General purpose
	//#define( 'JAZZ_ITEMDEF_EVENT_MESSAGE_WARNING', 8 );
	//#define( 'JAZZ_ITEMDEF_EVENT_MESSAGE_ERROR',   9 );
	//
		//
		$box_id = rand() ;
		
		//
		if( isset($arr_error) && is_array($arr_error) && (count($arr_error)>2) )
		{
			//
			$arr_txt_co = 'fg-amber'; // assumed value |  a 'default' egy amcsi programozói szleng és nem azt jelenti hogy "mulasztás esetére" :P
			$arr_brd_co = 'border-jazz-amber-lt-i';
			
			if( (int)$arr_error[1]==JAZZ_ITEMDEF_EVENT_MESSAGE  ){ $arr_txt_co = 'fg-oliv'; $arr_brd_co = 'border-jazz-oliv-lt2-i'; }
			
			// alert box
			echo '<div class="alert '.$arr_brd_co.' fontsiz10-i col-12 col-md-8" id="win_jazz187_alert_box'.$box_id.'"><p class="m-0 p-1 fg-gray3">';
			
			// cancel [x]
			echo '<span class="fa fa-times fa-fw fontsiz8-i fg-gray7 m-0 p-1 pt-0 btn-win-close mousexhand float-right" data-id="jazz187_alert_box'.$box_id.'"></span>' ;
			
			// icon
			if( ((int)$arr_error[1]==1)||((int)$arr_error[1]==JAZZ_ITEMDEF_EVENT_MESSAGE_ERROR) ){ // #1 means JAZZ_ITEMDEF_EVENT | <1.8.7 means 'error'
			 echo '<span class="fa fa-minus-circle fa-fw fg-nato-scarlet-i fontsiz10-i"></span> ' ;
			}
			else
			if( (int)$arr_error[1]==JAZZ_ITEMDEF_EVENT_MESSAGE_WARNING ){ echo '<span class="fa fa-warning fa-fw fg-amber-lt fontsiz11-i"></span> ' ; }
			else
			if( (int)$arr_error[1]==JAZZ_ITEMDEF_EVENT_MESSAGE ){ echo '<span class="fa fa-check fa-fw fg-green fontsiz10-i"></span> ' ; }
			
			// main title
			echo '<span class="fontsiz9-i fontbold">'.$arr_error[0].'</span> &nbsp;';
			
			// message
			echo $arr_error[2] ;
			
			echo '</p></div>';
		}
		else
		if( isset($arr_error) && is_string($arr_error) ){
			
			// alert box
			echo '<div class="alert border-jazz-amber-lt-i fontsiz10-i col-12 col-md-8" id="win_jazz187_alert_box'.$box_id.'"><p class="m-0 p-1 fg-gray5">';
			
			// cancel [x]
			echo '<span class="fa fa-times fa-fw fontsiz8-i fg-gray7 m-0 p-1 pt-0 btn-win-close mousexhand float-right" data-id="jazz187_alert_box'.$box_id.'"></span>' ;
			
			echo '<span class="fa fa-warning fa-fw fg-amber-lt fontsiz11-i"></span>&nbsp; ' ;
			
			// message
			echo $arr_error ;
			
			echo '</p></div>';
		}
	//
	
}//#display_jazz_alert_box()


//
// @doc JAP 6.5.2 fejezet
//
// @arg1 Title of the popup window
// @arg  Size of window | 0:compact (mini), 1:normal[default], 2:large
function display_jazz_popup_box_create( $title = null, $siz = 1 ){
 //
	$box_id = rand() ;
 
	echo "\n".'<div class="modal fade" id="popup_'.$box_id.'" tabindex="-1" role="dialog">';
	
	echo "\n".' <div class="modal-dialog';
	 //
	 if( (int)$siz==0 ){ echo ' modal-sm'; }
	 else
	 if( (int)$siz==2 ){ echo ' modal-lg'; }
	 else
	 echo ' modal-dialog-centered';
	 //
	echo '" role="document">';
	
	echo "\n".'  <div class="modal-content">';
	echo "\n".'   <div class="modal-body">'."\n";
 
	if( isset($title) && is_string($title) )
	{
		echo '<p class="fontsiz10 fg-gray3">'.$title.
			 ' <img src="'.site_dir().'img/fa/fa-times-7-gray7.png" id="popup_'.$box_id.'_dismiss" class="mousexhand p-1 float-right" data-dismiss="modal"></p>'.  
			 '<hr>'."\n";
	}
 
	return (int)$box_id ;
 //
}


//
// @doc JAP 6.5.2 fejezet
//
function display_jazz_popup_box_closure( $box_id = 0 ){
 //
	echo "\n".'</div></div></div></div>'.( (int)$box_id>0 ? '<!-- #popup-box-id-'.$box_id.' -->' : "" )."\n";
 //
}

