<?php
//
// @lastmod 2018-10-02 17:34
//
  //@notes:
  //-- replace from 'success:' to done(fn(){) and wont be fired twice :P :)
//

//
$genjs_cbx_arr_pm = array(); // 2018-08-25
foreach( $arr_pmeth as $k => $v ) $genjs_cbx_arr_pm[] = array( $k,$v ) ;


//
$js_budget_head_ops ='
<script>
//
//function recalc_com_balance(){ ... } // dropped 20180927
//function recalc_exp_balance(){ ... } // dropped 20180927

//
jQuery(document).ready(function($){
		
  //
  $(".modifyBudgetPencil").on("click",function(){    //$(".modifyBudgetPencil").each(function(index) {
				
	$(".text_bc_def").toggle();
	$(".edit_bc_def").toggle();
				
  });
  
  //
  // exp: mod.value in a column
  //
  function fn_xcol_exp(){
  
   //
   var xcol_rid = $(this).attr("xcol-rid") ;
   var xcol_t = $(this).attr("xcol-t") ;
   var xcol_id = $(this).attr("xcol-id") ;
   var xcol_v = "" ;
   
   var html = null;
   var wi = 0;
   
   var undere = $(this).attr("undere") ; // under edition
   
   //
   if( undere==1 ) undere = $(this).children().length ; // clear sticky bits :):):) may this will correct it to 0 :)
   
   if( undere == 0 )
   switch( xcol_t ){
   
	case "id" : xcol_v = xcol_id;
			  break;
   
    //spec. "Customer":
	case "cust" :
	
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).attr("xcol-txt");
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" xcol-rid=\""+xcol_rid+"\" id=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" value=\"\" class=\"py-0 border-0 text-left\" style=\"width:100%;font-size:9pt;font-family:Sans-serif;\">" ;
			  
			  $(this).html( html ) ;
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).val("").val( xcol_v ).focus() ;
			  
			  // ...
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("blur",function(){
				
				$("#pa_"+xcol_rid).attr("undere",0) ; // first :)
				
				var rid = $("#pa_"+xcol_rid ).attr("xcol-rid") ;
				
				var v = $("#edit_exp_"+xcol_rid+"_"+xcol_t ).val() ;
				
				$("#pa_"+xcol_rid).html( v ) ;
				$("#pa_"+xcol_rid).attr( "xcol-txt",v ) ;
				
				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
				}).done( function( ret ){
		 
							if( ret.e == 0 )
							{
							 $("#pa_"+xcol_rid).attr( "xcol-id",ret.id ) ;
							}
							else
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});   
				
			  });
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();	
				 $( this ).trigger("blur") ;
				}
				else
				if ( keyc == 27 ) {
				 // Esc key:					 
				 $("#pa_"+xcol_rid).attr("undere",0) ; // first :)
				 $("#pa_"+xcol_rid).html(  $("#pa_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
			  });
			  
			  break;
	// txt:
	case "event" :
	case "venue" :
	case "evdt" :
	case "ptcp" :
	case "trv" :

			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).attr("xcol-txt");
			  xcol_v = xcol_v.replace(/\"/g,"");
			  
			  if( xcol_t=="event" ){ $(this).outerWidth(240); $(this).attr("width",240) ; }
			  else
			  if( xcol_t=="venue" ){ $(this).outerWidth(143); $(this).attr("width",143) ; }
			  else
			  if( xcol_t=="evdt" ){ $(this).outerWidth(114); $(this).attr("width",110) ; }
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" value=\"\" class=\"py-0 border-0 text-left\" style=\"width:100%;font-size:9pt;font-family:Sans-serif;\">" ;
			  
			  $(this).html( html ) ;
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).val("").val( xcol_v ).focus() ;
			  
			  // ...
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("blur",function(){
				
				$( this ).parent().attr("undere",0) ; // first :)
  
				var v = $( this ).val().trim() ;
				
				var rid = $(this).parent().attr("xcol-rid") ;
				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc": '.(int)$_REQUEST['id'].',"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
						success: function( ret ){
		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
		    
						}
				});   
				
				$( this ).parent().attr( "xcol-txt", v ) ;
				$( this ).parent().html( v ) ;
				//
			  });

			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();	
				 $( this ).trigger("blur") ;
				}
				else
				if ( keyc == 27 ) {
				 // Esc key:					 
				 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
				 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
			  });
			  
			  break;
			  
	case "subj" : 
			  
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).html();
			  xcol_v = xcol_v.replace(/\"/g,"");
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_subj\" id=\"edit_exp_"+xcol_rid+"_subj\" value=\"\" class=\"py-0 border-0 text-left\" style=\"width:100%;font-size:9pt;font-family:Sans-serif;\">" ;
			  
			  $(this).html( html ) ;
			  
			  $("#edit_exp_"+xcol_rid+"_subj" ).val("").val( xcol_v ).focus() ;
			  
			  // leave A.
			  $("#edit_exp_"+xcol_rid+"_subj" ).on("blur",function(){
				
				$( this ).parent().attr("undere",0) ; // first :)
  
				var v = $( this ).val().trim() ;
				
				var rid = $(this).parent().attr("xcol-rid") ;
   
				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,subj rid="+rid) },
						success: function( ret ){
		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
		    
						}
				});   
				
				$( this ).parent().attr( "xcol-txt", v ) ; // last! :)
				$( this ).parent().html( v ) ; // last! :)
				
			  });
			  
			  // leave B.
			  $("#edit_exp_"+xcol_rid+"_subj" ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();
				 $( this ).trigger("blur") ;
				}
				else
				if ( keyc == 27 ) {
				 // Esc key:					 
				 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
				 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
				
			  });
			  
			  break;	  
			  //# Subject
   
	case "invo" : 
			  
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).attr("xcol-txt");
			  xcol_v = xcol_v.replace(/\"/g,"");
	
			  wi = Math.round( (xcol_v.length*7) +4 );
			  if( wi<64 ) wi=64;
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" value=\"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:100%;max-width:"+wi+"px !important;font-size:9pt;font-family:Sans-serif;margin:0px !important;\">" ;
			  
			  $(this).html( html ) ;
			  
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).val("").val( xcol_v ).focus() ;
			  
			  // leave A.
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("blur",function(){
				
				$("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
  
				var v = $( this ).val().trim() ;
				
				var rid = $(this).parent().attr("xcol-rid") ;
   
				$("#"+xcol_t+"_"+xcol_rid).html( v ) ; // last! :)
				$("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt", v ) ; // last! :)

				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
						success: function( ret ){
		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
		    
						}
				});   
								
			  });
			  
			  // leave B.
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();	
				 $( this ).trigger("blur") ;				
				}
			    else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}				
				
			  });
			  
			  break;
			  //# Invo
 
	case "date":
	
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).html();
			  xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			  
			  if( xcol_v=="" ){ xcol_v = "'.date('Y-m-d').'"; }
			  else
			  if( xcol_v=="0000-00-00" ) xcol_v = "'.date('Y-m-d').'";
			  
			  $(this).css("width","86px") ;
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" value=\"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:96%;font-size:9pt;font-family:Sans-serif;margin:0px !important;\">" ;
			  
			  $(this).html( html ) ;
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).val("").val( xcol_v ).focus() ;

			  //
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("blur",function(){
				
				$( this ).parent().attr("undere",0) ; // first :)
  
				var v = $( this ).val().trim() ;
				
				var rid = $(this).parent().attr("xcol-rid") ;

				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
						success: function( ret ){
		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
		    
						}
				});   
				
				$( this ).parent().html( v ) ; // last! :)
				$( this ).parent().attr("xcol-txt", v ) ; // last! :)
				
			  });
			  
			  // leave B.
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();	
				 $( this ).trigger("blur") ;
				}
			    else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}				
				
			  });
			  
			  break;
			  //# Date
			  
	case "xr" : 
	case "ba" : 
			  
			  $(this).attr("undere", 1 ) ;
			  
			  xcol_v = Number( $(this).attr("xcol-a") ) ; 
			  
			  if( isNaN(xcol_v) ) 
			   xcol_v = 1.0; // bugfix 2018-09-18
			  
			  xcol_v = (""+xcol_v).replace(/\./,",");
			  //xcol_v = $(this).html(); xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,""); xcol_v = xcol_v.replace(/[A-Za-z]/g,"");
	
			  wi = Math.round( (xcol_v.length*7) +4 );
			  if( wi<64 ) wi=64;
			  
			  if( $(this).outerWidth() < 128 ) $(this).outerWidth( 128 ) ;
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" value=\""+xcol_v+"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:100%;max-width:"+wi+"px !important;font-size:9pt;font-family:Sans-serif;margin:0px !important;\">" ;
			  
			  if( xcol_t=="ba" ){ html = html+" "+base_cur; }
			  
			  $(this).html( html ) ;
			  
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).focus() ;
			  
			  // leave A.
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).bind("blur",function(){
  
				var xcol_v = $( this ).val().trim() ;
				
			    xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			    xcol_v = xcol_v.replace(/[A-Za-z]/g,"");
			    xcol_v = xcol_v.replace(/,-/,""); // HUF...				
				
				if( xcol_v.indexOf(".")==-1 ){
				 if( xcol_v.indexOf(",") >0 ){
				  xcol_v = xcol_v.replace(",",".");
				 }
				}	

				if( isNaN( xcol_v ) ) xcol_v = 0.0;
				
				var v = xcol_v ;
				
				var ty = $(this).parent().attr("xcol-t") ;
				
				if( ty=="xr" ) if( Number(v)==0.0 ) v=1.0 ; // !! XR cannot be 0.0 | 20181002
				
				var rid = $(this).parent().attr("xcol-rid") ;
			  
				// !
				$("#"+xcol_t+"_"+rid).attr("undere",0) ; // 2018-08-26
				
				$("#"+xcol_t+"_"+rid).html( v ) ; // alibi :) :) will be corrected after Ajax (Async) Call
   
				//
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
				}).done( function( ret ){
		 
							if( ret.e == 0 ){
							 //
							 var rid = ret.rid;
							 
							 var v = ret.v.toFixed(2).replace( ".", "," ) ;
							 
							 if( xcol_t=="xr" ){
							   //
							   $("#xr_"+rid).attr("xcol-a", ret.v ) ; // round( xr,4 )
							   $("#xr_"+rid).html( v ) ; // 
							   
							   //
							   $("#ba_"+rid).attr("xcol-a", ret.ba.toFixed(2) ) ; // ba-t is modositjuk!
							   $("#ba_"+rid).html( ret.bastr ) ; 
							   //
							 
							   if( !( items_exp_req[ret.req]===undefined ) )
							   {
							    items_exp_req[ret.req][ret.rid] = null;
							    items_exp_req[ret.req][ret.rid] = new Array( ret.ba, '.$basic_currency_id.') ;
							   }
							   
							   // bugfix: 2018-09-17
							   if( genjs_calc_exprev[rid]===undefined ) genjs_calc_exprev[rid] = new Array( 0,0,'.$basic_currency_id.',1.0,0 ) ; // 
							   //
							   genjs_calc_exprev[ rid ][4] = ret.ba.toFixed(2) ; // `ba` numerical value 2018-09-16
							   //
							 }
							 else
							 if( xcol_t=="ba" ){
							   //
							   $("#ba_"+rid).attr("xcol-a", ret.v.toFixed(2) ) ;
							   $("#ba_"+rid).html( ret.str ) ;							   
							 
							   if( !( items_exp_req[ret.req]===undefined ) )
							   {
							    items_exp_req[ret.req][ret.rid] = null;
							    items_exp_req[ret.req][ret.rid] = new Array( ret.v, '.$basic_currency_id.') ;
							   }
							   
							   // bugfix: 2018-09-17
							   if( genjs_calc_exprev[rid]===undefined ) genjs_calc_exprev[rid] = new Array( 0,0,'.$basic_currency_id.',1.0,0 ) ; // 
							   //
							   genjs_calc_exprev[ rid ][4] = ret.v.toFixed(2) ; // `ba` numerical value 2018-09-16
							   //
							 }
							 // -- es ez mindketto esetre! (xr,ba)
							   
							 // recalc_exp_balance: -- implemented in recalc_balance -- from 2018-09-16
							 
							 recalc_balance() ; // kieg 2018-09-13
							 //
							}
							else
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});
				
			  });
			  
			  // leave B.
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();	
				 $( this ).trigger("blur") ;
				}
			    else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}				
				
			  });
			  
			  break;
			  //# XR,BA
			  
	case "ia" :
			  
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).html();
			  xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			  xcol_v = xcol_v.replace(/[A-Za-z]/g,"");
			  xcol_v = xcol_v.replace(/,-/,""); // HUF...
	
			  wi = Math.round( (xcol_v.length*7) +4 );
			  if( wi<64 ) wi=64;
			  
			  if( $(this).outerWidth() < 128 ) $(this).outerWidth( 128 ) ;
			  
			  html = "<input type=\"text\" name=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_exp_"+xcol_rid+"_"+xcol_t+"\" value=\""+xcol_v+"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:"+wi+"px !important;font-size:9pt;font-family:Sans-serif;margin:0px !important;\"> <select name=\"cbx_exp_"+xcol_rid+"_"+xcol_t+"\" id=\"cbx_exp_"+xcol_rid+"_"+xcol_t+"\" class=\"fontsiz9 m-0 p-0 border-0\">'.$cbx_opts_crncy.'</select> " ;
			  
			  $(this).html( html ) ;
			  
			  $("#cbx_exp_"+xcol_rid+"_"+xcol_t ).val( $(this).attr("xcol-c") ) ; // ! Money
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).focus() ; // Focus now!
			  
			  // leave ONE WAY!
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			   var keyc = (ev.keyCode ? ev.keyCode : ev.which);
			   if ( keyc == 13 ) {
				
				ev.preventDefault();	
  
				var v = $( this ).val() ;
				
				var rid = $(this).parent().attr("xcol-rid") ;
				
				var ccc = $("#cbx_exp_"+rid+"_"+xcol_t).val() ;
				var ccn = $("#cbx_exp_"+rid+"_"+xcol_t).children(":selected").text() ;
				
				if( ( ccc===undefined )||( ccc==null )||( ccc==0 ) )
				{
				 ccc=base_cur_id;
				 ccn=base_cur;
				}
				
				// ! here after `var rid`
				$("#"+xcol_t+"_"+rid).attr("undere",0) ; // 2018-08-26
				
				$( this ).parent().html( v ) ; // alibi :) :) will be corrected after Async Call
   
				// SQL:
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"c":ccc,"ccn":ccn,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
				}).done(
						 function( ret ){
		 
							if( ret.e == 0 ){
							 //
							 $("#ia_"+rid).attr("xcol-a", ret.v ) ;
							 $("#ia_"+rid).attr("xcol-c", ret.c ) ; // currency!
							 $("#ia_"+rid).html( ret.str ) ;        // perfect
							 //
							 
							 //
							   $("#ba_"+rid).attr("xcol-a", Number( ret.ba.toFixed(2) ) ) ; // kieg 2018-09-18
							   $("#ba_"+rid).html( ret.bastr ) ;							   
							 
							   if( !( items_exp_req[ret.req]===undefined ) )
							   {
							    items_exp_req[ret.req][ret.rid] = null;
							    items_exp_req[ret.req][ret.rid] = new Array( ret.ia, ret.c ) ;
							   }
							   
							   if( !(ret.soe===undefined) )
							   {
							    sumof_exp_req[ ret.req ] = null;
								sumof_exp_req[ ret.req ] = new Array( ret.soe, ret.c ) ;
							   }
							   
							   // bugfix: 2018-09-17
							   if( genjs_calc_exprev[rid]===undefined ) genjs_calc_exprev[rid] = new Array( 0,0,ret.c,1.0,0 ) ; // 
							   
							   //
							   genjs_calc_exprev[ rid ][4] = Number( ret.ba.toFixed(2) ) ; // `ba` numerical value
							 
							   //
							   recalc_balance() ;
							   //
							}
							else
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});
				
				//#ENTER key ^M 
				//
			   }
			   else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#ia_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#ia_"+xcol_rid).html(  $("#ia_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
			   
			   
			  });
			  
			  // leave-by-dropDown-Enter!
			  $("#cbx_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			   var keyc = (ev.keyCode ? ev.keyCode : ev.which);
			   if ( keyc == 13 ) {
				
				ev.preventDefault();

				$("#edit_exp_"+xcol_rid+"_"+xcol_t ).trigger(ev); // átadjuk az Enter leütést :)
			   }
			   else
			   if( keyc==9 ){
			    e = new jQuery.Event("keydown") ; // TAB key handler
				e.keyCode = 13 ; e.which = 13 ;
				$("#edit_exp_"+xcol_rid+"_"+xcol_t ).trigger(e);
			   }
				else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#ia_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#ia_"+xcol_rid).html(  $("#ia_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
			   
			  });//#leave-by-dropDownEnter

			  
			  break;
			  //# IA
   
	case "req" : 
	case "pm" : 
	case "prj" : 
	case "pow" : 
	case "br" : 
	case "pa" : 
			  
			  $(this).attr("undere", 1 ) ;
			  
			  var arr_wi = { "req":78, "pm":75 } ;
			  var wi = "98%";
			  var mwi = "160px";
			  
			  switch( xcol_t ){			  
			   case "pm": 
			   case "req": wi="80px !important"; 
						   mwi="82px !important";
						   break;
			   case "br": 
			   case "pow": wi="70px !important";
						   mwi="72px !important";
						   break;
			  }
			  
			  var genjs_cbx = document.createElement("select");
			  genjs_cbx.id = "edit_exp_"+xcol_rid+"_"+xcol_t;
			  			  
			  
			  //$(genjs_arr_cbx).css( "width",wi ) ;
			  //$(genjs_arr_cbx).css( "max-width",mwi ) ;
			  //$(genjs_arr_cbx).css( "margin","0 !important" ) ;
			  
			  //$.each( genjs_arr_cbx[ xcol_t ], function(key, val) {
			  //	genjs_cbx.appendChild(new Option(val, key));
			  //});
			  
			  //
				if( xcol_t!="pm" ) genjs_cbx.appendChild( new Option( "-", 0 ) );
			  
				genjs_arr_cbx[ xcol_t ].forEach( function( a ){
			      genjs_cbx.appendChild( new Option(a[1], a[0]) );
			    });
			  //
			  
			  $(this).html("");
			  $(this).append( genjs_cbx ) ;
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).val( xcol_id ) ;
			  
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).focus() ;

			  //+ONCHANGE
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).bind("blur",function(){
				$( this ).parent().attr("undere",0) ;
				var v = $( this ).val() ;
				xcol_v = $(this).children(":selected").text();
				var rid = $(this).parent().attr("xcol-rid") ;
				$( this ).parent().attr("xcol-id",v);
				$( this ).parent().html( xcol_v ) ; // last! :)
				// SQL Update:
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid ) },
				}).done(
						 function( ret ){ if( ret.e != 0 )alert("Server-side SQL Update function failed. Code "+ret.e);	}
				);   
			  });
			  
			  //+KEYPRESS:
			  $("#edit_exp_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){
			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) { ev.preventDefault(); $( this ).trigger("blur") ; }
			  });
			  //#KEYPRESS
			  
			  break;
			  //# Combo fields			  
			  
		default:
			  break ;
			  
   }//#switch()
   
   //    
   //#modification-of-expenditures:
   //
  }
  
  $(".xcol-exp").on("click", fn_xcol_exp );
  //###
  
  // comm: mod.value in a column
  function fn_xcol_comm(){
   //
   var xcol_rid = $(this).attr("xcol-rid") ;
   var xcol_t = $(this).attr("xcol-t") ;
   var xcol_id = $(this).attr("xcol-id") ;
   var xcol_v = "" ;
   
   var html = null;
   var wi = 0;
   
   var undere = $(this).attr("undere") ; // under edition
   
   //
   if( undere==1 ) undere = $(this).children().length ; // clear sticky bits :):):) may this will correct it to 0 :)
   
   if( undere == 0 )
   switch( xcol_t ){
   
	case "id" : xcol_v = xcol_id;
			  break;
   
	case "subj" : 
	
			  xcol_v = $(this).html();
			  xcol_v = xcol_v.replace(/\"/g,"");
			  
			  html = "<input type=\"text\" name=\"edit_comm_"+xcol_rid+"_subj\" id=\"edit_comm_"+xcol_rid+"_subj\" value=\"\" class=\"py-0 border-0 text-left\" style=\"width:100%;font-size:9pt;font-family:Sans-serif;\">" ;
			  
			  $(this).attr("undere", 1 ) ;
			  
			  $(this).html( html ) ;
			  
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).val("").val( xcol_v ).focus() ;
			  
			  // leave A.
			  $("#edit_comm_"+xcol_rid+"_subj" ).on("blur",function(){
				
				$("#subj_"+xcol_rid).attr("undere",0) ; // first :)
  
				var v = $(this).val().trim() ;
				
				var rid = $("#subj_"+xcol_rid).attr("xcol-rid") ;
				
				$("#subj_"+xcol_rid).html( v ) ; // last! :)				
				$("#subj_"+xcol_rid).attr("xcol-txt", v ) ;
   
				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
						success: function( ret ){
		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
		    
						}
				});   
				
			  });
			  
			  // leave B.
			  $("#edit_comm_"+xcol_rid+"_subj" ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();
				 $("#edit_comm_"+xcol_rid+"_subj" ).trigger("blur") ;
				}
			    else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
				
			  });
			  
			  break;	  
			  
	case "xr" : 
	case "ba" :
			  
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).html();
			  xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			  xcol_v = xcol_v.replace(/[A-Za-z]/g,"");
	
			  wi = Math.round( (xcol_v.length*7) +4 );
			  if( wi<64 ) wi=64;
			  
			  html = "<input type=\"text\" name=\"edit_comm_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_comm_"+xcol_rid+"_"+xcol_t+"\" value=\""+xcol_v+"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:100%;max-width:"+wi+"px !important;font-size:9pt;font-family:Sans-serif;margin:0px !important;\">" ;
			  
			  if( xcol_t=="ba" ){ html = html+" "+base_cur; }
			  
			  $(this).html( html ) ;
			  
			  
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).focus() ;
			  
			  // leave A.
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).on("blur",function(){
  
				var xcol_v = $( this ).val() ;
				
			    xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			    xcol_v = xcol_v.replace(/[A-Za-z]/g,"");
			    xcol_v = xcol_v.replace(/,-/,""); // HUF...				
				
				if( xcol_v.indexOf(".")==-1 ){
				 if( xcol_v.indexOf(",") >0 ){
				  xcol_v = xcol_v.replace(",",".");
				 }
				}	

				if( isNaN( xcol_v ) ) xcol_v = 0.0;
				
				var v = xcol_v ;
				
				var ty = $(this).parent().attr("xcol-t") ;
				
				if( ty=="xr" ) if( Number(v)==0.0 ) v=1.0 ; // !! XR cannot be 0.0 | 20181002
				
				var rid = $(this).parent().attr("xcol-rid") ;
				
				// ! here after `var rid`
				$("#"+xcol_t+"_"+rid).attr("undere",0) ; // 2018-08-26
   
				// SQL Update:
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
				}).done(
						function( ret ){
		 
							if( ret.e == 0 ){
							 //
							 var rid = ret.rid;
							 
							 var v = ret.v.toFixed(2).replace( ".", "," ) ;
							 
							 if( xcol_t=="xr" ){
							   //
							   $("#xr_"+rid).attr("xcol-a", ret.v ) ;
							   $("#xr_"+rid).html( v ) ; // not here..
							   
							   $("#xr_"+rid).attr("xcol-txt", v ) ;
							   
							   if( genjs_calc_comm[rid]===undefined ) genjs_calc_comm[rid] = new Array( 0,0,base_cur_id,1.0,0 ) ;
							   genjs_calc_comm[rid][0]=Number( ret.req ) ;
							   genjs_calc_comm[rid][3]=Number( ret.v ) ; // xr
							   genjs_calc_comm[rid][4] = Number( ret.ba ) ;
							   
							   $("#ba_"+rid).html( ret.bastr ) ; // not here..
							   $("#ba_"+rid).attr( "xcol-txt", ret.bastr ) ; // not here..
							   //
							 }
							 else
							 if( xcol_t=="ba" ){
							   //
							   $("#ba_"+rid).attr("xcol-a", ret.v ) ;
							   $("#ba_"+rid).html( ret.str ) ; // !
							   $("#ba_"+rid).attr("xcol-txt", ret.str ) ; // + 20180925
							   
							   $("#xr_"+rid).attr("xcol-txt", ret.str ) ;

							   if( genjs_calc_comm[rid]===undefined ) genjs_calc_comm[rid] = new Array( 0,0,base_cur_id,1.0,0 ) ;
							   genjs_calc_comm[rid][0]=Number( ret.req ) ;
							   genjs_calc_comm[rid][4]=Number( ret.v ) ;
							   //
							 }
							 
							 recalc_balance() ;
							 //
							}
							else
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});
				
				$( this ).parent().html( v ) ; // alibi :) :) will be corrected after Async Call
				
			  });
			  
			  // leave B.
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault();	
				 $( this ).trigger("blur") ;
				}
			    else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
				
			  });
			  
			  break;
			  //# XR,BA
			  
	case "ia" :
			  
			  if( $(this).attr("xcol-a")===undefined ){
			   //
			   $(this).attr("xcol-a", 0) ;
			  }
			  
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).attr("xcol-a"); // !! Itt az amt-bol szamolunk!!
			  
			  xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			  xcol_v = xcol_v.replace(/[A-Za-z]/g,"");
			  xcol_v = xcol_v.replace(/,-/,""); // HUF...
			  
			  xcol_v = xcol_v.replace(/\./,","); // . -> ,
	
			  wi = Math.round( (xcol_v.length*7) +4 );
			  if( wi<64 ) wi=64;
			  
			  html = "<input type=\"text\" name=\"edit_comm_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_comm_"+xcol_rid+"_"+xcol_t+"\" value=\""+xcol_v+"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:64%;max-width:"+wi+"px !important;background-color:rgb(255,252,192);font-size:9pt;font-family:Sans-serif;margin:0px !important;\"> <select name=\"cbx_comm_"+xcol_rid+"_"+xcol_t+"\" id=\"cbx_comm_"+xcol_rid+"_"+xcol_t+"\" class=\"fontsiz9 m-0 p-0 border-0\">'.$cbx_opts_crncy.'</select> " ;
			  
			  $(this).html( html ) ;
			  
			  $("#cbx_comm_"+xcol_rid+"_"+xcol_t ).val( $(this).attr("xcol-c") ) ; // ! set Money Currency
			  
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).focus() ; // Focus now!
			  
			  // leave ONE WAY!
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			   var keyc = (ev.keyCode ? ev.keyCode : ev.which);
			   if ( keyc == 13 ) {
				
				ev.preventDefault();	
				
				//
				var rid = $(this).parent().attr("xcol-rid") ;
				var req = $(this).parent().attr("xcol-req") ;
				
				if( req===undefined ) req=0 ;
  
				//
				var v = $( this ).val() ;
				
			    v = v.replace(/\"/g,""); v = v.replace(/ /g,"");
			    v = v.replace(/[A-Za-z]/g,"");
			    v = v.replace(/,-/,""); // HUF...
				items_com_req[ req ][1] = Number(v) ; // beirjuk (regisztraljuk a valtozas kerest)
				//
				
				var ccc = $("#cbx_comm_"+rid+"_"+xcol_t).val() ;
				var ccn = $("#cbx_comm_"+rid+"_"+xcol_t).children(":selected").text() ;
				
				if( ( ccc===undefined )||( ccc==null )||( ccc==0 ) )
				{
				 ccc=base_cur_id;
				 ccn=base_cur;
				}
				
				//var sumexp = 0;
								
				// ! here after `var rid`
				$("#"+xcol_t+"_"+rid).attr("undere",0) ; // 2018-08-26
   
				// SQL Update:
				$.ajax({
						//async:false, /* !! important */
						
						type: "get",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v, "c":ccc,"ccn":ccn,"req":req,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
				}).done(
						 function( ret ){
							
							if( ret.e == 0 ){
							 //
							 rid = ret.rid;
							 
							 // IA:
							 $("#ia_"+rid).attr("xcol-a", ret.ia ) ; // az eredetit az `ia` adja vissza
							 $("#ia_"+rid).attr("xcol-c", ret.c ) ; // currency!
							 $("#ia_"+rid).html( ret.str ) ;        // perfect
							 
							 $("#ia_"+rid).attr("xcol-txt", ret.str ) ;        // 2018-09-19
							 
							 // BA:
							 $("#ba_"+rid).html( ret.bastr ) ;            // kieg 20180925
							 $("#ba_"+rid).attr("xcol-txt", ret.bastr ) ; //+kieg 20180925
							 
							 //items_com_req[ ret.req ][3] = ret.v ;
							 
							 //
							 if( genjs_calc_comm[rid]===undefined ) genjs_calc_comm[rid] = new Array( 0,0,ret.c,1,0 ) ;
							 genjs_calc_comm[rid][0]=Number( ret.req ) ;
							 genjs_calc_comm[rid][1]=Number( ret.v ) ;
							 genjs_calc_comm[rid][3]=Number( ret.xr ) ;
							 
							 if( isNaN(genjs_calc_comm[rid][3]) ) genjs_calc_comm[rid][3]=1.0 ; // bugfix 20180921
							 if( genjs_calc_comm[rid][3] <0.0 ) genjs_calc_comm[rid][3] = 1.0 ; 
							 
							 if( genjs_calc_comm[rid][3] >2.0 ){
							   genjs_calc_comm[rid][4]=Number( ret.v / genjs_calc_comm[rid][3] ) ; // osztani!! ha 2-nél nagyobb!
							 }else{
							   genjs_calc_comm[rid][4]=Number( ret.v * genjs_calc_comm[rid][3] ) ; // szorozni.. de ezt majd helyretesszuk...
							 }
							 
							 //
							 if( !(ret.soe===undefined) )
							 {
							  genjs_calc_comm[rid][5]=Number( ret.ia ) ; // kieg 20180921
							  genjs_calc_comm[rid][6]=Number( ret.soe ) ;
							  
							  //
							  if( sumof_exp_req[ ret.req ]===undefined )
							   sumof_exp_req[ ret.req ]=new Array(0.0,base_cur_id) ; // 20181002
							  
							  sumof_exp_req[ ret.req ][0] = ret.soe; // 20180921
							 }
							 
							 //recalc_com_balance() ; // old...
							 
							 recalc_balance() ; // and last
							 //
							}
							else
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});
				
				$( this ).parent().html( v ) ; // alibi :) :) will be corrected after Async Call
				
			   }//#ENTER key ^M 
			   else
			   if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#ia_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#ia_"+xcol_rid).html(  $("#ia_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
			   
			  });//#leave-by-inputboxEnter
			  
			  // leave-by-dropDown-Enter!
			  $("#cbx_comm_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			   var keyc = (ev.keyCode ? ev.keyCode : ev.which);
			   if ( keyc == 13 ) {
				
				ev.preventDefault();

				$("#edit_comm_"+xcol_rid+"_"+xcol_t ).trigger(ev); // rafinéria: átadjuk az Enter leütést a szövegboxnak :)
			   }
			   else
			   if( keyc==9 ){
			    e = new jQuery.Event("keydown") ; // TAB key handler: #13 !
				e.keyCode = 13 ; e.which = 13 ;
				$("#edit_comm_"+xcol_rid+"_"+xcol_t ).trigger(e);
			   }
				else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#ia_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#ia_"+xcol_rid).html(  $("#ia_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}
			   
			   
			  });//#leave-by-dropDownEnter
			  
			  break;
			  //# IA
 
	case "date":
	
			  $(this).attr("undere", 1 ) ;
	
			  xcol_v = $(this).html();
			  xcol_v = xcol_v.replace(/\"/g,""); xcol_v = xcol_v.replace(/ /g,"");
			  
			  if( xcol_v=="" ){ xcol_v = "'.date('Y-m-d').'"; }
			  else
			  if( xcol_v=="0000-00-00" ) xcol_v = "'.date('Y-m-d').'";
			  
			  $(this).attr("width",86) ;
			  
			  html = "<input type=\"text\" name=\"edit_comm_"+xcol_rid+"_"+xcol_t+"\" id=\"edit_comm_"+xcol_rid+"_"+xcol_t+"\" value=\"\" class=\"m-0 p-0 border-0 text-center\" style=\"width:96%;font-size:9pt;font-family:Sans-serif;margin:0px !important;\">" ;
			  
			  $(this).html( html ) ;
			  
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).val("").val( xcol_v ).focus() ;

			  //
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).bind("blur",function(){
				
				$( this ).parent().attr("undere",0) ; // first :)
  
				var v = $( this ).val().trim() ;
				
				var rid = $(this).parent().attr("xcol-rid") ;

				// SQL Update:
				$.ajax({
				
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
						success: function( ret ){
		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
		    
						}
				});   
				
				$( this ).parent().html( v ) ; // last! :)
				$( this ).parent().attr("xcol-txt", v ) ; // last! :)
				
			  });
			  
			  // leave B.
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).on("keydown",function(ev){

			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) {
				 ev.preventDefault(); 
				 $( this ).trigger("blur") ;
				}
			    else
				if ( keyc == 27 ) 
				{
					 // Esc key:					 
					 $("#"+xcol_t+"_"+xcol_rid).attr("undere",0) ; // first :)
					 $("#"+xcol_t+"_"+xcol_rid).html(  $("#"+xcol_t+"_"+xcol_rid).attr( "xcol-txt" )  ) ;
				}				
				
			  });
			  
			  break;
			  //# Date

   
	case "req" : 
	case "pm" : 
	case "prj" : 
	case "pow" : 
	case "br" : 
	case "pa" : 
			  
			  $(this).attr("undere", 1 ) ;
			  
			  var arr_wi = { "req":78, "pm":75 } ;
			  var wi = "98%";
			  var mwi = "160px";
			  
			  switch( xcol_t ){			  
			   case "pm": 
			   case "req": wi="80px !important"; 
						   mwi="82px !important";
						   break;
			   case "br": 
			   case "pow": wi="70px !important";
						   mwi="72px !important";
						   break;
			  }
			  
			  var genjs_cbx = document.createElement("select");
			  genjs_cbx.id = "edit_comm_"+xcol_rid+"_"+xcol_t;
			  			  
			  
			  //$(genjs_arr_cbx).css( "width",wi ) ;
			  //$(genjs_arr_cbx).css( "max-width",mwi ) ;
			  //$(genjs_arr_cbx).css( "margin","0 !important" ) ;
			  
			  //$.each( genjs_arr_cbx[ xcol_t ], function(key, val) {
			  //	genjs_cbx.appendChild(new Option(val, key));
			  //});
			  
			  //
				if( xcol_t!="pm" ) genjs_cbx.appendChild( new Option( "-", 0 ) );
			  
				genjs_arr_cbx[ xcol_t ].forEach( function( a ){
			      genjs_cbx.appendChild( new Option(a[1], a[0]) );
			    });
			  //
			  
			  $(this).html("");
			  $(this).append( genjs_cbx ) ;
			  
			  //if( xcol_t=="req" ){
			   //
			   //html = $(this).html()+req_modal_btn;
			   //$(this).html( html );
			  //}
			  
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).val( xcol_id ) ;
			  
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).focus() ;

			  //+ONCHANGE
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).bind("blur",function(){
				$( this ).parent().attr("undere",0) ;
				var v = $( this ).val() ;
				xcol_v = $(this).children(":selected").text();
				var rid = $(this).parent().attr("xcol-rid") ;
				// SQL Update:
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":xcol_t,"v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,"+xcol_t+" rid="+rid) },
						success: function( ret ){ if( ret.e != 0 )alert("Server-side SQL Update function failed. Code "+ret.e);	}
				});   
				$( this ).parent().attr("xcol-id",v);
				$( this ).parent().html( xcol_v ) ; // last! :)
			  });
			  
			  //+KEYPRESS:
			  $("#edit_comm_"+xcol_rid+"_"+xcol_t ).on("keydown keypress",function(ev){
			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) { ev.preventDefault(); $( this ).trigger("blur") ; }
			  });
			  //#KEYPRESS
			  
			  break;
			  //# Combo fields
			  
   }//#switch()
   
   //
   //#modification-of-commitments   
   //
  }
  
  // comm: mod.value in a column
  $(".xcol-comm").on("click", fn_xcol_comm ) ;
  //###
  
  
  // exp: új sor kattintás
  $("#exp_pluscol_title").on("click",function(){
   
   var expTableRef = document.getElementById("tbl_expend").getElementsByTagName("tbody")[0];

   var exp_data_rid = 0 ;
   
   var xrow_t = '.( ((int)$_REQUEST['id']>740000) && ((int)$_REQUEST['id']<750000) ? '908' : '906'   ).' ;
   
   // SQL Insert:
   $.ajax({
    type: "post",
    url: site_dir+"?svc=ajax-natomm-fps-bcedit",
    data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"new","t":xrow_t,"subj":"","cur":base_cur_id },
   
    async: false,
   
    dataType: "json",
    error:function(){ alert("Ajax call failed.") },
    success: function( ret ){
		 
		 if( ret.e == 0 ){ // return format is JSON array 
		  //
		  exp_data_rid = ret.id ;
		  
		  //alert("Success. ID="+ret.id) ;
		 }
		 else
		 alert("Server-side SQL Insert function failed. Code "+ret.e);
		    
    }
   });   

   if( exp_data_rid==0 ) return ;
   
   //   
   // Insert a row in the table at row index 0
   var newExpRow   = expTableRef.insertRow( last_exp_rownum ) ;
   
   $(newExpRow).attr("data-rid", exp_data_rid ); // ennek a BCode sornak a DB rowID-je...
   $(newExpRow).attr("id", "tr"+exp_data_rid ); // kieg. 2018-09-19
   
   var genjs_subj_id = "exp_"+exp_data_rid ;  // Date.now();
   
   //
   // !! register a new row in exp|Req calculator:   
   genjs_calc_exprev[ exp_data_rid ] = null;
   genjs_calc_exprev[ exp_data_rid ] = new Array( 0,0,'.$basic_currency_id.',1.0,0 ) ;
   //
   
';

//echo "// ".json_encode( $genJS_Array_expHead ) ."\n";

	$genjs_exp_pluscells="\n";

	// is set 'Subject' or 'Event' column instead
	//
	$has_subj_column = 0 ; // inverse logic 20180927
	//
	
$gcol_ = 0;
foreach( $genJS_Array_expHead as $htyp ){

 $genjs_exp_pluscells.='
    var exp_'.$htyp.'_cell = newExpRow.insertCell( '.$gcol_.' ) ;
        exp_'.$htyp.'_cell.innerHTML=" ";
        exp_'.$htyp.'_cell.setAttribute("xcol-rid", exp_data_rid) ;
        exp_'.$htyp.'_cell.setAttribute("xcol-t", "'.$htyp.'") ;
        exp_'.$htyp.'_cell.setAttribute("xcol-id", "0") ;
        exp_'.$htyp.'_cell.setAttribute("xcol-txt", "") ;
        exp_'.$htyp.'_cell.setAttribute("undere", "0") ;
';
 
 switch( $htyp ){
 
  case "id" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="right"; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("xrow-t", xrow_t) ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "rwid_"+exp_data_rid) ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("xcol-id", exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).addClass("xcol-num") ; '."\n". /* kieg 2018-09-19 */
									'      $(exp_'.$htyp.'_cell).addClass("mousexhand") ; '."\n". /* kieg 2018-09-19 */
									'        exp_'.$htyp.'_cell.innerHTML="&nbsp;"+( last_exp_rownum+1 )+"&nbsp"; '."\n" .
									'
									
			  $("#rwid_"+exp_data_rid ).on("click",function(){
				var r = Number( $(this).attr("xcol-id") ) ; 
				if( isNaN(r) ) r = 0;
				if( r>0 ) 
				if( confirm("Please confirm if you want to hide this row:" ) ){				 
				 //
				 $.ajax({ async:true, type: "get", url: site_url+site_dir+"?svc=b-switch", dataType: "json", 
					data: {"bc":bcid,"r":r,"a":0 }, /* off */
					error:function(){ alert("Ajax call failed.") },
				 }).done( function( ret ){ 
					 if( ret.e == 0 ){
						//
						$("#tr"+ret.r).toggle(); // hide complete <tr>
						//
						genjs_calc_exprev[ret.r] = null;
						genjs_calc_exprev[ret.r] = new Array( 0,0,base_cur_id,1.0,0 ) ;
						
						recalc_balance() ;
						//
					 }
					 else{ alert("Function failed. e:"+ret.e); }		
				 }) ;
				}
			  });	';
			  
			  break;
			  //

 
  case "event" : 
  case "venue" : 
  case "evdt" : 
  case "ptcp" : 
  case "trv" : 
  case "subj" : 
  
			  if( strcmp( $htyp,'subj' )==0 ){
			    //
				$has_subj_column = 1 ;
				
				$genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="left"; '."\n" ;
			  }
			  else
			  if( strcmp( $htyp,'event' )==0 )
			  {
				$genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="left"; '."\n" ;
			  }
			  else
			  {
				$genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n" ;
			  }
  
			  //
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.setAttribute("id", "'.$htyp.'_"+exp_data_rid) ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("xcol-id", exp_data_rid) ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("xcol-txt", "") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("undere", "1") ;'."\n".
									'        exp_'.$htyp.'_cell.innerHTML="<input type=\"text\" value=\"\" id=\"'.$htyp.'_"+genjs_subj_id+"\" xcol-rid=\""+exp_data_rid+"\" class=\"py-0 border-0 text-left\" style=\"width:100%;font-size:9pt;font-family:Sans-serif;\">" ; '."\n".
									
									'
			  // leave A.
			  $("#'.$htyp.'_"+genjs_subj_id ).on("blur",function(){
				var v = $(this).val().trim() ;
				var rid = $(this).parent().attr("xcol-rid") ;   
				$("#'.$htyp.'_"+rid).attr("undere",0) ; // first :)
				$("#'.$htyp.'_"+rid).html( v ) ; // !
				$("#'.$htyp.'_"+rid).attr( "xcol-txt",v ) ; // Ajax elott mert utana mas lesz a $(this) !! 20180927
				//
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":"'.$htyp.'","v":v },
						dataType: "json",
						error:function(){ alert("Ajax call failed.") },
						success: function( ret ){ if( ret.e != 0 )alert("Server-side SQL Update function failed. Code "+ret.e); }
				});   				
			  });
			  // leave B.
			  $("#'.$htyp.'_"+genjs_subj_id ).on("keydown",function(ev){
			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) { ev.preventDefault(); $("#'.$htyp.'_"+genjs_subj_id ).trigger("blur") ; }
			    else
				if ( keyc == 27 ) 
				{
					 $("#'.$htyp.'_"+rid).attr("undere",0) ; // first :)
					 $("#'.$htyp.'_"+rid).html(  $("#'.$htyp.'_"+rid).attr( "xcol-txt" )  ) ;
				}
			  });
