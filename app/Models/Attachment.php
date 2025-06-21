<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Attachment extends Model
{
    use HasFactory;
    protected $fillable = ['task_id', 'original_name', 'path'];
    public function task() { return $this->belongsTo(Task::class); }

    public function isImage(): bool
    {
        // Definiuję listę popularnych rozszerzeń plików graficznych
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];

        // Pobieram rozszerzenie z oryginalnej nazwy pliku
        $extension = pathinfo($this->original_name, PATHINFO_EXTENSION);

        // Zwracam true, jeśli rozszerzenie znajduje się na liście
        return in_array(strtolower($extension), $imageExtensions, true);
    }
}
