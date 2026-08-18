<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeaderMenuItem;
use Illuminate\Http\JsonResponse;

class HeaderMenuController extends Controller
{
    public function index(): JsonResponse
    {
        $items = HeaderMenuItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HeaderMenuItem $item): array => [
                'id' => $item->id,
                'title' => [
                    'en' => $item->title_en,
                    'fa' => $item->title_fa,
                ],
                'path' => $item->path,
                'order' => $item->sort_order,
            ])
            ->values();

        return response()->json([
            'data' => $items,
        ]);
    }
}
