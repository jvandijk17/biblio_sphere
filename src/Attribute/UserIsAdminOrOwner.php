<?php

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class UserIsAdminOrOwner
{
    public function __construct(
        private string $subject = 'user'
    ) {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }
}
