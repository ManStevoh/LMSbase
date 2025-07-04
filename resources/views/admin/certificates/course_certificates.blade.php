@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            <section class="card">
                <div class="card-body">
                    <form action="{{ getAdminPanelUrl() }}/certificates/course-competition" method="get" class="row mb-0">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('admin/main.class') }}</label>
                                <select name="webinars_ids[]" multiple="multiple" class="form-control search-webinar-select2"
                                        data-placeholder="{{ trans('admin/main.search_webinar') }}">
                                    @if(!empty($webinars))
                                        @foreach($webinars as $webinar)
                                            <option value="{{ $webinar->id }}"
                                                    selected="selected">{{ $webinar ? $webinar->title : ''}}</option>
                                        @endforeach
                                    @endif
                                </select>
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
                                <label class="input-label">{{ trans('admin/main.instructor') }}</label>
                                <input type="text" name="teacher_name" class="form-control" placeholder="Search instructor by name" value="{{ request()->get('teacher_name') }}">
                                <small class="form-text text-muted">You can search by first name, middle name, or last name</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.student') }}</label>
                                <input type="text" name="student_name" class="form-control" placeholder="Search student by name" value="{{ request()->get('student_name') }}">
                                <small class="form-text text-muted">You can search by first name, middle name, or last name</small>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-primary w-100">{{ trans('public.show_results') }}</button>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                               <table class="table table-striped font-14">
                                <tr>
                                    <th>#</th>
                                    <th class="text-left">{{ trans('admin/main.title') }}</th>
                                    <th class="text-left">{{ trans('admin/main.type') }}</th>
                                    <th class="text-left">{{ trans('quiz.student') }}</th>
                                    <th class="text-left">Student Email</th>
                                    <th class="text-left">{{ trans('admin/main.instructor') }}</th>
                                    <th class="text-left">Instructor Email</th>
                                    <th class="text-center">{{ trans('public.date_time') }}</th>
                                    <th>{{ trans('admin/main.action') }}</th>
                                </tr>
                            
                                @foreach($certificates as $certificate)
                                    <tr>
                                        <td class="text-center">{{ $certificate->id }}</td>
                            
                                        <td class="text-left">
                                            @if(!empty($certificate->webinar_id))
                                                {{ $certificate->webinar->title }}
                                            @else
                                                {{ $certificate->bundle->title }}
                                            @endif
                                        </td>
                            
                                        <td class="text-left">
                                            @if(!empty($certificate->webinar_id))
                                                {{ trans('product.course') }}
                                            @else
                                                {{ trans('update.bundle') }}
                                            @endif
                                        </td>
                            
                                        <td class="text-left">
                                            {{ $certificate->student->full_name . ' ' . $certificate->student->middle_name . ' ' . $certificate->student->last_name }}
                                        </td>
                            
                                        <td class="text-left">
                                            {{ $certificate->student->email ?? '-' }}
                                        </td>
                            
                                        <td class="text-left">
                                            @php
                                                $instructor = $certificate->webinar_id ? $certificate->webinar->teacher : $certificate->bundle->teacher;
                                            @endphp
                                            {{ $instructor->full_name ?? '-' }}
                                        </td>
                            
                                        <td class="text-left">
                                            {{ $instructor->email ?? '-' }}
                                        </td>
                            
                                        <td class="text-center">{{ dateTimeFormat($certificate->created_at, 'j M Y') }}</td>
                            
                                        <td>
                                            <a href="{{ getAdminPanelUrl() }}/certificates/{{ $certificate->id }}/download" target="_blank"
                                               class="btn-transparent text-primary" data-toggle="tooltip"
                                               title="{{ trans('quiz.download_certificate') }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $certificates->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
