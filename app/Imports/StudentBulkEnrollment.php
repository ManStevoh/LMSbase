<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Jobs\StudentBulkUploadEmailNotification;
use App\Jobs\StudentBulkEnrollmentEmailNotification;
use App\Models\CourseLearning;
use App\Models\CourseLearningLastView;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductOrder;
use App\Models\Sale;
use App\Models\Webinar;
use Google\Service\Classroom\Course;
use Illuminate\Support\Carbon;
use App\Models\WebinarAssignmentHistory;
use App\Models\QuizzesResult;
use App\Models\Bundle;
use App\Models\Product;
use Exception;

class StudentBulkEnrollment implements ToModel, WithStartRow, WithEvents
{
    protected $emailData = [];
    protected $enrollmentData = [];

    public function __construct()
    {
        $this->emailData = [];
        $this->enrollmentData = [];
    }

    // Specify the row number to start reading the data (4th row)
    public function startRow(): int
    {
        return 2;
    }
    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $this->sendEmails();
                $this->sendEnrollmentEmails();
            },
        ];
    }

    public function sendEmails()
    {
        foreach ($this->emailData as $email) {
            dispatch_sync(new StudentBulkUploadEmailNotification(
                $email['email'],
                $email['name'],
                $email['login_url'],
                $email['password']
            ));
        }
    }

    public function sendEnrollmentEmails()
    {
        foreach ($this->enrollmentData as $email) {
            dispatch_sync(new StudentBulkEnrollmentEmailNotification(
                $email['email'],
                $email['name'],
                $email['login_url'],
                $email['courses']
            ));
        }
    }
    public function model(array $row)
    {
        //If names are empty, skip the row
        if (empty($row[0]) && empty($row[1])) {
            return null;
        }
        $user = User::where('email', $row[3])->first();

        try {
            DB::beginTransaction();
            // Handle tenant creation if necessary
            $app_url = config('app.url');
            if ($app_url == 'https://devbank.sapphitalgroup.net') {
                $password = bin2hex(random_bytes(3));
                if (!$user) {
                    $user = User::create([
                        'full_name' => $row[0],
                        'middle_name' => $row[1],
                        'last_name' => $row[2],
                        'email' => $row[3],
                        'mobile' => $row[4],
                        'role_name' => 'user',
                        'role_id' => 1,
                        'password' => Hash::make($password),
                        'created_at' => time(),
                        'password_status' => false
                    ]);
                    $this->emailData[] = [
                        'email' => $user->email,
                        'name' => $user->full_name,
                        'login_url' => route('login'),
                        'password' => $password,
                    ];
                }
            } else {
                if (!$user) {
                    $password = bin2hex(random_bytes(3));
                    $user = User::create([
                        'full_name' => $row[0],
                        'middle_name' => $row[1],
                        'last_name' => $row[2],
                        'email' => $row[3],
                        'mobile' => $row[4],
                        'role_name' => 'user',
                        'role_id' => 1,
                        'password' => Hash::make($password),
                        'created_at' => time(),
                        'password_status' => false
                    ]);
                    $this->emailData[] = [
                        'email' => $user->email,
                        'name' => $user->full_name,
                        'login_url' => route('login'),
                        'password' => $password,
                    ];
                }
            }


            if (!empty($user)) {
                $sellerId = null;
                $itemType = null;
                $itemId = null;
                $itemColumnName = null;
                $checkUserHasBought = false;
                $isOwner = false;
                $product = null;


                $errors = [];

                $courseIDs = array_map('trim', explode(',', $row[5]));

                $courseNames = [];

                foreach ($courseIDs as $id) {
                    $course_inde = array_search($id, $courseIDs);
                    $data['user_id'] = $user->id;
                    $data['webinar_id'] = $id;

                    $progress = array_map('trim', explode(',', $row[6]))[$course_inde]; //

                    $visit_dates = array_map('trim', explode(',', $row[7]));
                    $visit = isset($visit_dates[$course_inde]) ? $visit_dates[$course_inde] : null;

                    $start_dates = array_map('trim', explode(',', $row[8]));
                    $start_date = isset($start_dates[$course_inde]) ? $start_dates[$course_inde] : null;

                    //$end_date = array_map('trim', explode(',', $row[9]))[$course_inde];

                    if (!empty($data['webinar_id'])) {


                        $course = Webinar::find($data['webinar_id']);
                        if (is_null($course)) {
                            throw new Exception("Invalid Course");
                        }

                        $courseNames[] = $course->slug;


                        if (!empty($course)) {
                            $sellerId = $course->creator_id;
                            $itemId = $course->id;

                            $itemType = Sale::$webinar;

                            $itemColumnName = 'webinar_id';

                            $isOwner = $course->isOwner($user->id);

                            $checkUserHasBought = $course->checkUserHasBought($user);

                            foreach ($course->files as $file) {
                                $visit_id = empty($visit) ? strtotime(now()) : Carbon::parse($visit)->timestamp;
                                CourseLearningLastView::upsert([
                                    [
                                        'user_id' => $user->id,
                                        'webinar_id' => $course->id,
                                        'visited_at' => $visit_id,
                                        'item_id' => $file->id,
                                        'item_type' => 'file',
                                    ]
                                ], ['user_id', 'webinar_id']);
                            }

                            // $this->enrollUserToCourse(course: $course, user: $user, progress: $progress, start_date: $start_date, end_date: $end_date);
                            if ($progress == 100) {
                                $this->completeCourse($course, $user->id);
                            }
                        }
                    } elseif (!empty($data['bundle_id'])) {

                        $bundle = Bundle::find($data['bundle_id']);
                        $courseNames[] = $bundle->slug;

                        if (!empty($bundle)) {
                            $sellerId = $bundle->creator_id;
                            $itemId = $bundle->id;
                            $itemType = Sale::$bundle;
                            $itemColumnName = 'bundle_id';
                            $isOwner = $bundle->isOwner($user->id);

                            $checkUserHasBought = $bundle->checkUserHasBought($user);
                        }
                    } elseif (!empty($data['product_id'])) {

                        $product = Product::find($data['product_id']);
                        $courseNames[] = $product->slug;

                        if (!empty($product)) {
                            $sellerId = $product->creator_id;
                            $itemId = $product->id;
                            $itemType = Sale::$product;
                            $itemColumnName = 'product_order_id';

                            $isOwner = ($product->creator_id == $user->id);

                            $checkUserHasBought = $product->checkUserHasBought($user);
                        }
                    }

                    if ($isOwner or $checkUserHasBought) {
                        // $errors = [
                        //     'user_id' => [trans('cart.cant_purchase_your_course')],
                        //     'webinar_id' => [trans('cart.cant_purchase_your_course')],
                        //     'bundle_id' => [trans('cart.cant_purchase_your_course')],
                        //     'product_id' => [trans('update.cant_purchase_your_product')],
                        // ];
                        continue;
                    }

                    if ((!empty($errors) or count($errors))) {
                        $errors = [
                            'user_id' => [trans('site.you_bought_webinar')],
                            'webinar_id' => [trans('site.you_bought_webinar')],
                            'bundle_id' => [trans('update.you_bought_bundle')],
                            'product_id' => [trans('update.you_bought_product')],
                        ];
                    }

                    if (!empty($errors) && count($errors)) {
                        throw new \Exception(implode(', ', array_map('implode', $errors)));
                    }

                    if (!empty($itemType) and !empty($itemId) and !empty($itemColumnName) and !empty($sellerId)) {


                        $productOrder = null;
                        if (!empty($product)) {
                            $productOrder = ProductOrder::create([
                                'product_id' => $course->id,
                                'seller_id' => $product->creator_id,
                                'buyer_id' => $user->id,
                                'specifications' => null,
                                'quantity' => 1,
                                'status' => 'pending',
                                'created_at' => time()
                            ]);

                            $itemId = $productOrder->id;
                            $itemType = Sale::$product;
                            $itemColumnName = 'product_order_id';
                        }

                        $sale = Sale::create([
                            'created_at' => !empty($start_date) ? Carbon::parse($start_date)->timestamp : now()->timestamp,
                            'buyer_id' => $user->id,
                            'seller_id' => $sellerId,
                            $itemColumnName => $itemId,
                            'type' => $itemType,
                            'manual_added' => true,
                            'payment_method' => Sale::$credit,
                            'amount' => 0,
                            'total_amount' => 0,
                            'access_to_purchased_item' => true // TODO, confirm if the imported ones should have access to the purchased item
                        ]);

                        if (!empty($product) and !empty($productOrder)) {
                            $productOrder->update([
                                'sale_id' => $sale->id,
                                'status' => $product->isVirtual() ? ProductOrder::$success : ProductOrder::$waitingDelivery,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Error processing the row: " . $e->getMessage());
        }
    }





    private function completeCourse(Webinar $course, $user_id)
    {
        $this->setFilesLearningProgressStat($course, $user_id); // done
        $this->setSessionsLearningProgressStat($course, $user_id); //done
        $this->setTextLessonsLearningProgressStat($course, $user_id); // done
        $this->setAssignmentsLearningProgressStat($course, $user_id); // done
        $this->setQuizzesLearningProgressStat($course, $user_id); // done
        $this->insertCertificate($course, $user_id);
    }

    private function insertCertificate(Webinar $webinar, $user_id)
    {
        $webinar->makeImportCertificateForUser(User::find($user_id));
    }


    private function setFilesLearningProgressStat(Webinar $webinar, $user_id)
    {
        $files = $webinar->files()
            ->where('status', 'active')
            ->get();

        foreach ($files as $file) {
            $status = CourseLearning::where('user_id', $user_id)
                ->where('file_id', $file->id)
                ->first();

            if (empty($status)) {
                CourseLearning::create([
                    'user_id' => $user_id,
                    'file_id' => $file->id,
                    'created_at' => time(),
                ]);
            }
        }
    }
    private function setSessionsLearningProgressStat($webinar, $userId)
    {
        $sessions = $webinar->sessions()
            ->where('status', 'active')
            ->get();

        foreach ($sessions as $session) {
            $status = CourseLearning::where('user_id', $userId)
                ->where('session_id', $session->id)
                ->first();

            if (empty($status)) {
                CourseLearning::create([
                    'user_id' => $userId,
                    'session_id' => $session->id,
                    'created_at' => time(),
                ]);
            }
        }
    }
    private function  setTextLessonsLearningProgressStat($webinar, $userId)
    {
        $textLessons = $webinar->textLessons()
            ->where('status', 'active')
            ->get();

        foreach ($textLessons as $textLesson) {
            $status = CourseLearning::where('user_id', $userId)
                ->where('text_lesson_id', $textLesson->id)
                ->first();

            if (empty($status)) {
                CourseLearning::create([
                    'user_id' => $userId,
                    'text_lesson_id' => $textLesson->id,
                    'created_at' => time(),
                ]);
            }
        }
    }
    private function  setAssignmentsLearningProgressStat($webinar, $userId)
    {
        $assignments = $webinar->assignments()
            ->where('status', 'active')
            ->get();

        foreach ($assignments as $assignment) {
            $assignmentHistory = WebinarAssignmentHistory::where('assignment_id', $assignment->id)
                ->where('student_id', $userId)
                ->where('status', WebinarAssignmentHistory::$passed)
                ->first();

            if (empty($assignmentHistory)) {
                WebinarAssignmentHistory::create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $userId,
                    'status' => WebinarAssignmentHistory::$passed,
                    'created_at' => time(),
                ]);
            }
        }
    }
    private function setQuizzesLearningProgressStat($webinar, $userId)
    {
        $quizzes = $webinar->quizzes()
            ->where('status', 'active')
            ->get();

        foreach ($quizzes as $quiz) {
            $quizHistory = QuizzesResult::where('quiz_id', $quiz->id)
                ->where('user_id', $userId)
                ->where('status', QuizzesResult::$passed)
                ->first();

            if (empty($quizHistory)) {
                QuizzesResult::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => $userId,
                    'status' => QuizzesResult::$passed,
                    'created_at' => time(),
                ]);
            }
        }
    }
}
