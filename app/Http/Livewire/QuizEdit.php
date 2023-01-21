<?php

namespace App\Http\Livewire;

use App\Models\Question;
use App\Models\Quiz;
use Livewire\Component;

class QuizEdit extends Component
{

    public $quiz;
    public $name;
    public $question;
    public $questions;

    protected $rules = [
        'name' => 'required',
    ];
    public function mount()
    {
        $this->name = $this->quiz->name;
        $alreadyAddQuestion = $this->quiz->questions->pluck('id')->toArray();
        $this->questions = Question::select(['id', 'name'])->whereNotIn('id', $alreadyAddQuestion)->get();
        if (count($this->questions) > 0) {
            $this->question = $this->questions[0]->id;
        }
    }
    public function render()
    {
        //dd($this->quiz->questions->pluck('id')->toArray());
        $questions = Question::select(['id', 'name'])->whereNotIn('id', $this->quiz->questions->pluck('id')->toArray())->get();
        return view('livewire.quiz-edit', [
            'questions' => $questions
        ]);
    }


    public function addQuestion()
    {
        $this->validate([
            'question' => 'required',
        ]);
        $quiz = Quiz::findOrFail($this->quiz->id);
        $quiz->questions()->attach($this->question);

        flash()->addSuccess('Question added successfully');

        return redirect()->route('quiz.edit', $this->quiz->id);
    }


    public function editQuiz()
    {
        $this->validate();
        $quiz = Quiz::findOrFail($this->quiz->id);

        $quiz->name = $this->name;
        $quiz->save();

        flash()->addSuccess('Quiz edit successfully');
    }

    public function removeQuiz($id)
    {
        $quiz = Quiz::findOrFail($this->quiz->id);
        $quiz->questions()->detach($id);

        flash()->addSuccess('Quiz removed successfully');

        return redirect()->route('quiz.edit', $this->quiz->id);
    }
}
