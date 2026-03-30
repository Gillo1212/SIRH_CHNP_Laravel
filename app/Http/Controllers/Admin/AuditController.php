<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

/**
 * AuditController — Traçabilité complète (Intégrité CID)
 * Consultation, filtrage et export des journaux d'audit.
 */
class AuditController extends Controller
{
    // ─────────────────────────────────────────────────────
    // JOURNAL COMPLET
    // ─────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('audit.view');

        $query = Activity::with('causer')->latest();

        // ── Preset rapide (pills) ────────────────────────
        $type = $request->input('type', 'all');

        if ($type === 'connexions') {
            $query->where(function ($q) {
                $q->where('log_name', 'auth')
                  ->orWhere('description', 'like', '%connexion%')
                  ->orWhere('description', 'like', '%login%')
                  ->orWhere('description', 'like', '%authentification%')
                  ->orWhere('description', 'like', '%déconnexion%')
                  ->orWhere('description', 'like', '%logout%');
            });
        } elseif ($type === 'echecs') {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('log_name', 'auth')
                       ->where(function ($q3) {
                           $q3->where('description', 'like', '%échoué%')
                              ->orWhere('description', 'like', '%rejeté%')
                              ->orWhere('description', 'like', '%refus%')
                              ->orWhere('description', 'like', '%erreur%')
                              ->orWhere('description', 'like', '%bloqué%')
                              ->orWhere('description', 'like', '%interdit%')
                              ->orWhere('description', 'like', '%tentative%')
                              ->orWhere('event', 'login_failed');
                       });
                })->orWhere(function ($q2) {
                    $q2->where('description', 'like', '%403%')
                       ->orWhere('description', 'like', '%accès refusé%');
                });
            });
        }

        // ── Filtres avancés ──────────────────────────────
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                  ->where('causer_type', 'App\\Models\\User');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        // ── Statistiques globales ─────────────────────────
        $stats = [
            'total'      => Activity::count(),
            'today'      => Activity::whereDate('created_at', today())->count(),
            'this_week'  => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'events'     => Activity::select('event')->distinct()->pluck('event')->filter()->values(),
            'log_names'  => Activity::select('log_name')->distinct()->pluck('log_name')->filter()->values(),
            'subjects'   => Activity::select('subject_type')->distinct()->pluck('subject_type')->filter()
                              ->map(fn($s) => class_basename($s))->unique()->values(),
        ];

        // ── Liste utilisateurs pour le filtre ─────────────
        $users = \App\Models\User::select('id', 'login', 'name')
            ->orderBy('login')
            ->get();

        return view('admin.audit.index', compact('logs', 'stats', 'users', 'type'));
    }

    // ─────────────────────────────────────────────────────
    // CONNEXIONS
    // ─────────────────────────────────────────────────────

    public function connexions(Request $request)
    {
        $this->authorize('audit.view-connexions');

        $query = Activity::with('causer')
            ->where(function ($q) {
                $q->where('log_name', 'auth')
                  ->orWhere('description', 'like', '%connexion%')
                  ->orWhere('description', 'like', '%login%')
                  ->orWhere('description', 'like', '%authentification%')
                  ->orWhere('description', 'like', '%déconnexion%')
                  ->orWhere('description', 'like', '%logout%');
            })
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'today'        => Activity::where('log_name', 'auth')->whereDate('created_at', today())->count(),
            'cette_semaine'=> Activity::where('log_name', 'auth')->whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
        ];

        $users = \App\Models\User::select('id', 'login')->orderBy('login')->get();

        return view('admin.audit.connexions', compact('logs', 'stats', 'users'));
    }

    // ─────────────────────────────────────────────────────
    // ÉCHECS & ACTIONS SUSPECTES
    // ─────────────────────────────────────────────────────

    public function echecs(Request $request)
    {
        $this->authorize('audit.view-echecs');

        $query = Activity::with('causer')
            ->where(function ($q) {
                $q->where('log_name', 'auth')
                  ->where(function ($q2) {
                      $q2->where('description', 'like', '%échoué%')
                         ->orWhere('description', 'like', '%rejeté%')
                         ->orWhere('description', 'like', '%refus%')
                         ->orWhere('description', 'like', '%erreur%')
                         ->orWhere('description', 'like', '%bloqué%')
                         ->orWhere('description', 'like', '%interdit%')
                         ->orWhere('description', 'like', '%tentative%')
                         ->orWhere('event', 'login_failed');
                  });
            })
            ->orWhere(function ($q) {
                $q->where('description', 'like', '%403%')
                  ->orWhere('description', 'like', '%accès refusé%');
            })
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'today'    => (clone $query)->whereDate('created_at', today())->count(),
            'this_week'=> (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
        ];

        return view('admin.audit.echecs', compact('logs', 'stats'));
    }

    // ─────────────────────────────────────────────────────
    // EXPORT CSV
    // ─────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $this->authorize('audit.export');

        $query = Activity::with('causer')->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        $logs = $query->take(5000)->get();

        // Audit de l'export lui-même (Intégrité CID)
        activity('audit')
            ->causedBy($request->user())
            ->withProperties(['exported_count' => $logs->count(), 'ip' => $request->ip()])
            ->log('Export journal d\'audit : ' . $logs->count() . ' entrées');

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="audit-log-' . now()->format('Y-m-d_His') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, [
                'Date/Heure', 'Journal', 'Événement', 'Description',
                'Utilisateur', 'Sujet', 'ID Sujet', 'IP', 'User-Agent',
            ]);

            foreach ($logs as $log) {
                $props = is_array($log->properties) ? $log->properties : $log->properties?->toArray() ?? [];
                fputcsv($file, [
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->log_name ?? '—',
                    $log->event ?? '—',
                    $log->description,
                    $log->causer?->login ?? $log->causer?->name ?? '—',
                    $log->subject_type ? class_basename($log->subject_type) : '—',
                    $log->subject_id ?? '—',
                    $props['ip'] ?? '—',
                    $props['user_agent'] ?? '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
