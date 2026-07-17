<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\HasImageUploadMessages;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PostRequest extends FormRequest
{
    use HasImageUploadMessages;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'image' => image_upload_file_rules(['nullable']),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->imageUploadMessages('image');
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $hasNew = $this->hasFile('image');
            $hasKept = filled($this->input('existing_image'));

            if (! $hasNew && ! $hasKept) {
                $validator->errors()->add('image', 'Vui lòng chọn ảnh đại diện.');
            }
        });
    }

    /** @return array<string, mixed> */
    public function toModelData(): array
    {
        $post = $this->route('post');
        $postId = $post instanceof Post ? $post->id : null;

        return [
            'title' => $this->input('title'),
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'excerpt' => $this->input('excerpt'),
            'content' => $this->input('content'),
            'slug' => generate_unique_slug($this->input('title'), 'posts', $postId),
            'is_published' => $this->boolean('is_published'),
            'published_at' => $this->input('published_at') ?? now(),
        ];
    }
}
