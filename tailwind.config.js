/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * SIRH SÉCURISÉ - CONFIGURATION TAILWIND CSS
 * Centre Hospitalier National de Pikine (CHNP) - Sénégal
 * ═══════════════════════════════════════════════════════════════════════════════
 * 
 * Configuration conforme à la Charte Graphique Officielle v2.0
 * Compatible avec Tailwind CSS 3.x et Laravel 12
 * 
 * ═══════════════════════════════════════════════════════════════════════════════
 */

import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    theme: {
        extend: {
            /**
             * ─────────────────────────────────────────────────────────────────────
             * COULEURS - Palette officielle SIRH CHNP
             * ─────────────────────────────────────────────────────────────────────
             */
            colors: {
                // Couleurs Primaires
                primary: {
                    DEFAULT: '#0A4D8C',     // Bleu Médical Principal
                    50: '#E3F2FD',          // Hover backgrounds
                    100: '#BBDEFB',
                    200: '#90CAF9',
                    300: '#64B5F6',
                    400: '#42A5F5',
                    500: '#1565C0',         // Bleu Sécurité (Boutons)
                    600: '#0A4D8C',         // Bleu Médical Principal
                    700: '#0D47A1',         // Bleu Profond (Badges sécurité)
                    800: '#1565C0',
                    900: '#0A4D8C',
                },

                // Couleurs d'état - Succès
                success: {
                    DEFAULT: '#10B981',
                    light: '#D1FAE5',
                    dark: '#059669',
                },

                // Couleurs d'état - Attention
                warning: {
                    DEFAULT: '#F59E0B',
                    light: '#FEF3C7',
                    dark: '#D97706',
                },

                // Couleurs d'état - Danger
                danger: {
                    DEFAULT: '#EF4444',
                    light: '#FEE2E2',
                    dark: '#DC2626',
                },

                // Couleurs d'état - Information
                info: {
                    DEFAULT: '#3B82F6',
                    light: '#DBEAFE',
                    dark: '#2563EB',
                },

                // Gris (Neutrals) - Conformes à la charte
                gray: {
                    50: '#F9FAFB',           // Fond Neutre
                    100: '#F3F4F6',
                    200: '#E5E7EB',          // Bordures
                    300: '#D1D5DB',
                    400: '#9CA3AF',
                    500: '#6B7280',          // Texte Secondaire
                    600: '#4B5563',
                    700: '#374151',          // Labels
                    800: '#1F2937',
                    900: '#111827',          // Texte Principal
                },
            },

            /**
             * ─────────────────────────────────────────────────────────────────────
             * TYPOGRAPHIE - Police Inter
             * ─────────────────────────────────────────────────────────────────────
             */
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            fontSize: {
                'micro': ['0.75rem', { lineHeight: '1.4' }],    // 12px
                'small': ['0.875rem', { lineHeight: '1.5' }],   // 14px
                'base': ['1rem', { lineHeight: '1.6' }],        // 16px
                'lg': ['1.125rem', { lineHeight: '1.4' }],      // 18px
                'xl': ['1.25rem', { lineHeight: '1.4' }],       // 20px
                '2xl': ['1.5rem', { lineHeight: '1.3' }],       // 24px
                '3xl': ['2rem', { lineHeight: '1.2' }],         // 32px
            },

            /**
             * ─────────────────────────────────────────────────────────────────────
             * ESPACEMENT - Scale 8px (multiples de 4px)
             * ─────────────────────────────────────────────────────────────────────
             */
            spacing: {
                '4.5': '1.125rem',    // 18px
                '18': '4.5rem',       // 72px
                '22': '5.5rem',       // 88px
            },

            /**
             * ─────────────────────────────────────────────────────────────────────
             * BORDURES ET ARRONDIS
             * ─────────────────────────────────────────────────────────────────────
             */
            borderRadius: {
                'sm': '0.25rem',      // 4px
                'md': '0.5rem',       // 8px - Boutons
                'lg': '0.75rem',      // 12px - Cartes
                'xl': '1rem',         // 16px
            },

            /**
             * ─────────────────────────────────────────────────────────────────────
             * OMBRES
             * ─────────────────────────────────────────────────────────────────────
             */
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
                'card-hover': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'input-focus': '0 0 0 3px rgba(21, 101, 192, 0.1)',
                'input-error': '0 0 0 3px rgba(239, 68, 68, 0.1)',
            },

            /**
             * ─────────────────────────────────────────────────────────────────────
             * LAYOUT
             * ─────────────────────────────────────────────────────────────────────
             */
            width: {
                'sidebar': '260px',
                'sidebar-collapsed': '80px',
            },

            height: {
                'header': '64px',
            },

            minHeight: {
                'screen-minus-header': 'calc(100vh - 64px)',
            },

            /**
             * ─────────────────────────────────────────────────────────────────────
             * ANIMATIONS ET TRANSITIONS
             * ─────────────────────────────────────────────────────────────────────
             */
            transitionDuration: {
                'fast': '150ms',
                'normal': '200ms',
                'slow': '300ms',
            },

            transitionTimingFunction: {
                'ease-in-out': 'ease-in-out',
            },

            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-in': 'slideIn 0.2s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },

            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideIn: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
            },
        },
    },

    /**
     * ─────────────────────────────────────────────────────────────────────────────
     * PLUGINS
     * ─────────────────────────────────────────────────────────────────────────────
     */
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
