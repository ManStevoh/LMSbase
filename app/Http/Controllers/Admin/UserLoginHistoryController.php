<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserLoginHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\UserLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserLoginHistoryController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('admin_user_login_history');

        $query = UserLoginHistory::query()->select(DB::raw('*, ST_AsText(location) as location'));

        $query = $this->filters($query, $request);

        $sessions = $query->orderBy('created_at', 'desc')
            ->with([
                'user'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.login_history'),
            'sessions' => $sessions
        ];

        return view('admin.users.login_history.lists.index', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $sessionStatus = $request->input('session_status');
        $search = $request->get('search');
        $email = $request->get('email');

        $query = fromAndToDateFilter($from, $to, $query, 'session_start_at');

        // Enhanced user search by name
        if (!empty($search)) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    // Search in individual name fields
                    $q->where('full_name', 'like', "%$search%")
                      ->orWhere('middle_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%");

                    // Also search for full name pattern across combined fields
                    $nameParts = explode(' ', $search);

                    // If we have multiple name parts, search for them across all name columns
                    if (count($nameParts) > 1) {
                        // For each name part, check if it exists in any of the name fields
                        foreach ($nameParts as $part) {
                            if (strlen($part) > 1) { // Ignore single-character parts
                                $q->orWhere('full_name', 'like', "%$part%")
                                  ->orWhere('middle_name', 'like', "%$part%")
                                  ->orWhere('last_name', 'like', "%$part%");
                            }
                        }

                        // Also try to match the exact full name pattern
                        $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%$search%"]);
                        $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$search%"]);
                    }
                });
            });
        }

        // Search by email
        if (!empty($email)) {
            $query->whereHas('user', function ($query) use ($email) {
                $query->where('email', 'like', "%$email%");
            });
        }

        // Filter by session status
        if (!empty($sessionStatus)) {
            if ($sessionStatus == "open") {
                $query->whereNull('session_end_at');
            } else if ($sessionStatus == "ended") {
                $query->whereNotNull('session_end_at');
            }
        }

        return $query;
    }

    public function export(Request $request)
    {
        $this->authorize('admin_user_login_history_export');

        $query = UserLoginHistory::query()->select(DB::raw('*, ST_AsText(location) as location'));

        $query = $this->filters($query, $request);

        $sessions = $query->orderBy('created_at', 'desc')
            ->with([
                'user'
            ])
            ->get();

        $export = new UserLoginHistoryExport($sessions);
        return Excel::download($export, 'user_login_history.xlsx');
    }

    public function endSession($id)
    {
        $this->authorize('admin_user_login_history_end_session');

        $session = UserLoginHistory::findOrFail($id);

        if (!empty($session)) {
            $session->update([
                'session_end_at' => time(),
                'end_session_type' => 'by_admin'
            ]);

            $sessionManager = app('session');
            $sessionManager->getHandler()->destroy($session->session_id);
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.user_login_session_successful_deleted'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }


    public function delete($id)
    {
        $this->authorize('admin_user_login_history_delete');

        $session = UserLoginHistory::findOrFail($id);

        if (!empty($session)) {
            $session->delete();
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.login_history_successful_deleted'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function endAllUserSessions($userId)
    {
        $this->authorize('admin_user_login_history_end_session');

        $sessions = UserLoginHistory::query()->where('user_id',$userId)
            ->whereNull('session_end_at')
            ->get();

        foreach ($sessions as $session) {
            $session->update([
                'session_end_at' => time(),
                'end_session_type' => 'by_admin'
            ]);

            $sessionManager = app('session');
            $sessionManager->getHandler()->destroy($session->session_id);
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.user_all_login_sessions_ended_successfully'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
