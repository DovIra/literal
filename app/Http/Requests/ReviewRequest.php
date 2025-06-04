<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\EventReview;
use App\Models\EventParticipant;


class ReviewRequest extends FormRequest
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

        // イベントIDはルートパラメータか入力から取得
        $eventId = $this->route('id') ?? $this->input('event_id');

        $userId = Auth::id();

        // 作成時
        if ($this->isMethod('post')) {
            if (!$eventId) {
                return false;
            }
            return $this->isParticipant($eventId, $userId);
        }

        // 更新時
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $reviewId = $this->route('reviewId');
            if (!$reviewId) {
                return false;
            }

            $review = EventReview::find($reviewId);
            if (!$review) {
                return false;
            }

            if (!$this->isParticipant($review->event_id, $userId)) {
                return false;
            }

            return $review->created_by === $userId;
        }

        // 上記以外は許可しない
        return false;
    }

    // イベント参加者か確認
    protected function isParticipant(int $eventId, int $userId): bool
    {
        return EventParticipant::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ];
    }
}
