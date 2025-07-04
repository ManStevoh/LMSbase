<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\Certificate;
use App\User;
use App\Models\UserLoginHistory;
use App\Models\Sale;
use App\Models\Translation\SettingTranslation;
use App\Models\WebinarReport;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\UserActivityExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function courseCertificate(Request $request)
    {
        $fdate = $request->fdate ?? date('Y-m-01');
        $tdate = $request->tdate ?? date('Y-m-d');
        $id = $request->course;
        $course = Webinar::find($id);

        $courses = Webinar::select(
            'id',
            'slug'
        )
            ->orderBy('slug', 'ASC')
            ->get();
        $data = User::leftJoin('certificates', 'certificates.student_id', '=', 'users.id')
            ->leftJoin('quizzes_results', 'quizzes_results.id', 'certificates.quiz_result_id')
            ->whereRaw('certificates.created_at >= UNIX_TIMESTAMP(?) ', [$fdate])
            ->whereRaw('certificates.created_at <= UNIX_TIMESTAMP(?) ', [$tdate])
            ->where('certificates.webinar_id', $id)
            ->select(
                'users.full_name as user_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.role_name as role',
                DB::raw("DATE_FORMAT(users.created_date ,'%d-%m-%Y %h:%i:%s') as registered_at"),
                DB::raw("COALESCE(certificates.user_grade,'0') as grade"),
                DB::raw("COALESCE(quizzes_results.status,'N/A') as status")
            )
            ->get();

        return view('admin.reports.course_certificates', compact('data', 'course', 'courses', 'id', 'fdate', 'tdate'));
    }
    public function issuedCertificates(Request $request)
    {
        $fdate = $request->fdate ?? date('Y-m-01');
        $tdate = $request->tdate ?? date('Y-m-d');
        $data = User::join('certificates', 'certificates.student_id', '=', 'users.id')
            ->leftJoin('quizzes_results', 'quizzes_results.id', 'certificates.quiz_result_id')
            ->leftJoin('webinars', 'webinars.id', 'certificates.webinar_id')
            ->whereRaw('certificates.created_at >= UNIX_TIMESTAMP(?) ', [$fdate])
            ->whereRaw('certificates.created_at <= UNIX_TIMESTAMP(?) ', [$tdate])
            ->select(
                'users.full_name as user_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.role_name as role',
                'users.id',
                DB::raw("DATE_FORMAT(users.created_date ,'%d-%m-%Y %h:%i:%s') as registered_at"),
                DB::raw("COALESCE(certificates.user_grade,'0') as grade"),
                DB::raw("COALESCE(quizzes_results.status,'N/A') as status"),
                DB::raw("COALESCE(webinars.slug,'N/A') as course")
            )
            ->paginate(10);

        return view('admin.reports.all_certificates', compact('data', 'fdate', 'tdate'));
    }
    public function userActivity(Request $request)
    {
        $fdate = $request->fdate ?? date('Y-m-01');
        $tdate = $request->tdate ?? date('Y-m-d');
        $action = $request->input('action');

        $login = UserLoginHistory::whereDate('user_login_histories.created_at', '>=', $fdate)
            ->whereDate('user_login_histories.created_at', '<=', $tdate)
            ->select(
                'user_id',
                DB::raw("MAX(FROM_UNIXTIME(user_login_histories.created_at, '%d-%m-%Y %h:%i:%s')) as last_activity"),
                DB::raw("MAX(user_login_histories.country) as country"),
                DB::raw("MAX(user_login_histories.os) as os"),
                DB::raw(
                    "
                        CONCAT(
                            FLOOR((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                                  MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))) / 86400), ' days, ',
                            MOD(FLOOR((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                                      MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))) / 3600), 24), ' hours, ',
                            MOD(FLOOR((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                                      MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))) / 60), 60), ' minutes, ',
                            MOD((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                                MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))), 60), ' seconds'
                        ) as study_time"
                ),
                DB::raw(
                    "
                        CONCAT(
                            FLOOR(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                      COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))) / 86400), ' days, ',
                            MOD(FLOOR(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                          COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))) / 3600), 24), ' hours, ',
                            MOD(FLOOR(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                          COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))) / 60), 60), ' minutes, ',
                            MOD(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                    COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))), 60), ' seconds'
                        ) as total_time"
                )
            )
            ->groupBy('user_id');
        $sales = Sale::whereRaw('sales.created_at >= UNIX_TIMESTAMP(?) ', [$fdate])
            ->whereRaw('sales.created_at <= UNIX_TIMESTAMP(?) ', [$tdate])
            ->select(
                'sales.buyer_id',
                DB::raw("MAX(FROM_UNIXTIME(sales.created_at, '%d-%m-%Y %h:%i:%s')) as last_enrollment"),
                DB::raw("COUNT(sales.id) as total_courses")
            )
            ->groupBy('sales.buyer_id');
        $certificates = Certificate::whereRaw('certificates.created_at >= UNIX_TIMESTAMP(?) ', [$fdate])
            ->whereRaw('certificates.created_at <= UNIX_TIMESTAMP(?) ', [$tdate])
            ->select(
                'certificates.student_id',
                DB::raw("COUNT(*) as certificates")
            )
            ->groupBy('student_id');
        $activity = User::leftJoinSub($login, 'logins', function ($join) {
            $join->on('logins.user_id', '=', 'users.id');
        })
            ->leftJoinSub($sales, 'sales', function ($join) {
                $join->on('sales.buyer_id', '=', 'users.id');
            })
            ->leftJoinSub($certificates, 'certs', function ($join) {
                $join->on('certs.student_id', '=', 'users.id');
            })
            ->select(
                'users.id',
                'users.full_name as user_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.role_name as role',
                DB::raw("DATE_FORMAT(users.created_date ,'%d-%m-%Y %h:%i:%s') as registered_at"),
                DB::raw("COALESCE(logins.last_activity, 'N/A') as last_activity"),
                DB::raw("COALESCE(logins.country, 'N/A') as country"),
                DB::raw("COALESCE(logins.os,'N/A') as os"),
                DB::raw("COALESCE(logins.study_time,'0') as study_time"),
                DB::raw("COALESCE(logins.total_time,'0') as total_time"),
                DB::raw("COALESCE(sales.total_courses,'0') as courses"),
                DB::raw("COALESCE(sales.last_enrollment,'N/A') as last_enrollment"),
                DB::raw("COALESCE(certs.certificates,'0') as certificates")
            );
        if ($action == 'export') {
            $users = $activity->get();
            $webinarExport = new UserActivityExport($users);

            return Excel::download($webinarExport, 'user_activity_report.xlsx');
        }
        $activities = $activity->paginate(10);
        return view('admin.reports.user_activities', compact('activities', 'fdate', 'tdate'));
    }

    public function allUserData(Request $request)
    {
        $fdate = $request->fdate ?? date('Y-m-01');
        $tdate = $request->tdate ?? date('Y-m-d');
        $email = $request->email;
        $action = $request->input('action');

        $login = UserLoginHistory::whereDate('user_login_histories.created_at', '>=', $fdate)
            ->whereDate('user_login_histories.created_at', '<=', $tdate)
            ->select(
                'user_id',
                DB::raw("MAX(FROM_UNIXTIME(user_login_histories.created_at, '%d-%m-%Y %h:%i:%s')) as last_activity"),
                DB::raw("MAX(user_login_histories.country) as country"),
                DB::raw("MAX(user_login_histories.os) as os"),
                DB::raw("
                    CONCAT(
                        FLOOR((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                              MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))) / 86400), ' days, ',
                        MOD(FLOOR((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                                  MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))) / 3600), 24), ' hours, ',
                        MOD(FLOOR((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                                  MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))) / 60), 60), ' minutes, ',
                        MOD((MAX(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW()))) -
                            MAX(COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW())))), 60), ' seconds'
                    ) as study_time
                "),
                DB::raw("
                    CONCAT(
                        FLOOR(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                  COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))) / 86400), ' days, ',
                        MOD(FLOOR(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                      COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))) / 3600), 24), ' hours, ',
                        MOD(FLOOR(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                      COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))) / 60), 60), ' minutes, ',
                        MOD(SUM(COALESCE(user_login_histories.session_end_at, UNIX_TIMESTAMP(NOW())) -
                                COALESCE(user_login_histories.session_start_at, UNIX_TIMESTAMP(NOW()))), 60), ' seconds'
                    ) as total_time
                ")
            )
            ->groupBy('user_id');

        $sales = Sale::whereRaw('sales.created_at >= UNIX_TIMESTAMP(?) ', [$fdate])
            ->whereRaw('sales.created_at <= UNIX_TIMESTAMP(?) ', [$tdate])
            ->select(
                'sales.buyer_id',
                DB::raw("MAX(FROM_UNIXTIME(sales.created_at, '%d-%m-%Y %h:%i:%s')) as last_enrollment"),
                DB::raw("COUNT(sales.id) as total_courses")
            )
            ->groupBy('sales.buyer_id');

        $certificates = Certificate::whereRaw('certificates.created_at >= UNIX_TIMESTAMP(?) ', [$fdate])
            ->whereRaw('certificates.created_at <= UNIX_TIMESTAMP(?) ', [$tdate])
            ->select(
                'certificates.student_id',
                DB::raw("COUNT(*) as certificates")
            )
            ->groupBy('student_id');

        $all_user_data = User::leftJoinSub($login, 'logins', function ($join) {
                $join->on('logins.user_id', '=', 'users.id');
            })
            ->leftJoinSub($sales, 'sales', function ($join) {
                $join->on('sales.buyer_id', '=', 'users.id');
            })
            ->leftJoinSub($certificates, 'certs', function ($join) {
                $join->on('certs.student_id', '=', 'users.id');
            })
            ->select(
                'users.id',
                'users.full_name as user_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.role_name as role',
                DB::raw("DATE_FORMAT(users.created_date ,'%d-%m-%Y %h:%i:%s') as registered_at"),
                DB::raw("COALESCE(logins.last_activity, 'N/A') as last_activity"),
                DB::raw("COALESCE(logins.country, 'N/A') as country"),
                DB::raw("COALESCE(logins.os,'N/A') as os"),
                DB::raw("COALESCE(logins.study_time,'0') as study_time"),
                DB::raw("COALESCE(logins.total_time,'0') as total_time"),
                DB::raw("COALESCE(sales.total_courses,'0') as courses"),
                DB::raw("COALESCE(sales.last_enrollment,'N/A') as last_enrollment"),
                DB::raw("COALESCE(certs.certificates,'0') as certificates")
            );

        // ✅ Apply email filter
        if (!empty($email)) {
            $all_user_data->where('users.email', 'like', "%$email%");
        }

        // Export to Excel if requested
        if ($action == 'export') {
            $users = $all_user_data->get();
            $webinarExport = new UserActivityExport($users);
            return Excel::download($webinarExport, 'user_activity_report.xlsx');
        }

        // Paginated results
        $all_user_data = $all_user_data->paginate(10);

        return view('admin.reports.all_user_data', compact('all_user_data', 'fdate', 'tdate'));
    }


    public function reasons(Request $request)
    {
        $this->authorize('admin_report_reasons');

        $value = [];

        $settings = Setting::where('name', 'report_reasons')->first();

        $locale = $request->get('locale', getDefaultLocale());
        storeContentLocale($locale, $settings->getTable(), $settings->id);

        if (!empty($settings) and !empty($settings->value)) {
            $value = json_decode($settings->value, true);
        }


        $data = [
            'pageTitle' => trans('admin/pages/setting.report_reasons'),
            'value' => $value,
        ];


        return view('admin.reports.reasons', $data);
    }

    public function storeReasons(Request $request)
    {
        $this->authorize('admin_report_reasons');

        $name = 'report_reasons';

        $values = $request->get('value', null);

        if (!empty($values)) {
            $locale = $request->get('locale', getDefaultLocale());

            $values = array_filter($values, function ($val) {
                if (is_array($val)) {
                    return array_filter($val);
                } else {
                    return !empty($val);
                }
            });

            $values = json_encode($values);
            $values = str_replace('record', rand(1, 600), $values);

            $settings = Setting::updateOrCreate(
                ['name' => $name],
                [
                    'updated_at' => time(),
                ]
            );

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $settings->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => $values,
                ]
            );

            cache()->forget('settings.' . $name);
        }

        removeContentLocale();

        return back();
    }

    public function webinarsReports()
    {
        $this->authorize('admin_webinar_reports');

        $reports = WebinarReport::with(['user' => function ($query) {
            $query->select('id', 'full_name');
        }, 'webinar' => function ($query) {
            $query->select('id', 'slug');
        }])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/comments.classes_reports'),
            'reports' => $reports
        ];

        return view('admin.webinars.reports', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_webinar_reports_delete');

        $report = WebinarReport::findOrFail($id);

        $report->delete();

        return redirect()->back();
    }
}
