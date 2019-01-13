<?PHP

include_once ( 'php/common.php' ) ;

# ______________________________________________________________________
# BEGIN OF CLASS WikiQuery

class WikiQuery {
	var $language , $project ;
	
	function WikiQuery ( $language , $project = 'wikipedia' ) {
		if ( $language == 'commons' ) $project = 'wikimedia' ;
		if ( $language == "xxx" ) $language = "" ;
		$this->language = $language ;
		$this->project = $project ;
	}
	
	function get_api_base_url ( $what = '' ) {
    $ret = "http://" ;
    if ( $this->language != '' ) $ret .= "{$this->language}." ;
		$ret .= "{$this->project}.org/w/api.php?format=php&" ;
		if ( $what != '' ) $ret .= 'action=query&prop=' . $what . '&' ;
		return $ret ;
	}
	
	function get_result ( $url ) {
		$cnt = 3 ;
		do {
			$result = @file_get_contents ( $url ) ;
			$cnt-- ;
			} while ( $result === false AND $cnt > 0 ) ;
		return unserialize ( $result ) ;
	}

	function get_image_data ( $image , $tw = -1 , $th = -1 ) {
		$url = $this->get_api_base_url ( 'imageinfo' ) ;
		$url .= 'titles=' . myurlencode ( $image ) ;
		$url .= '&iiprop=timestamp|user|comment|url|sha1|size|mime|archivename' ;
		$url .= '&iilimit=50' ;
		if ( $tw > -1 ) $url .= '&iiurlwidth=' . $tw ;
		if ( $th > -1 ) $url .= '&iiurlheight=' . $th ;
		$data = $this->get_result ( $url ) ;

		if ( !isset ( $data['query'] ) ) return false ; # Error
		$data = $data['query'] ;

		if ( !isset ( $data['pages'] ) ) return false ; # Error
		$data = $data['pages'] ;

		$data = array_shift ( $data ) ;
		$data['id'] = $data['pageid'] ; unset ( $data['pageid'] ) ;
		$data['imghistory'] = $data['imageinfo'] ;
//		unset ( $data['imageinfo'] ) ;
#		$data['image']['bits'] = '???' ;
		
		return $data ;
	}


	function get_existing_pages_sub ( $pages ) {
		$ret = array () ;
		if ( count ( $pages ) == 0 ) return $ret ;
		$add = '' ;
		foreach ( $pages AS $p ) {
			if ( $add != '' ) $add .= '|' ;
			$add .= myurlencode ( $p ) ;
		}
		$url = $this->get_api_base_url ( 'info' ) ;
		$url .= 'titles=' . $add ;
		$data = $this->get_result ( $url ) ;

		if ( !isset ( $data['query'] ) ) return $ret ; $data = $data['query'] ;
		if ( !isset ( $data['pages'] ) ) return $ret ; $data = $data['pages'] ;
		
		if ( !isset ( $data ) ) return $ret ;
		
		foreach ( $data AS $d ) {
			if ( !isset ( $d['pageid'] ) ) continue ;
			if ( $d['pageid'] == 0 ) continue ;
			$ret[] = $d['title'] ;
		}
		return $ret ;
	}

	function get_existing_pages ( $pages ) {
    $ret = array () ;
    while ( count ( $pages ) > 0 ) {
      $temp = array () ;
      while ( count ( $pages ) > 0 and count ( $temp ) < 200 ) {
        $temp[] = array_shift ( $pages ) ;
      }
      $res = $this->get_existing_pages_sub ( $temp ) ;
      foreach ( $res AS $r ) $ret[] = $r ;
    }
    return $ret ;
	}

	function does_image_exist ( $image ) {
		$url = $this->get_api_base_url ( 'imageinfo' ) ;
		$url .= 'titles=' . myurlencode ( $image ) ;
		$data = $this->get_result ( $url ) ;
		
//		print "<pre>" ; print_r ( $data ) ; print "</pre>" ;
		
		if ( !isset ( $data['query'] ) ) return false ; $data = $data['query'] ;
		if ( !isset ( $data['pages'] ) ) return false ; $data = $data['pages'] ;

		$data = array_shift ( $data ) ;
		return ! isset ( $data['missing'] ) ;
	}
	
