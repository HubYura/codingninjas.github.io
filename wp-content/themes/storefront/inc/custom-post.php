<?php
/**
 * Sample implementation of the Custom Post feature
 *
 * @package 
 */

/**
 *
 * @uses 
 */

add_filter( 'template_include', 'echo_cur_tplfile', 99 );
function echo_cur_tplfile( $template ){
	echo '<span style="color:red">Shab:'. wp_basename( $template ) .'</span>';
	return $template; 
} 

class sm_movies {

	function sm_movies() {
		add_action('init',array($this,'create_post_type'));
		add_action('init',array($this,'create_taxonomies'));
		add_action('manage_sm_movies_posts_columns',array($this,'columns'),10,2);
		add_action('manage_sm_movies_posts_custom_column',array($this,'column_data'),11,2);
		add_filter('posts_join',array($this,'join'),10,1);
		add_filter('posts_orderby',array($this,'set_default_sort'),20,2);
	}

	function create_post_type() {
		$labels = array(
			'name'               => 'Movies',
			'singular_name'      => 'Movie',
			'menu_name'          => 'Movies',
			'name_admin_bar'     => 'Movies',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Movie',
			'new_item'           => 'New Movie',
			'edit_item'          => 'Edit Movie',
			'view_item'          => 'View Movie',
			'all_items'          => 'All Movies',
			'search_items'       => 'Search Movie',
			'parent_item_colon'  => 'Parent movies',
			'not_found'          => 'No movies Found',
			'not_found_in_trash' => 'No movies Found in Trash'
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-video',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
			'has_archive'         => true,
			/*'rewrite'             => array( 'slug' => 'movies' ),*/
			'query_var'           => true
		);

		register_post_type( 'sm_movies', $args );
	}

	function create_taxonomies() {

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Type Movies',
			'singular_name'     => 'Types Movies',
			'search_items'      => 'Search Type',
			'all_items'         => 'All Types',
			'parent_item'       => 'Parent Type',
			'parent_item_colon' => 'Parent Type:',
			'edit_item'         => 'Edit Type',
			'update_item'       => 'Update Type',
			'add_new_item'      => 'Add New',
			'new_item_name'     => 'Add New Type',
			'menu_name'         => 'Types',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			/*'rewrite'           => array( 'slug' => 'type_movies' ),*/
		);

		register_taxonomy('sm_movies_type',array('sm_movies'),$args);
	}

	function columns($columns) {
		unset($columns['date']);
		unset($columns['taxonomy-sm_movies_attribute']);
		unset($columns['comments']);
		unset($columns['author']);
		return array_merge(
			$columns,
			array(
				'price' => 'Price',
			));
	}

	function column_data($column,$post_id) {
		switch($column) {
			case 'price' :
				echo get_post_meta($post_id,'_price',1);
				break;
		}
	}

	function join($wp_join) {
		global $wpdb;
		if(get_query_var('post_type') == 'sm_movies') {
			$wp_join .= " LEFT JOIN (
					SELECT post_id, meta_value AS price
					FROM $wpdb->postmeta
					WHERE meta_key = '_price' ) AS meta
					ON $wpdb->posts.ID = meta.post_id ";
		}
		return ($wp_join);
	}

	function set_default_sort($orderby,&$query) {
		global $wpdb;
		if(get_query_var('post_type') == 'sm_movies') {
			return "meta.price DESC";
		}
	 	return $orderby;
	}
}

new sm_movies();

function do_excerpt($string, $word_limit) {
  $words = explode(' ', $string, ($word_limit + 1));
  if (count($words) > $word_limit)
  array_pop($words);
  echo implode(' ', $words).' ...';
}

// подключаем функцию активации мета блока (movies_proextra_fields)
add_action('admin_init', 'movies_proextra_fields', 1);

function movies_proextra_fields() {
	add_meta_box( 'proextra_fields', 'Details of the post', 'proextra_fields_box_func', 'sm_movies', 'normal', 'high'  );
}

function proextra_fields_box_func( $post ){
?>
	<p><label>Subheading<input type="text" name="proextra[subheading]" class="input-movies-trslt" value="<?php echo get_post_meta($post->ID, 'subheading', 1); ?>" style="width:80%;float:right;" /></label></p>
	<p><label>Price<input type="text" name="proextra[_price]" class="input-movies-trslt" value="<?php echo get_post_meta($post->ID, '_price', 1); ?>" style="width:80%;float:right;" /></label></p>
	<input type="hidden" name="proextra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
<?php
}

// включаем обновление полей при сохранении
add_action('save_post', 'movies_proextra_fields_update', 0);

/* Сохраняем данные, при сохранении поста */
function movies_proextra_fields_update( $post_id ){
	if( ! wp_verify_nonce($_POST['proextra_fields_nonce'], __FILE__) ) return false; // проверка
	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return false; // если это автосохранение
	if( ! current_user_can('edit_post', $post_id) ) return false; // если юзер не имеет право редактировать запись

	if( !isset($_POST['proextra']) ) return false;

	// сохранить/удалить данные
	$_POST['proextra'] = array_map('trim', $_POST['proextra']);
	foreach( $_POST['proextra'] as $key=>$value ){
		if( empty($value) ){
			delete_post_meta($post_id, $key); // удаляем поле если значение пустое
			continue;
		}

		update_post_meta( $post_id, $key, $value ); 
	}

	return $post_id;
}

