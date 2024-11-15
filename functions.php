<?php



/**

 * MWL functions and definitions

 *

 * @link https://developer.wordpress.org/themes/basics/theme-functions/

 *

 * @package MWL

 */

if (!defined('_S_VERSION')) {

	// Replace the version number of the theme on each release.

	define('_S_VERSION', '1.0.0');

}

/**

 * Sets up theme defaults and registers support for various WordPress features.

 *

 * Note that this function is hooked into the after_setup_theme hook, which

 * runs before the init hook. The init hook is too late for some features, such

 * as indicating support for post thumbnails.

 */

function mwl_setup()

{

	/*

		* Make theme available for translation.

		* Translations can be filed in the /languages/ directory.

		* If you're building a theme based on MWL, use a find and replace

		* to change 'mwl' to the name of your theme in all the template files.

		*/

	load_theme_textdomain('mwl', get_template_directory() . '/languages');



	// Add default posts and comments RSS feed links to head.

	add_theme_support('automatic-feed-links');



	/*

		* Let WordPress manage the document title.

		* By adding theme support, we declare that this theme does not use a

		* hard-coded <title> tag in the document head, and expect WordPress to

		* provide it for us.

		*/

	add_theme_support('title-tag');



	/*

		* Enable support for Post Thumbnails on posts and pages.

		*

		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/

		*/

	add_theme_support('post-thumbnails');



	// This theme uses wp_nav_menu() in one location.

	register_nav_menus(

		array(

			'menu-1' => esc_html__('Primary', 'mwl'),

		)

	);



	/*

		* Switch default core markup for search form, comment form, and comments

		* to output valid HTML5.

		*/

	add_theme_support(

		'html5',

		array(

			'search-form',

			'comment-form',

			'comment-list',

			'gallery',

			'caption',

			'style',

			'script',

		)

	);



	// Set up the WordPress core custom background feature.

	add_theme_support(

		'custom-background',

		apply_filters(

			'mwl_custom_background_args',

			array(

				'default-color' => 'ffffff',

				'default-image' => '',

			)

		)

	);



	// Add theme support for selective refresh for widgets.

	add_theme_support('customize-selective-refresh-widgets');



	/**

	 * Add support for core custom logo.

	 *

	 * @link https://codex.wordpress.org/Theme_Logo

	 */

	add_theme_support(

		'custom-logo',

		array(

			'height'      => 250,

			'width'       => 250,

			'flex-width'  => true,

			'flex-height' => true,

		)

	);

}

add_action('after_setup_theme', 'mwl_setup');







/* Disable WordPress Admin Bar for all users */

add_filter( 'show_admin_bar', function() {

	if(!current_user_can( 'manage_options' ))

		return false;

} );



function block_wp_admin() {

	if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

		wp_safe_redirect( home_url() );

		exit;

	}

}

add_action( 'admin_init', 'block_wp_admin' );







/**

 * Set the content width in pixels, based on the theme's design and stylesheet.

 *

 * Priority 0 to make it available to lower priority callbacks.

 *

 * @global int $content_width

 */

function mwl_content_width()

{

	$GLOBALS['content_width'] = apply_filters('mwl_content_width', 640);

}

add_action('after_setup_theme', 'mwl_content_width', 0);



/**

 * Register widget area.

 *

 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar

 */

function mwl_widgets_init()

{

	register_sidebar(

		array(

			'name'          => esc_html__('Sidebar', 'mwl'),

			'id'            => 'sidebar-1',

			'description'   => esc_html__('Add widgets here.', 'mwl'),

			'before_widget' => '<section id="%1$s" class="widget %2$s">',

			'after_widget'  => '</section>',

			'before_title'  => '<h2 class="widget-title">',

			'after_title'   => '</h2>',

		)

	);

}

add_action('widgets_init', 'mwl_widgets_init');



/**

 * Enqueue scripts and styles.

 */

function mwl_scripts()