'.
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "req" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n" .
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-0") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "req_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "prj" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n" .
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "prj_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "pow" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "pow_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "br" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "br_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "pm" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n".			  
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "pm_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "invo" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "invo_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "date" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "date_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "pa" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="center"; '."\n".			  
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-center px-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "pa_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "ia" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="right"; '."\n" .
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-right pr-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "ia_"+exp_data_rid) ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("xcol-c", '.$basic_currency_id.') ; '."\n". /* kieg. 2018-09-12 */
									'        exp_'.$htyp.'_cell.setAttribute("xcol-a", 0) ; '."\n". /* kieg. 2018-09-12 */
									'        exp_'.$htyp.'_cell.setAttribute("req", 0) ; '."\n". /* kieg. 2018-09-12 */
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "xr" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="right"; '."\n" .
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-right pr-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "xr_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;
 
  case "ba" : 
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.style.textAlign="right"; '."\n" .
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-right pr-1") ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("id", "ba_"+exp_data_rid) ; '."\n".
									'        exp_'.$htyp.'_cell.setAttribute("xcol-c", '.$basic_currency_id.') ; '."\n". /* kieg.180912 */
									'        exp_'.$htyp.'_cell.setAttribute("xcol-a", 0) ; '."\n". /* kieg.180912 */
									'      $(exp_'.$htyp.'_cell).on("click", fn_xcol_exp ) ; '."\n";
			  break;

  default:
			  $genjs_exp_pluscells.='        exp_'.$htyp.'_cell.setAttribute("id", "'.$htyp.'_"+exp_data_rid) ; '."\n".
									'      $(exp_'.$htyp.'_cell).addClass("xcol-exp text-right pr-1") ; '."\n".
			                        '        exp_'.$htyp.'_cell.setAttribute("xcol-txt", "") ; '."\n"; 
			  
			 
 }

 $gcol_++; 

}//#FOREACH cell_Head_Types


