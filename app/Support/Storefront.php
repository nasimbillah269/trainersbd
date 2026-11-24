<?php

namespace App\Support;

use App\Models\CourseCategory;

/**
 * Shared storefront data (site settings, payment methods, categories).
 * Values are cached for the current request only.
 */
class Storefront
{
    protected static array $cache = [];

    public static function site(): array
    {
        return self::$cache['site'] ??= (function () {
            $g = general();
            return [
                'name'    => $g->title ?: 'TrainersBD',
                'tagline' => $g->subtitle ?: 'Online Courses in Bangla',
                'phone'   => $g->mobile ?: '',
                'email'   => $g->email ?: '',
                'address' => $g->address_one ?: 'Dhaka, Bangladesh',
                'meta_description' => $g->meta_description,
                'meta_keywords'    => $g->meta_keyword,
                // Only real links are shown; empty or "#" values are hidden
                'social'  => array_filter([
                    'facebook'  => trim((string) $g->facebook_link),
                    'youtube'   => trim((string) $g->youtube_link),
                    'linkedin'  => trim((string) $g->linkedin_link),
                    'instagram' => trim((string) $g->instagram_link),
                ], fn ($link) => $link !== '' && $link !== '#'),
            ];
        })();
    }

    public static function tel(): string
    {
        return preg_replace('/[^0-9+]/', '', self::site()['phone']);
    }

    /** All payment methods (admin settings), including disabled ones. */
    public static function allPaymentMethods(): array
    {
        $raw = general()->payment_methods ?? null;
        $list = is_array($raw) ? $raw : (json_decode($raw ?: '[]', true) ?: []);
        $colors = ['bkash' => '#E2136E', 'nagad' => '#F15A22', 'bank' => '#1E40AF'];
        foreach ($list as &$m) {
            $m['color'] = $colors[$m['key']] ?? '#4F46E5';
            $m['step_list'] = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $m['steps'] ?? ''))));
        }
        return $list;
    }

    public static function paymentMethods(): array
    {
        return array_values(array_filter(self::allPaymentMethods(), fn ($m) => !empty($m['enabled'])));
    }

    public static function categories()
    {
        return self::$cache['cats'] ??= CourseCategory::active()
            ->withCount(['courses' => fn ($q) => $q->where('status', 'active')])
            ->get();
    }

    /**
     * Menu tree for a location managed in Admin → Menus Setting.
     * Returns [['label','url','target','active','children' => [...]], ...]
     */
    public static function menu(string $location = 'Header Menus'): array
    {
        return self::$cache['menu:' . $location] ??= (function () use ($location) {
            $menu = \App\Models\Attribute::where('type', 8)->whereNull('parent_id')
                ->where('location', $location)->where('status', 'active')->first();
            if (!$menu) {
                return [];
            }
            $all = \App\Models\Attribute::where('type', 8)->where('category_id', $menu->id)
                ->where('status', 'active')->orderBy('view')->orderBy('id')
                ->with(['pagelink:id,name,slug,template,status', 'productCtglink:id,name,slug,icon,status'])
                ->get();
            $build = function ($parentId) use (&$build, $all) {
                return $all->where('parent_id', $parentId)->map(function ($item) use ($build) {
                    $link = self::menuItemLink($item);
                    if (!$link) {
                        return null;
                    }
                    $children = $build($item->id);
                    $link['children'] = $children;
                    $link['active'] = $link['active'] || collect($children)->contains('active', true);
                    return $link;
                })->filter()->values()->all();
            };
            return $build($menu->id);
        })();
    }

    /** Resolve a menu item (custom link / page / course category) to label + URL. */
    public static function menuItemLink($item): ?array
    {
        $label = null;
        $url = null;

        if ((int) $item->menu_type === 1) {                 // Page
            $page = $item->pagelink;
            if (!$page || $page->status !== 'active') {
                return null;
            }
            $label = $item->name ?: $page->name;
            $url = $page->template === 'Front Page' ? route('index') : route('pageView', $page->slug);
        } elseif ((int) $item->menu_type === 3) {           // Course category
            $cat = $item->productCtglink;
            if (!$cat || !$cat->status) {
                return null;
            }
            $label = $item->name ?: $cat->name;
            $url = route('courses.category', $cat->slug);
        } elseif ((int) $item->menu_type === 2) {           // Blog category (blog removed)
            return null;
        } else {                                             // Custom link
            $label = $item->name;
            $slug = trim((string) $item->slug);
            if ($slug === '' || $label === null) {
                return null;
            }
            if (preg_match('#^(https?:)?//|^(mailto|tel):#i', $slug)) {
                $url = $slug;
            } elseif (str_starts_with($slug, '#')) {
                $url = rtrim(route('index'), '/') . '/' . $slug;
            } else {
                $url = url(ltrim($slug, '/'));
            }
        }

        // Active when the link is the current page, or a parent section of it (/courses → /courses/category/x)
        $active = false;
        if (!str_contains($url, '#')) {
            $current = rtrim(url()->current(), '/');
            $linkPath = rtrim(strtok($url, '?'), '/');
            $home = rtrim(route('index'), '/');
            if (str_contains($url, '?')) {
                $active = rtrim($url, '/') === rtrim(request()->fullUrl(), '/');
            } else {
                $active = $linkPath === $current || ($linkPath !== $home && str_starts_with($current . '/', $linkPath . '/'));
            }
        }

        return [
            'label'  => $label,
            'url'    => $url,
            'target' => $item->target ? '_blank' : null,
            'active' => $active,
            'icon'   => (int) $item->menu_type === 3 ? ($item->productCtglink->icon ?? null) : null,
            'count'  => (int) $item->menu_type === 3 ? $item->productCtglink->courses()->where('status', 'active')->count() : null,
        ];
    }

    public static function money($amount): string
    {
        return '৳' . number_format((float) $amount);
    }

    public static function initials(?string $name): string
    {
        return collect(preg_split('/\s+/', trim((string) $name)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
    }
}
