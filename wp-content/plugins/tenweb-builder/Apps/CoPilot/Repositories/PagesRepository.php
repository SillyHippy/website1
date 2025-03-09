<?php

namespace Tenweb_Builder\Apps\CoPilot\Repositories;

use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;

class PagesRepository extends BaseWpPostsRepository
{
    protected string $postType = WpPostType::PAGE;

    public function get($args = [])
    {
        $args = array_merge([
            'posts_per_page' => -1,
        ], $args);

        return get_pages($args);
    }
}
