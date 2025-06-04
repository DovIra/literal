<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;


class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // ログインしていなければNG
        if (!Auth::check()) {
            return false;
        }

        // 新規登録（store）なら許可
        if ($this->isMethod('post')) {
            return true;
        }

        // ルートモデルバインディングでイベント取得
        $eventId = $this->route('id'); 
        $event = Event::find($eventId);

        // 更新（update）の場合は、イベントの作成者とログインユーザーが一致すればOK
        return $event && $event->created_by === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'event_name' => 'required|string|max:50',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:50',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // update時のみ追加
            $rules['delete_images.*'] = 'nullable|integer';
        }

        return $rules;
    }
}
