<?php /* index.php */ ?>
<?php include("header.php"); ?>

<main class="w-full">
  <section class="hero-frame">
    <div class="hero-bg" aria-hidden="true"></div>

    <div class="relative screen-center px-4">
      <div class="glass w-full max-w-[360px] p-6">
        <h1 class="text-[22px] leading-7 text-gray-800 mb-9">
          Start a Lasting <span class="font-bold" style="color:var(--brand)">Bond</span> Today.
        </h1>

        <form class="space-y-3" action="#" method="post" novalidate>
          <div>
            <label for="email" class="block text-[13px] mb-1 text-gray-700">Email</label>
            <input id="email" class="input" type="email" placeholder="example@gmail.com" required />
          </div>
          <div>
            <label for="password" class="block text-[13px] mb-1 text-gray-700">Password</label>
            <input id="password" class="input" type="password" placeholder="enter your password" required />
          </div>
          <div class="text-[11px] leading-4 text-gray-600">
            <label class="inline-flex items-start gap-2 select-none">
              <input type="checkbox" class="mt-[2px]" required />
              <span>I agree to the Terms and Conditions and Privacy Policy</span>
            </label>
          </div>
          <div class="flex items-center gap-x-9 pt-1">
            <a href="/Dating_app/register.php" class="btn btn-ghost flex-1 text-center">Register</a>
            <button type="submit" class="btn btn-pink flex-1">Login</button>
          </div>
        </form>
      </div>

      <div class="flex items-center gap-3 my-5">
        <div class="divider-line flex-1"></div>
        <span class="text-xs" style="color:#7c7c7c;">OR</span>
        <div class="divider-line flex-1"></div>
      </div>

      <div class="flex items-center justify-center gap-x-5 gap-y-2 text-gray-500">
        <a href="#" class="icon" aria-label="Instagram">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
          </svg>
        </a>
        <a href="#" class="icon" aria-label="Google">
          <svg viewBox="0 0 24 24">
            <path d="M21.6 12.227c0-.68-.061-1.333-.176-1.96H12v3.71h5.4a4.61 4.61 0 0 1-2.004 3.028v2.52h3.244c1.898-1.748 2.96-4.322 2.96-7.298Z" fill="#4285F4"/>
            <path d="M12 22c2.7 0 4.965-.894 6.62-2.41l-3.244-2.52c-.9.6-2.05.958-3.376.958-2.596 0-4.796-1.752-5.582-4.106H3.06v2.58A9.998 9.998 0 0 0 12 22Z" fill="#34A853"/>
            <path d="M6.418 13.922A6.006 6.006 0 0 1 6.09 12c0-.667.115-1.311.328-1.922V7.498H3.06A9.996 9.996 0 0 0 2 12c0 1.601.384 3.115 1.06 4.502l3.358-2.58Z" fill="#FBBC05"/>
            <path d="M12 5.88c1.468 0 2.782.505 3.82 1.494l2.865-2.865C16.96 2.894 14.7 2 12 2 8.06 2 4.7 4.264 3.06 7.498l3.358 2.58C7.204 7.724 9.404 5.88 12 5.88Z" fill="#EA4335"/>
          </svg>
        </a>
      </div>
      <?php include __DIR__ . '/partials/footer-links.php'; ?>
    </div>
    
  </section>
</main>

<?php include("footer.php"); ?>
