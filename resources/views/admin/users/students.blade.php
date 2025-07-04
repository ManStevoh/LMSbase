@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.students') }} {{ trans('admin/main.list') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a>{{ trans('admin/main.students') }}</a></div>
                <div class="breadcrumb-item"><a href="#">{{ trans('admin/main.users_list') }}</a></div>
            </div>
        </div>
    </section>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.total_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalStudents }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-briefcase"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.organizations_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalOrganizationsStudents }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-info-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.inactive_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $inactiveStudents }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-ban"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.ban_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $banStudents }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="card">
            <div class="card-body">
                <form method="get" class="mb-0">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.search') }}</label>
                                <input name="full_name" type="text" class="form-control" value="{{ request()->get('full_name') }}">
                            </div>
                        </div>


                        {{-- search by email  --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.email') }}</label>
                                <input name="email" type="text" class="form-control" value="{{ request()->get('email') }}">
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                <div class="input-group">
                                    <input type="date" id="from" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                <div class="input-group">
                                    <input type="date" id="to" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.filters') }}</label>
                                <select name="sort" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.filter_type') }}</option>
                                    <option value="purchased_classes_asc" @if(request()->get('sort') == 'purchased_classes_asc') selected @endif>{{ trans('admin/main.purchased_classes_ascending') }}</option>
                                    <option value="purchased_classes_desc" @if(request()->get('sort') == 'purchased_classes_desc') selected @endif>{{ trans('admin/main.purchased_classes_descending') }}</option>

                                    <option value="purchased_classes_amount_asc" @if(request()->get('sort') == 'purchased_classes_amount_asc') selected @endif>{{ trans('admin/main.purchased_classes_amount_ascending') }}</option>
                                    <option value="purchased_classes_amount_desc" @if(request()->get('sort') == 'purchased_classes_amount_desc') selected @endif>{{ trans('admin/main.purchased_classes_amount_descending') }}</option>


                                    <option value="purchased_appointments_asc" @if(request()->get('sort') == 'purchased_appointments_asc') selected @endif>{{ trans('admin/main.purchased_appointments_ascending') }}</option>
                                    <option value="purchased_appointments_desc" @if(request()->get('sort') == 'purchased_appointments_desc') selected @endif>{{ trans('admin/main.purchased_appointments_descending') }}</option>

                                    <option value="purchased_appointments_amount_asc" @if(request()->get('sort') == 'purchased_appointments_amount_asc') selected @endif>{{ trans('admin/main.purchased_appointments_amount_ascending') }}</option>
                                    <option value="purchased_appointments_amount_desc" @if(request()->get('sort') == 'purchased_appointments_amount_desc') selected @endif>{{ trans('admin/main.purchased_appointments_amount_descending') }}</option>

                                    <option value="register_asc" @if(request()->get('sort') == 'register_asc') selected @endif>{{ trans('admin/main.register_date_ascending') }}</option>
                                    <option value="register_desc" @if(request()->get('sort') == 'register_desc') selected @endif>{{ trans('admin/main.register_date_descending') }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.organization') }}</label>
                                <select name="organization_id" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.select_organization') }}</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" @if(request()->get('organization_id') == $organization->id) selected @endif>{{ $organization->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.users_group') }}</label>
                                <select name="group_id" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.select_users_group') }}</option>
                                    @foreach($userGroups as $userGroup)
                                        <option value="{{ $userGroup->id }}" @if(request()->get('group_id') == $userGroup->id) selected @endif>{{ $userGroup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.status') }}</label>
                                <select name="status" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.all_status') }}</option>
                                    <option value="active_verified" @if(request()->get('status') == 'active_verified') selected @endif>{{ trans('admin/main.active_verified') }}</option>
                                    <option value="active_notVerified" @if(request()->get('status') == 'active_notVerified') selected @endif>{{ trans('admin/main.active_not_verified') }}</option>
                                    <option value="inactive" @if(request()->get('status') == 'inactive') selected @endif>{{ trans('admin/main.inactive') }}</option>
                                    <option value="ban" @if(request()->get('status') == 'ban') selected @endif>{{ trans('admin/main.banned') }}</option>
                                    <option value="active_verified" @if(request()->get('status') == 'active_verified') selected @endif>Active & Verified</option>
                                    <option value="active_notVerified" @if(request()->get('status') == 'active_notVerified') selected @endif>Active & Not Verified</option>
                                    <option value="inactive" @if(request()->get('status') == 'inactive') selected @endif>Inactive</option>
                                
                                    <option value="pending" @if(request()->get('status') == 'pending') selected @endif>Pending</option>
                                    
                                </select>
                            </div>
                        </div>
                        
                            {{--MORE FILTERING SYSTEM--}}                    
                        {{-- Filter by Country --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">Country</label>
                                <select name="country" data-plugin-selectTwo class="form-control populate">
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
                            <select name="state" id="stateDropdown" data-plugin-selectTwo class="form-control populate">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state }}" @if(request()->get('state') == $state) selected @endif>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                                           
                                            
                                            {{-- Filter by LGA --}}
                              <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">LGA</label>
                                    <select name="lga" id="lgaDropdown" data-plugin-selectTwo class="form-control populate">
                                        <option value="">Select LGA</option>
                                        @foreach($lgas as $lga)
                                            <option value="{{ $lga }}" @if(request()->get('lga') == $lga) selected @endif>{{ $lga }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                                            
     


                        
                        {{-- Filter by Gender --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">Gender</label>
                                <select name="gender" data-plugin-selectTwo class="form-control populate select2">
                                    <option value="">All</option>
                                    <option value="male" @if(request()->get('gender') == 'male') selected @endif>Male</option>
                                    <option value="female" @if(request()->get('gender') == 'female') selected @endif>Female</option>
                                </select>
                            </div>
                        </div>
                        

                        <div class="col-md-3">
                            <div class="form-group mt-1">
                                <label class="input-label mb-4"> </label>
                                <input type="submit" class="text-center btn btn-primary w-100" value="{{ trans('admin/main.show_results') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-1">
                                <label class="input-label mb-4"> </label>
                                                      {{-- Reset Button --}}
                                    <a href="{{ url()->current() }}" class="btn btn-warning w-100">
                                Reset Filters
                                   </a>
                            </div>
                        </div>
                       

                    </div>
                </form>
            </div>
        </section>
    </div>
    
    
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            @can('admin_users_export_excel')
                <a href="{{ getAdminPanelUrl() }}/students/excel?{{ http_build_query(request()->all()) }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#fileUploadModal">
                        Upload Students
                    </button>
            @endcan
            <div class="h-10"></div>
        </div>

        <div class="card-body">
            <div class="table-responsive text-center">
                <table class="table table-striped font-14">
                    <tr>
                        <th>ID</th>
                        <th>{{ trans('admin/main.name') }}</th>
                        <th>Gender</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>LGA</th>
                        <th>{{ trans('admin/main.classes') }}</th>
                        <th>{{ trans('admin/main.appointments') }}</th>
                        <th>{{ trans('admin/main.wallet_charge') }}</th>
                        <th>{{ trans('admin/main.income') }}</th>
                        <th>{{ trans('admin/main.user_group') }}</th>
                        <th>{{ trans('admin/main.register_date') }}</th>
                        <th>{{ trans('admin/main.status') }}</th>
                        <th width="120">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="{{ $user->getAvatar() }}" alt="{{ $user->full_name }}">
                                    </figure>
                                    <div class="media-body ml-1">
                                        <div class="mt-0 mb-1 font-weight-bold">{{ $user->full_name }} {{ $user->middle_name }} {{ $user->last_name }}</div>

                                        @if($user->mobile)
                                            <div class="text-primary text-small font-600-bold">{{ $user->mobile }}</div>
                                        @endif

                                        @if($user->email)
                                            <div class="text-primary text-small font-600-bold">{{ $user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->country }}</td>
                            <td>{{ $user->state }}</td>
                            <td>{{ $user->lga }}</td>

                            <td>
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{ $user->classesPurchasedsCount }}</div>
                                    <div class="text-small font-600-bold">{{ handlePrice($user->classesPurchasedsSum) }}</div>
                                </div>
                            </td>

                            <td>
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{ $user->meetingsPurchasedsCount }}</div>
                                    <div class="text-small font-600-bold">{{ handlePrice($user->meetingsPurchasedsSum) }}</div>
                                </div>
                            </td>

                            <td>{{ handlePrice($user->getAccountingBalance()) }}</td>

                            <td>{{ handlePrice($user->getIncome()) }}</td>

                            <td>
                                {{ !empty($user->userGroup) ? $user->userGroup->group->name : '' }}
                            </td>

                            <td>{{ dateTimeFormat($user->created_at, 'j M Y | H:i') }}</td>

                            <td>
                                @if($user->ban and !empty($user->ban_end_at) and $user->ban_end_at > time())
                                    <div class="mt-0 mb-1 font-weight-bold text-danger">{{ trans('admin/main.ban') }}</div>
                                    <div class="text-small font-600-bold">Until {{ dateTimeFormat($user->ban_end_at, 'Y/m/j') }}</div>
                                @else
                                    <div class="mt-0 mb-1 font-weight-bold {{ ($user->status == 'active') ? 'text-success' : 'text-warning' }}">{{ trans('admin/main.'.$user->status) }}</div>
                                @endif
                            </td>

                            <td class="text-center mb-2" width="120">
                                @can('admin_users_impersonate')
                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $user->id }}/impersonate" target="_blank" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.login') }}">
                                        <i class="fa fa-user-shield"></i>
                                    </a>
                                @endcan

                                @can('admin_users_edit')
                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $user->id }}/edit" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin_users_delete')
                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/users/'.$user->id.'/delete' , 'btnClass' => '', 'deleteConfirmMsg' => trans('update.user_delete_confirm_msg')])
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            {{ $users->appends(request()->input())->links() }}
        </div>
    </div>


    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.students_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.students_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.students_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.students_hint_description_2')}}</div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.students_hint_title_3')}}</div>
                        <div class="text-small font-600-bold">{{trans('admin/main.students_hint_description_3')}}</div>
                    </div>
                </div>


            </div>
        </div>
    </section>
    <!-- Modal -->
