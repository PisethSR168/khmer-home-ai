<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;

class WebAiController extends Controller
{
    public function __construct(protected GeminiAiService $aiService)
    {
    }

    public function assistant()
    {
        $featuredRooms = Property::available()->with(['images', 'amenities'])->take(4)->get();
        return view('ai.assistant', compact('featuredRooms'));
    }

    public function smartSearch(Request $request)
    {
        $query = $request->input('q', $request->input('query', ''));

        if (empty($query)) {
            return redirect()->route('properties.index');
        }

        $aiResult = $this->aiService->smartSearch($query, auth()->id());

        return view('properties.ai-results', [
            'query' => $query,
            'explanation' => $aiResult['explanation'],
            'criteria' => $aiResult['extracted_criteria'],
            'properties' => $aiResult['properties'],
            'total' => $aiResult['total_matches'],
        ]);
    }
}
