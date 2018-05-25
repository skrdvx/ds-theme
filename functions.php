<?php


// Converts bytes into human readable file size.
function FileSizeConvert($bytes)
{
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}

// File permissions
function chmod_recursive($start_dir, $debug = true) {
    $dir_perms = 0755;
    $file_perms = 0755;

    $str = "";
    $files = array();
    if (is_dir($start_dir)) {
        $fh = opendir($start_dir);
        while (($file = readdir($fh)) !== false) {
            // skip hidden files and dirs and recursing if necessary
            if (strpos($file, '.')=== 0) continue;

            $filepath = $start_dir . '/' . $file;
            if ( is_dir($filepath) ) {
                //$newname = sanitize_file_name($filepath);
                // echo $str = "chmod $filepath To $dir_perms\n";
                chmod($filepath, $dir_perms);
                chmod_recursive($filepath);
            } else {
                ////$newname = sanitize_file_name($filepath);
                // echo $str = "chmod $filepath tp $file_perms\n";
                chmod($filepath, $file_perms);
            }
        }
        closedir($fh);
    }
    if ($debug) {
        // echo $str.'<br	>';
    }
}

// Styles, scripts
function ds_scripts() {
	wp_enqueue_style( 'ds-bootstrap-css', get_template_directory_uri() . '/assets/css/bootstrap.min.css' );
	wp_enqueue_style( 'ds-lens', get_template_directory_uri() . '/assets/css/lens.css' );
	wp_enqueue_style( 'ds-style', get_template_directory_uri() . '/assets/css/styles.css' );
	wp_enqueue_style( 'ds-desktop', get_template_directory_uri() . '/assets/css/debug.css' );

  wp_enqueue_script('ds-bootstrap-js', get_template_directory_uri().'/assets/js/bootstrap.min.js', array('jquery') );
  wp_enqueue_script('ds-scripts', get_template_directory_uri().'/assets/js/scripts.js', array('jquery') );
	wp_enqueue_script('ds-nanoscroller', get_template_directory_uri().'/assets/js/jquery.nanoscroller.min.js', array('jquery') );
  wp_enqueue_script('altmetric-badges', 'https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js');
	wp_enqueue_script('dimensions-badges', 'https://badge.dimensions.ai/badge.js');


	//Slick JS
	//wp_enqueue_script('slick-js', get_template_directory_uri().'/assets/js/slick.min.js', array('jquery') );
	//wp_enqueue_script('slickjs-init', get_template_directory_uri().'/assets/js/slider-init.js', array('slickjs-js') );
	//wp_enqueue_style( 'slick-js-css', get_template_directory_uri() . '/assets/js/slick.css' );
}
add_action( 'wp_enqueue_scripts', 'ds_scripts' );

// Article vars
function parameter_queryvars( $qvars ) {
	$qvars[] = 'article';
	return $qvars;
}
add_filter('query_vars', 'parameter_queryvars' );

function parameter_queryvars2( $qvars ) {
	$qvars[] = 'template';
	return $qvars;
}
add_filter('query_vars', 'parameter_queryvars2' );


// Search hook
function my_fuzzy_threshold() {
  return 10;
}
add_filter( 'searchwp_fuzzy_threshold', 'my_fuzzy_threshold' );









// Define the action and give functionality to the action.
// function ds_action() {
//   do_action( 'ds_action' );
// }
//
// // Register the action with WordPress.
// function ds_action_example() {
//   echo '<div class="updated notice"><p>';
//   echo _e( 'Success! Article updated.', 'ds-theme' );
//   echo '</p></div>';
// }
// add_action( 'ds_action', 'ds_action_example' );
//
// function ds_admin_notice() {
//   ds_action();
// }
// // add_action( 'admin_notices', 'ds_admin_notice' );



// Add admin notice
// function SITE_profile_update_notice() {
// // Add this to your code where you want to the transient to fire  after function
// set_transient( 'fx-admin-notice-panel', true, 5 );
// }
// add_action( 'admin_notices', 'fx_admin_notice_example_notice' );