	function get_redirect_target ( $title ) {
		$url = $this->get_api_base_url ( 'info' ) ;
		$url .= "redirects&titles=" . myurlencode ( $title ) ;
		$data = $this->get_result ( $url ) ;
		$data = $data['query'] ;
		if ( !isset ( $data['redirects'] ) ) return $data['redirects'] ;
		$data = $data['redirects'] ;
		$data = array_shift ( $data ) ;
		$data = $data['to'] ;
		return $data ;
	}

	function get_categories ( $title ) {
		$ret = array () ;

		$url = $this->get_api_base_url ( 'categories' ) ;
		$url .= 'titles=' . myurlencode ( $title ) ;
		$url .= '&cllimit=500' ;
		
		$data = $this->get_result ( $url ) ;
		
		if ( !isset ( $data['query'] ) ) return $ret ; $data = $data['query'] ;
		if ( !isset ( $data['pages'] ) ) return $ret ; $data = $data['pages'] ;
		$data = array_shift ( $data ) ;
		
		if ( !isset ( $data['categories'] ) ) return $ret ; # Error
		$data = $data['categories'] ;
		if ( !is_array ( $data ) ) return $ret ;
		foreach ( $data AS $t ) {
			if ( $t['ns'] != '14' ) continue ; # Not a real category?
			$ret[] = array_pop ( explode ( ':' , $t['title'] , 2 ) ) ;
		}
		return $ret ;
	}
	
	function get_used_templates ( $title , $return_all_namespaces = false ) {
		$ret = array () ;

		$url = $this->get_api_base_url ( 'templates' ) ;
		$url .= 'titles=' . myurlencode ( $title ) ;
		$url .= '&tllimit=500' ;
		
		$data = $this->get_result ( $url ) ;
		
		if ( !isset ( $data['query'] ) ) return $ret ; $data = $data['query'] ;
		if ( !isset ( $data['pages'] ) ) return $ret ; $data = $data['pages'] ;
		
		$data = array_shift ( $data ) ;
		if ( !isset ( $data['templates'] ) ) return $ret ;
		$data = $data['templates'] ;
		if ( !is_array ( $data ) ) return $ret ;
		foreach ( $data AS $t ) {
			if ( !$return_all_namespaces AND $t['ns'] != '10' ) continue ; # Not a real template
			$ret[] = array_pop ( explode ( ':' , $t['title'] , 2 ) ) ;
		}
		return $ret ;
	}
	
	function get_pages_in_category ( $category , $namespace = 0 , $depth = 1 ) {
		if ( $depth <= 0 ) return array () ;
		
		$ret = array () ;

		$url = $this->get_api_base_url () ;
		$url .= 'action=query&list=categorymembers&' ;
		$url .= 'cmtitle=' . myurlencode ( "Category:$category" ) ;
		$url .= '&cmprop=ids|title|timestamp&cmlimit=500' ;
		
		if ( $namespace != -1 ) $url1 = $url . '&cmnamespace=' . $namespace ;
		$url2 = $url . '&cmnamespace=14' ;
		
//		print "<pre>" ;		print_r ( $url1 ) ;		print "</pre>" ;
		
		# Load pages
		$data = $this->get_result ( $url1 ) ;
		if ( isset ( $data['query'] ) ) $data = $data['query'] ;
		if ( isset ( $data['categorymembers'] ) ) {
			$data = $data['categorymembers'] ;

			foreach ( $data AS $d ) {
				$key = $d['title'] ;
				if ( isset ( $ret[$key] ) ) continue ;
				#if ( $d['ns'] == '14' ) continue ;
				$d['id'] = $d['pageid'] ;
				unset ( $d['pageid'] ) ;
				$ret[$key] = $d ;
			}
		}
		
		if ( $depth == 1 ) return $ret ; # No need to search deeper

		# Load pages in subcategories
		$data = $this->get_result ( $url2 ) ;
		if ( isset ( $data['query'] ) ) $data = $data['query'] ;
		if ( !isset ( $data['categorymembers'] ) ) return $ret ;
		
		$data = $data['categorymembers'] ;
		foreach ( $data AS $d ) {
			$key = $d['title'] ;
			$key = array_pop ( explode ( ':' , $key , 2 ) ) ;
			$subret = $this->get_pages_in_category ( $key , $namespace , $depth - 1 ) ;
			foreach ( $subret AS $k => $v ) {
				if ( isset ( $ret[$k] ) ) continue ; # Have that one already
				$ret[$k] = $v ;
			}
		}

		return $ret ;
	}
	
