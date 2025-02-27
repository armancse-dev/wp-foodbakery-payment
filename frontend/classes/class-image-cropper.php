<?php

/**
 * File Type: Header Element
 */
if ( ! class_exists( 'Directory_Image_Cropper' ) ) {

    class Directory_Image_Cropper {

        /**
         * Start construct Functions
         */
        public function __construct() {

            add_action( 'wp_ajax_save_iamge_to_file', array( $this, 'save_iamge_to_file_callback' ), 30, 2 );
            add_action( 'wp_ajax_nopriv_save_iamge_to_file', array( $this, 'save_iamge_to_file_callback' ), 30, 2 );
            add_action( 'wp_ajax_image_crop_to_file', array( $this, 'image_crop_to_file_callback' ), 30, 2 );
            add_action( 'wp_ajax_nopriv_image_crop_to_file', array( $this, 'image_crop_to_file_callback' ), 30, 2 );
        }

        public function save_iamge_to_file_callback() {
            if ( ($_FILES["img"]["size"] < 1024000 ) ) {
                $upload_dir_info = wp_upload_dir();
                $upload_dir = $upload_dir_info['path'] . '/';
                $imagePath = $upload_dir;
                $allowedExts = array( "gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG" );
                $temp = explode( ".", $_FILES["img"]["name"] );
                $extension = end( $temp );
                //Check write Access to Directory

                if ( ! is_writable( $imagePath ) ) {
                    $response = Array(
                        "status" => 'error',
                        "message" => directory_plugin_text_srt( 'directory_publisher_no_permissions_to_upload' ),
                    );
                    print json_encode( $response );
                    return;
                }



                if ( in_array( $extension, $allowedExts ) ) {
                    if ( $_FILES["img"]["error"] > 0 ) {
                        $response = array(
                            "status" => 'error',
                            "message" => esc_html__('ERROR Return Code: ','foodbakery'). $_FILES["img"]["error"],
                        );
                    } else {

                        $filename = $_FILES["img"]["tmp_name"];
                        list($width, $height) = getimagesize( $filename );

                        move_uploaded_file( $filename, $imagePath . $_FILES["img"]["name"] );

                        $response = array(
                            "status" => 'success',
                            "url" => $upload_dir_info['url'] . '/' . $_FILES["img"]["name"],
                            "width" => $width,
                            "height" => $height
                        );
                    }
                } else {
                    $response = array(
                        "status" => 'error',
                        "message" => directory_plugin_text_srt( 'directory_cropping_file_error' ),
                    );
                }
            } else {
                $response = array(
                    "status" => 'error',
                    "message" => directory_plugin_text_srt( 'directory_uploading_avatar_error' ),
                );
            }


            print json_encode( $response );

            wp_die();
        }

        public function image_crop_to_file_callback() {
            $upload_dir_info = wp_upload_dir();

            /*
             * 	!!! THIS IS JUST AN EXAMPLE !!!, PLEASE USE ImageMagick or some other quality image processing libraries
             */

            $imgUrl = $_POST['imgUrl'];
            //$imgUrl='http://serverwp.com/wpdirectory/wp-content/uploads/2016/09/waterfall_trees_mountains_sky_grass_summer_87320_3840x2400-6-180x180.jpg';
            $imgUrl_explode = explode( '/', $imgUrl );
            $image_name = end( $imgUrl_explode );
            $directory_absolute_path = $upload_dir_info['path'];
            $directory_unlink_image_path = $directory_absolute_path . '/' . $image_name;

            // original sizes
            $imgInitW = $_POST['imgInitW'];
            $imgInitH = $_POST['imgInitH'];
            // resized sizes
            $imgW = $_POST['imgW'];
            $imgH = $_POST['imgH'];
            // offsets
            $imgY1 = $_POST['imgY1'];
            $imgX1 = $_POST['imgX1'];
            // crop box
            $cropW = $_POST['cropW'];
            $cropH = $_POST['cropH'];
            // rotation angle
            $angle = $_POST['rotation'];

            $jpeg_quality = 100;
            $rand_val = rand();
            $upload_dir = $upload_dir_info['path'] . '/';
            $output_filename = $upload_dir_info['path'] . "/croppedImg_" . $rand_val;

            
            $filetype = wp_check_filetype( $imgUrl );


           

            switch ( strtolower( $filetype['type'] ) ) {
                case 'image/png':
                    $img_r = (function_exists('imagecreatefrompng'))? imagecreatefrompng( $imgUrl ) : '';
                    $source_image = (function_exists('imagecreatefrompng'))? imagecreatefrompng( $imgUrl ) : '';
                    $type = '.png';
                    break;
                case 'image/jpeg':
                    $img_r = (function_exists('imagecreatefromjpeg'))? imagecreatefromjpeg( $imgUrl ) : '';
                    $source_image = (function_exists('imagecreatefromjpeg'))? imagecreatefromjpeg( $imgUrl ) : '';
                    error_log( "jpg" );
                    $type = '.jpeg';
                    break;
                case 'image/gif':
                    $img_r = (function_exists('imagecreatefromgif'))? imagecreatefromgif( $imgUrl ) : '';
                    $source_image = (function_exists('imagecreatefromgif'))? imagecreatefromgif( $imgUrl ) : '';
                    $type = '.gif';
                    break;
                default: die( 'image type not supported' );
            }


            //Check write Access to Directory

            if ( ! is_writable( dirname( $output_filename ) ) ) {
                $response = Array(
                    "status" => 'error',
                    "message" => esc_html__('Can`t write cropped File','foodbakery')
                );
            } else {

                // resize the original image to size of editor
                $resizedImage = imagecreatetruecolor( $imgW, $imgH );
                imagecopyresampled( $resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH );
                // rotate the rezized image
                $rotated_image = imagerotate( $resizedImage, -$angle, 0 );
                // find new width & height of rotated image
                $rotated_width = imagesx( $rotated_image );
                $rotated_height = imagesy( $rotated_image );
                // diff between rotated & original sizes
                $dx = $rotated_width - $imgW;
                $dy = $rotated_height - $imgH;
                // crop rotated image to fit into original rezized rectangle
                $cropped_rotated_image = imagecreatetruecolor( $imgW, $imgH );
                imagecolortransparent( $cropped_rotated_image, imagecolorallocate( $cropped_rotated_image, 0, 0, 0 ) );
                imagecopyresampled( $cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH );
                // crop image into selected area
                $final_image = imagecreatetruecolor( $cropW, $cropH );
                imagecolortransparent( $final_image, imagecolorallocate( $final_image, 0, 0, 0 ) );
                imagecopyresampled( $final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH );
                // finally output png image
               
                imagejpeg( $final_image, $output_filename . $type, $jpeg_quality );
                

                $output_filename2 = $upload_dir_info['url'] . "/croppedImg_" . $rand_val;
                $response = Array(
                    "status" => 'success',
                    "url" => $output_filename . $type,
                    "absolute_path" => $output_filename2 . $type
                );
                unlink( $directory_unlink_image_path );
            }
            print json_encode( $response );
            wp_die();
        }

    }

    global $Directory_Image_Cropper;
    $Directory_Image_Cropper = new Directory_Image_Cropper();
}