<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeHero;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomeHeroController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $hero = HomeHero::query()
            ->where('is_active', true)
            ->first();

        if (! $hero) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $hero->id,

                'eyebrow' => [
                    'en' => $hero->eyebrow_en,
                    'fa' => $hero->eyebrow_fa,
                ],

                'title' => [
                    'en' => $hero->title_en,
                    'fa' => $hero->title_fa,
                ],

                'description' => [
                    'en' => $hero->description_en,
                    'fa' => $hero->description_fa,
                ],

                'fullscreen_caption' => [
                    'en' => $hero->fullscreen_caption_en,
                    'fa' => $hero->fullscreen_caption_fa,
                ],

                'cta' => [
                    'title' => [
                        'en' => $hero->cta_title_en,
                        'fa' => $hero->cta_title_fa,
                    ],
                    'path' => $hero->cta_path,
                ],

                'media' => [
                    'desktop' => $this->mediaUrl($hero->desktop_image),

                    'mobile' => $this->mediaUrl(
                        $hero->mobile_image ?: $hero->desktop_image,
                    ),

                    'alt' => [
                        'en' => $hero->image_alt_en,
                        'fa' => $hero->image_alt_fa,
                    ],
                ],
            ],
        ]);
    }

    private function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
