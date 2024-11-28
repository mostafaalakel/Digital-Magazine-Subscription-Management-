<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use ApiResponseTrait;
    //all these function for user and ---admin(can manage all comments expect addComment it`s only for user)

    public function addComment(Request $request)
    {
        $this->authorize('create', Comment::class);
        $rules = [
            'article_id' => 'required|exists:articles,id',
            'content' => 'required|string|max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $comment = Comment::create([
            'article_id' => $request->article_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return $this->createdResponse(null, 'Comment added successfully!');
    }

    public function updateComment(Request $request, $commentId)
    {
        $this->authorize('manage', Comment::class);
        $rules = [
            'content' => 'required|string|max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $comment = Comment::findOrFail($commentId);

        $comment->update([
            'content' => $request->content,
        ]);

        return $this->updatedResponse(null, 'Comment updated successfully!');
    }

    public function deleteComment($commentId)
    {
        $this->authorize('manage', Comment::class);
        $comment = Comment::findOrFail($commentId);
        $comment->delete();
        return $this->deletedResponse('Comment deleted successfully!');
    }
}
