<?php
namespace Tenweb_Builder\DynamicTags\Tags;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Tenweb_Builder\DynamicTags\Module;

class Archive_Title extends Tag {

  public function get_name() {
    return 'tenweb-tag-archive-title';
  }

  public function get_title() {
    return __( 'Archive Title/Description', 'tenweb-builder' );
  }

  public function get_group() {
    return Module::TENWEB_GROUP;
  }

  public function get_categories() {
    return [ Module::TEXT_CATEGORY ];
  }

  public function render() {
    $show_description = 'description' === $this->get_settings('show_description');
    if ( $show_description ) {
      $title = get_the_archive_description();
    }
    else {
      $include_context = 'yes' === $this->get_settings('include_context');
      $title = $this->get_the_archive_title($include_context);
    }
    if ( !$title ) {
      $title = __( 'Archive description', 'tenweb-builder' );
    }
    echo wp_kses_post( $title );
  }

  public function get_the_archive_title( $include_context = true ) {
    if ( is_search() ) {
      $title = sprintf( __( 'Search Results for: %s', 'tenweb-builder' ), get_search_query() );
    } elseif ( is_category() ) {
      $title = single_cat_title( '', false );

      if ( $include_context ) {
        $title = sprintf( __( 'Category: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_tag() ) {
      $title = single_tag_title( '', false );
      if ( $include_context ) {
        $title = sprintf( __( 'Tag: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_author() ) {
      $title = '<span class="vcard">' . get_the_author() . '</span>';

      if ( $include_context ) {
        $title = sprintf( __( 'Author: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_year() ) {
      $title = get_the_date( _x( 'Y', 'yearly archives date format', 'tenweb-builder' ) );

      if ( $include_context ) {
        $title = sprintf( __( 'Year: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_month() ) {
      $title = get_the_date( _x( 'F Y', 'monthly archives date format', 'tenweb-builder' ) );

      if ( $include_context ) {
        $title = sprintf( __( 'Month: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_day() ) {
      $title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'tenweb-builder' ) );

      if ( $include_context ) {
        $title = sprintf( __( 'Day: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_tax( 'post_format' ) ) {
      if ( is_tax( 'post_format', 'post-format-aside' ) ) {
        $title = _x( 'Asides', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
        $title = _x( 'Galleries', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
        $title = _x( 'Images', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
        $title = _x( 'Videos', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
        $title = _x( 'Quotes', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
        $title = _x( 'Links', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
        $title = _x( 'Statuses', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
        $title = _x( 'Audio', 'post format archive title', 'tenweb-builder' );
      } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
        $title = _x( 'Chats', 'post format archive title', 'tenweb-builder' );
      }
    } elseif ( is_post_type_archive() ) {
      $title = post_type_archive_title( '', false );

      if ( $include_context ) {
        $title = sprintf( __( 'Archives: %s', 'tenweb-builder' ), $title );
      }
    } elseif ( is_tax() ) {
      $title = single_term_title( '', false );

      if ( $include_context ) {
        $tax = get_taxonomy( get_queried_object()->taxonomy );
        $title = sprintf( __( '%1$s: %2$s', 'tenweb-builder' ), $tax->labels->singular_name, $title );
      }
    } else {
      $title = __( 'Archives', 'tenweb-builder' );
    }

    return $title;
  }

  protected function register_controls() {
    $this->add_control(
      'include_context',
      [
        'label'   => __( 'Include Context', 'tenweb-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => 'no',
      ]
    );
    $this->add_control(
      'show_description',
      [
        'label'   => __( 'Show', 'tenweb-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'title',
        'options' => [
          'title' => __( 'Title', 'tenweb-builder' ),
          'description' => __( 'Description', 'tenweb-builder' ),
        ],
      ]
    );
  }
}
