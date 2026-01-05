<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/messages/sent",
     *     operationId="getSentMessages",
     *     tags={"Messages"},
     *     summary="Get sent messages",
     *     description="Returns a paginated list of sent messages",
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10,
     *             example=10
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1,
     *             example=1
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=42),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="phone_number", type="string", example="+9055543208888"),
     *                     @OA\Property(property="content", type="string", example="Hello Insider"),
     *                     @OA\Property(property="message_id", type="string", example="67f2f8a8-ea58-4ed0-a6f9-ff217df4d849"),
     *                     @OA\Property(property="sent_at", type="string", format="date-time", example="2026-01-04 12:30:00")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function sent(Request $request, MessageService $messageService): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);

        $messages = $messageService->getSentMessages($perPage);

        return response()->json([
            'meta' => [
                'total' => $messages->total(),
                'per_page' => $messages->perPage(),
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
            ],
            'data' => $messages->items(),
        ]);
    }
}
