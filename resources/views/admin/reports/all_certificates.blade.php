@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Issued Certificates') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">{{ __('Reports') }}</a></div>
                <div class="breadcrumb-item">{{ __('Issued Certificates') }}</div>
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
                            <input name="fdate" value="{{$fdate}}" id="fdate" class="form-control" type="date">
                        </div>
                        <div class="col">
                            <label for="tdate">To</label>
                            <input name="tdate" value="{{$tdate}}" id="tdate" class="form-control" type="date">
                        </div>
                        <div class="col">
                            <button class="btn btn-primary btn-sm" type="submit">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Export Button Section -->
            <div>
                <!--@can('admin_users_export_excel')-->
                <!--    <a href="#" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>-->
                    <!--<a href="{{ getAdminPanelUrl() }}/instructors/excel?{{ http_build_query(request()->all()) }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>-->
                <!--@endcan-->
            </div>
        
            
        </div>

        <div class="card-body">
            <div class="table-responsive text-center">
                <table id="exportTable" class="table table-striped font-14">
                    <tr>
                        <th>{{ __('#') }}</th>
                        <th>{{ __('User Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Course') }}</th>
                        <th>{{ __('Avg Score') }}</th>
                        <th>{{ __('status') }}</th>
                    </tr>
                    @forelse($data as $key => $item)
                        <tr>
                            <td>{{($data->perPage() * ($data->currentPage() - 1)) + $loop->iteration}}</td>
                            <td>{{ $item->full_name . ' ' . $item->middle_name . ' ' . $item->last_name }}</br><span class="text-warning"> ID: {{ $item->id }}</span></td>
                            <td>
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{ $item->email }}</div>
                            </td>
                            <td>
                                    <div class="">{{ $item->course }}</div>
                            </td>
                            <td>
                                <div class="media-body">
                                    <div >{{ $item->grade }}</div>
                                </div>
                            </td>
                            <td>{{ $item->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No data available for the applied filters</td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            {{ $data->appends(request()->input())->links() }}
        </div> 
    </div>


@endsection
