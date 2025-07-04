<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PayoutExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\OfflineBank;
use App\Models\Payout;
use App\Models\Role;
use App\Models\Setting;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_payouts_list');

        $payoutType = $request->get('payout', 'requests'); //requests or history

        $query = Payout::query();
        if ($payoutType == 'requests') {
            $query->where('status', Payout::$waiting);
        } else {
            $query->where('status', '!=', Payout::$waiting);
        }

        $payouts = $this->filters($query, $request)
            ->paginate(10);

        $roles = Role::all();

        $offlineBanks = OfflineBank::query()
            ->orderBy('created_at', 'desc')
            ->with([
                'specifications'
            ])
            ->get();

        $data = [
            'pageTitle' => ($payoutType == 'requests') ? trans('financial.payouts_requests') : trans('financial.payouts_history'),
            'payouts' => $payouts,
            'roles' => $roles,
            'offlineBanks' => $offlineBanks
        ];

        $user_ids = $request->get('user_ids', []);

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')
                ->whereIn('id', $user_ids)->get();
        }

        return view('admin.financial.payout.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('search');
        $email = $request->get('email');
        $user_ids = $request->get('user_ids', []);
        $role_id = $request->get('role_id');
        $account_type = $request->get('account_type');
        $sort = $request->get('sort');

        // Apply date filters
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        // General search field with advanced user name logic
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                // Optional: search on parent table fields if needed
                $query->orWhere('phone', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('id_number', 'like', "%$search%");

                $query->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', "%$search%")
                          ->orWhere('middle_name', 'like', "%$search%")
                          ->orWhere('last_name', 'like', "%$search%");

                        $nameParts = explode(' ', $search);
                        if (count($nameParts) > 1) {
                            foreach ($nameParts as $part) {
                                if (strlen($part) > 1) {
                                    $q->orWhere('full_name', 'like', "%$part%")
                                      ->orWhere('middle_name', 'like', "%$part%")
                                      ->orWhere('last_name', 'like', "%$part%");
                                }
                            }

                            $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%$search%"]);
                            $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$search%"]);
                        }
                    });
                });
            });
        }

        // Search by email
        if ($email) {
            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'like', "%$email%");
            });
        }

        // User multi-select filter
        if (!empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        // Role filter
        if ($role_id) {
            $query->whereHas('user', function ($q) use ($role_id) {
                $q->where('role_id', $role_id);
            });
        }

        // Bank/Account type filter
        if ($account_type) {
            $query->where('account_type', $account_type);
        }

        // Sorting logic
        if ($sort) {
            switch ($sort) {
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        return $query;
    }


    public function reject($id)
    {
        $this->authorize('admin_payouts_reject');

        $payout = Payout::findOrFail($id);
        $payout->update(['status' => Payout::$reject]);

        return back();
    }

    public function payout($id)
    {
        $this->authorize('admin_payouts_payout');

        $payout = Payout::findOrFail($id);
        $getFinancialSettings = getFinancialSettings();

        if ($payout->user->getPayout() < $getFinancialSettings['minimum_payout']) {
            return back()->with('msg', trans('public.income_los_then_minimum_payout'));
        }

        Accounting::create([
            'creator_id' => auth()->user()->id,
            'user_id' => $payout->user_id,
            'amount' => $payout->amount,
            'type' => Accounting::$deduction,
            'type_account' => Accounting::$income,
            'description' => trans('financial.payout_request'),
            'created_at' => time(),
        ]);

        $notifyOptions = [
            '[payout.amount]' => $payout->amount,
            '[payout.account]' => $payout->account_bank_name
        ];
        sendNotification('payout_proceed', $notifyOptions, $payout->user_id);

        $payout->update(['status' => Payout::$done]);

        return back();
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_payouts_export_excel');

        $payoutType = $request->get('payout', 'requests'); //requests or history

        $query = Payout::query();
        if ($payoutType == 'requests') {
            $query->where('status', Payout::$waiting);
        } else {
            $query->where('status', '!=', Payout::$waiting);
        }

        $payouts = $this->filters($query, $request)->get();

        $export = new PayoutExport($payouts);

        $filename = ($payoutType == 'requests') ? trans('financial.payouts_requests') : trans('financial.payouts_history');

        return Excel::download($export, $filename . '.xlsx');
    }
}
