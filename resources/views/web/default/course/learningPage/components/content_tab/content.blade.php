@php
    $icon = '';
    $hintText= '';

    if ($type == \App\Models\WebinarChapter::$chapterSession) {
        $icon = 'video';
        $hintText = dateTimeFormat($item->date, 'j M Y  H:i') . ' | ' . $item->duration . ' ' . trans('public.min');
    } elseif ($type == \App\Models\WebinarChapter::$chapterFile) {
        $hintText = trans('update.filetype'.$item->file_type) . ($item->volume > 0 ? ' | '.$item->getVolume() : '');

        if($hintText == "update.filetypevideo") $hintText = "Video";
        $icon = $item->getIconByType();
    } elseif ($type == \App\Models\WebinarChapter::$chapterTextLesson) {
        $icon = 'file-text';
        $hintText= $item->study_time . ' ' . trans('public.min');
    }

    $checkSequenceContent = $item->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));

    $itemPersonalNote = $item->personalNote()->where('user_id', $authUser->id)->first();
    $hasPersonalNote = (!empty($itemPersonalNote) and !empty($itemPersonalNote->note));
@endphp


<div class=" d-flex align-items-start p-10 cursor-pointer {{ (!empty($checkSequenceContent) and $sequenceContentHasError) ? 'js-sequence-content-error-modal' : 'tab-item' }}"
     data-type="{{ $type }}"

     data-id="{{ $item->id }}"
     data-passed-error="{{ !empty($checkSequenceContent['all_passed_items_error']) ? $checkSequenceContent['all_passed_items_error'] : '' }}"
     data-access-days-error="{{ !empty($checkSequenceContent['access_after_day_error']) ? $checkSequenceContent['access_after_day_error'] : '' }}"
>

        <span class="chapter-icon bg-gray300 mr-10">
            <i data-feather="{{ $icon }}" class="text-gray" width="16" height="16"></i>
        </span>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center justify-content-between">
            <div class="">
                <span class="font-weight-500 font-14 text-dark-blue d-block">{{ $item->title }}</span>
                <span class="font-12 text-gray d-block">{{ $hintText }}</span>
            </div>

            @if($hasPersonalNote)
                <span class="item-personal-note-icon d-flex-center bg-gray200">
                    <i data-feather="edit-2" class="text-gray" width="14" height="14"></i>
                </span>
            @endif
        </div>

        <div class="tab-item-info mt-15">
            <p class="font-12 text-gray d-block">
                @php
                    $description = !empty($item->description) ? $item->description : (!empty($item->summary) ? $item->summary : '');
                @endphp

                {!! truncate($description, 150) !!}
            </p>

            <div class="d-flex align-items-center justify-content-between mt-15">
                <label class="mb-0 mr-10 cursor-pointer font-weight-normal font-14 text-dark-blue" for="readToggle{{ $type }}{{ $item->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" @if($sequenceContentHasError) disabled @endif
                    id="readToggle{{ $type }}{{ $item->id }}"
                    data-item-id="{{ $item->id }}"
                    data-item="{{ $type }}_id"
                    value="{{ $item->webinar_id }}"
                    class="js-passed-lesson-toggle custom-control-input"
                    @if(!empty($item->checkPassedItem())) checked @endif>
                    <label class="custom-control-label" for="readToggle{{ $type }}{{ $item->id }}"></label>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_bottom')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // console.log('[Init] DOM Content Loaded');

        // Update progress bar UI
        function updateProgressBar(progress) {
            const progressBar = document.getElementById('courseProgressBar');
            const progressText = document.getElementById('courseProgressText');
            if (progressBar && progressText) {
                progressBar.style.width = progress + '%';
                progressText.textContent = progress + '% {{ trans('update.learnt') }}';


                console.log('[Progress] Progress bar updated to:', progress + '%');
            }
        }


        // Dispatch course progress update event
        console.log('[Listener] Attaching courseProgressUpdated listener...');
        document.addEventListener('courseProgressUpdated', function (event) {
            const progress = event.detail.progress;
            updateProgressBar(progress);
        });
        console.log('[Listener] courseProgressUpdated listener attached.');

        // Handle manual toggle (user click)
        const toggleButtons = document.querySelectorAll('.js-passed-lesson-toggle');
        console.log('[Listener] Attaching toggleButtons change event listener...');
        toggleButtons.forEach(button => {
            button.addEventListener('change', function () {
                const itemId = this.dataset.itemId;
                const itemType = this.dataset.item;
                const courseId = this.value;
                const isCompleted = this.checked;

                const progressBar = document.getElementById('courseProgressBar');
                if (progressBar) {
                    progressBar.classList.add('progress-bar-animated', 'progress-bar-striped');
                }

                $.post(
                        "/course/" + courseId + "/learningStatus",
                    {
                        item: itemType,
                        item_id: itemId,
                        status: isCompleted
                    },
                    function (result) {
                        // On success, update progress
                        $.get("/course/" + courseId + "/getProgress", function (response) {
                            if (response && response.success) {
                                const progressEvent = new CustomEvent('courseProgressUpdated', {
                                    detail: {
                                        progress: response.progress
                                    }
                                });
                                document.dispatchEvent(progressEvent);

                                // Optionally refresh navbar
                                $.get(window.location.href + '?refresh_navbar=1', function (data) {
                                    const tempDiv = document.createElement('div');
                                    tempDiv.innerHTML = data;
                                    const newNavbar = tempDiv.querySelector('.learning-page-navbar');
                                    const currentNavbar = document.querySelector('.learning-page-navbar');

                                    if (newNavbar && currentNavbar) {
                                        currentNavbar.replaceWith(newNavbar);
                                    }
                                });

                                if (progressBar) {
                                    progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                                }
                            }
                        });
                    }
                ).fail(function () {
                    // Revert on failure
                    button.checked = !isCompleted;
                    if (progressBar) {
                        progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                    }
                });
            });
        });

        // Handle automatic toggle from emitted event
        console.log('[Listener] Attaching autoMarkCurrentLessonCompleted listener...');
        document.addEventListener('autoMarkCurrentLessonCompleted', function (event) {
            const { itemId, itemType } = event.detail;
            const toggleSelector = `input.js-passed-lesson-toggle[data-item-id="${itemId}"][data-item="${itemType}_id"]`;
            const toggleInput = document.querySelector(toggleSelector);


    if (!toggleInput.checked) {
        console.log('[AutoMark] Automatically toggling lesson for item:', itemId);


        toggleInput.dispatchEvent(new Event('change', { bubbles: false }));
        toggleInput.click(); // ensures label styles or JS bindings update

}

        });
    });