/** Admin Notice on Activation. @since 0.1.0 */
// function fx_admin_notice_example_notice(){

    /* Check transient, if available display notice */
    // if( get_transient( 'fx-admin-notice-panel' ) ){
        // echo '<div class="updated notice is-dismissible">
        //     <div class="notice"><p>Good Job!</p></div>
        // </div>';

        /* Delete transient, only display this notice once. */
        // delete_transient( 'fx-admin-notice-panel' );
    // }
// }



// Add a flash notice to {prefix}options table until a full page refresh is done
function add_flash_notice( $notice = "", $type = "warning", $dismissible = true ) {
  // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
  $notices = get_option( "my_flash_notices", array() );
  $dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
  // We add our new notice.
  array_push($notices, array(
    "notice" => $notice,
    "type" => $type,
    "dismissible" => $dismissible_text
    ));
    // Then we update the option with our notices array
    update_option("my_flash_notices", $notices );
}


// Function executed when the 'admin_notices' action is called, here we check if there are notices on
// our database and display them, after that, we remove the option to prevent notices being displayed forever.
function display_flash_notices() {
  $notices = get_option( "my_flash_notices", array() );
  // Iterate through our notices to be displayed and print them.
  foreach ( $notices as $notice ) {
    printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
      $notice['type'],
      $notice['dismissible'],
      $notice['notice']
    );
  }
  // Now we reset our options to prevent notices being displayed forever.
  if( ! empty( $notices ) ) {
    delete_option( "my_flash_notices", array() );
  }
}
// We add our display_flash_notices function to the admin_notices
add_action( 'admin_notices', 'display_flash_notices', 12 );

// add_flash_notice( __(''), 'warning', true );
// add_flash_notice( __(''), 'info', false );
// add_flash_notice( __(''), 'error', false );



