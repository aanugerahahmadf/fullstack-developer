<style>
    .fi-main-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 20;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-top: 1px solid rgba(229, 231, 235, 0.8);
        padding: 0.75rem 0;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }

    .dark .fi-main-footer {
        background-color: rgba(24, 24, 27, 0.98);
        border-top: 1px solid rgba(63, 63, 70, 0.5);
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
    }

    /* Padding bottom agar konten terakhir tidak tertutup footer */
    .fi-layout, .fi-main {
        padding-bottom: 3.5rem !important;
    }

    .footer-text {
        font-size: 9px;
        font-weight: 600;
        width: 100%;
        padding: 0 1rem;
        text-align: center;
        line-height: 1.4;
    }

    @media (min-width: 768px) {
        .footer-text {
            font-size: 11px;
        }
    }
</style>

<footer class="fi-main-footer">
    <div class="footer-text text-gray-600 dark:text-gray-400">
        &copy; {{ date('Y') }} PT Kilang Pertamina Internasional – Refinery Unit VI Balongan. All rights reserved.
    </div>
</footer>
