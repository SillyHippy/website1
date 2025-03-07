<?php

namespace Tenweb_Builder\Apps\CoPilot\AjaxActionControllers;

use Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions\ChangeWpPostVisibility;
use Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions\CloneWpPost;
use Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions\CreateWpPost;
use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;

class WpPostController
{
    public function changeWpPostVisibility(): void
    {
        check_ajax_referer('twbb-cop-nonce', 'twbb_cop_nonce');

        if (! isset($_POST['visibility']) || (empty($_POST['id']) && empty($_POST['title']))) {
            wp_send_json_error('Validation error, please provide correct visibility and page id or title.');
        }
        $visibility = (int) $_POST['visibility'];
        $id = (int) $_POST['id'];
        $postType = ! empty($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : WpPostType::PAGE;

        if (!empty($_POST['title'])) {
            $result = ChangeWpPostVisibility::runByTitle(esc_sql(sanitize_text_field($_POST['title'])), $visibility);
        } else {
            $result = ChangeWpPostVisibility::runById($id, $visibility, $postType);
        }

        if (empty($result)) {
            wp_send_json_error(['Could not change visibility']);
        }

        wp_send_json_success(['status' => 'success']);
    }

    public function cloneWpPost(): void
    {
        check_ajax_referer('twbb-cop-nonce', 'twbb_cop_nonce');

        if (empty($_POST['id']) && empty($_POST['title'])) {
            wp_send_json_error('Validation error, please provide correct page id or title.');
        }
        $id = (int) $_POST['id'];
        $postType = ! empty($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : WpPostType::PAGE;

        if (!empty($_POST['title'])) {
            $result = CloneWpPost::runByTitle(esc_sql(sanitize_text_field($_POST['title'])), $postType);
        } else {
            $result = CloneWpPost::runById($id, $postType);
        }

        if (empty($result)) {
            wp_send_json_error(['Could not clone post']);
        }

        wp_send_json_success(['status' => 'success', 'new_post_id' => $result]);
    }

    public function createWpPost(): void
    {
        check_ajax_referer('twbb-cop-nonce', 'twbb_cop_nonce');

        if (empty($_POST['data'])) {
            wp_send_json_error('Validation error, please provide data');
        }
        $postType = ! empty($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : WpPostType::PAGE;

        // todo add validation and remove phpcs ignore
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $result = CreateWpPost::run($_POST['data'], $postType);

        if (empty($result)) {
            wp_send_json_error(['Could not create post']);
        }

        wp_send_json_success(['status' => 'success', 'new_post_id' => $result]);
    }
}