// Update post
function my_acf_save_post( $post_id ) {

	// Get folders
  WP_Filesystem();
  $rootPath		 = get_home_path();
  $uploadPath  = wp_upload_dir()['path'];
  $articleSlug = $post_id;
  // $articleSlug = get_post_field('post_name', get_post());
  $articlePath = $rootPath.'digital-science/'.$articleSlug;
  $articleUrl = 'http://ds.skrdv.com/digital-science/'.$articleSlug;

	// Get fields
	$articleZip      = get_field('article-zip', $post_id);
	$articleXml	     = get_field('article-xml', $post_id);
	$articlePdf	     = get_field('article-pdf', $post_id);
	$articleDate     = get_field('article-date', $post_id);
  $articleDoi      = get_field('article-doi', $post_id);
	$articleAuthors  = get_field('article-authors', $post_id);
	$articleAbstract = get_field('article-abstract', $post_id);
  $articleBody     = get_field('article-body', $post_id);

  $articleFile     = $articleZip['filename'];
  $articleName     = str_replace('.', '', substr($articleFile, 0, -4));
  $articleXmlPng   = $articleName.'PNG.xml';
  $articleXmlUrl = $articleUrl.'/'.$articleName.'.xml';
  $articleXmlPath = $articlePath.'/'.$articleName.'.xml';
  $articleXmlPngUrl = $articleUrl.'/'.$articleName.'PNG.xml';
  $articleXmlPngPath = $articlePath.'/'.$articleName.'PNG.xml';
  // $articleXml      = $articleUrl.'/'.$articleXmlPng;
  // $articlePdf      = $articleUrl.'/'.$articleXmlPng;

  $xmlFile        = $articlePath.'/'.$articleXml;
  $xmlUrl         = $articleUrl.'/'.$articleXml;


	// Get archive
	if (!$articleZip) {

    add_flash_notice( __('ZIP archive is missing. '), 'warning', true );

  } else {

    // Delete all files
    $articleAllFiles = glob($articlePath.'/*');
    foreach($articleAllFiles as $file){
      if(is_file($file))
        unlink($file);
    }

    // Unzip archive
		$articleUnzip = unzip_file( $uploadPath.'/'.$articleFile, $articlePath);
    update_attached_file($articleZip, $articleUnzip);
    chmod_recursive($articlePath, true);

    // Check unzip archive
    if ($articleUnzip) {
      add_flash_notice( __('ZIP archive: '.$articlePath.'/'.$articleFile), 'info', true );
    } else {
      add_flash_notice( __('Unzip error!'), 'error', true );
    }

	}


  	// Check PDF file
  	if (!$articlePdf) {

  		$articlePdf = $articleName.'.pdf';
  		// $articlePdfUrl = $articleUrl.'/'.$articlePdf;

      // Check for existing xml file
      // $articlePdfArchivePath = $articlePath.'/'.$articleName.'.pdf';
      // $articlePdfFiles = glob($articlePath.'/*.pdf');
      // $articlePdfFile  = $articlePdfFiles[0];
      // $articlePdfUrl = $articleUrl.'/'.$articleName.'.pdf';

      add_flash_notice( __('PDF file: '.$articleUrl.'/'.$articlePdf), 'info', true );

    //   if ($articlePdfArchivePath === $articlePdfFile) {
    //     add_flash_notice( __('PDF file exists: '.$articlePdfUrl), 'info', true );
    //   } else {
    //     add_flash_notice( __('PDF file is missed.'), 'error', true );
    //   }
  	} else {

      $articlePdf = $articleName.'.pdf';

      add_flash_notice( __('PDF file not changed: '.$articleUrl.'/'.$articlePdf), 'warning', true );
    }



  // Check XML
	if ($articleXml) {

    // Edit XML
    $dom=new DOMDocument();
    $dom->load($articleXmlUrl);
    if (!$dom->load($articleXmlUrl)){
     add_flash_notice( __('Error in XML document.'), 'error', true );
    }

    $images = $dom->documentElement->getElementsByTagName('graphic');

    $imagesArrayXml = array();
    foreach ($images as $image) {
      $imageSrc = $image->getAttributeNS('http://www.w3.org/1999/xlink', 'href');
      $imageName = str_replace('.', '', substr($imageSrc, 0, -4));
      $imagePngPath = '/digital-science/'.$articleSlug.'/'.$imageName.'.png';
      $image->setAttributeNS('http://www.w3.org/1999/xlink', 'href', $imagePngPath);
      array_push($imagesArrayXml, $imageName);
    }
    $dom->saveXML();
    // $dom->save($articleXmlPngPath);
    $dom->save($articlePath.'/'.$articleName.'PNG.xml');

    add_flash_notice( __('XML file: '.$articleUrl.'/'.$articleXml), 'info', true );

    // Check for existing xml file
    // $articleXmlArchivePath = $articlePath.'/'.$articleName.'.xml';
    // $articleXmlFiles = glob($articlePath.'/*.xml');
    // $articleXmlFile  = $articleXmlFiles[0];

    // $articleXml = $articleXmlPng;
    $articleXml = $articleName.'.xml';
    //
    // if ($articleXmlArchivePath === $articleXmlFile) {
    //   add_flash_notice( __('XML file exists: '.$articleXml), 'info', true );
    // } else {
    //   add_flash_notice( __('XML file is missed.'), 'error', true );
    // }
	} else {

    // $articleXmlUrl = $articleUrl.'/'.$articleName.'PNG.xml';
    $articleXml = $articleXmlPng;

    add_flash_notice( __('XML file not changed: '.$articleUrl.'/'.$articleXml), 'info', true );

  }

  // Update permissions
  chmod_recursive($articlePath, true);

  // Check ImageMagick
  if (extension_loaded('imagick')) {
    add_flash_notice( __('ImageMagick Loaded.'), 'info', true );
  } else {
    add_flash_notice( __('Extension ImageMagick not found by extension_loaded.'), 'error', true );
  }

  // Convert images to png
  $imagesTif = glob($articlePath.'/*.tif');
  if ($imagesTif) {
    $imagesArrayFiles = array();
    foreach($imagesTif as $image) {
      $imageName = str_replace($articlePath.'/', '', substr($image, 0, -4));
      array_push($imagesArrayFiles, $imageName);
      $im = new imagick($image);
      $im->writeImage($articlePath.'/'.$imageName.'.png');
    }
  }


  // Check images for exists
  // if ($imagesArrayXml === $imagesArrayFiles) {
  //   add_flash_notice( __('All images in archive.'), 'info', true );
  // } elseif(!$imagesArrayXml) {
  //   add_flash_notice( __('No images in archive.'), 'error', true );
  // } else {
  //   add_flash_notice( __('Not enough images in archive.'), 'error', true );
  // }

	// Parse XML
  if ($articleXml) {

    // $articleXmlUrl = $articleUrl.'/'.$articleName.'PNG.xml';

    // $xmlFile = simplexml_load_file($articlePath.'/'.$articleXml); // or die("Error: Cannot create object");
  	$xmlFile = simplexml_load_file($articleXmlPngUrl); // or die("Error: Cannot create object");

    // Get Journal Meta
    $journalTitle = $xmlFile->front->{'journal-meta'}->{'journal-title-group'}->{'journal-title'};
    $journalIssn = $xmlFile->front->{'journal-meta'}->{'issn'}[0];
    $journalPublisher = $xmlFile->front->{'journal-meta'}->{'publisher'}->{'publisher-name'};

    // Get Article Title
    $articleTitle = $xmlFile->front->{'article-meta'}->{'title-group'}->{'article-title'};
    $articleTitleItalic = $xmlFile->front->{'article-meta'}->{'title-group'}->{'article-title'}->{'italic'};
    $articleTitleFull = $articleTitle.' '.$articleTitleItalic;

    // Update Title
    if (!$articleTitle) {
      // $articleTitleFull = get_post_field('post_name', get_post());
      $articleTitle = $articleSlug;
      add_flash_notice( __('Article Title updated.'), 'info', true );
    } else {
      add_flash_notice( __('Article Title no changed.'), 'info', true );
    }

    // Get Article Meta
    $articleId = $xmlFile->front->{'article-meta'}->{'article-id'};
    // $articleDoi = $articleId[1];
    $articleDoiArray = array();
    foreach ($articleId as $key => $value) {
      // if ($key === 0) {
      // echo $value;
        $articleDoi = $value;
        array_push($articleDoiArray, $value);
      // }
    }
    $articleDoiList = implode(', ', $articleDoiArray);
    $articleDoi = $articleDoiList;
    // $articleDoi = json_decode($articleDoiArray[1]);
    // $articleDoi = json_encode($articleDoiArray[0]);

    // Get Article Authors
    if (!$articleAuthors) {
      $articleAuthorsArray = array();
      $articleAuthors = $xmlFile->front->{'article-meta'}->{'contrib-group'}->{'contrib'};
      foreach ($articleAuthors as $value) {
        $articleAuthorName = $value->name->{'given-names'};
        $articleAuthorSurname = $value->name->{'surname'};
        $articleAuthorFullName = $articleAuthorName.' '.$articleAuthorSurname;
        array_push($articleAuthorsArray, $articleAuthorFullName);
      }
      $articleAuthorsList = implode(', ', $articleAuthorsArray);
      $articleAuthors = $articleAuthorsList;
      add_flash_notice( __('Article Authors updated.'), 'info', true );
    } else {
      add_flash_notice( __('Article Authors not updated.'), 'error', true );
    }

    // Get Article Dates
    // if ($articleDate) {
      $articlePubDateD = $xmlFile->front->{'article-meta'}->{'pub-date'}->{'day'};
      $articlePubDateM = $xmlFile->front->{'article-meta'}->{'pub-date'}->{'month'};
      $articlePubDateY = $xmlFile->front->{'article-meta'}->{'pub-date'}->{'year'};
      $articlePubDateFull = $articlePubDateD.'.'.$articlePubDateM.'.'.$articlePubDateY;
      $articleReceivedDateM = $xmlFile->front->{'article-meta'}->{'history'}->{'date'}[0]->{'month'};
      $articleReceivedDateD = $xmlFile->front->{'article-meta'}->{'history'}->{'date'}[0]->{'day'};
      $articleReceivedDateY = $xmlFile->front->{'article-meta'}->{'history'}->{'date'}[0]->{'year'};
      $articleReceivedDateFull = $articleReceivedDateD.'.'.$articleReceivedDateM.'.'.$articleReceivedDateY;
      $articleAcceptedDateM = $xmlFile->front->{'article-meta'}->{'history'}->{'date'}[1]->{'month'};
      $articleAcceptedDateD = $xmlFile->front->{'article-meta'}->{'history'}->{'date'}[1]->{'day'};
      $articleAcceptedDateY = $xmlFile->front->{'article-meta'}->{'history'}->{'date'}[1]->{'year'};
      $articleAcceptedDateFull = $articleAcceptedDateD.'.'.$articleAcceptedDateM.'.'.$articleAcceptedDateY;
      $articleDate = $articlePubDateFull;
    // }

    // Get Article Abstract
    // if (!$articleAbstract) {
      // $articleAbstractString = '';
      // $articleAbstractArray = array();
      // $articleAbstractArray = $xmlFile->front->{'article-meta'}->{'abstract'}->{'p'};
      // foreach ($articleAbstract as $value) {
      //   $articleAbstractString .= $value.' ';
      //   foreach ($value->bold as $bold) {
      //     $articleAbstractString .= '<strong>'.$bold.'</strong><br>';
      //   }
      //   foreach ($value->italic as $italic) {
      //     $articleAbstractString .= '<italic>'.$italic.'</iitalic>';
      //   }
      // }
      // $articleAbstract = explode(' ', json_decode($articleAbstractArray));
    //   add_flash_notice( __('Article Abstract updated.'), 'info', true );
    // } else {
    //   add_flash_notice( __('Article Abstract not changed.'), 'error', true );
    // }



    function getContent(&$NodeContent="",$nod)
    {    $NodList=$nod->childNodes;
        for( $j=0 ;  $j < $NodList->length; $j++ )
        {       $nod2=$NodList->item($j);//Node j
            $nodemane=$nod2->nodeName;
            $nodevalue=$nod2->nodeValue;
            if($nod2->nodeType == XML_TEXT_NODE)
                $NodeContent .=  $nodevalue;
            else
            {     $NodeContent .= "<$nodemane ";
               $attAre=$nod2->attributes;
               foreach ($attAre as $value)
                  $NodeContent .="{$value->nodeName}='{$value->nodeValue}'" ;
                $NodeContent .=">";
                getContent($NodeContent,$nod2);
                $NodeContent .= "</$nodemane>";
            }
        }

    }

    // Get Abstarct
    $dom=new DOMDocument();
    $dom->load($articleXmlUrl);
    // $dom->loadXML($xmlUrl);
    // $dom->loadHTML($xmlUrl);

    if (!$dom->load($xmlUrl)){
     add_flash_notice( __('Error in XML document.'), 'error', true );
    }

    $abstract = $dom->documentElement->getElementsByTagName('abstract');
    $nodItem = $abstract->item(0);
    $abstractHtml = getContent($aContent, $nodItem);
    $articleAbstract = $aContent;

    // Get Article Body
    $body = $dom->documentElement->getElementsByTagName('body');
    $bodyItem = $body->item(0);
    $contentHtml = getContent($bContent, $bodyItem);
    $articleBody = $bContent;

    // $articleBodyArray = array();
    // $articleBodySec = $xmlFile->body->sec;
    // foreach ($articleBodySec as $value) {
    //   $articleBodySecTitle = '<u>'.$value->title.'</u><br>';
    //   $articleBodySecP = $value->p.'<br>';
    //   $articleBodyString .= $articleBodySecTitle.$articleBodySecP;
    //   foreach ($value->italic as $value) {
    //     $articleBodyString .= '<i>'.$value.'</i>';
    //   }
    // }
    // $articleBody = $articleBodyString;
  }




  // update_field('debug', $articleXml);
  update_field('debug', $articleUrl);

  if (!$articleZip) {

    // $articleXml = $articlePdf = $articleDate = $articleAuthors = $articleAbstract = $articleBody = '';
    update_field('article-xml', '', $post_id);
    update_field('article-pdf', '', $post_id);
    update_field('article-doi', '', $post_id);
    update_field('article-authors', '', $post_id);
    update_field('article-abstract', '', $post_id);
    update_field('article-body', '', $post_id);
    update_field('article-date', '', $post_id);
    update_field('article-date', '', $post_id);

  } else {

    update_field('article-xml', $articleXml, $post_id);
    update_field('article-pdf', $articlePdf, $post_id);
    update_field('article-doi', $articleDoi, $post_id);
    update_field('article-date', $articleDate, $post_id);
    update_field('article-authors', $articleAuthors, $post_id);
    update_field('article-abstract', $articleAbstract, $post_id);
    update_field('article-body', $articleBody, $post_id);

    // update_field('article-date', $articleXml, $post_id);


  }
  //
  // if ($articleXml) {
  //   update_field('article-xml', $articleXml);
  // }
  // if ($articlePdf) {
  //   update_field('article-pdf', $articlePdf);
  // }
  // if ($articleDate) {
  //   update_field('article-date', $articleDate, $post_id);
  // }
  // if ($articleDoiList) {
  //   update_field('article-doi', $articlePdf);
  // }
  // if ($articleAuthors) {
  //   update_field('article-authors', $articleAuthors);
  // }
  // if ($articleAbstract) {
  //   update_field('article-abstract', $articleAbstract);
  // }
  // if ($articleBody) {
  //   update_field('article-body', $articleBody);
  // }





  // add_flash_notice( __('Update all post fields.'), 'warning', true );
  // add_flash_notice( __('All fields updated.'), 'info', true );
}
add_action('acf/save_post', 'my_acf_save_post', 20);



