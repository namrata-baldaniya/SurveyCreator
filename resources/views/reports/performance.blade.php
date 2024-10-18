@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4">Survey Performance Report</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Question</th>
                        <th>Type</th>
                        <th>Answer 1</th>
                        <th>Answer 2</th>
                        <th>Answer 3</th>
                        <th>Answer 4</th>
                        <th>Answer 5</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $data)
                        <tr>
                            <td>{{ $data['question'] }}</td>
                            <td>{{ ucfirst($data['type']) }}</td>
                            @foreach ($data['percentages'] as $optionId => $percentage)
                                <td>{{ $percentage }}</td>
                            @endforeach
                            @for ($i = count($data['percentages']); $i < 4; $i++)
                                <td>0%</td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
