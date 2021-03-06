<?php
namespace App;

use App\Support\Traits\Attributes;
use App\Support\Traits\Linkable;
use App\Support\Traits\Sortable;
use Baum\Node;
use Illuminate\Database\Eloquent\SoftDeletes;
use MartinBean\Database\Eloquent\Sluggable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

class Category extends Node implements HasMediaConversions
{

    use Linkable, Sortable, Attributes, Sluggable, HasMediaTrait, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * Autoload Relationships
     *
     * @var array
     */
    protected $with = ['media'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'intro_text',
        'enabled',
        'title',
    ];

    /**
     * With Baum, all NestedSet-related fields are guarded from mass-assignment
     * by default.
     *
     * @var array
     */
    protected $guarded = ['id', 'parent_id', 'lft', 'rgt', 'depth'];

    /**
     * Columns which restrict what we consider our Nested Set list
     *
     * @var array
     */
    protected $scoped = [];


    /**
     * Convert Images
     */
    public function registerMediaConversions()
    {

        $this->addMediaConversion('thumb')->setManipulations(['w' => 240])->performOnCollections('*');

        $this->addMediaConversion('medium')->setManipulations(['w' => 800])->performOnCollections('*');

        $this->addMediaConversion('full')->setManipulations(['w' => 1024])->performOnCollections('*');

        $this->addMediaConversion('adminThumb')->setManipulations(['w' => 100, 'sharp' => 15])->performOnCollections('*');
    }


    /**
     * @return mixed
     */
    public function productsEnabled()
    {
        return $this->products()->enabled();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'categories_id')->orderBy('position', 'asc');
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }


    /**
     * @return string
     */
    public function getIsEnabledAttribute()
    {
        return ($this->attributes['enabled'] == 0) ? 'No' : 'Yes';
    }
}
