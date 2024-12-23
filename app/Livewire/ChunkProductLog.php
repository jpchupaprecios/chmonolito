<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChunkProductLog as ChunkProductLogModel;
class ChunkProductLog extends Component
{
    public function delete()
    {
        \App\Models\ChunkProductLog::truncate();
        return true;
    }
    public function render()
    {
        $chunkProductLog = ChunkProductLogModel::orderBy('id', 'desc')->get();

        return view('livewire.chunk-product-log', [
            'chunkProductsLog' => $chunkProductLog
        ])->layout('layouts.guest');
    }
}
