<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Certificate;
use App\Models\Webinar;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WebinarCertificateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_course_certificate_list');

        $query = Certificate::query();
        $query->where(function (Builder $query) {
            $query->whereNotNull('webinar_id');
            $query->orWhereNotNull('bundle_id');
        });

        $query = $this->filters($query, $request);

        $certificates = $query->with([
            'webinar',
            'bundle',
            'student',
        ])->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('update.competition_certificates'),
            'certificates' => $certificates,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');
        $webinarsIds = $request->get('webinars_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        if (!empty($webinarsIds)) {
            $data['webinars'] = Webinar::select('id')
                ->whereIn('id', $webinarsIds)->get();
        }

        return view('admin.certificates.course_certificates', $data);
    }

    // private function filters($query, $request)
    // {
    //     $filters = $request->all();

    //     if(!empty($filters['email'])){
    //         $query->whereHas('student', function ($query) use ($filters) {
    //             $query->where('email', 'like', '%' . $filters['email'] . '%');
    //         });
    //     }

    //     if (!empty($filters['student_ids'])) {
    //         $query->whereIn('student_id', $filters['student_ids']);
    //     }

    //     if (!empty($filters['teacher_ids'])) {
    //         $webinarsIds = Webinar::where(function ($query) use ($filters) {
    //             $query->whereIn('creator_id', $filters['teacher_ids']);
    //             $query->orWhereIn('teacher_id', $filters['teacher_ids']);
    //         })
    //             ->pluck('id')->toArray();

    //         if ($webinarsIds and is_array($webinarsIds)) {
    //             $query->whereIn('webinar_id', $webinarsIds);
    //         }
    //     }

    //     if (!empty($filters['webinars_ids'])) {
    //         $query->whereIn('webinar_id', $filters['webinars_ids']);
    //     }

    //     return $query;
    // }

    private function filters($query, $request)
    {
        $filters = $request->all();

        // Email search
        if (!empty($filters['email'])) {
            $query->whereHas('student', function ($query) use ($filters) {
                $query->where('email', 'like', '%' . $filters['email'] . '%');
            });
        }

        // Student search - enhanced with full name search
        if (!empty($filters['student_ids'])) {
            $query->whereIn('student_id', $filters['student_ids']);
        } elseif (!empty($filters['student_name'])) {
            // If we have a student name but no IDs selected from dropdown
            $studentName = $filters['student_name'];

            $query->whereHas('student', function ($query) use ($studentName) {
                $query->where(function ($q) use ($studentName) {
                    // Search in individual name fields
                    $q->where('full_name', 'like', "%$studentName%")
                      ->orWhere('middle_name', 'like', "%$studentName%")
                      ->orWhere('last_name', 'like', "%$studentName%");

                    // Also search for full name pattern across combined fields
                    $nameParts = explode(' ', $studentName);

                    // If we have multiple name parts, search for them across all name columns
                    if (count($nameParts) > 1) {
                        // For each name part, check if it exists in any of the name fields
                        foreach ($nameParts as $part) {
                            if (strlen($part) > 1) { // Ignore single-character parts
                                $q->orWhere('full_name', 'like', "%$part%")
                                  ->orWhere('middle_name', 'like', "%$part%")
                                  ->orWhere('last_name', 'like', "%$part%");
                            }
                        }

                        // Also try to match the exact full name pattern
                        $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%$studentName%"]);
                        $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$studentName%"]);
                    }
                });
            });
        }

        // Teacher/Instructor search - enhanced with full name search
        if (!empty($filters['teacher_ids'])) {
            $webinarsIds = Webinar::where(function ($query) use ($filters) {
                $query->whereIn('creator_id', $filters['teacher_ids']);
                $query->orWhereIn('teacher_id', $filters['teacher_ids']);
            })
            ->pluck('id')->toArray();

            if ($webinarsIds && is_array($webinarsIds)) {
                $query->whereIn('webinar_id', $webinarsIds);
            }
        } elseif (!empty($filters['teacher_name'])) {
            // If we have a teacher name but no IDs selected from dropdown
            $teacherName = $filters['teacher_name'];

            // First, find webinars that have teachers matching the name criteria
            $query->whereHas('webinar', function ($webinarQuery) use ($teacherName) {
                $webinarQuery->whereHas('teacher', function ($teacherQuery) use ($teacherName) {
                    $teacherQuery->where(function ($q) use ($teacherName) {
                        // Search in individual name fields
                        $q->where('full_name', 'like', "%$teacherName%")
                          ->orWhere('middle_name', 'like', "%$teacherName%")
                          ->orWhere('last_name', 'like', "%$teacherName%");

                        // Also search for full name pattern across combined fields
                        $nameParts = explode(' ', $teacherName);

                        // If we have multiple name parts, search for them across all name columns
                        if (count($nameParts) > 1) {
                            // For each name part, check if it exists in any of the name fields
                            foreach ($nameParts as $part) {
                                if (strlen($part) > 1) { // Ignore single-character parts
                                    $q->orWhere('full_name', 'like', "%$part%")
                                      ->orWhere('middle_name', 'like', "%$part%")
                                      ->orWhere('last_name', 'like', "%$part%");
                                }
                            }

                            // Also try to match the exact full name pattern
                            $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%$teacherName%"]);
                            $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$teacherName%"]);
                        }
                    });
                })
                ->orWhereHas('creator', function ($creatorQuery) use ($teacherName) {
                    $creatorQuery->where(function ($q) use ($teacherName) {
                        // Search in individual name fields
                        $q->where('full_name', 'like', "%$teacherName%")
                          ->orWhere('middle_name', 'like', "%$teacherName%")
                          ->orWhere('last_name', 'like', "%$teacherName%");

                        // Also search for full name pattern across combined fields
                        $nameParts = explode(' ', $teacherName);

                        // If we have multiple name parts, search for them across all name columns
                        if (count($nameParts) > 1) {
                            // For each name part, check if it exists in any of the name fields
                            foreach ($nameParts as $part) {
                                if (strlen($part) > 1) { // Ignore single-character parts
                                    $q->orWhere('full_name', 'like', "%$part%")
                                      ->orWhere('middle_name', 'like', "%$part%")
                                      ->orWhere('last_name', 'like', "%$part%");
                                }
                            }

                            // Also try to match the exact full name pattern
                            $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", ["%$teacherName%"]);
                            $q->orWhereRaw("CONCAT(full_name, ' ', COALESCE(last_name, '')) LIKE ?", ["%$teacherName%"]);
                        }
                    });
                });
            });
        }

        // Webinar/Course search
        if (!empty($filters['webinars_ids'])) {
            $query->whereIn('webinar_id', $filters['webinars_ids']);
        }

        return $query;
    }

    public function show($certificateId)
    {
        $this->authorize('admin_course_certificate_list');

        $certificate = Certificate::findOrFail($certificateId);

        if ($certificate->type == 'course') {
            $makeCertificate = new MakeCertificate();

            return $makeCertificate->makeCourseCertificate($certificate);
        }

        abort(404);
    }
}
