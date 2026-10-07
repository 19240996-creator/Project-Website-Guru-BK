<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentFuturePlan;

class FuturePlanController extends Controller
{
    public function index(Request $request)
    {
        $classes = StudentClass::orderBy('grade')->orderBy('name')->get();

        $query = Student::with(['studentClass', 'futurePlan'])
            ->where('status', 'aktif')
            ->latest();

        if ($request->filled('grade')) {
            $query->whereHas('studentClass', function ($q) use ($request) {
                $q->where('grade', $request->grade);
            });
        }

        if ($request->filled('class_id')) {
            $query->where('student_class_id', $request->class_id);
        }

        if ($request->filled('goal')) {
            if ($request->goal === 'belum_menentukan') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('futurePlans')
                      ->orWhereHas('futurePlan', function ($sub) {
                          $sub->where('primary_goal', 'belum_menentukan');
                      });
                });
            } else {
                $query->whereHas('futurePlan', function ($q) use ($request) {
                    $q->where('primary_goal', $request->goal);
                });
            }
        }

        $students = $query->paginate(20)->withQueryString();

        // Calculate distribution stats
        $allActive = Student::where('status', 'aktif')->with('futurePlan')->get();
        $totalActive = $allActive->count();
        $countKuliah = $allActive->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah')->count();
        $countKerja = $allActive->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'bekerja')->count();
        $countKuliahKerja = $allActive->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'kuliah_kerja')->count();
        $countWirausaha = $allActive->filter(fn($s) => $s->futurePlan && $s->futurePlan->primary_goal === 'wirausaha')->count();
        $countUndecided = $allActive->filter(fn($s) => !$s->futurePlan || $s->futurePlan->primary_goal === 'belum_menentukan')->count();

        return view('guru.peminatan.index', compact(
            'students',
            'classes',
            'totalActive',
            'countKuliah',
            'countKerja',
            'countKuliahKerja',
            'countWirausaha',
            'countUndecided'
        ));
    }
}
