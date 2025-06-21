<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Task $task)
    {
        // Sprawdzam, czy użytkownik ma uprawnienie 'addComment' zdefiniowane w TaskPolicy.
        $this->authorize('addComment', $task);

        $request->validate(['body' => 'required|string']);

        $task->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Komentarz został dodany.');
    }

    public function destroy(Comment $comment)
    {
        // Autoryzacja: tylko autor komentarza może go usunąć
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Komentarz został usunięty.');
    }
}
