<?php

namespace Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions;

use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;

class FindWpPostIdByTitle extends BaseWpPostAction
{

    private string $title;

    protected function __construct(string $postType, string $title)
    {
        parent::__construct($postType);
        $this->title = $title;
    }

    public static function find(string $title, string $postType = WpPostType::PAGE): int
    {
        return (new static($postType, $title))->handle();
    }

    protected function handle()
    {
        return $this->repo->getIdByTitle($this->title);
    }
}
