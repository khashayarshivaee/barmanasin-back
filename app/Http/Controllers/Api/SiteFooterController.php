<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteFooter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SiteFooterController extends Controller
{
    public function index(): JsonResponse
    {
        $footer = SiteFooter::query()
            ->where('is_active', true)
            ->with([
                'links' => fn ($query) =>
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->first();

        if (! $footer) {
            return response()->json([
                'data' => null,
            ]);
        }


        $links = $footer->links
            ->map(fn ($link) => [
                'id' => $link->id,

                'group' => $link->group,

                'title' => [
                    'en' => $link->title_en,
                    'fa' => $link->title_fa,
                ],

                'url' => $link->url,

                'sort_order' => $link->sort_order,
            ]);


        return response()->json([
            'data' => [

                'logo_url' =>
                    $footer->logo_path
                        ? Storage::disk('public')
                        ->url($footer->logo_path)
                        : null,

                'description' => [
                    'en' => $footer->description_en,
                    'fa' => $footer->description_fa,
                ],

                'contact' => [
                    'address' => [
                        'en' => $footer->address_en,
                        'fa' => $footer->address_fa,
                    ],

                    'phone' => $footer->phone,

                    'fax' => $footer->fax,

                    'email' => $footer->email,
                ],

                'copyright' => [
                    'en' => $footer->copyright_en,
                    'fa' => $footer->copyright_fa,
                ],

                'links' => [
                    'services' =>
                        $links
                            ->where('group', 'services')
                            ->values(),

                    'about' =>
                        $links
                            ->where('group', 'about')
                            ->values(),

                    'social' =>
                        $links
                            ->where('group', 'social')
                            ->values(),
                ],
            ],
        ]);
    }
}