// 20180927
$genjs_subj_evt_focus = ( $has_subj_column==1 ? "\n//\n$(\"#subj_\"+genjs_subj_id).focus(); \n" : "\n//\n$(\"#event_\"+genjs_subj_id).focus(); \n" ) ;


$js_budget_head_ops.= $genjs_exp_pluscells.$genjs_subj_evt_focus.'

  //
  last_exp_rownum++ ;
  //
   
  }); // create new Exp row
  

  // ---------- ----------- COMMITMENTS ----------
  
  // Comm: új sor kattintás
  $("#comm_pluscol_title").on("click",function(){
   
   var commTableRef = document.getElementById("tbl_commit").getElementsByTagName("tbody")[0];

   var comm_data_rid = 0 ;
   
   // SQL Insert: | Type = 907 `com`
   $.ajax({
    type: "post",
    url: site_dir+"?svc=ajax-natomm-fps-bcedit",
    data: {"bc": '.(int)$_REQUEST['id'].',"yr":'.$fi_sel_yr.',"cmd":"new","t":907,"subj":"","cur":'.$basic_currency_id.' },
   
    async: false,
   
    dataType: "json",
    error:function(){ alert("Ajax call failed.") },
    success: function( ret ){
		 
		 if( ret.e == 0 ){ // return format is JSON array 
		  //
		  comm_data_rid = ret.id ;
		  
		  //alert("Success. ID="+ret.id) ;
		 }
		 else
		 alert("Server-side SQL Insert function failed. Code "+ret.e);
		    
    }
   });   

   if( comm_data_rid==0 ) return ;
   
   
   //   
   // Insert a row in the table at row index 0
   var newCommRow   = commTableRef.insertRow( last_comm_rownum ) ;
   
   $(newCommRow).attr("data-rid", comm_data_rid ); // ennek a BCode sornak a DB rowID-je...
   $(newCommRow).attr("id", "tr"+comm_data_rid ); // kieg. 2018-09-19
   
   var genjs_subj_id = "comm_"+comm_data_rid ; 
   
   //
   // !! register a new row in exp|Req calculator:   
   genjs_calc_comm[ comm_data_rid ] = null;
   genjs_calc_comm[ comm_data_rid ] = new Array( 0,0,348,1.0,0 ) ;
   //

   // Insert a cell in the row at index 0   
';

//
	//
	$genjs_comm_pluscells="\n";

$gcol_ = 0;
foreach( $genJS_Array_commHead as $htyp ){

 $genjs_comm_pluscells.='
    var comm_'.$htyp.'_cell = newCommRow.insertCell( '.$gcol_.' ) ;
        comm_'.$htyp.'_cell.innerHTML=" ";
        comm_'.$htyp.'_cell.setAttribute("xcol-rid", comm_data_rid) ;
        comm_'.$htyp.'_cell.setAttribute("xcol-t", "'.$htyp.'") ;
        comm_'.$htyp.'_cell.setAttribute("xcol-id", "0") ;
        comm_'.$htyp.'_cell.setAttribute("xcol-txt", "") ;
        comm_'.$htyp.'_cell.setAttribute("undere", "0") ;
';
 
 switch( $htyp ){
 
  case "id" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="right"; '."\n".
			  
									'        comm_'.$htyp.'_cell.setAttribute("xrow-t", 907) ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "rwid_"+comm_data_rid) ; '."\n".
			  
									'        comm_'.$htyp.'_cell.setAttribute("xcol-id", comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).addClass("xcol-num") ; '."\n". /* kieg 2018-09-19 */
									'      $(comm_'.$htyp.'_cell).addClass("mousexhand") ; '."\n". /* kieg 2018-09-19 */
									'        comm_'.$htyp.'_cell.innerHTML="&nbsp;"+( last_comm_rownum+1 )+"&nbsp"; '.";\n" .
									'
			  $("#rwid_"+comm_data_rid ).on("click",function(){
				var r = Number( $(this).attr("xcol-id") ) ; 
				if( isNaN(r) ) r = 0;
				if( r>0 ) 
				if( confirm("Please confirm if you want to hide this row:" ) ){				 
				 //
				 $.ajax({ async:true, type: "get", url: "'.site_url().site_dir().'?svc=b-switch", dataType: "json", 
					data: {"bc":'.(int)$_REQUEST['id'].',"r":r,"a":0 }, /* off */
					error:function(){ alert("Ajax call failed.") },
					success: function( ret ){ 
					 if( ret.e == 0 ){
						//
						$("#tr"+ret.r).toggle(); // hide complete <tr>
						
						//
						genjs_calc_comm[ret.r] = null;
						genjs_calc_comm[ret.r] = new Array( 0,0,'.$basic_currency_id.',1.0,0 ) ;
						
						recalc_balance() ;
						//
					 }
					 else{ alert("Function failed. e:"+ret.e); }
					}			
				 }) ;
				}
			  });	';
			  
			  break;
 
  case "subj" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="left"; '."\n".
			  
									'        comm_'.$htyp.'_cell.setAttribute("id", "subj_"+comm_data_rid) ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("undere", "1") ;'."\n".
									'        comm_'.$htyp.'_cell.innerHTML="<input type=\"text\" value=\"\" id=\"subj_"+genjs_subj_id+"\" xcol-rid=\""+comm_data_rid+"\" class=\"py-0 border-0 text-left\" style=\"width:100%;font-size:9pt;font-family:Sans-serif;\">" ; '."\n".
									
									'
			  // leave A.
			  $("#subj_"+genjs_subj_id ).bind("blur",function(){
				var v = $(this).val().trim() ;
				var rid = $(this).attr("xcol-rid") ;   
				$("#'.$htyp.'_"+rid).attr("undere",0) ; //1st
				$("#'.$htyp.'_"+rid).html( v ) ; // now!
				$("#'.$htyp.'_"+rid).attr( "xcol-txt",v ) ;
				// SQL Update:
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":"subj","v":v,"cur":base_cur_id },
						dataType: "json",
						error:function(){ alert("Ajax call failed.") },
						success: function( ret ){ if( ret.e != 0 )alert("Server-side SQL Update function failed. Code "+ret.e); }
				});   				
			  });
			  // leave B.
			  $("#subj_"+genjs_subj_id ).on("keydown",function(ev){
			    var keyc = (ev.keyCode ? ev.keyCode : ev.which);
				if ( keyc == 13 ) { ev.preventDefault(); $("#subj_"+genjs_subj_id ).trigger("blur") ; }
			  });
'.
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "req" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="center"; '."\n" .
									'      $(comm_'.$htyp.'_cell).addClass("xcol-comm text-center px-1") ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "req_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "prj" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="center"; '."\n" .
									'      $(comm_'.$htyp.'_cell).addClass("xcol-comm text-center px-1") ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "prj_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "pow" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'      $(comm_'.$htyp.'_cell).addClass("xcol-comm text-center px-1") ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "pow_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "br" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'      $(comm_'.$htyp.'_cell).addClass("xcol-comm text-center px-1") ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "br_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "date" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="center"; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "date_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "pa" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="center"; '."\n".			  
									'      $(comm_'.$htyp.'_cell).addClass("xcol-comm text-center px-1") ; '."\n".
									'        comm_'.$htyp.'_cell.setAttribute("id", "pa_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "ia" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="right"; '."\n" .
									'        comm_'.$htyp.'_cell.setAttribute("id", "ia_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "xr" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="right"; '."\n" .
									'        comm_'.$htyp.'_cell.setAttribute("id", "xr_"+comm_data_rid) ; '."\n".
									'      $(comm_'.$htyp.'_cell).on("click", fn_xcol_comm ) ; '."\n";
			  break;
 
  case "ba" : 
			  $genjs_comm_pluscells.='        comm_'.$htyp.'_cell.style.textAlign="right"; '."\n" .
									'        comm_'.$htyp.'_cell.setAttribute("id", "ba_"+comm_data_rid) ; '."\n".
									'      $("#ba_"+comm_data_rid).on("click", fn_xcol_comm ) ; '."\n";
			  break;
			  
 }

 $gcol_++; 

}//#FOREACH `com` cell_Head_Types


