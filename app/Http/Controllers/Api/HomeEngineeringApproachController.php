<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeEngineeringApproachSection;
use Illuminate\Http\JsonResponse;

class HomeEngineeringApproachController extends Controller
{
    public function index(): JsonResponse
    {
        $section =
            HomeEngineeringApproachSection::query()
                ->where('is_active', true)
                ->with([
                    'steps' => function ($query) {
                        $query
                            ->where('is_active', true)
                            ->orderBy('sort_order');
                    },
                ])
                ->first();


        if (! $section) {
            return response()->json([
                'data' => null,
            ]);
        }


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
                ],


                'steps' => $section->steps
                    ->map(function ($step) {

                        return [
                            'id' => $step->id,

                            'title' => [
                                'en' => $step->title_en,
                                'fa' => $step->title_fa,
                            ],

                            'description' => [
                                'en' => $step->description_en,
                                'fa' => $step->description_fa,
                            ],

                            'sort_order' =>
                                $step->sort_order,
                        ];

                    })
                    ->values(),

            ],
        ]);
    }
}
