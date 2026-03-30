
<footer style="background: #ffffff; border-top: 1px solid #E5E7EB; padding: 1rem 1.5rem;">
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        
        {{-- ═══════════════════════════════════════════════════════════════════
             PARTIE GAUCHE - Copyright
             ═══════════════════════════════════════════════════════════════════ --}}
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <span style="font-size: 0.875rem; color: #6B7280;">
                © {{ date('Y') }} 
                <strong style="color: #0A4D8C;">SIRH CHNP</strong> 
                — Centre Hospitalier National de Pikine
            </span>
            
            <span style="color: #D1D5DB;">|</span>
            
            <span style="font-size: 0.75rem; color: #9CA3AF;">
                Version 1.0.0
            </span>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             PARTIE CENTRE - Badges Sécurité
             ═══════════════════════════════════════════════════════════════════ --}}
        <!-- <div style="display: flex; align-items: center; gap: 1rem;">
            {{-- Badge SSL/TLS --}}
            <div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #D1FAE5; border-radius: 9999px;">
                <i class="fas fa-lock" style="color: #10B981; font-size: 0.75rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600; color: #059669;">SSL/TLS</span>
            </div>

            {{-- Badge RGPD --}}
            <div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #DBEAFE; border-radius: 9999px;">
                <i class="fas fa-shield-alt" style="color: #3B82F6; font-size: 0.75rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600; color: #2563EB;">Conforme RGPD</span>
            </div>

            {{-- Badge Chiffrement --}}
            <div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: #E3F2FD; border-radius: 9999px;">
                <i class="fas fa-key" style="color: #0A4D8C; font-size: 0.75rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600; color: #0A4D8C;">AES-256</span>
            </div>
        </div> -->

        {{-- ═══════════════════════════════════════════════════════════════════
             PARTIE DROITE - Liens
             ═══════════════════════════════════════════════════════════════════ --}}
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('politique-confidentialite') }}" style="font-size: 0.75rem; color: #6B7280; text-decoration: none; transition: color 150ms;"
               onmouseover="this.style.color='#1565C0'"
               onmouseout="this.style.color='#6B7280'">
                Politique de confidentialité
            </a>

            <span style="color: #D1D5DB;">|</span>

            <a href="{{ route('support.index') }}" style="font-size: 0.75rem; color: #6B7280; text-decoration: none; transition: color 150ms;"
               onmouseover="this.style.color='#1565C0'"
               onmouseout="this.style.color='#6B7280'">
                Support
            </a>

            <span style="color: #D1D5DB;">|</span>

            <a href="{{ route('aide.index') }}" style="font-size: 0.75rem; color: #6B7280; text-decoration: none; transition: color 150ms;"
               onmouseover="this.style.color='#1565C0'"
               onmouseout="this.style.color='#6B7280'">
                Aide
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         LIGNE INFÉRIEURE - Informations développeur
         ═══════════════════════════════════════════════════════════════════════ --}}
    <!-- <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #F3F4F6; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem;">
        <div style="font-size: 0.7rem; color: #9CA3AF;">
            <i class="fas fa-code" style="margin-right: 0.25rem;"></i>
            Développé avec <span style="color: #EF4444;">♥</span> par 
            <span style="color: #6B7280; font-weight: 500;">Étudiant Master 2</span>
            — Projet de Mémoire 2025-2026
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            {{-- Stack technique --}}
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.65rem; color: #9CA3AF;">Propulsé par</span>
                <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel" style="height: 16px; opacity: 0.6;" title="Laravel 12">
                <span style="font-size: 0.65rem; color: #D1D5DB;">•</span>
                <span style="font-size: 0.65rem; color: #9CA3AF; font-weight: 500;">PHP 8.2+</span>
                <span style="font-size: 0.65rem; color: #D1D5DB;">•</span>
                <span style="font-size: 0.65rem; color: #9CA3AF; font-weight: 500;">MySQL 8.0</span>
            </div>
        </div>
    </div> -->
</footer>

{{-- Styles responsive pour le footer --}}
<style>
    @media (max-width: 768px) {
        footer > div:first-child {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        footer > div:first-child > div:nth-child(2) {
            flex-wrap: wrap;
        }
        
        footer > div:last-child {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
