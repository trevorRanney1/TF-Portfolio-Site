<?php
/**
 * @package Total
 */
add_action('widgets_init', 'total_register_personal_info');

function total_register_personal_info() {
    register_widget('total_personal_info');
}

class Total_Personal_Info extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'total_personal_info', 'Total - Personal Info', array(
                'description' => esc_html__('A widget to display Personal Information', 'total')
            )
        );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields() {
        $fields = array(
            'title' => array(
                'total_widgets_name' => 'title',
                'total_widgets_title' => esc_html__('Title', 'total'),
                'total_widgets_field_type' => 'text',
            ),
            'image' => array(
                'total_widgets_name' => 'image',
                'total_widgets_title' => esc_html__('Image', 'total'),
                'total_widgets_field_type' => 'upload',
            ),
            'intro' => array(
                'total_widgets_name' => 'intro',
                'total_widgets_title' => esc_html__('Short Intro', 'total'),
                'total_widgets_field_type' => 'textarea',
                'total_widgets_row' => '4'
            ),
            'signature' => array(
                'total_widgets_name' => 'name',
                'total_widgets_title' => esc_html__('Name', 'total'),
                'total_widgets_field_type' => 'text',
            )
        );

        return $fields;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        extract($args);

        $title = isset($instance['title']) ? $instance['title'] : '';
        $image = isset($instance['image']) ? $instance['image'] : '';
        $intro = isset($instance['intro']) ? $instance['intro'] : '';
        $name = isset($instance['name']) ? $instance['name'] : '';


        echo $before_widget;  // WPCS: XSS OK.
        ?>
        <div class="ht-personal-info">
            <?php
            if (!empty($title)):
                echo $before_title . esc_html($title) . $after_title;  // WPCS: XSS OK.
            endif;

            if (!empty($image)):
                $image_id = attachment_url_to_postid($image);
                if ($image_id) {
                    $image_array = wp_get_attachment_image_src($image_id, 'thumbnail');
                    $image_url = $image_array[0];
                } else {
                    $image_url = $image;
                }
                echo '<div class="ht-pi-image"><img src="' . esc_url($image_url) . '"/></div>';
            endif;

            if (!empty($name)):
                echo '<h5 class="ht-pi-name"><span>' . esc_html($name) . '</span></h5>';
            endif;

            if (!empty($intro)):
                echo '<div class="ht-pi-intro">' . wp_kses_post($intro) . '</div>';
            endif;
            ?>
        </div>
        <?php
        echo $after_widget;  // WPCS: XSS OK.
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param	array	$new_instance	Values just sent to be saved.
     * @param	array	$old_instance	Previously saved values from database.
     *
     * @uses	total_widgets_updated_field_value()		defined in widget-fields.php
     *
     * @return	array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ($widget_fields as $widget_field) {

            extract($widget_field);

            // Use helper function to get updated field values
            $instance[$total_widgets_name] = total_widgets_updated_field_value($widget_field, $new_instance[$total_widgets_name]);
        }

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param	array $instance Previously saved values from database.
     *
     * @uses	total_widgets_show_widget_field()		defined in widget-fields.php
     */
    public function form($instance) {
        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ($widget_fields as $widget_field) {

            // Make array elements available as variables
            extract($widget_field);
            $total_widgets_field_value = !empty($instance[$total_widgets_name]) ? esc_attr($instance[$total_widgets_name]) : '';
            total_widgets_show_widget_field($this, $widget_field, $total_widgets_field_value);
        }
    }

}
