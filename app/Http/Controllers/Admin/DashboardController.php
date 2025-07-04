<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\traits\DashboardTrait;
use App\Http\Controllers\Controller;
use App\Models\FeatureWebinar;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    use DashboardTrait;

    public function index()
    {
        $this->authorize('admin_general_dashboard_show');
        $user = auth()->user();

        if ($user->can('admin_general_dashboard_daily_sales_statistics')) {
            $dailySalesTypeStatistics = $this->dailySalesTypeStatistics();
        }

        if ($user->can('admin_general_dashboard_income_statistics')) {
            $getIncomeStatistics = $this->getIncomeStatistics();
        }

        if ($user->can('admin_general_dashboard_total_sales_statistics')) {
            $getTotalSalesStatistics = $this->getTotalSalesStatistics();
        }

        if ($user->can('admin_general_dashboard_new_sales')) {
            $getNewSalesCount = $this->getNewSalesCount();
        }

        if ($user->can('admin_general_dashboard_new_comments')) {
            $getNewCommentsCount = $this->getNewCommentsCount();
        }

        if ($user->can('admin_general_dashboard_new_tickets')) {
            $getNewTicketsCount = $this->getNewTicketsCount();
        }

        if ($user->can('admin_general_dashboard_new_reviews')) {
            $getPendingReviewCount = $this->getPendingReviewCount();
        }

        if ($user->can('admin_general_dashboard_sales_statistics_chart')) {
            $getMonthAndYearSalesChart = $this->getMonthAndYearSalesChart('month_of_year');
            $getMonthAndYearSalesChartStatistics = $this->getMonthAndYearSalesChartStatistics();
        }

        if ($user->can('admin_general_dashboard_recent_comments')) {
            $recentComments = $this->getRecentComments();
        }

        if ($user->can('admin_general_dashboard_recent_tickets')) {
            $recentTickets = $this->getRecentTickets();
        }

        if ($user->can('admin_general_dashboard_recent_webinars')) {
            $recentWebinars = $this->getRecentWebinars();
        }

        if ($user->can('admin_general_dashboard_recent_courses')) {
            $recentCourses = $this->getRecentCourses();
        }

        if ($user->can('admin_general_dashboard_users_statistics_chart')) {
            $usersStatisticsChart = $this->usersStatisticsChart();
        }

        $data = [
            'pageTitle' => trans('admin/main.general_dashboard_title'),
            'dailySalesTypeStatistics' => $dailySalesTypeStatistics ?? null,
            'getIncomeStatistics' => $getIncomeStatistics ?? null,
            'getTotalSalesStatistics' => $getTotalSalesStatistics ?? null,
            'getNewSalesCount' => $getNewSalesCount ?? 0,
            'getNewCommentsCount' => $getNewCommentsCount ?? 0,
            'getNewTicketsCount' => $getNewTicketsCount ?? 0,
            'getPendingReviewCount' => $getPendingReviewCount ?? 0,
            'getMonthAndYearSalesChart' => $getMonthAndYearSalesChart ?? null,
            'getMonthAndYearSalesChartStatistics' => $getMonthAndYearSalesChartStatistics ?? null,
            'recentComments' => $recentComments ?? null,
            'recentTickets' => $recentTickets ?? null,
            'recentWebinars' => $recentWebinars ?? null,
            'recentCourses' => $recentCourses ?? null,
            'usersStatisticsChart' => $usersStatisticsChart ?? null,
        ];

        return view('admin.dashboard', $data);
    }

    public function marketing()
    {
        $this->authorize('admin_marketing_dashboard_show');

        $buyerIds = Sale::whereNull('refund_at')
            ->pluck('buyer_id')
            ->toArray();
        $teacherIdsHasClass = Webinar::where('status', Webinar::$active)
            ->pluck('creator_id', 'teacher_id')
            ->toArray();
        $teacherIdsHasClass = array_merge(array_keys($teacherIdsHasClass), $teacherIdsHasClass);


        $usersWithoutPurchases = User::whereNotIn('id', array_unique($buyerIds))->count();
        $teachersWithoutClass = User::where('role_name', Role::$teacher)
            ->whereNotIn('id', array_unique($teacherIdsHasClass))
            ->count();
        $featuredClasses = FeatureWebinar::where('status', 'publish')
            ->count();

        $now = time();
        $activeDiscounts = Ticket::where('start_date', '<', $now)
            ->where('end_date', '>', $now)
            ->count();

        $getClassesStatistics = $this->getClassesStatistics();

        $getNetProfitChart = $this->getNetProfitChart();
        $getNetCertChart = $this->getNetCertChart();

        $getNetProfitStatistics = $this->getNetProfitStatistics();

        $getTopSellingClasses = $this->getTopSellingClasses();

        $getTopSellingAppointments = $this->getTopSellingAppointments();

        $getTopSellingTeachers = $this->getTopSellingTeachersAndOrganizations('teachers');

        $getTopSellingOrganizations = $this->getTopSellingTeachersAndOrganizations('organizations');

        $getMostActiveStudents = $this->getMostActiveStudents();

        $data = [
            'pageTitle' => trans('admin/main.marketing_dashboard_title'),
            'usersWithoutPurchases' => $usersWithoutPurchases,
            'teachersWithoutClass' => $teachersWithoutClass,
            'featuredClasses' => $featuredClasses,
            'activeDiscounts' => $activeDiscounts,
            'getClassesStatistics' => $getClassesStatistics,
            'getNetProfitChart' => $getNetProfitChart,
            'getNetCertChart' => $getNetCertChart,
            'getNetProfitStatistics' => $getNetProfitStatistics,
            'getTopSellingClasses' => $getTopSellingClasses,
            'getTopSellingAppointments' => $getTopSellingAppointments,
            'getTopSellingTeachers' => $getTopSellingTeachers,
            'getTopSellingOrganizations' => $getTopSellingOrganizations,
            'getMostActiveStudents' => $getMostActiveStudents,
        ];

        return view('admin.marketing_dashboard', $data);
    }

    public function academy()
    {
        // $this->authorize('admin_marketing_dashboard_show');
        //TODO:: Implement this permission

        $buyerIds = Sale::whereNull('refund_at')
            ->pluck('buyer_id')
            ->toArray();
        $teacherIdsHasClass = Webinar::where('status', Webinar::$active)
            ->pluck('creator_id', 'teacher_id')
            ->toArray();
        $teacherIdsHasClass = array_merge(array_keys($teacherIdsHasClass), $teacherIdsHasClass);

        $usersWithoutPurchases = User::where('users.role_name', Role::$user)->count();

        $teachersWithoutClass = User::where('role_name', Role::$teacher)
            ->count();

        $featuredClasses = Webinar::count();

        $now = time();
        $activeDiscounts = Ticket::where('start_date', '<', $now)
            ->where('end_date', '>', $now)
            ->count();

        $getClassesStatistics = $this->getClassesStatistics();
        //count imported completed couser to show total under ceritficate earned

        $getEnrollmentsChart = $this->getEnrollmentsChart();

        $getCertChart = $this->getNetProfitChart();

        $getNetProfitStatistics = $this->getNetProfitStatistics();

        $getTopSellingClasses = $this->getTopSellingClasses();

        $getTopSellingAppointments = $this->getTopSellingAppointments();

        $getTopSellingTeachers = $this->getTopSellingTeachersAndOrganizations('teachers');

        $getTopSellingOrganizations = $this->getTopSellingTeachersAndOrganizations('organizations');

        $getMostActiveStudents = $this->getTenMostActiveStudents();

        $getMostRecentEnrollments = $this->getMostRecentEnrollments();

        $certificatesCount = Webinar::where('status', Webinar::$active)
            ->with('certificates')
            ->count();

        $completedCoursesStatistics = $this->getCompletedCoursesStatisticsWithTotal();
        $getStudentsWithFullCourseProgress = $this->getStudentsWithFullCourseProgress();
        //  dd($getStudentsWithFullCourseProgress);



        $data = [
            'pageTitle' => "Academy Dashboard",
            'certificatesCount' => $certificatesCount,
            'usersWithoutPurchases' => $usersWithoutPurchases,
            'teachersWithoutClass' => $teachersWithoutClass,
            'featuredClasses' => $featuredClasses,
            'activeDiscounts' => $activeDiscounts,
            'getClassesStatistics' => $getClassesStatistics,
            'getEnrollmentsChart' => $getEnrollmentsChart,
            'getCertChart' => $getCertChart,
            'getNetProfitStatistics' => $getNetProfitStatistics,
            'getTopSellingClasses' => $getTopSellingClasses,
            'getTopSellingAppointments' => $getTopSellingAppointments,
            'getTopSellingTeachers' => $getTopSellingTeachers,
            'getTopSellingOrganizations' => $getTopSellingOrganizations,
            'getMostActiveStudents' => $getMostActiveStudents,
            'getMostRecentEnrollments' => $getMostRecentEnrollments,
            'getTopCompletedClasses' => $this->getTopCompletedClasses(),
            'completedCoursesStatistics' => $getStudentsWithFullCourseProgress,
        ];

        // return view('admin.marketing_dashboard', $data);
        return view('admin.academy', $data);
    }
    // app/Http/Controllers/DashboardController.php
    // app/Http/Controllers/DashboardController.php
    public function getStudentsWithFullCourseProgress()
    {
        // Get all active courses
        $courses = Webinar::where('status', Webinar::$active)
            ->with(['sales' => function ($query) {
                $query->where('type', Sale::$webinar)
                    ->with('buyer');
            }])
            ->get();

        $totalCompletions = 0;
        $studentsWithCompletions = 0;
        $students = [];

        foreach ($courses as $course) {
            foreach ($course->sales as $sale) {
                // Skip if buyer is null
                if (!$sale->buyer) {
                    continue;
                }

                $student = $sale->buyer;
                if (!isset($students[$student->id])) {
                    $students[$student->id] = [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'email' => $student->email,
                        'completed_courses' => [],
                        'completed_courses_count' => 0
                    ];
                }

                // Get progress for this user
                $filesStat = $course->getFilesLearningProgressStat($student->id);
                $sessionsStat = $course->getSessionsLearningProgressStat($student->id);
                $textLessonsStat = $course->getTextLessonsLearningProgressStat($student->id);
                $assignmentsStat = $course->getAssignmentsLearningProgressStat($student->id);
                $quizzesStat = $course->getQuizzesLearningProgressStat($student->id);

                $passed = $filesStat['passed'] + $sessionsStat['passed'] + $textLessonsStat['passed'] + $assignmentsStat['passed'] + $quizzesStat['passed'];
                $count = $filesStat['count'] + $sessionsStat['count'] + $textLessonsStat['count'] + $assignmentsStat['count'] + $quizzesStat['count'];

                if ($count > 0 && ($passed * 100) / $count >= 100) {
                    $students[$student->id]['completed_courses'][] = [
                        'course_id' => $course->id,
                        'course_title' => $course->title,
                        'progress' => 100,
                    ];
                    $students[$student->id]['completed_courses_count']++;
                    $totalCompletions++;
                }
            }
        }

        // Count students with completions
        foreach ($students as $student) {
            if ($student['completed_courses_count'] > 0) {
                $studentsWithCompletions++;
            }
        }

        return [
            'students' => array_values($students),
            'total_completions' => $totalCompletions,
            'total_students_with_completions' => $studentsWithCompletions,
        ];
    }

    public function getCourseCompletionStatistics()
    {
        // Get all users with their completed courses
        $users = User::with(['sales' => function ($query) {
            $query->where('type', Sale::$webinar)
                ->with(['webinar.courseLearnings' => function ($learningQuery) {
                    $learningQuery->where('progress', 100);
                }]);
        }])->get();

        // Process the data to get the statistics
        $statistics = [
            'users_with_completed_courses' => 0,
            'total_completed_courses' => 0,
            'user_completion_details' => []
        ];

        foreach ($users as $user) {
            $completedCoursesCount = 0;

            foreach ($user->sales as $sale) {
                // Check if this sale has any completed course learnings
                if ($sale->webinar && count($sale->webinar->courseLearnings) > 0) {
                    $completedCoursesCount++;
                }
            }

            if ($completedCoursesCount > 0) {
                $statistics['users_with_completed_courses']++;
                $statistics['total_completed_courses'] += $completedCoursesCount;

                $statistics['user_completion_details'][] = [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'completed_courses_count' => $completedCoursesCount
                ];
            }
        }

        return $statistics;
    }





    public function getTopCompletedClasses()
    {
        $courses = Webinar::where('status', Webinar::$active)
            ->withCount('certificates')
            ->orderBy('certificates_count', 'desc')
            ->limit(5)
            ->get();

        return $courses;
    }


    public function getCompletedCoursesStatisticsWithTotal()
    {
        $courses = Webinar::where('status', Webinar::$active)
            ->with(['certificates.user']) // still load users if needed in the response
            ->withCount('certificates')   // use this for accurate counts
            ->get();

        $statistics = [];
        $overallCompletions = 0;

        foreach ($courses as $course) {
            $completedCount = $course->certificates_count;

            $statistics[] = [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'completed_students_count' => $completedCount,
                'completed_students' => $course->certificates->map(function ($certificate) {
                    return [
                        'id' => $certificate->user->id,
                        'full_name' => $certificate->user->full_name,
                        'email' => $certificate->user->email,
                    ];
                }) // Ensure unique users
            ];

            $overallCompletions += $completedCount;
        }

        return [
            'courses' => $statistics,
            'total_completions_across_all_courses' => $overallCompletions,
        ];
    }

    // public function getStudentsWithFullCourseProgress()
    // {
    //     $students = User::whereHas('sales', function ($query) {
    //         $query->where('type', Sale::$webinar)
    //             ->whereHas('webinar', function ($webinarQuery) {
    //                 $webinarQuery->whereHas('courseLearnings', function ($learningQuery) {
    //                     $learningQuery->where('progress', 100);
    //                 });
    //             });
    //     })->with(['sales.webinar' => function ($query) {
    //         $query->with('courseLearnings');
    //     }])->get();

    //     return $students->map(function ($student) {
    //         return [
    //             'id' => $student->id,
    //             'name' => $student->full_name,
    //             'email' => $student->email,
    //             'courses' => $student->sales->map(function ($sale) {
    //                 return [
    //                     'course_id' => $sale->webinar->id,
    //                     'course_title' => $sale->webinar->title,
    //                     'progress' => $sale->webinar->courseLearnings->first()->progress ?? 0,
    //                 ];
    //             }),
    //         ];
    //     });
    // }

    public function getUserCourseCompletionStatistics()
    {
        $users = User::withCount(['courseLearnings as completed_courses_count' => function ($query) {
            $query->where('progress', 100);
        }])->get();

        $statistics = $users->map(function ($user) {
            return [
                'user_id' => $user->id,
                'name' => $user->full_name,
                'completed_courses_count' => $user->completed_courses_count,
            ];
        });

        $totalCompletedCourses = $statistics->sum('completed_courses_count');

        return [
            'user_statistics' => $statistics,
            'total_completed_courses' => $totalCompletedCourses,
        ];
    }

    public function getSaleStatisticsData(Request $request)
    {
        $this->authorize('admin_general_dashboard_sales_statistics_chart');

        $type = $request->get('type');

        $chart = $this->getMonthAndYearSalesChart($type);

        return response()->json([
            'code' => 200,
            'chart' => $chart
        ], 200);
    }

    public function getNetProfitChartAjax(Request $request)
    {

        $type = $request->get('type');

        $chart = $this->getNetProfitChart($type);

        return response()->json([
            'code' => 200,
            'chart' => $chart
        ], 200);
    }


    public function getEnrollmentsChartAjax(Request $request)
    {

        $type = $request->get('type');

        $chart = $this->getEnrollmentsChart($type);

        return response()->json([
            'code' => 200,
            'chart' => $chart
        ], 200);
    }


    public function getNetCertChartAjax(Request $request)
    {

        $type = $request->get('type');

        $chart = $this->getNetCertChart($type);

        return response()->json([
            'code' => 200,
            'chart' => $chart
        ], 200);
    }

    public function cacheClear()
    {
        $this->authorize('admin_clear_cache');

        Artisan::call('clear:all', [
            '--force' => true
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Website cache successfully cleared.',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
