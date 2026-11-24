<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseReview;
use App\Models\General;
use App\Models\Instructor;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the TrainersBD demo catalogue.
 * Run: php artisan db:seed --class=CourseSeeder
 * Safe to re-run: it clears the course tables first.
 */
class CourseSeeder extends Seeder
{
    const DEMO_VIDEO = 'M7lc1UVf-VE';

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['course_order_items', 'course_orders', 'course_reviews', 'course_lessons', 'course_modules', 'courses', 'instructors', 'course_categories'] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $cats = [];
        foreach ([
            ['web-development', 'Web Development', 'bi-code-slash'],
            ['graphic-design', 'Graphic Design', 'bi-palette'],
            ['digital-marketing', 'Digital Marketing', 'bi-megaphone'],
            ['freelancing', 'Freelancing', 'bi-laptop'],
            ['office-apps', 'Office Applications', 'bi-file-earmark-spreadsheet'],
            ['spoken-english', 'Spoken English', 'bi-chat-quote'],
            ['data-science', 'Data & AI', 'bi-cpu'],
            ['video-editing', 'Video Editing', 'bi-camera-reels'],
        ] as $i => [$slug, $name, $icon]) {
            $cats[$slug] = CourseCategory::create(['slug' => $slug, 'name' => $name, 'icon' => $icon, 'sort' => $i])->id;
        }

        $ins = [];
        foreach ([
            'rakib'   => ['Rakibul Hasan', 'Senior Full-Stack Developer', 'Rakibul has spent 10+ years building web products for local and international clients. He teaches the way real teams work: clean code, version control and shipping projects.'],
            'nusrat'  => ['Nusrat Jahan', 'Brand & UI Designer', 'Nusrat is a brand designer who has worked with agencies in Dhaka and Europe. Her classes are project-based, so every student leaves with a portfolio.'],
            'tanvir'  => ['Tanvir Ahmed', 'Performance Marketer', 'Tanvir has managed ad budgets for e-commerce brands across Bangladesh. He teaches marketing with real dashboards and real numbers.'],
            'farhana' => ['Farhana Akter', 'Top-Rated Freelancer', 'Farhana has completed 500+ projects on international marketplaces and now helps beginners land their first clients.'],
            'sabbir'  => ['Sabbir Rahman', 'Data Analyst & Excel Trainer', 'Sabbir trains corporate teams on Excel, Power BI and Python for data analysis.'],
        ] as $key => [$name, $title, $bio]) {
            $ins[$key] = Instructor::create(compact('name', 'title', 'bio'))->id;
        }

