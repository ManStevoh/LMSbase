@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('All User Data') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">{{ __('Reports') }}</a></div>
                <div class="breadcrumb-item">{{ __('All User Data') }}</div>
            </div>
        </div>
    </section>


    <div class="card">

       <div class="card-header d-flex justify-content-between align-items-center">
            <!-- Filter Form Section -->
            <div>
                <form method="POST" action="">
                    @csrf
                    <div class="row align-items-end g-2">
                        <div class="col">
                            <label for="fdate">From</label>
                            <input name="fdate" value="{{ $fdate }}" id="fdate" class="form-control" type="date">
                        </div>

                        {{-- search by email --}}
                        <div class="col">
                            <label for="email">{{ trans('admin/main.email') }}</label>
                            <input name="email" id="email" type="text" class="form-control" value="{{ request()->get('email') }}">
                        </div>

                        <div class="col">
                            <label for="tdate">To</label>
                            <input name="tdate" value="{{ $tdate }}" id="tdate" class="form-control" type="date">
                        </div>

                        <div class="col">
                            <!-- Hidden input to specify the action -->
                            <input type="hidden" name="action" id="action" value="filter">
                            <button class="btn btn-primary btn-sm" type="submit" onclick="document.getElementById('action').value='filter'">Filter</button>
                            @can('admin_users_export_excel')
                                <button class="btn btn-success btn-sm" type="submit" onclick="document.getElementById('action').value='export'">Export</button>
                            @endcan
                        </div>
                    </div>
                </form>

            </div>


        </div>


        <div class="card-body">
            <div class="table-responsive text-center">
                <table class="table table-striped font-14">
                    <tr>
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Full Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Registered') }}</th>
                        <th>{{ __('About') }}</th>
                        <th>{{ __('Last Activity') }}</th>
                        <th>{{ __('Last Enrollment') }}</th>
                        <th>{{ __('Last Login Country') }}</th>
                        <th>{{ __('Last Login OS') }}</th>
                        <th>{{ __('Study Time') }}</th>
                        <th>{{ __('Total Time on Platform') }}</th>
                        <th>{{ __('Courses') }}</th>
                        <th>{{ __('Certificates') }}</th>

                    </tr>

                    @foreach($all_user_data as $item)

                        <tr>
                            <td>{{ ($all_user_data->perPage() * ($all_user_data->currentPage() - 1)) + $loop->iteration }}</td>
                            {{-- <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="{{ $item->getAvatar() }}" alt="{{ $item->full_name }}">
                                    </figure>
                                    <div class="media-body ml-1">
                                        <div class="mt-0 mb-1 font-weight-bold">{{ $item->full_name }}</div>

                                        @if($item->mobile)
                                            <div class="text-primary text-small font-600-bold">{{ $item->mobile }}</div>
                                        @endif

                                        @if($item->email)
                                            <div class="text-primary text-small font-600-bold">{{ $item->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td> --}}
                            <td>{{ $item->user_name }}</td>
                            <td>
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{ $item->email }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->role }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->registered_at }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->about }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->last_activity }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->last_enrollment }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->country }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->os }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->study_time }}</div>
                                </div>
                            </td>
                           <td>
                                <div class="media-body">
                                    <div >{{ $item->total_time }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div>{{ $item->courses }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div>{{ $item->certificates }}</div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            {{ $all_user_data->appends(request()->input())->links() }}
        </div>
    </div>



@endsection