	function get_backlinks ( $title , $blfilter = 'all' , $blfilterredir = 'all' ) {
		$ret = array () ;
		$url = $this->get_api_base_url () ;
		$url .= '&action=query&list=backlinks&' ;
		$url .= 'bllimit=500&bltitle=' . myurlencode ( $title ) . "&blfilterredir=$blfilterredir" ;
		$data = $this->get_result ( $url ) ;
		if ( !isset ( $data['query'] ) ) return $ret ; $data = $data['query'] ;
		if ( !isset ( $data['backlinks'] ) ) return $ret ; $data = $data['backlinks'] ;
		
		foreach ( $data AS $d ) {
			$ret[$d['title']]['*'] = $d['title'] ;
			if ( $d['ns'] != '0' ) $ret[$d['title']]['ns'] = $d['ns'] ;
			$ret[$d['title']]['id'] = $d['pageid'] ;
		}
		
		return $ret ;
	}
	
	function get_images_in_category ( $category , $depth = 1 ) {
		return $this->get_pages_in_category ( $category , 6 , $depth ) ;
	}
	
	function get_recent_uploads ( $num = 50 ) {
    $ret = array() ;
		$url = $this->get_api_base_url () ;
    $url .= "action=query&list=logevents&letype=upload&lelimit=" . $num ;
		$data = $this->get_result ( $url ) ;
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;
		$data = $data['logevents'] ;
		return $data ;
	}

  # All links on a page
  # Returns array of array ( ns , title )
	function get_links ( $title , $ns , $cont = '' ) {
    	$ret = array() ;
		$url = $this->get_api_base_url ( 'links' ) ;
		$url .= 'pllimit=500&titles=' . myurlencode ( $title ) ;
		if ( $cont != '' ) $url .= "&plcontinue=" . $cont ;
		if ( isset ( $ns ) ) $url .= '&plnamespace=' . $ns ;
		
		$data = $this->get_result ( $url ) ;
		if ( !isset ( $data['query'] ) ) return $ret ;

		$cont = '' ;
		if ( isset($data['query-continue']) and isset($data['query-continue']['links']) ) $cont = $data['query-continue']['links']['plcontinue'] ;

		$data = $data['query'] ;
		$data = $data['pages'] ;
		$data = array_shift ( $data ) ;
		if ( !isset ( $data['links'] ) ) return $ret ;
		$data = $data['links'] ;

		if ( $cont != '' ) {
			$l2 = $this->get_links ( $title , $ns , $cont ) ;
			while ( count($l2) > 0 ) array_push ( $data , array_pop ( $l2 ) ) ;
		}

		return $data ;
	}
	
	function get_external_links ( $all_titles , $start = '' ) {
		$ret = array() ;
		while ( count ( $all_titles ) > 0 ) {
			$titles = array () ;
			while ( count ( $titles ) < 100 and count ( $all_titles ) > 0 ) $titles[] = array_pop ( $all_titles ) ;
			$url = $this->get_api_base_url ( 'extlinks' ) ;
			foreach ( $titles AS $k => $v ) $titles[$k] = myurlencode ( $v ) ;
			$url .= 'titles=' . implode ( "|" , $titles ) ;
	
			$data = $this->get_result ( $url ) ;
			if ( !isset ( $data['query'] ) ) return $ret ;
			$data = $data['query'] ;
			$data = $data['pages'] ;
			
			foreach ( $data AS $d ) {
				if ( !isset ( $d['extlinks'] ) ) continue ;
				$curpage = $d['title'] ;
				foreach ( $d['extlinks'] AS $n )
					$url = $n['*'] ;
					if ( substr ( $url , 0 , strlen ( $start ) ) != $start ) continue ; # Those are not the droids we are looking for
					$ret[$url] = $curpage ;
			}
		}

		return $ret ;
	}
	
