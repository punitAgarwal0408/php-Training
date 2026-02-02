<?php
namespace Modules\Cms\Repositories;

use Modules\Cms\Entities\Comment;
use Illuminate\Support\Facades\Cache;

class CommentRepository
{
    protected $cacheKeyPrefix = 'cms_comments_';

    public function allForPage($pageId)
    {
        $key = $this->cacheKeyPrefix . 'page_' . $pageId;
        return Cache::remember($key, 60, function () use ($pageId) {
            return Comment::where('page_id', $pageId)
                ->with(['author', 'replies'])
                ->orderBy('created_at', 'asc')
                ->get();
        });
    }

    public function find($id)
    {
        $key = $this->cacheKeyPrefix . 'id_' . $id;
        return Cache::remember($key, 60, function () use ($id) {
            return Comment::with(['author', 'replies'])->find($id);
        });
    }

    public function create(array $data)
    {
        $comment = Comment::create($data);
        $this->clearPageCache($comment->page_id);
        return $comment;
    }

    public function update($id, array $data)
    {
        $comment = Comment::findOrFail($id);
        $comment->update($data);
        $this->clearPageCache($comment->page_id);
        Cache::forget($this->cacheKeyPrefix . 'id_' . $id);
        return $comment;
    }

    public function delete($id)
    {
        $comment = Comment::findOrFail($id);
        $pageId = $comment->page_id;
        $comment->delete();
        $this->clearPageCache($pageId);
        Cache::forget($this->cacheKeyPrefix . 'id_' . $id);
        return true;
    }

    public function approve($id)
    {
        return $this->update($id, ['status' => 'approved']);
    }

    public function reject($id)
    {
        return $this->update($id, ['status' => 'rejected']);
    }

    protected function clearPageCache($pageId)
    {
        Cache::forget($this->cacheKeyPrefix . 'page_' . $pageId);
    }
}
