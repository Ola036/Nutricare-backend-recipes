<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\UserRecipe;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Recipe::where('user_id', $request->user()->id)
            ->orderBy('favorite', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecipeRequest $request)
    {
        $user = $request->user();
        $message = 'I have ingredients: ' . $request->ingredients;

        if ($request->calories && $request->calories > 0) {
            $message .= ' and i need ' . $request->calories . ' calories';
        }

        if ($user->information->health_conditions) {
            $message .= ' and i have health conditions: ' . implode(', ', $user->information->health_conditions);
        }

        if ($user->information->dietary_preferences) {
            $message .= ' and i have dietary preferences: ' . implode(', ', $user->information->dietary_preferences);
        }

        $message .= '. Suggest a recipe.';

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $message
                ],
            ],
        ]);

        $recipe = Recipe::create([
            'user_id' => $request->user()->id,
            'ingredients' => $request->ingredients,
            'calories' => $request->calories,
            'response' => $result->choices[0]->message->content,
            'favorite' => false,
        ]);

        return response()->json($recipe, 200);
    }

    /**
     * Mark the specified resource as a Favorite.
     */
    public function favorite(Recipe $recipe)
    {
        $recipe->update(['favorite' => true]);

        return [
            'message' => 'Recipe has been marked as a favorite.',
        ];
    }

    /**
     * Mark the specified resource as not a Favorite.
     */
    public function unfavorite(Recipe $recipe)
    {
        $recipe->update(['favorite' => false]);

        return [
            'message' => 'Recipe has been unmarked as a favorite.',
        ];
    }
}