	function get_images_on_page ( $title , $check_ignore = 0 ) {
	    $ret = array() ;
		$url = $this->get_api_base_url ( 'images' ) ;
		$url .= 'imlimit=500&titles=' . myurlencode ( $title ) ;

		$data = $this->get_result ( $url ) ;
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;
		$data = $data['pages'] ;
		$data = array_shift ( $data ) ;
		if ( !isset ( $data['images'] ) ) return $ret ;
		$data = $data['images'] ;
		foreach ( $data AS $i ) {
			$image = $i['title'] ;
			if ( $check_ignore and is_image_ignored ( $image , $this->language , $this->project ) ) continue ;
			$ret[$image] = $image ; # Avoids double listing
		}
#		print_r ( $data ) ;
		return $ret ;
	}

	function get_used_image ( $image , $ns = "0" ) {
		$ret = array() ;
		$iucontinue = "" ;
		
		do {
		  $url = $this->get_api_base_url () ;
		  $url .= 'iutitle=' . myurlencode ( $image ) ;
		  $url .= '&iunamespace=' . myurlencode ( $ns ) ;
		  $url .= '&iulimit=500&list=imageusage&action=query' ;
		  if ( $iucontinue != "" ) $url .= "&iucontinue=" . urlencode ( $iucontinue ) ;
	
		  $data = $this->get_result ( $url ) ;
		  if ( !isset ( $data['query'] ) ) return $ret ;
		  
		  $iucontinue = "" ;
		  if ( isset ( $data['query-continue'] ) ) {
			$d = $data['query-continue'] ;
			if ( isset ( $d['imageusage'] ) ) {
			  $d = $d['imageusage'] ;
			  $iucontinue = $d['iucontinue'] ;
			}
		  }
		  
		  $data = $data['query'] ;
		  $data = $data['imageusage'] ;
	
		  foreach ( $data AS $i ) {
			$title = $i['title'] ;
			$ret[$title ] = $title ;
		  }
		} while ( $iucontinue != "" ) ;
		return $ret ;
	}

	function get_namespaces () {
      $url = $this->get_api_base_url () ;
      $url .= "action=query&meta=siteinfo&siprop=namespaces" ;
      $ret = array () ;
      $data = $this->get_result ( $url ) ;
      if ( !isset ( $data['query'] ) ) return $ret ;
      $data = $data['query'] ;
      if ( !isset ( $data['namespaces'] ) ) return $ret ;
      $data = $data['namespaces'] ;
      
      foreach ( $data AS $k => $v ) {
      	$ret[$k] = $v['*'] ;
      }
#      print "<pre>" ; print_r ( $ret ) ; print "</pre>" ; 
     return $ret ;
	}

  function get_url_usage ( $url , $namespace = '' ) {
    $ret = array () ;
    $orig_url = $url ;
    if ( substr ( $url , 0 , 7 ) == 'http://' ) $url = substr ( $url , 7 ) ;
    $wurl = $this->get_api_base_url () ;
    $wurl .= "action=query&list=exturlusage&euquery=" . urlencode ( $url ) ;
    if ( $namespace != '' ) $wurl .= "&eunamespace=" . $namespace ;
    $data = $this->get_result ( $wurl ) ;
    if ( !isset ( $data['query'] ) ) return $ret ;
    $data = $data['query'] ;
    if ( !isset ( $data['exturlusage'] ) ) return $ret ;
    $data = $data['exturlusage'] ;
    foreach ( $data AS $d ) {
//    	print $d['url'] ."<hr/>" ;
    	if ( $d['url'] != $orig_url ) continue ;
//    	print "TESTING : " ; print_r ( $d ) ; print "<br/>" ;
      $ret[] = $d['title'] ;
    }
    return $ret ;
  }


