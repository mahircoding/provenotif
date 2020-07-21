<?php
namespace ProveNotif\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor button widget.
 *
 * Elementor widget that displays a button with the ability to controll every
 * aspect of the button design.
 *
 * @since 0.1.0
 */
class ProveNotif_Widget_SocialProof extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve button widget name.
     *
     * @since 0.1.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'social-proof-popup';
    }

    /**
     * Get widget title.
     *
     * Retrieve button widget title.
     *
     * @since 0.1.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Social Proof Popup', 'provenotif' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve button widget icon.
     *
     * @since 0.1.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
    return 'eicon-product-rating';
    }

    public function get_categories() {
        return [ 'provenotif' ];
    }

    public function get_script_depends() {
        return [ 'provenotif' ];
    }
	
	  public function get_keywords() {
		return [ 'social proof', 'sales', 'notification', 'fomo', 'order' ];
	  }

    public function get_custom_help_url() {
        return 'https://my.provenotif.com/tutorial';
    }

    /**
     * Register button widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 0.1.0
     * @access protected
     */
    protected function _register_controls() {
    $this->start_controls_section(
      'section_content',
      [
        'label' => __( 'Content', 'provenotif' ),
      ]
    );
      
    $repeater = new Repeater();
      
    $repeater->add_control(
      'name',
      [
        'label' => __( 'Name', 'provenotif' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( '', 'provenotif' ),
        'label_block' => true,
        'dynamic' => [
          'active' => true,
        ],

      ]
    );
 
    $repeater->add_control(
      'desc',
      [
        'label' => __( 'Description', 'provenotif' ),
        'type' => Controls_Manager::TEXTAREA,
        'default' => __( '', 'provenotif' ),
        'rows' => 2,
        'label_block' => true,
        'dynamic' => [
          'active' => true,
        ],
      ]
    );
 
    $repeater->add_control(
      'text',
      [
        'label' => __( 'Text', 'provenotif' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( '', 'provenotif' ),
        'label_block' => true,
        'dynamic' => [
          'active' => true,
        ],
      ]
    );
      
    $repeater->add_control(
      'verified',
      [
        'label' => __( 'Verified', 'provenotif' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( '', 'provenotif' ),
        'label_block' => true,
        'dynamic' => [
          'active' => true,
        ],
      ]
    ); 
    
    $repeater->add_control(
      'image',
      [
        'label' => __( 'Image', 'provenotif' ),
        'type' => Controls_Manager::MEDIA,
        'dynamic' => [
          'active' => true,
            ],
      ]
    );
		
	 $repeater->add_control(
      'link',
      [
        'label' => __( 'Link', 'provenotif' ),
        'type' => Controls_Manager::URL,
        'placeholder' => __( 'https://your-link.com', 'provenotif' ),
        'dynamic' => [
          'active' => true,
        ],
      ]
    );

      
    $this->add_control(
            'notify',
            [
                'type'                 => Controls_Manager::REPEATER,
                'default'              => [
                    [
                        'name'    => __( 'Joko', 'provenotif' ),
                        'desc'    => __( 'Membeli Jam Tangan', 'provenotif' ),
                        'text'     => __( 'Jakarta', 'provenotif' ),
                        'verified'  => __( 'by ProveNotif', 'provenotif' ),
						            'link' => __( 'link produk', 'provenotif' ),
                    ],
                    [
                        'name'    => __( 'Putri', 'provenotif' ),
                        'desc'    => __( 'Membeli Gamis Muslimah', 'provenotif' ),
                        'text'     => __( '<span style="color: rgb(0, 102, 204); padding:0 8px 0.5px; margin-right:1px;  border-radius:0px; background-color: rgb(204, 224, 245);">Order Now</span>', 'provenotif' ),
                        'verified'  => __( 'by ProveNotif', 'provenotif' ),
                        'link' => __( 'link produk', 'provenotif' ),
                    ],
                ],
                'fields'            => array_values( $repeater->get_controls() ),
                'title_field' => '{{{name}}}',
            ]
        );

    $this->add_control(
            'style',
            [
                'label'                => __( 'Style', 'provenotif' ),
                'type'                 => Controls_Manager::SELECT,
                'options'              => [
                    'square'    => __( 'Square', 'provenotif' ),
                    'rounded'    => __( 'Rounded', 'provenotif' ),
					'sharp'    => __( 'Sharp', 'provenotif' ),
                ],
                'default' => 'square', 
                'separator'  => 'before',
            ]
        );
 
    $this->end_controls_section();

    $this->start_controls_section(
            'section_content_style',
            [
                'label'                 => __( 'Content', 'provenotif' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_content',
				'label' => esc_html__( 'Background', 'provenotif' ),
                'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .provenotif-style-square, {{WRAPPER}} .provenotif-style-rounded, {{WRAPPER}} .provenotif-style-sharp',
			]
		);
		
		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_content',
                'label' => esc_html__( 'Border', 'provenotif' ),
				'separator'  => 'before',
                'selector' => '{{WRAPPER}} .provenotif-style-square, {{WRAPPER}} .provenotif-style-rounded, {{WRAPPER}} .provenotif-style-sharp',
            ]
        );

    $this->end_controls_section();

    /**
         * Style Tab: Name
         */
        $this->start_controls_section(
            'section_notif_name_style',
            [
                'label'                 => __( 'Name', 'provenotif' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'notif_name_typography',
                'label'                 => __( 'Typography', 'provenotif' ),
                'selector'              => '{{WRAPPER}} .provenotif-name',
            ]
        );

        $this->add_control(
            'notif_name_text_color',
            [
                'label'                 => __( 'Text Color', 'provenotif' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .provenotif-name' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();

    /**
         * Style Tab: Product or Description
         */
        $this->start_controls_section(
            'section_notif_description_style',
            [
                'label'                 => __( 'Description', 'provenotif' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'notif_description_typography',
                'label'                 => __( 'Typography', 'provenotif' ),
                'selector'              => '{{WRAPPER}} .provenotif-desc',
            ]
        );

        $this->add_control(
            'notif_description_text_color',
            [
                'label'                 => __( 'Text Color', 'provenotif' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .provenotif-desc' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Style Tab: Times or Text
         */
        $this->start_controls_section(
            'section_notif_times_style',
            [
                'label'                 => __( 'Times', 'provenotif' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'notif_times_typography',
                'label'                 => __( 'Typography', 'provenotif' ),
                'selector'              => '{{WRAPPER}} .provenotif-times',
            ]
        );

        $this->add_control(
            'notif_times_text_color',
            [
                'label'                 => __( 'Text Color', 'provenotif' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .provenotif-times' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Style Tab: Verified by Brand
         */
        $this->start_controls_section(
            'section_notif_verified_style',
            [
                'label'                 => __( 'Verified', 'provenotif' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'notif_verified_typography',
                'label'                 => __( 'Typography', 'provenotif' ),
                'selector'              => '{{WRAPPER}} .provenotif-veri-square', '{{WRAPPER}} .provenotif-veri-rounded',
            ]
        );

        $this->add_control(
            'notif_verified_text_color',
            [
                'label'                 => __( 'Text Color', 'provenotif' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .provenotif-veri-square' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .provenotif-veri-rounded' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section(); 
	
		$this->start_controls_section(
			'section_image_style',
			[
				'label' => __( 'Image', 'provenotif' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'thumbnail',
			]
		);
		
		$this->end_controls_section();

  }

  
 
  /**
   * Render the widget output on the frontend.
   *
   * Written in PHP and used to generate the final HTML.
   *
   * @since 1.1.0
   *
   * @access protected
   */       
	
	private function render_image( $item, $instance ) {
    $image_id = $item['image']['id'];
    $image_size = $instance['image_size_size'];
    if ( 'custom' === $image_size ) {
      $image_src = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size', $instance );
    } else {
      $image_src = wp_get_attachment_image_src( $image_id, $image_size );
      $image_src = $image_src[0];
    }

    return sprintf( '<img src="%s" />', $image_src, $item['image'] );
  }
	
	private function render_item_header( $item ) {
		$url = $item['link']['url'];

		$item_id = $item['_id'];

		if ( $url ) {
			$unique_link_id = 'item-link-' . $item_id;

			$this->add_render_attribute( $unique_link_id, [
				'href' => $url,
				'class' => 'provenotif-wrap',
			] );

			if ( $item['link']['is_external'] ) {
				$this->add_render_attribute( $unique_link_id, 'target', '_blank' );
			}

			return '<div><a ' . $this->get_render_attribute_string( $unique_link_id ) . '>';
		} else {
			return '<div class="provenotif-wrap">';
		}
	}
	
	private function render_item_footer( $item ) {
		if ( $item['link']['url'] ) {
			return '</a></div>';
		} else {
			return '</div>';
		}
	}
	
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        
        <?php
   
        if($settings['style']=='square'){
            $this->square_render($settings);
        } elseif($settings['style']=='rounded') {
            $this->rounded_render($settings);
        } else {
            $this->sharp_render($settings);
  
              }
        ?>

        
        <?php
    }   

    
  	protected function square_render($settings) {
		$settings = $this->get_settings(); ?>

		<div id="provenotif-animation">

		<?php foreach ( $settings['notify'] as $item ) : ?>
			<section class="provenotif-wrap">
				<?php echo $this->render_item_header( $item ); ?>
				<div class="provenotif-style-square">
					<?php if ( ! empty( $item['image']['url'] ) ) : ?>
					<div class="SquareProvenotifPreviewPicture">
						<?php echo $this->render_image( $item, $settings ); ?>
					</div>
					<?php endif; ?>
				<div class="provenotif-content-wrapper">
				<?php if ( ! empty( $item['name'] ) ) : ?>
					<span class="provenotif-name"><?php echo $item['name']; ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $item['desc'] ) ) : ?>
					<span class="provenotif-desc"><?php echo $item['desc']; ?></span>
				<?php endif; ?>
				<div class="provenotif-loc">
				<?php if ( ! empty( $item['text'] ) ) : ?>
					<span class="provenotif-times"><?php echo $item['text']; ?></span>
				<?php endif; ?>
					<span class="ProvenotifPreviewVerified">
				<?php if ( ! empty( $item['verified'] ) ) : ?>
					<span class="provenotif-veri-square"><span class="provenotif-circle"></span><?php echo $item['verified']; ?></span>
				<?php endif; ?>
					</span>
				</div>
				</div>
				</div>
				<?php echo $this->render_item_footer( $item ); ?>
			</section>
		<?php endforeach; ?>
		</div>
				
	<?php 
	
  }
	
  
  protected function rounded_render($settings) {
		$settings = $this->get_settings(); ?>

		<div id="provenotif-animation">

		<?php foreach ( $settings['notify'] as $item ) : ?>
			<section class="provenotif-wrap">
				<?php echo $this->render_item_header( $item ); ?>
				<div class="provenotif-style-rounded">
					<?php if ( ! empty( $item['image']['url'] ) ) : ?>
					<div class="RoundedProvenotifPreviewPicture">
						<?php echo $this->render_image( $item, $settings ); ?>
					</div>
					<?php endif; ?>
				<div class="provenotif-content-wrapper">
				<?php if ( ! empty( $item['name'] ) ) : ?>
					<span class="provenotif-name"><?php echo $item['name']; ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $item['desc'] ) ) : ?>
					<span class="provenotif-desc"><?php echo $item['desc']; ?></span>
				<?php endif; ?>
				<div class="provenotif-loc">
				<?php if ( ! empty( $item['text'] ) ) : ?>
					<span class="provenotif-times"><?php echo $item['text']; ?></span>
				<?php endif; ?>
					<span class="ProvenotifPreviewVerified">
					<?php if ( ! empty( $item['verified'] ) ) : ?>
          <span class="provenotif-veri-rounded"><span class="provenotif-circle"></span><?php echo $item['verified']; ?></span>
        <?php endif; ?>
					</span>
				</div>
				</div>
				</div>
				<?php echo $this->render_item_footer( $item ); ?>
			</section>
		<?php endforeach; ?>
		</div>
				
	<?php 
  
  }
	
   protected function sharp_render($settings) {
		$settings = $this->get_settings(); ?>

		<div id="provenotif-animation">

		<?php foreach ( $settings['notify'] as $item ) : ?>
			<section class="provenotif-wrap">
				<?php echo $this->render_item_header( $item ); ?>
				<div class="provenotif-style-sharp">
					<?php if ( ! empty( $item['image']['url'] ) ) : ?>
					<div class="SquareProvenotifPreviewPicture">
						<?php echo $this->render_image( $item, $settings ); ?>
					</div>
					<?php endif; ?>
				<div class="provenotif-content-wrapper">
				<?php if ( ! empty( $item['name'] ) ) : ?>
					<span class="provenotif-name"><?php echo $item['name']; ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $item['desc'] ) ) : ?>
					<span class="provenotif-desc"><?php echo $item['desc']; ?></span>
				<?php endif; ?>
				<div class="provenotif-loc">
				<?php if ( ! empty( $item['text'] ) ) : ?>
					<span class="provenotif-times"><?php echo $item['text']; ?></span>
				<?php endif; ?>
					<span class="ProvenotifPreviewVerified">
				<?php if ( ! empty( $item['verified'] ) ) : ?>
          <span class="provenotif-veri-square"><span class="provenotif-circle"></span><?php echo $item['verified']; ?></span>
        <?php endif; ?>
					</span>
				</div>
				</div>
				</div>
				<?php echo $this->render_item_footer( $item ); ?>
			</section>
		<?php endforeach; ?>
		</div>
				
	<?php 
  
  }

 
  /**
   * Render the widget output in the editor.
   *
   * Written as a Backbone JavaScript template and used to generate the live preview.
   *
   * @since 1.1.0
   *
   * @access protected
   */
  protected function _content_template() {
    
  }
}
