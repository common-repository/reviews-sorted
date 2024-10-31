<?php
class ReviewsSortedAPI
{
	
    CONST SYS_URL = 'https://sys.reviewssorted.com/api/v1';

    public static function check_key($licence){

    	$data = array("activation_key" => $licence);
	    $data_string = json_encode($data);

	    $response = wp_remote_post( self::SYS_URL . '/account/check-key', array(
	        'body'      => json_encode($data),
	        'headers'   => array(
	            'Content-Type: application/json',	            
	        )        
	    ) );

	    return json_decode($response["body"]);
    }
    public static function import_review($review){
    	$verify_key = get_option('reviews_sorted_verify_key', '');
    	if( !$verify_key ){
    		return false;	
    	}

    	$data = [
    		'site' 		=> get_bloginfo('name'),
    		'reviews' 	=> [
    			[
					"id"		=> isset($review['id']) 		? $review['id'] : time(),
					"author"	=> $review['authorfname'] . ' ' . $review['authorlname'],
					"phone"		=> isset($review['phone']) 		? $review['phone'] : '',
					"email"		=> isset($review['email']) 		? $review['email'] : '',
					"state"		=> isset($review['state']) 		? $review['state'] : '',
					"region"	=> isset($review['region']) 	? $review['region'] : '',
					"content"	=> isset($review['content']) 	? $review['content'] : '',
					"rating"	=> isset($review['rating']) 	? $review['rating'] : '',
		            "date"		=> isset($review['created_at']) ? date("Y-m-d H:i:s", strtotime($review['created_at'])) : date('Y-m-d H:i:s'), //"2022-04-07",
		            "recommend"	=> isset($review['recommend']) 	? $review['recommend'] : 'yes',
		            "service"	=> isset($review['service']) 	? $review['service'] : ''
				],
    		]
    	];

	    // $data_string = json_encode($data);

	    $response = wp_remote_post( self::SYS_URL . '/review/import', array(
	        'body'      => json_encode($data),
	        'headers'   => array(
	            'Content-Type' 		=> 'application/json',
	            'Authorization' 	=> $verify_key,	            
	        )        
	    ) );
	    
    	return json_decode($response["body"]);
    }
}