$js_budget_head_ops.= $genjs_comm_pluscells.'

  //
  $("#subj_"+genjs_subj_id).focus(); // !! :)

  //
  last_comm_rownum++ ;
  //
   
  }); // create new Comm row
  
  
  // POPUP Modals:
  
  $(".popup-req-save").on("click",function(){
  
   var r_circ = $("#id_popnew_req_cbx_circ").val();
   var r_num  = $("#id_popnew_req_txt_num").val();
   var r_abc  = $("#id_popnew_req_txt_abc").val();
   var r_yr   = $("#id_popnew_req_cbx_yr").val();
   var r_appr = $("#id_popnew_req_txt_comm").val();
   var r_txt  = $("#id_popnew_req_txt_txt").val();
   var r_cur  = $("#id_popnew_req_cbx_cur").val();
   
			$.ajax({
					async: true, /* ! */
					type: "post",
					url: "'.site_dir().'?svc=aulo-reqs",
					data: {"cmd":"new","circ":r_circ,"num":r_num,"yr":r_yr,"abc":r_abc,"appr":r_appr,"cur":r_cur,"txt":r_txt },
					dataType: "json",
					error:function(){ alert("Ajax call failed.") },
					success: function( ret ){ 
						
						if( ret.e == 0 ){
						 //alert("Success! Request Added: "+ret.req); 
						 
						 genjs_arr_cbx["req"] = ret.cbx ; // complette list to populate comboBox content
						 
						 $("#popup_'.$popup_req_id.'_dismiss").trigger("click") ;
						 
						}
						else
						if( ret.e == 1938 ){
						 alert("Insert failed: duplication! Request exists yet."); 
						}
						else{
						 alert("SQL Insert failed. Code: "+ret.e+" Param.:"+ret.p+" Msg:"+ret.msg); 
						}
					}
			});   				
       
  });
  
  
  //
  $(".popup-prj-save").on("click",function(){
  
   var prj  = $("#id_popnew_prj_txt_prj").val();
   var evt  = $("#id_popnew_prj_txt_evt").val();
   var pow  = $("#id_popnew_prj_cbx_pow").val();
      
			$.ajax({
					async: true, /* ! */
					type: "post",
					url: "'.site_dir().'?svc=aulo-projects",
					data: {"cmd":"new","prj":prj,"evt":evt,"pow":pow },
					dataType: "json",
					error:function(){ alert("Ajax call failed.") },
					success: function( ret ){ 
						
						if( ret.e == 0 ){
						 //alert("Success! Project Stored: "+ret.req); 
						 
						 genjs_arr_cbx["prj"] = ret.cbx ; // complette
						 
						 //
						 $("#id_popnew_prj_txt_prj").val("")
						 $("#id_popnew_prj_txt_evt").val("0")
						 $("#id_popnew_prj_cbx_pow").val(0);
						 
						 $("#popup_'.$popup_prj_id.'_dismiss").trigger("click") ;
						 
						}
						else
						if( ret.e == 1938 ){
						 alert("Insert failed: duplication! Project exists yet."); 
						}
						else{
						 alert("SQL Insert failed. Code: "+ret.e+" Param.:"+ret.p+" Msg:"+ret.msg); 
						}
					}
			});   				
   
  });

  
  
  //
  $(".popup-pa-save").on("click",function(){
  
   var pname  = $("#id_popnew_pa_txt_pname").val();
   var cc     = $("#id_popnew_pa_cbx_cc").val();
   
			$.ajax({
					async: true, /* ! */
					type: "post",
					url: "'.site_dir().'?svc=aulo-partners",
					data: {"cmd":"new","pname":pname,"cc":cc },
					dataType: "json",
					error:function(){ alert("Ajax call failed.") },
					success: function( ret ){ 
						
						if( ret.e == 0 ){
						 //alert("Success! Partner Stored: "+ret.req); 
						 
						 genjs_arr_cbx["pa"] = ret.cbx ; // complette
						 
						 //
						 $("#id_popnew_pa_txt_pname").val("")
						 
						 $("#popup_'.$popup_pa_id.'_dismiss").trigger("click") ;
						 
						}
						else
						if( ret.e == 1938 ){
						 alert("Insert failed: duplication! Partner exists yet."); 
						}
						else{
						 alert("SQL Insert failed. Code: "+ret.e+" Param.:"+ret.p+" Msg:"+ret.msg); 
						}
					}
			});   				
	});

	
	//
	var est_orig_sent = 0;
	
	//
	$("#td_bc_estorig").on("click",function(){
	   
	  $("#span_bc_estorig").html(base_cur+" ");
	  $("#id_bc_estorig").focus() ;
	 
	});
	//
	$("#td_bc_estorigmod").on("click",function(){
	
	 if( $(this).attr("undere")==0 ){
	 
	  var amt_e = $(this).attr("data-amt") ;
	  var rid = $(this).attr("data-rid") ;
	  
	  var html_e = "<input type=\"text\" name=\"id_bc_estorigmod\" id=\"id_bc_estorigmod\" value=\""+amt_e+"\" width=\"74\" class=\"m-0 p-0 border-0 text-right fontsiz9\" style=\"width:74px;\"> <span id=\"span_bc_estorigmod\" style=\"padding-right:2px;\">"+base_cur+"</span>";
	  
	  $(this).attr( "undere", 1 ) ;
	  
	  //
	  $(this).html( html_e );
	  
	  $("#id_bc_estorigmod" ).on("blur",function(ev){
		
		$(this).parent().attr("undere",0) ; // first :)
		
	    var num_txt = $(this).val() ;
		
		if( num_txt.indexOf(".")==-1 ){
		 //
		 if( num_txt.indexOf(",") >0 ){
		  //
		  num_txt = num_txt.replace(",",".");
		 }
		}	
		num_txt = num_txt.replace(base_cur,"");		
		num_txt = num_txt.replace(/ /g,"");		
	
		var amt = Number( num_txt ) ; // JS Number()
		
		if( isNaN(amt) ){ 
		 // var ev = new Event("keydown"); ev.keyCode = 27; $("#id_bc_estorigmod" ).trigger(ev) ; } // igy ne :)
		 
		 $("#td_bc_estorigmod").attr("undere",0) ; // first :)
		 amt_txt = (""+jsvar_balance_items["orig"].toFixed(2) ).replace( ".", "," );
		 $("#td_bc_estorigmod").html( nf_1000spaces( amt_txt ) +" '.$basic_currency_txt.'" ) ; 
		}
		
		//
		if( !isNaN(amt) )
		if( amt !=0 )
		if( amt != jsvar_balance_items["orig"] ){
			
			// est_orig_sent = 1; // old:  `semaphor` | new: jsvar_balance_items[orig]
			
			$.ajax({
					//async: false,
					type: "post",
					url: site_dir+"?svc=ajax-natomm-fps-bcedit",
					data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","t":"904","rid":rid,"amt":amt,"cur":base_cur_id },
					dataType: "json",
					error:function(){ alert("Ajax call failed.") },
			}).done( function( ret ){ 
					
					 if( ret.e!=0 ){ alert("SQL Update failed. e:"+ret.e); }
					 //est_orig_sent = 0 ;
					 
					 
					 jsvar_balance_items["orig"] = amt ;
					 
					 amt_txt = (""+amt.toFixed(2) ).replace( ".", "," );
					 
					 $("#td_bc_estorigmod").attr("data-amt", amt_txt ) ;
					 $("#td_bc_estorigmod").html( nf_1000spaces( amt_txt ) +" '.$basic_currency_txt.'" ) ;
					 
					 recalc_balance() ; // !! re-Calculator
			});
		}
		else
		{
		 $("#td_bc_estorigmod").attr("undere",0) ; // first :)
		 amt_txt = (""+jsvar_balance_items["orig"].toFixed(2) ).replace( ".", "," );
		 $("#td_bc_estorigmod").html( nf_1000spaces( amt_txt ) +" '.$basic_currency_txt.'" ) ; 
		}
		
	  });
	  //_Save Mod. op. B. `Enter`
	  $("#id_bc_estorigmod" ).on("keydown",function(ev){	    
		var keyc = (ev.keyCode ? ev.keyCode : ev.which);
		if ( keyc == 13 ) { ev.preventDefault(); $("#id_bc_estorigmod" ).trigger("blur") ; }
		else
		if ( keyc == 27 ) 
					{
					 // Esc key: set back unmodified amt.
					 
					 $("#td_bc_estorigmod").attr("undere",0) ; // first :)
					 
					 amt_txt = (""+jsvar_balance_items["orig"].toFixed(2) ).replace( ".", "," );
					 
					 $("#td_bc_estorigmod").html( nf_1000spaces( amt_txt ) +" '.$basic_currency_txt.'" ) ;					 
					}
	  });
	  
	  //
	  $("#id_bc_estorigmod").focus() ;
	 
	 }//? !undere
	});


	
    //_Save Mod. op. A. `Tab|Blur`
	$("#id_bc_estorig" ).on("blur",function(){
	
	    var num_txt = $(this).val() ;
		
		if( num_txt.indexOf(".")==-1 ){
		 //
		 if( num_txt.indexOf(",") >0 ){
		  //
		  num_txt = num_txt.replace(",",".");
		 }
		}	
		num_txt = num_txt.replace( base_cur,"" );
		num_txt = num_txt.replace(/ /g,"");
	
		var amt = Number( num_txt ) ; // JS Number()
		
		if( !isNaN(amt) )
		if( amt !=0 )
		if( est_orig_sent==0 ){
			
			est_orig_sent = 1; // `semaphor`
			
			$.ajax({
					async: false,
					type: "post",
					url: site_dir+"?svc=ajax-natomm-fps-bcedit",
					data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"new","t":"904","amt":amt,"cur":base_cur_id },
					dataType: "json",
					error:function(){ alert("Ajax call failed.") },
			}).done( function( ret ){ 
					 if( ret.e == 0 ){
						//
						location.href=site_url+site_dir+"?r=budget-ctrl&id="+bcid; 
					 }
					 else{ alert("SQL Insert failed. e:"+ret.e); }
			});
		}
	});
	//_Save Mod. op. B. `Enter`
	$("#id_bc_estorig" ).on("keydown",function(ev){
		var keyc = (ev.keyCode ? ev.keyCode : ev.which);
		if ( keyc == 13 ) { ev.preventDefault(); $("#id_bc_estorig" ).trigger("blur") ; }
	});
	
	
	
	//
	
	//
	$("#td_bc_estmod").on("click",function(){
	 
	  $("#span_bc_estmod").html("'.$basic_currency_txt.' ");
	  $("#id_bc_estmod").focus() ;
	  
	});
	
	var est_mod_sent = 0;
	
    //_Save Mod. op. A. `Tab|Blur`
	$("#id_bc_estmod" ).on("blur",function(){
	
		var amt = Number( $(this).val() ) ; // PICK UP A NEW Est.Mod. AMOUNT
		
		if( amt==null ) amt = 0 ; // bugfix 180913
		
		if( !isNaN(amt) )
		if( amt !=0 )
		if( est_mod_sent==0 ){
			
			est_mod_sent = 1; // `semaphor`
			
			/* --DO NOT Calculation!! location.href loads ALL from scrath! :)
			  // 0. nincs ezert ez alkalmas index lesz az uj elemnek...
			  jsvar_balance_items["mods"][0] = amt ; 
			  //
			  jsvar_balance_items["emod"]=0; // es ezt ujraszamoljuk:
			  for( var i in jsvar_balance_items["mods"] ){
				jsvar_balance_items["emod"] = Number( jsvar_balance_items["emod"] + jsvar_balance_items["mods"][i] ) ;
			  }
			*/
			
			$.ajax({
					async: false,
					type: "get",
					url: site_dir+"?svc=ajax-natomm-fps-bcedit",
					data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"new","t":"905","amt":amt,"cur":base_cur_id },
					dataType: "json",
					error:function(){ alert("Ajax call failed.") },
			}).done( function( ret ){ 
					 if( ret.e == 0 ){
						//
						//recalc_balance() ; // rather Call php again!
						location.href=site_url+site_dir+"?r=budget-ctrl&id="+bcid; 
					 }
					 else{ alert("SQL Insert failed. e:"+ret.e); }
			});
		}
		
		
		
	});
	//_Save Mod. op. B. `Enter`
	$("#id_bc_estmod" ).on("keydown",function(ev){
		var keyc = (ev.keyCode ? ev.keyCode : ev.which);
		if ( keyc == 13 ) { ev.preventDefault(); $("#id_bc_estmod" ).trigger("blur") ; }
		//else
		//if ( keyc == 27 ) { ... }
	});

});


