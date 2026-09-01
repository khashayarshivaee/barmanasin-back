<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeFeaturedProject;
use App\Models\HomeFeaturedProjectsSection;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomeFeaturedProjectsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $section = HomeFeaturedProjectsSection::query()
            ->where('is_active', true)
            ->first();

        if (! $section) {
            return response()->json([
                'data' => null,
            ]);
        }

        $featuredProjects = HomeFeaturedProject::query()
            ->where('is_active', true)
            ->whereHas('project', function ($query) {
                $query
                    ->where('status', Project::STATUS_PUBLISHED)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->whereHas('category', function ($categoryQuery) {
                        $categoryQuery->where('is_active', true);
                    });
            })
            ->with([
                'project.category',
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

                'projects' => $featuredProjects
                    ->map(function (HomeFeaturedProject $featuredProject): array {
                        $project = $featuredProject->project;

                        return [
                            'id' => $project->id,
                            'slug' => $project->slug,

                            'title' => [
                                'en' => $project->title_en,
                                'fa' => $project->title_fa,
                            ],

                            'short_description' => [
                                'en' => $project->short_description_en,
                                'fa' => $project->short_description_fa,
                            ],

                            'category' => [
                                'slug' => $project->category->slug,

                                'name' => [
                                    'en' => $project->category->name_en,
                                    'fa' => $project->category->name_fa,
                                ],
                            ],

                            'location' => [
                                'en' => $project->location_en,
                                'fa' => $project->location_fa,
                            ],

                            'year' => $project->year,

                            'cover_image_url' => $this->publicStorageUrl(
                                $project->cover_image_path
                            ),

                            'mobile_cover_image_url' => $this->publicStorageUrl(
                                $project->mobile_cover_image_path
                            ),

                            'path' => '/projects/' . $project->slug,
                        ];
                    })
                    ->values(),
            ],
        ]);
    }

    private function publicStorageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $url = Storage::disk('public')->url($path);

        if (
            str_starts_with($url, 'http://') ||
            str_starts_with($url, 'https://')
        ) {
            return $url;
        }

        return url($url);
    }
}
