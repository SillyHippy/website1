<?php

namespace Tenweb_Builder\Apps\CoPilot\Actions\BasePostActions;

use Tenweb_Builder\Apps\CoPilot\Enums\WpPostType;

class ChangeWpPostVisibility extends BaseWpPostAction
{
    private ?int $postId;

    private int $visibility;

    protected function __construct(string $postType, ?int $postId, bool $visibility)
    {
        parent::__construct($postType);
        $this->postId = $postId;
        $this->visibility = $visibility;
    }

    public static function runById(?int $postId, bool $visibility, string $postType = WpPostType::PAGE): bool
    {
        return (new static($postType, $postId, $visibility))->handle();
    }

    protected function handle(): bool
    {
        if (! $this->postId) {
            return false;
        }

        if ($this->visibility === 0) {
            $this->repo->unpublish($this->postId);
        } elseif ($this->visibility === 1) {
            $this->repo->publish($this->postId);
        }

        return true;
    }

    public static function runByTitle(string $title, bool $visibility, string $postType = WpPostType::PAGE): bool
    {
        $postId = FindWpPostIdByTitle::find($title, $postType);

        return (new static($postType, $postId, $visibility))->handle();
    }
}
