<?php

namespace App\Core\Services;

use App\Core\Dao\QuizAnswerDao;

class QuizAnswerService extends BaseService
{
    public const PASS_MARK_PERCENTAGE = 50;

    protected $quizanswerDao;
    protected $quizService;

    public function __construct(QuizAnswerDao $quizanswerDao, QuizService $quizService)
    {
        parent::__construct($quizanswerDao);
        $this->quizanswerDao = $quizanswerDao;
        $this->quizService = $quizService;
    }

    public function validationRules()
    {
        return array(
            'user_id' => 'required|integer',
            'module_id' => 'required|integer',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer' => 'required|string',
        );
    }

    public function save($data)
    {
        return $this->quizanswerDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->quizanswerDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->quizanswerDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->quizanswerDao->search($id, $name, $limit, $extra);
    }

    public function getLatestSubmissionUuid($userId, $moduleId)
    {
        return $this->quizanswerDao->getLatestSubmissionUuid($userId, $moduleId);
    }

    public function getModuleQuizResults($userId, $moduleId)
    {
        $listOfQuestion = $this->quizService->search(null, null, null, ['module_id' => $moduleId]);
        if ($listOfQuestion->isEmpty()) {
            return null;
        }

        $submissionUuid = $this->getLatestSubmissionUuid($userId, $moduleId);
        $countCorrectAnswer = 0;

        foreach ($listOfQuestion as $question) {
            $extra = [
                'user_id' => $userId,
                'question_id' => $question->id,
            ];

            if (!empty($submissionUuid)) {
                $extra['submission_uuid'] = $submissionUuid;
            }

            $userAnswer = $this->one(null, null, $extra);
            if ($userAnswer) {
                $question->user_answer = $userAnswer->answer;
                $question->is_user_answer_correct = $userAnswer->answer === $question->correct_option;
                if ($question->is_user_answer_correct) {
                    $countCorrectAnswer++;
                }
            }
        }

        $totalQuestions = $listOfQuestion->count();
        $passPercentage = $totalQuestions > 0 ? ($countCorrectAnswer / $totalQuestions) * 100 : 0;

        return [
            'submission_uuid' => $submissionUuid,
            'count_correct_answer' => $countCorrectAnswer,
            'total_questions' => $totalQuestions,
            'passPercentage' => $passPercentage,
            'pass_mark' => self::PASS_MARK_PERCENTAGE,
            'passed' => $passPercentage >= self::PASS_MARK_PERCENTAGE,
            'list_of_item' => $listOfQuestion,
        ];
    }

    public function hasPassed($passPercentage)
    {
        return $passPercentage >= self::PASS_MARK_PERCENTAGE;
    }

    public function belowPassMarkMessage()
    {
        return 'You have scored below the pass mark of ' . self::PASS_MARK_PERCENTAGE . '%. You should re-do the quiz.';
    }
}
