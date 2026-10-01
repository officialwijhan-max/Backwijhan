<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

/**
 * Mirrors the three engagement models defined in the frontend's
 * src/routes/pricing.tsx (English) and the ArabicPricingPage component in
 * src/components/arabic-pages.tsx (Arabic).
 */
class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'number' => '01',
                'title_en' => 'Discovery Sprint',
                'title_ar' => 'دورة الاكتشاف',
                'tag_en' => 'Fixed scope, fixed price',
                'tag_ar' => 'نطاق وسعر ثابتان',
                'price_en' => '$1,000',
                'price_ar' => '1,000 دولار',
                'price_note_en' => 'fixed price per sprint',
                'price_note_ar' => 'سعر ثابت لكل دورة',
                'summary_en' => 'A short, focused engagement that turns a business idea or problem into a validated product definition. You know the full cost before we start.',
                'summary_ar' => 'تعاون قصير ومركّز يحوّل فكرة العمل أو المشكلة إلى تعريف منتج تم التحقق منه. تعرف التكلفة الكاملة قبل أن نبدأ.',
                'includes_en' => [
                    'Business and requirements analysis',
                    'User needs and success criteria',
                    'Product definition and feature prioritization',
                    'MVP scope and delivery plan',
                    'A fixed quote for the build that follows',
                ],
                'includes_ar' => [
                    'تحليل العمل والمتطلبات',
                    'احتياجات المستخدمين ومعايير النجاح',
                    'تعريف المنتج وترتيب أولويات الخصائص',
                    'نطاق المنتج الأولي وخطة التسليم',
                    'عرض سعر ثابت لمرحلة البناء التالية',
                ],
                'best_for_en' => 'Founders and teams who need clarity before committing to a build.',
                'best_for_ar' => 'المؤسسون والفرق الذين يحتاجون إلى وضوح قبل الالتزام بالبناء.',
            ],
            [
                'number' => '02',
                'title_en' => 'Product Build',
                'title_ar' => 'بناء المنتج',
                'tag_en' => 'Scoped engagement',
                'tag_ar' => 'تعاون بنطاق محدد',
                'price_en' => 'From $1,500',
                'price_ar' => 'يبدأ من 1,500 دولار',
                'price_note_en' => 'per agreed scope',
                'price_note_ar' => 'مقابل نطاق متفق عليه',
                'summary_en' => 'Design and engineering delivered against an agreed scope, timeline and price. The scope is defined together during or after discovery — never guessed.',
                'summary_ar' => 'تصميم وهندسة يُسلَّمان وفق نطاق وجدول وسعر متفق عليها. يُحدَّد النطاق معاً أثناء الاكتشاف أو بعده، ولا نخمّنه أبداً.',
                'includes_en' => [
                    'UX, UI and prototyping',
                    'Web, mobile and backend engineering',
                    'Quality assurance and release validation',
                    'Milestone-based delivery with visible progress',
                    'A defined price per agreed scope',
                ],
                'includes_ar' => [
                    'تجربة المستخدم والواجهات والنماذج الأولية',
                    'هندسة الويب والهاتف والأنظمة الخلفية',
                    'ضمان الجودة واعتماد الإصدارات',
                    'تسليم على مراحل مع تقدّم ظاهر',
                    'سعر محدد مقابل نطاق متفق عليه',
                ],
                'best_for_en' => 'Businesses ready to build a defined product or system, including ERP.',
                'best_for_ar' => 'الشركات المستعدة لبناء منتج أو نظام محدد، بما في ذلك أنظمة ERP.',
            ],
            [
                'number' => '03',
                'title_en' => 'Embedded Partnership',
                'title_ar' => 'شراكة مدمجة',
                'tag_en' => 'Monthly engagement',
                'tag_ar' => 'تعاون شهري',
                'price_en' => 'From $100',
                'price_ar' => 'يبدأ من 100 دولار',
                'price_note_en' => 'per month',
                'price_note_ar' => 'شهرياً',
                'summary_en' => 'Ongoing product, design and engineering capacity working as part of your team, billed monthly. Scale the capacity up or down as the product evolves.',
                'summary_ar' => 'قدرات مستمرة في المنتج والتصميم والهندسة تعمل كجزء من فريقك وتُحتسب شهرياً. يمكن زيادة السعة أو تخفيضها مع تطور المنتج.',
                'includes_en' => [
                    'Dedicated product and engineering capacity',
                    'Continuous improvement and iteration',
                    'Delivery management and stakeholder communication',
                    'Priority response for your product',
                    'A predictable monthly rate',
                ],
                'includes_ar' => [
                    'قدرات مخصصة في المنتج والهندسة',
                    'تحسين وتطوير مستمر',
                    'إدارة التسليم والتواصل مع أصحاب المصلحة',
                    'أولوية استجابة لمنتجك',
                    'سعر شهري متوقع',
                ],
                'best_for_en' => 'Teams with a live product that needs a long-term technology partner.',
                'best_for_ar' => 'الفرق التي لديها منتج قائم وتحتاج إلى شريك تقني طويل الأمد.',
            ],
        ];

        foreach ($plans as $index => $plan) {
            PricingPlan::updateOrCreate(
                ['number' => $plan['number']],
                [...$plan, 'sort_order' => $index, 'is_active' => true],
            );
        }
    }
}
