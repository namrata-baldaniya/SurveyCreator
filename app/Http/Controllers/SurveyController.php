<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\UserSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    // Display a paginated list of surveys
    public function index()
    {
        $surveys = Survey::paginate(5);
        return view('surveys.index', compact('surveys'));
    }

    // Show the form for creating a new survey
    public function create()
    {
        return view('surveys.create');
    }

    // Store a newly created survey in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|in:text,radio,checkbox',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'required|string|max:255',
        ]);

        // Create the survey with a unique slug
        $survey = Survey::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'slug' => Str::slug($request->input('name')) . '-' . Str::random(6),
        ]);

        // Iterate through questions and save them
        foreach ($request->questions as $questionData) {
            $question = $survey->questions()->create([
                'question_text' => $questionData['text'],
                'type' => $questionData['type'],
            ]);

            // Save question options if present
            if (isset($questionData['options'])) {
                foreach ($questionData['options'] as $option) {
                    $question->options()->create(['option_text' => $option]);
                }
            }
        }

        // Generate the shareable URL
        $shareableUrl = route('surveys.share', $survey->slug);

        return redirect()->route('surveys.index')->with('success', 'Survey created successfully! Shareable URL: ' . $shareableUrl);
    }

    // Display a specific survey by its slug
    public function showBySlug($slug)
    {
        $survey = Survey::with('questions.options')->where('slug', $slug)->firstOrFail();
        return view('surveys.show', compact('survey'));
    }

    // Delete a survey from the database
    public function destroy(Survey $survey)
    {
        $survey->delete();
        return redirect()->route('surveys.index')->with('success', 'Survey deleted successfully!');
    }

    // Submit survey responses from users
    public function submit(Request $request, $surveyId)
    {
        // Validate user input
        $request->validate([
            'user_name' => 'required|string|max:255',
            'user_age' => 'required|integer|min:1|max:120',
            'user_gender' => 'required|in:male,female,other',
            'answers' => 'required|array',
        ]);

        // Create a new user survey record
        $user = UserSurvey::create([
            'name' => $request->input('user_name'),
            'age' => $request->input('user_age'),
            'gender' => $request->input('user_gender'),
        ]);

        // Store user answers to the survey
        foreach ($request->input('answers') as $questionId => $response) {
            $question = Question::find($questionId);
            if (!$question) {
                continue;
            }

            // Store radio or checkbox answers
            if (in_array($question->type, ['radio', 'checkbox'])) {
                $optionIds = is_array($response) ? $response : [$response];
                foreach ($optionIds as $optionId) {
                    Answer::create([
                        'survey_id' => $surveyId,
                        'question_id' => $questionId,
                        'question_option_id' => $optionId,
                        'user_id' => $user->id,
                    ]);
                }
            } elseif ($question->type === 'text') {
                // Store text-type question answer
                Answer::create([
                    'survey_id' => $surveyId,
                    'question_id' => $questionId,
                    'answer_text' => $response,
                    'user_id' => $user->id,
                ]);
            }
        }

        return redirect()->route('surveys.index')->with('success', 'Survey submitted successfully!');
    }

    // Show the form for editing an existing survey
    public function edit(Survey $survey)
    {
        $survey->load('questions.options');
        return view('surveys.edit', compact('survey'));
    }

    // Update an existing survey in the database
    public function update(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        // Update survey name and description
        $survey->update($request->only(['name', 'description']));

        // Handle deletion of options
        if ($request->filled('delete_options')) {
            $deleteOptionIds = explode(',', rtrim($request->input('delete_options'), ','));
            QuestionOption::whereIn('id', $deleteOptionIds)->delete();
        }

        // Update existing questions
        foreach ($request->input('questions', []) as $questionId => $questionData) {
            $question = Question::findOrFail($questionId);
            $question->update($questionData);

            // Update existing options
            if (!empty($questionData['options'])) {
                foreach ($questionData['options'] as $optionId => $optionText) {
                    $option = QuestionOption::findOrFail($optionId);
                    $option->update(['option_text' => $optionText]);
                }
            }

            // Add new options
            if (!empty($questionData['new_options'])) {
                foreach ($questionData['new_options'] as $newOptionText) {
                    $question->options()->create(['option_text' => $newOptionText]);
                }
            }
        }

        // Add new questions 
        if ($request->filled('new_questions')) {
            foreach ($request->input('new_questions') as $newQuestionData) {
                $newQuestion = $survey->questions()->create($newQuestionData);

                // Add options for the new question
                if (!empty($newQuestionData['options'])) {
                    foreach ($newQuestionData['options'] as $newOptionText) {
                        $newQuestion->options()->create(['option_text' => $newOptionText]);
                    }
                }
            }
        }

        return redirect()->route('surveys.index')->with('success', 'Survey updated successfully.');
    }
}
