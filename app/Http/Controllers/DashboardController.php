<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\LaporanMasyarakat;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::whereHas('role', function($query) {
                $query->where('name', 'Admin');
            })->count(),
            'petugas_users' => User::whereHas('role', function($query) {
                $query->where('name', 'Petugas Data');
            })->count(),
            'validator_users' => User::whereHas('role', function($query) {
                $query->where('name', 'Validator');
            })->count(),
            'total_forms' => Form::count(),
            'total_submissions' => FormSubmission::count(),
            'total_laporan' => LaporanMasyarakat::count(),
            'laporan_pending' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Pending');
            })->count(),
            'laporan_ditindaklanjuti' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Ditindaklanjuti');
            })->count(),
            'laporan_selesai' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Selesai');
            })->count(),
            'laporan_ditolak' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Ditolak');
            })->count(),
        ];
        
        // Get recent submissions
        $recent_submissions = FormSubmission::with('form')
            ->latest()
            ->take(5)
            ->get();
        
        // Get recent laporan
        $recent_laporan = LaporanMasyarakat::with('status')
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboard', compact('stats', 'recent_submissions', 'recent_laporan'));
    }
    
    /**
     * Get real-time dashboard data for API.
     */
    public function getRealtimeData()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::whereHas('role', function($query) {
                $query->where('name', 'Admin');
            })->count(),
            'petugas_users' => User::whereHas('role', function($query) {
                $query->where('name', 'Petugas Data');
            })->count(),
            'validator_users' => User::whereHas('role', function($query) {
                $query->where('name', 'Validator');
            })->count(),
            'total_forms' => Form::count(),
            'total_submissions' => FormSubmission::count(),
            'total_laporan' => LaporanMasyarakat::count(),
            'laporan_pending' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Pending');
            })->count(),
            'laporan_ditindaklanjuti' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Ditindaklanjuti');
            })->count(),
            'laporan_selesai' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Selesai');
            })->count(),
            'laporan_ditolak' => LaporanMasyarakat::whereHas('status', function($query) {
                $query->where('label', 'Ditolak');
            })->count(),
        ];
        
        return response()->json($stats);
    }
}
