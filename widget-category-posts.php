<?php
/*
Plugin Name:  Posts from a Specific Category
Description:  Adds a widget that shows the latest posts from one or more categories.
Version:      1.0
Author:       Anton Anastasiia
License:  GPLv2 or later     
*/

add_action( 'widgets_init', 'posts_category_widgets' );
function posts_category_widgets() {
	register_widget( 'Posts_Category' );
}

class Posts_Category extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'posts_category',  
			'Posts from a Specific Category'   
		);
		add_action( 'widgets_init', function() {
			register_widget( 'Posts_Category' );
		});
	}

	public $args = array(
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		'before_widget' => '<div class="widget-wrap">',
		'after_widget'  => '</div>',
	);

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$number = ! empty( $instance['number'] ) ? $instance['number'] : 1;
		$number_words = ! empty( $instance['number_words'] ) ? $instance['number_words'] : 10;
        $categories_id = array();
        $categories = get_categories( array(
			'order'   => 'ASC',
			'hide_empty' => false
		) );

		foreach( $categories as $category ) {
			$cat = $category->term_id;
			if ( $instance[$cat] == true ) {
           		array_push($categories_id, $cat);
			}
		}
		$args = array(
    		'post_type' => 'post' ,
  			'orderby' => 'date' ,
  			'order' => 'DESC' ,
  			'category__in'  => $categories_id, 
 			'posts_per_page' => $number,
		); 
		$cat_posts = new WP_query($args);
		if ($cat_posts->have_posts()) {
		 	while ($cat_posts->have_posts()) {
		 	   	$cat_posts->the_post(); 
		 	   		if ( $instance['no_thumbnail']  == true ) { 
		 	   			if ( has_post_thumbnail() ) {
		 	   	?>
					<div class="widget-psk">
    					<div class="widget-psk-wp">
    						<?php if ( $instance['thumbnail']  == true ) { ?>
    							<div class="psk-image"><?php the_post_thumbnail(); ?></div>
    						<?php } ?>
        						<div class="psk-title"><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></div>
        					<?php if ( $instance['content']  == true ) { ?>
        						<div class="psk-content"><?php echo wp_trim_words( get_the_content(), $number_words, '...' ); ?>...</div>
        					<?php } ?>
        					<?php	if ( $instance[ 'date' ]  == true ) { ?>
        						<div class="psk-date"><?php the_time('F j, Y'); ?></div>
        					<?php } ?>
    					</div>
    				</div>
        		<?php   
        				} 
					} else {
        		?>
        		    <div class="widget-psk">
    					<div class="widget-psk-wp">
    						<?php if ( $instance['thumbnail']  == true ) { ?>
    							<div class="psk-image"><?php the_post_thumbnail(); ?></div>
    						<?php } ?>
        						<div class="psk-title"><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></div>
        					<?php if ( $instance['content']  == true ) { ?>
        						<div class="psk-content"><?php echo wp_trim_words( get_the_content(), $number_words, '...' ); ?>...</div>
        					<?php } ?>
        					<?php	if ( $instance[ 'date' ]  == true ) { ?>
        						<div class="psk-date"><?php the_time('F j, Y'); ?></div>
        					<?php } ?>
    					</div>
    				</div>
	    <?php   
	    		}
	        }
	    }
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
		$number = ! empty( $instance['number'] ) ? $instance['number'] : 1;
		$number_words = ! empty( $instance['number_words'] ) ? $instance['number_words'] : 10;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
    		$categories = get_categories( array(
				'order'   => 'ASC',
				'hide_empty' => false
			) );
        ?>
		<p>
			<label for="categories">Select category:</label>
		</p>
		<p>
		<?php 			
			foreach( $categories as $category ) {
			$cat = $category->term_id;
		?>
	 		<input class="checkbox" type="checkbox" <?php checked( $instance[$cat], 'on' ); ?> id="<?php echo $this->get_field_id( $cat ); ?>" name="<?php echo $this->get_field_name( $cat ); ?>" /> <label for="<?php echo $this->get_field_id( $cat ); ?>"><?php echo $category->name; ?></label>
		<?php } ?>
    	</p>
    	<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php echo esc_html__( 'Number of posts to show:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<p>
    		<label for="<?php echo $this->get_field_id( 'thumbnail' ); ?>">Show The Thumbnail</label>
    		<input class="checkbox" type="checkbox" <?php checked( $instance['thumbnail'], 'on' ); ?> id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>" />
		</p>
		<p>
    		<label for="<?php echo $this->get_field_id( 'no_thumbnail' ); ?>">Exclude post which have no thumbnail</label>
    		<input class="checkbox" type="checkbox" <?php checked( $instance['no_thumbnail'], 'on' ); ?> id="<?php echo $this->get_field_id('no_thumbnail'); ?>" name="<?php echo $this->get_field_name( 'no_thumbnail' ); ?>" />
		</p>
    	<p>
    		<label for="<?php echo $this->get_field_id( 'date' ); ?>">Show The Date</label>
    		<input class="checkbox" type="checkbox" <?php checked( $instance['date'], 'on' ); ?> id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" />
		</p>
		 <p>
    		<label for="<?php echo $this->get_field_id( 'content' ); ?>">Show The Content</label>
    		<input class="checkbox" type="checkbox" <?php checked( $instance['content'], 'on' ); ?> id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" />
    	    <label for="<?php echo esc_attr( $this->get_field_id( 'number_words' ) ); ?>"><?php echo esc_html__( 'Number of words:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_words' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_words' ) ); ?>" type="text" value="<?php echo esc_attr( $number_words ); ?>">
		</p>
	  	<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['thumbnail'] = $new_instance['thumbnail'];
		$instance['no_thumbnail'] = $new_instance['no_thumbnail'];
        $instance['date'] = $new_instance['date'];
        $instance['content'] = $new_instance['content'];
        $instance['number'] = $new_instance['number'];
        $instance['number_words'] = $new_instance['number_words'];
    	$categories = get_categories( array(
			'order'   => 'ASC',
			'hide_empty' => false
		) );
		foreach( $categories as $category ) {
			$cat = $category->term_id;
    		$instance[$cat ] = $new_instance[$cat ];
		}
		return $instance;
	}
}