add_filter('the_content','rei_add_to_cart_button', 20,1);
function rei_add_to_cart_button($content){
	global $post;
	if ($post->post_type !== 'sm_movies') {return $content; }
	
	ob_start();
	?>
	<h2><?php echo get_post_meta($post->ID, 'subheading', true); ?></h2>
	<h3><?php echo get_post_meta($post->ID, '_regular_price', true); ?></h3>
	<?php
    if ( class_exists( 'WooCommerce' ) ) {
      echo ( 'WooCommerce' );
          ?>
	<form action="<?php echo esc_url( get_permalink() ); ?>" method="post">
		<input name="add-to-cart" type="hidden" value="<?php echo $post->ID ?>" />
		<input name="quantity" type="number" value="1" min="1"  />
		<input name="submit" type="submit" value="Add to cart" />
	</form>
	<?php
	do_action( 'woocommerce_after_add_to_cart_quantity' );
    } else {
      echo ( 'No WooCommerce' );
    }

	
	return $content . ob_get_clean();
}

/***
    Skype
****/
function my_custom_setup_fields() {
    $fields = array(
        array(
            'key' => 'skype',
            'label' => 'Skype',
            'placeholder' => 'Skype',
            'error' => 'Do not forget Skype.'
        )
    );
   
   
    return $fields;
}
/**
 * Поле Skype
 */
add_action( 'woocommerce_register_form_start', 'my_custom_checkout_field' );
 
function my_custom_checkout_field() {
    $fields = my_custom_setup_fields();
 
 
    if ( ! empty( $fields ) ) {
       
        foreach ($fields as $field) {
            woocommerce_form_field(
                $field['key'],
                array(
                    'type'          => 'text',
                    'class'         => array('my-class form-row-wide'),
                    'label'         => __($field['label']),
                    'placeholder'   => __($field['placeholder']),
                ),
                get_user_meta( get_current_user_id(), $field['key'] , true  )
            );
        }
    }
}
/**
 * Верт
 */
add_action('woocommerce_register_form_start', 'my_custom_checkout_field_process');
 
function my_custom_checkout_field_process() {
    $fields = my_custom_setup_fields();
   
    if ( ! empty( $fields ) ) {
        foreach($fields as $field) {
            $key = $field['key'];
               
            if ( ! $_POST[$key] ) {
                wc_add_notice( __( $field['error'] ), 'error' );
            }
        }
    }
}
/*
 * Обнов поле
 */
function wooc_save_extra_register_fields( $customer_id ) {
      if ( isset( $_POST['skype'] ) ) {
             // Skype для ВП
             update_user_meta( $customer_id, 'skype', sanitize_text_field( $_POST['skype'] ) );
             // Skype для ВК
             update_user_meta( $customer_id, 'skype', sanitize_text_field( $_POST['skype'] ) );
      }
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );

function wooc_account_skype_fields() {
	$cur_user_id = get_current_user_id();
	$user_skype = get_user_meta( $cur_user_id, 'skype', true ); 
		?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="account_skype"><?php esc_html_e( 'Skype', 'woocommerce' ); ?>&nbsp;</label>
			<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_skype" id="account_skype" autocomplete="skype" value="<?php echo $user_skype; ?>" />
		</p>
       <?php
 }
 add_action( 'woocommerce_edit_account_form_start', 'wooc_account_skype_fields' );
//перенаправление на wishlist
function custom_registration_redirect() {
    wp_logout();
    return home_url('/wishlist');
}
add_action('woocommerce_registration_redirect', 'custom_registration_redirect', 2);
//замена Add to Cart на Buy Now! 
add_filter( 'woocommerce_product_single_add_to_cart_text', 'lw_cart_btn_text' );
add_filter( 'woocommerce_product_add_to_cart_text', 'lw_cart_btn_text' );
function lw_cart_btn_text() {
 return __( 'Buy Now!', 'woocommerce' );
}
//перенаправление на checkout
add_filter('add_to_cart_redirect', 'lw_add_to_cart_redirect');
function lw_add_to_cart_redirect() {
 global $woocommerce;
 $lw_redirect_checkout = $woocommerce->cart->get_checkout_url();
 return $lw_redirect_checkout;
}

//подключ ВК
class WCCPT_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

    /**
     * Method to read a product from the database.
     * @param WC_Product
     */

    public function read( &$product ) {

        $product->set_defaults();

        if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || ! in_array( $post_object->post_type, array( 'sm_movies', 'product' ) ) ) { 
            throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
        }

        $id = $product->get_id();

        $product->set_props( array(
            'name'              => $post_object->post_title,
            'slug'              => $post_object->post_name,
            'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
            'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
            'status'            => $post_object->post_status,
            'description'       => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id'         => $post_object->post_parent,
            'menu_order'        => $post_object->menu_order,
            'reviews_allowed'   => 'open' === $post_object->comment_status,
        ) );

        $this->read_attributes( $product );
        $this->read_downloads( $product );
        $this->read_visibility( $product );
        $this->read_product_data( $product );
        $this->read_extra_data( $product );
        $product->set_object_read( true );
    }

    /**
     * Get the product type based on product ID.
     *
     * @since 3.0.0
     * @param int $product_id
     * @return bool|string
     */
    public function get_product_type( $product_id ) {
        $post_type = get_post_type( $product_id );
        if ( 'product_variation' === $post_type ) {
            return 'variation';
        } elseif ( in_array( $post_type, array( 'sm_movies', 'product' ) ) ) { 
            $terms = get_the_terms( $product_id, 'product_type' );
            return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
        } else {
            return false;
        }
    }
}

add_filter( 'woocommerce_data_stores', 'woocommerce_data_stores' );

function woocommerce_data_stores ( $stores ) {      
    $stores['product'] = 'WCCPT_Product_Data_Store_CPT';
    return $stores;
}