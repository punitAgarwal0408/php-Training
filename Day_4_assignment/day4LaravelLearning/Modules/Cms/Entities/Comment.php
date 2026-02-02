<?php
namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'page_id', 'user_id', 'content', 'status', 'parent_id'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
