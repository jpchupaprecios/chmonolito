<?php

declare(strict_types=1);

namespace App\Models\Search\Refinements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class RefinementLinks extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'link', 'checked'];

    public function product()
    {
        return $this->belongsTo(Refinement::class);
    }

    public function getTitle(): string
    {
        return $this->attributes['title'] ?? '';
    }

    public function setTitle(string $title): void
    {
        $this->attributes['title'] = $title;
    }

    public function getLink(): string
    {
        return $this->attributes['link'] ?? '';
    }

    public function setLink(string $link): void
    {
        $this->attributes['link'] = $link;
    }

    public function isChecked(): bool
    {
        return $this->attributes['checked'] ?? false;
    }

    public function setChecked(bool $checked): void
    {
        $this->attributes['checked'] = $checked;
    }
}
