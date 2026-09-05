<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeContactSection;
use Illuminate\Http\JsonResponse;

class HomeContactSectionController extends Controller
{
    public function index(): JsonResponse
    {
        $section = HomeContactSection::query()
            ->where('is_active', true)
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

                'cta_label' => [
                    'en' => $section->cta_label_en,
                    'fa' => $section->cta_label_fa,
                ],

                'cta_path' => $section->cta_path,
            ],
        ]);
    }
}
