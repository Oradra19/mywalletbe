<?php

namespace App\Http\Controllers;

use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Authenticated user retrieved.',
            'data' => $request->user(),
        ]);
    }

    public function balance(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Balance retrieved.',
            'data' => [
                'balance' => $request->user()->balance,
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        return response()->json(
            $this->transferService->history($request->user())
        );
    }
}
