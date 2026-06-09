<div class="rich-editor" data-upload-url="{{ route('admin.media.upload') }}">
    <div class="rich-editor-area"></div>
    <textarea name="{{ $name }}" class="hidden rich-editor-input">{!! old($name, $value ?? '') !!}</textarea>
</div>