</script>
@endpush
{{-- @push('scripts_bottom')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Event listener for course progress updates
            document.addEventListener('courseProgressUpdated', function(event) {
                const progress = event.detail.progress;
                updateProgressBar(progress);
            });

            // Set up click handlers for lesson completion toggles
            const toggleButtons = document.querySelectorAll('.js-passed-lesson-toggle');
            toggleButtons.forEach(button => {
                button.addEventListener('change', function () {
                    const itemId = this.dataset.itemId;
                    const itemType = this.dataset.item;
                    const courseId = this.value;
                    const isCompleted = this.checked;

                    // Show a small loading indicator on the progress bar
                    const progressBar = document.getElementById('courseProgressBar');
                    if (progressBar) {
                        progressBar.classList.add('progress-bar-animated', 'progress-bar-striped');
                    }

                    $.post(
                        "/course/" + courseId + "/learningStatus",
                        {
                            item: itemType,
                            item_id: itemId,
                            status: isCompleted
                        },
                        function (result) {
                            // Get the updated progress
                            $.get("/course/" + courseId + "/getProgress", function(response) {
                                if (response && response.success) {
                                    // Dispatch custom event with progress data
                                    const progressEvent = new CustomEvent('courseProgressUpdated', {
                                        detail: {
                                            progress: response.progress
                                        }
                                    });
                                    document.dispatchEvent(progressEvent);

                                    // Option 1: Refresh only the navbar component via AJAX
                                    $.get(window.location.href + '?refresh_navbar=1', function(data) {
                                        // Extract just the navbar HTML from the response
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = data;
                                        const newNavbar = tempDiv.querySelector('.learning-page-navbar');

                                        if (newNavbar) {
                                            // Replace the existing navbar with the fresh one
                                            const currentNavbar = document.querySelector('.learning-page-navbar');
                                            if (currentNavbar) {
                                                currentNavbar.replaceWith(newNavbar);
                                            }
                                        }
                                    });

                                    // Option 2: Remove the loading indicator
                                    if (progressBar) {
                                        progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                                    }
                                }
                            });
                        }
                    ).fail(function (err) {
                        // Revert checkbox if request fails
                        button.checked = !isCompleted;

                        // Remove loading indicator
                        if (progressBar) {
                            progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                        }
                    });
                });
            });

            // Function to update the progress bar
            function updateProgressBar(progress) {
                const progressBar = document.getElementById('courseProgressBar');
                const progressText = document.getElementById('courseProgressText');
                if (progressBar && progressText) {
                    progressBar.style.width = progress + '%';
                    progressText.textContent = progress + '% {{ trans('update.learnt') }}';
                }
            }
        });
    </script>
@endpush --}}

