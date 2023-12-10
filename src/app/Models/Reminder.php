<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'remind_at',
        'event_at',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($data) {
            $data->created_by = auth()->user()->id ?? 1;
        });

        static::updating(function ($data) {
            $data->updated_by = auth()->user()->id;
        });

        static::deleting(function ($data) {
            $data->updated_by = auth()->user()->id;
            $data->update();
        });
    }

    /**
     * payload
     *
     * @param Request $request
     * @return array
     */
    public function rawPayload($request): array
    {
        $payload['title'] = $request->title;
        $payload['description'] = $request->description;
        $payload['remind_at'] = $request->remind_at;
        $payload['event_at'] = $request->event_at;

        return $payload;
    }
}
