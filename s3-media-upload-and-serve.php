<?php

add_action('init','initiate_s3_upload_and_serve_func');
function initiate_s3_upload_and_serve_func(){
    
    define( 'S3_PLUGIN_PATH_URL', plugin_dir_path( __FILE__ ) );

    // define your configuration settings.
    define( 'S3_BUCKET_NAME', 'your_bucket_name' );
    define( 'S3_KEY', 'your_key' );
    define( 'S3_SECRET', 'your_secret' );

    add_filter( 'wp_generate_attachment_metadata', 'initiate_s3_upload_and_serve_config_func' );
}


function initiate_s3_upload_and_serve_config_func( $args ) {

	$upload_dir = wp_upload_dir(); // upload dir

    $main_file=$args['file']; // get file and folder located
     
    $uploaded_dir_path=str_replace(basename($main_file),'',$main_file); // Remove fileName so we can extract the path folder ex 2020/01;

    $file_name_only=basename($main_file); // get file name only

    $baseurl=$upload_dir['baseurl']; // base_url of upload dir.
    $wp_content_dir=str_replace(array('http://'.$_SERVER['SERVER_NAME'].'/','https://'.$_SERVER['SERVER_NAME'].'/'),'',$baseurl);// remove link of website.
    
    $file_key=$wp_content_dir.'/'.$uploaded_dir_path.$file_name_only; // join the dir and file name.

    $file_path_on_server=$upload_dir['basedir'].'/'.$uploaded_dir_path.basename($file_name_only); // join file name on server 

 
    //  upload to S3
    if(!function_exists('insertFileToS3Bucket')){ 
        include S3_PLUGIN_PATH_URL.'/plugin_modules/custom_invoice/s3-buket/index.php'; // include if it doesn't exit.
    }

    $upload_main=insertFileToS3Bucket($file_key,$file_path_on_server); // insert the original file into server.

  
    if($upload_main['@metadata'] && $upload_main['@metadata']['statusCode']==200){ // all correct.
        // your success message.
    }else{
        $message = '<p>The Image Has been uploaded succesfully into your server but  couldn\'t be uploaded into S3 . File Name '.$file_path_on_server.'</p>';
        $message.= '<code>'.$upload_main.'</code>';

        // write error on logs.
        $file = S3_PLUGIN_PATH_URL . '/s3-upload-logs.txt'; 
        $current = file_get_contents($file);
        file_put_contents($file, $message);
    }

    if(count($args['sizes'])>0){ // image is compressed into different file sizes.

        foreach($args['sizes'] as $file){

            $file_dir=$file['file'];

    
            $file_key=$wp_content_dir.'/'.$uploaded_dir_path.$file_dir;

            $file_path_on_server=$upload_dir['basedir'].'/'.$uploaded_dir_path.$file_dir;

            //  upload to S3
            $upload_other_files=insertFileToS3Bucket($file_key,$file_path_on_server);

            // print_r($upload_main);
            if($upload_other_files['@metadata'] && $upload_other_files['@metadata']['statusCode']==200){ // all correct.
                // your success message.
            }else{
                $message = '<p>The Image Has been uploaded succesfully into your server but  couldn\'t be uploaded into S3 . File Name '.$file_path_on_server.'</p>';
                $message.= '<code>'.$upload_main.'</code>';
            
                // write error on logs.
                $file = S3_PLUGIN_PATH_URL . '/s3-upload-logs.txt'; 
                $current = file_get_contents($file);
                file_put_contents($file, $message);
            }
        }

    }

	return $args;
}