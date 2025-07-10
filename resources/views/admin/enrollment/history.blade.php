@extends('admin.layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ trans('update.enrollment_history') }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">
                <a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
            </div>
            <div class="breadcrumb-item">{{ trans('update.enrollment_history') }}</div>
        </div>
    </div>

    <div class="section-body">
        <section class="card">
            <div class="card-body">
                <form method="get" class="mb-0">
                    <div class="row">

                        {{-- Existing Filters --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.search') }}</label>
                                <input type="text" class="form-control" name="item_title" value="{{ request()->get('item_title') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.email') }}</label>
                                <input name="email" type="text" class="form-control" value="{{ request()->get('email') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                <input type="date" class="form-control" name="from" value="{{ request()->get('from') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                <input type="date" class="form-control" name="to" value="{{ request()->get('to') }}">
                            </div>
                        </div>
                        
        <div class="col-md-3">
    <div class="form-group">
        <label class="input-label">{{ trans('admin/main.course') }}</label>
        <select name="webinar_ids[]" multiple class="form-control search-webinar-select2">
            @if(!empty($webinars))
                @foreach($webinars as $webinar)
                    <option value="{{ $webinar->id }}" selected>{{ $webinar->title }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>



                        {{-- Affiliate Filters --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">Referred?</label>
                                <select name="has_affiliate" class="form-control">
                                    <option value="">All</option>
                                    <option value="yes" {{ request()->get('has_affiliate') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ request()->get('has_affiliate') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">Affiliate Code</label>
                                <input type="text" name="affiliate_code" class="form-control" value="{{ request()->get('affiliate_code') }}">
                            </div>
                        </div>

                        {{-- Country/State/LGA/Gender --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">Country</label>
                                <select name="country" data-plugin-selectTwo class="form-control">
                                    <option value="">All</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}" @if(request()->get('country') == $country) selected @endif>{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">State</label>
                                <select name="state" class="form-control">
                                    <option value="">All</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state }}" @if(request()->get('state') == $state) selected @endif>{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">LGA</label>
                                <select name="lga" class="form-control">
                                    <option value="">All</option>
                                    @foreach($lgas as $lga)
                                        <option value="{{ $lga }}" @if(request()->get('lga') == $lga) selected @endif>{{ $lga }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">All</option>
                                    <option value="male" @if(request()->get('gender') == 'male') selected @endif>Male</option>
                                    <option value="female" @if(request()->get('gender') == 'female') selected @endif>Female</option>
                                </select>
                            </div>
                        </div>

                        {{-- Instructor/Student --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.instructor') }}</label>
                                <select name="teacher_ids[]" multiple class="form-control search-user-select2" data-search-option="just_teacher_role">
                                    @if(!empty($teachers))
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" selected>{{ $teacher->full_name }} {{ $teacher->middle_name }} {{ $teacher->last_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.student') }}</label>
                                <select name="student_ids[]" multiple class="form-control search-user-select2" data-search-option="just_student_role">
                                    @if(!empty($students))
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" selected>{{ $student->full_name }} {{ $student->middle_name }} {{ $student->last_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 mt-4">
                            <input type="submit" class="btn btn-primary w-100" value="{{ trans('admin/main.show_results') }}">
                        </div>

                        <div class="col-md-3 mt-4">
                            <a href="{{ url()->current() }}" class="btn btn-secondary w-100">
                                Reset Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @can('admin_enrollment_export')
                            <a href="{{ getAdminPanelUrl() }}/enrollments/export?{{ request()->getQueryString() }}" class="btn btn-primary">
                                {{ trans('admin/main.export_xls') }}
                            </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped font-14">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('admin/main.student') }}</th>
                                        <th>Email</th>
                                        <th>Affiliate Referrer</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>LGA</th>
                                        <th>Gender</th>
                                        <th>{{ trans('admin/main.instructor') }}</th>
                                        <th>{{ trans('admin/main.item') }}</th>
                                        <th>{{ trans('admin/main.type') }}</th>
                                        <th>{{ trans('admin/main.date') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>
                                                {{ optional($sale->buyer)->full_name }} {{ optional($sale->buyer)->middle_name }} {{ optional($sale->buyer)->last_name }}
                                                <div class="text-primary text-small font-600-bold">ID : {{ optional($sale->buyer)->id }}</div>
                                            </td>
                                            <td>{{ optional($sale->buyer)->email }}</td>

                                            {{-- New: Affiliate Referrer --}}
                                            <td>
                                                @php
                                                    $ref = optional($sale->buyer)->referredBy;
                                                    $refUser = optional($ref)->affiliateUser;
                                                @endphp

                                                @if($refUser)
                                                    {{ $refUser->full_name }} {{ $refUser->middle_name }} {{ $refUser->last_name }}
                                                    <div class="text-small text-muted">Code: {{ optional($refUser->affiliateCodeRelation)->code }}</div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>{{ optional($sale->buyer)->country }}</td>
                                            <td>{{ optional($sale->buyer)->state }}</td>
                                            <td>{{ optional($sale->buyer)->lga }}</td>
                                            <td>{{ ucfirst(optional($sale->buyer)->gender) }}</td>
                                            <td>
                                                {{ $sale->item_seller }}
                                                <div class="text-primary text-small font-600-bold">ID : {{ $sale->seller_id }}</div>
                                            </td>
                                            <td>
                                                {{ $sale->item_title }}
                                                <div class="text-primary text-small font-600-bold">ID : {{ $sale->item_id }}</div>
                                            </td>
                                            <td>
                                                @if($sale->manual_added)
                                                    <span class="text-warning">{{ trans('public.manual') }}</span>
                                                @else
                                                    {{ trans('update.normal_purchased') }}
                                                @endif
                                            </td>
                                            <td>{{ dateTimeFormat($sale->created_at, 'j F Y H:i') }}</td>
                                            <td>
                                                @if(!empty($sale->refund_at))
                                                    <span class="text-warning">{{ trans('admin/main.refund') }}</span>
                                                @elseif(!$sale->access_to_purchased_item)
                                                    <span class="text-danger">{{ trans('update.access_blocked') }}</span>
                                                @else
                                                    <span class="text-success">{{ trans('admin/main.success') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('admin_sales_invoice')
                                                    @if($sale->webinar_id)
                                                        <a href="{{ getAdminPanelUrl() }}/financial/sales/{{ $sale->id }}/invoice" target="_blank">
                                                            <i class="fa fa-print" title="{{ trans('admin/main.invoice') }}"></i>
                                                        </a>
                                                    @endif
                                                @endcan

                                                @if($sale->access_to_purchased_item)
                                                    @can('admin_enrollment_block_access')
                                                        @include('admin.includes.delete_button', [
                                                            'url' => getAdminPanelUrl().'/enrollments/'. $sale->id .'/block-access',
                                                            'tooltip' => trans('update.block_access'),
                                                            'btnIcon' => 'fa-times-circle',
                                                        ])
                                                    @endcan
                                                @else
                                                    @can('admin_enrollment_enable_access')
                                                        @include('admin.includes.delete_button', [
                                                            'url' => getAdminPanelUrl().'/enrollments/'. $sale->id .'/enable-access',
                                                            'tooltip' => trans('update.enable-student-access'),
                                                            'btnClass' => 'text-success ml-1',
                                                            'btnIcon' => 'fa-check',
                                                        ])
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        {{ $sales->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
