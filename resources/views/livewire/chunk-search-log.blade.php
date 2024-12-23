<div>
    <table>
        <thead>
            <tr>
                <th>Query</th>
                <th>Chunk ID</th>
                <th>Html</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chunkSearchLog->chunkSearchLogDetails as $chunkSearchLogDetail)
                <tr>
                    <td>{{ $chunkSearchLogDetail->chunk->query }}</td>
                    <td>{{ $chunkSearchLogDetail->search->id }}</td>
                    <td>{{ $chunkSearchLogDetail->chunk_html_output }}</td>
                    <td>{{ $chunkSearchLogDetail->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
