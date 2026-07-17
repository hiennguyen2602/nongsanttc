<?php

namespace App\Http\Requests\Concerns;

trait HasImageUploadMessages
{
    /** @return array<string, string> */
    protected function imageUploadMessages(string ...$prefixes): array
    {
        $messages = [];

        foreach ($prefixes as $prefix) {
            $messages = array_merge($messages, image_upload_validation_messages($prefix));
        }

        return $messages;
    }
}