{

	wp_enqueue_style('mwl-style', get_stylesheet_uri(), array(), _S_VERSION);

	wp_style_add_data('mwl-style', 'rtl', 'replace');

	wp_enqueue_style('vendor-style', get_template_directory_uri() . '/assets/css/vendor.min.css');

	wp_enqueue_style('choices','https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', array(), _S_VERSION);



	$src = '/assets/css/style.css';

    $filetime = filemtime(get_stylesheet_directory().$src);

    wp_enqueue_style('app-style',get_template_directory_uri().$src,array(),$filetime);



	// wp_enqueue_script( 'mwl-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	wp_enqueue_script('choices','https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js', array(), null, false);

	wp_enqueue_script('vendor-scripts', get_template_directory_uri() . '/assets/js/vendor.min.js', array('jquery'), null, true);

	wp_enqueue_script('app-scripts', get_template_directory_uri() . '/assets/js/app.js', array('jquery'), null, true);





	$src = '/assets/js/custom.js';

    $filetime = filemtime(get_stylesheet_directory().$src);

    wp_enqueue_script( 'custom', get_template_directory_uri().$src, array( 'jquery' ), $filetime, false);



    $src = '/assets/js/app.donation.js';

    $filetime = filemtime(get_stylesheet_directory().$src);

    wp_enqueue_script( 'donation', get_template_directory_uri().$src, array( 'jquery' ), $filetime, false);	





     wp_localize_script( 'custom', 'ajaxobject', [

 		'ajax_url' => admin_url( 'admin-ajax.php'),

 	]);

	// wp_localize_script('custom-script', 'order_data', array(

	// 	'order_id' => $order_id,

	// ));



	if (is_singular() && comments_open() && get_option('thread_comments')) {

		wp_enqueue_script('comment-reply');

	}

}

add_action('wp_enqueue_scripts', 'mwl_scripts');





function my_enqueue() {



    wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my-ajax-script.js', array('jquery') );



   

}



function load_template_part($template_name, $part_name=null) {

    ob_start();

    get_template_part($template_name, $part_name);

    $var = ob_get_contents();

    ob_end_clean();

    return $var;

}







/**

 * Custom template tags for this theme.

 */

require get_template_directory() . '/inc/template-tags.php';



/**

 * Functions which enhance the theme by hooking into WordPress.

 */

require get_template_directory() . '/inc/template-functions.php';



/**

 * Customizer additions.

 */

require get_template_directory() . '/inc/customizer.php';



/* 

** Theme's custom functionals 

*/



require get_template_directory() . '/inc/acf-blocks.php';



require get_template_directory() . '/inc/acf-functions.php';



require get_template_directory() . '/inc/user-registration.php';



require get_template_directory() . '/inc/update-price.php';



require get_template_directory() . '/inc/woocommerce-functions.php';



require get_template_directory() . '/inc/custom-post.php';



require get_template_directory() . '/inc/post-functions.php';





require get_template_directory() . '/core/donation.php';





/**

 * Load Jetpack compatibility file.

 */

/* if (defined('JETPACK__VERSION')) {

	require get_template_directory() . '/inc/jetpack.php';

} */

add_filter('wpcf7_form_elements', function ($html) {



	preg_match('~<input([^>]+)type=["\']submit["\']([^>/]+)/?>~i', $html, $match);



	if ($match) {

		$input = $match[0];

		$attr = trim($match[1] . $match[2]);



		preg_match('/value=["\']([^"\']+)/', $input, $mm);

		$button_text = $mm[1];



		$html = str_replace($input, "<button $attr><span>$button_text</span></button>", $html);

	}



	return $html;

});



 function add_country_field_to_registration_form() {

	?>

	<p>

			<label for="country"><?php _e('Country', 'textdomain'); ?><br />

			<select name="country" id="country">

					<option value=""><?php _e('Select a country', 'textdomain'); ?></option>

					<?php

					$countries = get_remote_countries(); 

				

					foreach ($countries as $country) {

							echo '<option value="' . esc_attr($country) . '">' . esc_html($country) . '</option>';

					}

					?>

			</select>

	</p>

	<?php

}

//add_action('register_form', 'add_country_field_to_registration_form'); 



/* // Подключение библиотеки iATS

require_once('path/to/iats.php');



// Получение данных о подписке и платежной карте от пользователя

$card_number = $_POST['card_number'];

$expiry_month = $_POST['expiry_month'];

$expiry_year = $_POST['expiry_year'];

$cvv = $_POST['cvv'];

$email = $_POST['email'];

$name = $_POST['name'];

$plan_id = $_POST['plan_id'];

$payment_method = $_POST['payment_method'];



// Создание объекта платежной карты

$credit_card = new iATS\CreditCard();

$credit_card->setNumber($card_number);

$credit_card->setExpiryMonth($expiry_month);

$credit_card->setExpiryYear($expiry_year);

$credit_card->setCvv($cvv);



// Создание объекта подписки

$subscription = new iATS\Subscription();

$subscription->setCustomerEmail($email);

$subscription->setCustomerName($name);

$subscription->setPlanId($plan_id);

$subscription->setPaymentMethod($payment_method);

$subscription->setCreditCard($credit_card);



// Создание клиента в системе iATS

$client = new iATS\Client();

$client->setCredentials('ВАШ_ЛОГИН', 'ВАШ_ПАРОЛЬ');



// Создание запроса на создание подписки

$request = new iATS\Request\SubscriptionCreateRequest($subscription);



// Отправка запроса на сервер iATS

$response = $client->sendRequest($request);



// Обработка ответа

if ($response->isSuccessful()) {

	// Подписка успешно создана

	$subscription_id = $response->getSubscriptionId();

	// выполнение дальнейших действий

} else {

	// Обработка ошибки создания подписки

	$error_message = $response->getErrorMessage();

	// выполнение дальнейших действий

}

 */



