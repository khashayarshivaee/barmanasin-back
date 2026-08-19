<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeaderMenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HeaderMenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menuItems = HeaderMenuItem::query()
            ->where('is_active', true)
            ->with([
                'megaMenuSections' => function ($query): void {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->with([
                            'links' => function ($query): void {
                                $query
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('id');
                            },
                        ]);
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $menuItems->map(
                fn (HeaderMenuItem $item): array => [
                    'id' => $item->id,

                    'title' => [
                        'en' => $item->title_en,
                        'fa' => $item->title_fa,
                    ],

                    'path' => $item->path,

                    'type' => $item->type,

                    'order' => $item->sort_order,

                    'sections' => $item->type === 'mega'
                        ? $item->megaMenuSections
                            ->map(
                                fn ($section): array => [
                                    'id' => $section->id,

                                    'title' => [
                                        'en' => $section->title_en,
                                        'fa' => $section->title_fa,
                                    ],

                                    'order' => $section->sort_order,

                                    'links' => $section->links
                                        ->map(
                                            fn ($link): array => [
                                                'id' => $link->id,

                                                'title' => [
                                                    'en' => $link->title_en,
                                                    'fa' => $link->title_fa,
                                                ],

                                                'description' => [
                                                    'en' => $link->description_en,
                                                    'fa' => $link->description_fa,
                                                ],

                                                'path' => $link->path,

                                                'type' => $link->link_type,

                                                'open_in_new_tab' =>
                                                    $link->open_in_new_tab,

                                                'icon' => $link->icon
                                                    ? Storage::disk('public')
                                                        ->url($link->icon)
                                                    : null,

                                                'order' => $link->sort_order,
                                            ]
                                        )
                                        ->values(),
                                ]
                            )
                            ->values()
                        : [],
                ]
            )->values(),
        ]);
    }
}
