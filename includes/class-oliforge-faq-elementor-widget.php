<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OliForge_FAQ_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'oliforge_faq';
	}

	public function get_title() {
		return __( 'OliForge FAQ', 'oliforge-faq' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_categories() {
		return array( 'oliforge', 'general' );
	}

	public function get_style_depends() {
		return array( 'oliforge-faq' );
	}

	private function faq_options() {
		$options = array( '' => __( '— Select FAQ —', 'oliforge-faq' ) );
		$posts   = get_posts(
			array(
				'post_type'      => OliForge_FAQ_Plugin::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		foreach ( $posts as $p ) {
			$options[ $p->ID ] = $p->post_title ? $p->post_title : '#' . $p->ID;
		}
		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => __( 'FAQ', 'oliforge-faq' ) ) );

		$this->add_control(
			'faq_id',
			array(
				'label'   => __( 'FAQ', 'oliforge-faq' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->faq_options(),
				'default' => '',
			)
		);
		$this->add_control(
			'open',
			array(
				'label'   => __( 'Initially open', 'oliforge-faq' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'none'  => __( 'None', 'oliforge-faq' ),
					'first' => __( 'First item', 'oliforge-faq' ),
					'all'   => __( 'All items', 'oliforge-faq' ),
				),
				'default' => 'none',
			)
		);
		$this->add_control(
			'schema',
			array(
				'label'        => __( 'Output FAQPage schema', 'oliforge-faq' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => __( 'Style', 'oliforge-faq' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'question_typography',
				'label'    => __( 'Question typography', 'oliforge-faq' ),
				'selector' => '{{WRAPPER}} .oliforge-faq__question',
			)
		);
		$this->add_control(
			'question_color',
			array(
				'label'     => __( 'Question color', 'oliforge-faq' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .oliforge-faq__question' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'answer_color',
			array(
				'label'     => __( 'Answer color', 'oliforge-faq' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .oliforge-faq__answer' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'border_color',
			array(
				'label'     => __( 'Border color', 'oliforge-faq' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .oliforge-faq__item' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		if ( empty( $s['faq_id'] ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Select an FAQ in the widget settings.', 'oliforge-faq' ) . '</p>';
			}
			return;
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in render().
		echo OliForge_FAQ_Plugin::render( (int) $s['faq_id'], $s['open'], 'yes' === $s['schema'] );
	}
}
