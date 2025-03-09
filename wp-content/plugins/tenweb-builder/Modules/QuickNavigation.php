<?php
namespace Tenweb_Builder\Modules;

use Tenweb_Builder\Templates;

class QuickNavigation {

  public $menu_ids = array();

  /**
   * Check if there is condition front_page in singular_conditions.
   *
   * @param array $conditions
   *
   * @return bool
   */
  public function is_frontend_condition( $conditions = array() ) {
    foreach ( $conditions as $condition ) {
      if ( !empty($condition) ) {
        foreach ( $condition as $cond ) {
          if ( $cond['post_type'] === 'front_page' && $cond['condition_type'] === 'include' ) {
            return true;
          }
        }
      }
    }
    return false;
  }

  /**
   * Check if the page has this template.
   *
   * @param int $template_id
   * @param int $id
   *
   * @return bool
   */
  public function check_template( $template_id, $id, $type ) {
    $template_type = get_post_meta( $template_id, '_elementor_template_type', true );
    $post_type = get_post_type($id);
    $page_for_posts = get_option('page_for_posts');
    if ($template_type === "twbb_archive") {
      if ( $page_for_posts === $id ) {
        return TRUE;
      }
    } else {
      $data = get_option('twbb_general_conditions');
      if( is_array($data) && array_key_exists($template_id, $data) && !empty($data[$template_id]) && $page_for_posts !== $id && $data[$template_id][0]['condition_type'] === 'include' && $data[$template_id][0]['page_type'] === 'general') {
        return TRUE;
      }

      $data = get_option('twbb_singular_conditions');
      if( is_array($data) ) {
        $is_frontend_condition = $this->is_frontend_condition($data);
      }
      $is_front_page = ( get_option( 'page_on_front' ) === $id ) ? true : false;
      if ( isset($data[$template_id]) && is_array($data[$template_id]) && $page_for_posts !== $id) {
        foreach ( $data[$template_id] as $key => $value ) {
          /*
          1.Specific posts/pages
          2.All Singular
          3.All Posts/Pages
          4.Front page
          */
          if ( $value['condition_type'] === 'include' && (( in_array(strval($id), $value["specific_pages"], true) && $value['post_type'] === $post_type) ||
              ($value['page_type'] === 'singular' && $value['post_type'] === 'all' && (!$is_front_page || !$is_frontend_condition)) ||
              ($value['page_type'] === 'singular' && $value['post_type'] === $post_type && $value['filter_type'] === 'all' && (!$is_front_page || !$is_frontend_condition)) ||
              ($value['page_type'] === 'singular' && $value['post_type'] === 'front_page' && get_option( 'page_on_front' ) === $id))) {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  /**
   * Check if current page is template.
   *
   * @return bool
   */
  public function is_template() {
    if ( get_post_type(get_the_ID()) === 'elementor_library' ) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check if front page is in menu.
   *
   * @return array, bool
   */
  public function is_frontpage_in_menu() {
    $frontpage_id =  get_option( 'page_on_front' );
    $theme_locations = get_nav_menu_locations();
    $menu_name = "";
    if ( isset($theme_locations['header_menu']) ) {
      $menu_obj = get_term($theme_locations['header_menu'], 'nav_menu');
      $menu_name = $menu_obj->name;
    }
    $menu_items = wp_get_nav_menu_items( $menu_name );
    foreach ($menu_items as $menu_item) {
      if( $menu_item->object_id == $frontpage_id || $frontpage_id == '0') {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
        return true;
      }
    }

    $frontPage = get_post($frontpage_id);
    return array(
              'object_id' =>  $frontPage->ID,
              'type'      => 'post_type',
              'title'     =>  $frontPage->post_title,
              'children'  =>  array(),
            );
  }

  /**
   * Output the menu html with hierarchical.
   *
   * @param array $TreeArray
   * @param int $first
   *
   */
  public function outputMenuHtml( $TreeArray, $first = 0 ) {
    echo $first ? '<ul class="sub-menu">':'';
    foreach( $TreeArray as $arr ) {
      $this->menu_ids[] = $arr->object_id;
      $link = "#";
      $pen_icon = "";
      $editable_page = false;
      $target = "";
      $class = 'class="twbb_cursor_default"';
      $current = "";
      $has_templ = '';
      if( $arr->type === 'post_type' ) {
        $editable_page = true;
        $link = admin_url('post.php?post='.$arr->object_id.'&action=elementor');

        if ( $this->check_template(get_the_ID(), $arr->object_id, 'page') ) {
            $has_templ = '<span class="tooltip-container"><i class="twbb-check twbb-widget-icon tooltip-icon active"></i><span class="tooltip-text">'.__('The template you are editing is applied to the following page', 'tenweb-builder').'</span></span>';
            $pen_icon = '<i class="twbb-edit twbb-widget-icon has_templ"></i>';
        } else {
            $pen_icon = '<i class="twbb-edit twbb-widget-icon"></i>';
        }


        $target = 'target="_blank"';
        $class = "";
        if( isset($_GET['elementor-preview']) && intval($_GET['elementor-preview']) == $arr->object_id ) {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison, WordPress.Security.NonceVerification.Recommended
            $current = '<span class="current">'. __("Current", "tenweb-builder") .'</span>';
            $pen_icon = '';
        }
      }

      echo '<li>';
      $title = $arr->title ? $arr->title : __("No title", "tenweb-builder");
      if(is_array($arr->children)) {
        echo wp_kses_post('<a href="'.$link.'" '.$target.' '.$class.'>'.$title.$current.$has_templ.$pen_icon.'</a>');
        $this->outputMenuHtml( $arr->children, 1 );
      } else {
        echo wp_kses_post('<a href="'.$link.'" '.$target.' '.$class.'>'.$title.$current.$has_templ.$pen_icon.'</a>');
      }
      echo '</li>';
    }
    echo $first ? '</ul>' : '';
  }


  /**
   * Create new array with tree logic.
   *
   * @param array $elements
   * @param int $parentId
   *
   * @return array
   */
  function buildMenuTree(array $elements, $parentId = 0) {
    $branch = array();

    foreach ($elements as $element) {
      if ( intval($element->menu_item_parent) === intval($parentId) ) {
        if ( is_int($element->ID) ) {
          $children = $this->buildMenuTree($elements, $element->ID);
          if ( $children ) {
            $element->children = $children;
          }
          $branch[] = $element;
        }
      }
    }
    return $branch;
  }

  /**
   * Check if template has 404 Page condition.
   *
   * @param int $template_id
   *
   * @return bool
   */
  public function check_404_template( $template_id ) {
    $singular_conditions = get_option('twbb_singular_conditions');
    if( isset($singular_conditions[$template_id]) ) {
      foreach ( $singular_conditions[$template_id] as $value ) {
        if ( $value['post_type'] === "not_found" ) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Custom header row with menues.
   */
  public function twbb_custom_header($dashboardInfo) {
    ?>
    <div id="twbb_custom_header" style="display: none">
      <div class="website_structure">
        <ul class="twbb_nav">
          <li>
            <a href="#" id="twbb_website_structure"><?php esc_html_e("Website Structure", "tenweb-builder"); ?><span class="twbb-arrow-down twbb-widget-icon"></span></a>
            <div class="twbb_sub_menu_cont">
              <i class="fa fa-caret-up"></i>
              <a class="twbb_add_page twbb_add_page_inmenu" href="<?php echo esc_url(add_query_arg(array('add_page' => 1), $dashboardInfo['dashboard_url'])); ?>" target="_blank">
                <?php esc_html_e("Add an Additional Page", "tenweb-builder"); ?>
              </a>
            <ul class="twbb_sub_menu">
              <?php
              $theme_locations = get_nav_menu_locations();
              $menu_name = "";
              $elements = [];
              if ( isset($theme_locations['header_menu']) ) {
                $menu_obj = get_term($theme_locations['header_menu'], 'nav_menu');
                $menu_name = isset($menu_obj->name) ? $menu_obj->name : '';
                $elements = wp_get_nav_menu_items( $menu_name );
              }

              ?>
                <li class="title site_menu">
                    <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) ); ?>/assets/images/site_menu.svg">
                    <label><?php esc_html_e("Site Menu", "tenweb-builder"); ?></label>
                    <span class="twbb-arrow-up twbb-widget-icon"></span>
                  <ul class="title_container opened">
                    <?php if ( gettype ( $elements ) === 'array' && !empty($elements) ) {

                        $nav_menu = $this->buildMenuTree($elements);
                        if( is_array($this->is_frontpage_in_menu()) ) {
                          array_unshift($nav_menu, (object) $this->is_frontpage_in_menu());
                        }
                        $this->outputMenuHtml($nav_menu);
                    }
                    else { ?>
                        <p class="nothing_published"><?php esc_html_e("Nothing published in this section", "tenweb-builder"); ?></p>
                    <?php } ?>
                  </ul>
                </li>
                <hr>
              <?php
              $pages = 21;
              $all_pages =  get_pages(
                  array(
                      "exclude" => $this->menu_ids,//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                      "number" => $pages,
                      "post_type" => "page",
                      "sort_column" => "post_modified",
                      "sort_order" => "DESC",
                      "post_status" => "publish"
                  )
              );
              ?>
                <li class="title inner_pages"><img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMTcuMDAxIiBoZWlnaHQ9IjE4IiB2aWV3Qm94PSIwIDAgMTcuMDAxIDE4Ij48ZGVmcz48c3R5bGU+LmF7ZmlsbDpyZ2JhKDE2NCwxNzUsMTgzLDAuMTQpO30uYntmaWxsOm5vbmU7fS5je2ZpbGw6dXJsKCNhKTt9LmR7ZmlsbDojZmZmO308L3N0eWxlPjxsaW5lYXJHcmFkaWVudCBpZD0iYSIgeDE9Ii0wLjEwMyIgeTE9IjEuMjE3IiB4Mj0iMC43NjIiIHkyPSIwLjI1MiIgZ3JhZGllbnRVbml0cz0ib2JqZWN0Qm91bmRpbmdCb3giPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzIxNjBiNSIgc3RvcC1vcGFjaXR5PSIwLjU4OCIvPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzIxNjBiNSIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKC01MiAtNzAzLjUpIj48cGF0aCBjbGFzcz0iYSIgZD0iTTcuMTE3LDE2SDEuMjczQTEuMjc0LDEuMjc0LDAsMCwxLDAsMTQuNzI4VjEuMjczQTEuMjcyLDEuMjcyLDAsMCwxLDEuMjczLDBIMTIuNjg5YTEuMjcyLDEuMjcyLDAsMCwxLDEuMjczLDEuMjczVjE0LjcyOEExLjI3NCwxLjI3NCwwLDAsMSwxMi42ODksMTZaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg1NS4wNCA3MDMuNSkiLz48cmVjdCBjbGFzcz0iYiIgd2lkdGg9IjE2LjgiIGhlaWdodD0iMTYuOCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNTIgNzA0LjcpIi8+PHBhdGggY2xhc3M9ImMiIGQ9Ik03LjExNywxNkgxLjI3M0ExLjI3NCwxLjI3NCwwLDAsMSwwLDE0LjcyN1YxLjI3M0ExLjI3MSwxLjI3MSwwLDAsMSwxLjI3MywwSDEyLjY4OGExLjI3MSwxLjI3MSwwLDAsMSwxLjI3MywxLjI3M1YxNC43MjdBMS4yNzQsMS4yNzQsMCwwLDEsMTIuNjg4LDE2WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNTMuNDQgNzA1LjEpIi8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNTUuNjggNzA3LjU4KSI+PHBhdGggY2xhc3M9ImQiIGQ9Ik04LjgsNC4wNTlILjcwNmEuNzA2LjcwNiwwLDAsMCwwLDEuNDEySDguOGEuNzA2LjcwNiwwLDAsMCwwLTEuNDEyWm0wLDAiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgLTQuMDU5KSIvPjxwYXRoIGNsYXNzPSJkIiBkPSJNOC44LDQuMDU5SC43MDZhLjcwNi43MDYsMCwwLDAsMCwxLjQxMkg4LjhhLjcwNi43MDYsMCwwLDAsMC0xLjQxMlptMCwwIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIC0wLjg1OSkiLz48cGF0aCBjbGFzcz0iZCIgZD0iTTguOCw0LjA1OUguNzA2YS43MDYuNzA2LDAsMCwwLDAsMS40MTJIOC44YS43MDYuNzA2LDAsMCwwLDAtMS40MTJabTAsMCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAyLjM0MSkiLz48cGF0aCBjbGFzcz0iZCIgZD0iTTguOCw0LjA1OUguNzA2YS43MDYuNzA2LDAsMCwwLDAsMS40MTJIOC44YS43MDYuNzA2LDAsMCwwLDAtMS40MTJabTAsMCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCA1LjU0MSkiLz48L2c+PC9nPjwvc3ZnPg==<?php //echo plugin_dir_url( __DIR__ ) . /assets/images/inner_pages.svg ?>"><label><?php esc_html_e("Inner Pages", "tenweb-builder"); ?></label><span class="twbb-arrow-down twbb-widget-icon"></span>
                  <ul class="title_container closed">
                    <?php
                    if( !empty( $all_pages ) ) {
                        $page_count = 0;
                        foreach ( $all_pages as $all_page ) {
                          $has_templ = '';
                          if ( $this->is_template() && $this->check_template(get_the_ID(), $all_page->ID, 'page') ) {
                            $has_templ = '<span class="tooltip-container"><i class="twbb-check twbb-widget-icon tooltip-icon active"></i><span class="tooltip-text">'.__('The template you are editing is applied to the following page', 'tenweb-builder').'</span></span>';
                          }
                          $current = '';
                          //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.PHP.StrictComparisons.LooseComparison
                          if ( isset($_GET['elementor-preview']) && intval($_GET['elementor-preview']) == $all_page->ID ) {
                            $current = '<span class="current">' . __("Current", "tenweb-builder") . '</span>';
                          }
                          $title = $all_page->post_title ? $all_page->post_title : __("No title", "tenweb-builder");
                          $link = admin_url('post.php?post='.$all_page->ID.'&action=elementor'); ?>
                          <li>
                            <a href="<?php echo esc_url( $link ); ?>" target="_blank">
                              <?php echo esc_html( $title.$current.$has_templ ); ?>
                              <?php if ($current === '') { ?>
                                <i class="twbb-edit twbb-widget-icon <?php echo $has_templ !== '' ? 'has_templ' : '' ?>"></i>
                              <?php } ?>
                            </a>
                          </li>
                        <?php
                          $page_count++;
                          if( $page_count >= $pages-1 ) {
                            $link_pages = admin_url('edit.php?post_type=page');
                            echo "<a href='".esc_url( $link_pages )."' class='view_more' target='_blank'>".esc_html__("View More", "tenweb-builder")."<i class='fas fa-chevron-right'></i></a>";
                            break;
                          }
                        }
                    }
                    else { ?>
                        <p class="nothing_published"><?php esc_html_e("Nothing published in this section", "tenweb-builder"); ?></p>
                    <?php } ?>
                  </ul>
                </li>
                <hr>
              <?php
              $templates = \Tenweb_Builder\Modules\TenwebRestApi::getTemplates();
              $loaded_templates = Templates::get_instance()->get_loaded_templates();
              $template_is_404 = false;
              ?>
              <li class="title other"><img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMTgiIGhlaWdodD0iMTgiIHZpZXdCb3g9IjAgMCAxOCAxOCI+PGRlZnM+PHN0eWxlPi5he2ZpbGw6dXJsKCNhKTt9PC9zdHlsZT48bGluZWFyR3JhZGllbnQgaWQ9ImEiIHgxPSItMC4xMDMiIHkxPSIxLjIxNyIgeDI9IjAuNzYyIiB5Mj0iMC4yNTIiIGdyYWRpZW50VW5pdHM9Im9iamVjdEJvdW5kaW5nQm94Ij48c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiMyMTYwYjUiIHN0b3Atb3BhY2l0eT0iMC41ODgiLz48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMyMTYwYjUiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cGF0aCBjbGFzcz0iYSIgZD0iTTEzLjAwOCwxNy4yNDNWMTQuNjcyYS4xNTEuMTUxLDAsMCwwLS4xNTEtLjE1MUgxMC4yODVhLjc1Ni43NTYsMCwwLDEsMC0xLjUxMmgyLjU3MWEuMTUyLjE1MiwwLDAsMCwuMTUxLS4xNTJWMTAuMjg1YS43NTYuNzU2LDAsMCwxLDEuNTEzLDB2Mi41NzFhLjE1Mi4xNTIsMCwwLDAsLjE1MS4xNTJoMi41NzFhLjc1Ni43NTYsMCwxLDEsMCwxLjUxMkgxNC42NzJhLjE1MS4xNTEsMCwwLDAtLjE1MS4xNTF2Mi41NzFhLjc1Ni43NTYsMCwxLDEtMS41MTMsMFpNMS41LDE4QTEuNSwxLjUsMCwwLDEsMCwxNi41VjExLjAzYTEuNSwxLjUsMCwwLDEsMS41LTEuNUg3LjFhMS41LDEuNSwwLDAsMSwxLjUsMS41VjE2LjVBMS41LDEuNSwwLDAsMSw3LjEsMThabTkuMzE4LTkuNTNhMS41LDEuNSwwLDAsMS0xLjUtMS41VjEuNWExLjUsMS41LDAsMCwxLDEuNS0xLjVoNS42YTEuNSwxLjUsMCwwLDEsMS41LDEuNVY2Ljk3YTEuNSwxLjUsMCwwLDEtMS41LDEuNVpNMS41LDguNDdBMS41LDEuNSwwLDAsMSwwLDYuOTdWMS41QTEuNSwxLjUsMCwwLDEsMS41LDBINy4xQTEuNSwxLjUsMCwwLDEsOC42LDEuNVY2Ljk3YTEuNSwxLjUsMCwwLDEtMS41LDEuNVoiLz48L3N2Zz4=<?php //echo plugin_dir_url( __DIR__ ) . /assets/images/other.svg ?>"><label><?php esc_html_e("Other", "tenweb-builder"); ?></label><span class="twbb-arrow-down twbb-widget-icon"></span>
                <ul class="title_container closed">
                <?php
                if( !empty($templates['twbb_single']) || !empty($templates['twbb_archive']) || !empty($templates['twbb_slide']) ) {
                    foreach ( $templates as $key => $values ) {
                      /* don't print header and footer templates */
                      if( $key === 'twbb_header' || $key === 'twbb_footer' ) {
                        continue;
                      }

                      if(!empty($values)) {
                        $title = ucfirst(str_replace("twbb_", "", $key));
                        echo "<span class='template_title'>" . esc_html($title) . "</span>";
                      }

                      foreach ( $values as $template) {
                        $current = '';
                        if( (get_post_type( get_the_ID() ) === 'elementor_library' ? 1 : 0) ) {
                          foreach ( $loaded_templates as $key_l => $value_l ) {
                            if ( $template->ID === $value_l ) {
                              $current = '<span class="current">' . esc_html__("Current", "tenweb-builder") . '</span>';
                            }
                          }
                        }
                        if ( $this->check_404_template($template->ID) ) {
                          $template_is_404 = true;
                          $link404 = admin_url('post.php?post='.$template->ID.'&action=elementor');
                          $title404 = $template->post_title.$current;
                        } else {
                            $link = admin_url('post.php?post=' . $template->ID . '&action=elementor'); ?>
                            <li><a href="<?php echo esc_url($link); ?>" target="_blank"><?php echo esc_html($template->post_title . $current); ?>
                                <i class="twbb-edit twbb-widget-icon"></i></a></li>
                            <?php
                        }
                      }
                    }
                    if( $template_is_404 ) {
                      ?>
                      <span class='template_title'>404 PAGE</span>
                      <li><a href="<?php echo esc_url($link404); ?>" target="_blank"><?php echo esc_html($title404); ?><i class="twbb-edit twbb-widget-icon"></i></a></li>
                      <?php
                    }
                }
                else { ?>
                    <p class="nothing_published"><?php esc_html_e("Nothing published in this section", "tenweb-builder"); ?></p>
                <?php } ?>
                </ul>
              </li>
            </ul>
            </div>
          </li>
        </ul>
      </div>

      <div class="twbb_finder">
        <a id="display_finder"><span class="twbb-search twbb-widget-icon"></span></a>
        <label><?php esc_html_e("Finder", "tenweb-builder"); ?></label>
      </div>
      <a class="twbb_add_page" href="<?php echo esc_url(add_query_arg(array('add_page' => 1), $dashboardInfo['dashboard_url'])); ?>" target="_blank">
        <?php esc_html_e("Add Page", "tenweb-builder"); ?>
      </a>
      <div class="twbb_custom_header_menu">
        <ul class="twbb_nav">
          <li class="header_footer"><a href="#"><?php esc_html_e("Header & Footer", "tenweb-builder"); ?><span class="twbb-arrow-down twbb-widget-icon"></span></a>
            <ul class="twbb_sub_menu">
              <i class="fa fa-caret-up"></i>
              <li data-template="twbb_header" class="twbb_header_menu"><a href="#" class="twbb_nav_header_menu"><?php esc_html_e("Header Template", "tenweb-builder"); ?></a></li>
              <li data-template="twbb_footer"><a href="#" class="twbb_nav_footer_menu"><?php esc_html_e("Footer Template", "tenweb-builder"); ?></a></li>
            </ul>
          </li>
          <li class="advanced">
              <a id="display_condition_popup" >
                  <?php esc_html_e('Display Conditions', 'tenweb-builder') ?>
              </a>
          </li>
        </ul>
      </div>
			<?php
      $header_class = ( $dashboardInfo['ai_created'] && $dashboardInfo['if_trial_user'] ) ? 'ai_created_page' : ( $dashboardInfo['if_trial_user'] ? 'trial_page' : 'hide_all' );
			?>
			<div class="twbb_upgrade_for_trial_users <?php echo esc_attr($header_class); ?>">
				<a href="<?php echo esc_url(TENWEB_DASHBOARD . '/websites?oneMonthFree=1'); ?>" target="_blank"><?php esc_html_e("Try 10Web for free", "tenweb-builder"); ?></a>
			</div>
    <?php if ( !TENWEB_WHITE_LABEL ){ ?>
      <div class="twbb_dashboard"><a href="<?php echo esc_url(add_query_arg(array('from_twbb_editor' => ''), $dashboardInfo['dashboard_url'])); ?>" target="_blank"><?php esc_html_e("10Web Dashboard", "tenweb-builder"); ?></a></div>
    <?php } ?>
      </div>
    <?php
  }

  /**
   * Popups header/footer/find.
  */
  public function twbb_template_popup() {
    ?>
    <div class="template_popup page_layout" style="display: none">
      <div id="template_popup_container">
        <span class="close_popup"> </span>
        <div class="template_popup_content">
          <div class="popup_content active" id="page_layout_content">
            <div class="popup_content_scroll_content">
              <div class="popup_content_scroll">
                <?php
                $templates = \Tenweb_Builder\Modules\TenwebRestApi::getTemplates();
                $loaded_templates = Templates::get_instance()->get_loaded_templates();
                foreach( $templates as $key => $val ) {
                  $title = str_replace('twbb_','',$key);
                  ?>
                  <div id="<?php echo esc_attr($key); ?>_container" class="template_container" data-template="<?php echo esc_attr(ucfirst($title)); ?>">
                    <h1>
                        <?php if( count( $templates[$key] ) === 0 ) { ?>
                      <span class="twbb-back no-template"><?php echo esc_html(sprintf( __( 'No %s Template', 'tenweb-builder' ), ucfirst($title) )); ?></span>
                            <a class="add-template-link" href="<?php echo esc_url(admin_url( "/edit.php?post_type=elementor_library&tabs_group=twbb_templates&elementor_library_type=" . $key ));?>">Add/Import</a>
                        <?php } else { ?>
                      <span class="twbb-back"><?php echo esc_html(sprintf( __( 'Current %s Template', 'tenweb-builder' ), ucfirst($title) )); ?></span>
                        <?php } ?>
                    </h1>
                    <div class="template_row">
                      <div class="twbb-clear template_select">
                        <select name="<?php echo esc_attr($key) ?>_template" id="<?php echo esc_attr($key) ?>_template" data-current="<?php echo isset($loaded_templates[$key]) ? esc_attr($loaded_templates[$key]) : 0 ?>" data-single = <?php echo count($templates[$key]) === 1 ? '1' : '' ?>>
                          <?php
                          for( $i = 0; $i < count($templates[$key]); $i++) {
                            ?>
                            <option value="<?php echo esc_attr($templates[$key][$i]->ID); ?>"><?php echo esc_html($templates[$key][$i]->post_name); ?></option>
                            <?php
                          }
                          ?>
                        </select>
                        <button id="twbb_popup_save">
                          <span class="twbb-save-popup-loader">
                            <i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i>
                          </span>
                          <?php esc_html_e('Save', 'tenweb-builder') ?>
                        </button>
                        <?php if( isset($loaded_templates[$key]) ) { ?>
                            <a class="edit_template_global" href="<?php echo esc_url(admin_url('post.php?post='. (isset($loaded_templates[$key]) ? $loaded_templates[$key] : 0) .'&action=elementor')); ?>" target="_blank"><?php esc_html_e('Edit', 'tenweb-builder') ?></a>
                        <?php } ?>
                    </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="template_popup twbb_finder_popup_layout">
    </div>
    <?php
  }

  public function websiteStructure() {
      ?>
      <script type='text/template' id='twbb_website_structure_top_bar-template'>
          <div class="twbb_website_structure_top_bar">
            <ul class="twbb_sub_menu">
              <?php
              $theme_locations = get_nav_menu_locations();
              $menu_name = "";
              $elements = [];
              if ( isset($theme_locations['header_menu']) ) {
                  $menu_obj = get_term($theme_locations['header_menu'], 'nav_menu');
                  $menu_name = isset($menu_obj->name) ? $menu_obj->name : '';
                  $elements = wp_get_nav_menu_items( $menu_name );
              }

              ?>
              <li class="title site_menu">
                  <p class="twbb-website-structure-sub">
                  <img class="twbb-structure-icon" src="<?php echo esc_url(plugin_dir_url( __DIR__ ) . '/assets/images/website_structure_site_menu_white.svg'); ?>">
                  <label><?php esc_html_e("Site menu", "tenweb-builder"); ?></label>
                  <span class="twbb-dropdown-icon twbb-dropdown-grey"></span>
                  </p>
                  <ul class="title_container closed">
                      <?php if ( gettype ( $elements ) === 'array' && !empty($elements) ) {

                          $nav_menu = $this->buildMenuTree($elements);
                          if( is_array($this->is_frontpage_in_menu()) ) {
                              array_unshift($nav_menu, (object) $this->is_frontpage_in_menu());
                          }
                          $this->outputMenuHtml($nav_menu);
                      }
                      else { ?>
                          <p class="nothing_published"><?php esc_html_e("Nothing published in this section", "tenweb-builder"); ?></p>
                      <?php } ?>
                  </ul>
              </li>
              <hr class="MuiDivider-root MuiDivider-fullWidth eui-divider css-b9906h-MuiDivider-root">
              <?php
              $pages = 21;
              $all_pages =  get_pages(
                  array(
                      "exclude" => $this->menu_ids, //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
                      "number" => $pages,
                      "post_type" => "page",
                      "sort_column" => "post_modified",
                      "sort_order" => "DESC",
                      "post_status" => "publish"
                  )
              );
              ?>
              <li class="title inner_pages">
                  <p class="twbb-website-structure-sub">
                  <img class="twbb-structure-icon" src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . '/assets/images/website_structure_inner_pages_white.svg'); ?>">
                  <label><?php esc_html_e("Inner pages", "tenweb-builder"); ?></label>
                  <span class="twbb-dropdown-icon twbb-dropdown-grey"></span>
                  </p>
                  <ul class="title_container closed">
                      <?php
                      if( !empty( $all_pages ) ) {
                          $page_count = 0;
                          foreach ( $all_pages as $all_page ) {
                              $has_templ = '';
                              if ( $this->is_template() && $this->check_template(get_the_ID(), $all_page->ID, 'page') ) {
                                  $has_templ = '<span class="tooltip-container"><i class="twbb-check twbb-widget-icon tooltip-icon active"></i><span class="tooltip-text">'.__('The template you are editing is applied to the following page', 'tenweb-builder').'</span></span>';
                              }
                              $current = '';
                              if ( isset($_GET['elementor-preview']) && intval($_GET['elementor-preview']) === $all_page->ID ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                  $current = '<span class="current">' . __("Current", "tenweb-builder") . '</span>';
                              }
                              $title = $all_page->post_title ? $all_page->post_title : __("No title", "tenweb-builder");
                              $link = admin_url('post.php?post='.$all_page->ID.'&action=elementor'); ?>
                              <li>
                                  <a href="<?php echo esc_url($link); ?>" target="_blank">
                                      <?php $content = $title.$current.$has_templ;
                                      echo wp_kses_post($content); ?>
                                      <?php if ($current === '') { ?>
                                          <i class="twbb-edit twbb-widget-icon <?php echo $has_templ !== '' ? 'has_templ' : '' ?>"></i>
                                      <?php } ?>
                                  </a>
                              </li>
                              <?php
                              $page_count++;
                              if( $page_count >= $pages-1 ) {
                                  $link_pages = admin_url('edit.php?post_type=page');
                                  echo "<a href='". esc_url($link_pages)."' class='view_more' target='_blank'>".esc_html_e("View More", "tenweb-builder")."<i class='fas fa-chevron-right'></i></a>";
                                  break;
                              }
                          }
                      }
                      else { ?>
                          <p class="nothing_published"><?php esc_html_e("Nothing published in this section", "tenweb-builder"); ?></p>
                      <?php } ?>
                  </ul>
              </li>
              <hr class="MuiDivider-root MuiDivider-fullWidth eui-divider css-b9906h-MuiDivider-root">
              <?php
              $templates = \Tenweb_Builder\Modules\TenwebRestApi::getTemplates();
              $loaded_templates = Templates::get_instance()->get_loaded_templates();
              $template_is_404 = false;
              ?>
              <li class="title other">
                  <p class="twbb-website-structure-sub">
                  <img class="twbb-structure-icon" src="<?php echo esc_url(plugin_dir_url( __DIR__ ) . '/assets/images/website_structure_other_white.svg'); ?>">
                  <label><?php esc_html_e("Other", "tenweb-builder"); ?></label>
                  <span class="twbb-dropdown-icon twbb-dropdown-grey"></span>
                  </p>
                  <ul class="title_container closed">
                      <?php
                      if( !empty($templates['twbb_single']) || !empty($templates['twbb_archive']) || !empty($templates['twbb_slide']) ) {
                          foreach ( $templates as $key => $values ) {
                              /* don't print header and footer templates */
                              if( $key === 'twbb_header' || $key === 'twbb_footer' ) {
                                  continue;
                              }

                              if(!empty($values)) {
                                  $title = ucfirst(str_replace(array("twbb_","_"), array("", " "), $key));
                                  echo "<span class='template_title'>" . esc_html( $title ). "</span>";
                              }

                              foreach ( $values as $template) {
                                  $current = '';
                                  if( (get_post_type( get_the_ID() ) === 'elementor_library' ? 1 : 0) ) {
                                      foreach ( $loaded_templates as $key_l => $value_l ) {
                                          if ( $template->ID === $value_l ) {
                                              $current = '<span class="current">' . esc_html__("Current", "tenweb-builder") . '</span>';
                                          }
                                      }
                                  }
                                  if ( $this->check_404_template($template->ID) ) {
                                      $template_is_404 = true;
                                      $link404 = admin_url('post.php?post='.$template->ID.'&action=elementor');
                                      $title404 = $template->post_title.$current;
                                  } else {
                                      $link = admin_url('post.php?post=' . $template->ID . '&action=elementor'); ?>
                                      <li>
                                          <a href="<?php echo esc_url( $link ); ?>" target="_blank">
                                              <?php echo esc_html( $template->post_title ) . wp_kses($current, array('span'=>array('class')) ); ?>
                                              <?php if($current === '') { ?><i class="twbb-edit twbb-widget-icon"></i> <?php } ?>
                                          </a>
                                      </li>
                                      <?php
                                  }
                              }
                          }
                          if( $template_is_404 ) {
                              ?>
                              <span class='template_title'>404 PAGE</span>
                              <li><a href="<?php echo esc_url($link404); ?>" target="_blank"><?php echo esc_html( $title404 ); ?><i class="twbb-edit twbb-widget-icon"></i></a></li>
                              <?php
                          }
                      }
                      else { ?>
                          <p class="nothing_published"><?php esc_html_e("Nothing published in this section", "tenweb-builder"); ?></p>
                      <?php } ?>
                  </ul>
              </li>
          </ul>
          </div>
      </script>

      <script type='text/template' id='twbb_topbar-buttons-template'>
          <div class="twbb_website_structure-footer">
              <span class="twbb-add-blank-page-button"><?php esc_html_e( 'Add Blank Page', 'tenweb-builder' ); ?></span>
              <a href="<?php echo esc_url( TENWEB_DASHBOARD . '/websites/' . get_option('tenweb_domain_id') . '/generate-page/'); ?>" target="_blank" class="twbb-add-new-ai-page-button"><?php esc_html_e( 'Create New Page with AI', 'tenweb-builder' ); ?></a>
          </div>
      </script>

      <script type='text/template' id='twbb_new_blank_page-template'>
          <div class="twbb-new-blank-page-layout"></div>
          <div class="twbb-new-blank-page-container">
              <p class="twbb-new-blank-page-title"><?php esc_html_e( 'New blank page', 'tenweb-builder' ); ?></p>
              <input type="text" value="" class="twbb-new-blank-page-input" placeholder="<?php echo esc_attr( 'Enter Page Title...'); ?>">
              <div class="twbb-new-blank-page-buttons-row">
                  <span class="twbb-new-blank-page-cancel-button"><?php esc_html_e( 'Cancel', 'tenweb-builder' ); ?></span>
                  <span class="twbb-new-blank-page-create-button twbb-create-button-disabled"><span class="twbb-new-blank-page-create-button-title"><?php esc_html_e( 'Create', 'tenweb-builder' ); ?></span><i></i></span>
              </div>
          </div>
      </script>
      <?php
  }
}
