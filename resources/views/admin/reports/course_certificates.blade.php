@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Course Certificates') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">{{ __('Reports') }}</a></div>
                <div class="breadcrumb-item">{{ __('Course Certificates') }}</div>
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
                            <label for="course">Course</label>
                            <select name="course" id="course" class="form-control select2">
                                @foreach($courses as $item)
                                    <option value="{{$item->id}}" @if($id == $item->id) selected @endif>{{$item->slug}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="fdate">From</label>
                            <input name="fdate" value="{{$fdate}}" id="fdate" class="form-control" type="date">
                        </div>
                        <div class="col">
                            <label for="tdate">To</label>
                            <input name="tdate" value="{{$tdate}}" id="tdate" class="form-control" type="date">
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

@if(!is_null($course))
        <div class="card-body">
            <div class="table-responsive text-center">
                <table class="table table-striped font-14">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Certficates') }}</th>
                    </tr>
                        <tr>
                            <td>{{ $course->slug }}</td>
                            <td>{{ $course->certificates }}</td>
                        </tr> 
                   
                </table>
            </div>
        </div>
@endif
        <div class="card-body">
            <div class="table-responsive text-center">
                <table id="exportTable" class="table table-striped font-14">
                    <tr>
                        <th>{{ __('#') }}</th>
                        <th>{{ __('User Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Avg Score') }}</th>
                        <th>{{ __('status') }}</th>
                    </tr>
                    @forelse($data as $key => $item)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{ $item->full_name . ' ' . $item->middle_name . ' ' . $item->last_name }}</td>
                            <td>
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{ $item->email }}</div>
                                </div>
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

       {{-- <div class="card-footer text-center">
            {{ $data->appends(request()->input())->links() }}
        </div> --}}
    </div>


@endsection
