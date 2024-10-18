@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Survey</h1>

    <form action="{{ route('surveys.update', $survey->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="delete_options" id="delete-options">

        <div class="form-group">
            <label for="name">Survey Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $survey->name) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $survey->description) }}</textarea>
        </div>

        <h3>Questions</h3>
        <div id="questions-container">
            @foreach ($survey->questions as $question)
                <div class="form-group question-block mb-4 p-3 border rounded bg-light" id="question-{{ $question->id }}">
                    <label for="question_{{ $question->id }}">Question:</label>
                    <input type="text" name="questions[{{ $question->id }}][text]" id="question_{{ $question->id }}" class="form-control mb-2" value="{{ old('questions.' . $question->id . '.text', $question->question_text) }}" required>
                    <select name="questions[{{ $question->id }}][type]" class="form-control mb-2" onchange="toggleOptionsVisibility({{ $question->id }})">
                        <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="radio" {{ $question->type == 'radio' ? 'selected' : '' }}>Radio</option>
                        <option value="checkbox" {{ $question->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                    </select>

                    <div class="options-container mt-3" id="options-container-{{ $question->id }}" style="display: {{ ($question->type == 'radio' || $question->type == 'checkbox') ? 'block' : 'none' }}">
                        <h4>Options</h4>
                        @foreach ($question->options as $option)
                            <div class="input-group mb-2" id="option-{{ $option->id }}">
                                <input type="text" name="questions[{{ $question->id }}][options][{{ $option->id }}]" value="{{ old('questions.' . $question->id . '.options.' . $option->id, $option->option_text) }}" class="form-control">
                                <button type="button" class="btn btn-danger" onclick="removeOption('{{ $option->id }}')">Remove</button>
                            </div>
                        @endforeach
                        <button type="button" class="btn btn-secondary" onclick="addNewOption({{ $question->id }})">Add Option</button>
                    </div>
                </div>
            @endforeach
        </div>
        
        <button type="button" class="btn btn-secondary mb-3" onclick="addNewQuestion()">Add New Question</button>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
    function toggleOptionsVisibility(questionId) {
        const questionType = document.querySelector(`select[name='questions[${questionId}][type]']`).value;
        const optionsContainer = document.getElementById(`options-container-${questionId}`);
        optionsContainer.style.display = (questionType === 'radio' || questionType === 'checkbox') ? 'block' : 'none';
    }

    function removeOption(optionId) {
        const optionElement = document.getElementById(`option-${optionId}`);
        if (optionElement) {
            const deleteOptionsInput = document.getElementById('delete-options');
            deleteOptionsInput.value += optionId + ',';
            optionElement.remove();
        }
    }

    function addNewOption(questionId) {
        const optionsContainer = document.getElementById(`options-container-${questionId}`);
        const optionId = `new-option-${Date.now()}`;
        const optionHtml = `
            <div class="input-group mb-2" id="${optionId}">
                <input type="text" name="questions[${questionId}][new_options][]" class="form-control" placeholder="Enter new option" required>
                <button type="button" class="btn btn-danger" onclick="removeOption('${optionId}')">Remove</button>
            </div>
        `;
        optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
    }

    function addNewQuestion() {
        const questionsContainer = document.getElementById('questions-container');
        const questionId = `new-question-${Date.now()}`;
        const questionHtml = `
            <div class="form-group question-block mb-4 p-3 border rounded bg-light" id="${questionId}">
                <label for="question_${questionId}">Question:</label>
                <input type="text" name="new_questions[${questionId}][text]" id="question_${questionId}" class="form-control mb-2" placeholder="Enter question text" required>
                <select name="new_questions[${questionId}][type]" class="form-control mb-2" onchange="toggleOptionsVisibility('${questionId}')">
                    <option value="text">Text</option>
                    <option value="radio">Radio</option>
                    <option value="checkbox">Checkbox</option>
                </select>

                <div class="options-container mt-3" id="options-container-${questionId}" style="display: none;">
                    <h4>Options</h4>
                    <button type="button" class="btn btn-secondary" onclick="addNewOption('${questionId}')">Add Option</button>
                </div>
            </div>
        `;
        questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
    }
</script>

@endsection
