<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

if (!function_exists('spaRender')) {
    function spaRender(Request $request, string $content, array $data = [])
    {
        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = View::make($content, $data);
            $sections = $view->renderSections();

            return response()->json([
                'title' => $sections['title'] ?? '',
                'styles'  => $sections['styles'] ?? '',
                'content' => $sections['content'] ?? '',
                'modal' => $sections['modal'] ?? '',
                'scripts' => $sections['scripts'] ?? '',
            ]);
        } else {
            return view($content, $data);
        }
    }
}

if (!function_exists('sanitizePhone')) {
    function sanitizePhone(?string $value): ?string
    {
        if (!$value) return null;

        $value = preg_replace('/\D+/', '', $value);

        if ($value === '') return null;

        if (str_starts_with($value, '0')) {
            $value = '62' . substr($value, 1);
        }

        if (str_starts_with($value, '8')) {
            $value = '62' . $value;
        }

        return $value;
    }
}

if (!function_exists('sanitizeUsername')) {
    function sanitizeUsername(?string $value): ?string
    {
        if (!$value) return null;

        $value = strtolower(trim($value));
        $value = preg_replace('/^(https?:\/\/)?(www\.)?tiktok\.com\/@/i', '', $value);
        return ltrim($value, '@');
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice($price): string
    {
        if ($price == 0) {
            return 'Rp 0 (Gratis)';
        }

        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}

if (!function_exists('formatSize')) {
    function formatSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes, 1024) : 0));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . $units[$pow];
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $withTime = true)
    {
        if (!$date) {
            return null;
        }

        $bulanIndo = [
            1 => 'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        $timestamp = strtotime($date);
        $day   = date('d', $timestamp);
        $month = $bulanIndo[(int)date('m', $timestamp)];
        $year  = date('Y', $timestamp);
        $time  = date('H:i', $timestamp);

        return $withTime
            ? "{$day} {$month} {$year} {$time}"
            : "{$day} {$month} {$year}";
    }
}
