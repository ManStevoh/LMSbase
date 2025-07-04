<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\traits\LearningPageAssignmentTrait;
use App\Http\Controllers\Web\traits\LearningPageForumTrait;
use App\Http\Controllers\Web\traits\LearningPageItemInfoTrait;
use App\Http\Controllers\Web\traits\LearningPageMixinsTrait;
use App\Http\Controllers\Web\traits\LearningPageNoticeboardsTrait;
use App\Models\Certificate;
use App\Models\CourseLearningLastView;
use App\Models\CourseNoticeboard;
use Illuminate\Http\Request;
use App\Models\Webinar;
use Illuminate\Support\Facades\DB;

class LearningPageController extends Controller
{
    use LearningPageMixinsTrait, LearningPageAssignmentTrait, LearningPageItemInfoTrait,
        LearningPageNoticeboardsTrait, LearningPageForumTrait;

    public function index(Request $request, $slug)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            $this->authorize("panel_webinars_learning_page");
        }

        $requestData = $request->all();

        $webinarController = new WebinarController();

        $data = $webinarController->course($slug, true);

        $course = $data['course'];
        $user = $data['user'];

        /* Check Not Active */
        if ($course->status != "active" and (empty($user) or (!$user->isAdmin() and !$course->canAccess($user)))) {
            $data = [
                'pageTitle' => trans('update.access_denied'),
                'pageRobot' => getPageRobotNoIndex(),
            ];
            return view('web.default.course.not_access', $data);
        }

        $installmentLimitation = $webinarController->installmentContentLimitation($user, $course->id, 'webinar_id');
        if ($installmentLimitation != "ok") {
            return $installmentLimitation;
        }


        if (!$data or (!$data['hasBought'] and empty($course->getInstallmentOrder()))) {
            abort(403);
        }

        if (!empty($requestData['type']) and $requestData['type'] == 'assignment' and !empty($requestData['item'])) {

            $assignmentData = $this->getAssignmentData($course, $requestData);

            $data = array_merge($data, $assignmentData);
        }

        if ($course->creator_id != $user->id and $course->teacher_id != $user->id and !$user->isAdmin()) {
            $unReadCourseNoticeboards = CourseNoticeboard::where('webinar_id', $course->id)
                ->whereDoesntHave('noticeboardStatus', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            if ($unReadCourseNoticeboards) {
                $url = $course->getNoticeboardsPageUrl();
                return redirect($url);
            }
        }

        if ($course->certificate) {
            $data["courseCertificate"] = Certificate::where('type', 'course')
                ->where('student_id', $user->id)
                ->where('webinar_id', $course->id)
                ->first();
        }

        $data['userLearningLastView'] = CourseLearningLastView::query()
            ->where('user_id', $user->id)
            ->where('webinar_id', $course->id)
            ->first();

            //course learning progress
        $data['courseLearningProgress'] = $course->getProgress (true);

        return view('web.default.course.learningPage.index', $data);
    }

    private function calculateCourseProgress($userId, $courseId)
    {
        $course = Webinar::with(['sessions', 'files', 'textLessons', 'quizzes', 'assignments'])
            ->where('id', $courseId)
            ->first();
            $webinarContentCount = 0;
            if (!empty($course->sessions)) {
                $webinarContentCount += $course->sessions->count();
            }
            if (!empty($course->files)) {
                $webinarContentCount += $course->files->count();
            }
            if (!empty($course->textLessons)) {
                $webinarContentCount += $course->textLessons->count();
            }
            if (!empty($course->quizzes)) {
                $webinarContentCount += $course->quizzes->count();
            }
            if (!empty($course->assignments)) {
                $webinarContentCount += $course->assignments->count();
            }

            // Completed items by the user
            $completedItems = DB::table('course_learning_last_views')
                ->where('user_id', $userId)
                ->where('webinar_id', $courseId)
                ->count();
            if ($webinarContentCount > 0) {
                return round(($completedItems / $webinarContentCount) * 100, 2);
            }

            return 0;
        }
}
