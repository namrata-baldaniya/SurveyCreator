@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="text-center">
            <h1 class="mb-4">{{ $survey->name }}</h1>
            <p class="text-muted">{{ $survey->description }}</p>
        </div>

        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Fill out this survey</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('surveys.submit', $survey->id) }}" method="POST">
                    @csrf
                    <div class="border-bottom pb-3 mb-3">
                        <h4>User Details</h4>
                        <div class="mb-3">
                            <label for="user_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="user_age" name="user_age" placeholder="Enter your age" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_gender" class="form-label">Gender</label>
                            <select class="form-select" id="user_gender" name="user_gender" required>
                                <option value="" disabled selected>Select your gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-3">Survey Questions</h4>
                        @foreach ($survey->questions as $index => $question)
                            <div class="mb-4 p-3 bg-light rounded">
                                <label class="form-label">
                                    <strong>{{ $index + 1 }}. {{ $question->question_text }}</strong>
                                </label>
                                <div class="mt-2">
                                    @if ($question->type === 'radio')
                                        @foreach ($question->options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" id="option_{{ $option->id }}">
                                                <label class="form-check-label" for="option_{{ $option->id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @elseif ($question->type === 'checkbox')
                                        @foreach ($question->options as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" id="option_{{ $option->id }}">
                                                <label class="form-check-label" for="option_{{ $option->id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @elseif ($question->type === 'text')
                                        <input type="text" class="form-control mt-2" name="answers[{{ $question->id }}]" placeholder="Your answer" required>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Survey</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
