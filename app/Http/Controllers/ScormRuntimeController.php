<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScormRuntimeController extends Controller
{
    public function initialize(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response('false'); // Usuario no autenticado
        }

        $courseId = $request->input('course_id', 0);

        session([
            'scorm_data' => [
                'user_id' => $user->id,
                'student_id' => $user->email,
                'student_name' => $user->name ?? "{$user->nombres} {$user->ape_pat}",
                'course_id' => $courseId,
                'cmi.core.lesson_status' => 'not attempted',
                'cmi.core.score.raw' => '0',
                'cmi.suspend_data' => ''
            ]
        ]);

        return response('true');
    }

    public function getValue(Request $request)
    {
        $element = $request->input('element');
        $data = session('scorm_data', []);
        return response($data[$element] ?? '');
    }

    public function setValue(Request $request)
    {
        $element = $request->input('element');
        $value = $request->input('value');

        $data = session('scorm_data', []);
        $data[$element] = $value;
        session(['scorm_data' => $data]);

        return response('true');
    }

    public function commit()
    {
        $data = session('scorm_data', []);

        if (empty($data['user_id']) || empty($data['student_id'])) {
            return response('false');
        }

        DB::table('scorm_user_progress')->updateOrInsert(
            [
                'student_id' => $data['student_id'],
                'course_id' => $data['course_id'] ?? 0
            ],
            [
                'user_id' => $data['user_id'],
                'student_name' => $data['student_name'],
                'lesson_status' => $data['cmi.core.lesson_status'] ?? '',
                'score_raw' => $data['cmi.core.score.raw'] ?? '',
                'suspend_data' => $data['cmi.suspend_data'] ?? '',
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response('true');
    }

    public function finish()
    {
        $this->commit();
        session()->forget('scorm_data');
        return response('true');
    }

    public function getLastError()
    {
        return response('0');
    }

    public function getErrorString()
    {
        return response('No error');
    }

    public function getDiagnostic()
    {
        return response('');
    }
}
