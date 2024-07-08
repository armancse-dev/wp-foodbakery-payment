<?php

class foodbakery_cousine_api{

    function __construct() {

        add_action( 'rest_api_init', array($this, 'my_register_route') );

    }

    function my_register_route() {

       

        register_rest_route( 'restaurant', 'cousines', array(
            'methods' => 'GET',
            'callback' => array($this,'restaurants_cousines')
            )
        );

    }
    


function restaurants_cousines(){

    $terms = get_terms( array( 
        'number' => 10,     
        'taxonomy' => 'restaurant-category',
        'hide_empty' => false,
        ) );


    foreach ($terms as $value) {

        $cousine_term_id =$value->term_id;
        $cousine_name =$value->name;
        $cousine_slug =$value->slug;

        $cousine_image_id= get_term_meta( $cousine_term_id,'foodbakery_listing_term_image', true );
        $cousine_image_data=wp_get_attachment_image_src( $cousine_image_id, 'full' );
        $cousine_image_url=$cousine_image_data[0];



        $cousines_array[] = array( 
            array(
              'field_name'=>'cousine_name',
              'field_type'=>'text',
              'data_type'=>'text_display',
              'color' => '#000000',
              'data' =>$cousine_name,
              'API_URL' =>'restaurant/list/?restaruant_category='.$cousine_slug
              ),
            array(
                'field_name'=>'cousine_image',
                'field_type'=>'imageType',
                'data_type'=>'imageType_display',
                'data'=>$cousine_image_url
                )
            );
    }

    $cousines = array( 

        'section' => 'cousines', 
        'fields' => $cousines_array
        );

    return rest_ensure_response($cousines);

}

}
new foodbakery_cousine_api();

?>