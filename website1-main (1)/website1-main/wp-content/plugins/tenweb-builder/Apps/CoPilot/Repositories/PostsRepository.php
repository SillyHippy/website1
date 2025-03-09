<?php

namespace Tenweb_Builder\Apps\CoPilot\Repositories;

use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;

class PostsRepository extends BaseWpPostsRepository
{
    protected string $postType = WpPostType::POST;
}
