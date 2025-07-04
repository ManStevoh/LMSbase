@extends('admin.layouts.app')

@push('libraries_top')
<link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.carousel.min.css">
<link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.theme.min.css">

@endpush

@section('content')

<section class="section">

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4> Number of Learners</h4>
                    </div>
                    <div class="card-body">
                        {{ $usersWithoutPurchases }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info"> <!-- Changed color to blue -->
                    <i class="fas fa-book"></i> <!-- Changed icon to "book" -->
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Number of Courses</h4>
                    </div>
                    <div class="card-body">
                        {{ $featuredClasses }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger"> <!-- Changed color to red -->
                    <i class="fas fa-star"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Number of Instructors</h4>
                    </div>
                    <div class="card-body">
                        {{ $teachersWithoutClass }}
                    </div>
                </div>
            </div>
        </div>

        {{-- See --}}

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-award"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>No. of Completed Certificates</h4>
                    </div>
                    <div class="card-body">
                        <!-- {{ $certificatesCount }} -->
                        {{ $completedCoursesStatistics['total_completions']}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- START OF GRAPHS -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Learners Enrollments</h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <button type="button" class="js-sale-chart-month btn btn-primary">{{trans('admin/main.month')}}</button>
                            <button type="button" class="js-sale-chart-year btn">{{trans('admin/main.year')}}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="position-relative">
                                <canvas id="netProfitChart2" height="360"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Certificates Report</h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <button type="button" class="js-sale-chart-month2 btn btn-primary">{{trans('admin/main.month')}}</button>
                            <button type="button" class="js-sale-chart-year2 btn">{{trans('admin/main.year')}}</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="position-relative">
                                <canvas id="certChart" height="360"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END OF GRAPHS -->

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Highest Completion Courses (Top 5)</h4>
                    <div class="card-header-action">
                        <a href="{{ getAdminPanelUrl() }}/certificates/course-competition" class="btn btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table class="table table-striped font-14">

                            <tr>
                                <th>#</th>
                                <th class="text-left">{{trans('admin/main.name')}}</th>
                                <th>Certificate Count</th>
                            </tr>

                            @foreach($getTopCompletedClasses as $getTopSellingClass)
                            <tr>
                                <td>{{ $getTopSellingClass->id }}</td>
                                <td>
                                    <a href="{{ $getTopSellingClass->getUrl() }}" target="_blank" class="media-body text-left">
                                        <div>{{ $getTopSellingClass->title }}</div>
                                        <div class="text-primary text-small font-600-bold">{{ trans('webinars.'.$getTopSellingClass->type) }}</div>
                                    </a>
                                </td>
                                <td>{{ $getTopSellingClass->certificates_count }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Most Active Learners (Top 10)</h4>
                    <div class="card-header-action">
                        <a href="{{ getAdminPanelUrl() }}/students?sort=register_desc" class="btn btn-sm btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table class="table table-striped font-14">
                            <tr>
                                <th>#</th>
                                <th class="text-left">{{trans('admin/main.name')}}</th>
                                <th>Completed Courses</th>
                                <th>Meetings Attended</th>

                            </tr>
                            @foreach($getMostActiveStudents as $getMostActiveStudent)
                            <tr>
                                <td>{{ $getMostActiveStudent->id }}</td>
                                <td class="text-left">{{ $getMostActiveStudent->full_name }} {{ $getMostActiveStudent->last_name }}</td>
                                <td>{{ $getMostActiveStudent->certificates_count }}</td>
                                <td>{{ $getMostActiveStudent->reserved_appointments }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>




        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Most Active Courses (Top 5)</h4>
                    <div class="card-header-action">
                        <a href="{{ getAdminPanelUrl() }}/enrollments/history" class="btn btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table class="table table-striped font-14">

                            <tr>
                                <th>#</th>
                                <th class="text-left">{{trans('admin/main.name')}}</th>
                                <th>Enrollments</th>
                            </tr>

                            @foreach($getTopSellingClasses as $getTopSellingClass)
                            <tr>
                                <td>{{ $getTopSellingClass->id }}</td>
                                <td>
                                    <a href="{{ $getTopSellingClass->getUrl() }}" target="_blank" class="media-body text-left">
                                        <div>{{ $getTopSellingClass->title }}</div>
                                        <div class="text-primary text-small font-600-bold">{{ trans('webinars.'.$getTopSellingClass->type) }}</div>
                                    </a>
                                </td>
                                <td>{{ $getTopSellingClass->sales_count }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Most Recent Enrollments (Top 10)</h4>
                    <div class="card-header-action">
                        <a href="{{ getAdminPanelUrl() }}/enrollments/history" class="btn btn-sm btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table class="table table-striped font-14">
                            <tr>
                                <th>#</th>
                                <th class="text-left">Student</th>
                                <th>Course</th>
                                <th>Ordered At</th>
                            </tr>

                            @foreach($getMostRecentEnrollments as $getMostRecentEnrollment)
                            <tr>
                                <td class="text-left">{{ $getMostRecentEnrollment->id }}</td>
                                <td class="text-left">{{ $getMostRecentEnrollment->buyer?->full_name ?? "" }} {{ $getMostRecentEnrollment->buyer?->last_name ?? "" }}</td>
                                <td>{{ $getMostRecentEnrollment->webinar?->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($getMostRecentEnrollment->created_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

    </div>
</section>

@endsection

@push('scripts_bottom')
<script src="/assets/default/vendors/chartjs/chart.min.js"></script>
<script src="/assets/admin/vendor/owl.carousel/owl.carousel.min.js"></script>

<script src="/assets/admin/js/academy_dashboard.min.js"></script>

<script>
    (function($) {
        "use strict";
        getSaleStatisticsData('day_of_month'); // Load enrollment chart
        getSaleStatisticsData2('day_of_month'); // Load certificate chart

        @if(!empty($getEnrollmentsChart))
        makeNetProfitChart('Income', @json($getEnrollmentsChart['labels']), @json($getEnrollmentsChart['data']));
        @endif

        @if(!empty($makeNetCertChart))
        makeNetCertChart('Income', @json($getCertChart['labels']), @json($getCertChart['data']));
        @endif

    })(jQuery)
</script>
@endpush
