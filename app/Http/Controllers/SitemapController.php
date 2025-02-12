<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class SitemapController extends Controller
{
    public function index()
    {
        $content = file_get_contents(resource_path('sitemaps/sitemap_index.xml'));
        return response($content, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function profiles()
    {
        $content = file_get_contents(resource_path('sitemaps/sitemap_profiles.xml'));
        return response($content, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function menu()
    {
        $content = file_get_contents(resource_path('sitemaps/sitemap_menu.xml'));
        return response($content, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    public function landing()
    {
        $content = file_get_contents(resource_path('sitemaps/sitemap_landing.xml'));
        return response($content, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}