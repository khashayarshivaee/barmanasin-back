<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeIntro;
use Illuminate\Http\JsonResponse;

class HomeIntroController extends Controller
{
    public function show(): JsonResponse
    {
        $intro = HomeIntro::query()
            ->with([
                'facts' => fn ($query) =>
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->where('is_active', true)
            ->first();

        if (! $intro) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $intro->id,

                'eyebrow' => [
                    'en' => $intro->eyebrow_en,
                    'fa' => $intro->eyebrow_fa,
                ],

                'title' => [
                    'en' => $intro->title_en,
                    'fa' => $intro->title_fa,
                ],

                'description' => [
                    'en' => $intro->description_en,
                    'fa' => $intro->description_fa,
                ],

                'cta' => [
                    'title' => [
                        'en' => $intro->cta_title_en,
                        'fa' => $intro->cta_title_fa,
                    ],
                    'path' => $intro->cta_path,
                ],

                'facts' => $intro->facts
                    ->map(fn ($fact) => [
                        'value' => $fact->value,

                        'label' => [
                            'en' => $fact->label_en,
                            'fa' => $fact->label_fa,
                        ],

                        'suffix' => [
                            'en' => $fact->suffix_en,
                            'fa' => $fact->suffix_fa,
                        ],
                    ])
                    ->values(),
            ],
        ]);
    }
}
