<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAllotmentMark extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    protected $fillable = [
        "id",
        "student_allotment_id",
        "examcenter_detail_id",
        "is_supplementary",
        "enrollment",
        "fixcode",
        "course",
        "user_examiner_map_id",
        "user_id",
        "ai_code",
        "ai_code_district_id",
        "student_id",
        "subject_id",
        "reval_final_theory_marks",
        "subject_name",
        "subject_code",
        "theory_max_marks",
        "final_theory_marks",
        "theory_absent",
        "practical_max_marks",
        "final_practical_marks",
        "practical_absent",
        "sessional_marks_reil_result",
        "total_max_marks",
        "practical_examiner_id",
        "practical_examiner_district_id",
        "user_deo_id",
        "deo_district_id",
        "theory_examiner_id",
        "theory_examiner_district",
        "is_practical_lock_submit",
        "is_theory_lock_submit",
        "theory_lock_submit_user_id",
        "theory_lastpage_submitted_date",
        "is_exclude_for_theory",
        "is_exclude_for_practical",
        "is_temp_exam_subject",
        "is_update_marks_after_result",
        "is_update_theory_marks_admin",
        "update_date_theory_marks_admin",
        "is_update_practical_marks_admin",
        "update_date_practical_marks_admin",
        "created_at",
        "updated_at",
        "stream",
        "deleted_at",
        "user_practical_examiner_id",
        "exam_year",
        "exam_month"
    ];
    protected $dates = ['deleted_at'];
    protected $softDelete = true;

    public $rulesUserPracticalExaminer = [
        'student_allotment_id' => 'required'
    ];

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function students()
    {
        return $this->hasOne(Student::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function admission_subject()
    {
        return $this->hasMany(AdmissionSubject::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }

    public function exam_subject()
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function supplementary_subject()
    {
        return $this->hasMany(SupplementarySubject::class);
    }

    public function supplementary()
    {
        return $this->hasOne(Supplementary::class);
    }

    public function toc_subject()
    {
        return $this->hasMany(TocMark::class);
    }

    public function studentfees()
    {
        return $this->hasOne(StudentFee::class);
    }

    public function studentfee()
    {
        return $this->hasOne(StudentFee::class);
    }

    public function toc()
    {
        return $this->hasOne(Toc::class);
    }

    public function bankdetils()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function suppstudentfee()
    {
        return $this->hasOne(SuppStudentFee::class);
    }

    public function tocdetils()
    {
        return $this->hasOne(Toc::class);
    }

    public function studentallotment()
    {
        return $this->belongsTo(StudentAllotment::class, 'student_id', 'student_id');
    }

}
