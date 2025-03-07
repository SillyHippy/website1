<?php

namespace Tenweb_Builder\Apps\CoPilot\Repositories;

use DateTimeZone;
use Exception;
use Tenweb_Builder\Apps\CoPilot\Interfaces\BaseRepositoryInterface;
use WP_Post;

class BaseWpPostsRepository implements BaseRepositoryInterface
{
    protected string $postType;

    private static $instance;

    private function __construct()
    {
        if (empty($this->postType)) {
            throw new Exception('Post type is not defined');
        }
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function get($args = [])
    {
        $args = array_merge([
            'post_type' => $this->postType,
            'posts_per_page' => -1,
        ], $args);

        // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
        return get_posts($args);
    }

    public function find($id)
    {
        return get_post($id);
    }

    public function delete($id)
    {
        return wp_delete_post($id);
    }

    public function publish($id)
    {
        return wp_update_post([
            'ID' => $id,
            'post_status' => 'publish',
        ]);
    }

    public function unpublish($id)
    {
        return wp_update_post([
            'ID' => $id,
            'post_status' => 'draft',
        ]);
    }

    public function getIdByTitle($title): ?int
    {
        /** @var WP_Post|null $post */
        $post = null;

        // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
        $posts = get_posts(
            [
                'post_type' => $this->postType,
                'title' => $title,
                'post_status' => 'all',
                'numberposts' => 1,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'orderby' => 'post_date ID',
                'order' => 'ASC',
            ]
        );

        if (! empty($posts)) {
            $post = $posts[0];
        }

        return $post instanceof WP_Post ? $post->ID : null;
    }

    public function clone(?int $sourcePostId, ?string $newName = null): ?int
    {
        $p = $this->find($sourcePostId);

        if (! ($p instanceof WP_Post)) {
            return null;
        }

        $newPost = [
            'post_name' => $p->post_name,
            'post_type' => $p->post_type,
            'ping_status' => $p->ping_status,
            'post_parent' => $p->post_parent,
            'menu_order' => $p->menu_order,
            'post_password' => !empty($p->post_password) ? $p->post_password : '',
            'post_excerpt' => $p->post_excerpt,
            'comment_status' => $p->comment_status,
            'post_title' => $newName ?: $p->post_title . ' - Clone',
            'post_content' => $p->post_content,
            'post_author' => $p->post_author,
            'to_ping' => $p->to_ping,
            'pinged' => $p->pinged,
            'post_content_filtered' => $p->post_content_filtered,
            'post_category' => $p->post_category,
            'tags_input' => $p->tags_input,
            'tax_input' => $p->tax_input,
            'page_template' => $p->page_template,
            'post_date' => current_datetime()->format('Y-m-d H:i:s'),
            'post_date_gmt' => current_datetime()->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
        ];

        $newId = wp_insert_post($newPost);
        //todo add error handling
        $format = get_post_format($sourcePostId);
        set_post_format($newId, $format);

        $meta = get_post_meta($sourcePostId);

        foreach ($meta as $key => $val) {
            update_post_meta($newId, $key, maybe_unserialize($val[0]));
        }

        return $newId;
    }

    public function create(array $data): int
    {
        $data['post_type'] = $this->postType;

        return wp_insert_post($data);
    }
}