        $courses = [
            ['complete-web-development-bootcamp', 'Complete Web Development Bootcamp with Laravel', 'Go from zero to a job-ready full-stack developer. HTML, CSS, JavaScript, PHP, MySQL and Laravel with 6 real projects.', 'web-development', 'rakib', 'paid', 4500, 8000, 'Beginner to Advanced', '48h 30m', 'Bestseller', 3240, 'web-development.jpg', true],
            ['professional-graphic-design', 'Professional Graphic Design: Photoshop & Illustrator', 'Master Adobe Photoshop and Illustrator, then design logos, social media posts and print materials clients pay for.', 'graphic-design', 'nusrat', 'paid', 3500, 6000, 'Beginner', '32h 10m', 'Popular', 2180, 'graphic-design.jpg', true],
            ['digital-marketing-masterclass', 'Digital Marketing Masterclass: Facebook & Google Ads', 'Plan, launch and scale profitable ad campaigns. Includes pixel setup, audience research and reporting.', 'digital-marketing', 'tanvir', 'paid', 3000, 5000, 'Intermediate', '26h 45m', 'New', 1760, 'digital-marketing.jpg', true],
            ['freelancing-career-guideline', 'Freelancing Career: Win Your First International Client', 'Profile setup, proposal writing, pricing, client communication and getting paid safely from abroad.', 'freelancing', 'farhana', 'paid', 2000, 3500, 'All Levels', '14h 20m', null, 2950, 'freelancing.jpg', true],
            ['advanced-excel-power-bi', 'Advanced Excel & Power BI for Office Professionals', 'Formulas, pivot tables, dashboards and Power BI reports that make you the go-to person at work.', 'office-apps', 'sabbir', 'paid', 2500, 4000, 'Intermediate', '20h 05m', null, 1340, 'excel-power-bi.jpg', false],
            ['ui-ux-design-figma', 'UI/UX Design with Figma: From Wireframe to Prototype', 'Learn the full product design process and build a case-study portfolio with Figma.', 'graphic-design', 'nusrat', 'paid', 4000, 6500, 'Beginner to Intermediate', '24h 40m', 'New', 860, 'ui-ux-figma.jpg', false],
            ['python-data-analysis', 'Python for Data Analysis & AI Fundamentals', 'Python, Pandas, visualisation and an introduction to machine learning with hands-on datasets.', 'data-science', 'sabbir', 'paid', 5000, 8500, 'Intermediate', '36h 15m', null, 940, 'python-data.jpg', false],
            ['video-editing-premiere-pro', 'Video Editing with Premiere Pro & After Effects', 'Edit YouTube videos, reels and ads with professional cuts, colour grading and motion graphics.', 'video-editing', 'tanvir', 'paid', 3200, 5500, 'Beginner', '19h 30m', null, 720, 'video-editing.jpg', false],
            ['html-css-for-beginners', 'HTML & CSS for Absolute Beginners', 'Build and publish your first web page. A complete free course for anyone starting web development.', 'web-development', 'rakib', 'free', 0, 0, 'Beginner', '5h 10m', 'Free', 15800, 'html-css.jpg', true],
            ['canva-design-basics', 'Canva Design Basics: Social Media Posts', 'Create eye-catching posts, banners and thumbnails with free tools.', 'graphic-design', 'nusrat', 'free', 0, 0, 'Beginner', '3h 05m', 'Free', 9100, 'canva-design.jpg', true],
            ['freelancing-first-steps', 'Freelancing First Steps: Is It Right for You?', 'Understand marketplaces, in-demand skills and a realistic 90-day plan before you invest.', 'freelancing', 'farhana', 'free', 0, 0, 'All Levels', '2h 20m', 'Free', 7300, 'freelancing-first-steps.jpg', true],
        ];

        $learn = [
            'Understand the core concepts from the ground up, explained in simple Bangla',
            'Build real projects step by step that you can show to employers and clients',
            'Follow the same workflow and tools professionals use every day',
            'Avoid the common mistakes beginners make and learn how to fix them',
            'Get practical tips for finding work and growing your income with this skill',
            'Keep learning on your own with a clear roadmap after the course',
        ];
        $requirements = [
            'A computer or laptop with an internet connection',
            'No prior experience needed, we start from the basics',
            'Willingness to practise for about 1 hour a day',
        ];

        $reviewers = [
            ['Mahmudul Islam', 5, 'Very well structured. Every lesson builds on the previous one and the projects made everything click for me. Support from the team was quick too.'],
            ['Sharmin Sultana', 5, 'I tried many free tutorials before, but this course finally gave me a clear path. Explained in easy Bangla, highly recommended.'],
            ['Arif Hossain', 4, 'Great content and practical examples. Payment through bKash was easy and I was enrolled within a few hours.'],
            ['Tasnim Ahmed', 5, 'The trainer explains everything patiently. I finished my first project in the second week.'],
            ['Rashed Karim', 5, 'Worth every taka. The real-world examples are what make this course different.'],
            ['Jannatul Ferdous', 4, 'Good pace and clear audio. Would love even more practice exercises.'],
        ];

