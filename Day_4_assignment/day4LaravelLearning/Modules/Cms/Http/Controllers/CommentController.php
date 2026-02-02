<?php
namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cms\Repositories\CommentRepository;
use Modules\Cms\Entities\Comment;
use Modules\Cms\Http\Requests\CommentRequest;
use Modules\Cms\Resources\CommentResource;

class CommentController extends Controller
{
    protected $comments;

    public function __construct(CommentRepository $comments)
    {
        $this->comments = $comments;
    }

    // GET /api/cms/pages/{id}/comments
    public function index($pageId)
    {
        $comments = $this->comments->allForPage($pageId);
        return CommentResource::collection($comments);
    }

    // POST /api/cms/pages/{id}/comments
    public function store(CommentRequest $request, $pageId)
    {
        $data = $request->validated();
        $data['page_id'] = $pageId;
        $data['user_id'] = Auth::id();
        $comment = $this->comments->create($data);
        return new CommentResource($comment);
    }

    // PUT /api/cms/comments/{id}
    public function update(CommentRequest $request, $id)
    {
        $comment = $this->comments->update($id, $request->validated());
        return new CommentResource($comment);
    }

    // DELETE /api/cms/comments/{id}
    public function destroy($id)
    {
        $this->comments->delete($id);
        return response()->json(['message' => 'Comment deleted']);
    }

    // POST /api/cms/comments/{id}/approve
    public function approve($id)
    {
        // Only admin can approve
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $comment = $this->comments->approve($id);
        return new CommentResource($comment);
    }

    // POST /api/cms/comments/{id}/reject
    public function reject($id)
    {
        // Only admin can reject
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $comment = $this->comments->reject($id);
        return new CommentResource($comment);
    }
}