$(".xcol-emod").on("click",function(){
 //
 var emod = Number( $(this).attr("data-amt") ) ; 
 var jtxt = ( ""+ emod.toFixed(2) ).replace( ".", "," ) ;
 
 var rid  = $(this).attr("rid") ;

  //
  //	
  if( $(this).attr("undere")==0 ){
	  
	  var html_e = "<input type=\"text\" name=\"edit_emod_"+rid+"\" id=\"edit_emod_"+rid+"\" value=\""+jtxt+"\" class=\"m-0 p-0 border-0 text-right fontsiz9\" style=\"width:60%;\"><span class=\"fontsiz9\"> "+base_cur+"</span>";
	  
	  $(this).attr( "undere", 1 ) ;
	  
	  //
	  $(this).html( html_e );
	  
	  //
	    //$("#edit_emod_"+rid ).val( emod ) ; // not-that-way
	    $("#edit_emod_"+rid ).focus() ;
	  //
  }	
  
  //
	//
	$("#edit_emod_"+rid ).on("blur",function(){
		//
	    var num_txt = $(this).val() ;
		var jtxt = $(this).val() ;
		
		if( num_txt.indexOf(".")==-1 ){
		 //
		 if( num_txt.indexOf(",") >0 ){
		  //
		  num_txt = num_txt.replace(",",".");
		 }
		}	
		num_txt = num_txt.replace( base_cur,"" );
		num_txt = num_txt.replace(/ /g,"");
	
		var amt_mod = Number( num_txt ) ; // JS Number()
		 
		if( isNaN( Number(amt_mod) ) ){
		  //
		  $("#est_error_txt").html("<span class=\"fa fa-warning fa-fw fg-amber fontsiz10-i\"></span> Format error: this is not a number.");
		  
	      //$("#edit_emod_"+rid ).val( jtxt ) ;
		  $("#edit_emod_"+rid ).focus() ;
		  //
		}
		else
		{
		 $("#est_error_txt").html("");
		 //
		
		 var text = nf_1000spaces( ( ""+ amt_mod.toFixed(2) ).replace( ".", "," ) ) +" "+base_cur ;
		 
		 //
		 $("#td_emod_"+rid ).attr("undere",0) ;
		 
		 $("#td_emod_"+rid ).html( text ) ;
		 $("#td_emod_"+rid ).attr("xcol-txt", text ) ;
		 $("#td_emod_"+rid ).attr("data-amt", amt_mod ) ;
		 
		 $("#td_emod_"+rid ).focus();
		 //
				//
				$.ajax({
				
						type: "get",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":"905","amt":amt_mod },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,905 ("+site_dir+"?svc=ajax-natomm-fps-bcedit) rid="+rid) },
				}).done(function( ret ){
						//
						if( ret.e == 0 )
						{
							 jsvar_balance_items["mods"][ret.rid] = ret.amt ;							 
							 recalc_balance();
						}
						else
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});
		 //
		}
		//
	});

  
	//_Save Mod. op. B. `Enter`|`Esc`
	$("#edit_emod_"+rid ).on("keydown",function(ev){
		//
		var keyc = (ev.keyCode ? ev.keyCode : ev.which);
		if ( keyc == 13 ) { ev.preventDefault(); $("#edit_emod_"+rid ).trigger("blur") ; }
		else
		if ( keyc == 27 ){
		 //
		 $("#td_bc_"+rid ).attr("undere",0) ;
		 var text = $("#td_emod_"+rid).attr("xcol-txt") ;
		 $("#td_emod_"+rid ).html( text ) ;
		 $("#td_emod_"+rid ).focus() ;
		}
	});  

});//#Estimate-Mod--onClick()
 
 
$(".xcol-jtxt").on("click",function(){
 //
 var jtxt = $(this).attr("xcol-txt") ;
 var rid  = $(this).attr("rid") ;
 var typ  = $(this).attr("xest-t") ;
 
  //
  //	
  if( $(this).attr("undere")==0 ){
	  
	  var html_e = "<input type=\"text\" name=\"jtxt_"+rid+"\" id=\"edit_bc_"+rid+"\" value=\"\" xest-t=\""+typ+"\" class=\"m-0 p-0 border-0 fontsiz9\" style=\"width:99%;\">";
	  
	  $(this).attr( "undere", 1 ) ;
	  
	  //
	  $(this).html( html_e );
	  
	  //
	    $("#edit_bc_"+rid ).val("").val( jtxt ).focus() ; // setCursorToLast :)
	  //
  }	
  
  //
	//
	$("#edit_bc_"+rid ).on("blur",function(){
		//
		 var text = $(this).val().trim() ; // trim TABs,#32,\n...
		 var typ  = $(this).attr("xest-t") ;
		 //
		 $("#td_bc_"+rid ).attr("undere",0) ;
		 $("#td_bc_"+rid ).html( text ) ;
		 $("#td_bc_"+rid ).attr("xcol-txt", text ) ;
		 //
				//
				$.ajax({
						type: "post",
						url: site_dir+"?svc=ajax-natomm-fps-bcedit",
						data: {"bc":bcid,"yr":fi_sel_yr,"cmd":"mod","rid":rid,"t":"jtxt","v":text },
						dataType: "json",
						error:function(){ alert("Ajax call failed. | fn=mod,jtxt rid="+rid) },
				}).done( function( ret ){		 
							if( ret.e != 0 )
							alert("Server-side SQL Update function failed. Code "+ret.e);
				});
		 //
	});
  
	//_Save Mod. op. B. `Enter`|`Esc`
	$("#edit_bc_"+rid ).on("keydown",function(ev){
		//
		var keyc = (ev.keyCode ? ev.keyCode : ev.which);
		if ( keyc == 13 ) { ev.preventDefault(); $("#edit_bc_"+rid ).trigger("blur") ; }
		else
		if ( keyc == 27 ){
		 //
		 $("#td_bc_"+rid ).attr("undere",0) ;
		 var text = $("#td_bc_"+rid).attr("xcol-txt") ;
		 $("#td_bc_"+rid ).html( text ) ;
		}
	});
 
 
 //
}); //#xcol-jtxt.on:click


