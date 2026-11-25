<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e(websiteTitle('Admin Login')); ?></title>
    <link rel="shortcut icon" href="<?php echo e(asset(general()->favicon())); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #475467; background: #F4F5FA; display: grid; grid-template-columns: 1.1fr 1fr; }
        a { color: #003080; text-decoration: none; }
        .side { position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; padding: 3rem; color: #b9cdee;
            background: radial-gradient(circle at 15% 10%, rgba(91, 139, 224, .45), transparent 45%), radial-gradient(circle at 90% 90%, rgba(232, 8, 16, .22), transparent 45%), #001540; }
        .side::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px); background-size: 24px 24px; mask-image: linear-gradient(180deg, #000, transparent 80%); }
        .brand { position: relative; display: flex; align-items: center; gap: .75rem; color: #fff; font-size: 1.35rem; font-weight: 600; letter-spacing: -.02em; }
        .brand strong { color: #8fb0ec; font-weight: 800; }
        .brand i { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 13px; background: linear-gradient(135deg, #1a4fb5, #1a5fd0); color: #fff; font-size: 1.15rem; box-shadow: 0 12px 24px -10px rgba(26, 79, 181, .9); }
        .side h1 { position: relative; color: #fff; font-size: clamp(2rem, 3.2vw, 2.9rem); line-height: 1.1; letter-spacing: -.035em; margin: 0 0 1rem; font-weight: 800; }
        .side h1 span { background: linear-gradient(90deg, #8fb0ec, #ff6a6f); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .side p { position: relative; max-width: 440px; font-size: 1.02rem; color: #98A2B3; margin: 0; }
        .points { position: relative; display: grid; gap: .75rem; margin-top: 2rem; }
        .points div { display: flex; align-items: center; gap: .75rem; color: #d6e2f5; font-weight: 500; }
        .points i { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 10px; background: rgba(255, 255, 255, .08); color: #8fb0ec; font-size: .85rem; }
        .side small { position: relative; color: #fff; z-index: 2; font-weight: 600; }
        .main { display: grid; place-items: center; padding: 2rem; }
        .card { width: 100%; max-width: 420px; }
        .card h2 { margin: 0 0 .35rem; color: #0F172A; font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; }
        .card > p { margin: 0 0 2rem; }
        label { display: block; font-size: .85rem; font-weight: 600; color: #1E293B; margin-bottom: .45rem; }
        .field { margin-bottom: 1.1rem; }
        .input { position: relative; }
        .input > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #98A2B3; font-size: .9rem; }
        .input input { width: 100%; height: 50px; border: 1px solid #D9DCE5; border-radius: 13px; background: #fff; padding: 0 2.9rem 0 2.75rem; font: inherit; font-size: .95rem; color: #0F172A; outline: 0; transition: border-color .15s, box-shadow .15s; }
        .input input:focus { border-color: #003080; box-shadow: 0 0 0 4px #d6e2f5; }
        .toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: #98A2B3; width: 36px; height: 36px; border-radius: 9px; cursor: pointer; }
        .toggle:hover { background: #F4F5FA; color: #003080; }
        .err { color: #D92D20; font-size: .8rem; margin-top: .35rem; }
        .row { display: flex; justify-content: space-between; align-items: center; margin: .25rem 0 1.5rem; font-size: .88rem; }
        .check { display: inline-flex; align-items: center; gap: .5rem; cursor: pointer; margin: 0; font-weight: 500; color: #475467; }
        .check input { width: 17px; height: 17px; accent-color: #003080; }
        .btn { width: 100%; height: 50px; border: 0; border-radius: 13px; background: #003080; color: #fff; font: inherit; font-weight: 700; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: .6rem; box-shadow: 0 12px 24px -12px rgba(0, 48, 128, .9); transition: background .15s, transform .15s; }
        .btn:hover { background: #002670; transform: translateY(-1px); }
        .alert { display: flex; gap: .65rem; align-items: flex-start; padding: .85rem 1rem; border-radius: 12px; margin-bottom: 1.25rem; font-size: .88rem; background: #FEF3F2; color: #912018; border: 1px solid #FECDCA; }
        .alert.ok { background: #ECFDF3; color: #05603A; border-color: #ABEFC6; }
        .back { display: inline-flex; align-items: center; gap: .5rem; margin-top: 2rem; font-size: .88rem; font-weight: 600; color: #667085; }
        .back:hover { color: #003080; }
        .brand-card { position: relative; display: inline-flex; align-self: flex-start; padding: .55rem 1rem; background: #fff; border-radius: 16px; box-shadow: 0 16px 32px -16px rgba(0, 0, 0, .5); }
        .brand-card img { height: 58px; width: auto; display: block; }
        .side::after { content: ""; position: absolute; left: -10%; right: -10%; bottom: -60px; height: 140px; background: #E80810; border-radius: 50%; transform: rotate(-6deg); opacity: .9; box-shadow: 0 -14px 0 0 #fff; }
        .side > * { z-index: 1; }
        .card-logo { display: none; height: 56px; margin-bottom: 1.5rem; }
        @media (max-width: 900px) { body { grid-template-columns: 1fr; } .side { display: none; } .card-logo { display: block; } }
    </style>
</head>
<body>
    <aside class="side">
        <a href="<?php echo e(route('index')); ?>" class="brand-card"><img src="<?php echo e(asset(general()->logo())); ?>" alt="<?php echo e(general()->title); ?>"></a>
        <div>
            <h1>Manage your courses,<br><span>orders &amp; students</span></h1>
            <p>Verify bKash, Nagad and bank payments, publish new courses and keep the website up to date, all from one place.</p>
            <div class="points">
                <div><i class="fa-solid fa-shield-halved"></i> Manual payment verification</div>
                <div><i class="fa-solid fa-layer-group"></i> Courses, modules &amp; YouTube lessons</div>
                <div><i class="fa-solid fa-chart-line"></i> Sales dashboard &amp; student list</div>
            </div>
        </div>
        <small>&copy; <?php echo e(date('Y')); ?> <?php echo e(general()->title); ?> &middot; Admin Panel</small>
    </aside>

    <main class="main">
        <div class="card">
            <img src="<?php echo e(asset(general()->logo())); ?>" alt="<?php echo e(general()->title); ?>" class="card-logo">
            <h2>Welcome back 👋</h2>
            <p>Sign in to the <?php echo e(general()->title); ?> admin panel.</p>

            <?php if(session('error')): ?>
                <div class="alert"><i class="fa-solid fa-circle-exclamation"></i><span><?php echo e(session('error')); ?></span></div>
            <?php endif; ?>
            <?php if(session('success') || session('status')): ?>
                <div class="alert ok"><i class="fa-solid fa-circle-check"></i><span><?php echo e(session('success') ?: session('status')); ?></span></div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin')); ?>" method="post" autocomplete="on">
                <?php echo csrf_field(); ?>
                <div class="field">
                    <label for="email">Email address</label>
                    <div class="input">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="you@trainersbd.com" required autofocus autocomplete="username">
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="toggle" aria-label="Show password" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password';this.firstElementChild.className=p.type==='password'?'fa-regular fa-eye':'fa-regular fa-eye-slash';"><i class="fa-regular fa-eye"></i></button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="err"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="row">
                    <label class="check"><input type="checkbox" name="remember"> Remember me</label>
                </div>
                <button type="submit" class="btn"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign in</button>
            </form>

            <a href="<?php echo e(route('index')); ?>" class="back"><i class="fa-solid fa-arrow-left"></i> Back to website</a>
        </div>
    </main>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/auth/adminLogin.blade.php ENDPATH**/ ?>