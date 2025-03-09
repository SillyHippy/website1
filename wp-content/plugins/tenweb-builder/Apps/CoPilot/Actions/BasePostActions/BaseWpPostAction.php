<?php

namespace Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions;

use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;
use Tenweb_Builder\Apps\CoPilot\Repositories\BaseWpPostsRepository;
use Tenweb_Builder\Apps\CoPilot\Repositories\PagesRepository;
use Tenweb_Builder\Apps\CoPilot\Repositories\PostsRepository;

class BaseWpPostAction
{
    protected ?BaseWpPostsRepository $repo = null;

    public function __construct(string $postType)
    {
        if ($postType === WpPostType::PAGE) {
            $this->repo = PagesRepository::getInstance();
        } elseif ($postType === WpPostType::POST) {
            $this->repo = PostsRepository::getInstance();
        }
    }
}
