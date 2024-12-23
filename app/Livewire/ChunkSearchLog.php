<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChunkSearchLog as ChunkSearchLogModel;
class ChunkSearchLog extends Component
{
    public function delete()
    {
        \App\Models\ChunkSearchLog::truncate();
        return true;
    }
    public function render()
    {
        $chunkSearchLog = ChunkSearchLogModel::orderBy('id', 'desc')->get();

        return view('livewire.chunk-search-log', [
            'chunkSearchLog' => $chunkSearchLog
        ])->layout('layouts.guest');
    }
}
