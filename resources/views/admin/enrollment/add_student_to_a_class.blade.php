@extends('admin.layouts.app')

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


            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#fileUploadModal">
                        Bulk Enrollment
                    </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <form action="{{ getAdminPanelUrl() }}/enrollments/store" method="Post">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label class="input-label">{{trans('admin/main.class')}}</label>
                                            <select name="webinar_id" class="form-control search-webinar-select2"
                                                    data-placeholder="Search classes">

                                            </select>

                                            @error('webinar_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label d-block">{{ trans('admin/main.user') }}</label>
                                            <select name="user_id" class="form-control search-user-select2" data-placeholder="{{ trans('public.search_user') }}">

                                            </select>
                                            @error('user_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class=" mt-4">
                                            <button type="submit" class="btn btn-primary">{{ trans('admin/main.add') }}</button>
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
        <!-- Modal -->
<div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileUploadModalLabel">Upload Bulk Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <a download class="btn btn-outline-primary mb-3" href="{{url('docs/LMS Student Bulk Enrollment Import final.xlsx')}}"><i class="fa fa-download"></i> Download CSV Sample</a>
                <ul>
                    <li class="text-danger">Kindly do not change the structure of the file</li>
                    <hr>
                    <li class="text-danger">Separate multiple courses with a comma (e.g., 19,34,890), use YYYY-MM-DD for dates, ensure valid emails, and enter valid phone numbers.</li>
                </ul>

                <form id="fileUploadForm" action="{{ route('admin.bulkstudentenrollment') }}" method="POST" enctype="multipart/form-data">
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
            url: "{{ route('admin.bulkstudentenrollment') }}", // Replace with your route
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
            error: function (xhr, status, error) {
                // Handle errors
                let errors = xhr.responseJSON?.errors || {};
                let errorMessage = xhr.responseJSON.errors.file || 'An error occurred. Please try again.';
                alertMessage.removeClass('d-none alert-success').addClass('alert-danger').html(errorMessage);
                uploadButton.prop('disabled', false).html('Upload');

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
@endsection

