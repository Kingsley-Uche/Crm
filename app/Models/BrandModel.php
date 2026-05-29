<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BrandModel extends Model
{
    protected $table = 'brand_models';

    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'website_url',
        'contact_email',
        'contact_phone',
        'address',

        // Core SEO
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',

        // Open Graph (Facebook, WhatsApp, LinkedIn, Telegram)
        'og_title',
        'og_description',
        'og_image',
        'og_type',

        // Twitter / X
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_card',

        // Search Engine
        'canonical_url',
        'robots',

        // Status
        'is_indexed',
    ];

    /**
     * Boot model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {

            // Generate slug automatically
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }

            // Default SEO fields
            $brand->meta_title = $brand->meta_title
                ?? $brand->name;

            $brand->meta_description = $brand->meta_description
                ?? Str::limit(strip_tags($brand->description), 160);

            $brand->og_title = $brand->og_title
                ?? $brand->meta_title;

            $brand->og_description = $brand->og_description
                ?? $brand->meta_description;

            $brand->twitter_title = $brand->twitter_title
                ?? $brand->meta_title;

            $brand->twitter_description = $brand->twitter_description
                ?? $brand->meta_description;

            $brand->og_image = $brand->og_image
                ?? $brand->logo_url;

            $brand->twitter_image = $brand->twitter_image
                ?? $brand->logo_url;

            $brand->og_type = $brand->og_type
                ?? 'website';

            $brand->twitter_card = $brand->twitter_card
                ?? 'summary_large_image';

            $brand->robots = $brand->robots
                ?? 'index,follow';

            $brand->is_indexed = $brand->is_indexed
                ?? true;

            // Canonical URL
            if (empty($brand->canonical_url) && !empty($brand->website_url)) {
                $brand->canonical_url =
                    rtrim($brand->website_url, '/') . '/' . $brand->slug;
            }
        });
    }
}