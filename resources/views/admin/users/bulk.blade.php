@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

         @include('admin.layouts.toastr')

    <section class="section">
        <div class="section-header">
            <h1>Bulk Import</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item"><a>{{ trans('admin/main.users') }}</a>
                </div>
                <div class="breadcrumb-item">Bulk Import</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show auto-dismiss" role="alert">
                                <strong>Whoops! </strong>Something went wrong.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show auto-dismiss" role="alert">
                            <strong>Wow! </strong>{{ Session::get('success') }}<br><br>
                        </div>
                    @endif

                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-6">

                                    <a download class="btn btn-outline-primary mb-3" href="{{url('docs/LMS Users Bulk Import.csv')}}"><i class="fa fa-download"></i> Download CSV Sample</a>
                                    <form action="{{ getAdminPanelUrl() }}/users/import/bulk" method="Post" enctype="multipart/form-data">


                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label>Select CSV File</label>
                                               <div class="fields">
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" id="import_csv" name="import_csv" accept=".csv,">
                        <label class="input-group-text btn btn-outline-primary" for="import_csv"><i class="fa fa-upload"></i> Upload</label>
                    </div>
                </div>

                   @error('import_csv')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>



                                        <div class="form-group">
                                            <label>{{ trans('/admin/main.role_name') }}</label>
                                            <select class="form-control select2 @error('role_id') is-invalid @enderror" id="roleId" name="role_id">
                                                <option disabled selected>{{ trans('admin/main.select_role') }}</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role_id') === $role->id ? 'selected' :''}}>{{ $role->name }} - {{ $role->caption }}</option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group" id="groupSelect">
                                            <label class="input-label d-block">{{ trans('admin/main.group') }}</label>
                                            <select name="group_id" class="form-control select2 @error('group_id') is-invalid @enderror">
                                                <option value="" selected disabled></option>

                                                @foreach($userGroups as $userGroup)
                                                    <option value="{{ $userGroup->id }}" @if(!empty($notification) and !empty($notification->group) and $notification->group->id == $userGroup->id) selected @endif>{{ $userGroup->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">@error('group_id') {{ $message }} @enderror</div>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('/admin/main.status') }}</label>
                                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                                <option disabled selected>{{ trans('admin/main.select_status') }}</option>
                                                @foreach (\App\User::$statuses as $status)
                                                    <option
                                                        value="{{ $status }}" {{ old('status') === $status ? 'selected' :''}}>{{  $status }}</option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="text-right mt-4">
                                            <button class="btn btn-primary" type="submit">{{ trans('admin/main.submit') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
<script>
    $(document).ready(function () {
        // Auto dismiss alerts after 5 seconds
        setTimeout(function () {
            $(".auto-dismiss").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 5000); // Adjust the timeout as needed
    });
</script>

@endpush

