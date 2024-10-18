@extends('layouts.app')

@section('content')
<h1>Surveys</h1>
<a href="{{ route('surveys.create') }}" class="btn btn-primary">Create Survey</a>
<a href="{{ route('report.performance') }}" class="btn btn-primary">View Performance</a>

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($surveys as $survey)
    <tr>
        <td>{{ $survey->name }}</td>
        <td>{{ $survey->description }}</td>
        <td>
            <a href="{{ route('surveys.share', $survey->slug) }}" target="_blank">{{ route('surveys.share', $survey->slug) }}</a>
        </td>
        <td>
            <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('surveys.destroy', $survey->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </td>
    </tr>
@endforeach

    

        
    </tbody>
</table>
{{ $surveys->links('pagination::bootstrap-4') }}

@endsection
