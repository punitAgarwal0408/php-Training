<?php
namespace Modules\Cms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ];
    }
}