        foreach ($courses as $i => [$slug, $title, $subtitle, $cat, $instructor, $type, $price, $old, $level, $duration, $badge, $students, $image, $featured]) {
            $course = Course::create([
                'slug' => $slug, 'title' => $title, 'subtitle' => $subtitle,
                'description' => '<p>' . e($subtitle) . ' This course is taught in Bangla, so you can focus on learning the skill instead of struggling with the language.</p>'
                    . '<p>Every module ends with a practical task. By the end you will have finished projects you can add to your portfolio or CV, and the confidence to keep improving on your own.</p>'
                    . '<h4>Who this course is for</h4><ul><li>Students and fresh graduates who want an in-demand, practical skill</li><li>Job holders looking to upgrade their career or switch fields</li><li>Anyone who wants to start freelancing or earn from home</li></ul>',
                'category_id' => $cats[$cat], 'instructor_id' => $ins[$instructor], 'type' => $type,
                'price' => $price, 'old_price' => $old, 'level' => $level, 'language' => 'Bangla',
                'duration' => $duration, 'badge' => $badge, 'students_count' => $students,
                'thumbnail' => 'medies/courses/' . $image, 'thumbnail_alt' => $title,
                'preview_video' => self::DEMO_VIDEO, 'learn_points' => $learn, 'requirements' => $requirements,
                'featured' => $featured, 'status' => 'active', 'sort' => $i,
                'meta_title' => $title . ' | TrainersBD', 'meta_description' => $subtitle,
            ]);

            $this->curriculum($course);

            foreach (array_slice($reviewers, $i % 3, 3 + ($i % 4)) as $j => [$name, $rating, $text]) {
                CourseReview::create([
                    'course_id' => $course->id, 'name' => $name, 'email' => null,
                    'rating' => $rating, 'review' => $text, 'status' => 'approved',
                    'created_at' => now()->subDays(5 + $j * 9 + $i),
                ]);
            }
        }

        // A couple of reviews waiting for moderation
        CourseReview::create(['course_id' => 1, 'name' => 'Nayeem Hasan', 'email' => 'nayeem@example.com', 'rating' => 5, 'review' => 'Just finished module 3, loving it so far!', 'status' => 'pending']);
        CourseReview::create(['course_id' => 2, 'name' => 'Sumaiya Akter', 'email' => 'sumaiya@example.com', 'rating' => 4, 'review' => 'Clear explanations. The logo design project was my favourite.', 'status' => 'pending']);

