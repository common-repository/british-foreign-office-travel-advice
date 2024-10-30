<?php

/*
 * Plugin Name: British Foreign Office Travel Advice
 * Description: Displays BFO Travel Advice for countries around the globe. Ensure you have a custom meta field in each post called 'geo_country' with the country you want to display information for.
 * Author: Matt Hawthorne
 * Author URI: http://utopiamultimedia.com
 * Plugin URI: http://utopiamultimedia.com/wordpress-plugins/
 * Author Email: matt@utopiamultimedia.com
 * Version: 1.0
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


/*you can use the following shortcodes

Add a custom meta field to your posts called 'geo_country', and add the country you would like information for.

[fo-advice] to get info as the page loads (displayed in an accordion type box)
[fo-advice-button] to get info via ajax

*/

add_shortcode( 'fo-advice', 'utopia_get_foadvice' );
add_shortcode( 'fo-advice-button', 'utopia_get_foadvice_button' );

function setup_accordion(){
// accordion thanks to www.snyderplace.com

wp_enqueue_script( 'utopia_fo_jquery_accordion', plugin_dir_url( __FILE__ ) . 'js/jquery.accordion.js', array(), '1.0.0', true );
wp_enqueue_script( 'utopia_fo_jquery_accordion_setup', plugin_dir_url( __FILE__ ) . 'js/fo_accordion_setup.js', array(), '1.0.0', true );

};

add_action( 'wp_enqueue_scripts', 'setup_accordion' );

if ( ! class_exists( 'utopia_bfo_advice' ) ) {

	class utopia_bfo_advice {

        /**
         * WordPress requires an action name for each AJAX request
         *
         * @var string $action
         */
        private $action = 'utopia_construct_bfo_ajaxcall';

		function __construct() {

			// Add our javascript file that will initiate our AJAX requests
			add_action( 'wp_enqueue_scripts', array( $this, 'utopia_bfo_scripts' ) );

            // Let's make sure we are actually doing AJAX first
            if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                // Add our callbacks for AJAX requests
                add_action( 'wp_ajax_' . $this->action, array( $this, 'utopia_bfo_do_ajax' ) ); // For logged in users
                add_action( 'wp_ajax_nopriv_' . $this->action, array( $this, 'utopia_bfo_do_ajax' ) ); // For logged out users
            }
		}
        /**
         * Enqueue our script that will initiate our AJAX requests and pass important variables
         * from PHP to our JavaScript.
         */
        function utopia_bfo_scripts() {

                // Load our script
				wp_enqueue_script( 'utopia_bfo_script', plugins_url('ajax.js', __FILE__), array('jquery') );

                // Pass a collection of variables to our JavaScript
				wp_localize_script( 'utopia_bfo_script', 'utopiaBFO', array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'action' => $this->action,
                    'nonce' => wp_create_nonce( $this->action ),
				) );
		}
        /**
         * Back-end processing of our AJAX requests.
         */
        function utopia_bfo_do_ajax() {

            // By default, let's start with an error message
			$response = array(
				'status' => 'error',
				'message' => 'Invalid nonce',
			);

            // Next, check to see if the nonce is valid
            if( isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], $this->action ) ){

                // Update our message / status since our request was successfully processed
                $response['status'] = 'success';
               // $response['message'] = "TEST MESSAGE";
//$response['postID'] = $_GET['postID'];

$response['message'] = utopia_fo_advice($_GET['postID']);

            }

            // Return our response to the script in JSON format
			header( 'Content: application/json' );
			echo json_encode( $response );
			die;
		}
	}

	new utopia_bfo_advice();
}

// -----------------------------------shortcodes -------------------------------------------



// shortcode to import FO advice directly into a page
function utopia_get_foadvice(){

$pageID=get_the_ID();
if (isset($pageID))
{
return utopia_fo_advice($pageID);
}
};

// shortcode to display a link to get FO advice via ajax
function utopia_get_foadvice_button(){

wp_enqueue_scripts('/fo_style.css' );

wp_register_style('utopia_fo_sidebar', plugins_url('/css/fo_style.css', __FILE__));
wp_enqueue_style('utopia_fo_sidebar');


$pageID=get_the_ID();
$themeta = get_post_meta($pageID , 'geo_country');

if (!empty($themeta))
{
$country = strtolower($themeta[0]);
$country = ucfirst($country);
return '<button class="ajax-yall fo-sidebar" data-postid="'. $pageID .'">'. __('Foreign Office Travel Advice for ' . $country, 'ajax-yall') .'</button>';
}
};


