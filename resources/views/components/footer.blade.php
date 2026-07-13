<footer class="av-footer">
    <div class="container">
        <div class="av-footer__grid">
            <div class="av-footer__brand">
                <a href="{{ route('home') }}" class="av-nav__brand" style="margin-bottom: 1rem; display: inline-flex;">
                    <span class="av-nav__brand-mark" aria-hidden="true">AV</span>
                    Artevo
                </a>
                <p>A digital museum ecosystem for preserving, verifying, exhibiting and exchanging historical artifacts — built for museums, collectors, researchers and the public.</p>
            </div>

            <div>
                <h4>Platform</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About Artevo</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4>Legal</h4>
                <ul>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                </ul>
            </div>

            <div>
                <h4>Get started</h4>
                <ul>
                    <li><a href="{{ route('register') }}">Create an account</a></li>
                    <li><a href="{{ route('login') }}">Sign in</a></li>
                </ul>
            </div>
        </div>

        <div class="av-footer__bottom">
            <span>&copy; {{ now()->year }} Artevo. All rights reserved.</span>
            <div class="av-footer__social" aria-label="Social links">
                <a href="#" aria-label="Artevo on X">X</a>
                <a href="#" aria-label="Artevo on Instagram">IG</a>
                <a href="#" aria-label="Artevo on LinkedIn">in</a>
            </div>
        </div>
    </div>
</footer>
