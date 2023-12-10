<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\GlobalHelper;

class ReminderResource extends JsonResource
{
    use GlobalHelper;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "remind_at" => strval($this->remind_at),
            "event_at" => strval($this->event_at)
        ];
    }
}
