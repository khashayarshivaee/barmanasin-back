<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capability;
use App\Models\HomeCapabilitiesSection;
use App\Models\HomeFeaturedCapability;
use Illuminate\Http\JsonResponse;

class HomeCapabilitiesController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $section = HomeCapabilitiesSection::query()
            ->where('is_active', true)
            ->first();

        if (! $section) {
            return response()->json([
                'data' => null,
            ]);
        }

        $featuredCapabilities = HomeFeaturedCapability::query()
            ->where('is_active', true)
            ->whereHas('capability', function ($query) {
                $query
                    ->where('is_active', true)
                    ->where('status', Capability::STATUS_PUBLISHED)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            })
            ->with([
                'capability.focusPoints' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => [
                'section' => [
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

                    'cta' => [
                        'title' => [
                            'en' => $section->cta_title_en,
                            'fa' => $section->cta_title_fa,
                        ],

                        'path' => $section->cta_path,
                    ],
                ],

                'capabilities' => $featuredCapabilities
                    ->map(function (HomeFeaturedCapability $featuredCapability): array {
                        $capability = $featuredCapability->capability;

                        return [
                            'id' => $capability->id,

                            'slug' => $capability->slug,

                            'title' => [
                                'en' => $capability->title_en,
                                'fa' => $capability->title_fa,
                            ],

                            'short_description' => [
                                'en' => $capability->short_description_en,
                                'fa' => $capability->short_description_fa,
                            ],

                            'focus_points' => $capability->focusPoints
                                ->map(function ($focusPoint): array {
                                    return [
                                        'id' => $focusPoint->id,

                                        'title' => [
                                            'en' => $focusPoint->title_en,
                                            'fa' => $focusPoint->title_fa,
                                        ],
                                    ];
                                })
                                ->values(),

                            'path' => '/capabilities/' . $capability->slug,
                        ];
                    })
                    ->values(),
            ],
        ]);
    }
}