  function get_backlinks_api ( $title , $blfilterredir = 'all' , $namespaces = array() ) {
		$ret = array () ;
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&bllimit=500&list=backlinks&bltitle=' . myurlencode ( $title ) . "&blfilterredir=$blfilterredir" ;
		if ( count ( $namespaces ) > 0 ) $url .= '&blnamespace=' . implode ( '|' , $namespaces ) ;
		$data = $this->get_result ( $url ) ;

    if ( !isset ( $data['query'] ) ) return $ret ;
    $data = $data['query'] ;

    if ( !isset ( $data['backlinks'] ) ) return $ret ;
    $data = $data['backlinks'] ;


		foreach ( $data AS $d ) {
			$ret[] = $d['title'] ;
		}
		
		return $ret ;
	}
	
  function get_random_pages ( $number = 1 , $namespaces = array ( 0 ) ) {
		$ret = array () ;
		if ( $number > 10 ) $number = 10 ;
		$url = $this->get_api_base_url () ;
		$url .= "action=query&list=random&rnlimit=$number" ;
    $url .= "&rnnamespace=" . implode ( '|' , $namespaces ) ;
    #print "<br/>$url</br>" ;
		$data = $this->get_result ( $url ) ;
		
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;
		
		if ( !isset ( $data['random'] ) ) return $ret ;
		$data = $data['random'] ;
		
		foreach ( $data AS $k => $d ) {
      $ret[$d['title']] = $d ;
		}
		
    return $ret ;
  }
	
  function get_random_page ( $namespaces = array ( 0 ) ) {
    return $this->get_random_pages ( 1 , $namespaces ) ;
  }
  
  function get_article_url ( $title , $action = '' ) {
  	$ret = 'http://' . $this->language . '.' . $this->project . '.org' ;
  	if ( $action == '' ) {
  		return "$ret/wiki/" . myurlencode ( $title ) ;
  	} else {
  		return "$ret/w/index.php?action=$action&title=" . myurlencode ( $title ) ;
  	}
//    return get_wikipedia_url ( $this->language , $title , $action , $this->project ) ;
  }
  
  function get_article_link ( $title , $text = '' , $action = '' , $target = '' ) {
    if ( $text == '' ) $text = str_replace ( '_' , ' ' , $title ) ;
    $url = $this->get_article_url ( $title , $action ) ;
    if ( $target != '' ) $target = " target='$target'" ;
    $style = '' ;
    if ( $action == 'edit' ) $style = " style='color:red;'" ;
    return "<a$target$style href='$url'>$text</a>" ;
  }
  
  function get_page_info ( $title ) {
		$ret = array () ;
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&prop=info&titles=' . myurlencode ( $title ) ;
		$data = $this->get_result ( $url ) ;
		
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;
		
		if ( !isset ( $data['pages'] ) ) return $ret ;
		$data = $data['pages'] ;
		
		$ret = array_shift ( $data ) ;
		
    return $ret ;
  }

  function get_language_links ( $title ) {
		$ret = array () ;
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&prop=langlinks&lllimit=500&redirect=&titles=' . myurlencode ( $title ) ;
		$data = $this->get_result ( $url ) ;
		
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;

		if ( !isset ( $data['pages'] ) ) return $ret ;
		$data = $data['pages'] ;
		
		$data = array_shift ( $data ) ;
		
		if ( !isset ( $data['langlinks'] ) ) return $ret ;
		$data = $data['langlinks'] ;
		
		foreach ( $data AS $d ) {
      $ret[$d['lang']] = $d['*'] ;
		}
		
    return $ret ;
  }

  function get_external_links2 ( $title ) {
		$ret = array () ;
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&prop=extlinks&titles=' . myurlencode ( $title ) ;
		$data = $this->get_result ( $url ) ;
		
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;

		if ( !isset ( $data['pages'] ) ) return $ret ;
		$data = $data['pages'] ;
		
		$data = array_shift ( $data ) ;
		
		if ( !isset ( $data['extlinks'] ) ) return $ret ;
		$data = $data['extlinks'] ;
		
#		print "<pre>" ;
#		print_r ( $data ) ;
#		print "</pre>" ;
		
		foreach ( $data AS $d ) {
      $ret[] = $d['*'] ;
		}
		
    return $ret ;
  }
  
