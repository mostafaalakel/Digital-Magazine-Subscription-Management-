<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleDetailsResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Article;
use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    use ApiResponseTrait;

    //return summary data about articles
    public function articlesByMagazine($magazine_id)
    {
        $this->authorize('viewAny', Article::class);

        $magazine = Magazine::findOrFail($magazine_id);

        if (auth()->user()->role == 'subscriber') {
            if (!$this->checkIfUserSubscription($magazine)) {
                return $this->forbiddenResponse('You need an active subscription to this magazine to view its details.');
            }
        }

        $articles = $magazine->articles()->select('id as article_id', 'title')->paginate(10);
        return $this->retrievedResponse($articles, 'Articles retrieved successfully for the magazine');
    }


    public function articleDetails($article_id)
    {
        $this->authorize('view', Article::class);

        $article = Article::with('comments.user', 'magazine')->findOrFail($article_id);

        $magazine = $article->magazine;
        if (auth()->user()->role == 'subscriber') {
            if (!$this->checkIfUserSubscription($magazine)) {
                return $this->forbiddenResponse('You need an active subscription to this magazine to view its details.');
            }
        }

        $articleResource = new ArticleDetailsResource($article);
        return $this->retrievedResponse($articleResource, 'Article Details retrieved successfully');
    }


    public function storeArticle(Request $request)
    {
        $this->authorize('create', Article::class);

        $rules = [
            'magazine_id' => 'required|exists:magazines,id',
            'title' => 'required|string',
            'content' => 'required|string',
            'publish_date' => 'required|date'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }


        Article::create([
            'magazine_id' => $request->magazine_id,
            'title' => $request->title,
            'content' => $request->content,
            'publish_date' => $request->publish_date
        ]);

        return $this->createdResponse(null, 'Article created successfully');
    }


    public function updateArticle(Request $request, $article_id)
    {
        $this->authorize('update', Article::class);
        $rules = [
            'magazine_id' => 'required|exists:magazines,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish_date' => 'required|date'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $article = Article::findOrFail($article_id);

        $article->update([
            'magazine_id' => $request->magazine_id,
            'title' => $request->title,
            'content' => $request->content,
            'publish_date' => $request->publish_date
        ]);

        return $this->updatedResponse(null, 'Article updated successfully');
    }


    public function deleteArticle($article_id)
    {
        $this->authorize('delete', Article::class);
        $article = Article::findOrFail($article_id);
        $article->delete();
        return $this->deletedResponse('Article deleted successfully');
    }

    public function checkIfUserSubscription($magazine)
    {
        return $magazine->subscriptions()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }
}
