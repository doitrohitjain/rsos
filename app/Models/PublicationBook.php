<?php

namespace App\Models;

use Config;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;

class PublicationBook extends Authenticatable
{

    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        'ai_code',
        'subject_volume_id',
        'medium_id',
        'exam_year',
        'exam_month',
        'expected_enrollment_count',
        'last_year_book_stock_count',
        'required_book_count',
        'is_eligible',
        'lock_submitted',
        'user_id',
        'auto_student_count',
        'last_update_by_user_id',
        'subject_id',
        'course',
        'subject_code',
        'remaining_books_stock_count',
        'last_year_book_received_count',
        'total_books_count',
        'distributed_books_count',
        'current_books_stock_count',
        'hindi_auto_student_count',
        'english_auto_student_count',
        'hindi_last_year_book_stock_count',
        'english_last_year_book_stock_count',
        'hindi_required_book_count',
        'english_required_book_count',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array
     */

    public $booksrequrementmakerules = [
        'ai_code' => 'required|numeric',
        'course' => 'required|numeric',
        'subject_volume_id' => 'required|numeric',
        'subject_id' => 'required|numeric',
        'hindi_auto_student_count' => 'required|numeric',
        'english_auto_student_count' => 'required|numeric',
        'hindi_last_year_book_stock_count' => 'required|numeric',
        'english_last_year_book_stock_count' => 'required|numeric',
        'hindi_required_book_count' => 'required|numeric',
        'english_required_book_count' => 'required|numeric',

    ];

    public $booksrequrementmakehindirules = [
        'ai_code' => 'required|numeric',
        'course' => 'required|numeric',
        'subject_volume_id' => 'required|numeric',
        'subject_id' => 'required|numeric',
        'hindi_auto_student_count' => 'required|numeric',
        'hindi_last_year_book_stock_count' => 'required|numeric',
        'hindi_required_book_count' => 'required|numeric',
    ];

    public $booksrequrementrulemessage = [
        'ai_code.required' => 'AI code is Required.',
        'course.required' => 'Course is Required.',
        'subject_id.required' => 'Subject is Required.',
        'subject_volume_id.required' => 'Volume is Required.',
        'hindi_auto_student_count.required' => 'Hindi Enrollment Student Count is Required.',
        'english_auto_student_count.required' => 'English Enrollment Student Count is Required.',
        'hindi_last_year_book_stock_count.required' => 'Hindi Last Year Book Stock Count is Required.',
        'english_last_year_book_stock_count.required' => 'English Last Year Book Stock Count is Required.',
        'hindi_required_book_count.required' => 'Hindi Required Book Count is Required.',
        'english_required_book_count.required' => 'English Required Book Count is Required.',

    ];

    public $booksrequrementhindimessage = [
        'ai_code.required' => 'AI code is Required.',
        'course.required' => 'Course is Required.',
        'subject_id.required' => 'Subject is Required.',
        'subject_volume_id.required' => 'Volume is Required.',
        'hindi_auto_student_count.required' => 'Enrollment Student Count is Required.',
        'hindi_last_year_book_stock_count.required' => 'Last Year Book Stock Count is Required.',
        'hindi_required_book_count.required' => 'Required Book Count is Required.',
    ];


}
