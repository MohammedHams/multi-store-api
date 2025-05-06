<?php

namespace App\Http\Controllers\Api;

use App\Models\ExternalServiceLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExternalServiceLogsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = ExternalServiceLog::query()
            ->when($request->service_type, fn($q) => $q->where('service_type', $request->service_type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->with('order')
            ->paginate(15);

        return response()->json($logs);
    }

    public function show(ExternalServiceLog $log): JsonResponse
    {
        $log->load('order');
        return response()->json($log);
    }


    public function retry(ExternalServiceLog $log): JsonResponse
    {
        if ($log->status !== 'failed') {
            return response()->json(['message' => 'لا يمكن إعادة المحاولة إلا للسجلات الفاشلة'], 400);
        }


        $log->update([
            'attempts' => $log->attempts + 1,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'تمت إعادة المحاولة بنجاح',
            'log' => $log
        ]);
    }

    public function destroy(ExternalServiceLog $log): JsonResponse
    {
        $log->delete();
        return response()->json(null, 204);
    }
}