        $this->settings();
        $this->slider();
    }

    protected function curriculum(Course $course): void
    {
        $free = $course->type === 'free';
        $modules = $free
            ? ['Getting Started' => ['Welcome and course overview', 'Setting up your tools', 'Your first exercise'],
               'Core Concepts' => ['Key concepts explained', 'Working with the basics', 'Practice exercise and solution', 'Common mistakes to avoid'],
               'Hands-on Practice' => ['Mini project walkthrough', 'Polishing your work', 'Sharing what you built'],
               'Next Steps' => ['Where to go from here', 'Recommended learning path']]
            : ['Introduction & Setup' => ['Welcome and course overview', 'How to get the most from this course', 'Installing the tools you need'],
               'Fundamentals' => ['Key concepts explained', 'Working with the basics', 'Practice exercise and solution', 'Common mistakes to avoid', 'Module quiz review'],
               'Building Your First Project' => ['Planning the project', 'Building step by step', 'Polishing the details', 'Project review'],
               'Intermediate Techniques' => ['Going deeper', 'Tips professionals use', 'Speeding up your workflow', 'Debugging and problem solving'],
               'Real-World Project' => ['Project brief', 'Building the full project', 'Testing and final touches', 'Presenting your work'],
               'Career & Next Steps' => ['Building your portfolio', 'Finding clients and jobs', 'Your learning roadmap']];

        $m = 0;
        foreach ($modules as $title => $lessons) {
            $module = CourseModule::create(['course_id' => $course->id, 'title' => $title, 'sort' => $m]);
            foreach ($lessons as $l => $lesson) {
                $preview = $free || ($m === 0 && $l < 2);
                CourseLesson::create([
                    'course_id' => $course->id, 'module_id' => $module->id, 'title' => $lesson,
                    'video' => $preview ? 'https://www.youtube.com/watch?v=' . self::DEMO_VIDEO : null,
                    'duration' => sprintf('%02d:%02d', 6 + (($m * 7 + $l * 5) % 18), ($m * 13 + $l * 29) % 60),
                    'is_preview' => $preview, 'sort' => $l,
                ]);
            }
            $m++;
        }
    }

    protected function settings(): void
    {
        $general = General::first();
        if (!$general) {
            return;
        }
        $general->title = 'TrainersBD';
        $general->subtitle = 'Online Courses in Bangla';
        $general->meta_title = 'TrainersBD | Online Courses in Bangla';
        $general->meta_description = 'Practical online courses in web development, design, digital marketing and freelancing, taught in Bangla.';
        $general->meta_keyword = 'online course, bangla course, web development, graphic design, freelancing, digital marketing';
        $general->currency = 'BDT';
        $general->payment_methods = json_encode([
            ['key' => 'bkash', 'name' => 'bKash', 'enabled' => true, 'type' => 'Send Money (Personal)', 'account' => '01XXXXXXXXX', 'details' => '',
             'steps' => "Open your bKash app and tap \"Send Money\".\nEnter the number shown above and the total amount.\nComplete the payment, then copy the Transaction ID (TrxID)."],
            ['key' => 'nagad', 'name' => 'Nagad', 'enabled' => true, 'type' => 'Send Money (Personal)', 'account' => '01XXXXXXXXX', 'details' => '',
             'steps' => "Open your Nagad app and tap \"Send Money\".\nEnter the number shown above and the total amount.\nComplete the payment, then copy the Transaction ID."],
            ['key' => 'bank', 'name' => 'Bank Transfer', 'enabled' => true, 'type' => 'Account Name: TrainersBD', 'account' => 'A/C XXXX XXXX XXXX', 'details' => 'Bank name, Branch name',
             'steps' => "Transfer the total amount to the bank account above.\nUse your mobile number as the payment reference.\nEnter the sending account number and the transaction / reference ID."],
        ]);
        $general->save();
    }

    /** Replace the "Front Page Slider" slides with the TrainersBD hero images. */
    protected function slider(): void
    {
        $slider = Attribute::where('type', 1)->where('location', 'Front Page Slider')->whereNull('parent_id')->first();
        if (!$slider) {
            $slider = Attribute::create(['type' => 1, 'name' => 'Home page', 'location' => 'Front Page Slider', 'status' => 'active']);
        }

        $old = Attribute::where('type', 1)->where('parent_id', $slider->id)->pluck('id');
        Media::where('src_type', 3)->whereIn('src_id', $old)->delete();
        Attribute::whereIn('id', $old)->delete();

        $slides = [
            ['New batch starts soon', 'Web Development Bootcamp', '/course/complete-web-development-bootcamp', 'students-learning.jpg', 'Students learning together on laptops'],
            ['Build real portfolio projects', 'Project-based learning', '#courses', 'coding-class.jpg', 'Students working on a coding project'],
            ['Watch free classes, no sign-up', 'Free Courses', '#free-courses', 'online-class.jpg', 'Online class on a laptop screen'],
        ];
        foreach ($slides as $i => [$title, $tag, $link, $file, $alt]) {
            $slide = new Attribute();
            $slide->type = 1;
            $slide->parent_id = $slider->id;
            $slide->name = $title;
            $slide->description = $tag;
            $slide->seo_title = 'View';
            $slide->seo_description = $link;
            $slide->view = $i;
            $slide->status = 'active';
            $slide->save();
            $slide->slug = 'slide-' . $slide->id;
            $slide->save();

            $path = 'public/medies/slider/' . $file;
            $media = new Media();
            $media->src_id = $slide->id;
            $media->src_type = 3;
            $media->use_Of_file = 1;
            $media->file_name = $file;
            $media->file_rename = $file;
            $media->alt_text = $alt;
            $media->file_url = $path;
            $media->file_size = is_file(base_path($path)) ? filesize(base_path($path)) : 0;
            $media->file_type = 1;
            $media->file_path = 'public/medies/slider';
            $media->mine_type = 'image/jpeg';
            $media->save();
        }
    }
}
