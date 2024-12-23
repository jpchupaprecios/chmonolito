<div>
    <table style="color: #333;">
        <thead>
        <tr>
            <th>Query</th>
            <th>Chunk ID</th>
            <th>Html</th>
            <th>Time</th>
        </tr>
        </thead>
        <tbody>
        @foreach($chunkProductsLog as $chunkProductLog)
            <tr log-type="{{ $chunkProductLog->type . " " .$chunkProductLog->status }}">
                <td>{{ $chunkProductLog->sku }}</td>
                <td>{{ $chunkProductLog->id }}</td>
                <td>{{ $chunkProductLog->chunk_html_output }}</td>
                <td>{{ $chunkProductLog->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
