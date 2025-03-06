<?php

namespace Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions;

use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;

class CreateWpPost extends BaseWpPostAction
{
    private array $data;

    protected function __construct(string $postType, array $data)
    {
        parent::__construct($postType);
        $this->data = $data;
    }

    public static function run(array $data = [], string $postType = WpPostType::PAGE)
    {
        return (new static($postType, $data))->handle();
    }

    protected function handle()
    {
        //todo validate data before creating
        return $this->repo->create($this->data);
    }
}
