<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\CourseCategory;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * Header menu + information pages for TrainersBD.
 * Run: php artisan db:seed --class=MenuPageSeeder
 * Replaces the items of the "Header Menus" menu and rewrites the About / Contact / policy pages.
 */
class MenuPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = $this->pages();
        $this->headerMenu($pages);
    }

    protected function pages(): array
    {
        $content = [
            'about-us' => [
                'name' => 'About Us',
                'short' => 'We help people in Bangladesh learn practical, job-ready skills in their own language.',
                'body' => '<h2>Who we are</h2><p>TrainersBD is an online learning platform that brings together experienced trainers from Bangladesh to teach practical skills in clear, simple Bangla. Our courses cover web development, graphic design, digital marketing, freelancing, office applications and more.</p>'
                    . '<h2>What makes us different</h2><ul><li><strong>Taught by practitioners</strong> – every trainer works in the field they teach.</li><li><strong>Project-based</strong> – students build real work they can show to employers and clients.</li><li><strong>Personal support</strong> – our team helps you from choosing a course to completing it.</li><li><strong>Simple payment</strong> – pay with bKash, Nagad or bank transfer; no card needed.</li></ul>'
                    . '<h2>Our mission</h2><p>To make quality skills training affordable and accessible to every learner in Bangladesh, so that more people can build a career, start freelancing or grow their business.</p>',
            ],
            'contact-us' => [
                'name' => 'Contact Us',
                'short' => null,
                'body' => '<h2>We are here to help</h2><p>Questions about a course, your payment or your enrollment? Call or message us and our support team will reply as quickly as possible. When asking about an order, please share your order number and the mobile number you used at checkout.</p>',
                'template' => 'Contact Us',
            ],
            'privacy-policy' => [
                'name' => 'Privacy Policy',
                'short' => 'How TrainersBD collects, uses and protects your information.',
                'body' => '<p>This policy explains what information TrainersBD collects when you use our website and how we use it.</p>'
                    . '<h3>Information we collect</h3><ul><li>Your name, mobile number and email address when you place an order.</li><li>Payment details you submit for verification: payment method, sender number and transaction ID.</li><li>Reviews you choose to submit.</li><li>Basic technical data such as your IP address and browser type.</li></ul>'
                    . '<h3>How we use it</h3><ul><li>To verify your payment and enroll you in the courses you ordered.</li><li>To contact you about your order and provide support.</li><li>To improve our courses and website.</li></ul>'
                    . '<h3>Sharing</h3><p>We do not sell your personal information. We only share it when required by law or when needed to deliver our service.</p>'
                    . '<h3>Contact</h3><p>If you have questions about this policy, please contact us using the details on our Contact page.</p>',
            ],
            'terms-and-conditions' => [
                'name' => 'Terms And Conditions',
                'short' => 'The terms that apply when you use TrainersBD and enroll in our courses.',
                'body' => '<h3>Enrollment</h3><p>Orders are confirmed after our team verifies your manual payment. Course access is provided by the TrainersBD team after confirmation.</p>'
                    . '<h3>Payments</h3><p>Please send the exact order amount to the account shown at checkout and submit the correct sender number and transaction ID. Orders with incorrect or duplicate transaction IDs may be put on hold or cancelled.</p>'
                    . '<h3>Course content</h3><p>All course materials are for your personal learning only. Sharing, reselling or recording course content is not allowed.</p>'
                    . '<h3>Free courses</h3><p>Free course videos are hosted on YouTube and are subject to YouTube\'s terms of service.</p>'
                    . '<h3>Changes</h3><p>We may update these terms from time to time. The latest version is always available on this page.</p>',
            ],
            'refund-and-return-policy' => [
                'name' => 'Refund Policy',
                'short' => 'When and how you can request a refund.',
                'body' => '<h3>Before enrollment</h3><p>If your order has not yet been confirmed, you can cancel it and receive a full refund of the amount you sent.</p>'
                    . '<h3>After enrollment</h3><p>Refund requests after course access has been given are reviewed case by case. Please contact us with your order number and reason.</p>'
                    . '<h3>Wrong or duplicate payments</h3><p>If you sent the wrong amount or paid twice, contact us with your transaction IDs and we will refund or adjust the difference.</p>'
                    . '<h3>How refunds are made</h3><p>Refunds are sent to the same bKash, Nagad or bank account the payment came from, usually within 7 working days.</p>',
            ],
        ];

        $ids = [];
        foreach ($content as $slug => $c) {
            $page = Post::where('type', 0)->where('slug', $slug)->first() ?: new Post(['type' => 0]);
            $page->type = 0;
            $page->name = $c['name'];
            $page->slug = $slug;
            $page->short_description = $c['short'];
            $page->description = $c['body'];
            $page->template = $c['template'] ?? null;
            $page->status = 'active';
            $page->seo_title = $c['name'] . ' | TrainersBD';
            $page->seo_description = $c['short'];
            $page->save();
            $ids[$slug] = $page->id;
        }

        $home = Post::where('type', 0)->where('template', 'Front Page')->first();
        $ids['home'] = $home?->id;

        return $ids;
    }

    protected function headerMenu(array $pages): void
    {
        $menu = Attribute::where('type', 8)->whereNull('parent_id')->where('location', 'Header Menus')->first();
        if (!$menu) {
            $menu = new Attribute();
            $menu->type = 8;
            $menu->name = 'Main Menu';
            $menu->location = 'Header Menus';
            $menu->status = 'active';
            $menu->save();
        }

        $old = Attribute::where('type', 8)->where('category_id', $menu->id)->pluck('id');
        Media::where('src_type', 3)->whereIn('src_id', $old)->delete();
        Attribute::whereIn('id', $old)->delete();

        $view = 0;
        $add = function (array $data, $parentId) use ($menu, &$view) {
            $item = new Attribute();
            $item->type = 8;
            $item->parent_id = $parentId;
            $item->category_id = $menu->id;
            $item->menu_type = $data['menu_type'];
            $item->src_id = $data['src_id'] ?? null;
            $item->name = $data['name'] ?? null;
            $item->slug = $data['slug'] ?? null;
            $item->target = false;
            $item->view = $view++;
            $item->status = 'active';
            $item->save();
            return $item;
        };

        if ($pages['home'] ?? null) {
            $add(['menu_type' => 1, 'src_id' => $pages['home']], $menu->id);
        } else {
            $add(['menu_type' => 0, 'name' => 'Home', 'slug' => '/'], $menu->id);
        }

        $courses = $add(['menu_type' => 0, 'name' => 'Courses', 'slug' => 'courses'], $menu->id);
        foreach (CourseCategory::active()->get() as $cat) {
            $add(['menu_type' => 3, 'src_id' => $cat->id], $courses->id);
        }

        $add(['menu_type' => 0, 'name' => 'Free Courses', 'slug' => 'free-courses'], $menu->id);
        $add(['menu_type' => 1, 'src_id' => $pages['about-us']], $menu->id);
        $add(['menu_type' => 0, 'name' => 'How to Enroll', 'slug' => '#how-it-works'], $menu->id);
        $add(['menu_type' => 1, 'src_id' => $pages['contact-us']], $menu->id);
    }
}
