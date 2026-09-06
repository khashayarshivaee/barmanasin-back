<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <title>
        Account Activation — BARMANASIN
    </title>

    <style>
        :root {
            color-scheme: dark;

            --bg: #0b0e11;
            --surface: #12161a;
            --surface-soft: #171c21;

            --border: rgba(255, 255, 255, 0.08);
            --border-strong: rgba(255, 255, 255, 0.14);

            --text: #f4f6f8;
            --muted: #8d98a5;

            --success: #75c694;
            --warning: #d9ae69;
            --danger: #df7b7b;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;

            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            color: var(--text);

            background:
                radial-gradient(
                    circle at 78% 8%,
                    rgba(148, 163, 184, 0.09),
                    transparent 28rem
                ),
                linear-gradient(
                    145deg,
                    #080a0c 0%,
                    #0d1115 55%,
                    #10151a 100%
                );
        }

        .page {
            min-height: 100vh;

            display: grid;
            grid-template-columns:
                minmax(280px, 0.8fr)
                minmax(420px, 1.2fr);
        }

        .identity {
            position: relative;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            padding: clamp(32px, 5vw, 72px);

            overflow: hidden;

            border-right: 1px solid var(--border);
        }

        .identity::before {
            content: "";

            position: absolute;
            inset: 0;

            pointer-events: none;

            opacity: 0.18;

            background-image:
                linear-gradient(
                    rgba(255,255,255,.06) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255,255,255,.06) 1px,
                    transparent 1px
                );

            background-size: 52px 52px;

            mask-image:
                linear-gradient(
                    to bottom,
                    black,
                    transparent 80%
                );
        }

        .brand {
            position: relative;

            z-index: 1;

            font-size: 0.82rem;
            font-weight: 700;

            letter-spacing: 0.24em;
        }

        .identity-copy {
            position: relative;

            z-index: 1;

            max-width: 500px;
        }

        .eyebrow {
            margin-bottom: 18px;

            color: var(--muted);

            font-size: 0.68rem;
            font-weight: 700;

            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .identity h1 {
            margin: 0;

            max-width: 520px;

            font-size: clamp(
                2.4rem,
                5vw,
                5rem
            );

            font-weight: 500;

            line-height: 0.98;

            letter-spacing: -0.055em;
        }

        .identity p {
            max-width: 480px;

            margin: 26px 0 0;

            color: var(--muted);

            font-size: 0.95rem;

            line-height: 1.75;
        }

        .identity-footer {
            position: relative;

            z-index: 1;

            color: rgba(255, 255, 255, 0.35);

            font-size: 0.68rem;

            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .content {
            display: flex;
            align-items: center;
            justify-content: center;

            padding: clamp(28px, 6vw, 84px);
        }

        .card {
            width: min(100%, 520px);

            padding: clamp(28px, 4vw, 42px);

            border: 1px solid var(--border);

            border-radius: 18px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.045),
                    rgba(255,255,255,.018)
                );

            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.24);

            backdrop-filter: blur(18px);
        }

        .status-mark {
            width: 42px;
            height: 42px;

            display: grid;
            place-items: center;

            margin-bottom: 28px;

            border: 1px solid var(--border-strong);
            border-radius: 50%;

            color: var(--muted);

            font-size: 1rem;
        }

        .status-mark.success {
            color: var(--success);

            border-color:
                rgba(117, 198, 148, 0.35);

            background:
                rgba(117, 198, 148, 0.08);
        }

        .status-mark.warning {
            color: var(--warning);

            border-color:
                rgba(217, 174, 105, 0.35);

            background:
                rgba(217, 174, 105, 0.08);
        }

        .status-mark.danger {
            color: var(--danger);

            border-color:
                rgba(223, 123, 123, 0.35);

            background:
                rgba(223, 123, 123, 0.08);
        }

        .card h2 {
            margin: 0;

            font-size: clamp(
                1.65rem,
                3vw,
                2.15rem
            );

            font-weight: 550;

            letter-spacing: -0.035em;
        }

        .lead {
            margin: 12px 0 0;

            color: var(--muted);

            font-size: 0.9rem;

            line-height: 1.7;
        }

        .account {
            margin-top: 22px;

            padding: 14px 16px;

            border: 1px solid var(--border);

            border-radius: 10px;

            background:
                rgba(255, 255, 255, 0.025);
        }

        .account-label {
            display: block;

            margin-bottom: 4px;

            color: var(--muted);

            font-size: 0.65rem;

            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .account-address {
            font-size: 0.9rem;
            font-weight: 600;

            word-break: break-word;
        }

        form {
            margin-top: 30px;
        }

        .field {
            margin-top: 18px;
        }

        label {
            display: block;

            margin-bottom: 8px;

            color: #dce2e8;

            font-size: 0.78rem;
            font-weight: 600;
        }

        input {
            width: 100%;
            height: 48px;

            padding: 0 14px;

            color: var(--text);

            font: inherit;

            border: 1px solid var(--border-strong);
            border-radius: 9px;

            outline: none;

            background: rgba(0, 0, 0, 0.18);

            transition:
                border-color 150ms ease,
                box-shadow 150ms ease;
        }

        input:focus {
            border-color:
                rgba(203, 213, 225, 0.5);

            box-shadow:
                0 0 0 3px
                rgba(148, 163, 184, 0.08);
        }

        .hint {
            margin-top: 8px;

            color: var(--muted);

            font-size: 0.7rem;

            line-height: 1.55;
        }

        .error {
            margin-top: 7px;

            color: #f3a2a2;

            font-size: 0.72rem;
        }

        .errors {
            margin-top: 24px;

            padding: 13px 15px;

            border:
                1px solid
                rgba(223, 123, 123, 0.22);

            border-radius: 9px;

            color: #f0a0a0;

            background:
                rgba(223, 123, 123, 0.055);

            font-size: 0.76rem;

            line-height: 1.6;
        }

        .button {
            width: 100%;
            height: 50px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            margin-top: 26px;

            color: #111417;

            font: inherit;
            font-size: 0.82rem;
            font-weight: 700;

            border: 0;
            border-radius: 9px;

            cursor: pointer;

            background: #eef2f5;

            transition:
                transform 150ms ease,
                background 150ms ease;
        }

        .button:hover {
            transform: translateY(-1px);

            background: #ffffff;
        }

        .security-note {
            margin-top: 22px;

            padding-top: 20px;

            border-top: 1px solid var(--border);

            color:
                rgba(255, 255, 255, 0.38);

            font-size: 0.68rem;

            line-height: 1.65;
        }

        @media (max-width: 860px) {
            .page {
                display: block;
            }

            .identity {
                min-height: 280px;

                padding: 30px;

                border-right: 0;
                border-bottom:
                    1px solid var(--border);
            }

            .identity h1 {
                max-width: 420px;

                font-size: clamp(
                    2.3rem,
                    11vw,
                    4rem
                );
            }

            .identity-footer {
                display: none;
            }

            .content {
                padding: 24px 18px 48px;
            }

            .card {
                border-radius: 14px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <section class="identity">

        <div class="brand">
            BARMANASIN
        </div>

        <div class="identity-copy">

            <div class="eyebrow">
                Secure Account Access
            </div>

            <h1>
                Your company workspace.
            </h1>

            <p>
                Set up secure access to your Barmanasin
                account and company email services.
            </p>

        </div>

        <div class="identity-footer">
            Mining · Engineering · Materials
        </div>

    </section>


    <main class="content">

        <div class="card">

            @if ($state === 'ready')

                <div class="status-mark">
                    →
                </div>

                <h2>
                    Activate your account
                </h2>

                <p class="lead">
                    Create a secure password to activate
                    your Barmanasin account.
                </p>

                @if ($user)
                    <div class="account">
                        <span class="account-label">
                            Account
                        </span>

                        <div class="account-address">
                            {{ $user->mailbox_address ?: $user->email }}
                        </div>
                    </div>
                @endif


                @if ($errors->any())
                    <div class="errors">
                        Please review the fields below
                        and try again.
                    </div>
                @endif


                <form
                    method="POST"
                    action="{{ request()->url() }}"
                    autocomplete="off"
                >
                    @csrf

                    <div class="field">
                        <label for="password">
                            New Password
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            minlength="12"
                            autocomplete="new-password"
                            autofocus
                        >

                        <div class="hint">
                            Minimum 12 characters with uppercase,
                            lowercase, number and symbol.
                        </div>

                        @error('password')
                        <div class="error">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    <div class="field">
                        <label for="password_confirmation">
                            Confirm Password
                        </label>

                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            minlength="12"
                            autocomplete="new-password"
                        >
                    </div>


                    <button
                        class="button"
                        type="submit"
                    >
                        Activate Account
                    </button>

                </form>


                <div class="security-note">
                    This activation link is single-use and
                    expires automatically. Barmanasin administrators
                    cannot view the password you choose.
                </div>


            @elseif ($state === 'success')

                <div class="status-mark success">
                    ✓
                </div>

                <h2>
                    Account activated
                </h2>

                <p class="lead">
                    Your password has been created and your
                    account is now active.
                </p>

                @if ($user)
                    <div class="account">
                        <span class="account-label">
                            Account
                        </span>

                        <div class="account-address">
                            {{ $user->mailbox_address ?: $user->email }}
                        </div>
                    </div>
                @endif

                <div class="security-note">
                    Your mail workspace will become available
                    through your Barmanasin account once mailbox
                    provisioning is enabled.
                </div>


            @elseif ($state === 'unavailable')

                <div class="status-mark warning">
                    !
                </div>

                <h2>
                    Account unavailable
                </h2>

                <p class="lead">
                    This account is currently inactive or suspended.
                    Please contact your Barmanasin administrator.
                </p>


            @else

                <div class="status-mark danger">
                    ×
                </div>

                <h2>
                    Link unavailable
                </h2>

                <p class="lead">
                    This activation link is invalid,
                    expired or has already been used.
                </p>

                <div class="security-note">
                    Ask your administrator to generate a new
                    activation link for your account.
                </div>

            @endif

        </div>

    </main>

</div>

</body>
</html>
