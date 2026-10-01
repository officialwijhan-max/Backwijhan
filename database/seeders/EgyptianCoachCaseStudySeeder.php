<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\CaseStudyContribution;
use App\Models\CaseStudyFeature;
use App\Models\CaseStudySection;
use App\Models\CaseStudyTechnology;
use App\Models\WorkCategory;
use Illuminate\Database\Seeder;

/**
 * The real Egyptian Coach case study (Wijhan portfolio, Batch #3.2).
 *
 * Source of truth: "Egyptian Coach — Categories & Functional Description",
 * v1.0, September 2026, prepared by The Tailors Dev (the product's
 * functional specification), plus the verified team-staffing agreement
 * between The Tailors and Egyptian Coach dated 21 Sept 2025 (technical
 * team: Backend Developer, Flutter Developer, Project Manager; The Tailors
 * provided technical management, weekly reporting, admin-panel training
 * and QA testing as part of that engagement).
 *
 * Only technology named anywhere in that contract is Flutter (mobile) — no
 * backend framework, database or infrastructure is verified, so none is
 * claimed here. No user/business metrics exist in either source document,
 * so none are seeded.
 */
class EgyptianCoachCaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $category = WorkCategory::where('name_en', 'Sports Technology')->first();

        $caseStudy = CaseStudy::updateOrCreate(
            ['slug' => 'egyptian-coach'],
            [
                'work_category_id' => $category?->id,
                'industry_en' => null,
                'industry_ar' => null,
                'title_en' => 'Egyptian Coach',
                'title_ar' => 'Egyptian Coach',
                'client_name' => null,
                'client_visibility' => true,
                'headline_en' => 'One app for everything football.',
                'headline_ar' => 'تطبيق واحد لكل ما يخص كرة القدم.',
                'summary_en' => 'A connected sports platform bringing football content, talent discovery, player development and a sports marketplace into one experience.',
                'summary_ar' => 'منصة رياضية متكاملة تجمع المحتوى الكروي واكتشاف المواهب وتطوير اللاعبين والمتجر الرياضي في تجربة واحدة.',
                'hero_description_en' => 'Egyptian Coach brings football news, talent discovery, training, academies, predictions, VAR voting, loyalty rewards and a sports marketplace together in a single, connected experience — built to give football fans and players a reason to open the app every day.',
                'hero_description_ar' => 'يجمع Egyptian Coach المحتوى الكروي واكتشاف المواهب والتدريب والأكاديميات والتوقعات والتصويت على الحالات التحكيمية ونظام الولاء والمتجر الرياضي في تجربة واحدة متصلة، مصمّمة لتمنح عشّاق ولاعبي كرة القدم سبباً للعودة يومياً.',
                'outcome_en' => 'Egyptian Coach brings football content, player development, talent discovery, fan interaction and sports commerce together within one connected digital ecosystem.',
                'outcome_ar' => 'يجمع Egyptian Coach المحتوى الكروي وتطوير اللاعبين واكتشاف المواهب والتفاعل الجماهيري والتجارة الرياضية داخل نظام رقمي واحد متصل.',
                'cover_image_url' => null,
                'logo' => '/case-studies/egyptian-coach/logo.png',
                'is_published' => true,
                'featured' => true,
                'display_order' => 0,
                'published_at' => now(),
            ],
        );

        $sections = [
            [
                'type' => 'challenge',
                'title_en' => 'The Product Opportunity',
                'title_ar' => 'الفرصة وراء المنتج',
                'content_en' => 'Football fans and players in Egypt typically need several separate apps to follow the game: one for news and fixtures, another for finding an academy or coach, another for showcasing a talent, and separate channels for predictions or buying sports gear. Egyptian Coach was conceived as a single connected home for all of it — content, player development, fan interaction and sports commerce — so a user has one reason to open one app every day, not five.',
                'content_ar' => 'غالباً ما يحتاج عشّاق ولاعبو كرة القدم في مصر إلى عدة تطبيقات منفصلة: واحد للأخبار والمباريات، وآخر للبحث عن أكاديمية أو مدرب، وقناة مختلفة لإبراز موهبة، ومكان آخر للتوقعات أو شراء الأدوات الرياضية. صُمم Egyptian Coach ليكون بيتاً رقمياً واحداً لكل ذلك: المحتوى وتطوير اللاعبين والتفاعل الجماهيري والتجارة الرياضية، بحيث يملك المستخدم سبباً واحداً لفتح تطبيق واحد كل يوم بدلاً من خمسة.',
            ],
            [
                'type' => 'product',
                'title_en' => 'One Connected Football Experience',
                'title_ar' => 'تجربة كروية متصلة واحدة',
                'content_en' => "Rather than being another football news app, Egyptian Coach organizes its scope around four connected groups: daily content (home, news, fixtures, standings and women's football), player development (talent discovery, coaching and academies), fan engagement (predictions, VAR-style voting, awards, loyalty and notifications), and sports commerce (a multi-sport marketplace). Every section can be reordered, shown or hidden from the admin panel, so the product's shape can evolve without an app store release.",
                'content_ar' => 'بدلاً من أن يكون مجرد تطبيق أخبار كروية آخر، ينظّم Egyptian Coach نطاقه حول أربع مجموعات متصلة: المحتوى اليومي (الرئيسية، الأخبار، المباريات، الترتيب، والكرة النسائية)، وتطوير اللاعب (اكتشاف المواهب، التدريب، والأكاديميات)، والتفاعل الجماهيري (التوقعات، التصويت على الحالات التحكيمية، الجوائز، الولاء، والإشعارات)، والتجارة الرياضية (متجر متعدد الرياضات). يمكن إعادة ترتيب أي قسم أو إظهاره أو إخفاؤه من لوحة الإدارة، بحيث يتطور شكل المنتج دون الحاجة لإصدار جديد من التطبيق.',
            ],
            [
                'type' => 'talent',
                'title_en' => 'Talent & Player Development',
                'title_ar' => 'اكتشاف المواهب وتطوير اللاعبين',
                'content_en' => 'One of the product\'s strongest ideas is turning the app into a discovery space for emerging players. Inside Talents, users browse a video feed of player profiles — filterable across All, Top/Featured and Rising Talents — and can upload their own footage for others, including coaches and academies, to find. Submitted content moves through a moderation flow (Draft, Pending, Approved, Rejected, Hidden) before it\'s visible publicly. Around it, You Are The Coach connects players to coaches and structured training programs by level and exercise type, and Academies lets users search, compare and reach out to football academies by location, pricing and available subscriptions — turning the app from content into a practical, actionable service.',
                'content_ar' => 'من أقوى أفكار المنتج تحويل التطبيق إلى مساحة لاكتشاف اللاعبين الناشئين. داخل قسم موهبتي، يتصفح المستخدمون Feed فيديو لملفات اللاعبين، قابلاً للفلترة بين الكل وأفضل المواهب والمواهب الصاعدة، ويمكنهم رفع فيديوهاتهم الخاصة ليصل إليها آخرون، بمن فيهم المدربون والأكاديميات. يمر المحتوى المرفوع بمسار مراجعة (مسودة، قيد المراجعة، مقبول، مرفوض، مخفي) قبل ظهوره للعامة. وحول هذا القسم، يربط قسم أنت المدرب اللاعبين بمدربين وبرامج تدريب منظمة حسب المستوى ونوع التمرين، بينما يتيح قسم الأكاديميات البحث عن أكاديميات كرة القدم ومقارنتها والتواصل معها حسب الموقع والسعر والاشتراكات المتاحة، ليتحول التطبيق من محتوى إلى خدمة عملية فعلية.',
            ],
            [
                'type' => 'engagement',
                'title_en' => 'Fan Engagement',
                'title_ar' => 'التفاعل الجماهيري',
                'content_en' => 'Egyptian Coach layers several interaction loops on top of the football calendar. Predictions let users forecast match scores before a set deadline and earn points from the actual result. VAR Voting turns refereeing moments into a quick poll users can weigh in on. Awards run time-boxed competitions tied to activities like predictions or loyalty. Loyalty accumulates points from configured actions across the app, with every balance change auditable rather than user-editable. Notifications tie it together with deep links straight into the relevant news, match or product.',
                'content_ar' => 'يضيف Egyptian Coach عدة حلقات تفاعل فوق التقويم الكروي. يتيح قسم التوقعات للمستخدمين توقّع نتيجة المباراة قبل موعد إغلاق محدد وكسب نقاط بناءً على النتيجة الفعلية. ويحوّل صوت الـ VAR اللحظات التحكيمية إلى استطلاع سريع يمكن للمستخدم المشاركة فيه. وتُقام الجوائز كمسابقات محددة المدة مرتبطة بأنشطة مثل التوقعات أو الولاء. ويراكم نظام الولاء نقاطاً من أنشطة قابلة للإعداد داخل التطبيق، مع تسجيل كل تعديل على الرصيد بدلاً من ترك تعديله للمستخدم نفسه. وتربط الإشعارات كل ذلك ببعضه عبر روابط مباشرة تنقل المستخدم فوراً إلى الخبر أو المباراة أو المنتج المعني.',
            ],
            [
                'type' => 'marketplace',
                'title_en' => 'Sports Marketplace',
                'title_ar' => 'المتجر الرياضي',
                'content_en' => 'The Marketplace extends Egyptian Coach beyond content into commerce. Users browse new and used sports products across categories including Football, Handball, Basketball, Volleyball, Swimming, Tennis and Fitness, filter by sport and condition, and reach sellers directly — including WhatsApp where available. Listings move through a review step before publishing, and business rules keep the catalog trustworthy: a sold product can no longer appear as available, and a discounted price can never exceed the original price.',
                'content_ar' => 'يوسّع المتجر الرياضي نطاق Egyptian Coach من المحتوى إلى التجارة. يتصفح المستخدمون منتجات رياضية جديدة ومستعملة ضمن تصنيفات تشمل كرة القدم وكرة اليد وكرة السلة والكرة الطائرة والسباحة والتنس واللياقة البدنية، ويمكنهم الفلترة حسب الرياضة وحالة المنتج، والتواصل مباشرة مع البائع، بما في ذلك عبر واتساب عند توفره. تمر المنتجات المعروضة بخطوة مراجعة قبل النشر، وتحافظ قواعد العمل على مصداقية الكتالوج: فالمنتج المُباع لا يظهر بعدها كمتاح، والسعر بعد الخصم لا يمكن أن يتجاوز السعر الأصلي أبداً.',
            ],
            [
                'type' => 'content',
                'title_en' => 'Content & Football Data',
                'title_ar' => 'المحتوى والبيانات الكروية',
                'content_en' => "Underneath the interactive features sits the football content that brings people back daily: News covers daily sports content with Featured, Breaking and Live states; Fixtures organizes today's, upcoming and previous matches; Standings tracks team rankings by competition and season; and Women's Football gets its own dedicated space for news, matches, teams and players rather than being folded into general coverage. Depending on the section, this football data can come from manual admin entry or an external data provider — the specification keeps that source explicit rather than assumed.",
                'content_ar' => 'تحت الخصائص التفاعلية يقع المحتوى الكروي الذي يُعيد المستخدمين يومياً: يغطي قسم الأخبار المحتوى الرياضي اليومي بحالات مميز وعاجل ومباشر، وينظّم قسم المباريات مباريات اليوم والقادمة والسابقة، ويتابع قسم الترتيب مراكز الفرق حسب البطولة والموسم، بينما يحصل قسم الكرة النسائية على مساحته الخاصة للأخبار والمباريات والفرق واللاعبات بدلاً من إدماجه ضمن التغطية العامة. وحسب القسم، قد تأتي هذه البيانات الكروية من إدخال يدوي من الإدارة أو من مزود بيانات خارجي — وتُبقي المواصفات مصدر البيانات واضحاً بدلاً من افتراضه.',
            ],
            [
                'type' => 'engineering',
                'title_en' => 'Product Complexity',
                'title_ar' => 'تعقيد المنتج الهندسي',
                'content_en' => "Beneath a simple-looking football app, the product specification defines a genuinely complex set of rules. Most sections are configurable from an admin panel rather than hardcoded, so banners, Explore cards, categories and optional features can change without a new app release. User-submitted content — talents and marketplace listings — is designed to move through moderation states before going public. The experience is multilingual, with Arabic as the primary right-to-left language alongside English and French. Notifications and banners are designed to deep-link straight into specific screens. And the specification is explicit about time-sensitive and data-integrity rules: prediction deadlines, one active vote per user per VAR poll, awards that can't be shown as available once their period ends, sold marketplace items that must disappear from listings, discounted prices that can never exceed the original price, and loyalty balances that can only change through an audited process — never edited by the user directly.",
                'content_ar' => 'خلف مظهر تطبيق كروي بسيط، تحدد مواصفات المنتج مجموعة قواعد معقّدة فعلياً. معظم الأقسام قابلة للإعداد من لوحة الإدارة بدلاً من أن تكون ثابتة في الكود، بحيث يمكن تغيير البنرات وبطاقات Explore والتصنيفات والخصائص الاختيارية دون إصدار جديد من التطبيق. صُمم المحتوى الذي ينشئه المستخدم — المواهب ومنتجات المتجر — للمرور بحالات مراجعة قبل ظهوره للعامة. والتجربة متعددة اللغات، مع العربية كلغة أساسية من اليمين لليسار إلى جانب الإنجليزية والفرنسية. كما صُممت الإشعارات والبنرات لتفتح مباشرة شاشات أو محتوى محدداً عبر روابط مباشرة. وتوضح المواصفات بدقة قواعد الحساسية الزمنية وسلامة البيانات: مواعيد إغلاق التوقعات، وتصويت واحد فعّال لكل مستخدم في كل استطلاع VAR، وعدم إظهار الجوائز كمتاحة بعد انتهاء فترتها، واختفاء منتجات المتجر المباعة من قوائم العرض، وعدم تجاوز السعر بعد الخصم للسعر الأصلي أبداً، ورصيد الولاء الذي لا يتغير إلا عبر عملية مسجَّلة وموثقة — لا يعدّلها المستخدم بنفسه مطلقاً.',
            ],
        ];

        foreach ($sections as $index => $section) {
            CaseStudySection::updateOrCreate(
                ['case_study_id' => $caseStudy->id, 'type' => $section['type']],
                [...$section, 'display_order' => $index],
            );
        }

        $features = [
            [
                'title_en' => 'Discover',
                'title_ar' => 'استكشف',
                'category_en' => 'Content',
                'category_ar' => 'المحتوى',
                'description_en' => 'Home, News, Fixtures, Standings and Women\'s Football — the daily football content that brings users back.',
                'description_ar' => 'الرئيسية، الأخبار، المباريات، الترتيب، والكرة النسائية — المحتوى الكروي اليومي الذي يُعيد المستخدمين للتطبيق.',
            ],
            [
                'title_en' => 'Develop',
                'title_ar' => 'طوّر',
                'category_en' => 'Player Development',
                'category_ar' => 'تطوير اللاعب',
                'description_en' => 'Talents, You Are The Coach and Academies — from showcasing a player to structured training and academy access.',
                'description_ar' => 'موهبتي، أنت المدرب، والأكاديميات — من إبراز اللاعب إلى التدريب المنظم والوصول للأكاديميات.',
            ],
            [
                'title_en' => 'Engage',
                'title_ar' => 'تفاعل',
                'category_en' => 'Fan Interaction',
                'category_ar' => 'التفاعل الجماهيري',
                'description_en' => 'Predictions, VAR Voting, Awards, Loyalty and Notifications — interaction loops built around real football moments.',
                'description_ar' => 'التوقعات، صوت الـ VAR، الجوائز، الولاء، والإشعارات — حلقات تفاعل مبنية حول لحظات كروية حقيقية.',
            ],
            [
                'title_en' => 'Trade',
                'title_ar' => 'تبادل',
                'category_en' => 'Sports Commerce',
                'category_ar' => 'التجارة الرياضية',
                'description_en' => 'A multi-sport Marketplace for buying and selling new and used sports products.',
                'description_ar' => 'متجر رياضي متعدد الرياضات لبيع وشراء المنتجات الرياضية الجديدة والمستعملة.',
            ],
        ];

        foreach ($features as $index => $feature) {
            CaseStudyFeature::updateOrCreate(
                ['case_study_id' => $caseStudy->id, 'title_en' => $feature['title_en']],
                [...$feature, 'image' => null, 'secondary_image' => null, 'display_order' => $index],
            );
        }

        $contributions = [
            ['name_en' => 'Technical Team Coordination', 'name_ar' => 'تنسيق الفريق التقني'],
            ['name_en' => 'Quality Assurance & Feature Validation', 'name_ar' => 'ضمان الجودة والتحقق من الميزات'],
            ['name_en' => 'Backend & Mobile Development Coordination', 'name_ar' => 'تنسيق تطوير الواجهة الخلفية والموبايل'],
            ['name_en' => 'Release & Progress Reporting', 'name_ar' => 'متابعة الإصدارات والتقارير الدورية'],
            ['name_en' => 'Admin Systems Training', 'name_ar' => 'تدريب على أنظمة الإدارة'],
        ];

        foreach ($contributions as $index => $contribution) {
            CaseStudyContribution::updateOrCreate(
                ['case_study_id' => $caseStudy->id, 'name_en' => $contribution['name_en']],
                [...$contribution, 'display_order' => $index],
            );
        }

        // Only technology verified anywhere in the source contract (mobile
        // team role: "Flutter Developer"). No backend/database/hosting
        // technology is named in either source document, so none is seeded.
        CaseStudyTechnology::updateOrCreate(
            ['case_study_id' => $caseStudy->id, 'name' => 'Flutter'],
            ['category' => 'Mobile', 'display_order' => 0],
        );
    }
}
