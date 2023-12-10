<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    protected $reminderService;

    public function __construct()
    {
        $this->reminderService = new ReminderService();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->reminderService->list($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'remind_at' => 'required|integer',
            'event_at' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $create = $this->reminderService->post($request);

        return $create;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->reminderService->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'remind_at' => 'required|integer',
            'event_at' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return $this->reminderService->put($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->reminderService->delete($id);
    }
}