$(".xcol-num").on("click",function(){
 //
 var r = Number( $(this).attr("xcol-id") ) ;
 
 if( isNaN(r) ) r = 0;
 
 if( r>0 )
 if( confirm("Please confirm if you want to delete this row:" ) )
 {
  //
  $.ajax({
				async:true,
				type: "get",
				url: site_url+site_dir+"?svc=b-switch",
				data: {"bc":bcid,"r":r,"a":0 }, /* off */
				dataType: "json",
				error:function(){ alert("Ajax call failed.") },
  }).done(
				function( ret ){ 
					 if( ret.e == 0 ){
						//
						$("#tr"+ret.r).toggle(); // hide complete <tr>
						//
						genjs_calc_exprev[ret.r] = null;
						genjs_calc_exprev[ret.r] = new Array( 0,0,base_cur_id,1.0,0 ) ;
						
						genjs_calc_comm[ret.r] = null;
						genjs_calc_comm[ret.r] = new Array( 0,0,base_cur_id,1.0,0 ) ;
						
						recalc_balance() ;
						//
					 }
					 else{ alert("Function failed. e:"+ret.e); }
  }) ;
  //
 }
 
 //
}); //#xcol-num.on:click


//  ... FUNCTIONS:

//
	function nf_1000spaces(nStr) { // NumberFormatThousandSpaces (c) https://jsfiddle.net/k07x1fuL/
		//
		nStr += "";
		x = nStr.split(",");
		x1 = x[0];
		x2 = x.length > 1 ? "," + x[1] : "";
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
            x1 = x1.replace(rgx, "$1" + " " + "$2");
		}
		
		return x1 + x2;
	}
	
	function recalc_balance(){
	  //
		//
		// Expenditures -- recalc:
		//
		genjs_sumof_exp = 0; 
		for( var i in genjs_calc_exprev )		
		if( !( genjs_calc_exprev[i]===undefined) && !( genjs_calc_exprev[i][4]===undefined) )
		{
		  genjs_sumof_exp += Number( genjs_calc_exprev[i][4] ) ; // `ba` numerical value
		}

	    jsvar_balance_items["exp"] = genjs_sumof_exp ;
	  
	    amt_txt = ( ""+ genjs_sumof_exp.toFixed(2) ).replace( ".", "," ) ;
	    $("#exp_foot_amt").html( nf_1000spaces( amt_txt ) ) ;
		
		$("#num_exp_foot_amt").val( genjs_sumof_exp ); // 2018-09-19
		//
	  
		//
		// Commitments -- recalc:
		//
		genjs_sumof_com = 0; 
		for( var i in genjs_calc_comm )		
		if( !( genjs_calc_comm[i]===undefined) && !( genjs_calc_comm[i][4]===undefined) )
		{
		  genjs_sumof_com += Number( genjs_calc_comm[i][4] ) ; // `ba` numerical value
		}

	    jsvar_balance_items["com"] = genjs_sumof_com ;
	  
	    amt_txt = ( ""+ genjs_sumof_com.toFixed(2) ).replace( ".", "," ) ;
	    $("#com_foot_amt").html( nf_1000spaces( amt_txt ) ) ;
		
		$("#num_com_foot_amt").val( genjs_sumof_com ); // 2018-09-19
		//
	  
	  //
	  var amt_txt = nf_1000spaces( ( ""+ jsvar_balance_items["orig"].toFixed(2) ).replace( ".", "," ) ) ;
	  
	  $("#sp_est_orig").html( amt_txt ) ;
	  $("#sp_est_authorig").html( amt_txt ) ;
	  //
	  
		//
		jsvar_balance_items["emod"] = 0; 
		for( var i in jsvar_balance_items["mods"] )		
		{
		  jsvar_balance_items["emod"] += Number( jsvar_balance_items["mods"][i] ) ;
		}
		
	  amt_txt = ( ""+ jsvar_balance_items["emod"].toFixed(2) ).replace( ".", "," ) ;
	  $("#sp_est_mod").html( nf_1000spaces( amt_txt ) ) ;
	  //
	  
	  jsvar_balance_items["eact"] = jsvar_balance_items["orig"] + jsvar_balance_items["emod"] ;
	  amt_txt = ( ""+ jsvar_balance_items["eact"].toFixed(2) ).replace( ".", "," ) ;
	  $("#sp_est_act").html( nf_1000spaces( amt_txt ) ) ;
	  //
	  
	  amt_txt = ( ""+ jsvar_balance_items["exp"].toFixed(2) ).replace( ".", "," ) ;
	  $("#sp_est_exp").html( nf_1000spaces( amt_txt ) ) ;
	  //
	  
	  amt_txt = ( ""+ jsvar_balance_items["com"].toFixed(2) ).replace( ".", "," ) ;
	  $("#sp_est_com").html( nf_1000spaces( amt_txt ) ) ;
	  //
	  
	  // ha 71,72,73 akkor minuszolunk ha 74 akkor mas a keplet
	  jsvar_balance_items["blnc"] = jsvar_balance_items["eact"] -jsvar_balance_items["exp"] -jsvar_balance_items["com"] ;
	  
	  amt_txt = ( ""+ jsvar_balance_items["blnc"].toFixed(2) ).replace( ".", "," ) ;
	  $("#sp_total_blnc").html( nf_1000spaces( amt_txt ) ) ;
	  //
	  
	}//#recalc_balance()
//
</script>
';
//
	addJSHook(JSHOOK_LAST, $js_budget_head_ops);
//