  function get_user_data ( $users ) {
  	if ( !is_array ( $users ) ) $users = array ( $users ) ;
  	
  	$u = array () ;
  	foreach ( $users AS $v ) $u[] = myurlencode ( $v ) ;
  	
	$ret = array () ;
	$url = $this->get_api_base_url () ;
	$url .= 'action=query&list=users&usprop=groups|editcount&ususers=' . implode ( '|' , $u ) ;
	$data = $this->get_result ( $url ) ;
  	
	if ( !isset ( $data['query'] ) ) return $ret ;
	$data = $data['query'] ;

	if ( !isset ( $data['users'] ) ) return $ret ;
	$data = $data['users'] ;
	
	foreach ( $data AS $d ) {
		$name = $d['name'] ;
		$r = array () ;
		$r['name'] = $name ;
		$r['editcount'] = $d['editcount'] ;
		if ( isset ( $d['groups'] ) ) {
			foreach ( $d['groups'] AS $v ) $r[$v] = 1 ;
		}
		
		$ret[$name] = $r ;
	}
	

#		print "<pre>" ;
#		print_r ( $ret ) ;
#		print "</pre>" ;
	return $ret ;
  }
  
  function get_revisions ( $title , $last = 50 ) {
		$ret = array () ;
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&prop=revisions&rvlimit=' . $last . '&rvprop=ids|user|timestamp|comment|flags&titles=' . myurlencode ( $title ) ;
		$data = $this->get_result ( $url ) ;

		
		if ( !isset ( $data['query'] ) ) return $ret ;
		$data = $data['query'] ;

		if ( !isset ( $data['pages'] ) ) return $ret ;
		$data = $data['pages'] ;

		$data = array_shift ( $data ) ;
		
		if ( !isset ( $data['revisions'] ) ) return $ret ;
		$data = $data['revisions'] ;
		
#		print "<pre>" ;
#		print_r ( $data ) ;
#		print "</pre>" ;
		return $data ;

  }

  function user_exists ( $user ) {
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&list=users&usprop=blockinfo&ususers=' . myurlencode ( $user ) ;
		$data = $this->get_result ( $url ) ;

		
		if ( !isset ( $data['query'] ) ) return false ;
		$data = $data['query'] ;
		
		if ( !isset ( $data['users'] ) ) return false ;
		$data = $data['users'] ;
		
		if ( !isset ( $data['0'] ) ) return false ;
		$data = $data['0'] ;
		
		if ( isset ( $data['missing'] ) ) return false ;
		
		return true ;
	}
	
  function user_exists_unblocked ( $user ) {
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&list=users&usprop=blockinfo&ususers=' . myurlencode ( $user ) ;
		$data = $this->get_result ( $url ) ;

		
		if ( !isset ( $data['query'] ) ) return false ;
		$data = $data['query'] ;
		
		if ( !isset ( $data['users'] ) ) return false ;
		$data = $data['users'] ;
		
		if ( !isset ( $data['0'] ) ) return false ;
		$data = $data['0'] ;
		
		if ( isset ( $data['missing'] ) ) return false ;
		if ( isset ( $data['blockedby'] ) ) return false ;
		if ( isset ( $data['blockreason'] ) ) return false ;
		
		return true ;
	}
	
	function search ( $key , $namespace = '' , $limit = 25 ) {
		$url = $this->get_api_base_url () ;
		$url .= 'action=opensearch&limit=$limit&search=' . urlencode ( $key ) ;
		if ( $namespace != '' ) $url .= "&namespace=" . $namespace ;
		$data = json_decode ( file_get_contents ( $url ) ) ;
		
		return $data[1] ;
	}
	
	function fullSearch ( $key , $namespace = '' , $limit = 25 ) {
		$url = $this->get_api_base_url () ;
		$url .= 'action=query&list=search&srlimit=$limit&srsearch=' . urlencode ( $key ) ;
		if ( $namespace != '' ) $url .= "&srnamespace=" . $namespace ;
		$data = unserialize ( file_get_contents ( $url ) ) ;
		$data = $data['query']['search'] ;
		
		return $data ;
	}


}


# END OF CLASS WikiQuery
# ______________________________________________________________________
