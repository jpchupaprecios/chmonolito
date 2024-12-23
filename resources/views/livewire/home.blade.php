<div>
    <h1 style="color:red">Scraping Amazon</h1>
    <div>
        <input type="text" wire:model.defer="query" placeholder="Buscar en Amazon" />
        <button wire:click="search">Buscar</button>
    </div>

    <div id="output" style="margin-top: 20px; border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;">
        <!-- Los resultados irán apareciendo aquí -->
    </div>
</div>
