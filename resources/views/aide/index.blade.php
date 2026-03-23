@extends('layouts.master')

@section('title', __('messages.help'))
@section('page-title', __('messages.help'))

@section('breadcrumb')
    <li><span style="color:#6B7280;">{{ __('messages.help') }}</span></li>
@endsection

@section('content')
<div class="row g-4">

    {{-- Bannière de bienvenue --}}
    <div class="col-12">
        <div style="background:linear-gradient(135deg,#0A4D8C,#1565C0);border-radius:16px;padding:32px 36px;color:white;position:relative;overflow:hidden;">
            <div style="position:absolute;right:-20px;top:-20px;width:180px;height:180px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
            <div style="position:absolute;right:60px;bottom:-40px;width:120px;height:120px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
            <div style="position:relative;">
                <h2 style="font-weight:700;font-size:22px;margin-bottom:8px;">
                    <i class="fas fa-life-ring me-2" style="opacity:0.85;"></i>
                    Centre d'aide SIRH CHNP
                </h2>
                <p style="font-size:14px;opacity:0.85;margin-bottom:20px;">
                    Trouvez rapidement des réponses à vos questions sur l'utilisation du Système d'Information RH.
                </p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="{{ route('aide.faq') }}" style="background:rgba(255,255,255,0.2);color:white;text-decoration:none;padding:8px 18px;border-radius:8px;font-size:13.5px;font-weight:500;border:1px solid rgba(255,255,255,0.3);transition:all 150ms;"
                       onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-question-circle me-1"></i>FAQ
                    </a>
                    <a href="{{ route('support.create') }}" style="background:white;color:#0A4D8C;text-decoration:none;padding:8px 18px;border-radius:8px;font-size:13.5px;font-weight:600;transition:all 150ms;"
                       onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-headset me-1"></i>Contacter le support
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Cartes des sections --}}
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('aide.faq') }}" style="text-decoration:none;">
            <div class="card h-100" style="border-radius:12px;border:1px solid #E5E7EB;transition:all 200ms;cursor:pointer;"
                 onmouseover="this.style.borderColor='#1565C0';this.style.transform='translateY(-3px)'"
                 onmouseout="this.style.borderColor='#E5E7EB';this.style.transform='none'">
                <div class="card-body" style="padding:24px;text-align:center;">
                    <div style="width:56px;height:56px;background:#EFF6FF;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fas fa-question-circle" style="color:#0A4D8C;font-size:24px;"></i>
                    </div>
                    <h6 style="font-weight:700;color:#111827;margin-bottom:6px;">FAQ</h6>
                    <p style="font-size:12.5px;color:#6B7280;margin:0;line-height:1.5;">Questions fréquentes sur les congés, documents et votre compte.</p>
                </div>
                <div style="padding:12px 24px;border-top:1px solid #F3F4F6;font-size:12px;color:#1565C0;font-weight:500;">
                    Consulter la FAQ <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3">
        <a href="{{ route('aide.guide') }}" style="text-decoration:none;">
            <div class="card h-100" style="border-radius:12px;border:1px solid #E5E7EB;transition:all 200ms;cursor:pointer;"
                 onmouseover="this.style.borderColor='#059669';this.style.transform='translateY(-3px)'"
                 onmouseout="this.style.borderColor='#E5E7EB';this.style.transform='none'">
                <div class="card-body" style="padding:24px;text-align:center;">
                    <div style="width:56px;height:56px;background:#ECFDF5;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fas fa-book-open" style="color:#059669;font-size:24px;"></i>
                    </div>
                    <h6 style="font-weight:700;color:#111827;margin-bottom:6px;">Guide utilisateur</h6>
                    <p style="font-size:12.5px;color:#6B7280;margin:0;line-height:1.5;">Apprenez à utiliser chaque module du SIRH pas à pas.</p>
                </div>
                <div style="padding:12px 24px;border-top:1px solid #F3F4F6;font-size:12px;color:#059669;font-weight:500;">
                    Lire le guide <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3">
        <a href="{{ route('aide.raccourcis') }}" style="text-decoration:none;">
            <div class="card h-100" style="border-radius:12px;border:1px solid #E5E7EB;transition:all 200ms;cursor:pointer;"
                 onmouseover="this.style.borderColor='#D97706';this.style.transform='translateY(-3px)'"
                 onmouseout="this.style.borderColor='#E5E7EB';this.style.transform='none'">
                <div class="card-body" style="padding:24px;text-align:center;">
                    <div style="width:56px;height:56px;background:#FFFBEB;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fas fa-keyboard" style="color:#D97706;font-size:24px;"></i>
                    </div>
                    <h6 style="font-weight:700;color:#111827;margin-bottom:6px;">Raccourcis clavier</h6>
                    <p style="font-size:12.5px;color:#6B7280;margin:0;line-height:1.5;">Naviguez plus vite avec les raccourcis disponibles dans l'interface.</p>
                </div>
                <div style="padding:12px 24px;border-top:1px solid #F3F4F6;font-size:12px;color:#D97706;font-weight:500;">
                    Voir les raccourcis <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-6 col-lg-3">
        <a href="{{ route('support.create') }}" style="text-decoration:none;">
            <div class="card h-100" style="border-radius:12px;border:1px solid #E5E7EB;transition:all 200ms;cursor:pointer;"
                 onmouseover="this.style.borderColor='#EF4444';this.style.transform='translateY(-3px)'"
                 onmouseout="this.style.borderColor='#E5E7EB';this.style.transform='none'">
                <div class="card-body" style="padding:24px;text-align:center;">
                    <div style="width:56px;height:56px;background:#FEE2E2;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="fas fa-headset" style="color:#EF4444;font-size:24px;"></i>
                    </div>
                    <h6 style="font-weight:700;color:#111827;margin-bottom:6px;">Support technique</h6>
                    <p style="font-size:12.5px;color:#6B7280;margin:0;line-height:1.5;">Signalez un bug ou posez une question à l'équipe technique.</p>
                </div>
                <div style="padding:12px 24px;border-top:1px solid #F3F4F6;font-size:12px;color:#EF4444;font-weight:500;">
                    Ouvrir un ticket <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Infos rapides --}}
    <div class="col-md-8">
        <div class="card" style="border-radius:12px;border:1px solid #E5E7EB;">
            <div class="card-header d-flex align-items-center gap-2" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;padding:14px 20px;">
                <i class="fas fa-star" style="color:#F59E0B;"></i>
                <span style="font-weight:600;font-size:14px;color:#111827;">Questions les plus posées</span>
            </div>
            <div class="card-body" style="padding:0;">
                @foreach([
                    ['q' => 'Comment faire une demande de congé ?', 'link' => 'aide.faq', 'section' => 'conges'],
                    ['q' => 'Comment télécharger mon attestation de travail ?', 'link' => 'aide.faq', 'section' => 'documents'],
                    ['q' => 'Comment modifier mon mot de passe ?', 'link' => 'aide.faq', 'section' => 'compte'],
                    ['q' => 'Comment consulter mon solde de congés ?', 'link' => 'aide.faq', 'section' => 'conges'],
                    ['q' => 'Comment contacter le service RH ?', 'link' => 'aide.faq', 'section' => 'compte'],
                ] as $item)
                <a href="{{ route($item['link']) }}#{{ $item['section'] }}" style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid #F3F4F6;text-decoration:none;color:#374151;font-size:13.5px;transition:background 150ms;"
                   onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-chevron-right" style="color:#1565C0;font-size:11px;flex-shrink:0;"></i>
                    {{ $item['q'] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card" style="border-radius:12px;border:1px solid #E5E7EB;">
            <div class="card-header d-flex align-items-center gap-2" style="background:#F9FAFB;border-bottom:1px solid #E5E7EB;padding:14px 20px;">
                <i class="fas fa-phone-alt" style="color:#0A4D8C;"></i>
                <span style="font-weight:600;font-size:14px;color:#111827;">Contact direct</span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div style="margin-bottom:14px;">
                    <div style="font-size:11px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Service RH</div>
                    <a href="mailto:rh@chnp.sn" style="font-size:13.5px;color:#1565C0;text-decoration:none;">
                        <i class="fas fa-envelope me-1"></i>rh@chnp.sn
                    </a>
                </div>
                <div style="margin-bottom:14px;">
                    <div style="font-size:11px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Support technique</div>
                    <a href="mailto:support@chnp.sn" style="font-size:13.5px;color:#1565C0;text-decoration:none;">
                        <i class="fas fa-envelope me-1"></i>support@chnp.sn
                    </a>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Horaires support</div>
                    <div style="font-size:13px;color:#374151;">
                        <i class="fas fa-clock me-1 text-muted"></i>Lun–Ven : 8h–17h
                    </div>
                </div>
                <hr style="border-color:#F3F4F6;margin:16px 0;">
                <a href="{{ route('politique-confidentialite') }}" style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#6B7280;text-decoration:none;"
                   onmouseover="this.style.color='#1565C0'" onmouseout="this.style.color='#6B7280'">
                    <i class="fas fa-shield-alt"></i>Politique de confidentialité
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
