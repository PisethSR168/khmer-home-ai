<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\GeminiAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiController extends BaseApiController
{
    public function __construct(protected GeminiAiService $aiService)
    {
    }

    public function smartSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $userId = $request->user()?->id;
        $result = $this->aiService->smartSearch($request->query('query', $request->input('query')), $userId);

        return $this->sendResponse($result, 'AI search processed successfully');
    }

    public function recommendations(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $propertyId = $request->filled('property_id') ? (int) $request->property_id : null;

        $result = $this->aiService->smartRecommendations($userId, $propertyId);

        return $this->sendResponse($result, 'Smart recommendations generated');
    }

    public function rentalAssistant(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $userId = $request->user()?->id;
        $result = $this->aiService->rentalAssistantChat(
            $request->message,
            $request->input('history', []),
            $userId
        );

        return $this->sendResponse($result);
    }

    public function generateDescription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'property_type' => 'required|string',
            'khan_district' => 'required|string',
            'price_per_month' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $userId = $request->user()?->id;
        $result = $this->aiService->generatePropertyDescription($request->all(), $userId);

        return $this->sendResponse($result, 'AI description generated');
    }

    public function checkCompleteness(Request $request): JsonResponse
    {
        $result = $this->aiService->checkListingCompleteness($request->all());
        return $this->sendResponse($result);
    }

    public function analyzePhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image_url' => 'required|string',
            'caption' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $result = $this->aiService->analyzePhoto($request->image_url, $request->caption);
        return $this->sendResponse($result);
    }

    public function duplicateCheck(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|integer',
            'image_hash' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $result = $this->aiService->detectDuplicateImages(
            (int) $request->property_id,
            $request->image_hash
        );

        return $this->sendResponse($result);
    }
}
