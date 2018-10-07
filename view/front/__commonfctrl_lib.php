<?php
// v1.8.7+
// Common - FRONT-END - Controls Library (c) 2018 Jasmine Kft. 
//
// @lastmod Robert 2018-07-03
//

function common_sample_x(){
 
	return true ;
	
}

// # Jazz 1v2.5+ Yazz&Yazy :: (c) 2017 Jasmine Kft. (Budapest HUN)
//

 // v1.2.5+
 function jazz1x5Paginator( $arr = null, $cnt__ = 0 ){
  //
    //
	global $arr_cfg ;
  
    //
	if( !isset($arr) ) return "" ;
	if( !is_array($arr) ) return "" ;
	
	if( !is_numeric($cnt__) ) return "" ;
	if( $cnt__ <1 ) $cnt__ = 0;
	//
  
	if( !isset($arr['st']) ) $arr['st'] = 0 ;
	//
	
	//
	$mx_= 1; // tapasztalati ├║ton...
	$pg_= 0; // mert az emberek "sajnos" 1-t┼Ĺl sz├ímolnak ├ęs nem 0-t├│l :))
	
	//
	if( isset($arr['pg'])  ){ $pg_ = (int)$arr['pg']; }
	if( isset($arr['rst']) ){ $pg_ = 1; } // reset
	//
	if( isset($arr['pg'])  ){
	// processing [ST] variable:
	 if( (int)$arr['st'] == -1 ){ $pg_-- ; }
	 else
	 if( (int)$arr['st'] == 1 ){ $pg_++ ; }
	}
	//
	if( $pg_<1 ){ $pg_ = 1; }
	//


    //
	$te_pgsiz_ = (int)$arr_cfg[ 'PGR_TEXT' ];
	
    //
    if( $cnt__ >0 ){
     //
     if( $cnt__ > $te_pgsiz_ ){
      //
      $mx_ = (int)($cnt__ / $te_pgsiz_ ); // eg├ęszoszt├ís
      //
      if( ($cnt__ % $te_pgsiz_) > 0 ){ $mx_++; } // ├ęs ha maradt t├Ârt ├ęrt├ęk, akkor +1 :)
     }  
    }
    //
    if( $pg_ > $mx_ ){ $pg_ = $mx_; }
    //

	//DBG: var_dump( [ $arr, 'pg_:'.$pg_, 'mx_:'.$mx_, 'cnt:'.$cnt__, 'te_pg:'.$te_pgsiz_ ] ) ;
 	
	//
	return yazzPaginator1( $pg_, $mx_, 0, 0, '', 0, 0 ) ;
	//
  //
  
 }//jazz1x5Paginator()
 
 
 
 //
 // yazzPaginator1 : extensions for stepper_v1 | from 1.2.4-18: 05/18/2017
 //
 // @param obyid : "order-by-Id" 1:ASC, -1:DESc, 0:inactive
 // @param obyname : "order-by-Name" 1:ASC, -1:DESc, 0:inactive
 //
 function yazzPaginator1( $pg=1, $mx=1, $cl=0, $pos=0, $lblText='', $obyid=0, $obyname=0 ){
  //
	//
	//echo '<p>in: pg='.$pg.' mx='.$mx.'</p>';
	//
	$r = '';
	$r.='<nav aria-label="'.$lblText.'">'."\n";
	$r.= '  <ul class="pagination pagination-sm m-0 py-1 ';
	//
	if( (int)$pos ==1 ){
	$r.= 'justify-content-end">'."\n";
	}
	elseif( (int)$pos ==2 ){
	$r.= 'justify-content-center">'."\n";
	}
	else
	$r.= '">'."\n"; // @bigfix! 12/07/17	
	//
	//
	$r.= '    <li class="page-item">'."\n";
	$r.= '      <a class="page-link" href="[LINK]&[PG]=1"><span class="fa fa-step-backward fa-1"></span></a>'."\n";
	$r.= '    </li>'."\n";
	$r.= '    <li class="page-item '.( ((int)$pg -1) == 0 ? " disabled " : '' ).'">'."\n";
	$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=-1"><span class="fa fa-angle-left fa-1"></span></a>'."\n";
	//$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=-1"><span class="fa fa-caret-left fa-1"></span></a>'."\n";
	//$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=-1"><span class="fa fa-chevron-left fa-1"></span></a>'."\n";
	//$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=-1"> &lt; </a>'."\n";
	$r.= '    </li>'."\n";
	//
	
	// 	//a max-2t is megn├ęzz├╝k
	$mx_m2 = $mx-2; // mx -2 ?
	$mxm2_show = 0; // kint van-e? | ha kint van akkor a pg+2 nem szabad kitenni
	//
	if( ($mx_m2 >0) && ( $mx_m2 < $pg ) && ( $mx_m2 != ($pg-1) ) ){ // csak akkor tegy├╝k ki ha a mx_m2 ├ęs a 0 k├Âz├Âtt van m├ęg lehets├ęges sz├ím├ęrt├ęk! :)
	 //
	 $mxm2_show = 1;
	 //
	 $r.= '    <li class="page-item">';
	 $r.= '<a class="page-link" href="[LINK]&[PG]='.$mx_m2.'">';
     $r.= (int)$mx_m2;
     $r.= '</a>'."\n";
	 $r.= '</li>'."\n";
	 //
	}

	//
	$pg_m1 = $pg-1; // pg -1 ?
	//
	if( $pg_m1 >0 ){ // csak akkor tegy├╝k ki ha a PG ├ęs a 0 k├Âz├Âtt van m├ęg lehets├ęges sz├ím├ęrt├ęk! :)
	 //
	 $r.= '    <li class="page-item">';
	 $r.= '<a class="page-link" href="[LINK]&[PG]='.$pg_m1.'">';
     $r.= (int)$pg_m1;
     $r.= '</a>'."\n";
	 $r.= '</li>'."\n";
	 //
	}
	
	
	$r.= '    <li class="page-item active">';
	$r.= '<span class="page-link">';
    $r.= (int)$pg;
    $r.= '<span class="sr-only">(current)</span>';
    $r.= '</span>'."\n";
	$r.= '</li>'."\n";
	
	//
	$pg_p1 = $pg+1; // pg +1 ?
	//
	if( $pg_p1 <= $mx ){ // csak akkor tegy├╝k ki ha a PG ├ęs a MX k├Âz├Âtt van m├ęg lehets├ęges sz├ím├ęrt├ęk! :)
	 //
	 $r.= '    <li class="page-item">';
	 $r.= '<a class="page-link" href="[LINK]&[PG]='.$pg_p1.'">';
     $r.= (int)$pg_p1;
     $r.= '</a>'."\n";
	 $r.= '</li>'."\n";
	 //
	}
	
	// 	//a pg+2t is megn├ęzz├╝k
	$pg_p2 = $pg+2; // pg +2 ?
	//
	if( ($pg_p2 < $mx) && ($pg == 1) ){ // csak akkor tegy├╝k ki ha a PG=1 eset├ęn mx_m2 ├ęs a 0 k├Âz├Âtt van m├ęg lehets├ęges sz├ím├ęrt├ęk! :)
	 //
	 $r.= '    <li class="page-item">';
	 $r.= '<a class="page-link" href="[LINK]&[PG]='.$pg_p2.'">';
     $r.= (int)$pg_p2;
     $r.= '</a>'."\n";
	 $r.= '</li>'."\n";
	 //
	}
	
	//
	//
	$r.= '    <li class="page-item '.( ((int)$pg +1) > (int)$mx ? " disabled " : '' ).'">'."\n";
	$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=1"><span class="fa fa-angle-right fa-1"></span></a>'."\n";
	//$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=1"><span class="fa fa-caret-right fa-1"></span></a>'."\n";
	//$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=1"><span class="fa fa-chevron-right fa-1"></span></a>'."\n";
	//$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$pg.'&[ST]=1"> &gt; </a>'."\n";
	$r.= '    </li>'."\n";
	$r.= '    <li class="page-item">'."\n";
	$r.= '      <a class="page-link" href="[LINK]&[PG]='.(int)$mx.'"><span class="fa fa-step-forward fa-1"></span></a>'."\n";
	$r.= '    </li>'."\n";
	//
	$r.= '  </ul>'."\n";
	
	/*
	//$1.2.4-18
	if( ($obyid!=0) || ($obyname!=0) ){
	 //
	 $r.= '<ul class="m-0 py-1">'."\n";
	
	 //
	 if( $obyid== 1 )
	 $r.= ' <li class="fa fa-sort-numeric-asc"></li>'."\n";
	 //
	 if( $obyid== -1 )
	 $r.= ' <li class="fa fa-sort-numeric-desc"></li>'."\n";
	
	 //
	 //
	 if( $obyname== 1 )
	 $r.= ' <li class="fa fa-sort-alpha-asc"></li>'."\n";
	 //
	 if( $obyname== -1 )
	 $r.= ' <li class="fa fa-sort-alpha-desc"></li>'."\n";
	
	 $r.= '</ul>'."\n";
	 //
	}
	//#1.2.4-18
	*/
	
	$r.= '</nav>'."\n";
	//
	//
	return $r;
	//
  //
 }//#
 
 //
 // Stepper v1. (L├ęptet┼Ĺ) produces BS-4-a-6 pager components
 // @param pg : currentPageNumber
 // @param mx : maxNumsOfPages
 // @param cl : colorScheme (light blue:1, green:2, red:3, gray:0)
 // @param pos : position (0:left, 1:right, 2:center
 // @param lbl : nav aria-label text
 //
 function stepper_v1( $pg=1, $mx=1, $cl=0, $pos=0, $lblText='' ){
  //
  return yazzPaginator1( $pg, $mx, $cl, $pos, $lblText, 0, 0 );
  //
 }//stepper-v1()

?>