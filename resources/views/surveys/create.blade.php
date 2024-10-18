@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Create Survey</h1>
    <form id="surveyForm" action="{{ route('surveys.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf
        <div class="form-group mb-4">
            <label for="name" class="form-label">Survey Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter survey name" required>
        </div>
        <div class="form-group mb-4">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Enter survey description"></textarea>
        </div>

        <div id="questions-container">
            <h4 class="mb-3">Questions</h4>
            <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">Add Question</button>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Create Survey</button>
        </div>
    </form>
    <div id="error-message" class="alert alert-danger mt-3" style="display: none;"></div>
</div>

<script>
    let questionCount = 0;

    function addQuestion() {
        questionCount++;
        const questionHtml = `
            <div class="question-block mb-4 p-3 border rounded bg-light" id="question-${questionCount}">
                <label class="form-label">Question ${questionCount}</label>
                <input type="text" name="questions[${questionCount}][text]" class="form-control mb-2" placeholder="Enter your question" required>
                <select name="questions[${questionCount}][type]" class="form-select mb-2" required>
                    <option value="text">Text</option>
                    <option value="radio">Radio</option>
                    <option value="checkbox">Checkbox</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="addOption(${questionCount})">Add Option</button>
                <div class="options-container mt-3" id="options-${questionCount}"></div>
            </div>
        `;
        document.getElementById('questions-container').insertAdjacentHTML('beforeend', questionHtml);
    }

    function addOption(questionId) {
        const optionsContainer = document.getElementById(`options-${questionId}`);
        const currentOptions = optionsContainer.querySelectorAll('input[type="text"]');
        
        if (currentOptions.length >= 5) {
            alert('You can only add a maximum of 5 options for each question.');
            return; 
        }

        const optionId = `option-${questionId}-${Date.now()}`;
        const optionHtml = `
            <div class="input-group mb-2" id="${optionId}">
                <input type="text" name="questions[${questionId}][options][]" class="form-control" placeholder="Enter option" required>
                <button type="button" class="btn btn-danger" onclick="removeOption('${optionId}')">Remove</button>
            </div>
        `;
        optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
    }

    function removeOption(optionId) {
        const optionElement = document.getElementById(optionId);
        optionElement.remove();
    }

    function validateForm() {
        const questions = document.querySelectorAll('#questions-container .question-block');
        let isValid = true;
        let errorMessage = '';

        questions.forEach((questionBlock, index) => {
            const options = questionBlock.querySelectorAll('.options-container input[type="text"]');
            const questionType = questionBlock.querySelector('select[name^="questions["][name$="[type]"]').value;
            const hasOptions = Array.from(options).some(input => input.value.trim() !== '');

            if ((questionType === 'radio' || questionType === 'checkbox') && !hasOptions) {
                isValid = false;
                errorMessage += `Question ${index + 1} must have at least one option.<br>`;
            }
        });

        const errorMessageDiv = document.getElementById('error-message');
        if (!isValid) {
            errorMessageDiv.innerHTML = errorMessage;
            errorMessageDiv.style.display = 'block';
        } else {
            errorMessageDiv.style.display = 'none';
        }

        return isValid;
    }
</script>

<style>
    .question-block {
        transition: box-shadow 0.3s ease;
    }
    .question-block:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

@endsection