function utopia_fo_advice($pageID){
wp_register_style('utopia_fo_sidebar', plugins_url('/css/fo_style.css', __FILE__));
wp_enqueue_style('utopia_fo_sidebar');
$themeta = get_post_meta($pageID , 'geo_country');
if (!empty($themeta))
{
$country = strtolower($themeta[0]);
$fo_content =  utopia_getjsonadvice($country);
}

if (isset($fo_content))
{
return $fo_content;
}
};


function utopia_getjsonadvice($country){

$url = 'https://www.gov.uk/api/foreign-travel-advice/' . $country . '.json';
//$url = 'https://www.gov.uk/api/foreign-travel-advice/iraq.json';

$advicecontent = "";
//$json = file_get_contents($url);

$json = get_data($url);

//var_dump($json);

if (strpos($json, 'Page not found') !==False){
$advicecontent = '<p>Sorry, information about ' . ucfirst($country) . ' is not available';
return $advicecontent;
}
else
{
$data = json_decode($json, true);
$weburl=(string)$data['web_url'];
$title = (string)$data['title'];
$pubdate = (string)$data['updated_at'];
$pubdate = strftime("%d/%m/%Y %H:%M", strtotime($pubdate));
$description = (string)$data['details']['description'];
$summary = (string)$data['details']['summary'];
$parts1title =  (string)$data['details']['parts'][0]['title'];
$parts1content =  (string)$data['details']['parts'][0]['body'];
$parts2title =  (string)$data['details']['parts'][1]['title'];
$parts2content =  (string)$data['details']['parts'][1]['body'];
$parts3title =  (string)$data['details']['parts'][2]['title'];
$parts3content =  (string)$data['details']['parts'][2]['body'];
$parts4title =  (string)$data['details']['parts'][3]['title'];
$parts4content =  (string)$data['details']['parts'][3]['body'];
$parts5title =  (string)$data['details']['parts'][4]['title'];
$parts5content =  (string)$data['details']['parts'][4]['body'];
$parts6title =  (string)$data['details']['parts'][5]['title'];
$parts6content =  (string)$data['details']['parts'][5]['body'];

if (isset($data['details']['image']['web_url']))
{
$countryimage = (string)$data['details']['image']['web_url'];
$countrypdf = (string)$data['details']['document']['web_url'];
}

$advicecontent = '<div id="fo-advice-content">';
//$advicecontent .= '<h2>'. $title . '</h2>';

$advicecontent .= '<p>' . $description . '</p>';
$advicecontent .=  '<p class="fo-pubdate">Last Updated: '. $pubdate . '</p>';
$advicecontent .= '<div class="clearfix"></div>';


$advicecontent .= '<div class="accordion" id="nav-section1">Summary<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .= '<p>' . $summary . '</p>';
$advicecontent .= '</div>';


$advicecontent .= '<div class="accordion" id="nav-section2">' . $parts1title . '<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .=  $parts1content;
$advicecontent .= '</div>';


$advicecontent .= '<div class="accordion" id="nav-section3">' . $parts2title . '<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .=  $parts2content;
$advicecontent .= '</div>';

$advicecontent .= '<div class="accordion" id="nav-section3">' . $parts3title . '<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .=  $parts3content;
$advicecontent .= '</div>';

$advicecontent .= '<div class="accordion" id="nav-section4">' . $parts4title . '<span></span></div>';
$advicecontent .=  '<div>';
$advicecontent .=  $parts4content;
$advicecontent .= '</div>';

$advicecontent .= '<div class="accordion" id="nav-section5">' . $parts5title . '<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .=  $parts5content;
$advicecontent .= '</div>';

if (isset($countryimage))
{
$advicecontent .= '<div class="accordion" id="nav-section6">Printables<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .= '<div style="float:right"><img src="' . $countryimage . '" /><p><a href="' .$countrypdf.'">Print Map</a></p></div>';
$advicecontent .= '</div>';
}

$advicecontent .= '<div class="accordion" id="nav-section5">Other Information<span></span></div>';
$advicecontent .= '<div>';
$advicecontent .=  '<a href="' . $weburl . '" target="_blank">Foreign Office Website</a><br/>';
$advicecontent .= '</div>';
$advicecontent .= '</div>';

return $advicecontent;
}


};

function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
};

?>


