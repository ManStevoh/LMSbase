<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Webinar;
use App\Models\CourseLearning;
use App\Models\WebinarAssignmentHistory;
use App\Models\QuizzesResult;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/main.student'),
            trans('admin/main.student_email'),
            trans('admin/main.student') . ' ' . trans('admin/main.id'),
            trans('admin/main.instructor'),
            trans('admin/main.instructor') . ' ' . trans('admin/main.id'),
            trans('admin/main.paid_amount'),
            trans('admin/main.item'),
            trans('admin/main.item') . ' ' . trans('admin/main.id'),
            trans('admin/main.sale_type'),
            trans('admin/main.date'),
            trans('admin/main.status'),
            trans('admin/main.progress'),
        ];
    }

    public function map($sale): array
    {
        $paidAmount = $sale->payment_method == \App\Models\Sale::$subscribe
            ? trans('admin/main.subscribe')
            : (!empty($sale->total_amount) ? handlePrice($sale->total_amount) : trans('public.free'));

        $status = !empty($sale->refund_at) ? trans('admin/main.refund') : trans('admin/main.success');

        // Student Progress
        $progress = 0;
        if ($sale->type == 'webinar' && !empty($sale->webinar_id) && !empty($sale->buyer_id)) {
            $progress = $this->calculateStudentProgress($sale->buyer_id, $sale->webinar_id);
        }

        return [
            $sale->id,
            !empty($sale->buyer) ? $sale->buyer->full_name . ' ' . ($sale->buyer->middle_name ?? '') . ' ' . ($sale->buyer->last_name ?? '') : 'Deleted User',
            !empty($sale->buyer) ? $sale->buyer->email : 'Deleted Email',
            !empty($sale->buyer) ? $sale->buyer->id : 'Deleted User',
            !empty($sale->item_seller_user) ? $sale->item_seller_user->full_name . ' ' . ($sale->item_seller_user->middle_name ?? '') . ' ' . ($sale->item_seller_user->last_name ?? '') : $sale->item_seller,
            $sale->seller_id,
            $paidAmount,
            $sale->item_title,
            $sale->item_id,
            trans('admin/main.' . $sale->type),
            dateTimeFormat($sale->created_at, 'j M Y H:i'),
            $status,
            round($progress) . '%',
        ];
    }

    // Keep all the progress methods below as-is

    protected function calculateStudentProgress($userId, $webinarId)
    {
        $webinar = Webinar::find($webinarId);
        if (empty($webinar)) {
            return 0;
        }

        $filesStat = $this->getFilesProgress($userId, $webinar);
        $sessionsStat = $this->getSessionsProgress($userId, $webinar);
        $textLessonsStat = $this->getTextLessonsProgress($userId, $webinar);
        $assignmentsStat = $this->getAssignmentsProgress($userId, $webinar);
        $quizzesStat = $this->getQuizzesProgress($userId, $webinar);

        $passed = $filesStat['passed'] + $sessionsStat['passed'] + $textLessonsStat['passed'] + $assignmentsStat['passed'] + $quizzesStat['passed'];
        $count = $filesStat['count'] + $sessionsStat['count'] + $textLessonsStat['count'] + $assignmentsStat['count'] + $quizzesStat['count'];

        if ($passed > 0 && $count > 0) {
            return ($passed * 100) / $count;
        }

        if (!is_null($webinar->capacity)) {
            $salesCount = $webinar->sales()->count();
            if ($salesCount > 0 && $webinar->capacity > 0) {
                return ($salesCount * 100) / $webinar->capacity;
            }
        }

        return 0;
    }

    protected function getFilesProgress($userId, $webinar)
    {
        $passed = 0;
        $files = $webinar->files()->where('status', 'active')->get();

        foreach ($files as $file) {
            $status = CourseLearning::where('user_id', $userId)->where('file_id', $file->id)->first();
            if (!empty($status)) {
                $passed += 1;
            }
        }

        return ['passed' => $passed, 'count' => count($files)];
    }

    protected function getSessionsProgress($userId, $webinar)
    {
        $passed = 0;
        $sessions = $webinar->sessions()->where('status', 'active')->get();

        foreach ($sessions as $session) {
            $status = CourseLearning::where('user_id', $userId)->where('session_id', $session->id)->first();
            if (!empty($status)) {
                $passed += 1;
            }
        }

        return ['passed' => $passed, 'count' => count($sessions)];
    }

    protected function getTextLessonsProgress($userId, $webinar)
    {
        $passed = 0;
        $textLessons = $webinar->textLessons()->where('status', 'active')->get();

        foreach ($textLessons as $textLesson) {
            $status = CourseLearning::where('user_id', $userId)->where('text_lesson_id', $textLesson->id)->first();
            if (!empty($status)) {
                $passed += 1;
            }
        }

        return ['passed' => $passed, 'count' => count($textLessons)];
    }

    protected function getAssignmentsProgress($userId, $webinar)
    {
        $passed = 0;
        $assignments = $webinar->assignments()->where('status', 'active')->get();

        foreach ($assignments as $assignment) {
            $assignmentHistory = WebinarAssignmentHistory::where('assignment_id', $assignment->id)
                ->where('student_id', $userId)
                ->where('status', WebinarAssignmentHistory::$passed)
                ->first();

            if (!empty($assignmentHistory)) {
                $passed += 1;
            }
        }

        return ['passed' => $passed, 'count' => count($assignments)];
    }

    protected function getQuizzesProgress($userId, $webinar)
    {
        $passed = 0;
        $quizzes = $webinar->quizzes()->where('status', 'active')->get();

        foreach ($quizzes as $quiz) {
            $quizHistory = QuizzesResult::where('quiz_id', $quiz->id)
                ->where('user_id', $userId)
                ->where('status', QuizzesResult::$passed)
                ->first();

            if (!empty($quizHistory)) {
                $passed += 1;
            }
        }

        return ['passed' => $passed, 'count' => count($quizzes)];
    }
}