<div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileUploadModalLabel">Upload Student Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <a download class="btn btn-outline-primary mb-3" href="{{url('docs/LMS Users Bulk Import.csv')}}"><i class="fa fa-download"></i> Download CSV Sample</a>
                <form id="fileUploadForm" action="{{ route('admin.studentbulkupload') }}" method="POST" enctype="multipart/form-data">
                    @csrf <!-- Include CSRF token for Laravel -->
                    <div class="form-group">
                        <label for="fileInput">Select File</label>
                        <input type="file" class="form-control" id="fileInput" name="file" required>
                    </div>
                    <div id="alertMessage" class="alert d-none" role="alert"></div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
$(document).ready(function () {
    $('#fileUploadForm').on('submit', function (e) {
        e.preventDefault();

        // Clear previous messages
        let alertMessage = $('#alertMessage');
        alertMessage.addClass('d-none').removeClass('alert-success alert-danger').text('');

        // Disable the button and change text to "Uploading..."
        let uploadButton = $(this).find('button[type="submit"]');
        uploadButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');

        // Prepare the form data
        let formData = new FormData(this);

        // Send the AJAX request
        $.ajax({
            url: "{{ route('admin.studentbulkupload') }}", // Replace with your route
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                // Show success message
                alertMessage.removeClass('d-none alert-danger').addClass('alert-success').html('<i class="fa fa-check-circle"></i> ' + (response.message || 'File uploaded successfully.'));

                // Change button text to "Uploaded" with check-circle icon
                uploadButton.html('<i class="fa fa-check-circle"></i> Uploaded');

                // Clear the form
                $('#fileUploadForm')[0].reset();

                // Close the modal after a short delay
                setTimeout(function () {
                    $('#fileUploadModal').modal('hide');
                    alertMessage.removeClass('d-none alert-danger alert-success').html('');
                    uploadButton.prop('disabled', false).html('Upload');
                }, 3000);
            },
            error: function (xhr) {
                // Handle errors
                let errors = xhr.responseJSON?.errors || {};
                let errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                alertMessage.removeClass('d-none alert-success').addClass('alert-danger').html(errorMessage);

                // Append detailed errors if available
                if (Object.keys(errors).length) {
                    for (let [field, messages] of Object.entries(errors)) {
                        alertMessage.append(`<div>${field}: ${messages.join(', ')}</div>`);
                    }
                }

                // Re-enable the button and reset text
                uploadButton.prop('disabled', false).html('Upload');
            }
        });
    });
});

</script>





@push('scripts_bottom')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select an option',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush

@endsection
