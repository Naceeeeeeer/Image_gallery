<h1>Moments</h1>

@if(isset($moment))
    <ul>
        <li>{{ $moment->file_name }}</li>
    </ul>
@else
    <p>No histogram available</p>
@endif
