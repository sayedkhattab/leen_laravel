<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalBanner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'action_text',
        'action_url',
        'is_limited_time',
        'starts_at',
        'expires_at',
        'display_order',
        'is_active',
        'target_audience',
        'link_type',
        'linked_seller_id',
        'linked_home_service_id',
        'linked_studio_service_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_limited_time' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active banners.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Scope a query to only include banners for a specific audience.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $audience
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAudience($query, $audience)
    {
        return $query->where(function ($query) use ($audience) {
            $query->where('target_audience', 'all')
                ->orWhere('target_audience', $audience);
        });
    }

    /**
     * Get the seller linked to this banner.
     */
    public function linkedSeller()
    {
        return $this->belongsTo(Seller::class, 'linked_seller_id');
    }

    /**
     * Get the home service linked to this banner.
     */
    public function linkedHomeService()
    {
        return $this->belongsTo(HomeService::class, 'linked_home_service_id');
    }

    /**
     * Get the studio service linked to this banner.
     */
    public function linkedStudioService()
    {
        return $this->belongsTo(StudioService::class, 'linked_studio_service_id');
    }

    /**
     * Get the dynamic link URL based on the link type.
     */
    public function getDynamicLinkUrl()
    {
        switch ($this->link_type) {
            case 'seller':
                if ($this->linked_seller_id) {
                    return '/sellers/' . $this->linked_seller_id;
                }
                break;
            case 'home_service':
                if ($this->linked_home_service_id) {
                    return '/home-services/' . $this->linked_home_service_id;
                }
                break;
            case 'studio_service':
                if ($this->linked_studio_service_id) {
                    return '/studio-services/' . $this->linked_studio_service_id;
                }
                break;
            case 'url':
            default:
                return $this->action_url;
        }

        return '#';
    }
} 