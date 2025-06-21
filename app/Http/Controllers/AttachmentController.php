<?php
namespace App\Http\Controllers;
use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class AttachmentController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task); // Tylko uprawnieni mogą dodawać załączniki
        $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:5120', // max 5MB
        ]);
        $file = $request->file('attachment');
        $path = $file->store('attachments', 'public'); // Zapisz plik w storage/app/public/attachments
        $task->attachments()->create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
        ]);
        return back()->with('success', 'Plik został pomyślnie dodany.');
    }
    public function destroy(Attachment $attachment)
    {
        $this->authorize('update', $attachment->task); // Sprawdzam uprawnienia do nadrzędnego zadania
        // Usuwam plik z dysku
        Storage::disk('public')->delete($attachment->path);
        // Usuwam wpis z bazy danych
        $attachment->delete();
        return back()->with('success', 'Załącznik został usunięty.');
    }
}
