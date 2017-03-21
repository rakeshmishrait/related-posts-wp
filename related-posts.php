<?php
/*
Plugin Name: Related Post
Description: Related posts are shown on listing.
Author: Rakesh Mishra
Version: 1.0
Author URI: http://rakesh-mishra.com
*/

class Related_Posts extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'related_posts_widget', // Base ID
            esc_html__( 'Related Posts', 'related_posts' ), // Name
            array( 'description' => esc_html__( 'A Related Posts Widget', 'related_posts' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        if(is_singular('post'))
        {
            $post_id = get_the_ID();
            if(!empty($post_id))
            {
                $categories = get_the_category($post_id);

                if(!empty($categories))
                {
                    foreach($categories as $key=>$val){
                        $term[] = $val->term_id;
                    }

                    $args = array(
                                    'post_status' =>'publish',
                                    'post_type' =>'post',
                                    'category__in' => $term,
                                  );

                    $the_query = new WP_Query( $args );

                    if ( $the_query->have_posts() ) {
                        while ( $the_query->have_posts() ) {
                            $the_query->the_post();
                            ?>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> <br/>
                            <?php
                        }
                    }
                    // Reset Post Data
                    wp_reset_postdata();

                }

            }

        }



        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'related_posts' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'related_posts' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

} // class Related_Posts


function register_related_posts_widget() {
    register_widget( 'Related_Posts' );
}
add_action( 'widgets_init', 'register_related_posts_widget' );