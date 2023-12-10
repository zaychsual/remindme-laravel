<?php

namespace App\Services;

use Exception;
use App\Models\Reminder;
use App\Helpers\GlobalHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ReminderResource;

class ReminderService
{
    use ResponseHelper, GlobalHelper;

    private $modelReminder;

    public function __construct()
    {
        $this->modelReminder = new Reminder();
    }

    public function list($request)
    {
        DB::beginTransaction();
        try {
            $limit      = $request->limit ?? 10;
            $query = $this->modelReminder->paginate($limit);
            return response()->json([
                'ok' => true,
                'data' => [
                    'reminders' => ReminderResource::collection($query)
                ],
                'limit' => $limit
            ], 200);
            // return $this->responsePaginate(200, 'Sukses get data.', ReminderResource::collection($query));
        } catch (\Throwable $th) {
            return $this->responseInternalError();
        }
    }

    public function post($request)
    {
        try {
            DB::beginTransaction();
            $payload = $this->modelReminder->rawPayload($request);
            $add = $this->modelReminder->create($payload);

            if (!$add) return $this->responseInternalError();

            DB::commit();
            return response()->json([
                'ok' => true,
                'data' => new ReminderResource($add),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseInternalError();
        }
    }

    public function put($request, $id)
    {
        try {
            DB::beginTransaction();
            $find = $this->modelReminder->find($id);
            if (!$find) $this->responseNotFound();

            $payload = $this->modelReminder->rawPayload($request);
            $change = $find->update($payload);
            if (!$find) return $this->responseNotFound();

            DB::commit();
            return response()->json([
                'ok' => true,
                'data' => new ReminderResource($find),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseInternalError();
        }
    }

    public function show($id)
    {
        try {
            DB::beginTransaction();
            $find = $this->modelReminder->find($id);
            if (!$find) return $this->responseNotFound();

            return response()->json([
                'ok' => true,
                'data' => new ReminderResource($find),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseInternalError();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $find = $this->modelReminder->find($id);
            if (!$find) return $this->responseNotFound();
            $find->destroy($id);
            DB::commit();
            return response()->json([
                'ok' => true
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseInternalError();
        }
    }
}
