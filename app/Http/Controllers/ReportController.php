<?php
namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function showReport()
    {
        // Get all questions
        $questions = Question::with('options')->get();

        // Prepare the report data
        $reportData = [];

        foreach ($questions as $question) {
            // Get the total number of answers for this question
            $totalAnswers = Answer::where('question_id', $question->id)->count();

            // Initialize answer percentages
            $answerPercentages = [];

            if ($question->type === 'radio' || $question->type === 'checkbox') {
                // For radio and checkbox, calculate percentage for each option
                foreach ($question->options as $option) {
                    $optionCount = Answer::where('question_id', $question->id)
                                         ->where('question_option_id', $option->id)
                                         ->count();

                    $percentage = $totalAnswers > 0 ? ($optionCount / $totalAnswers) * 100 : 0;
                    $answerPercentages[$option->id] = round($percentage, 2) . '%';
                }
            } elseif ($question->type === 'text') {
                // For text type, calculate how many users submitted an answer
                $textAnswerCount = Answer::where('question_id', $question->id)
                                         ->whereNotNull('answer_text')
                                         ->count();

                $percentage = $totalAnswers > 0 ? ($textAnswerCount / $totalAnswers) * 100 : 0;
                $answerPercentages['text'] = round($percentage, 2) . '%';
            }
            // Add data to the report array
            $reportData[] = [
                'question' => $question->question_text,
                'type' => $question->type,
                'percentages' => $answerPercentages
            ];
            // dd($reportData);
        }

        // Pass the report data to the view
        return view('reports.performance', compact('reportData'));
    }
}
