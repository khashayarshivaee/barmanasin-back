<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeImageShowcaseSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomeImageShowcaseController extends Controller
{
    public function index(): JsonResponse
    {
        $section = HomeImageShowcaseSection::query()
            ->where('is_active', true)
            ->with([
                'slides' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit(5),
            ])
            ->first();

        if (! $section) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => [
                'eyebrow' => [
                    'en' => $section->eyebrow_en,
                    'fa' => $section->eyebrow_fa,
                ],

                'title' => [
                    'en' => $section->title_en,
                    'fa' => $section->title_fa,
                ],

                'description' => [
                    'en' => $section->description_en,
                    'fa' => $section->description_fa,
                ],

                'slides' => $section->slides
                    ->map(fn ($slide) => [
                        'id' => $slide->id,

                        'image_url' => Storage::disk('public')
                            ->url($slide->image_path),

                        'title' => [
                            'en' => $slide->title_en,
                            'fa' => $slide->title_fa,
                        ],

                        'description' => [
                            'en' => $slide->description_en,
                            'fa' => $slide->description_fa,
                        ],

                        'sort_order' => $slide->sort_order,
                    ])
                    ->values(),
            ],
        ]);
    }
}