// Validate
// function my_acf_validate_save_post() {
//
// 	// check if user is an administrator
// 	if( current_user_can('manage_options') ) {
// 		// clear all errors
// 		acf_reset_validation_errors();
// 	}
//
//   $articleZip      = get_field('article-zip');
// 	$articleXml	     = get_field('article-xml');
// 	$articlePdf	     = get_field('article-pdf');
// 	$articleDate     = get_field('article-date');
//   $articleDoi      = get_field('article-doi');
// 	$articleAuthors  = get_field('article-authors');
// 	$articleArstract = get_field('article-abstract');
//   $articleBody     = get_field('article-body');
//
//   display_flash_notices();
//
//   if (!$articleZip) {
//     add_flash_notice( __("Archive field is empty. All field cleared."), "error", true );
//   } else {
//     add_flash_notice( __("Archive unpacked. All fields updated."), "info", true );
//   }
//
//   if (!$articlePdf) {
//     add_flash_notice( __("PDF is missing."), "error", true );
//   } else {
//     add_flash_notice( __("PDF file: ".$articlePdf), "info", true );
//   }
//
// }
// add_action('acf/validate_save_post', 'my_acf_validate_save_post', 10, 0);




function article_update_after_creation( $post_id ) {
	$postUpdate = array( 'ID' => $post_id, 'post_name' => $post_id  );
	wp_update_post( $postUpdate );

	$tempinfo = get_post_meta( $post_id, 'wpcf-article-zip' );

	$attachment_id = attachment_url_to_postid( $tempinfo[0] );
	update_field('field_5af5637940ed5', $attachment_id, $post_id);

	update_meta( $post_id, 'wpcf-article-zip', '' );

	my_acf_save_post( $post_id );

}

// add_action('cred_save_data','article_update_after_creation',10,2);
