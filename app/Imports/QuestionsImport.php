<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class QuestionsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Question([
            'question_type' => $row['question_type'],
            'question_text' => $row['question_text'],
            'description' => $row['description'],
            'options' => $row['options'],
            'correct_answer' => $row['correct_answer'],
            'exam_id' => $row['exam_id'],
        ]);
    }
}