// Active Classic Editor

add_filter (  'use_block_editor_for_post_type' , function (  $use , $post_type  ) {

 

	if (  'post' === $post_type  )  { 

		$use = false ; 

	} 

	return  $use ;

 

} , 9999 , 2  ) ;



// Add svg in media

// add_filter( 'upload_mimes', 'svg_upload_allow' );

// function svg_upload_allow( $mimes ) {

// 	$mimes['svg']  = 'image/svg+xml';



// 	return $mimes;

// }



// Add hierarchical

add_filter( 'register_post_type_args', 'add_hierarchy_support', 10, 2 );

function add_hierarchy_support( $args, $post_type ){



    if ($post_type === 'product') {

        $args['hierarchical'] = true;

        $args['supports'] = array_merge($args['supports'], array ('page-attributes') );

    }



    return $args;

}



function custom_pre_get_posts_query( $q ) {

 

    $tax_query = (array) $q->get( 'tax_query' );

 

    $tax_query[] = array(

           'taxonomy' => 'product_cat',

           'field' => 'slug',

           'terms' => array( 'programs','where-we-work' ), 

           'operator' => 'NOT IN'

    );

 

 

    $q->set( 'tax_query', $tax_query );

 

}

add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );



add_action( 'after_setup_theme', 'woocommerce_support' );

function woocommerce_support() {

    add_theme_support( 'woocommerce' );

}



add_action('wp_ajax_nopriv_stock_submission', 'stock_submission');

add_action('wp_ajax_stock_submission', 'stock_submission');



function stock_submission()

{

	$data = $_POST['data'];

	// print_r($data);

	$message = "";
	foreach($data as $row) {
		if(!empty($row['value'])){
			//echo $row['name'] .'-'. $row['value'].'</br>';
			if (strpos($row['name'], '_') !== false) {
				$parts = explode('_', $row['name']);
				$capitalized_label = array_map('ucfirst', $parts);
				$capitalized_label = implode('_', $capitalized_label);
				$capitalized_label = str_replace('_', ' ', $capitalized_label);
				$capitalized_label = str_replace('Des', 'Description', $capitalized_label);

			} else {
				$capitalized_label = ucfirst($row['name']);
			}


			$message .= '<b>'.$capitalized_label.'</b> : '. $row['value'].'<br>';

		}
	    
	}

	$to = 'info@mercywithoutlimits.org';
	$subject = 'MWL Stock form data';
	$body = $message;
	$headers = array('Content-Type: text/html; charset=UTF-8');

	// Send HTML email using wp_mail function
	wp_mail($to, $subject, $body, $headers);
	die();

}



