<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- محرك SEO القوي والمتقدم -->
    <title>{{ $landing['seo_title'] ?: $landing['site_name'] }}</title>
    <meta name="description" content="{{ $landing['seo_description'] ?? $landing['hero_subtitle'] }}">
    <meta name="keywords" content="{{ $landing['seo_keywords'] ?? '' }}">
    <meta name="author" content="{{ $landing['seo_author'] ?? $landing['site_name'] }}">
    <meta name="robots" content="{{ $landing['seo_robots'] ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $landing['seo_canonical_url'] ?? url('/') }}">

    <!-- Open Graph / WhatsApp / Facebook / LinkedIn -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">
    <meta property="og:site_name" content="{{ $landing['site_name'] }}">
    <meta property="og:title" content="{{ $landing['seo_title'] ?: $landing['site_name'] }}">
    <meta property="og:description" content="{{ $landing['seo_description'] ?? $landing['hero_subtitle'] }}">
    <meta property="og:url" content="{{ $landing['seo_canonical_url'] ?? url('/') }}">
    @if(!empty($landing['seo_og_image']))
        <meta property="og:image" content="{{ $landing['seo_og_image'] }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $landing['seo_title'] ?: $landing['site_name'] }}">
    <meta name="twitter:description" content="{{ $landing['seo_description'] ?? $landing['hero_subtitle'] }}">

    <!-- البيانات المهيكلة لمحرك بحث جوجل (JSON-LD Structured Data) -->
    @php
        $schemaOrgData = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $landing['site_name'],
            'applicationCategory' => 'BusinessApplication, RealEstateApplication, AccountingSoftware',
            'operatingSystem' => 'Web Cloud ERP',
            'description' => $landing['seo_description'] ?? $landing['hero_subtitle'],
            'url' => url('/'),
            'inLanguage' => 'ar-SA',
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'SAR',
                'availability' => 'https://schema.org/InStock',
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => $landing['site_name'],
                'telephone' => $landing['contact_phone'] ?? '',
                'email' => $landing['contact_email'] ?? '',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'SA',
                ],
            ],
        ];
        $allTabsMap = $availableOfficeTabs ?? \App\Http\Controllers\CompanyErpController::getAvailableOfficeTabs();
        $activeOfficePlans = $officePlans ?? collect();
    @endphp
    <script type="application/ld+json">{!! json_encode($schemaOrgData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Readex Pro', sans-serif; scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col selection:bg-emerald-500 selection:text-white">

    <!-- الشريط العلوي (Navbar) -->
    <header class="sticky top-0 z-40 bg-slate-950/85 backdrop-blur-xl border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-600/25 font-bold text-xl">
                    🏢
                </div>
                <div>
                    <span class="font-bold text-lg text-white tracking-tight block">{{ $landing['site_name'] }}</span>
                    <span class="text-[11px] text-emerald-400">{{ $landing['site_sub'] ?? 'ERP & Property Management System' }}</span>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-300" aria-label="القائمة الرئيسية">
                @if(!empty($landing['show_features']))
                    <a href="#features" class="hover:text-emerald-400 transition">مميزات النظام</a>
                @endif
                @if(!empty($landing['show_portals']))
                    <a href="#portals" class="hover:text-emerald-400 transition">بوابات المنصة</a>
                @endif
                @if(!empty($landing['show_plans']))
                    <a href="#plans" class="hover:text-emerald-400 transition">الباقات والاتفاقيات</a>
                @endif
                <a href="#contact" class="hover:text-emerald-400 transition">تواصل معنا</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('erp.dashboard') }}"
                       class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/25 transition">
                        الدخول للوحة التحكم ←
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/25 transition">
                        🔐 {{ $landing['cta_primary_text'] }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- القسم الرئيسي (Hero Section) -->
    <section class="relative overflow-hidden py-20 lg:py-24 border-b border-slate-900">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-6 text-center relative z-10 space-y-6">
            @if(!empty($landing['badge_text']))
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                    <span>✨</span>
                    <span>{{ $landing['badge_text'] }}</span>
                </div>
            @endif

            <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight tracking-tight">
                {{ $landing['hero_title'] }}
            </h1>

            <p class="text-slate-400 text-base md:text-lg max-w-3xl mx-auto leading-relaxed">
                {{ $landing['hero_subtitle'] }}
            </p>

            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}"
                   class="px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-xl shadow-emerald-600/25 transition flex items-center gap-2">
                    <span>🚀 {{ $landing['cta_primary_text'] }}</span>
                </a>

                @if(!empty($landing['contact_whatsapp']))
                    <a href="https://wa.me/{{ $landing['contact_whatsapp'] }}" target="_blank" rel="noopener"
                       class="px-7 py-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 font-bold text-sm transition flex items-center gap-2">
                        <span>💬 {{ $landing['cta_secondary_text'] }}</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- عرض الأقسام المرتبة ديناميكياً حسب اختيار الأدمن -->
    @php
        $sectionsList = [
            ['key' => 'stats', 'order' => $landing['order_stats'] ?? 1, 'show' => $landing['show_stats'] ?? 1],
            ['key' => 'features', 'order' => $landing['order_features'] ?? 2, 'show' => $landing['show_features'] ?? 1],
            ['key' => 'portals', 'order' => $landing['order_portals'] ?? 3, 'show' => $landing['show_portals'] ?? 1],
            ['key' => 'plans', 'order' => $landing['order_plans'] ?? 4, 'show' => $landing['show_plans'] ?? 1],
            ['key' => 'custom', 'order' => $landing['order_custom'] ?? 5, 'show' => $landing['show_custom'] ?? 1],
        ];
        usort($sectionsList, fn($a, $b) => $a['order'] <=> $b['order']);
    @endphp

    <main class="flex-1 flex flex-col">
        @foreach($sectionsList as $sec)
            @if(!empty($sec['show']))

                {{-- 1. قسم شريط الإحصائيات --}}
                @if($sec['key'] === 'stats' && count($landing['stats'] ?? []) > 0)
                    <section class="py-12 bg-slate-950 border-b border-slate-900">
                        <div class="max-w-6xl mx-auto px-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($landing['stats'] as $idx => $st)
                                    @php
                                        $colors = ['text-emerald-400', 'text-sky-400', 'text-amber-400', 'text-white'];
                                        $cClass = $colors[$idx % count($colors)];
                                    @endphp
                                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 text-center">
                                        <div class="text-2xl md:text-3xl font-extrabold {{ $cClass }} font-mono">{{ $st['number'] ?? '' }}</div>
                                        <div class="text-xs text-slate-400 mt-1.5 font-medium">{{ $st['label'] ?? '' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                {{-- 2. قسم المميزات والخدمات --}}
                @if($sec['key'] === 'features' && count($landing['features'] ?? []) > 0)
                    <section id="features" class="py-20 bg-slate-950 border-b border-slate-900">
                        <div class="max-w-7xl mx-auto px-6">
                            <div class="text-center max-w-2xl mx-auto mb-14">
                                @if(!empty($landing['features_badge']))
                                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">{{ $landing['features_badge'] }}</span>
                                @endif
                                <h2 class="text-2xl md:text-3xl font-bold text-white mt-2">{{ $landing['features_heading'] }}</h2>
                                @if(!empty($landing['features_subheading']))
                                    <p class="text-sm text-slate-400 mt-2">{{ $landing['features_subheading'] }}</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($landing['features'] as $feat)
                                    <article class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-emerald-500/50 transition group">
                                        <div class="w-12 h-12 rounded-xl bg-slate-800 group-hover:bg-emerald-500/20 border border-slate-700 flex items-center justify-center text-2xl mb-4 transition">
                                            {{ $feat['icon'] ?? '✨' }}
                                        </div>
                                        <h3 class="font-bold text-base text-white mb-2">{{ $feat['title'] ?? '' }}</h3>
                                        <p class="text-xs text-slate-400 leading-relaxed">{{ $feat['desc'] ?? '' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                {{-- 3. قسم البوابات الذكية --}}
                @if($sec['key'] === 'portals' && count($landing['portals'] ?? []) > 0)
                    <section id="portals" class="py-20 bg-slate-900/60 border-b border-slate-800/80">
                        <div class="max-w-7xl mx-auto px-6">
                            <div class="text-center max-w-2xl mx-auto mb-14">
                                @if(!empty($landing['portals_badge']))
                                    <span class="text-xs font-bold text-sky-400 uppercase tracking-wider">{{ $landing['portals_badge'] }}</span>
                                @endif
                                <h2 class="text-2xl md:text-3xl font-bold text-white mt-2">{{ $landing['portals_heading'] }}</h2>
                                @if(!empty($landing['portals_subheading']))
                                    <p class="text-sm text-slate-400 mt-2">{{ $landing['portals_subheading'] }}</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                                @foreach($landing['portals'] as $idx => $prt)
                                    @php
                                        $badgeStyles = [
                                            'bg-emerald-500/10 text-emerald-400',
                                            'bg-sky-500/10 text-sky-400',
                                            'bg-amber-500/10 text-amber-400',
                                            'bg-purple-500/10 text-purple-400',
                                        ];
                                        $bStyle = $badgeStyles[$idx % count($badgeStyles)];
                                    @endphp
                                    <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                                        @if(!empty($prt['badge']))
                                            <span class="inline-block px-3 py-1 rounded-lg {{ $bStyle }} text-xs font-bold">{{ $prt['badge'] }}</span>
                                        @endif
                                        <h3 class="font-bold text-white text-base">{{ $prt['title'] ?? '' }}</h3>
                                        <p class="text-xs text-slate-400 leading-relaxed">{{ $prt['desc'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                {{-- 4. قسم الباقات وصلاحيات المكاتب بالشكل الجديد --}}
                @if($sec['key'] === 'plans' && $activeOfficePlans->count() > 0)
                    <section id="plans" class="py-20 bg-slate-950 border-b border-slate-900">
                        <div class="max-w-7xl mx-auto px-6">
                            <div class="text-center max-w-2xl mx-auto mb-14">
                                @if(!empty($landing['plans_badge']))
                                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">{{ $landing['plans_badge'] }}</span>
                                @endif
                                <h2 class="text-2xl md:text-3xl font-bold text-white mt-2">{{ $landing['plans_heading'] }}</h2>
                                @if(!empty($landing['plans_subheading']))
                                    <p class="text-sm text-slate-400 mt-2">{{ $landing['plans_subheading'] }}</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($activeOfficePlans as $plan)
                                    @php
                                        $isHighlight = !empty($plan->is_highlighted);
                                        $planTabs = json_decode($plan->allowed_tabs ?? '[]', true) ?: [];
                                    @endphp
                                    <div class="p-7 rounded-3xl bg-slate-900/95 {{ $isHighlight ? 'border-2 border-emerald-500 shadow-2xl shadow-emerald-600/15' : 'border border-slate-800' }} flex flex-col justify-between relative space-y-6">
                                        <div class="space-y-4">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-3.5 py-1 rounded-xl {{ $isHighlight ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-200' }} text-xs font-extrabold">
                                                    {{ $plan->badge ?: 'باقة مكاتب' }}
                                                </span>
                                                @if(!empty($plan->offer_label))
                                                    <span class="px-3 py-1 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-[11px] font-bold">
                                                        🔥 {{ $plan->offer_label }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div>
                                                <h3 class="font-extrabold text-lg text-white">{{ $plan->name }}</h3>
                                                @if(!empty($plan->description))
                                                    <p class="text-xs text-slate-400 leading-relaxed mt-1.5">{{ $plan->description }}</p>
                                                @endif
                                            </div>

                                            <!-- صندوق السعر ونوع الاتفاقية -->
                                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800/90 space-y-1">
                                                <div class="flex items-baseline gap-2.5 flex-wrap">
                                                    @if($plan->billing_type === 'annual_subscription')
                                                        <span class="text-2xl font-extrabold text-emerald-400 font-mono">{{ number_format($plan->price, 0) }} ر.س</span>
                                                        <span class="text-xs text-slate-400">/ سنوياً</span>
                                                    @elseif($plan->billing_type === 'per_property_percentage')
                                                        <span class="text-2xl font-extrabold text-amber-400 font-mono">{{ $plan->commission_rate }}%</span>
                                                        <span class="text-xs text-slate-400">نسبة لكل عقد</span>
                                                    @else
                                                        <span class="text-2xl font-extrabold text-emerald-400 font-mono">{{ number_format($plan->price, 0) }} ر.س</span>
                                                        <span class="text-xs font-bold text-amber-400">+ {{ $plan->commission_rate }}% لكل عقد</span>
                                                    @endif

                                                    @if(!empty($plan->old_price) && $plan->old_price > $plan->price)
                                                        <span class="text-xs text-rose-400 line-through font-mono">{{ number_format($plan->old_price, 0) }} ر.س</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- قائمة الصلاحيات والصفحات المشمولة في الباقة -->
                                            <div class="space-y-2 pt-1">
                                                <div class="text-xs font-bold text-slate-300">المميزات والأنظمة المشمولة في الباقة ({{ count($planTabs) }} من {{ count($allTabsMap) }}):</div>
                                                <div class="grid grid-cols-1 gap-1.5 max-h-56 overflow-y-auto pr-1">
                                                    @foreach($allTabsMap as $tKey => $tLabel)
                                                        @if(in_array($tKey, $planTabs))
                                                            <div class="flex items-center gap-2 text-xs text-emerald-300 font-medium">
                                                                <span class="text-emerald-400 font-bold">✔</span>
                                                                <span>{{ $tLabel }}</span>
                                                            </div>
                                                        @else
                                                            <div class="flex items-center gap-2 text-[11px] text-slate-600 line-through">
                                                                <span>✖</span>
                                                                <span>{{ $tLabel }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pt-4 border-t border-slate-800">
                                            <a href="{{ !empty($landing['contact_whatsapp']) ? 'https://wa.me/' . $landing['contact_whatsapp'] . '?text=' . urlencode('السلام عليكم، أرغب بالاشتراك في: ' . $plan->name) : route('login') }}"
                                               class="block w-full text-center py-3.5 rounded-xl {{ $isHighlight ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/25' : 'bg-slate-800 hover:bg-slate-700 text-white' }} text-xs font-bold transition">
                                                اشترك في الباقة الآن ←
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                {{-- 5. الأقسام الإضافية المخصصة (Custom Sections) --}}
                @if($sec['key'] === 'custom' && count($landing['custom_sections'] ?? []) > 0)
                    @foreach($landing['custom_sections'] as $cs)
                        <section class="py-20 bg-slate-900/40 border-b border-slate-800/80">
                            <div class="max-w-7xl mx-auto px-6">
                                <div class="text-center max-w-2xl mx-auto mb-12">
                                    @if(!empty($cs['badge']))
                                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">{{ $cs['badge'] }}</span>
                                    @endif
                                    <h2 class="text-2xl md:text-3xl font-bold text-white mt-2">{{ $cs['title'] ?? '' }}</h2>
                                    @if(!empty($cs['desc']))
                                        <p class="text-sm text-slate-400 mt-2">{{ $cs['desc'] }}</p>
                                    @endif
                                </div>

                                @php
                                    $lines = array_filter(array_map('trim', explode("\n", $cs['items_raw'] ?? '')));
                                @endphp
                                @if(count($lines) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach($lines as $line)
                                            @php
                                                $parts = array_map('trim', explode('|', $line, 2));
                                                $itemTitle = $parts[0] ?? '';
                                                $itemDesc = $parts[1] ?? '';
                                            @endphp
                                            <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
                                                <h3 class="font-bold text-base text-white">{{ $itemTitle }}</h3>
                                                @if(!empty($itemDesc))
                                                    <p class="text-xs text-slate-400 leading-relaxed">{{ $itemDesc }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endforeach
                @endif

            @endif
        @endforeach
    </main>

    <!-- قسم التواصل والفوتر -->
    <footer id="contact" class="bg-slate-900 border-t border-slate-800 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-wrap items-center justify-between gap-6">
            <div class="space-y-2 max-w-md">
                <div class="font-bold text-base text-white">{{ $landing['site_name'] }}</div>
                <p class="text-xs text-slate-400">{{ $landing['footer_text'] }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs">
                @if(!empty($landing['contact_phone']))
                    <a href="tel:{{ $landing['contact_phone'] }}" class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 hover:border-emerald-500 transition" dir="ltr">
                        📞 {{ $landing['contact_phone'] }}
                    </a>
                @endif
                @if(!empty($landing['contact_email']))
                    <a href="mailto:{{ $landing['contact_email'] }}" class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 font-mono text-slate-300 hover:border-emerald-500 transition" dir="ltr">
                        ✉️ {{ $landing['contact_email'] }}
                    </a>
                @endif
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition">
                    {{ $landing['cta_primary_text'] }}
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