function get_country_list() {







	$country_list = ['United States', 'Afghanistan', 'Åland Islands', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica', 'Antigua & Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Caribbean Netherlands', 'Bosnia & Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo - Brazzaville', 'Congo - Kinshasa', 'Cook Islands', 'Costa Rica', 'Côte d’Ivoire', 'Croatia', 'Cuba', 'Curaçao', 'Cyprus', 'Czechia', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Islas Malvinas)', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Heard & McDonald Islands', 'Vatican City', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Isle of Man', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jersey', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'North Korea', 'South Korea', 'Kosovo', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macao', 'North Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar (Burma)', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Curaçao', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn Islands', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Réunion', 'Romania', 'Russia', 'Rwanda', 'St. Barthélemy', 'St. Helena', 'St. Kitts & Nevis', 'St. Lucia', 'St. Martin', 'St. Pierre & Miquelon', 'St. Vincent & Grenadines', 'Samoa', 'San Marino', 'São Tomé & Príncipe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Sint Maarten', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia & South Sandwich Islands', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Svalbard & Jan Mayen', 'Eswatini', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tokelau', 'Tonga', 'Trinidad & Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks & Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'U.S. Outlying Islands', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'British Virgin Islands', 'U.S. Virgin Islands', 'Wallis & Futuna', 'Western Sahara', 'Yemen', 'Zambia', 'Zimbabwe'];



	return $country_list;



}





/*

add_action('wp_ajax_donation_add_to_cart', 'donation_add_to_cart_callback');

add_action('wp_ajax_nopriv_donation_add_to_cart', 'donation_add_to_cart_callback');



function donation_add_to_cart_callback() {



	$data = get_cart_items();

	$title = $_POST['title'];

	$cart_item = [

		'type' => $_POST['type'],

		'amount' => $_POST['amount'],

		'title' => htmlspecialchars(trim($title)),

		'program_id' => $_POST['program_id'],		

		'cause_id' => $_POST['cause_id'],		

		'country' => $_POST['country'],		

	];





	$data[] = $cart_item;



	setcookie('cart_items', json_encode($data), time()+60*60*24*30, COOKIEPATH, COOKIE_DOMAIN);



	ob_start();

	$cart_items = $data;

	include STYLESHEETPATH . '/template-parts/donation-cart-items.php';	

	$html = ob_get_clean();

	include STYLESHEETPATH . '/template-parts/donation-page-cart-items.php';

	$html_page = ob_get_clean();

	echo json_encode([

		'data' => [

			'cart_count' => count($data),

			'html' => $html,

			'html_page' => $html_page,

		]

	]);



	wp_die();

}*/





add_action('wp_ajax_myappealfilterdonate', 'myappealfilterdonate');

add_action('wp_ajax_nopriv_myappealfilterdonate', 'myappealfilterdonate');

  function myappealfilterdonate()

  {

    global $post;

    $cause_id = $_POST['cause_id'];

    $cause_type = $_POST['cause_type'];    

    $post_type = get_post_type($_POST['cause_id']);

     if($post_type == 'programs'){

        $countries = get_the_terms( $_POST['cause_id'], 'country');

      }

      

?>

    <option value="" hidden>Choose a country</option>

    <?php

    if( is_array( $countries ) ){

            foreach ($countries as $country) {            

              echo '<option value="'.$country->term_id.'">' . $country->name . '</option>';

            }

          }    

    die();

  }

 /*add_action('wp_ajax_mypricefilter_donate', 'mypricefilter_donate');

add_action('wp_ajax_nopriv_mypricefilter_donate', 'mypricefilter_donate');

  function mypricefilter_donate(){

    global $post;

      $post_id = $_POST['post_id'];

      $donation_type = $_POST['donation_type'];

  ?>

      <div class="form__row form__row--three">

      <?php

      if($donation_type == 'monthly'){

        $price = 'add_monthly_price';

      }

      else if($donation_type == 'once'){

         $price = 'add_price';

      }

    if( have_rows($price, $post_id) ):

      while( have_rows($price, $post_id) ) : the_row();         

              $sub_value = get_sub_field('price', $post_id);

      

                ?>

                <div class="form__col">          

                  <div class="base-checkbox checkbox">

                    <div class="value_donate base-checkbox__label big" data-value="<?php echo $sub_value;?>">$<?php echo $sub_value;?></div>

                  </div>

                </div>

                <?php

             endwhile;

      endif;

            ?>

            <div class="form__col">

              <div class="form__field checkbox">

                <input class="form__input form__input--sm-padding text-center" type="text" name="temp_amoumt" placeholder="$0.00">

              </div>

            </div>

        </div>

             <?php

    die();

  }*/





  

function getAmount($money)

{

    $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);

    $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);



    $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;



    $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);

    $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);



    return (float) str_replace(',', '.', $removedThousandSeparator);

}





//add_filter('http_request_args', 'bal_http_request_args', 100, 1);

function bal_http_request_args($r) //called on line 237

{

	$r['timeout'] = 1000;

	return $r;

}

 

//add_action('http_api_curl', 'bal_http_api_curl', 100, 1);

function bal_http_api_curl($handle) //called on line 1315

{

	curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 1000 );

	curl_setopt( $handle, CURLOPT_TIMEOUT, 1000 );

}


// Change the From address.
add_filter( 'wp_mail_from', function ( $original_email_address ) {
    return 'noreply@mwlimits.org';
} );
 
// Change the From name.
add_filter( 'wp_mail_from_name', function ( $original_email_from ) {
    return 'Mercy Without Limits';
} );

// add_action('wp_head', 'show_template');
function show_template() {
   global $template;
   echo basename($template);
}

function splitString($str) {
    $parts = explode(' - ', $str);
    $result = [
        'name' => isset($parts[0]) ? strtolower(trim($parts[0])) : '',
        'appeal' => isset($parts[1]) ? strtolower(trim($parts[1])) : '',
        'country' => [],
    ];
	if (isset($parts[2])) {
        $result['country'] = explode('/', strtolower(trim($parts[2])));
    }
    return $result;
}